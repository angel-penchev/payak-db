ALTER TABLE courses 
ADD COLUMN min_users_per_project INT DEFAULT 1,
ADD COLUMN max_users_per_project INT DEFAULT 3;
