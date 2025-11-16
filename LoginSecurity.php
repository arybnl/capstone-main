<?php
/**
 * LoginSecurity.php
 * Handles login rate limiting, brute force protection, and audit logging
 * 
 * Save this file in your root directory (same folder as index.php and config.php)
 */

class LoginSecurity {
    private $db;
    
    // Configuration - adjust these settings as needed
    private $max_failed_attempts = 5;      // Max failed attempts before lockout
    private $lockout_duration = 1800;      // Lockout duration in seconds (30 minutes)
    private $rate_limit_window = 300;      // Time window for rate limiting (5 minutes)
    private $ip_rate_limit = 10;           // Max attempts per IP in time window
    
    /**
     * Constructor
     * @param mysqli $database_connection - Database connection object
     */
    public function __construct($database_connection) {
        $this->db = $database_connection;
    }
    
    /**
     * Check if login attempt is allowed
     * Returns array with 'allowed' boolean and 'message' string
     * 
     * @param string $email - User email address
     * @param string $ip_address - Client IP address
     * @return array
     */
    public function checkLoginAllowed($email, $ip_address) {
        // 1. Check if account is locked
        $account_check = $this->checkAccountLockout($email);
        if (!$account_check['allowed']) {
            return $account_check;
        }
        
        // 2. Check IP rate limiting
        $ip_check = $this->checkIPRateLimit($ip_address);
        if (!$ip_check['allowed']) {
            return $ip_check;
        }
        
        // 3. Check email-based rate limiting
        $email_check = $this->checkEmailRateLimit($email);
        if (!$email_check['allowed']) {
            return $email_check;
        }
        
        return ['allowed' => true, 'message' => 'Login attempt allowed'];
    }
    
    /**
     * Check if account is currently locked
     * @param string $email
     * @return array
     */
    private function checkAccountLockout($email) {
        // Check Users table lock status
        $stmt = $this->db->prepare(
            "SELECT user_id, account_locked, locked_until 
             FROM users 
             WHERE email = ?"
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            if ($row['account_locked'] == 1 && $row['locked_until']) {
                $locked_until = strtotime($row['locked_until']);
                
                if ($locked_until > time()) {
                    $minutes_left = ceil(($locked_until - time()) / 60);
                    
                    return [
                        'allowed' => false,
                        'message' => "Account is temporarily locked due to multiple failed login attempts. Please try again in {$minutes_left} minutes.",
                        'locked_until' => $locked_until
                    ];
                } else {
                    // Auto-unlock expired lockout
                    $this->unlockAccount($row['user_id'], 'auto');
                }
            }
        }
        
        $stmt->close();
        return ['allowed' => true];
    }
    
    /**
     * Check IP-based rate limiting
     * @param string $ip_address
     * @return array
     */
    private function checkIPRateLimit($ip_address) {
        $identifier = 'ip_' . hash('sha256', $ip_address);
        
        $stmt = $this->db->prepare(
            "SELECT failed_attempts, first_failed_attempt, locked_until 
             FROM login_rate_limit 
             WHERE identifier = ?"
        );
        $stmt->bind_param('s', $identifier);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            // Check if locked
            if ($row['locked_until'] && strtotime($row['locked_until']) > time()) {
                $minutes_left = ceil((strtotime($row['locked_until']) - time()) / 60);
                
                $stmt->close();
                return [
                    'allowed' => false,
                    'message' => "Too many login attempts from your IP address. Please try again in {$minutes_left} minutes."
                ];
            }
            
            // Check rate limit window
            $first_attempt = strtotime($row['first_failed_attempt']);
            if ((time() - $first_attempt) < $this->rate_limit_window) {
                if ($row['failed_attempts'] >= $this->ip_rate_limit) {
                    // Lock IP
                    $this->lockIPAddress($identifier);
                    
                    $stmt->close();
                    return [
                        'allowed' => false,
                        'message' => 'Too many login attempts from your IP address. Please try again later.'
                    ];
                }
            } else {
                // Time window expired, reset counter
                $this->resetRateLimit($identifier);
            }
        }
        
