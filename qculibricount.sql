-- =====================================================
-- QCU LibriCount - Oracle PL/SQL Database Schema
-- Converted from MySQL to Oracle
-- =====================================================

-- Drop existing objects (for fresh install)
BEGIN
    EXECUTE IMMEDIATE 'DROP TABLE system_logs CASCADE CONSTRAINTS PURGE';
    EXCEPTION WHEN OTHERS THEN NULL;
END;
/

BEGIN
    EXECUTE IMMEDIATE 'DROP TABLE attendance_logs CASCADE CONSTRAINTS PURGE';
    EXCEPTION WHEN OTHERS THEN NULL;
END;
/

BEGIN
    EXECUTE IMMEDIATE 'DROP TABLE students CASCADE CONSTRAINTS PURGE';
    EXCEPTION WHEN OTHERS THEN NULL;
END;
/

BEGIN
    EXECUTE IMMEDIATE 'DROP TABLE system_settings CASCADE CONSTRAINTS PURGE';
    EXCEPTION WHEN OTHERS THEN NULL;
END;
/

BEGIN
    EXECUTE IMMEDIATE 'DROP TABLE admin CASCADE CONSTRAINTS PURGE';
    EXCEPTION WHEN OTHERS THEN NULL;
END;
/

BEGIN
    EXECUTE IMMEDIATE 'DROP SEQUENCE seq_admin_id';
    EXCEPTION WHEN OTHERS THEN NULL;
END;
/

BEGIN
    EXECUTE IMMEDIATE 'DROP SEQUENCE seq_student_id';
    EXCEPTION WHEN OTHERS THEN NULL;
END;
/

BEGIN
    EXECUTE IMMEDIATE 'DROP SEQUENCE seq_attendance_log_id';
    EXCEPTION WHEN OTHERS THEN NULL;
END;
/

BEGIN
    EXECUTE IMMEDIATE 'DROP SEQUENCE seq_system_log_id';
    EXCEPTION WHEN OTHERS THEN NULL;
END;
/

BEGIN
    EXECUTE IMMEDIATE 'DROP SEQUENCE seq_settings_id';
    EXCEPTION WHEN OTHERS THEN NULL;
END;
/

-- =====================================================
-- TABLES
-- =====================================================

-- 1. Admin table
CREATE TABLE admin (
    admin_id NUMBER PRIMARY KEY,
    username VARCHAR2(50) UNIQUE NOT NULL,
    password VARCHAR2(255) NOT NULL
);

