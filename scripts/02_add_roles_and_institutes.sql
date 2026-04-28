-- Add roles, organizations, and schools tables with proper relationships
-- This script extends the existing syllabus management database

-- Create roles table
CREATE TABLE IF NOT EXISTS roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert the five role types
INSERT INTO roles (name, description) VALUES
('Super Admin', 'Full system access and control'),
('Organization Admin', 'Manages schools and users within their organization'),
('School Admin', 'Manages teachers and classes within their school'),
('Teacher', 'Manages assigned classes and lectures'),
('Solo Student', 'Individual student with direct Super Admin assignment');

-- Create organizations table
CREATE TABLE IF NOT EXISTS organizations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    address TEXT,
    phone VARCHAR(20),
    email VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create schools table
CREATE TABLE IF NOT EXISTS schools (
    id INT PRIMARY KEY AUTO_INCREMENT,
    organization_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    address TEXT,
    phone VARCHAR(20),
    email VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE
);

-- Update users table to add role and institute relationships
ALTER TABLE users 
ADD COLUMN role_id INT NOT NULL DEFAULT 1,
ADD COLUMN organization_id INT NULL,
ADD COLUMN school_id INT NULL,
ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active',
ADD COLUMN created_by INT NULL,
ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
ADD FOREIGN KEY (role_id) REFERENCES roles(id),
ADD FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE SET NULL,
ADD FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE SET NULL,
ADD FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL;

-- Create user_class_permissions table for granular access control
CREATE TABLE IF NOT EXISTS user_class_permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    class_id INT NOT NULL,
    granted_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (granted_by) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_class (user_id, class_id)
);

-- Update existing admin user to be Super Admin
UPDATE users SET role_id = 1 WHERE username = 'admin';

-- Insert sample data for testing
INSERT INTO organizations (name, description, address, phone, email) VALUES
('Tech University System', 'Leading technology education organization', '123 Tech Street, Silicon Valley', '+1-555-0100', 'info@techuniversity.edu'),
('Community College Network', 'Affordable education for all communities', '456 Community Ave, Downtown', '+1-555-0200', 'contact@ccnetwork.edu');

INSERT INTO schools (organization_id, name, description, address, phone, email) VALUES
(1, 'Tech University - Main Campus', 'Main campus with engineering and computer science programs', '123 Tech Street, Silicon Valley', '+1-555-0101', 'main@techuniversity.edu'),
(1, 'Tech University - North Campus', 'North campus focusing on business and liberal arts', '789 North Ave, Uptown', '+1-555-0102', 'north@techuniversity.edu'),
(2, 'Downtown Community College', 'Urban campus serving diverse student population', '456 Community Ave, Downtown', '+1-555-0201', 'downtown@ccnetwork.edu');
