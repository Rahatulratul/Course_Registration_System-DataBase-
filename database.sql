CREATE DATABASE IF NOT EXISTS course_registration;
USE course_registration;


CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE teachers (
    teacher_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    department_id INT DEFAULT NULL,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
);

CREATE TABLE students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    department_id INT DEFAULT NULL,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
);

CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_code VARCHAR(20) NOT NULL,
    section VARCHAR(10) NOT NULL,
    course_name VARCHAR(150) NOT NULL,
    credits INT NOT NULL,
    faculty_id INT DEFAULT NULL,
    capacity INT DEFAULT 30,
    course_time VARCHAR(100) DEFAULT NULL,
    room_number VARCHAR(50) DEFAULT NULL,
    FOREIGN KEY (faculty_id) REFERENCES teachers(teacher_id) ON DELETE SET NULL,
    UNIQUE(course_code, section)
);

CREATE TABLE enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE(student_id, course_id)
);

-- Insert default departments
INSERT IGNORE INTO departments (name) VALUES 
('Computer Science and Engineering'),
('Business Administration'),
('Electrical and Electronic Engineering'),
('English'),
('Pharmacy'),
('Economics');

-- Insert default admin (password is 'admin')
INSERT IGNORE INTO admins (name, email, password) VALUES ('Admin', 'admin@gmail.com', '$2y$10$ffCoCrQ8F49sJcU7TXNrEO4NQnwINmDoLpkQXITJ9.eMDluJD8Gw.');
