----- Database Name: capstone_try2_db -----
CREATE TABLE Users (
    user_id VARCHAR(36) PRIMARY KEY, -- changed: UUID to VARCHAR(36) for MySQL compatibility
    email VARCHAR(255) UNIQUE NOT NULL, -- User's email, used for login
    password_hash VARCHAR(255) NOT NULL, -- Hashed password for security
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    user_type ENUM('member', 'trainer', 'admin') NOT NULL, -- Role of the user (e.g., 'member', 'trainer', 'admin')
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active', -- User account status
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

ALTER TABLE users
ADD COLUMN profile_picture varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'default_profile.png',
ADD COLUMN profile_image_data longblob,
ADD COLUMN profile_image_mime varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;


CREATE TABLE Members (
    member_id VARCHAR(36) PRIMARY KEY, -- changed: UUID to VARCHAR(36) for MySQL compatibility
    user_id VARCHAR(36) UNIQUE NOT NULL, -- changed: UUID to VARCHAR(36) for MySQL compatibility. Foreign Key to Users.user_id, Links to Users table
    phone_number VARCHAR(20),
    membership_expiry DATE,
    qr_code_data TEXT, -- Data to generate QR code for gym access
    current_weight_kg DECIMAL(5,2), -- Latest recorded weight for quick access
    current_height_cm DECIMAL(5,2), -- Latest recorded height for quick access
    goal_calories_kcal INT, -- Member's daily calorie goal (from assessment)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Added for consistency
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Added for consistency
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

CREATE TABLE Trainers (
    trainer_id VARCHAR(36) PRIMARY KEY, -- changed: UUID to VARCHAR(36) for MySQL compatibility
    user_id VARCHAR(36) UNIQUE NOT NULL, -- changed: UUID to VARCHAR(36) for MySQL compatibility. Foreign Key to Users.user_id, Links to Users table
    specialization VARCHAR(255), -- E.g., "Bodybuilding", "Muay Thai", "HIIT"
    bio TEXT,
    contact_number VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Added for consistency
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Added for consistency
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

CREATE TABLE Admins (
    admin_id VARCHAR(36) PRIMARY KEY, -- changed: UUID to VARCHAR(36) for MySQL compatibility
    user_id VARCHAR(36) UNIQUE NOT NULL, -- changed: UUID to VARCHAR(36) for MySQL compatibility. Foreign Key to Users.user_id, Links to Users table
    -- Add any specific admin fields here, e.g., permissions_level TEXT
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Added for consistency
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Added for consistency
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);






-- --- Sample Data for MEMBER ---
-- 1. Insert into Users first
INSERT INTO Users (user_id, email, password_hash, first_name, last_name, user_type, status) VALUES
(UUID(), 'sample.member@example.com', '$2y$10$Q7rZ.C1M2s0X5P3j6Q1n4u8O9i0J.K.L.M.N.O.P.Q.R.S.T.U.V', 'Jane', 'Doe', 'member', 'active');

-- 2. Retrieve the user_id for the newly created member
SET @retrieved_user_id_member = (SELECT user_id FROM Users WHERE email = 'sample.member@example.com');

-- 3. Then create the Member, linking to the retrieved user_id
INSERT INTO Members (member_id, user_id, phone_number, membership_expiry, qr_code_data, current_weight_kg, current_height_cm, goal_calories_kcal) VALUES
(UUID(), @retrieved_user_id_member, '123-456-7890', '2024-12-31', CONCAT('{"user_id": "', @retrieved_user_id_member, '", "access_level": "member"}'), 75.50, 170.00, 2000);


-- --- Sample Data for TRAINER ---
-- 1. Insert into Users for trainer
INSERT INTO Users (user_id, email, password_hash, first_name, last_name, user_type, status) VALUES
(UUID(), 'trainer.mike@example.com', '$2y$10$Q7rZ.C1M2s0X5P3j6Q1n4u8O9i0J.K.L.M.N.O.P.Q.R.S.T.U.V', 'Mike', 'Stevens', 'trainer', 'active');

-- 2. Retrieve the user_id for the newly created trainer
SET @retrieved_user_id_trainer = (SELECT user_id FROM Users WHERE email = 'trainer.mike@example.com');

-- 3. Then create the Trainer, linking to the retrieved user_id
INSERT INTO Trainers (trainer_id, user_id, specialization, bio, contact_number) VALUES
(UUID(), @retrieved_user_id_trainer, 'Bodybuilding, HIIT', 'Experienced trainer with a passion for helping clients achieve their strength and fitness goals.', '098-765-4321');


-- --- Sample Data for ADMIN ---
-- 1. Insert into Users for admin
INSERT INTO Users (user_id, email, password_hash, first_name, last_name, user_type, status) VALUES
(UUID(), 'admin.boss@example.com', '$2y$10$Q7rZ.C1M2s0X5P3j6Q1n4u8O9i0J.K.L.M.N.O.P.Q.R.S.T.U.V', 'Admin', 'User', 'admin', 'active');

-- 2. Retrieve the user_id for the newly created admin
SET @retrieved_user_id_admin = (SELECT user_id FROM Users WHERE email = 'admin.boss@example.com');

-- 3. Then create the Admin, linking to the retrieved user_id
INSERT INTO Admins (admin_id, user_id) VALUES
(UUID(), @retrieved_user_id_admin);