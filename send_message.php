<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'config.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        echo 'Error: You are not logged in.';
        exit;
    }
    $username = $_SESSION['username'];
    $stmt = $conn->prepare("SELECT remaining_quota FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row['remaining_quota'] <= 0) {
        echo 'Error: Ske Me Mesazhe Te Mbetura.';
        exit;
    }
    $phone = $_POST['phone'];
    $message = $_POST['message'];
    // Validate phone number
    if (!preg_match('/^\+?\d{11,15}$/', $phone)) {
        if (strlen($phone) < 11) {
            echo 'Error: Numri i telefonit duhet të përfshijë kodin e vendit dhe të ketë të paktën 11 shifra.';
        } elseif (strlen($phone) > 15) {
            echo 'Error: Numri i telefonit duhet të jetë midis 11 dhe 15 shifra.';
        } else {
            echo 'Error: Numri i telefonit është i pavlefshëm.';
        }
        exit;
    }
    // Check if the message contains a link
    if (preg_match('/(?:https?:\/\/|www\.)/', $message)) {
        echo 'Error: Nuk lejohet te dergosh link. Na kontakto ne te japim akses.';
        exit;
    }
    $ch = curl_init('https://textbelt.com/text');
    $data = array(
        'phone' => $phone,
        'message' => $message,
        'key' => '5e561d20c00aead6aaa7755f1877bda3d203634a3PpjXJmIoa5tWReCdZYQNuPi8',
    );
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $responseData = json_decode($response, true);
    if ($responseData['success']) {
        $new_quota = $row['remaining_quota'] - 1;
        $stmt = $conn->prepare("UPDATE users SET remaining_quota = ? WHERE username = ?");
        $stmt->bind_param("is", $new_quota, $username);
        $stmt->execute();
        echo 'success:' . $new_quota; // Send 'success' and remaining quota as the response
    } else {
        echo 'Error: Dergesa E Mesazhit Deshtoi. ' . $responseData['error'];
    }
}
?>