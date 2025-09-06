<?php
session_start();
require '../backend/config.php';
require '../backend/routeros_api.class.php';

function add_user_to_mikrotik($username, $password) {
    $router_ip = '172.41.41.1';
    $router_user = 'api';
    $router_pass = 'api';
    
    $api = new RouterosAPI();
    
    if ($api->connect($router_ip, $router_user, $router_pass)) {
        $api->write('/ip/hotspot/user/add', false);
        $api->write('=name=' . $username, false);
        $api->write('=password=' . $password);
        $response = $api->read();
        
        $api->disconnect();
        
        if (isset($response[0]['!trap'])) {
            return 'Failed to add user to MikroTik: ' . $response[0]['!trap'][0]['message'];
        } else {
            return 'User added to MikroTik successfully!';
        }
    } else {
        return 'Unable to connect to MikroTik router.';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signup'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');

    if (!$stmt) {
        $signup_message = 'Failed to prepare statement: ' . implode(":", $pdo->errorInfo());
    } else {
        if ($stmt->execute([$username, $hashed_password])) {
            $signup_message = 'Your account has been created!';
            $mikrotik_message = add_user_to_mikrotik($username, $password);
        } else {
            $signup_message = 'Failed to add user: ' . implode(":", $stmt->errorInfo());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="src/css/register.css">
    <link rel="shortcut icon" href="src/img/favicon.png" />
    <title>SSO PAYA-NET</title>
</head>
<body>
    <div class="container">
        <div class="left-side" style="background-image: url('src/img/dashboard2.jpg');">
            <div class="overlay">
                <div class="text-center">
                    <h1>Turn Your Ideas into Reality</h1>
                    <p>Start for free and get attractive offers from the community</p>
                </div>
            </div>
        </div>
        <div class="right-side">
            <div class="login-form">
                <h2 class="title">PAYANET SSO</h2>
                <h3 class="subtitle">Register</h3>
                <p class="welcome-text">Welcome Back! Please enter your details.</p>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username" class="sr-only"></label>
                        <input id="username" name="username" type="text" required placeholder="Username">
                    </div>
                    <div class="form-group">
                        <label for="password" class="sr-only"></label>
                        <input id="password" name="password" type="password" required placeholder="Password">
                    </div>
                    <button type="button" class="btn-login" onclick="window.location.href='index.php'">Login</button>
                    <button type="submit" class="btn-register" name="signup">Register</button>
                    <?php if (isset($signup_message)) echo '<p class="message">' . $signup_message . '</p>'; ?>
                    <?php if (isset($mikrotik_message)) echo '<p class="message">' . $mikrotik_message . '</p>'; ?>
                </form>
                <p class="signup-text">Have account? <a href="index.php">Login here</a></p>
            </div>
        </div>
    </div>
</body>
</html>
