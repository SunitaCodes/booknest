<?php
require_once '../config/db.php';

$_SESSION['message'] = "You have been logged out successfully.";
$_SESSION['message_type'] = "success";

session_destroy();

header('Location: ../index.php');
exit();
?>
