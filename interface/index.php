<?php
require('../backend/routeros_api.class.php');

$API = new RouterosAPI();

// Ganti dengan informasi koneksi Anda
$routerIP = '172.41.41.1';
$routerUsername = 'api';
$routerPassword = 'api';

$logs = [];

if ($API->connect($routerIP, $routerUsername, $routerPassword)) {
    // Mengambil informasi uptime
    $uptime = $API->comm("/system/resource/print")[0]['uptime'];

    // Mengambil informasi traffic dari ether1
    $trafficInfo = getTrafficInfoFromMikroTik($API);

    // Mengambil informasi User Hotspot
    $userCount = getUserCount($API);

    // Mengambil log dari MikroTik
    $logs = getLogsFromMikroTik($API);

    $API->disconnect();
} else {
    $uptime = 'Unable to connect to RouterOS';
    $trafficInfo = [
        'upload' => 'Not available',
        'download' => 'Not available'
    ];
    $userCount = 'Unable to connect to RouterOS';
}

// Fungsi untuk mengambil informasi traffic dari MikroTik
function getTrafficInfoFromMikroTik($api)
{
    $response = $api->comm('/interface monitor-traffic ether1 once');
    $upload = null;
    $download = null;

    foreach ($response as $entry) {
        if (isset($entry['tx-bits-per-second'])) {
            $upload = formatBitsPerSecond($entry['tx-bits-per-second']);
        }
        if (isset($entry['rx-bits-per-second'])) {
            $download = formatBitsPerSecond($entry['rx-bits-per-second']);
        }
    }

    return [
        'upload' => $upload,
        'download' => $download,
    ];
}

// Fungsi untuk mengubah bit per detik menjadi Mbps
function formatBitsPerSecond($bitsPerSecond, $precision = 2)
{
    $mbps = $bitsPerSecond / (1024 * 1024);
    return round($mbps, $precision) . ' Mbps';
}

// Fungsi untuk mendapatkan jumlah user hotspot terdaftar
function getUserCount($api)
{
    $users = $api->comm("/ip/hotspot/user/print");
    return count($users);
}

// Fungsi untuk mengambil log dari MikroTik
function getLogsFromMikroTik($api)
{
    $logs = $api->comm('/log/print');
    return $logs;
}

function getTemperature($api)
{
    $response = $api->comm('/system/health/print');
    if (isset($response[0]['temperature'])) {
        return $response[0]['temperature'] . 'C';
    }
    return 'Not available';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="src/img/favicon.png"/>
  <title>PAYA-NET Dashboard</title>
  <link rel="stylesheet" href="src/css/styles.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-K+QdCjGbXRu5dlHHusmOB1EYQQbWXT5nDvJqOu0/6+z6EdfPaUJaxq9c6yB0C4fiL5fT/5ujh8q05C4jjsUTdA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
 
</head>
<body>
  <div class="container">
    <aside class="sidebar">
      <div class="sidebar-header">
        <div class="user-profile">
          <img src="src/img/profile_1.jpg" alt="Avatar" class="avatar">
          <div class="user-info">
            <h3>Administrator</h3>
            <p>Admin</p>
          </div>
        </div>
        <nav class="sidebar-nav">
          <ul>
            <li><a href="#"><img src="src/img/icon-home.svg">Home</a></li>
            <li><a href="#"><img src="src/img/icon-levels.svg">Analytics</a></li>
            <li><a href="#"><img src="src/img/icon-settings.svg">Settings</a></li>
            <li><a href="#"><img src="src/img/icon-accounts.svg">Users</a></li>
          </ul>
        </nav>
        <footer class="sidebar-footer">
          <ul>
            <li><a href="#"><img src="src/img/icon-lock.svg">Logout</a></li>
          </ul>
        </footer>
      </div>
    </aside>
    <main class="main-content">
      <header>
        <h2>Dashboard Overview</h2>
        <div class="date-info">
          <span id="current-date"></span>
        </div>
      </header>
      <section class="cards">
        <div class="card">
          <h3>Uptime</h3>
          <?php if (isset($uptime) && !empty($uptime)): ?>
            <span class="uptime"><?php echo $uptime; ?></span>
          <?php else: ?>
            <span class="error">Unable to retrieve uptime</span>
          <?php endif; ?>
        </div>

        <div class="card">
          <h3>Link Up</h3>
          <div class="traffic-section">
            <strong>Upload:</strong>
            <?php if (isset($trafficInfo['upload'])): ?>
              <?php echo $trafficInfo['upload']; ?>
            <?php else: ?>
              <span class="error">Error</span>
            <?php endif; ?>
            <br>
            <strong>Download:</strong>
            <?php if (isset($trafficInfo['download'])): ?>
              <?php echo $trafficInfo['download']; ?>
            <?php else: ?>
              <span class="error">Error</span>
            <?php endif; ?>
          </div>
        </div>

        <div class="card">
          <h3>Active Users</h3>
          <?php if (isset($userCount)): ?>
            <span class="active-users"><?php echo $userCount; ?></span>
          <?php else: ?>
            <span class="error">Unable to retrieve user count</span>
          <?php endif; ?>
        </div>

        <div class="card">
          <h3>Temperature</h3>
          <?php if (isset($temperature)): ?>
            <span class="temperature"><?php echo $temperature; ?></span>
          <?php else: ?>
            <span class="error">Unable to retrieve temperature</span>
          <?php endif; ?>
        </div>
      </section>

      <div class="logs">
        <h3>Mikrotik Logs</h3>
        <div class="log-container">
            <?php if (!empty($logs)): ?>
                <?php foreach ($logs as $log): ?>
                    <div class="log-entry <?php echo strtolower($log['topics']); ?>">
                        <p class="timestamp">[<?php echo $log['time']; ?>]</p>
                        <p class="message"><?php echo $log['message']; ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <span class="error">No logs available</span>
            <?php endif; ?>
        </div>
    </div>
    </main>
  </div>

  <script>
    // Fungsi untuk mengatur tanggal saat ini
    document.getElementById('current-date').innerText = new Date().toLocaleDateString();
  </script>
</body>
</html>