        $stmt->close();
        return ['allowed' => true];
    }
    
    /**
     * Check email-based rate limiting
     * @param string $email
     * @return array
     */
    private function checkEmailRateLimit($email) {
        $identifier = 'email_' . hash('sha256', strtolower(trim($email)));
        
        $stmt = $this->db->prepare(
            "SELECT failed_attempts, first_failed_attempt 
             FROM login_rate_limit 
             WHERE identifier = ?"
        );
        $stmt->bind_param('s', $identifier);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $first_attempt = strtotime($row['first_failed_attempt']);
            
            if ((time() - $first_attempt) < $this->rate_limit_window) {
                if ($row['failed_attempts'] >= $this->max_failed_attempts) {
                    $stmt->close();
                    return [
                        'allowed' => false,
                        'message' => 'Too many failed login attempts for this account. Please try again later or reset your password.',
                        'should_lock' => true
                    ];
                }
            } else {
                // Reset if window expired
                $this->resetRateLimit($identifier);
            }
        }
        
        $stmt->close();
        return ['allowed' => true];
    }
    
    /**
     * Log login attempt to audit table
     * 
     * @param string $email
     * @param string|null $user_id
     * @param string $status - 'success', 'failed', or 'locked'
     * @param string $ip_address
     * @param string $user_agent
     * @param string|null $failure_reason
     */
    public function logLoginAttempt($email, $user_id, $status, $ip_address, $user_agent, $failure_reason = null) {
        $stmt = $this->db->prepare(
            "INSERT INTO login_audit_log 
             (user_id, email, ip_address, user_agent, attempt_status, failure_reason) 
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        
        $stmt->bind_param('ssssss', 
            $user_id, 
            $email, 
            $ip_address, 
            $user_agent, 
            $status, 
            $failure_reason
        );
        
        $stmt->execute();
        $stmt->close();
    }
    
    /**
     * Record failed login attempt
     * 
     * @param string $email
     * @param string $ip_address
     */
    public function recordFailedAttempt($email, $ip_address) {
        // Record for email
        $email_identifier = 'email_' . hash('sha256', strtolower(trim($email)));
        $this->incrementRateLimit($email_identifier);
        
        // Record for IP
        $ip_identifier = 'ip_' . hash('sha256', $ip_address);
        $this->incrementRateLimit($ip_identifier);
        
        // Check if account should be locked
        $email_attempts = $this->getFailedAttempts($email_identifier);
        
        if ($email_attempts >= $this->max_failed_attempts) {
            // Lock the account
            $this->lockUserAccount($email, $ip_address);
        }
    }
    
    /**
     * Clear failed attempts after successful login
     * 
     * @param string $email
     * @param string $ip_address
     */
    public function clearFailedAttempts($email, $ip_address) {
        $email_identifier = 'email_' . hash('sha256', strtolower(trim($email)));
        $ip_identifier = 'ip_' . hash('sha256', $ip_address);
        
        $this->resetRateLimit($email_identifier);
        $this->resetRateLimit($ip_identifier);
    }
    
    /**
     * Lock user account
     * 
     * @param string $email
     * @param string $ip_address
     */
    private function lockUserAccount($email, $ip_address) {
        // Get user ID
        $stmt = $this->db->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $user_id = $row['user_id'];
            $locked_until = date('Y-m-d H:i:s', time() + $this->lockout_duration);
            
            // Update users table
            $stmt = $this->db->prepare(
                "UPDATE users 
                 SET account_locked = 1, locked_until = ? 
                 WHERE user_id = ?"
            );
            $stmt->bind_param('ss', $locked_until, $user_id);
            $stmt->execute();
            $stmt->close();
            
            // Log lockout
            $stmt = $this->db->prepare(
                "INSERT INTO account_lockouts 
                 (user_id, email, locked_until, reason, ip_address) 
                 VALUES (?, ?, ?, ?, ?)"
            );
            $reason = "Too many failed login attempts";
            $stmt->bind_param('sssss', $user_id, $email, $locked_until, $reason, $ip_address);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    /**
     * Unlock account
     * 
     * @param string $user_id
     * @param string $method - 'auto', 'admin', or 'user'
     */
    private function unlockAccount($user_id, $method = 'auto') {
        // Update users table
        $stmt = $this->db->prepare(
            "UPDATE users 
             SET account_locked = 0, locked_until = NULL 
             WHERE user_id = ?"
        );
        $stmt->bind_param('s', $user_id);
        $stmt->execute();
        $stmt->close();
        
        // Update lockout record
        $stmt = $this->db->prepare(
            "UPDATE account_lockouts 
             SET unlock_method = ?, unlocked_at = NOW() 
             WHERE user_id = ? AND unlocked_at IS NULL 
             ORDER BY locked_at DESC LIMIT 1"
        );
        $stmt->bind_param('ss', $method, $user_id);
        $stmt->execute();
        $stmt->close();
    }
    
    /**
     * Increment rate limit counter
     * 
     * @param string $identifier
     */
    private function incrementRateLimit($identifier) {
        $stmt = $this->db->prepare(
            "INSERT INTO login_rate_limit 
             (identifier, failed_attempts, first_failed_attempt, last_failed_attempt) 
             VALUES (?, 1, NOW(), NOW()) 
             ON DUPLICATE KEY UPDATE 
             failed_attempts = failed_attempts + 1, 
             last_failed_attempt = NOW()"
        );
        $stmt->bind_param('s', $identifier);
        $stmt->execute();
        $stmt->close();
    }
    
    /**
     * Reset rate limit
     * 
     * @param string $identifier
     */
    private function resetRateLimit($identifier) {
        $stmt = $this->db->prepare(
            "DELETE FROM login_rate_limit WHERE identifier = ?"
        );
        $stmt->bind_param('s', $identifier);
        $stmt->execute();
        $stmt->close();
    }
    
    /**
     * Lock IP address
     * 
     * @param string $identifier
     */
    private function lockIPAddress($identifier) {
        $locked_until = date('Y-m-d H:i:s', time() + $this->lockout_duration);
        
        $stmt = $this->db->prepare(
            "UPDATE login_rate_limit 
             SET locked_until = ? 
             WHERE identifier = ?"
        );
        $stmt->bind_param('ss', $locked_until, $identifier);
        $stmt->execute();
        $stmt->close();
    }
    
    /**
     * Get failed attempts count
     * 
     * @param string $identifier
     * @return int
     */
    private function getFailedAttempts($identifier) {
        $stmt = $this->db->prepare(
            "SELECT failed_attempts FROM login_rate_limit WHERE identifier = ?"
        );
        $stmt->bind_param('s', $identifier);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $stmt->close();
            return $row['failed_attempts'];
        }
        
        $stmt->close();
        return 0;
    }
    
    /**
     * Get client IP address
     * 
     * @return string
     */
    public static function getClientIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }
        return trim($ip);
    }
    
    /**
     * Get user agent
     * 
     * @return string
     */
    public static function getUserAgent() {
        return $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    }
    
    /**
     * Cleanup old records (run periodically via cron job or randomly)
     */
    public function cleanupOldRecords() {
        // Delete old audit logs (older than 90 days)
        $this->db->query(
            "DELETE FROM login_audit_log 
             WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 90 DAY)"
        );
        
        // Delete old rate limit records (older than 24 hours)
        $this->db->query(
            "DELETE FROM login_rate_limit 
             WHERE last_failed_attempt < DATE_SUB(NOW(), INTERVAL 24 HOUR)"
        );
        
        // Delete old unlocked lockout records (older than 30 days)
        $this->db->query(
            "DELETE FROM account_lockouts 
             WHERE unlocked_at IS NOT NULL 
             AND unlocked_at < DATE_SUB(NOW(), INTERVAL 30 DAY)"
        );
    }
}
?>