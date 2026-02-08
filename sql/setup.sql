-- Disable foreign key checks to prevent errors during import order
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS users (
    id VARCHAR(36) PRIMARY KEY, -- UUIDs are 36-byte
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    faculty_number VARCHAR(50) NOT NULL UNIQUE,
    avatar_url TEXT,

    university_email VARCHAR(255) NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,

    user_role ENUM('student', 'assistant', 'admin') NOT NULL DEFAULT 'student'
);

CREATE TABLE IF NOT EXISTS courses (
    id VARCHAR(255) PRIMARY KEY,
    display_name TEXT NOT NULL,
    opens_at_date DATE NOT NULL,
    closes_at_date DATE NOT NULL,
    owner_id VARCHAR(36),
    moodle_course_url VARCHAR(255) UNIQUE,

    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS enrollments (
    id VARCHAR(36) PRIMARY KEY, -- UUIDs are 36-byte
    student_id VARCHAR(36) NOT NULL,
    course_id VARCHAR(255) NOT NULL,
    grade INTEGER,

    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS group_projects (
    id VARCHAR(36) PRIMARY KEY, -- UUIDs are 36-byte
    course_id VARCHAR(255) NOT NULL,
    name TEXT NOT NULL,
    description TEXT,

    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS group_project_members (
    id VARCHAR(36) PRIMARY KEY, -- UUIDs are 36-byte
    group_project_id VARCHAR(36) NOT NULL,
    student_id VARCHAR(36) NOT NULL,

    FOREIGN KEY (group_project_id) REFERENCES group_projects(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Enable foreign key check
SET FOREIGN_KEY_CHECKS = 1;
