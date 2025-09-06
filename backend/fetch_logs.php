<?php
require('routeros_api.class.php');

$API = new RouterosAPI();

// Ganti dengan informasi koneksi Anda
$routerIP = '172.41.41.1';
$routerUsername = 'api';
$routerPassword = 'api';

if ($API->connect($routerIP, $routerUsername, $routerPassword)) {
    // Mengambil log dari MikroTik
    $logs = $API->comm('/log/print');

    $API->disconnect();

    $logEntries = array();
    foreach ($logs as $log) {
        $logEntries[] = array(
            'timestamp' => $log['time'],
            'message' => $log['message'],
            'type' => strtolower($log['topics'])
        );
    }

    echo json_encode($logEntries);
} else {
    echo json_encode(array('error' => 'Unable to connect to RouterOS'));
}
?>
