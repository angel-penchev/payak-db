<?php
require_once 'config/db.php';
include 'includes/header.php';
?>

    <h2>Welcome</h2>
    <p>Database connection status: 
        <?php echo isset($pdo) ? "<strong style='color:green'>Connected!</strong>" : "Failed"; ?>
    </p>
    <p>This structure is ready for Windows XAMPP deployment.</p>

<?php
include 'includes/footer.php';
?>
