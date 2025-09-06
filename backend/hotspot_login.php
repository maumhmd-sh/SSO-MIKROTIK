<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$user = $_SESSION['user'];
$username = $user['username'];
$password = $user['password'];

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

    if (isset($response['!trap'])) {
        echo 'Login failed: ' . $response['!trap'][0]['message'];
    } else {
        echo 'Login successful!';
    }
} else {
    echo 'Unable to connect to MikroTik router.';
}
?>
