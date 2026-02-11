-- Use the database
USE payak_db;

-- Disable FK checks for clean seeding
SET FOREIGN_KEY_CHECKS = 0;

INSERT INTO users (id, first_name, last_name, faculty_number, university_email, password_hash, user_role, avatar_url) VALUES
('u-111-admin', 'Ivan', 'Ivanov', 'ADMIN001', 'ivan.ivanov@uni-sofia.bg', '$2y$10$4cy3xZrHL1Pv0Gdv5uIL0epLSPvabv54ICpmlk7/1Lwl/1irGqhzy', 'admin', 'https://api.dicebear.com/7.x/avataaars/svg?seed=Ivan'),
('u-222-assistant', 'Petar', 'Petrov', 'ASST002', 'p.petrov@uni-sofia.bg', '$2y$10$4cy3xZrHL1Pv0Gdv5uIL0epLSPvabv54ICpmlk7/1Lwl/1irGqhzy', 'assistant', 'https://api.dicebear.com/7.x/avataaars/svg?seed=Petar'),
('u-001-student', 'Maria', 'Simeonova', '81900', 'msimeonova@uni-sofia.bg', '$2y$10$4cy3xZrHL1Pv0Gdv5uIL0epLSPvabv54ICpmlk7/1Lwl/1irGqhzy', 'student', 'https://api.dicebear.com/7.x/avataaars/svg?seed=Maria'),
('u-002-student', 'Georgi', 'Georgiev', '81901', 'ggeorgiev@uni-sofia.bg', '$2y$10$4cy3xZrHL1Pv0Gdv5uIL0epLSPvabv54ICpmlk7/1Lwl/1irGqhzy', 'student', 'https://api.dicebear.com/7.x/avataaars/svg?seed=Georgi'),
('u-003-student', 'Elena', 'Dimitrova', '81902', 'edimitrova@uni-sofia.bg', '$2y$10$4cy3xZrHL1Pv0Gdv5uIL0epLSPvabv54ICpmlk7/1Lwl/1irGqhzy', 'student', 'https://api.dicebear.com/7.x/avataaars/svg?seed=Elena');

INSERT INTO courses (id, display_name, opens_at_date, closes_at_date, owner_id, moodle_course_url) VALUES
('course-web-2025', 'Web технологии, зимен семестър 2025/2026', '2025-10-01', '2026-02-28', 'u-222-assistant', 'https://moodle.uni-sofia.bg/course/view.php?id=12345');

INSERT INTO enrollments (id, student_id, course_id, grade) VALUES
(UUID(), 'u-001-student', 'course-web-2025', NULL),
(UUID(), 'u-002-student', 'course-web-2025', NULL),
(UUID(), 'u-003-student', 'course-web-2025', NULL);

INSERT INTO group_projects (id, course_id, name, topic, description) VALUES
('proj-001', 'course-web-2025', 'Eco-Tracker', 'Sustainability Web App', 'A platform to track personal carbon footprint.'),
('proj-002', 'course-web-2025', 'StudyBuddy', 'Education', 'Social network for finding study partners for exams.');

INSERT INTO group_project_members (id, group_project_id, student_id) VALUES
(UUID(), 'proj-001', 'u-001-student'), 
(UUID(), 'proj-001', 'u-002-student'), 
(UUID(), 'proj-002', 'u-003-student'); 

-- Re-enable FK checks
SET FOREIGN_KEY_CHECKS = 1;
