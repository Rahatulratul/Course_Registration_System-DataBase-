CREATE DATABASE course_registration;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'teacher', 'admin') NOT NULL DEFAULT 'student'
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
    FOREIGN KEY (faculty_id) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE(course_code, section)
);

CREATE TABLE enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE(student_id, course_id)
);

-- Insert default admin (password is 'admin')
INSERT IGNORE INTO users (name, email, password, role) VALUES ('Admin', 'admin@gmail.com', '$2y$10$32bWfP7J/tW78O5hW8I1f.D8qIELyD9kHq1K49/T2V7z/.k8yHk4G', 'admin');
