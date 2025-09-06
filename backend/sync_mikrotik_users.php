<?php
require 'config.php';
require 'routeros_api.class.php';

function sync_users_from_mikrotik() {
    global $pdo;

    $router_ip = '172.41.41.1';
    $router_user = 'api';
    $router_pass = 'api';

    $api = new RouterosAPI();

    if ($api->connect($router_ip, $router_user, $router_pass)) {
        $api->write('/ip/hotspot/user/print');
        $response = $api->read();

        $api->disconnect();

        if (isset($response[0]['!trap'])) {
            return 'Failed to retrieve users from MikroTik: ' . $response[0]['!trap'][0]['message'];
        } else {
            foreach ($response as $user) {
                $username = $user['name'];
                $password = $user['password'];

                $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
                $stmt->execute([$username]);
                $existing_user = $stmt->fetch();

                if ($existing_user) {
                    $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE username = ?');
                    $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $username]);
                } else {
                    $stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
                    $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
                }
            }
            return 'Users synced from MikroTik successfully!';
        }
    } else {
        return 'Unable to connect to MikroTik router.';
    }
}

// Contoh penggunaan untuk menyinkronkan pengguna
$sync_message = sync_users_from_mikrotik();
echo $sync_message;
