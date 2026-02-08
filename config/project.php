<?php
$folderPath = dirname($_SERVER['SCRIPT_NAME']);
$baseUrl = "http://" . $_SERVER['HTTP_HOST'] . $folderPath;
define('BASE_URL', $baseUrl);
?>
