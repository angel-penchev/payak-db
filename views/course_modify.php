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
$createdId = '';
$isEditMode = isset($courseId) && !empty($courseId);
$courseData = [];

if ($isEditMode) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$courseId]);
        $courseData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$courseData) {
            die("Course not found.");
        }
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['display_name'] ?? '');
    $moodle = trim($_POST['moodle_url'] ?? '');
    $open = $_POST['opens_at'] ?? '';
    $close = $_POST['closes_at'] ?? '';
    $targetId = $isEditMode ? $courseId : trim($_POST['id'] ?? '');

    if (empty($name) || empty($moodle) || empty($open) || empty($close) || empty($targetId)) {
        $error = "All fields are required.";
    } elseif ($close < $open) {
        $error = "Closing date cannot be earlier than opening date.";
    } else {
        try {
            if ($isEditMode) {
                $sql = "UPDATE courses SET 
                        display_name = :name, 
                        moodle_course_url = :moodle, 
                        opens_at_date = :open, 
                        closes_at_date = :close 
                        WHERE id = :cid";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':name'   => $name,
                    ':moodle' => $moodle,
                    ':open'   => $open,
                    ':close'  => $close,
                    ':cid'    => $targetId
                ]);
                
                $success = "Course updated successfully!";
                $createdId = $targetId; // For the link
                
                $courseData['display_name'] = $name;
                $courseData['moodle_course_url'] = $moodle;
                $courseData['opens_at_date'] = $open;
                $courseData['closes_at_date'] = $close;
            } else {
                $sql = "INSERT INTO courses (id, display_name, moodle_course_url, opens_at_date, closes_at_date, owner_id) 
                        VALUES (:cid, :name, :moodle, :open, :close, :owner)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':cid'    => $targetId,
                    ':name'   => $name,
                    ':moodle' => $moodle,
                    ':open'   => $open,
                    ':close'  => $close,
                    ':owner'  => $current_user_id
                ]);

                $success = "Course created successfully!";
                $createdId = $targetId; // For the link
                $_POST = [];
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "A course with this ID or Moodle URL already exists.";
            } else {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}

$valId     = $_POST['id'] ?? ($courseData['id'] ?? '');
$valName   = $_POST['display_name'] ?? ($courseData['display_name'] ?? '');
$valMoodle = $_POST['moodle_url'] ?? ($courseData['moodle_course_url'] ?? '');
$valOpen   = $_POST['opens_at'] ?? ($courseData['opens_at_date'] ?? '');
$valClose  = $_POST['closes_at'] ?? ($courseData['closes_at_date'] ?? '');

$pageTitle = $isEditMode ? 'Edit Course' : 'Create Course';
?>

<div class="flex justify-center items-center min-h-[calc(100vh-200px)] py-10">
    <div class="w-full max-w-lg px-4">

        <?php if ($success) : ?>
            <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-lg border border-green-200 text-sm font-medium flex items-center justify-between">
                <span>
                    <?php echo $success; ?> 
                    View it <a href="<?php echo BASE_URL; ?>/courses/<?php echo urlencode($createdId); ?>" class="underline font-bold hover:text-green-900 transition-colors">here</a>.
                </span>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-600"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </div>
        <?php endif; ?>

        <?php if ($error) : ?>
            <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-lg border border-red-200 text-sm font-medium">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <ui-card class="shadow-xl">
            <ui-card-header>
                <ui-card-title class="text-2xl font-black tracking-tight">
                    <?php echo $pageTitle; ?>
                </ui-card-title>
                <ui-card-description>
                    <?php echo $isEditMode ? "Update the course details below." : "Enter the details below to register a new course."; ?>
                </ui-card-description>
            </ui-card-header>

            <ui-card-content>
                <form method="POST" action="" class="grid gap-6">
                    
                    <div class="grid grid-cols-3 gap-4">
                        <div class="grid gap-2 col-span-2">
                            <ui-label>Course Name</ui-label>
                            <ui-input 
                                name="display_name"
                                placeholder="Advanced Web Development"
                                value="<?php echo htmlspecialchars($valName); ?>"
                                required>
                            </ui-input>
                        </div>
                        <div class="grid gap-2 col-span-1">
                            <ui-label>Course ID</ui-label>
                            <?php if ($isEditMode) : ?>
                                <ui-input 
                                    value="<?php echo htmlspecialchars($valId); ?>"
                                    disabled 
                                    class="opacity-50 cursor-not-allowed bg-gray-100 dark:bg-gray-800 pointer-events-none shadow-none">
                                </ui-input>
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($valId); ?>">
                            <?php else : ?>
                                <ui-input 
                                    name="id"
                                    placeholder="w25-2025"
                                    value="<?php echo htmlspecialchars($valId); ?>"
                                    required>
                                </ui-input>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <ui-label>Opening Date</ui-label>
                            <ui-input 
                                type="date"
                                name="opens_at"
                                value="<?php echo htmlspecialchars($valOpen); ?>"
                                required>
                            </ui-input>
                        </div>
                        <div class="grid gap-2">
                            <ui-label>Closing Date</ui-label>
                            <ui-input 
                                type="date"
                                name="closes_at"
                                value="<?php echo htmlspecialchars($valClose); ?>"
                                required>
                            </ui-input>
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <ui-label>Moodle Course URL</ui-label>
                        <ui-input
                            type="url"
                            name="moodle_url"
                            placeholder="https://learn.fmi.uni-sofia.bg/..."
                            value="<?php echo htmlspecialchars($valMoodle); ?>"
                            required>
                        </ui-input>
                    </div>

                    <div class="flex flex-row-reverse gap-4 pt-4">
                        <ui-button type="submit" class="flex-1">
                            <?php echo $isEditMode ? "Save Changes" : "Create Course"; ?>
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
