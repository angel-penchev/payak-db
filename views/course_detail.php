<?php
// config/db.php is assumed to be included by the router
require_once 'config/db.php';

// Helper: Generate consistent pastel/vibrant color from string
function stringToColor($str) {
    $hash = md5($str);
    // A palette of nice colors for banners
    $colors = ['#0f172a', '#1e1b4b', '#4a044e', '#450a0a', '#064e3b', '#172554', '#312e81', '#881337'];
    return $colors[hexdec(substr($hash, 0, 1)) % count($colors)];
}

$current_course = null;
$projects = [];

try {
    if (isset($courseId) && $courseId > 0) {
        // 1. Fetch Course Details
        $stmt = $pdo->prepare("
            SELECT c.*, 
            (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) AS enrolled_count
            FROM courses c 
            WHERE c.id = ?
        ");
        $stmt->execute([$courseId]);
        $current_course = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Fetch Projects
        $stmt_proj = $pdo->prepare("SELECT * FROM group_projects WHERE course_id = ?");
        $stmt_proj->execute([$courseId]);
        $raw_projects = $stmt_proj->fetchAll(PDO::FETCH_ASSOC);

        // 3. Process Projects (Get REAL Members & Colors)
        foreach ($raw_projects as $p) {
            
            // Query: Fetch members from group_project_members joined with users
            $stmt_mem = $pdo->prepare("
                SELECT u.id, u.first_name, u.last_name, u.avatar_url, u.faculty_number 
                FROM group_project_members gpm 
                JOIN users u ON gpm.student_id = u.id 
                WHERE gpm.group_project_id = ?
            ");
            $stmt_mem->execute([$p['id']]);
            
            $p['members'] = $stmt_mem->fetchAll(PDO::FETCH_ASSOC);
            $p['color'] = stringToColor($p['name']); // Generate specific color
            $projects[] = $p;
        }
    }
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

// Redirect if invalid course
if (!$current_course) {
    echo "<div class='text-center py-20 text-red-500'>Course not found.</div>";
    exit;
}
?>

<div class="mb-12">
    <div class="flex flex-col md:flex-row gap-6 justify-between items-start md:items-center mb-6">
        <div>
            <div class="flex items-center gap-2 text-sm text-muted-foreground mb-2">
                <a href="<?php echo BASE_URL; ?>/courses" class="hover:text-foreground transition-colors">Courses</a>
                <span>/</span>
                <span class="font-mono text-xs px-1.5 py-0.5 rounded bg-muted/50">
                    <?php echo htmlspecialchars($current_course['id']); ?>
                </span>
            </div>
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight">
                <?php echo htmlspecialchars($current_course['display_name']); ?>
            </h1>
        </div>

        <div class="flex items-center gap-4 bg-white dark:bg-gray-900 p-2 rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm">
            
            <div class="flex items-center gap-2 px-3 py-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-500">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                <div class="flex flex-col">
                    <span class="text-xs font-bold uppercase text-muted-foreground leading-none">Students</span>
                    <span class="font-bold leading-none"><?php echo $current_course['enrolled_count']; ?></span>
                </div>
            </div>

            <div class="w-px h-8 bg-gray-200 dark:bg-gray-800"></div>

            <?php if (!empty($current_course['moodle_course_url'])): ?>
                <a href="<?php echo htmlspecialchars($current_course['moodle_course_url']); ?>" 
                    target="_blank" 
                    class="flex items-center gap-2 px-3 py-1 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors group">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-orange-500 group-hover:scale-110 transition-transform">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
                    </svg>
                    <div class="flex flex-col">
                        <span class="text-xs font-bold uppercase text-muted-foreground leading-none">Moodle</span>
                        <span class="font-bold leading-none text-xs">Open</span>
                    </div>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="flex items-center gap-4 text-sm text-muted-foreground border-l-2 border-primary pl-4">
        <div class="flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Starts: <span class="text-foreground font-medium"><?php echo $current_course['opens_at_date']; ?></span>
        </div>
        <div class="flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Ends: <span class="text-foreground font-medium"><?php echo $current_course['closes_at_date']; ?></span>
        </div>
    </div>
</div>

<div class="flex items-center justify-between border-b pb-4 mb-6">
    <h2 class="text-2xl font-bold tracking-tight flex items-center gap-3">
        Projects
        <span class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-sm font-mono px-2 py-0.5 rounded-full">
            <?php echo count($projects); ?>
        </span>
    </h2>

    <ui-button href="<?php echo BASE_URL; ?>/courses/<?php echo $courseId; ?>/project-create" class="gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
        Create Project
    </ui-button>
</div>    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <?php if (count($projects) > 0): ?>
        <?php foreach ($projects as $project): ?>
            
            <ui-card class="group flex flex-col rounded-xl border-2 border-transparent hover:border-gray-200 dark:hover:border-gray-700 transition-all duration-300 hover:shadow-2xl !p-0 !overflow-visible">
                <div class="w-full aspect-[21/9] bg-gray-100 dark:bg-gray-900 relative rounded-t-xl z-20">
                    <project-banner 
                        name="<?php echo htmlspecialchars($project['name']); ?>"
                        color="<?php echo $project['color']; ?>"
                        members='<?php echo json_encode($project['members']); ?>'
                    ></project-banner>
                </div>

                <ui-card-content class="flex flex-col flex-grow p-5 pt-6 relative z-10 bg-white dark:bg-black rounded-b-xl">
                    <div class="flex justify-between items-start mb-2">
                          <a href="<?php echo BASE_URL; ?>/courses/<?php echo $courseId; ?>/projects/<?php echo $project['id']; ?>" class="group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                            <h3 class="font-bold text-lg leading-tight">
                                <?php echo htmlspecialchars($project['name']); ?>
                            </h3>
                          </a>
                    </div>

                    <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mb-4">
                        <?php echo htmlspecialchars($project['description'] ?? 'No description provided.'); ?>
                    </p>

                    <div class="mt-auto flex items-center justify-between border-t border-gray-100 dark:border-gray-800 pt-4">
                        <div class="flex items-center gap-2 text-xs font-mono text-gray-400 uppercase">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            <span>Team of <?php echo count($project['members']); ?></span>
                        </div>
                        
                        <a href="<?php echo BASE_URL; ?>/courses/<?php echo $courseId; ?>/projects/<?php echo $project['id']; ?>" class="text-sm font-medium flex items-center gap-1 hover:gap-2 transition-all text-gray-900 dark:text-gray-100">
                            Open
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </a>
                    </div>
                </ui-card-content>

            </ui-card>

        <?php endforeach; ?>
    <?php else: ?>
        
        <div class="col-span-full py-16 flex flex-col items-center justify-center text-center border-2 border-dashed border-gray-200 dark:border-gray-800 rounded-xl">
            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            </div>
            <h3 class="text-xl font-bold">No projects yet</h3>
            <p class="text-gray-500 max-w-sm mt-2 mb-6">Start collaborating by creating the first project for this course.</p>
            <ui-button href="<?php echo BASE_URL; ?>/courses/<?php echo $courseId; ?>/project-create" variant="outline">
                Create Project
            </ui-button>
        </div>

    <?php endif; ?>
</div>
