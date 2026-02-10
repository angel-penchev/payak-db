<?php
try {
    // Get today's date
    $today = date('Y-m-d');

    // Fetch all courses with enrolled count
    $sql = "SELECT c.*, 
            (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) AS enrolled_count
            FROM courses c
            ORDER BY c.opens_at_date DESC"; // Ordered by start date usually makes more sense here, but c.id is fine too

    $stmt = $pdo->query($sql);
    $all_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Initialize arrays
    $active_courses = [];
    $other_courses = [];

    // Filter logic
    foreach ($all_courses as $course) {
        // Check if today is between start and end date (inclusive)
        $isActive = ($today >= $course['opens_at_date'] && $today <= $course['closes_at_date']);

        if ($isActive) {
            // Fetch projects ONLY for active courses (optimization)
            $stmt_proj = $pdo->prepare("SELECT * FROM group_projects WHERE course_id = ?");
            $stmt_proj->execute([$course['id']]);
            $course['projects'] = $stmt_proj->fetchAll(PDO::FETCH_ASSOC);

            $active_courses[] = $course;
        } else {
            // All inactive courses go here
            $other_courses[] = $course;
        }
    }
} catch (PDOException $e) {
    die("Error fetching courses: " . $e->getMessage());
}

?><main class="px-4 mx-auto w-full max-w-6xl">
    <div class="mb-8 text-center relative w-full">
        <h1 class="text-2xl font-bold inline-flex items-center gap-4 before:content-[''] before:w-12 before:h-0.5 before:bg-primary after:content-[''] after:w-12 after:h-0.5 after:bg-primary">
            Active Courses
        </h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10 auto-rows-fr w-full">
        <?php if (count($active_courses) > 0) : ?>
            <?php foreach ($active_courses as $course) : ?>
                <ui-card class="relative group hover:shadow-lg transition-all duration-200 cursor-pointer min-h-[18rem] border-2 border-transparent hover:border-gray-100 flex flex-col">
                    <a href="<?php echo BASE_URL; ?>/courses/<?php echo $course['id']; ?>" 
                       class="absolute inset-0 z-0">
                        <span class="sr-only">View Course</span>
                    </a>

                    <ui-card-content class="flex-grow flex flex-col p-6 w-full">

                        <div class="flex-grow"></div>

                        <div class="flex-grow-0 flex flex-col justify-center mb-4">
                            <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight leading-tight break-words">
                                <?php echo htmlspecialchars($course['display_name']); ?>
                            </h2>
                            <p class="font-mono text-muted-foreground mt-2 text-lg break-all">
                                #<?php echo htmlspecialchars($course['id']); ?>
                            </p>
                        </div>

                        <div class="flex-grow-0 flex items-end justify-end pt-2 mt-auto">
                            <div class="flex items-center gap-3 text-muted-foreground">
                                <?php if (!empty($course['moodle_course_url'])) : ?>
                                    <a href="<?php echo htmlspecialchars($course['moodle_course_url']); ?>" 
                                       target="_blank" 
                                       title="Open Moodle"
                                       class="relative z-10 hover:text-orange-500 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                                            <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                                        </svg>
                                    </a>
                                    <div class="h-5 w-px bg-gray-500/30"></div>
                                <?php endif; ?>

                                <div class="flex items-center gap-1.5" title="Enrolled Students">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                    <span class="text-lg font-bold">
                                        <?php echo htmlspecialchars($course['enrolled_count'] ?? 0); ?>
                                    </span>
                                </div>

                                <div class="flex items-center gap-1.5" title="Total Projects">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="2" x2="22" y1="12" y2="12"></line>
                                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1 4-10z"></path>
                                    </svg>
                                    <span class="text-lg font-bold">
                                        <?php echo count($course['projects'] ?? []); ?>
                                    </span>
                                </div>

                            </div>
                        </div>

                    </ui-card-content>
                </ui-card>

            <?php endforeach; ?>
        <?php else : ?>
            <div class="col-span-2 text-center text-muted-foreground p-8 border rounded-xl border-dashed">
                No active courses available.
            </div>
        <?php endif; ?>
    </div>

    <div class="flex items-center justify-between border-t p-4 mt-6 mx-auto w-full md:w-1/2">
        <span class="text-lg font-semibold tracking-tight">Other Courses</span>
        <a href="<?php echo BASE_URL; ?>/courses/course-create" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2 text-red-500 border-red-200 hover:bg-red-50">
            + Create Course
        </a>
    </div>

    <div class="flex flex-col gap-3 mx-auto w-full md:w-1/2">
        <?php if (count($other_courses) > 0) : ?>
            <?php foreach ($other_courses as $course) : ?>
                <ui-card size="sm" class="relative group transition-all duration-200 cursor-pointer border-2 border-transparent hover:border-gray-100 hover:shadow-lg">
                    <a href="<?php echo BASE_URL; ?>/courses/<?php echo $course['id']; ?>" class="absolute inset-0 z-0"><span class="sr-only">View Course</span></a>
                    <ui-card-header>
                        <div class="grid gap-0.5">
                            <ui-card-title><?php echo htmlspecialchars($course['display_name']); ?></ui-card-title>
                            <ui-card-description>#<?php echo htmlspecialchars($course['id']); ?></ui-card-description>
                        </div>
                        <ui-card-action>
                            <?php if (!empty($course['moodle_course_url'])) : ?>
                                <a href="<?php echo htmlspecialchars($course['moodle_course_url']); ?>" target="_blank" class="relative z-10 inline-flex h-8 w-8 items-center justify-center rounded-full bg-muted hover:bg-muted-foreground/20 transition-colors">&rarr;</a>
                            <?php else : ?>
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-muted/50 text-muted-foreground opacity-50 cursor-not-allowed">&rarr;</span>
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
