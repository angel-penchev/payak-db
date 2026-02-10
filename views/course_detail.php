<?php
// config/db.php
require_once 'config/db.php'; 


$current_course = null;
$projects = [];

try {
    if ($courseId > 0) {
        // 2. Fetch the SPECIFIC course for the Big Block
        $stmt = $pdo->prepare("
            SELECT c.*, 
            (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) AS enrolled_count
            FROM courses c 
            WHERE c.id = ?
        ");
        $stmt->execute([$courseId]);
        $current_course = $stmt->fetch(PDO::FETCH_ASSOC);

        // 3. Fetch projects from group_projects table matching this course
        $stmt_proj = $pdo->prepare("SELECT * FROM group_projects WHERE course_id = ?");
        $stmt_proj->execute([$courseId]);
        $projects = $stmt_proj->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Dashboard</title>
    <style>
        body { 
            font-family: 'Segoe UI', sans-serif; 
            padding: 20px; 
            max-width: 900px; 
            margin: 0 auto; 
            background-color: #fff;
        }

        /* --- BIG BLOCK (Course) --- */
        .hero-card {
            border: 2px solid #000;
            padding: 30px;
            margin-bottom: 30px;
            position: relative;
            min-height: 180px;
        }

        .hero-card h1 { margin: 0; font-size: 2.5em; font-weight: 500; }
        .hero-card .tag { color: #555; font-size: 1.2em; margin-top: 5px; }

        .hero-stats {
            position: absolute;
            bottom: 15px;
            right: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 1.1em;
            font-weight: bold;
        }

        /* --- DIVIDER WITH BUTTON --- */
        .project-divider {
            display: flex;
            align-items: center;
            margin: 40px 0 20px 0;
        }
        .line { flex-grow: 1; height: 1px; background: #000; }
        .divider-text { padding: 0 15px; font-size: 1.3em; font-weight: bold; }
        
        .btn-create {
            border: 2px solid #3498db;
            color: #3498db;
            padding: 4px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9em;
            margin-left: 10px;
        }

        /* --- PROJECTS GRID --- */
        .project-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .project-card {
            border: 2px solid #000;
            padding: 20px;
            min-height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .project-card h3 { margin: 0; font-size: 1.6em; font-weight: 500; }
        
        .user-icons {
            display: flex;
            gap: 8px;
            margin-top: 15px;
        }
        .user-circle {
            width: 25px;
            height: 25px;
            border: 1.5px solid #000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7em;
        }

        @media (max-width: 600px) {
            .project-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <?php if ($current_course): ?>
        <div class="hero-card">
            <h1><?php echo htmlspecialchars($current_course['display_name']); ?></h1>
            <div class="tag">
                ID: <?php echo htmlspecialchars($current_course['id']); ?>
            </div>
            
            <div class="hero-stats">
                <span>👤 <?php echo $current_course['enrolled_count']; ?></span>
                <span>🌐 <?php echo count($projects); ?></span>

            </div>
        </div>
    <?php else: ?>
        <p>Курсът не е намерен. Моля, проверете ID-то в URL адреса.</p>
    <?php endif; ?>

    <div class="project-divider">
        <div class="line"></div>
        <div class="divider-text">Проекти</div>
        <div class="line"></div>
        <a href="/payak-db/courses/<?php echo $courseId; ?>/project-create" class="btn-create" target="_blank">Създаване</a>
    </div>

    <div class="project-grid">
        <?php if (count($projects) > 0): ?>
            <?php foreach ($projects as $project): ?>
                <div class="project-card">
                    <a href="<?php echo BASE_URL; ?>/courses/<?php echo $courseId; ?>/projects/<?php echo $project['id']; ?>" target="_blank"><h3><?php echo htmlspecialchars($project['name'] ?? 'Project Name'); ?></h3></a>
                    
                    
                    <div class="user-icons">
                        <div class="user-circle">👤</div>
                        <div class="user-circle">👤</div>
                        <div class="user-circle">👤</div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="grid-column: span 2; text-align: center; color: #666;">
                Няма открити проекти за този курс.
            </p>
        <?php endif; ?>
    </div>

</body>
</html>