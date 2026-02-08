<?php
require_once 'config/db.php';

// Get the route from the URL (default to 'home' if empty)
$request = isset($_GET['route']) ? $_GET['route'] : '/';

// Remove trailing slashes for consistency (e.g., /courses/ -> /courses)
$request = rtrim($request, '/');

// Router Logic
switch ($request) {
    case '':
    case '/':
    case 'courses':
        // GET /
        require 'views/courses.php';
        break;

    case 'courses/course-create':
        // GET /courses/course-create
        require 'views/course_create.php';
        break;
    default:
        // Handle Dynamic Routes (using Regex)
        // Match GET /courses/<id>  (e.g., courses/w25-2025-2026)
        if (preg_match('/^courses\/(\d+)$/', $request, $matches)) {
            $courseId = $matches[1];
            require 'views/course_detail.php';
            break;
        }

        // Match GET /courses/<id>/project-create
        if (preg_match('/^courses\/(\d+)\/project-create$/', $request, $matches)) {
            $courseId = $matches[1];
            require 'views/project_create.php';
            break;
        }

        // Match GET /courses/<id>/projects/<id>
        if (preg_match('/^courses\/(\d+)\/projects\/(\d+)$/', $request, $matches)) {
            $courseId = $matches[1];
            $projectId = $matches[2];
            require 'views/project_detail.php';
            break;
        }

        // 404 Not Found
        http_response_code(404);
        require 'views/404.php';
        break;
}
?>
