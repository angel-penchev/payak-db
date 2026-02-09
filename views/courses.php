<?php
// config/db.php
require_once 'config/db.php'; 

$active_courses = [];
$other_courses = [];

try {
    // UPDATED QUERY:
    // We select all course data AND a count of enrollments for each course using a subquery.
    // This assumes your enrollments table has a column 'course_id'
    $sql = "SELECT c.*, 
            (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) AS enrolled_count
            FROM courses c
            ORDER BY c.id DESC";
            
    $stmt = $pdo->query($sql);
    $all_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // LOGIC: Split the courses
    // 1. Slice the first 2 for the top "Big Box" section
    $active_courses = array_slice($all_courses, 0, 2);
    
    // 2. Slice the rest (starting from index 2) for the bottom list
    $other_courses = array_slice($all_courses, 2);

} catch (PDOException $e) {
    die("Error fetching courses: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Dashboard</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            padding: 40px; 
            background-color: #fff;
            color: #333;
            max-width: 1000px;
            margin: 0 auto;
        }

        /* --- HEADERS --- */
        .section-header {
            text-align: center;
            font-size: 1.5em;
            margin-bottom: 20px;
            position: relative;
        }
        .section-header:before, .section-header:after {
            content: "";
            display: inline-block;
            width: 50px;
            height: 2px;
            background: #333;
            vertical-align: middle;
            margin: 0 10px;
        }

        /* --- TOP SECTION (GRID) --- */
        .top-grid {
            display: grid;
            grid-template-columns: 1fr 1fr; /* Two equal columns */
            gap: 20px;
            margin-bottom: 30px;
        }

        .big-card {
            border: 2px solid #333;
            border-radius: 8px;
            padding: 20px;
            background: #fff;
            position: relative;
            min-height: 150px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .big-card h2 {
            margin: 0 0 5px 0;
            font-size: 1.8em;
        }

        .tag {
            color: #666;
            font-size: 0.9em;
            font-style: italic;
        }

        .card-footer {
            margin-top: 20px;
            text-align: right;
            font-size: 1.2em;
        }

        /* --- MIDDLE (CREATE BUTTON) --- */
        .action-bar {
            text-align: right;
            margin: 20px 0;
            border-top: 2px solid #333;
            padding-top: 10px;
            position: relative;
        }
        .action-bar span {
            position: absolute;
            left: 50%;
            top: -12px;
            background: #fff;
            padding: 0 10px;
            transform: translateX(-50%);
            font-weight: bold;
        }
        
        .btn-create {
            display: inline-block;
            border: 2px solid #d9534f;
            color: #d9534f;
            padding: 5px 15px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn-create:hover {
            background: #d9534f;
            color: white;
        }

        /* --- BOTTOM LIST --- */
        .list-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .list-card {
            border: 2px solid #333;
            padding: 15px;
            border-radius: 6px;
            display: flex;
            justify-content: space-between; 
            align-items: center;
        }

        .list-info h3 {
            margin: 0;
            font-size: 1.2em;
        }
        
        .arrow-icon {
            font-size: 1.5em;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .top-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <div class="section-header">Active Courses</div>

    <div class="top-grid">
        <?php if (count($active_courses) > 0): ?>
            <?php foreach ($active_courses as $course): ?>
                <div class="big-card">
                    <div>
                        <h2><?php echo htmlspecialchars($course['display_name']); ?></h2>
                        <div class="tag">
                            ID: <?php echo htmlspecialchars($course['id']); ?>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <?php if (!empty($course['moodle_course_url'])): ?>
                            <a href="<?php echo htmlspecialchars($course['moodle_course_url']); ?>" target="_blank" style="text-decoration:none;">
                                🔗 Moodle
                            </a>
                        <?php endif; ?>
                        
                        👤 <?php echo htmlspecialchars($course['enrolled_count'] ?? 0); ?> 
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No active courses available.</p>
        <?php endif; ?>
    </div>


    <div class="action-bar">
        <span>Other Courses</span>
        <a href="#" class="btn-create">+ Create Course</a>
    </div>


    <div class="list-container">
        <?php if (count($other_courses) > 0): ?>
            <?php foreach ($other_courses as $course): ?>
                <div class="list-card">
                    <div class="list-info">
                        <h3><?php echo htmlspecialchars($course['display_name']); ?></h3>
                        <div class="tag">
                             ID: <?php echo htmlspecialchars($course['id']); ?>
                        </div>
                    </div>
                    
                    <div class="arrow-icon">
                        <?php if (!empty($course['moodle_course_url'])): ?>
                            <a href="<?php echo htmlspecialchars($course['moodle_course_url']); ?>" style="text-decoration:none; color:inherit;">
                                &rarr;
                            </a>
                        <?php else: ?>
                            &rarr;
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align:center; color:#999;">No other courses found.</p>
        <?php endif; ?>
    </div>

</body>
</html>