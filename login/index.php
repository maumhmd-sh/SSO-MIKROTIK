<?php
session_start();
require '../backend/config.php';
require '../backend/routeros_api.class.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signin'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;

        $router_ip = '172.41.41.1';
        $router_user = 'api';
        $router_pass = 'api';

        $api = new RouterosAPI();

        if ($api->connect($router_ip, $router_user, $router_pass)) {
            $api->write('/ip/hotspot/active/login', false);
            $api->write('=username=' . $username, false);
            $api->write('=password=' . $password);
            $response = $api->read();

            $api->disconnect();

            if (isset($response[0]['!trap'])) {
                $signin_message = 'Login failed: ' . $response[0]['!trap'][0]['message'];
            } else {
                header("Location: ../interface/");
                exit();
            }
        } else {
            $signin_message = 'Unable to connect to MikroTik router.';
        }
    } else {
        $signin_message = 'Invalid username or password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="src/css/style.css">
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
                <h3 class="subtitle">Login</h3>
                <p class="welcome-text">Welcome Back! Please enter your details.</p>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username" class="sr-only"></label>
                        <input id="username" name="username" type="text" autocomplete="username" required placeholder="Username">
                    </div>
                    <div class="form-group">
                        <label for="password" class="sr-only"></label>
                        <input id="password" name="password" type="password" autocomplete="new-password" required placeholder="Password">
                    </div>
                    <button type="submit" class="btn-login" name="signin">Login</button>
                    <button type="button" class="btn-register" onclick="window.location.href='register.php'">Register</button>
                    <?php if (isset($signin_message)) echo '<p class="message">' . $signin_message . '</p>'; ?>
                </form>
                <p class="signup-text">Don't have an account? <a href="register.php">Sign up for free</a></p>
            </div>
        </div>
    </div>
    <script src="src/js/script.js"></script>
</body>
</html>
