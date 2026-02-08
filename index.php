<?php
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

    // --- Route: Create Course ---
    case 'courses/course-create':
        $view = 'views/course_create.php';
        break;

    default:
        // Route: View Course (GET /courses/w25-2025-2026)
        if (preg_match('/^courses\/(\d+)$/', $request, $matches)) {
            $courseId = $matches[1];
            $view = 'views/course_detail.php';
            break;
        }

        // Route: Create Project (GET /courses/w25-2025-2026/project-create)
        if (preg_match('/^courses\/(\d+)\/project-create$/', $request, $matches)) {
            $courseId = $matches[1];
            $view = 'views/project_create.php';
            break;
        }

        // Route: View Project (GET /courses/w25-2025-2026/projects/fcf9edf9-0d01-4250-b17b-a6e3c466a7df)
        if (preg_match('/^courses\/(\d+)\/projects\/(\d+)$/', $request, $matches)) {
            $courseId = $matches[1];
            $projectId = $matches[2];
            $view = 'views/project_detail.php';
            break;
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
?>
