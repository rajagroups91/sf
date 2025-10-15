-- Create database
CREATE DATABASE IF NOT EXISTS sf_due_portal;
USE sf_due_portal;

-- Students table
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    roll_no VARCHAR(50) NOT NULL,
    dept_id INT NOT NULL,
    year1_due DECIMAL(10,2) DEFAULT 0,
    year2_due DECIMAL(10,2) DEFAULT 0,
    year3_due DECIMAL(10,2) DEFAULT 0,
    total_due DECIMAL(10,2) DEFAULT 0,
    paid DECIMAL(10,2) DEFAULT 0,
    balance DECIMAL(10,2) DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    hidden BOOLEAN DEFAULT FALSE
);

-- Departments table
CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

-- Update logs table
CREATE TABLE update_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    field_name VARCHAR(255) NOT NULL,
    old_value VARCHAR(255),
    new_value VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id)
);

-- Admin users table
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Insert sample departments
INSERT INTO departments (name) VALUES 
('Computer Science'),
('Electronics'),
('Mechanical'),
('Civil'),
('Electrical');

-- Insert sample admin (password: admin123)
INSERT INTO admin_users (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert sample students
INSERT INTO students (name, roll_no, dept_id, year1_due, year2_due, year3_due, total_due, paid, balance) VALUES
('John Doe', 'CS001', 1, 5000, 6000, 7000, 18000, 10000, 8000),
('Jane Smith', 'CS002', 1, 5500, 6500, 7500, 19500, 12000, 7500),
('Robert Brown', 'EC001', 2, 4800, 5800, 6800, 17400, 9000, 8400),
('Alice Johnson', 'ME001', 3, 5200, 6200, 7200, 18600, 11000, 7600);
