<?php
// Note: $pdo and $courseId are available from index.php
require_once 'config/db.php';

function gen_uuid() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
}

// ---------------------------------------------------------
// 1. GET CURRENT USER
// ---------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) session_start();
$current_user_fn = '???';
$current_user_id = $_SESSION['user_id'] ?? null;

if ($current_user_id) {
    $stmt = $pdo->prepare("SELECT faculty_number FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$current_user_id]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($u) $current_user_fn = $u['faculty_number'];
}

// ---------------------------------------------------------
// 2. HANDLE REQUESTS (JSON RESPONSES)
// ---------------------------------------------------------

// A. Check FN Existence (Helper)
if (isset($_GET['action']) && $_GET['action'] === 'check_fn') {
    ob_clean(); header('Content-Type: application/json');
    $fn = $_GET['fn'] ?? '';
    $stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE faculty_number = ? LIMIT 1");
    $stmt->execute([$fn]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        if ($fn === $current_user_fn) echo json_encode(['exists' => false, 'error' => 'own_fn']);
        else echo json_encode(['exists' => true, 'name' => $user['first_name'] . ' ' . $user['last_name']]);
    } else echo json_encode(['exists' => false]);
    exit;
}

// B. SAVE PROJECT (Main Logic)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'save_project') {
    ob_clean(); header('Content-Type: application/json');
    
    $pName = trim($_POST['project_name'] ?? '');
    $pTopic = trim($_POST['topic'] ?? '');
    $pDesc = trim($_POST['description'] ?? '');
    $pMembers = $_POST['team_members'] ?? [];

    // Ensure current user is in the list
    if (!in_array($current_user_fn, $pMembers)) array_unshift($pMembers, $current_user_fn);
    $pMembers = array_unique(array_filter($pMembers));

    $error = null;

    // Validation
    if (count($pMembers) > 3) $error = "The team cannot exceed 3 members.";
    elseif (empty($pName) || empty($pTopic)) $error = "Project name and topic are required.";

    // Check availability
    if (!$error) {
        $placeholders = implode(',', array_fill(0, count($pMembers), '?'));
        $sqlCheck = "SELECT u.first_name, u.last_name 
                     FROM group_project_members gpm 
                     JOIN group_projects gp ON gpm.group_project_id = gp.id 
                     JOIN users u ON gpm.student_id = u.id 
                     WHERE gp.course_id = ? AND u.faculty_number IN ($placeholders)";
        
        $params = array_merge([$courseId], $pMembers);
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute($params);
        $busy = $stmtCheck->fetchAll(PDO::FETCH_ASSOC);

        if ($busy) {
            $names = array_map(function($s){ return $s['first_name'].' '.$s['last_name'];}, $busy);
            $error = "Students already busy: " . implode(', ', $names);
        }
    }

    if ($error) {
        echo json_encode(['success' => false, 'message' => $error]);
        exit;
    }

    try {
        $pdo->beginTransaction();
        
        $targetId = gen_uuid();
        $stmt = $pdo->prepare("INSERT INTO group_projects (id, course_id, name, topic, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$targetId, $courseId, $pName, $pTopic, $pDesc]);

        foreach ($pMembers as $fn) {
            $uStmt = $pdo->prepare("SELECT id FROM users WHERE faculty_number=?");
            $uStmt->execute([$fn]);
            $u = $uStmt->fetch();
            if ($u) {
                $pdo->prepare("INSERT INTO group_project_members (id, group_project_id, student_id) VALUES (?, ?, ?)")
                    ->execute([gen_uuid(), $targetId, $u['id']]);
            }
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'redirect' => "index.php?route=courses/" . $courseId . "&status=created"]);
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => "Database Error: " . $e->getMessage()]);
        exit;
    }
}
?>

