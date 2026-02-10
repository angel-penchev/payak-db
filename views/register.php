<?php
require_once 'config/db.php';

$error = '';
$success = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $facultyNum = trim($_POST['faculty_number'] ?? '');
    $email = trim($_POST['university_email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $passConfirm = $_POST['confirm_password'] ?? '';
    $avatarUrl = $_POST['avatar_url'] ?? ''; 

    if (empty($firstName) || empty($lastName) || empty($facultyNum) || empty($email) || empty($pass)) {
        $error = "All fields are required.";
    } elseif ($pass !== $passConfirm) {
        $error = "Passwords do not match.";
    } else {
        try {
            $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );

            $passwordHash = password_hash($pass, PASSWORD_DEFAULT);

            // Use the $avatarUrl captured from the hidden input
            $sql = "INSERT INTO users (id, first_name, last_name, faculty_number, university_email, password_hash, avatar_url) 
                    VALUES (:id, :fname, :lname, :fnum, :email, :phash, :avatar)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id' => $uuid,
                ':fname' => $firstName,
                ':lname' => $lastName,
                ':fnum' => $facultyNum,
                ':email' => $email,
                ':phash' => $passwordHash,
                ':avatar' => $avatarUrl
            ]);

            $success = "Registration successful! You can now login.";
            $_POST = [];
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "User with this Email or Faculty Number already exists.";
            } else {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}
?>

<script type="module" src="<?php echo BASE_URL; ?>/assets/js/components/avatar-generator.js"></script>

<div class="flex justify-center items-center min-h-[calc(100vh-200px)] py-10">
    <div class="w-full max-w-2xl"> 
        <?php if ($success) : ?>
            <div class="mb-4 p-4 bg-green-50 text-green-700 rounded-lg border border-green-200 text-sm">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if ($error) : ?>
            <div class="mb-4 p-4 bg-red-50 text-red-700 rounded-lg border border-red-200 text-sm">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <ui-card>
            <ui-card-header>
                <ui-card-title>Create an Account</ui-card-title>
                <ui-card-description>Enter your details below to create your student account.</ui-card-description>
            </ui-card-header>

            <ui-card-content>
                <form method="POST" action="" class="grid gap-6">
                    
                    <div class="flex flex-col items-center gap-4 py-4 border-b border-gray-100">
                        <label class="text-sm font-medium text-muted-foreground">Customize Your Avatar</label>

                        <avatar-generator id="avatarGen"></avatar-generator>

                        <input type="hidden" name="avatar_url" id="avatarInput">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <ui-label>First Name</ui-label>
                            <ui-input name="first_name" placeholder="Gosho" value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" required></ui-input>
                        </div>
                        <div class="grid gap-2">
                            <ui-label>Last Name</ui-label>
                            <ui-input name="last_name" placeholder="Losho" value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" required></ui-input>
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <ui-label>Faculty Number</ui-label>
                        <ui-input name="faculty_number" placeholder="0MI00000" value="<?php echo htmlspecialchars($_POST['faculty_number'] ?? ''); ?>" required></ui-input>
                    </div>

                    <div class="grid gap-2">
                        <ui-label>University Email</ui-label>
                        <ui-input type="email" name="university_email" placeholder="glosho1@uni-sofia.bg" value="<?php echo htmlspecialchars($_POST['university_email'] ?? ''); ?>" required></ui-input>
                    </div>

                    <div class="grid gap-2">
                        <ui-label>Password</ui-label>
                        <ui-input type="password" name="password" required></ui-input>
                    </div>

                    <div class="grid gap-2">
                        <ui-label>Confirm Password</ui-label>
                        <ui-input type="password" name="confirm_password" required></ui-input>
                    </div>

                    <ui-button type="submit" class="w-full mt-4">
                        Register
                    </ui-button>

                    <div class="mt-2 text-center text-sm">
                        Already registered? 
                        <a href="<?php echo BASE_URL; ?>/login" class="underline hover:text-gray-900 dark:hover:text-gray-100 transition-colors">
                            Login
                        </a>
                    </div>
                </form>
            </ui-card-content>
        </ui-card>
    </div>
</div>

<script>
    // Robust logic to capture avatar data
    const avatarGen = document.getElementById('avatarGen');
    const avatarInput = document.getElementById('avatarInput');

    if (avatarGen && avatarInput) {
        // 1. Listen for updates (when user clicks arrows)
        avatarGen.addEventListener('avatar-generated', (e) => {
            avatarInput.value = e.detail.dataUri;
        });

        // 2. Capture initial state
        // We poll briefly until the Web Component hydrates and sets its value
        const checkValue = setInterval(() => {
            if (avatarGen.getAttribute('value')) {
                avatarInput.value = avatarGen.getAttribute('value');
                clearInterval(checkValue);
            }
        }, 100);
        
        // Timeout to stop polling after 2 seconds
        setTimeout(() => clearInterval(checkValue), 2000);
    }
</script>
