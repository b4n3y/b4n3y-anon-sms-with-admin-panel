<?php
session_start();
include('config.php');

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $sql = "SELECT id, remaining_quota, role FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['loggedin'] = true;
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $username;
        $_SESSION['remaining_quota'] = $row['remaining_quota'];
        $_SESSION['role'] = $row['role'];

        if ($_SESSION['role'] == 'admin') {
            header('Location: admin_panel.php');
        } else {
            header('Location: index.php');
        }
        exit;
    } else {
        $_SESSION['login_error'] = "Error: Username ose passwordi gabuar";
        header('Location: login_ui.php');
        exit;
    }
} else {
    $_SESSION['login_error'] = "Please provide both username and password";
    header('Location: login_ui.php');
    exit;
}

$stmt->close();
$conn->close();
?>