<div class="flex justify-center items-center min-h-screen py-10 bg-slate-50">
    <div class="w-full max-w-lg">
        
        <div id="error-box" class="hidden mb-4 p-4 bg-red-100 text-red-700 rounded-lg border border-red-200 text-sm"></div>

        <div class="bg-white shadow-sm border rounded-xl overflow-hidden">
            <div class="p-6 border-b bg-white">
                <h3 class="text-lg font-semibold text-slate-900">Create Project</h3>
            </div>

            <div class="p-6">
                <form id="project-form" onsubmit="submitProject(event)" class="grid gap-6" autocomplete="off">
                    
                    <div class="grid gap-3">
                        <label class="text-sm font-medium text-slate-700">Team Members</label>
                        <div class="flex gap-2">
                            <input type="text" id="fn_search" placeholder="Enter Faculty Number" class="flex-1 rounded-md border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                            <button type="button" onclick="validateAndAdd()" class="w-10 h-10 flex items-center justify-center rounded-md border bg-slate-900 text-white font-bold">+</button>
                        </div>
                        <p id="fn_status" class="text-xs"></p>
                        
                        <ul id="team_list" class="space-y-2 text-sm">
                            <li class="flex items-center justify-between p-2 bg-blue-50 text-blue-800 rounded border border-blue-100">
                                <span>1. <strong><?= htmlspecialchars($current_user_fn) ?></strong> (You)</span>
                                <input type="hidden" name="team_members[]" value="<?= htmlspecialchars($current_user_fn) ?>">
                            </li>
                        </ul>
                    </div>

                    <hr class="border-slate-100">

                    <div class="grid gap-4">
                        <div class="grid gap-2">
                            <label class="text-sm font-medium text-slate-700">Project Name</label>
                            <input type="text" name="project_name" class="flex h-10 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none" required>
                        </div>
                        <div class="grid gap-2">
                            <label class="text-sm font-medium text-slate-700">Topic</label>
                            <input type="text" name="topic" class="flex h-10 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none" required>
                        </div>
                        <div class="grid gap-2">
                            <label class="text-sm font-medium text-slate-700">Description</label>
                            <textarea name="description" rows="3" class="w-full rounded-md border border-slate-300 p-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-4">
                        <a href="index.php?route=courses/<?= htmlspecialchars($courseId) ?>" class="text-sm text-slate-500 hover:text-slate-800">Cancel</a>
                        <button type="submit" id="submit-btn" class="bg-blue-600 text-white px-6 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition-colors">
                            Create Project
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// ---------------------------------------------------------
// 4. JAVASCRIPT LOGIC
// ---------------------------------------------------------

async function submitProject(e) {
    e.preventDefault(); // Stop the browser from doing a POST reload
    
    const form = e.target;
    const btn = document.getElementById('submit-btn');
    const errorBox = document.getElementById('error-box');
    const currentRoute = "<?= isset($_GET['route']) ? $_GET['route'] : '' ?>"; 

    // Reset UI
    errorBox.classList.add('hidden');
    btn.disabled = true;
    btn.innerText = "Saving...";

    const formData = new FormData(form);

    try {
        // Send data to PHP via AJAX
        const response = await fetch(`index.php?route=${currentRoute}&action=save_project`, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();

        if (result.success) {
            // Success! Redirect the user.
            window.location.href = result.redirect;
        } else {
            // Error! Show message, keep form data as is.
            errorBox.textContent = result.message;
            errorBox.classList.remove('hidden');
            btn.disabled = false;
            btn.innerText = "Create Project";
        }
    } catch (err) {
        errorBox.textContent = "An unexpected error occurred. Please try again.";
        errorBox.classList.remove('hidden');
        btn.disabled = false;
        btn.innerText = "Create Project";
    }
}

async function validateAndAdd() {
    const input = document.getElementById('fn_search');
    const status = document.getElementById('fn_status');
    const list = document.getElementById('team_list');
    const fn = input.value.trim();
    const myFn = "<?= $current_user_fn ?>";

    if (!fn) return;
    if (fn === myFn) { status.innerText = "You are already in the list."; status.className = "text-xs text-orange-500"; return; }
    if (list.children.length >= 3) { status.innerText = "The team cannot exceed 3 members."; status.className = "text-xs text-red-600"; return; }
    if (document.querySelectorAll(`input[value="${fn}"]`).length > 0) { status.innerText = "Student already added."; status.className = "text-xs text-orange-500"; return; }

    try {
        const currentRoute = "<?= isset($_GET['route']) ? $_GET['route'] : '' ?>"; 
        const resp = await fetch(`index.php?route=${currentRoute}&action=check_fn&fn=${fn}`);
        const data = await resp.json();
        
        if (data.exists) {
            const index = list.children.length + 1;
            const li = document.createElement('li');
            li.className = "flex items-center justify-between p-2 bg-white rounded border";
            li.innerHTML = `<span>${index}. ${fn} (${data.name})</span><input type="hidden" name="team_members[]" value="${fn}"><button type="button" onclick="this.parentElement.remove()" class="text-xs text-red-500 hover:underline">remove</button>`;
            list.appendChild(li);
            input.value = ""; status.innerText = "Added!"; status.className = "text-xs text-green-600";
        } else {
            status.innerText = data.error === 'own_fn' ? "You are already in the list." : "Student not found.";
            status.className = "text-xs text-red-500";
        }
    } catch (e) { status.innerText = "Connection error."; }
}
</script>