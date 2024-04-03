<?php
session_start();
include('config.php');
if (!isset($_SESSION['user_id'])) {
    header('Location: login_ui.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$remaining_quota = $_SESSION['remaining_quota'];
$username = $_SESSION['username'];
$admin_username = $_SESSION['admin_username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Derguesi I Mesazheve Anonim</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 100%;
            min-height: 100vh;
            margin: auto;
            padding: 60px 20px 40px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            color: #fff;
            position: relative;
            overflow: hidden;
        }
        .container h2 {
            margin-bottom: 20px;
            text-align: center;
            font-size: 36px;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 5px;
        }
        .form-group input[type="tel"],
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            box-sizing: border-box;
            background-color: rgba(255, 255, 255, 0.9);
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
            color: #333;
        }
        .form-group textarea {
            height: 120px;
        }
        .form-group button {
            width: 100%;
            padding: 12px;
            background-color: #fff;
            color: #764ba2;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            font-size: 18px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }
        .form-group button:hover {
            background-color: #f1f1f1;
        }
        .logout-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: rgba(255, 255, 255, 0.2);
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .logout-button:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }
        .error-msg {
            color: #ff4d4d;
            font-size: 14px;
            margin-top: 10px;
            text-align: center;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        .user-greet {
            margin-top: 10px;
            text-align: center;
            font-size: 20px;
            font-weight: 500;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        .quota-info {
            margin-top: 10px;
            text-align: center;
            font-size: 16px;
            font-weight: 400;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        .warning-msg {
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
            font-weight: 300;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        .form-container {
            margin-top: 40px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease-in-out;
        }
        .admin-info {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 16px;
            font-weight: 400;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        .buy-credits {
            margin-top: 30px;
            text-align: center;
        }
        .buy-credits button {
            background-color: #fff;
            color: #764ba2;
            border: none;
            border-radius: 5px;
            padding: 12px 24px;
            font-size: 18px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .buy-credits button:hover {
            background-color: #f1f1f1;
        }
        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
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
                        console.log('AJAX Response: ' + response); // Debug statement
                        if (response.startsWith('Error:')) {
                            $('.error-msg').html(response);
                        } else if (response.startsWith('success')) {
                            var remainingQuota = response.split(':')[1];
                            $('.error-msg').html('');
                            $('.quota-info strong').text(remainingQuota); // Update remaining quota value
                            alert('Mesazhi U Dergua Me Sukses! Mesazhe Te Mbetura: ' + remainingQuota);
                            $('#contact-form')[0].reset(); // Reset the form
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('AJAX Error: ' + error); // Debug statement
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
            <div class="admin-info">Registered By: <strong><?php echo $admin_username; ?></strong></div>
<div class="user-greet">Mirësevini: <strong><?php echo ucfirst($username); ?></strong></div>
            <div class="quota-info">Mesazhe Të Mbetura: <strong><?php echo $remaining_quota; ?></strong></div>
            <h2>Mesazh Anonim</h2>
            <p class="warning-msg">Shënim: Ky mjet mund të dërgojë mesazhe në çdo numër në çdo shtet dhe mesazhet e dërguara janë 100% anonime dhe të pagjurmueshme. Përdorim të këndshëm :)</p>
            <div class="error-msg"></div>
        </div>
        <div class="form-container">
            <form id="contact-form" method="post">
                <div class="form-group">
                    <label for="phone">Numri i Telefonit:</label>
                    <input type="tel" id="phone" name="phone" placeholder="Shkruani Numrin e Telefonit" required>
                </div>
                <div class="form-group">
                    <label for="message">Mesazhi:</label>
                    <textarea id="message" name="message" placeholder="Shkruani Mesazhin" required></textarea>
                </div>
                <div class="form-group">
                    <button type="submit">Dërgo</button>
                </div>
            </form>
        </div>
        <div class="buy-credits">
            <button>Bli Më Shumë Kredite</button>
        </div>
    </div>
</body>
</html>
    </div>
</body>
</html>
