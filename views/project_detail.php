<?ph<?php
require_once 'config/db.php';

// Helper: Generate consistent color from string (reused here for the banner)
if (!function_exists('stringToColor')) {
    function stringToColor($str) {
        $hash = md5($str);
        $colors = ['#0f172a', '#1e1b4b', '#4a044e', '#450a0a', '#064e3b', '#172554', '#312e81', '#881337'];
        return $colors[hexdec(substr($hash, 0, 1)) % count($colors)];
    }
}

$project = null;
$members = [];

try {
    if (isset($projectId)) {
        // Get Project Details
        $stmt = $pdo->prepare("SELECT * FROM group_projects WHERE id = ?");
        $stmt->execute([$projectId]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($project) {
            // Get Members
            $stmt_members = $pdo->prepare("
                SELECT 
                    u.id, 
                    u.first_name, 
                    u.last_name, 
                    CONCAT(u.first_name, ' ', u.last_name) AS full_name, 
                    u.avatar_url,
                    u.faculty_number
                FROM group_project_members gpm
                JOIN users u ON gpm.student_id = u.id
                WHERE gpm.group_project_id = ?
            ");
            $stmt_members->execute([$projectId]);
            $members = $stmt_members->fetchAll(PDO::FETCH_ASSOC);
            
            // Assign color for the banner
            $project['color'] = stringToColor($project['name']);
        }
    }
} catch (PDOException $e) {
    echo "<div class='text-red-500 p-4'>Error loading project: " . htmlspecialchars($e->getMessage()) . "</div>";
}

if (!$project): ?>
    <div class="flex flex-col items-center justify-center min-h-[50vh] text-center">
        <h2 class="text-2xl font-bold mb-4">Project Not Found</h2>
        <ui-button href="<?php echo BASE_URL; ?>/courses/<?php echo htmlspecialchars($courseId); ?>" variant="outline">
            &larr; Back to Course
        </ui-button>
    </div>
<?php return; endif; ?>


<div class="flex items-center gap-2 text-sm text-muted-foreground mb-6">
    <a href="<?php echo BASE_URL; ?>/courses" class="hover:text-foreground transition-colors">Courses</a>
    <span>/</span>
    <a href="<?php echo BASE_URL; ?>/courses/<?php echo htmlspecialchars($courseId); ?>" class="hover:text-foreground transition-colors">
        <?php echo htmlspecialchars($courseId); ?>
    </a>
    <span>/</span>
    <span class="text-foreground font-medium truncate max-w-[200px]">
        <?php echo htmlspecialchars($project['name']); ?>
    </span>
</div>

<div class="relative w-full aspect-[21/9] md:aspect-[21/7] rounded-xl overflow-hidden shadow-2xl mb-8 border border-gray-200 dark:border-gray-800 !overflow-visible">
    <div class="absolute top-4 right-4 z-30">
        <ui-button href="<?php echo BASE_URL; ?>/courses/<?php echo $courseId; ?>/projects/<?php echo $projectId; ?>/edit" variant="secondary" size="sm" class="opacity-90 hover:opacity-100 shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
            Edit Project
        </ui-button>
    </div>

    <project-banner 
        name="<?php echo htmlspecialchars($project['name']); ?>"
        color="<?php echo $project['color']; ?>"
        members='<?php echo json_encode($members); ?>'
    ></project-banner>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <div class="md:col-span-2 space-y-8">
        <section>
            <h3 class="text-lg font-semibold border-b pb-2 mb-3 border-gray-200 dark:border-gray-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                Topic
            </h3>
            <p class="text-xl font-medium leading-relaxed">
                <?php echo htmlspecialchars($project['topic']); ?>
            </p>
        </section>

        <section>
            <h3 class="text-lg font-semibold border-b pb-2 mb-3 border-gray-200 dark:border-gray-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                Description
            </h3>
            <div class="prose dark:prose-invert max-w-none text-gray-600 dark:text-gray-300">
                <?php if (!empty($project['description'])): ?>
                    <?php echo nl2br(htmlspecialchars($project['description'])); ?>
                <?php else : ?>
                    <em class="text-muted-foreground">No description provided for this project.</em>
                <?php endif; ?>
            </div>
        </section>

        <section>
            <h3 class="text-lg font-semibold border-b pb-2 mb-3 border-gray-200 dark:border-gray-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                Resources
            </h3>
            <div class="flex flex-wrap gap-4">
                <a href="#" class="inline-flex items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium shadow-sm hover:bg-accent hover:text-accent-foreground transition-colors gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    Project URL
                </a>
                
                <a href="#" class="inline-flex items-center justify-center rounded-md bg-yellow-500/10 text-yellow-600 dark:text-yellow-500 border border-yellow-500/20 px-4 py-2 text-sm font-medium hover:bg-yellow-500/20 transition-colors gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Download Source Code
                </a>
            </div>
        </section>
    </div>

    <aside>
        <ui-card>
            <ui-card-header>
                <ui-card-title class="text-base font-bold uppercase tracking-wider text-muted-foreground">Team Members</ui-card-title>
            </ui-card-header>
            <ui-card-content class="grid gap-4">
                <?php foreach ($members as $member): ?>
                    <div class="flex items-center gap-3 group">
                        <div class="h-10 w-10 rounded-full overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-100 shrink-0">
                            <?php if (!empty($member['avatar_url'])): ?>
                                <img src="<?php echo htmlspecialchars($member['avatar_url']); ?>" alt="Avatar" class="h-full w-full object-cover">
                            <?php else: ?>
                                <img src="https://api.dicebear.com/9.x/avataaars/svg?seed=<?php echo $member['id']; ?>&backgroundColor=b6e3f4" class="h-full w-full object-cover">
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex flex-col min-w-0">
                            <span class="font-medium text-sm truncate group-hover:text-primary transition-colors">
                                <?php echo htmlspecialchars($member['full_name']); ?>
                            </span>
                            <span class="text-xs text-muted-foreground font-mono">
                                #<?php echo htmlspecialchars($member['faculty_number']); ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </ui-card-content>
        </ui-card>
    </aside>

</div>
