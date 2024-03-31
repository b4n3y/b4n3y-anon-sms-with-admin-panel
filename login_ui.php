<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hyni</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f1f1f1;
        }
        .wrapper {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            touch-action: manipulation;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        header {
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }
        .field {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .input-area {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 0 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .icon {
            margin-right: 10px;
            color: #bfbfbf;
            font-size: 18px;
            padding: 10px;
        }
        .input-area input {
            flex: 1;
            height: 40px;
            border: none;
            outline: none;
        }
        .error-txt {
            color: red;
            font-size: 14px;
            margin-top: 5px;
            text-align: center;
        }
        .pass-txt a {
            font-size: 14px;
            color: #5372F0;
            text-decoration: none;
        }
        button[type="submit"] {
            width: 100%;
            height: 30px;
            background-color: #5372F0;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
        }
        button[type="submit"]:hover {
            background-color: #2c52ed;
        }
        .sign-txt {
            font-size: 14px;
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <header>Hyni</header>
        <form action="login_process.php" method="POST">
            <div class="field email">
                <div class="input-area">
                    <i class="icon fas fa-user"></i>
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="error-txt"></div>
            </div>
            <div class="field password">
                <div class="input-area">
                    <i class="icon fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="error-txt">
                    <?php
                    session_start();
                    if (isset($_SESSION['login_error'])) {
                        echo $_SESSION['login_error'];
                        unset($_SESSION['login_error']);
                    }
                    ?>
                </div>
            </div>
            <button type="submit" name="login">Hyni</button>
        </form>
        <div class="sign-txt">Krijuar nga <a href="https://www.instagram.com/shermadhi_16/">Albani</a></div>
    </div>
</body>
</html>
