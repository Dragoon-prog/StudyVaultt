CREATE DATABASE IF NOT EXISTS studyvault
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE studyvault;

CREATE TABLE users (
    user_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('student','admin') NOT NULL DEFAULT 'student',
    status ENUM('active','inactive','blocked') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE semesters (
    semester_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE subjects (
    subject_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    semester_id INT UNSIGNED NOT NULL,
    course_code VARCHAR(30) NOT NULL,
    title VARCHAR(160) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_subject (semester_id, course_code, title),
    CONSTRAINT fk_subject_semester FOREIGN KEY (semester_id)
        REFERENCES semesters(semester_id) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE notes (
    note_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    subject_id INT UNSIGNED NOT NULL,
    title VARCHAR(180) NOT NULL,
    description TEXT,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type VARCHAR(100) NOT NULL,
    file_size INT UNSIGNED NOT NULL,
    status ENUM('pending','approved','flagged','rejected') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_notes_status (status),
    INDEX idx_notes_subject (subject_id),
    INDEX idx_notes_created (created_at),
    CONSTRAINT fk_note_user FOREIGN KEY (user_id)
        REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_note_subject FOREIGN KEY (subject_id)
        REFERENCES subjects(subject_id) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE comments (
    comment_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    note_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    rating TINYINT UNSIGNED NULL,
    comment_text TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CHECK (rating IS NULL OR rating BETWEEN 1 AND 5),
    CONSTRAINT fk_comment_note FOREIGN KEY (note_id)
        REFERENCES notes(note_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_comment_user FOREIGN KEY (user_id)
        REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE audit_logs (
    log_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    note_id INT UNSIGNED NULL,
    action VARCHAR(50) NOT NULL,
    old_status VARCHAR(30) NULL,
    new_status VARCHAR(30) NULL,
    changed_by INT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

DELIMITER $$

CREATE TRIGGER trg_note_status_audit
AFTER UPDATE ON notes
FOR EACH ROW
BEGIN
    IF OLD.status <> NEW.status THEN
        INSERT INTO audit_logs(note_id, action, old_status, new_status, changed_by)
        VALUES (NEW.note_id, 'STATUS_CHANGE', OLD.status, NEW.status, NEW.user_id);
    END IF;
END$$

CREATE TRIGGER trg_note_delete_audit
BEFORE DELETE ON notes
FOR EACH ROW
BEGIN
    INSERT INTO audit_logs(note_id, action, old_status, new_status, changed_by)
    VALUES (OLD.note_id, 'DELETE', OLD.status, NULL, OLD.user_id);
END$$

DELIMITER ;

CREATE OR REPLACE VIEW approved_notes AS
SELECT n.note_id, n.title, n.description, n.file_name, n.file_path,
       n.file_type, n.file_size, n.created_at,
       u.full_name AS uploader,
       s.course_code, s.title AS subject_title,
       sem.name AS semester
FROM notes n
JOIN users u ON u.user_id = n.user_id
JOIN subjects s ON s.subject_id = n.subject_id
JOIN semesters sem ON sem.semester_id = s.semester_id
WHERE n.status = 'approved';

INSERT INTO semesters(name) VALUES
('1st Year - 1st Sem'),
('1st Year - 2nd Sem'),
('1st Year - 3rd Sem'),
('2nd Year - 1st Sem'),
('2nd Year - 2nd Sem'),
('2nd Year - 3rd Sem'),
('3rd Year - 1st Sem'),
('3rd Year - 2nd Sem'),
('3rd Year - 3rd Sem'),
('4th Year - 1st Sem'),
('4th Year - 2nd Sem'),
('4th Year - 3rd Sem');

INSERT INTO subjects(semester_id, course_code, title)
SELECT semester_id, catalog.course_code, catalog.title
FROM semesters
CROSS JOIN (
    SELECT 'BSIS241A' AS course_code, 'Financial Management' AS title
    UNION ALL SELECT 'BSIS241A', 'Evaluation of Business Performance'
    UNION ALL SELECT 'BSIS241A', 'Multimedia and Computer Graphics'
    UNION ALL SELECT 'BSIS241A', 'Web Programming'
    UNION ALL SELECT 'GENED16B', 'Purposive Communication'
) AS catalog
WHERE semesters.name='3rd Year - 1st Sem';
