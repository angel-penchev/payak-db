<?php
require_once 'config/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// Redirect to Home if user is not logged in or not an Admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: " . BASE_URL . "/");
    exit;
}

$current_user_id = $_SESSION['user_id'] ?? null;
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data
    $courseId = trim($_POST['id'] ?? '');
    $name = trim($_POST['display_name'] ?? '');
    $moodle = trim($_POST['moodle_url'] ?? '');
    $open = $_POST['opens_at'] ?? '';
    $close = $_POST['closes_at'] ?? '';

    // Data Validation
    if (!$current_user_id) {
        $error = "You must be logged in to create a course.";
    } elseif (empty($courseId) || empty($name) || empty($moodle) || empty($open) || empty($close)) {
        $error = "All fields are required.";
    } elseif ($close < $open) {
        $error = "Closing date cannot be earlier than opening date.";
    } else {
        try {
            // Insert into database
            $sql = "INSERT INTO courses (id, display_name, moodle_course_url, opens_at_date, closes_at_date, owner_id) 
                    VALUES (:cid, :name, :moodle, :open, :close, :owner)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':cid'    => $courseId,
                ':name'   => $name,
                ':moodle' => $moodle,
                ':open'   => $open,
                ':close'  => $close,
                ':owner'  => $current_user_id
            ]);

            $success = "Course created successfully!";
            $_POST = []; // Clear form on success
        } catch (PDOException $e) {
            // Check for duplicate ID (Error 23000)
            if ($e->getCode() == 23000) {
                $error = "A course with this ID or Moodle URL already exists.";
            } else {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}
?>

<div class="flex justify-center items-center min-h-[calc(100vh-200px)] py-10">
    <div class="w-full max-w-lg px-4">

        <?php if ($success) : ?>
            <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-lg border border-green-200 text-sm font-medium">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if ($error) : ?>
            <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-lg border border-red-200 text-sm font-medium">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <ui-card class="shadow-xl">
            <ui-card-header> <ui-card-title class="text-2xl font-black tracking-tight">Create Course</ui-card-title>
                <ui-card-description>Enter the details below to register a new course.</ui-card-description>
            </ui-card-header>

            <ui-card-content>
                <form method="POST" action="" class="grid gap-6">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="grid gap-2 col-span-2">
                            <ui-label>Course Name</ui-label>
                            <ui-input 
                                name="display_name"
                                placeholder="Advanced Web Applications"
                                value="<?php echo htmlspecialchars($_POST['display_name'] ?? ''); ?>"
                                required>
                            </ui-input>
                        </div>
                        <div class="grid gap-2 col-span-1">
                            <ui-label>Course ID</ui-label>
                            <ui-input 
                                name="id"
                                placeholder="CS101"
                                value="<?php echo htmlspecialchars($_POST['id'] ?? ''); ?>"
                                required>
                            </ui-input>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <ui-label>Opening Date</ui-label>
                            <ui-input 
                                type="date"
                                name="opens_at"
                                value="<?php echo htmlspecialchars($_POST['opens_at'] ?? ''); ?>"
                                required>
                            </ui-input>
                        </div>
                        <div class="grid gap-2">
                            <ui-label>Closing Date</ui-label>
                            <ui-input 
                                type="date"
                                name="closes_at"
                                value="<?php echo htmlspecialchars($_POST['closes_at'] ?? ''); ?>"
                                required>
                            </ui-input>
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <ui-label>Moodle Course URL</ui-label>
                        <ui-input
                            type="url"
                            name="moodle_url"
                            placeholder="https://moodle.uni-sofia.bg/course/view.php?id=..."
                            value="<?php echo htmlspecialchars($_POST['moodle_url'] ?? ''); ?>"
                            required>
                        </ui-input>
                    </div>

                    <div class="flex flex-row-reverse gap-4 pt-4">
                        <ui-button type="submit" class="flex-1">
                            Create Course
                        </ui-button>
 
                        <ui-button href="<?php echo BASE_URL; ?>/courses" variant="ghost" class="flex-1">
                            Cancel
                        </ui-button>
                    </div>
                </form>
            </ui-card-content>
        </ui-card>
    </div>
</div>
