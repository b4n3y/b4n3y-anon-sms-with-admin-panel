<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login_ui.php');
    exit;
}
include('config.php');
function getQuoteCount($user_id, $conn) {
    $sql = "SELECT remaining_quota FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['remaining_quota'];
}
$users = [];
$sql = "SELECT id, username FROM users";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $user_id = $row['id'];
        $username = $row['username'];
        $quoteCount = getQuoteCount($user_id, $conn);
        $users[$user_id] = ['username' => $username, 'quoteCount' => $quoteCount];
    }
}
$quotaAdded = false;
$quotaAddedMessage = '';
if (isset($_POST['add_quota'])) {
    $user_id = $_POST['user_id'];
    $quota = $_POST['quota'];
    $quota = min($quota, 225);
    $sql = "UPDATE users SET remaining_quota = LEAST(remaining_quota + ?, 225) WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $quota, $user_id);
    if ($stmt->execute()) {
        $quotaAdded = true;
        $quotaAddedMessage = $quota . ' messages added successfully to ' . $users[$user_id]['username'] . '!';
        $users[$user_id]['quoteCount'] = min($users[$user_id]['quoteCount'] + $quota, 225);
    }
}
$quotaRemoved = false;
$quotaRemovedMessage = '';
if (isset($_POST['remove_quota_submit'])) {
    $remove_user_id = $_POST['remove_user_id'];
    $remove_quota = $_POST['remove_quota'];
    $sql = "UPDATE users SET remaining_quota = GREATEST(0, remaining_quota - ?) WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $remove_quota, $remove_user_id);
    if ($stmt->execute()) {
        $quotaRemoved = true;
        $quotaRemovedMessage = $remove_quota . ' messages removed successfully from ' . $users[$remove_user_id]['username'] . '!';
        $users[$remove_user_id]['quoteCount'] = max($users[$remove_user_id]['quoteCount'] - $remove_quota, 0);
    }
}
$userRegistered = false;
$userRegisteredMessage = '';
if (isset($_POST['register_user'])) {
    $new_username = $_POST['new_username'];
    $new_password = $_POST['new_password'];
    $sql = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $new_username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, 'user')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $new_username, $new_password);
        if ($stmt->execute()) {
            $userRegistered = true;
            $userRegisteredMessage = $new_username . ' was successfully registered!';
            $new_user_id = $stmt->insert_id;
            $users[$new_user_id] = ['username' => $new_username, 'quoteCount' => 0];
        }
    }
}
$userDeleted = false;
$userDeletedMessage = '';
if (isset($_POST['delete_user'])) {
    $delete_user_id = $_POST['delete_user_id'];
    $delete_username = $users[$delete_user_id]['username'];
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_user_id);
    if ($stmt->execute()) {
        $userDeleted = true;
        $userDeletedMessage = $delete_username . ' was successfully deleted!';
        unset($users[$delete_user_id]);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 100%;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        select, input[type="number"], input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }
        button[type="submit"] {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button[type="submit"]:hover {
            background-color: #45a049;
        }
        .logout-btn {
            display: block;
            width: 50%;
            padding: 10px;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
            text-align: center;
            text-decoration: none;
            margin-left: auto;
            margin-right: auto;
        }
        .logout-btn:hover {
            background-color: #d32f2f;
        }
        .success-message {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }
        .user-list {
            list-style: none;
            padding: 0;
        }
        .user-item {
            margin-bottom: 5px;
        }
        .divider {
            border-top: 2px solid #ccc;
            margin: 30px 0;
            position: relative;
        }
        .divider::before {
            content: "•••";
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            background-color: white;
            padding: 0 10px;
            color: #ccc;
            font-size: 20px;
        }
        .quota-text {
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin Panel</h2>
        <?php if ($quotaAdded): ?>
            <div class="success-message">
                <?php echo $quotaAddedMessage; ?>
            </div>
        <?php endif; ?>
        <?php if ($quotaRemoved): ?>
            <div class="success-message">
                <?php echo $quotaRemovedMessage; ?>
            </div>
        <?php endif; ?>
        <?php if ($userRegistered): ?>
            <div class="success-message">
                <?php echo $userRegisteredMessage; ?>
            </div>
        <?php endif; ?>
        <?php if ($userDeleted): ?>
            <div class="success-message">
                <?php echo $userDeletedMessage; ?>
            </div>
        <?php endif; ?>
        <h3>Add messages to User</h3>
        <form action="admin_panel.php" method="POST">
            <div class="form-group">
                <label for="user_id">Select User:</label>
                <select name="user_id" id="user_id" required>
                    <?php foreach ($users as $user_id => $user): ?>
                        <option value="<?php echo $user_id; ?>"><?php echo $user['username'] . " (" . $user['quoteCount'] . ")"; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="quota">Enter messages number (Max: 225):</label>
                <input type="number" name="quota" id="quota" min="1" max="225" required>
            </div>
            <button type="submit" name="add_quota">Add messages</button>
        </form>
        <div class="divider"></div>
        <h3>Remove messages from User</h3>
        <form action="admin_panel.php" method="POST">
            <div class="form-group">
                <label for="remove_user_id">Select User:</label>
                <select name="remove_user_id" id="remove_user_id" required>
                    <?php foreach ($users as $user_id => $user): ?>
                        <option value="<?php echo $user_id; ?>"><?php echo $user['username'] . " (" . $user['quoteCount'] . ")"; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="remove_quota">Enter messages to Remove:</label>
                <input type="number" name="remove_quota" id="remove_quota" required>
            </div>
            <button type="submit" name="remove_quota_submit">Remove messages</button>
        </form>
        <div class="divider"></div>
        <h3>Register New User</h3>
        <form action="admin_panel.php" method="POST">
            <div class="form-group">
                <label for="new_username">Username:</label>
                <input type="text" name="new_username" id="new_username" required>
            </div>
            <div class="form-group">
                <label for="new_password">Password:</label>
                <input type="password" name="new_password" id="new_password" required>
            </div>
            <button type="submit" name="register_user">Register User</button>
        </form>
        <div class="divider"></div>
        <h3>Delete User</h3>
        <form action="admin_panel.php" method="POST">
            <div class="form-group">
                <label for="delete_user_id">Select User:</label>
                <select name="delete_user_id" id="delete_user_id" required>
                    <?php foreach ($users as $user_id => $user): ?>
                        <option value="<?php echo $user_id; ?>"><?php echo $user['username'] . " (" . $user['quoteCount'] . ")"; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="delete_user">Delete User</button>
        </form>
        <div class="quota-text">
        </div>
                <a href="logout.php" class="logout-btn">Logout</a>
        <div class="quota-text">
            <?php
            $quotaUrl = 'https://textbelt.com/quota/5e561d20c00aead6aaa7755f1877bda3d203634a3PpjXJmIoa5tWReCdZYQNuPi8';
            $quotaData = json_decode(file_get_contents($quotaUrl), true);
            if ($quotaData && isset($quotaData['success']) && $quotaData['success']) {
                echo 'Remaining messages in the API: ' . $quotaData['quotaRemaining'];
            } else {
                echo 'Failed to fetch quota data.';
            }
            ?>
        </div>
    </div>
</body>
</html>
