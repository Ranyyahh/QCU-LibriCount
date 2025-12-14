CREATE DATABASE QCULibriCount;
USE QCULibriCount;

CREATE TABLE admin(
    admin_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE students(
    student_id INT PRIMARY KEY AUTO_INCREMENT,
    student_number VARCHAR(20) UNIQUE NOT NULL,
    firstname VARCHAR(100) NOT NULL,
    middlename VARCHAR(100),
    lastname VARCHAR(100) NOT NULL,
    course VARCHAR(50),
    year_level VARCHAR(20),
    date_registered TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE attendance_logs(
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    time_in DATETIME NOT NULL,
    time_out DATETIME,
    status ENUM('inside', 'exited', 'timeout') DEFAULT 'inside',
    session_duration INT,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_time_in (time_in)
);

CREATE TABLE system_logs(
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    action_type VARCHAR(50) NOT NULL,
    action_details TEXT,
    admin_id INT,
    log_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admin(admin_id) ON DELETE SET NULL
);

CREATE TABLE system_settings (
    setting_id INT PRIMARY KEY AUTO_INCREMENT,
    setting_name VARCHAR(50) UNIQUE NOT NULL,
    setting_value VARCHAR(255) NOT NULL,
    admin_id INT,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admin(admin_id) ON DELETE SET NULL
);

-- Insert initial data
INSERT INTO admin (username, password) VALUES
('admin', 'admin123');

INSERT INTO system_settings (setting_name, setting_value, admin_id) 
VALUES ('max_capacity', '50', 1);

INSERT INTO students (student_number, firstname, middlename, lastname, course, year_level) VALUES
('24-1416', 'Franz Remnant', 'Regunda', 'Reyes', 'Computer Science', '2nd Year'),
('24-1426', 'Jerv Christian', 'Atienza', 'Ganio', 'Information Technology', '3rd Year'), 
('24-1492', 'Jigger Anne', 'Cabalejo', 'Vizconde', 'College of Engineering', '2nd Year'),
('24-1502', 'James Samuel', 'Orit', 'Ojeda', 'Information Technology', '4th Year'),
('24-1433', 'Vincent Martin', 'Torres', 'Canillas', 'Information Technology', '2nd Year'),
('24-1501', 'Ayesha Mae', 'Gregorio', 'Rosales', 'Information Technology', '3rd Year'),
('24-1438', 'Elgie Kean', 'Maquiling', 'Vere', 'Information Technology', '1st Year'),
('24-1486', 'Eunice', 'Pepe', 'Matacubo', 'Information Technology', '2nd Year'),
('24-1702', 'Alexis Marie', 'Rivera', 'Brecia', 'Information Technology', '1st Year'),
('24-1462', 'Kurt Adrian', 'Lustereos', 'Uy', 'Information Technology', '4th Year')
