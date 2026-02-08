<script src="assets/js/components/card.js"></script>
<script src="assets/js/components/form.js"></script>

<?php
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

    // Basic Validation
    if (empty($firstName) || empty($lastName) || empty($facultyNum) || empty($email) || empty($pass)) {
        $error = "All fields are required.";
    } elseif ($pass !== $passConfirm) {
        $error = "Passwords do not match.";
    } else {
        try {
            // Generate UUID (Version 4 Polyfill for PHP < 8)
            $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );

            // Hash Password
            $passwordHash = password_hash($pass, PASSWORD_DEFAULT);

            // Insert User
            // Note: 'user_role' defaults to 'student' in DB schema, so we skip it here
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
        } catch (PDOException $e) {
            // Check for duplicate entry (Error 23000)
            if ($e->getCode() == 23000) {
                $error = "User with this Email or Faculty Number already exists.";
            } else {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}
?>

<ui-card-content>
    <form method="POST" action="" class="grid gap-4">

        <div class="grid grid-cols-2 gap-4">
            <ui-form-item>
                <ui-label>First Name</ui-label>
                <ui-input 
                    name="first_name" 
                    placeholder="John" 
                    value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>"
                    required>
                </ui-input>
            </ui-form-item>

            <ui-form-item>
                <ui-label>Last Name</ui-label>
                <ui-input 
                    name="last_name" 
                    placeholder="Doe" 
                    value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>"
                    required>
                </ui-input>
            </ui-form-item>
        </div>

        <ui-form-item>
            <ui-label>Faculty Number</ui-label>
            <ui-input 
                name="faculty_number" 
                placeholder="12345678" 
                value="<?php echo htmlspecialchars($_POST['faculty_number'] ?? ''); ?>"
                required>
            </ui-input>
        </ui-form-item>

        <ui-form-item>
            <ui-label>University Email</ui-label>
            <ui-input 
                type="email" 
                name="university_email" 
                placeholder="m@example.com" 
                value="<?php echo htmlspecialchars($_POST['university_email'] ?? ''); ?>"
                required>
            </ui-input>
        </ui-form-item>

        <ui-form-item>
            <ui-label>Password</ui-label>
            <ui-input type="password" name="password" required></ui-input>
        </ui-form-item>

        <ui-form-item>
            <ui-label>Confirm Password</ui-label>
            <ui-input type="password" name="confirm_password" required></ui-input>
        </ui-form-item>

        <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-black text-white hover:bg-black/90 h-10 px-4 py-2 mt-4">
            Register
        </button>
    </form>
</ui-card-content>
