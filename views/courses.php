<?php
try {
    $sql = "SELECT c.*, 
            (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) AS enrolled_count
            FROM courses c
            ORDER BY c.id DESC";

    $stmt = $pdo->query($sql);
    $all_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 1. Slice the first 2 for the top "Big Box" section
    $active_courses = array_slice($all_courses, 0, 2);

    // 2. Slice the rest (starting from index 2) for the bottom list
    $other_courses = array_slice($all_courses, 2);

    foreach ($temp_active as $course) {
        $stmt_proj = $pdo->prepare("SELECT * FROM group_projects WHERE course_id = ?");
        $stmt_proj->execute([$course['id']]);
        $projects = $stmt_proj->fetchAll(PDO::FETCH_ASSOC);

        // Add the projects array to the course data so we can access it in the HTML
        $course['projects'] = $projects;
        $active_courses[] = $course;
    }

} catch (PDOException $e) {
    die("Error fetching courses: " . $e->getMessage());
}
?>

<main class="px-4 mx-auto max-w-[1450px]">
    <div class="mb-8 text-center relative w-full">
        <h1 class="text-2xl font-bold inline-flex items-center gap-4 before:content-[''] before:w-12 before:h-0.5 before:bg-primary after:content-[''] after:w-12 after:h-0.5 after:bg-primary">
            Active Courses
        </h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
        <?php if (count($active_courses) > 0) : ?>
            <?php foreach ($active_courses as $course) : ?>
                <ui-card>
                    <ui-card-header>
                        <ui-card-title>
                            <?php echo htmlspecialchars($course['display_name']); ?>
                        </ui-card-title>
                        <ui-card-description>
                            Course ID: <?php echo htmlspecialchars($course['id']); ?>
                        </ui-card-description>
                    </ui-card-header>

                    <ui-card-content class="flex-grow">
                        <div class="flex items-center gap-2 text-2xl font-semibold text-primary">
                            <span>👤</span>
                            <span><?php echo htmlspecialchars($course['enrolled_count'] ?? 0); ?></span>
                        </div>
                        <p class="text-xs text-muted-foreground mt-1">Active Students</p>
                    </ui-card-content>

                    <ui-card-footer class="justify-between">
                        <span class="text-xs text-muted-foreground">Status: Active</span>
                        <?php if (!empty($course['moodle_course_url'])) : ?>
                            <a href="<?php echo htmlspecialchars($course['moodle_course_url']); ?>" 
                               target="_blank" 
                               class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-4 py-2">
                                Open Moodle 🔗
                            </a>
                        <?php endif; ?>
                    </ui-card-footer>
                </ui-card>

            <?php endforeach; ?>
        <?php else : ?>
            <div class="col-span-2 text-center text-muted-foreground p-8 border rounded-xl border-dashed">
                No active courses available.
            </div>
        <?php endif; ?>
    </div>


    <div class="flex items-center justify-between border-t py-6 mt-6">
        <span class="text-lg font-semibold tracking-tight">Other Courses</span>
        <a href="#" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2 text-red-500 border-red-200 hover:bg-red-50">
            + Create Course
        </a>
    </div>


    <div class="flex flex-col gap-3">
        <?php if (count($other_courses) > 0): ?>
            <?php foreach ($other_courses as $course): ?>
                
                <ui-card size="sm">
                    <ui-card-header>
                        <div class="grid gap-0.5">
                            <ui-card-title>
                                <?php echo htmlspecialchars($course['display_name']); ?>
                            </ui-card-title>
                            <ui-card-description>
                                ID: <?php echo htmlspecialchars($course['id']); ?>
                            </ui-card-description>
                        </div>
                        
                        <ui-card-action>
                            <?php if (!empty($course['moodle_course_url'])): ?>
                                <a href="<?php echo htmlspecialchars($course['moodle_course_url']); ?>" 
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-muted hover:bg-muted-foreground/20 transition-colors">
                                    &rarr;
                                </a>
                            <?php else: ?>
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-muted/50 text-muted-foreground opacity-50 cursor-not-allowed">
                                    &rarr;
                                </span>
                            <?php endif; ?>
                        </ui-card-action>
                    </ui-card-header>
                </ui-card>

            <?php endforeach; ?>
        <?php else : ?>
            <p class="text-center text-muted-foreground py-4">No other courses found.</p>
        <?php endif; ?>
    </div>
</main>

