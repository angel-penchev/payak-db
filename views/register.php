<?php
require_once 'config/db.php';

$error = '';
$success = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and retrieve inputs
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $facultyNum = trim($_POST['faculty_number'] ?? '');
    $email = trim($_POST['university_email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $passConfirm = $_POST['confirm_password'] ?? '';

    // Basic Validation
    if (empty($firstName) || empty($lastName) || empty($facultyNum) || empty($email) || empty($pass)) {
        $error = "All fields are required.";
    } elseif ($pass !== $passConfirm) {
        $error = "Passwords do not match.";
    } else {
        try {
            // Generate UUID
            $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff)
            );

            // Hash Password
            $passwordHash = password_hash($pass, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (id, first_name, last_name, faculty_number, university_email, password_hash) 
                    VALUES (:id, :fname, :lname, :fnum, :email, :phash)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id' => $uuid,
                ':fname' => $firstName,
                ':lname' => $lastName,
                ':fnum' => $facultyNum,
                ':email' => $email,
                ':phash' => $passwordHash
            ]);

            $success = "Registration successful! You can now login.";

            // Clear POST data so form resets
            $_POST = [];
        } catch (PDOException $e) {
            // Check for duplicate entry (Error 23000 is the SQL standard code for constraint violation)
            if ($e->getCode() == 23000) {
                $error = "User with this Email or Faculty Number already exists.";
            } else {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}
?>

<div class="flex justify-center items-center min-h-[calc(100vh-200px)] py-10">
    <div class="w-full max-w-lg">

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
                <form method="POST" action="" class="grid gap-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <ui-label>First Name</ui-label>
                            <ui-input 
                                name="first_name"
                                placeholder="Gosho"
                                value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>"
                                required>
                            </ui-input>
                        </div>
                        <div class="grid gap-2">
                            <ui-label>Last Name</ui-label>
                            <ui-input 
                                name="last_name"
                                placeholder="Losho"
                                value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>"
                                required>
                            </ui-input>
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <ui-label>Faculty Number</ui-label>
                        <ui-input 
                            name="faculty_number" 
                            placeholder="0MI00000" 
                            value="<?php echo htmlspecialchars($_POST['faculty_number'] ?? ''); ?>"
                            required>
                        </ui-input>
                    </div>

                    <div class="grid gap-2">
                        <ui-label>University Email</ui-label>
                        <ui-input
                            type="email"
                            name="university_email"
                            placeholder="glosho1@uni-sofia.bg"
                            value="<?php echo htmlspecialchars($_POST['university_email'] ?? ''); ?>"
                            required>
                        </ui-input>
                    </div>

                    <div class="grid gap-2">
                        <ui-label>Password</ui-label>
                        <ui-input
                            type="password"
                            name="password"
                            required>
                        </ui-input>
                    </div>

                    <div class="grid gap-2">
                        <ui-label>Confirm Password</ui-label>
                        <ui-input 
                            type="password" 
                            name="confirm_password" 
                            required>
                        </ui-input>
                    </div>

                   <button 
                      type="submit" 
                      class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 h-10 px-4 py-2 mt-4 
                      bg-black text-white hover:bg-black/90 
                      dark:bg-white dark:text-black dark:hover:bg-white/90
                      ring-offset-white dark:ring-offset-gray-950 focus-visible:ring-gray-950 dark:focus-visible:ring-gray-300">
                        Register
                    </button>
                </form>
            </ui-card-content>
        </ui-card>
    </div>
</div>
