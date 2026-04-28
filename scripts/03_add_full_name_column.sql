-- Add full_name column to users table
-- This script adds the missing full_name column that is referenced in the application

USE syllabus_management;

-- Add full_name column to users table
ALTER TABLE users 
ADD COLUMN full_name VARCHAR(255) NULL AFTER email;

-- Update existing users to have full_name set to username if not set
UPDATE users SET full_name = username WHERE full_name IS NULL OR full_name = '';
