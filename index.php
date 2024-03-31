<?php
session_start();
include('config.php');
if (!isset($_SESSION['user_id'])) {
    header('Location: login_ui.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$query = "SELECT remaining_quota FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $remaining_quota = $row['remaining_quota'];
} else {
    echo "Error fetching quota: " . $conn->error; 
    exit;
}
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'John'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Derguesi I Mesazheve Anonim</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 100%;
            height: 100vh;
            margin: auto;
            padding: 60px 20px 20px;
            background-color: #fff;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
        }
        .container h2 {
            margin-bottom: 20px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
        }
        .form-group input[type="tel"],
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .form-group textarea {
            height: 100px;
        }
        .form-group button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #0056b3;
        }
        .logout-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 16px;
            font-size: 16px;
            cursor: pointer;
        }
        .logout-button:hover {
            background-color: #c82333;
        }
        .error-msg {
            color: #ff0000;
            font-size: 14px;
            margin-top: 10px;
            text-align: center;
        }
        .user-greet {
            margin-top: 10px;
            text-align: center;
            font-size: 16px;
        }
        .quota-info {
            margin-top: 10px;
            text-align: center;
            font-size: 14px;
        }
        .warning-msg {
            margin-bottom: 20px;
        }
        .form-container {
            margin-top: 40px;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            $('#contact-form').submit(function(e){
                e.preventDefault(); // Prevent form submission
                var formData = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: 'send_message.php',
                    data: formData,
                    success: function(response){
                        if (response.startsWith('Error:')) {
                            $('.error-msg').html(response);
                        } else if (response.startsWith('success')) {
                            var remainingQuota = response.split(':')[1];
                            $('.error-msg').html('');
                            alert('Mesazhi U Dergua Me Sukses! Mesazhe Te Mbetura: ' + remainingQuota);
                            location.reload(); // Reload the page
                        }
                    }
                });
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <div>
            <form action="logout.php" method="post">
                <button type="submit" class="logout-button">Dil</button>
            </form>
            <div class="user-greet">Mireseerdhe: <strong><?php echo $username; ?></strong></div>
            <div class="quota-info">Mesazhe Te Mbetura: <strong><?php echo $remaining_quota; ?></strong></div>
            <h2>Mesazh Anonim</h2>
            <p class="warning-msg">Shenim : Ky tool mund te dergoj mesazhe ne cdo numer ne cdo shtet dhe mesazhet e derguara jane 100% anonim dhe te pagjurmueshme. Perdorim te kendshem :)</p>
            <div class="error-msg"></div>
        </div>
        <div class="form-container">
            <form id="contact-form" method="post">
                <div class="form-group">
                    <label for="phone">Numri Telefonit:</label>
                    <input type="tel" id="phone" name="phone" placeholder="Sheno Numrin E Telefonit" required>
                </div>
                <div class="form-group">
                    <label for="message">Mesazhi:</label>
                    <textarea id="message" name="message" placeholder="Sheno Mesazhin" required></textarea>
                </div>
                <div class="form-group">
                    <button type="submit">Dergo</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
