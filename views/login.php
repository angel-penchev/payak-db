<?php
require_once 'config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['university_email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, first_name, last_name, password_hash, user_role, avatar_url FROM users WHERE university_email = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify Password
            if ($user && password_verify($password, $user['password_hash'])) {
                // Success! Start Session
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                // Store user data in session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['user_role'] = $user['user_role'];
                $_SESSION['user_avatar'] = $user['avatar_url'];

                // Redirect to Home
                header("Location: " . BASE_URL . "/");
                exit;
            } else {
                $error = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?><div class="flex justify-center items-center min-h-[calc(100vh-200px)] py-10">
    <div class="w-full max-w-md">

        <?php if ($error) : ?>
            <div class="mb-4 p-4 bg-red-50 text-red-700 rounded-lg border border-red-200 text-sm">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <ui-card>
            <ui-card-header>
                <ui-card-title>Welcome Back</ui-card-title>
                <ui-card-description>Enter your credentials to access your account.</ui-card-description>
            </ui-card-header>

            <ui-card-content>
                <form method="POST" action="" class="grid gap-4">
                    
                    <div class="grid gap-2">
                        <ui-label>University Email</ui-label>
                        <ui-input 
                            type="email" 
                            name="university_email" 
                            placeholder="glosho@uni-sofia.bg" 
                            value="<?php echo htmlspecialchars($_POST['university_email'] ?? ''); ?>"
                            required>
                        </ui-input>
                    </div>

                    <div class="grid gap-2">
                        <div class="flex items-center">
                            <ui-label>Password</ui-label>
                            </div>
                        <ui-input 
                            type="password" 
                            name="password" 
                            required>
                        </ui-input>
                    </div>

                   <ui-button type="submit" class="w-full mt-4">
                        Login
                    </ui-button>

                    <div class="mt-2 text-center text-sm">
                        Don't have an account?
                        <a href="<?php echo BASE_URL; ?>/register" class="underline hover:text-gray-900 dark:hover:text-gray-100 transition-colors">
                            Sign up
                        </a>
                    </div>
                </form>
            </ui-card-content>
        </ui-card>

    </div>
</div>

