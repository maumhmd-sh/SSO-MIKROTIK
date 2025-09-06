<?php
session_start();
require '../backend/config.php';
require '../backend/routeros_api.class.php';
require '../backend/functions.php'; // Sertakan file functions.php
require 'header.php';
require 'sidebar.php';

// if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
//     header('Location: ../public/index.php');
//     exit();
// }

$router_ip = '172.41.41.1';
$router_user = 'api';
$router_pass = 'api';

$data = get_mikrotik_data($router_ip, $router_user, $router_pass);

// Ambil data hanya dari interface port 4
$interface4 = array_filter($data['interfaces'], function($interface) {
    return $interface['name'] === 'ether4'; // Sesuaikan dengan nama interface port 4 Anda
});

// Ambil data penggunaan dari interface port 4
$labels = [];
$txData = [];
$rxData = [];

foreach ($interface4 as $interface) {
    $labels[] = $interface['name'];
    $txData[] = $interface['tx-byte'];
    $rxData[] = $interface['rx-byte'];
}

?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3">
            <?php include 'sidebar.php'; ?>
        </div>
        <div class="col-md-9">
            <h1>Admin Dashboard</h1>
            <div class="card">
                <div class="card-header">Internet Usage (Interface Port 4)</div>
                <div class="card-body">
                    <canvas id="usageChart" width="400" height="200"></canvas>
                </div>
            </div>
            <div class="card">
                <div class="card-header">System Uptime</div>
                <div class="card-body">
                    <pre><?php print_r($data['system_resource']); ?></pre>
                </div>
            </div>
            <div class="card">
                <div class="card-header">MikroTik Logs</div>
                <div class="card-body" id="logs">
                    <pre><?php print_r($data['logs']); ?></pre>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data untuk grafik
    var labels = <?php echo json_encode($labels); ?>;
    var txData = <?php echo json_encode($txData); ?>;
    var rxData = <?php echo json_encode($rxData); ?>;

    // Konfigurasi Chart
    var ctx = document.getElementById('usageChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'TX Bytes',
                data: txData,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }, {
                label: 'RX Bytes',
                data: rxData,
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
