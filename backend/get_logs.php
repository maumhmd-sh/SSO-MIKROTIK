<?php
require('../backend/routeros_api.class.php');

$API = new RouterosAPI();

// Ganti dengan informasi koneksi Anda
$routerIP = '172.41.41.1';
$routerUsername = 'api';
$routerPassword = 'api';

if ($API->connect($routerIP, $routerUsername, $routerPassword)) {
    $response = $API->comm("/log/print", array("?topics=system")); // Mendapatkan log dari topik 'system'
    $API->disconnect();

    // Format data untuk dikirim sebagai JSON
    $logs = array();
    foreach ($response as $entry) {
        $logs[] = array(
            'time' => $entry['time'],
            'message' => $entry['message']
        );
    }

    echo json_encode($logs);
} else {
    echo json_encode(array()); // Jika tidak dapat terhubung, kirim array kosong
}
?>
