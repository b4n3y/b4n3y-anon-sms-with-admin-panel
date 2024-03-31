<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login_ui.php');
    exit;
}
include('config.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user_id']) && isset($_POST['quota'])) {
        $user_id = $_POST['user_id'];
        $quota = $_POST['quota'];
        $sql = "UPDATE users SET remaining_quota = remaining_quota + ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $quota, $user_id);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $_SESSION['success'] = "User's quota updated successfully.";
            header('Location: admin_panel.php');
            exit;
        } else {
            $_SESSION['error'] = "Failed to update user's quota.";
            header('Location: admin_panel.php');
            exit;
        }
    } else {
        $_SESSION['error'] = "Invalid form data.";
        header('Location: admin_panel.php');
        exit;
    }
} else {
    header('Location: admin_panel.php');
    exit;
}
?>
