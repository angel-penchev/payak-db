<?php
require_once 'config/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    // Redirect non-admins back to home
    header("Location: " . BASE_URL . "/");
    exit;
}

$current_user_id = $_SESSION['user_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_role') {
    $targetUserId = $_POST['user_id'] ?? '';
    $newRole = $_POST['role'] ?? '';

    // Basic validation
    $validRoles = ['student', 'assistant', 'admin'];

    if (empty($targetUserId) || !in_array($newRole, $validRoles)) {
        $error = "Invalid data provided.";
    } elseif ($targetUserId === $current_user_id) {
        $error = "You cannot change your own role here.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE users SET user_role = ? WHERE id = ?");
            $stmt->execute([$newRole, $targetUserId]);
            $success = "User role updated successfully.";
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

try {
    $stmt = $pdo->query("
        SELECT id, first_name, last_name, faculty_number, university_email, user_role, avatar_url 
        FROM users 
        ORDER BY first_name ASC, last_name ASC
    ");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching users: " . $e->getMessage());
}
?>

<main class="px-4 mx-auto w-full max-w-6xl py-8">

    <div class="flex flex-col md:flex-row justify-between items-end md:items-center mb-8 gap-4 border-b border-gray-200 dark:border-gray-800 pb-6">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight">User Management</h1>
            <p class="text-muted-foreground mt-1">Manage registered users and permissions.</p>
        </div>

        <div class="relative w-full md:w-72">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-2.5 top-2.5 text-muted-foreground"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <input 
                type="text" 
                id="userSearch" 
                placeholder="Search users..." 
                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring pl-9"
            >
        </div>
    </div>

    <?php if ($success) : ?>
        <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-lg border border-green-200 text-sm font-medium flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            <?php echo $success; ?>
        </div>
    <?php endif; ?>
    <?php if ($error) : ?>
        <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-lg border border-red-200 text-sm font-medium flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <div class="grid gap-4" id="usersGrid">
        <?php foreach ($users as $user) : ?>
            <ui-card class="user-card-item flex flex-col md:flex-row items-center gap-4 p-4 transition-all hover:border-gray-300 dark:hover:border-gray-700" 
                     data-name="<?php echo htmlspecialchars(strtolower($user['first_name'] . ' ' . $user['last_name'])); ?>">
                <div class="h-12 w-12 rounded-full overflow-hidden border border-gray-200 bg-gray-100 shrink-0">
                    <?php if (!empty($user['avatar_url'])) : ?>
                        <img src="<?php echo htmlspecialchars($user['avatar_url']); ?>" class="h-full w-full object-cover">
                    <?php else : ?>
                        <img src="https://api.dicebear.com/9.x/avataaars/svg?seed=<?php echo $user['id']; ?>&backgroundColor=b6e3f4" class="h-full w-full object-cover">
                    <?php endif; ?>
                </div>

                <div class="flex-grow text-center md:text-left">
                    <h3 class="font-bold text-lg leading-none mb-1">
                        <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                        <?php if ($user['id'] === $current_user_id) : ?>
                            <span class="ml-2 text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">You</span>
                        <?php endif; ?>
                    </h3>
                    <div class="text-sm text-muted-foreground flex flex-col md:flex-row gap-1 md:gap-4">
                        <span class="flex items-center gap-1 justify-center md:justify-start">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <?php echo htmlspecialchars($user['faculty_number']); ?>
                        </span>
                        <span class="hidden md:inline text-gray-300">|</span>
                        <span class="flex items-center gap-1 justify-center md:justify-start">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            <?php echo htmlspecialchars($user['university_email']); ?>
                        </span>
                    </div>
                </div>

                <div class="w-full md:w-auto shrink-0">
                    <form method="POST" action="" class="flex items-center gap-2">
                        <input type="hidden" name="action" value="update_role">
                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">

                        <div class="relative">
                            <select name="role" 
                                    class="h-9 w-full md:w-32 rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring cursor-pointer"
                                    <?php echo ($user['id'] === $current_user_id) ? 'disabled' : ''; ?>
                            >
                                <option value="student" <?php echo $user['user_role'] === 'student' ? 'selected' : ''; ?>>Student</option>
                                <option value="assistant" <?php echo $user['user_role'] === 'assistant' ? 'selected' : ''; ?>>Assistant</option>
                                <option value="admin" <?php echo $user['user_role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            </select>
                        </div>

                        <?php if ($user['id'] !== $current_user_id): ?>
                            <button type="submit" class="h-9 px-3 bg-primary text-primary-foreground hover:bg-primary/90 inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring shadow-sm">
                                Save
                            </button>
                        <?php endif; ?>
                    </form>
                </div>

            </ui-card>
        <?php endforeach; ?>

        <div id="noResults" class="hidden text-center py-12 text-muted-foreground">
            No users found matching your search.
        </div>
    </div>

</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('userSearch');
    const userCards = document.querySelectorAll('.user-card-item');
    const noResults = document.getElementById('noResults');
    let timeout = null;

    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            clearTimeout(timeout);
            const term = e.target.value.toLowerCase().trim();

            timeout = setTimeout(() => {
                let visibleCount = 0;
                
                userCards.forEach(card => {
                    const name = card.getAttribute('data-name');
                    if (name.includes(term)) {
                        card.style.display = 'flex';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                if (noResults) {
                    noResults.style.display = visibleCount === 0 ? 'block' : 'none';
                }
            }, 250);
        });
    }
});
</script>
