<?php
session_start();
require_once "config.php";

// Allow only leaders to delete
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'leader') {
    header("Location: index.php?error=access_denied");
    exit();
}
// Check if ID is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: leader_dashboard.php?error=user_id_missing");
    exit();
}

$userId = intval($_GET['id']);

// Delete user from the database
$query = "DELETE FROM users WHERE id = $userId";
if ($conn->query($query)) {
    // Redirect to the dashboard with success message
    header("Location: admin_page.php?success=user_deleted");
    exit();
} else {
    // In case of error, show an error message
    header("Location: admin_page.php?error=delete_failed");
    exit();
}
?>
