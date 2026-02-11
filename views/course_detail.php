<?php
require_once 'config/db.php';

// Helper: Generate consistent color from string
function stringToColor($str) {
    $hash = md5($str);
    $colors = ['#0f172a', '#1e1b4b', '#4a044e', '#450a0a', '#064e3b', '#172554', '#312e81', '#881337'];
    return $colors[hexdec(substr($hash, 0, 1)) % count($colors)];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'enroll') {
    // Ensure user is logged in and courseId exists
    if (isset($_SESSION['user_id']) && isset($courseId)) {
        try {
            // Check if already enrolled to avoid duplicates
            $checkStmt = $pdo->prepare("SELECT 1 FROM enrollments WHERE student_id = ? AND course_id = ?");
            $checkStmt->execute([$_SESSION['user_id'], $courseId]);

            if (!$checkStmt->fetchColumn()) {
                // Insert new enrollment using MySQL UUID()
                $enrollStmt = $pdo->prepare("
                    INSERT INTO enrollments (id, student_id, course_id, grade) 
                    VALUES (UUID(), ?, ?, NULL)
                ");
                $enrollStmt->execute([$_SESSION['user_id'], $courseId]);
            }

            // JavaScript Redirect to prevent "Headers already sent" error
            echo "<script>window.location.href = window.location.href;</script>";
            exit;

        } catch (PDOException $e) {
            die("Enrollment Error: " . $e->getMessage());
        }
    }
}

$current_course = null;
$projects = [];

$userId = $_SESSION['user_id'] ?? null; 
$userRole = '';
$isEnrolled = false;
$hasProjectInCourse = false;

try {
    if (isset($courseId) && $courseId > 0) {
        // Fetch Course Details
        $stmt = $pdo->prepare("
            SELECT c.*, 
            (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) AS enrolled_count
            FROM courses c 
            WHERE c.id = ?
        ");
        $stmt->execute([$courseId]);
        $current_course = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch User Status (If logged in)
        if ($userId) {
            // Get Role
            $stmtUser = $pdo->prepare("SELECT user_role FROM users WHERE id = ?");
            $stmtUser->execute([$userId]);
            $userRole = $stmtUser->fetchColumn();

            // Check Enrollment
            $stmtEnroll = $pdo->prepare("SELECT 1 FROM enrollments WHERE student_id = ? AND course_id = ?");
            $stmtEnroll->execute([$userId, $courseId]);
            $isEnrolled = (bool)$stmtEnroll->fetchColumn();

            // Check if user is already in a project for this specific course
            $stmtProjCheck = $pdo->prepare("
                SELECT 1 
                FROM group_project_members gpm
                JOIN group_projects gp ON gpm.group_project_id = gp.id
                WHERE gpm.student_id = ? AND gp.course_id = ?
            ");
            $stmtProjCheck->execute([$userId, $courseId]);
            $hasProjectInCourse = (bool)$stmtProjCheck->fetchColumn();
        }

        // Fetch Projects
        $stmt_proj = $pdo->prepare("SELECT * FROM group_projects WHERE course_id = ?");
        $stmt_proj->execute([$courseId]);
        $raw_projects = $stmt_proj->fetchAll(PDO::FETCH_ASSOC);

        foreach ($raw_projects as $p) {
            $stmt_mem = $pdo->prepare("
                SELECT u.id, u.first_name, u.last_name, u.avatar_url, u.faculty_number 
                FROM group_project_members gpm 
                JOIN users u ON gpm.student_id = u.id 
                WHERE gpm.group_project_id = ?
            ");
            $stmt_mem->execute([$p['id']]);

            $p['members'] = $stmt_mem->fetchAll(PDO::FETCH_ASSOC);
            $p['color'] = stringToColor($p['name']);
            $projects[] = $p;
        }
    }
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

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

            <?php if (!empty($current_course['moodle_course_url'])) : ?>
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
</div>

<div class="flex flex-col md:flex-row items-center justify-between border-b pb-4 mb-6 gap-4">
    <div class="flex items-center gap-3 w-full md:w-auto">
        <h2 class="text-2xl font-bold tracking-tight whitespace-nowrap">Projects</h2>
        <span id="projectCount" class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-sm font-mono px-2 py-0.5 rounded-full">
            <?php echo count($projects); ?>
        </span>
    </div>

    <div class="flex items-center gap-4 w-full md:w-auto">
        <div class="relative w-full md:w-64">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-2.5 top-2.5 text-muted-foreground"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <input 
                type="text" 
                id="projectSearch" 
                placeholder="Search name, theme, desc..." 
                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 pl-9"
            >
        </div>

        <?php if ($userRole === 'admin'): ?>
            <ui-button href="<?php echo BASE_URL; ?>/courses/<?php echo $courseId; ?>/edit" variant="outline" class="gap-2 whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                Edit Course
            </ui-button>
        <?php endif; ?>

        <?php if ($userRole === 'student') : ?>
            <?php if (!$isEnrolled) : ?>
                <form action="" method="POST">
                    <input type="hidden" name="action" value="enroll">
                    <ui-button type="submit" class="gap-2 whitespace-nowrap bg-green-600 hover:bg-green-700 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        Enroll in Course
                    </ui-button>
                </form>

            <?php elseif ($isEnrolled && !$hasProjectInCourse) : ?>
                <ui-button href="<?php echo BASE_URL; ?>/courses/<?php echo $courseId; ?>/project-create" class="gap-2 whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                    Create Project
                </ui-button>

            <?php endif; ?>

        <?php endif; ?>
    </div>
</div>

<div id="projectsGrid" class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <?php if (count($projects) > 0) : ?>
        <?php foreach ($projects as $project) : ?>
            <ui-card 
                class="project-card-item group flex flex-col rounded-xl border-2 border-transparent hover:border-gray-200 dark:hover:border-gray-700 transition-all duration-300 hover:shadow-2xl relative cursor-pointer !py-0 !overflow-visible !gap-0"
                data-searchable="<?php echo htmlspecialchars(strtolower($project['name'] . ' ' . ($project['topic'] ?? '') . ' ' . ($project['description'] ?? ''))); ?>"
            >
                <div class="w-full aspect-[21/9] bg-gray-100 dark:bg-gray-900 relative rounded-t-xl z-20 pointer-events-none">
                    <a href="<?php echo BASE_URL; ?>/courses/<?php echo $courseId; ?>/projects/<?php echo $project['id']; ?>" class="absolute inset-0 z-10 rounded-xl pointer-events-none">
                        <span class="sr-only">View Project</span>

                        <project-banner 
                            name="<?php echo htmlspecialchars($project['name']); ?>"
                            color="<?php echo $project['color']; ?>"
                            members='<?php echo json_encode($project['members']); ?>'
                        ></project-banner>
                    </a>
                </div>

                <ui-card-content class="flex flex-col flex-grow p-5 pt-6 relative z-20 bg-white dark:bg-black rounded-b-xl pointer-events-none">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-bold text-lg leading-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                            <?php echo htmlspecialchars($project['name']); ?>
                        </h3>
                    </div>

                    <?php if (!empty($project['topic'])) : ?>
                        <span class="text-xs font-semibold uppercase tracking-wider text-primary mb-2"><?php echo htmlspecialchars($project['topic']); ?></span>
                    <?php endif; ?>

                    <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mb-4">
                        <?php echo htmlspecialchars($project['description'] ?? 'No description provided.'); ?>
                    </p>

                    <div class="mt-auto flex items-center justify-between border-t border-gray-100 dark:border-gray-800 pt-4">
                        <div class="flex items-center gap-2 text-xs font-mono text-gray-400 uppercase">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            <span>Team of <?php echo count($project['members']); ?></span>
                        </div>

                        <span class="text-sm font-medium flex items-center gap-1 group-hover:gap-2 transition-all text-gray-900 dark:text-gray-100">
                            Open
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </span>
                    </div>
                </ui-card-content>
            </ui-card>

        <?php endforeach; ?>

        <div id="noResultsMsg" class="hidden col-span-full py-16 flex flex-col items-center justify-center text-center">
              <p class="text-muted-foreground">No matches for this search.</p>
        </div>
    <?php else : ?>
        <div class="col-span-full py-16 flex flex-col items-center justify-center text-center border-2 border-dashed border-gray-200 dark:border-gray-800 rounded-xl">
            <h3 class="text-xl font-bold">No projects yet</h3>
            <?php if ($userRole === 'student' && $isEnrolled && !$hasProjectInCourse) : ?>
                <ui-button href="<?php echo BASE_URL; ?>/courses/<?php echo $courseId; ?>/project-create" variant="outline" class="mt-4">
                    Create Project
                </ui-button>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('projectSearch');
    const projects = document.querySelectorAll('.project-card-item');
    const noResultsMsg = document.getElementById('noResultsMsg');
    const projectCountLabel = document.getElementById('projectCount');
    let timeout = null;

    if(searchInput) {
        searchInput.addEventListener('input', (e) => {
            clearTimeout(timeout);
            const term = e.target.value.toLowerCase().trim();

            timeout = setTimeout(() => {
                let visibleCount = 0;
                projects.forEach(card => {
                    const data = card.getAttribute('data-searchable');
                    if (data.includes(term)) {
                        card.style.display = ''; 
                        visibleCount++;
                    } else {
                        card.style.display = 'none'; 
                    }
                });

                if (noResultsMsg) {
                    noResultsMsg.style.display = (visibleCount === 0 && projects.length > 0) ? 'flex' : 'none';
                }
                if (projectCountLabel) {
                    projectCountLabel.textContent = visibleCount;
                }
            }, 250); 
        });
    }
});
</script>
