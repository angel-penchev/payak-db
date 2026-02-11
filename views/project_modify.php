<?php
require_once 'config/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$current_user_id = $_SESSION['user_id'] ?? null;
if (!$current_user_id) {
    echo "<script>window.location.href = '" . BASE_URL . "/login';</script>";
    exit;
}

// Fetch current user details
$stmt = $pdo->prepare("SELECT faculty_number, first_name, last_name FROM users WHERE id = ? LIMIT 1");
$stmt->execute([$current_user_id]);
$currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$currentUser) die("User error.");

$myFn = $currentUser['faculty_number'];
$myName = $currentUser['first_name'] . ' ' . $currentUser['last_name'];

$isEditMode = isset($projectId) && !empty($projectId);
$error = '';
$success = '';

// Data Containers
$pName = '';
$pTopic = '';
$pDesc = '';
$teamMembers = []; 

if ($isEditMode && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $stmt = $pdo->prepare("SELECT * FROM group_projects WHERE id = ?");
    $stmt->execute([$projectId]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$project) die("Project not found.");

    // Get Members
    $stmtMem = $pdo->prepare("
        SELECT u.faculty_number 
        FROM group_project_members gpm
        JOIN users u ON gpm.student_id = u.id
        WHERE gpm.group_project_id = ?
    ");
    $stmtMem->execute([$projectId]);
    $mems = $stmtMem->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array($myFn, $mems)) {
        echo "<script>window.location.href = '" . BASE_URL . "/courses/" . $courseId . "';</script>";
        exit;
    }

    $pName = $project['name'];
    $pTopic = $project['topic'];
    $pDesc = $project['description'];

    $teamMembers = array_values(array_diff($mems, [$myFn]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pName = trim($_POST['name'] ?? '');
    $pTopic = trim($_POST['topic'] ?? '');
    $pDesc = trim($_POST['description'] ?? '');
    
    $candidates = $_POST['members'] ?? [];
    $teamMembers = $candidates; // Keep for view on error
    
    $finalTeam = [$myFn];

    // Validate Candidates
    foreach ($candidates as $fn) {
        $fn = trim($fn);
        if (!empty($fn)) {
            // Check if user exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE faculty_number = ?");
            $stmt->execute([$fn]);
            if (!$stmt->fetch()) {
                $error = "Student with Faculty Number '$fn' not found.";
                break;
            }
            // Avoid adding myself
            if ($fn === $myFn) {
                $error = "You don't need to add yourself.";
                break;
            }
            // Avoid duplicates
            if (!in_array($fn, $finalTeam)) {
                $finalTeam[] = $fn;
            }
        }
    }

    if (!$error && (empty($pName) || empty($pTopic))) {
        $error = "Project Name and Topic are required.";
    }

    // Check availability
    if (!$error) {
        $placeholders = implode(',', array_fill(0, count($finalTeam), '?'));
        
        $excludeSql = $isEditMode ? "AND gp.id != ?" : "";
        $params = array_merge([$courseId], $finalTeam);
        if ($isEditMode) $params[] = $projectId;

        $sqlCheck = "SELECT u.first_name, u.last_name, u.faculty_number 
                     FROM group_project_members gpm 
                     JOIN group_projects gp ON gpm.group_project_id = gp.id 
                     JOIN users u ON gpm.student_id = u.id 
                     WHERE gp.course_id = ? AND u.faculty_number IN ($placeholders) $excludeSql";
        
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute($params);
        $busy = $stmtCheck->fetchAll(PDO::FETCH_ASSOC);

        if ($busy) {
            $names = array_map(function($s){ return $s['first_name'].' '.$s['last_name'] . ' (' . $s['faculty_number'] . ')';}, $busy);
            $error = "Students already in another project: " . implode(', ', $names);
        }
    }

    // SAVE TO DB
    if (!$error) {
        try {
            $pdo->beginTransaction();
            
            function gen_uuid_v4() {
                return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
            }

            $targetId = $isEditMode ? $projectId : gen_uuid_v4();

            if ($isEditMode) {
                $stmt = $pdo->prepare("UPDATE group_projects SET name = ?, topic = ?, description = ? WHERE id = ?");
                $stmt->execute([$pName, $pTopic, $pDesc, $targetId]);
                $pdo->prepare("DELETE FROM group_project_members WHERE group_project_id = ?")->execute([$targetId]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO group_projects (id, course_id, name, topic, description) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$targetId, $courseId, $pName, $pTopic, $pDesc]);
            }

            foreach ($finalTeam as $fn) {
                $stmtU = $pdo->prepare("SELECT id FROM users WHERE faculty_number = ?");
                $stmtU->execute([$fn]);
                $uId = $stmtU->fetchColumn();
                if ($uId) {
                    $pdo->prepare("INSERT INTO group_project_members (id, group_project_id, student_id) VALUES (?, ?, ?)")
                        ->execute([gen_uuid_v4(), $targetId, $uId]);
                }
            }

            $pdo->commit();
            
            // FIX: Use JS Redirect to avoid blank page (Headers already sent issue)
            $redirectUrl = BASE_URL . "/courses/" . $courseId . "/projects/" . $targetId;
            echo "<script>window.location.href = '" . $redirectUrl . "';</script>";
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Database Error: " . $e->getMessage();
        }
    }
}
?>

<div class="flex justify-center items-center min-h-[calc(100vh-200px)] py-10">
    <div class="w-full max-w-2xl px-4">
        
        <?php if ($error): ?>
            <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-lg border border-red-200 text-sm font-medium">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <ui-card class="shadow-xl">
            <ui-card-header>
                <ui-card-title class="text-2xl font-black tracking-tight">
                    <?php echo $isEditMode ? "Edit Project" : "Create Project"; ?>
                </ui-card-title>
                <ui-card-description>
                    <?php echo $isEditMode ? "Update your team's project details." : "Define your topic and assemble your team."; ?>
                </ui-card-description>
            </ui-card-header>

            <ui-card-content>
                <form method="POST" action="" class="grid gap-6">
                    
                    <div class="grid gap-3 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-100 dark:border-gray-800">
                        <label class="text-xs font-bold uppercase tracking-wider text-muted-foreground mb-1">Team Members (Max 3)</label>
                        
                        <div class="flex gap-2">
                            <input 
                                type="text" 
                                id="fnInput" 
                                placeholder="Enter Faculty Number (e.g. 0MI0000)" 
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 flex-1"
                                onkeypress="handleEnter(event)"
                            >
                            <button 
                                type="button" 
                                onclick="addMember()" 
                                class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                            </button>
                        </div>
                        <p id="listError" class="text-xs text-red-500 font-medium min-h-[1rem]"></p>

                        <ul id="memberList" class="space-y-2 mt-1">
                            
                            <li class="flex items-center justify-between p-3 bg-white dark:bg-black rounded-md border border-gray-200 dark:border-gray-800 shadow-sm">
                                <span class="flex items-center gap-2">
                                    <span class="h-6 w-6 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 flex items-center justify-center text-xs font-bold shrink-0">1</span>
                                    <span class="text-sm"><strong><?php echo htmlspecialchars($myFn); ?></strong> (You)</span>
                                </span>
                            </li>

                            <?php foreach ($teamMembers as $memFn): ?>
                                <li class="member-item flex items-center justify-between p-3 bg-white dark:bg-black rounded-md border border-gray-200 dark:border-gray-800 shadow-sm animate-in fade-in slide-in-from-left-2 duration-300">
                                    <span class="flex items-center gap-2">
                                        <span class="counter h-6 w-6 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 flex items-center justify-center text-xs font-bold shrink-0">?</span>
                                        <span class="text-sm font-mono"><?php echo htmlspecialchars($memFn); ?></span>
                                    </span>
                                    <input type="hidden" name="members[]" value="<?php echo htmlspecialchars($memFn); ?>">
                                    <button type="button" onclick="removeMember(this)" class="text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 p-1 rounded transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                    </button>
                                </li>
                            <?php endforeach; ?>

                        </ul>
                    </div>

                    <div class="grid gap-2">
                        <ui-label>Project Name</ui-label>
                        <ui-input 
                            name="name" 
                            placeholder="e.g. Smart Home Dashboard" 
                            value="<?php echo htmlspecialchars($pName); ?>" 
                            required>
                        </ui-input>
                    </div>

                    <div class="grid gap-2">
                        <ui-label>Topic</ui-label>
                        <ui-input 
                            name="topic" 
                            placeholder="e.g. IoT & WebSockets" 
                            value="<?php echo htmlspecialchars($pTopic); ?>" 
                            required>
                        </ui-input>
                    </div>

                    <div class="grid gap-2">
                        <ui-label>Description</ui-label>
                        <textarea 
                            name="description" 
                            rows="4" 
                            class="flex w-full rounded-md border border-input bg-white dark:bg-black px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            placeholder="Briefly describe your project..."
                        ><?php echo htmlspecialchars($pDesc); ?></textarea>
                    </div>

                    <div class="flex flex-row-reverse gap-4 pt-4">
                        <ui-button type="submit" class="flex-1">
                            <?php echo $isEditMode ? "Save Changes" : "Create Project"; ?>
                        </ui-button>
                        
                        <ui-button href="<?php echo BASE_URL; ?>/courses/<?php echo htmlspecialchars($courseId); ?>" variant="ghost" class="flex-1">
                            Cancel
                        </ui-button>
                    </div>

                </form>
            </ui-card-content>
        </ui-card>
    </div>
</div>

<script>
    function updateCounters() {
        let count = 2; // Start from 2 because "1" is You
        document.querySelectorAll('.member-item .counter').forEach(el => {
            el.innerText = count++;
        });
    }

    function addMember() {
        const input = document.getElementById('fnInput');
        const list = document.getElementById('memberList');
        const error = document.getElementById('listError');
        const val = input.value.trim().toUpperCase(); 
        const myFn = "<?php echo $myFn; ?>";

        error.innerText = "";

        if (!val) return;

        if (val === myFn) {
            error.innerText = "You are already in the team (Member 1).";
            return;
        }

        let exists = false;
        document.querySelectorAll('input[name="members[]"]').forEach(el => {
            if (el.value === val) exists = true;
        });
        if (exists) {
            error.innerText = "This student is already in the list.";
            return;
        }

        if (document.querySelectorAll('.member-item').length >= 2) {
            error.innerText = "Maximum team size is 3.";
            return;
        }

        const li = document.createElement('li');
        li.className = "member-item flex items-center justify-between p-3 bg-white dark:bg-black rounded-md border border-gray-200 dark:border-gray-800 shadow-sm animate-in fade-in slide-in-from-left-2 duration-300";
        li.innerHTML = `
            <span class="flex items-center gap-2">
                <span class="counter h-6 w-6 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 flex items-center justify-center text-xs font-bold shrink-0"></span>
                <span class="text-sm font-mono">${val}</span>
            </span>
            <input type="hidden" name="members[]" value="${val}">
            <button type="button" onclick="removeMember(this)" class="text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 p-1 rounded transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        `;
        
        list.appendChild(li);
        input.value = "";
        updateCounters();
        input.focus();
    }

    function removeMember(btn) {
        btn.closest('li').remove();
        updateCounters();
    }

    function handleEnter(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            addMember();
        }
    }

    document.addEventListener('DOMContentLoaded', updateCounters);
</script>
