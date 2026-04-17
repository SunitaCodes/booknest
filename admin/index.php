<?php
// Admin index page - redirect to dashboard
require_once '../includes/auth.php';
require_admin();

header('Location: dashboard.php');
exit();
?>
