<?php

session_start();

require_once 'config/db.php';
require_once 'config/project.php';

$request = isset($_GET['route']) ? $_GET['route'] : '/';
$request = rtrim($request, '/');

$view = null;

switch ($request) {
    // --- Route: Courses ---
    case '':
    case '/':
    case 'courses':
        $view = 'views/courses.php';
        break;

    case 'login':
        $view = 'views/login.php';
        break;

    case 'register':
        $view = 'views/register.php';
        break;

    case 'logout':
        // Destroy the session and redirect to login
        session_destroy();
        header("Location: " . BASE_URL . "/login");
        exit;

    // --- Route: Create Course ---
    case 'courses/course-create':
        $view = 'views/course_modify.php';
        break;

    default:
        // Route: Edit Course (GET /courses/w25-2025-2026/edit)
        if (preg_match('#^courses/([a-zA-Z0-9\-_]+)/edit$#', $request, $matches)) {
            $courseId = $matches[1];
            $view = 'views/course_modify.php';
            break;
        }

        // Route: View Course (GET /courses/w25-2025-2026)
        if (preg_match('/^courses\/([a-zA-Z0-9\-_]+)$/', $request, $matches)) {
            $courseId = $matches[1];
            $view = 'views/course_detail.php';
            break;
        }

        // Route: Create Project (GET /courses/w25-2025-2026/project-create)
        if (preg_match('#^courses/([a-zA-Z0-9\-_]+)/project-create$#', $request, $matches)) {
            $courseId = $matches[1];
            $view = 'views/project_modify.php';
            break;
        }

        // Route: Edit Project (GET /courses/w25-2025-2026/projects/fcf9edf9-0d01-4250-b17b-a6e3c466a7df/edit)
        if (preg_match('#^courses\/([a-zA-Z0-9\-_]+)/projects\/([a-zA-Z0-9\-_]+)/edit$#', $request, $matches)) {
            $courseId = $matches[1];
            $projectId = $matches[2];
            $view = 'views/project_modify.php';
            break;
        }

        // Route: View Project (GET /courses/w25-2025-2026/projects/fcf9edf9-0d01-4250-b17b-a6e3c466a7df)
        if (preg_match('#^courses\/([a-zA-Z0-9\-_]+)/projects\/([a-zA-Z0-9\-_]+)$#', $request, $matches)) {
            $courseId = $matches[1];
            $projectId = $matches[2];
            $view = 'views/project_detail.php';
            break;
        }

        // Route: Download project (GET /download/project/UUID)
        if (preg_match('#^download/project/([a-zA-Z0-9\-_]+)$#', $request, $matches)) {
            $targetId = $matches[1];
            require 'scripts/download_project.php'; // We will create this file next
            exit;
        }

        // --- 404 Not Found ---
        http_response_code(404);
        $view = 'views/404.php';
        break;
}

// Render the page
if ($view) {
    include 'includes/header.php';
    include $view;
    include 'includes/footer.php';
}
