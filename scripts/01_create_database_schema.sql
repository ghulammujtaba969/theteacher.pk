-- Create database for Syllabus Management System
CREATE DATABASE IF NOT EXISTS syllabus_management;
USE syllabus_management;

-- Users table for authentication
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'admin', 'teacher', 'student') DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

-- Classes table
CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_name VARCHAR(100) NOT NULL,
    class_code VARCHAR(20) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

-- Subjects table (linked to classes)
CREATE TABLE subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_name VARCHAR(100) NOT NULL,
    subject_code VARCHAR(20) NOT NULL,
    class_id INT NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_subject_class (subject_code, class_id)
);

-- Syllabi table (linked to subjects)
CREATE TABLE syllabi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    syllabus_title VARCHAR(200) NOT NULL,
    subject_id INT NOT NULL,
    description TEXT,
    objectives TEXT,
    duration_weeks INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
);

-- Lectures table (linked to syllabi)
CREATE TABLE lectures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lecture_title VARCHAR(200) NOT NULL,
    syllabus_id INT NOT NULL,
    lecture_type ENUM('video', 'audio', 'file', 'text') NOT NULL,
    content_url VARCHAR(500) NULL, -- For video/audio/file URLs or uploaded file paths
    text_content LONGTEXT NULL, -- For rich text content
    file_name VARCHAR(255) NULL, -- Original filename for uploaded files
    file_size INT NULL, -- File size in bytes
    duration_minutes INT NULL, -- For video/audio content
    lecture_order INT DEFAULT 1,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (syllabus_id) REFERENCES syllabi(id) ON DELETE CASCADE
);

-- Insert default Super Admin user
INSERT INTO users (username, email, password, role) VALUES 
('superadmin', 'admin@syllabus.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin');
-- Default password is 'password' (hashed with bcrypt)

-- Create indexes for better performance
CREATE INDEX idx_subjects_class ON subjects(class_id);
CREATE INDEX idx_syllabi_subject ON syllabi(subject_id);
CREATE INDEX idx_lectures_syllabus ON lectures(syllabus_id);
CREATE INDEX idx_lectures_type ON lectures(lecture_type);
CREATE INDEX idx_users_role ON users(role);