-- 2. Students table
CREATE TABLE students (
    student_id NUMBER PRIMARY KEY,
    student_number VARCHAR2(20) UNIQUE NOT NULL,
    firstname VARCHAR2(100) NOT NULL,
    middlename VARCHAR2(100),
    lastname VARCHAR2(100) NOT NULL,
    course VARCHAR2(50),
    year_level VARCHAR2(20),
    date_registered TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Attendance logs table
CREATE TABLE attendance_logs (
    log_id NUMBER PRIMARY KEY,
    student_id NUMBER NOT NULL,
    time_in TIMESTAMP NOT NULL,
    time_out TIMESTAMP,
    status VARCHAR2(10) DEFAULT 'inside',
    session_duration NUMBER,
    CONSTRAINT chk_status CHECK (status IN ('inside', 'exited', 'timeout')),
    CONSTRAINT fk_attendance_student FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
);

-- 4. System logs table
CREATE TABLE system_logs (
    log_id NUMBER PRIMARY KEY,
    action_type VARCHAR2(50) NOT NULL,
    action_details CLOB,
    admin_id NUMBER,
    log_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_syslogs_admin FOREIGN KEY (admin_id) REFERENCES admin(admin_id) ON DELETE SET NULL
);

-- 5. System settings table
CREATE TABLE system_settings (
    setting_id NUMBER PRIMARY KEY,
    setting_name VARCHAR2(50) UNIQUE NOT NULL,
    setting_value VARCHAR2(255) NOT NULL,
    admin_id NUMBER,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_settings_admin FOREIGN KEY (admin_id) REFERENCES admin(admin_id) ON DELETE SET NULL
);

-- =====================================================
-- SEQUENCES (Auto-increment)
-- =====================================================

CREATE SEQUENCE seq_admin_id START WITH 1 INCREMENT BY 1;
CREATE SEQUENCE seq_student_id START WITH 1 INCREMENT BY 1;
CREATE SEQUENCE seq_attendance_log_id START WITH 1 INCREMENT BY 1;
CREATE SEQUENCE seq_system_log_id START WITH 1 INCREMENT BY 1;
CREATE SEQUENCE seq_settings_id START WITH 1 INCREMENT BY 1;

-- =====================================================
-- TRIGGERS (Auto-increment implementation)
-- =====================================================

CREATE OR REPLACE TRIGGER trg_admin_id
BEFORE INSERT ON admin
FOR EACH ROW
BEGIN
    IF :NEW.admin_id IS NULL THEN
        SELECT seq_admin_id.NEXTVAL INTO :NEW.admin_id FROM DUAL;
    END IF;
END;
/

CREATE OR REPLACE TRIGGER trg_student_id
BEFORE INSERT ON students
FOR EACH ROW
BEGIN
    IF :NEW.student_id IS NULL THEN
        SELECT seq_student_id.NEXTVAL INTO :NEW.student_id FROM DUAL;
    END IF;
END;
/

CREATE OR REPLACE TRIGGER trg_attendance_log_id
BEFORE INSERT ON attendance_logs
FOR EACH ROW
BEGIN
    IF :NEW.log_id IS NULL THEN
        SELECT seq_attendance_log_id.NEXTVAL INTO :NEW.log_id FROM DUAL;
    END IF;
END;
/

CREATE OR REPLACE TRIGGER trg_system_log_id
BEFORE INSERT ON system_logs
FOR EACH ROW
BEGIN
    IF :NEW.log_id IS NULL THEN
        SELECT seq_system_log_id.NEXTVAL INTO :NEW.log_id FROM DUAL;
    END IF;
END;
/

CREATE OR REPLACE TRIGGER trg_settings_id
BEFORE INSERT ON system_settings
FOR EACH ROW
BEGIN
    IF :NEW.setting_id IS NULL THEN
        SELECT seq_settings_id.NEXTVAL INTO :NEW.setting_id FROM DUAL;
    END IF;
END;
/

-- =====================================================
-- STORED PROCEDURES
-- =====================================================

-- 1. Login procedure
CREATE OR REPLACE PROCEDURE sp_login(
    p_username IN VARCHAR2,
    p_password IN VARCHAR2,
    p_admin_id OUT NUMBER,
    p_status OUT VARCHAR2
)
AS
    v_count NUMBER;
    v_password VARCHAR2(255);
BEGIN
    SELECT COUNT(*) INTO v_count FROM admin WHERE username = p_username;
    
    IF v_count = 0 THEN
        p_status := 'USER_NOT_FOUND';
        p_admin_id := NULL;
        RETURN;
    END IF;
    
    SELECT admin_id, password INTO p_admin_id, v_password
    FROM admin WHERE username = p_username;
    
    IF p_password = v_password THEN
        p_status := 'SUCCESS';
    ELSE
        p_status := 'INVALID_PASSWORD';
        p_admin_id := NULL;
    END IF;
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        p_status := 'USER_NOT_FOUND';
        p_admin_id := NULL;
    WHEN OTHERS THEN
        p_status := 'ERROR: ' || SQLERRM;
        p_admin_id := NULL;
END;
/

-- 2. Get students procedure
CREATE OR REPLACE PROCEDURE sp_get_students(
    p_cursor OUT SYS_REFCURSOR
)
AS
BEGIN
    OPEN p_cursor FOR
        SELECT student_id, student_number, firstname, middlename, lastname, 
               course, year_level, date_registered
        FROM students
        ORDER BY student_id;
END;
/

-- 3. Add student procedure
CREATE OR REPLACE PROCEDURE sp_add_student(
    p_student_number IN VARCHAR2,
    p_firstname IN VARCHAR2,
    p_middlename IN VARCHAR2,
    p_lastname IN VARCHAR2,
    p_course IN VARCHAR2,
    p_year_level IN VARCHAR2,
    p_status OUT VARCHAR2
)
AS
BEGIN
    INSERT INTO students (student_number, firstname, middlename, lastname, course, year_level)
    VALUES (p_student_number, p_firstname, p_middlename, p_lastname, p_course, p_year_level);
    
    COMMIT;
    p_status := 'SUCCESS';
EXCEPTION
    WHEN DUP_VAL_ON_INDEX THEN
        p_status := 'DUPLICATE_STUDENT_NUMBER';
        ROLLBACK;
    WHEN OTHERS THEN
        p_status := 'ERROR: ' || SQLERRM;
        ROLLBACK;
END;
/

-- 4. Update student procedure
CREATE OR REPLACE PROCEDURE sp_update_student(
    p_student_id IN NUMBER,
    p_firstname IN VARCHAR2,
    p_middlename IN VARCHAR2,
    p_lastname IN VARCHAR2,
    p_course IN VARCHAR2,
    p_year_level IN VARCHAR2,
    p_status OUT VARCHAR2
)
AS
BEGIN
    UPDATE students 
    SET firstname = p_firstname,
        middlename = p_middlename,
        lastname = p_lastname,
        course = p_course,
        year_level = p_year_level
    WHERE student_id = p_student_id;
    
    IF SQL%ROWCOUNT = 0 THEN
        p_status := 'STUDENT_NOT_FOUND';
    ELSE
        p_status := 'SUCCESS';
    END IF;
    
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        p_status := 'ERROR: ' || SQLERRM;
        ROLLBACK;
END;
/

-- 5. Delete student procedure
CREATE OR REPLACE PROCEDURE sp_delete_student(
    p_student_id IN NUMBER,
    p_status OUT VARCHAR2
)
AS
BEGIN
    DELETE FROM students WHERE student_id = p_student_id;
    
    IF SQL%ROWCOUNT = 0 THEN
        p_status := 'STUDENT_NOT_FOUND';
    ELSE
        p_status := 'SUCCESS';
    END IF;
    
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        p_status := 'ERROR: ' || SQLERRM;
        ROLLBACK;
END;
/

-- 6. Log entry/exit procedure
CREATE OR REPLACE PROCEDURE sp_log_entry_exit(
    p_student_id IN NUMBER,
    p_action IN VARCHAR2, -- 'entry' or 'exit'
    p_log_id OUT NUMBER,
    p_status OUT VARCHAR2
)
AS
    v_last_log VARCHAR2(10);
    v_count NUMBER;
BEGIN
    -- Check if student exists
    SELECT COUNT(*) INTO v_count FROM students WHERE student_id = p_student_id;
    IF v_count = 0 THEN
        p_status := 'STUDENT_NOT_FOUND';
        RETURN;
    END IF;
    
    -- For entry: check if student already inside
    IF p_action = 'entry' THEN
        SELECT COUNT(*) INTO v_count FROM attendance_logs 
        WHERE student_id = p_student_id AND status = 'inside' AND time_out IS NULL;
        
        IF v_count > 0 THEN
            p_status := 'ALREADY_INSIDE';
            RETURN;
        END IF;
        
        INSERT INTO attendance_logs (student_id, time_in, status)
        VALUES (p_student_id, CURRENT_TIMESTAMP, 'inside')
        RETURNING log_id INTO p_log_id;
        
    -- For exit: check if student has entry
    ELSIF p_action = 'exit' THEN
        SELECT log_id INTO p_log_id FROM attendance_logs 
        WHERE student_id = p_student_id AND status = 'inside' AND time_out IS NULL
        ORDER BY time_in DESC
        FETCH FIRST 1 ROW ONLY;
        
        UPDATE attendance_logs 
        SET time_out = CURRENT_TIMESTAMP, 
            status = 'exited',
            session_duration = EXTRACT(DAY FROM (CURRENT_TIMESTAMP - time_in)) * 1440 +
                               EXTRACT(HOUR FROM (CURRENT_TIMESTAMP - time_in)) * 60 +
                               EXTRACT(MINUTE FROM (CURRENT_TIMESTAMP - time_in))
        WHERE log_id = p_log_id;
    END IF;
    
    COMMIT;
    p_status := 'SUCCESS';
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        p_status := 'NO_ENTRY_FOUND';
        ROLLBACK;
    WHEN OTHERS THEN
        p_status := 'ERROR: ' || SQLERRM;
        ROLLBACK;
END;
/

-- 7. Get real-time stats procedure
CREATE OR REPLACE PROCEDURE sp_get_realtime_stats(
    p_total_inside OUT NUMBER,
    p_total_students OUT NUMBER,
    p_total_entries_today OUT NUMBER,
    p_total_exits_today OUT NUMBER
)
AS
BEGIN
    -- Currently inside
    SELECT COUNT(*) INTO p_total_inside 
    FROM attendance_logs 
    WHERE status = 'inside' AND time_out IS NULL;
    
    -- Total students
    SELECT COUNT(*) INTO p_total_students FROM students;
    
    -- Total entries today
    SELECT COUNT(*) INTO p_total_entries_today 
    FROM attendance_logs 
    WHERE TRUNC(time_in) = TRUNC(CURRENT_TIMESTAMP);
    
    -- Total exits today
    SELECT COUNT(*) INTO p_total_exits_today 
    FROM attendance_logs 
    WHERE TRUNC(time_out) = TRUNC(CURRENT_TIMESTAMP);
END;
/

-- =====================================================
-- INDEXES
-- =====================================================

CREATE INDEX idx_attendance_status ON attendance_logs(status);
CREATE INDEX idx_attendance_time_in ON attendance_logs(time_in);
CREATE INDEX idx_system_logs_timestamp ON system_logs(log_timestamp);

-- =====================================================
-- INSERT INITIAL DATA
-- =====================================================

INSERT INTO admin (username, password) VALUES ('admin', 'admin123');

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
('24-1462', 'Kurt Adrian', 'Lustereos', 'Uy', 'Information Technology', '4th Year');

COMMIT;

-- =====================================================
-- VERIFY CONNECTION
-- =====================================================

SELECT 'Database successfully converted to Oracle PL/SQL!' AS Status FROM DUAL;
SELECT COUNT(*) AS Total_Students FROM students;
SELECT COUNT(*) AS Total_Admin FROM admin;
