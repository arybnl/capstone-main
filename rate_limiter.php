<?php
// rate_limiter.php - Password Reset Rate Limiting Class

class PasswordResetRateLimiter {
    private $db;
    
    // Configuration - adjust these as needed
    private $max_attempts = 3;           // Max attempts allowed
    private $time_window = 3600;         // Time window in seconds (1 hour)
    private $lockout_duration = 1800;    // Lockout duration in seconds (30 minutes)
    
    public function __construct($database_connection) {
        $this->db = $database_connection;
        $this->createRateLimitTable();
    }
    
    /**
     * Create the rate limiting table if it doesn't exist
     */
    private function createRateLimitTable() {
        $sql = "CREATE TABLE IF NOT EXISTS password_reset_rate_limit (
            id INT AUTO_INCREMENT PRIMARY KEY,
            identifier VARCHAR(255) NOT NULL,
            attempts INT DEFAULT 1,
            first_attempt DATETIME NOT NULL,
            last_attempt DATETIME NOT NULL,
            locked_until DATETIME NULL,
            INDEX idx_identifier (identifier),
            INDEX idx_locked_until (locked_until)
        )";
        
        $this->db->query($sql);
    }
    
    /**
     * Check if a password reset request is allowed
     * 
     * @param string $email The email address
     * @return array ['allowed' => bool, 'message' => string, 'retry_after' => int]
     */
    public function checkRateLimit($email) {
        // Use both email and IP for tracking
        $identifier = $this->getIdentifier($email);
        
        // Clean up old records
        $this->cleanupOldRecords();
        
        // Check if currently locked out
        $lockout_check = $this->checkLockout($identifier);
        if (!$lockout_check['allowed']) {
            return $lockout_check;
        }
        
        // Get current attempt record
        $record = $this->getAttemptRecord($identifier);
        
        if ($record) {
            $first_attempt = strtotime($record['first_attempt']);
            $time_elapsed = time() - $first_attempt;
            
            // If within time window
            if ($time_elapsed < $this->time_window) {
                if ($record['attempts'] >= $this->max_attempts) {
                    // Lock the account
                    $this->lockAccount($identifier);
                    
                    return [
                        'allowed' => false,
                        'message' => 'Too many password reset attempts. Please try again in ' . 
                                   ceil($this->lockout_duration / 60) . ' minutes.',
                        'retry_after' => $this->lockout_duration
                    ];
                } else {
                    // Increment attempt
                    $this->incrementAttempt($identifier);
                    
                    $remaining = $this->max_attempts - $record['attempts'] - 1;
                    return [
                        'allowed' => true,
                        'message' => 'Request allowed. ' . $remaining . ' attempts remaining.',
                        'remaining_attempts' => $remaining
                    ];
                }
            } else {
                // Time window expired, reset counter
                $this->resetAttempt($identifier);
                return [
                    'allowed' => true,
                    'message' => 'Request allowed.',
                    'remaining_attempts' => $this->max_attempts - 1
                ];
            }
        } else {
            // First attempt
            $this->recordAttempt($identifier);
            return [
                'allowed' => true,
                'message' => 'Request allowed.',
                'remaining_attempts' => $this->max_attempts - 1
            ];
        }
    }
    
    /**
     * Create a unique identifier combining email and IP
     */
    private function getIdentifier($email) {
        $ip = $this->getClientIP();
        return hash('sha256', strtolower(trim($email)) . '|' . $ip);
    }
    
    /**
     * Get client IP address
     */
    private function getClientIP() {
        $ip = '';
        
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
     * Check if account is currently locked
     */
    private function checkLockout($identifier) {
        $stmt = $this->db->prepare(
            "SELECT locked_until FROM password_reset_rate_limit 
             WHERE identifier = ? AND locked_until IS NOT NULL AND locked_until > NOW()"
        );
        $stmt->bind_param('s', $identifier);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $locked_until = strtotime($row['locked_until']);
            $retry_after = $locked_until - time();
            
            return [
                'allowed' => false,
                'message' => 'Too many password reset attempts. Account is temporarily locked. Please try again in ' . 
                           ceil($retry_after / 60) . ' minutes.',
                'retry_after' => $retry_after
            ];
        }
        
        return ['allowed' => true];
    }
    
    /**
     * Get attempt record for identifier
     */
    private function getAttemptRecord($identifier) {
        $stmt = $this->db->prepare(
            "SELECT * FROM password_reset_rate_limit WHERE identifier = ?"
        );
        $stmt->bind_param('s', $identifier);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Record first attempt
     */
    private function recordAttempt($identifier) {
        $stmt = $this->db->prepare(
            "INSERT INTO password_reset_rate_limit (identifier, attempts, first_attempt, last_attempt) 
             VALUES (?, 1, NOW(), NOW())"
        );
        $stmt->bind_param('s', $identifier);
        $stmt->execute();
    }
    
    /**
     * Increment attempt counter
     */
    private function incrementAttempt($identifier) {
        $stmt = $this->db->prepare(
            "UPDATE password_reset_rate_limit 
             SET attempts = attempts + 1, last_attempt = NOW() 
             WHERE identifier = ?"
        );
        $stmt->bind_param('s', $identifier);
        $stmt->execute();
    }
    
    /**
     * Reset attempt counter
     */
    private function resetAttempt($identifier) {
        $stmt = $this->db->prepare(
            "UPDATE password_reset_rate_limit 
             SET attempts = 1, first_attempt = NOW(), last_attempt = NOW(), locked_until = NULL 
             WHERE identifier = ?"
        );
        $stmt->bind_param('s', $identifier);
        $stmt->execute();
    }
    
    /**
     * Lock account
     */
    private function lockAccount($identifier) {
        $stmt = $this->db->prepare(
            "UPDATE password_reset_rate_limit 
             SET locked_until = DATE_ADD(NOW(), INTERVAL ? SECOND) 
             WHERE identifier = ?"
        );
        $stmt->bind_param('is', $this->lockout_duration, $identifier);
        $stmt->execute();
    }
    
    /**
     * Clean up old records
     */
    private function cleanupOldRecords() {
        // Delete records older than 24 hours
        $this->db->query(
            "DELETE FROM password_reset_rate_limit 
             WHERE last_attempt < DATE_SUB(NOW(), INTERVAL 24 HOUR)"
        );
    }
    
    /**
     * Clear rate limit for an identifier (useful after successful password reset)
     */
    public function clearRateLimit($email) {
        $identifier = $this->getIdentifier($email);
        $stmt = $this->db->prepare(
            "DELETE FROM password_reset_rate_limit WHERE identifier = ?"
        );
        $stmt->bind_param('s', $identifier);
        $stmt->execute();
    }
}
?>