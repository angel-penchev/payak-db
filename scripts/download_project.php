<?php

// scripts/download_project.php
require_once 'config/db.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$current_user_id = $_SESSION['user_id'] ?? null;
$current_role = $_SESSION['user_role'] ?? 'student';

if (!$current_user_id) {
    die("Access denied. Please login.");
}

// Fetch Project Info
$stmt = $pdo->prepare("SELECT * FROM group_projects WHERE id = ?");
$stmt->execute([$targetId]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project || empty($project['zip_file_path'])) {
    die("File not found.");
}

$filePath = $project['zip_file_path'];

// Check Permissions
// Admins and Assistants can always download
if ($current_role === 'admin' || $current_role === 'assistant') {
    $canDownload = true;
} else {
    // Students must be members of the team
    $stmtMem = $pdo->prepare("
        SELECT 1 FROM group_project_members gpm
        WHERE gpm.group_project_id = ? AND gpm.student_id = ?
    ");
    $stmtMem->execute([$targetId, $current_user_id]);
    $canDownload = $stmtMem->fetchColumn();
}

if (!$canDownload) {
    http_response_code(403);
    die("Access Denied: You are not a member of this project.");
}

// Serve the File
if (file_exists($filePath)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="source_code_' . basename($filePath) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;
} else {
    die("File does not exist on server.");
}
