<?php
session_start();
require_once "config.php";

// Check if user is logged in and is a leader
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'leader') {
    header("Location: index.php?error=access_denied");
    exit();
}

// Fetch ward and street data from database
$wards = [];
$totalUsers = 0;

// Get all wards and their streets with user counts
$wardQuery = "SELECT street AS ward, COUNT(*) AS user_count FROM users GROUP BY street";
$wardResult = $conn->query($wardQuery);

if ($wardResult) {
    while ($row = $wardResult->fetch_assoc()) {
        $wards[$row['ward']] = [
            'user_count' => $row['user_count'],
            'streets' => []
        ];
        $totalUsers += $row['user_count'];
    }
}

// Get all streets and their user counts
$streetQuery = "SELECT street AS ward, street, COUNT(*) AS user_count FROM users GROUP BY street";
$streetResult = $conn->query($streetQuery);

if ($streetResult) {
    while ($row = $streetResult->fetch_assoc()) {
        if (isset($wards[$row['ward']])) {
            $wards[$row['ward']]['streets'][$row['street']] = $row['user_count'];
        }
    }
}

// Function to get users in a specific street
function getUsersByStreet($conn, $street) {
    $users = [];
    $query = "SELECT id, name, email, phone FROM users WHERE street = '$street' ORDER BY name";
    $result = $conn->query($query);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    return $users;
}

// Check if we're viewing a specific street
$streetUsers = [];
$currentStreet = '';
if (isset($_GET['street'])) {
    $currentStreet = $_GET['street'];
    $streetUsers = getUsersByStreet($conn, $currentStreet);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Ward and Street Report</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    :root {
      --primary: #4361ee;
      --primary-light: #e6e9ff;
      --text: #333;
      --text-light: #666;
      --background: #f4f6f8;
      --white: #fff;
      --border: #ddd;
      --shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    * {
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: var(--background);
      margin: 0;
      padding: 2rem 1rem;
      color: var(--text);
    }

    .container {
      max-width: 900px;
      margin: 0 auto;
    }

    header {
      text-align: center;
      margin-bottom: 2rem;
    }

    h1 {
      color: var(--primary);
      margin-bottom: 0.5rem;
    }

    .subtitle {
      color: var(--text-light);
      font-size: 1rem;
    }

    .report-card {
      background: var(--white);
      border-radius: 10px;
      box-shadow: var(--shadow);
      overflow: hidden;
      margin-bottom: 2rem;
    }

    .ward-header {
      background: var(--primary);
      color: var(--white);
      padding: 1rem 1.5rem;
      font-size: 1.1rem;
      font-weight: 600;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .streets-list {
      padding: 1.5rem;
    }

    .street-item {
      padding: 1rem;
      border-bottom: 1px solid var(--border);
      display: flex;
      justify-content: space-between;
      align-items: center;
      cursor: pointer;
      transition: background-color 0.2s;
    }

    .street-item:hover {
      background-color: var(--primary-light);
    }

    .street-item:last-child {
      border-bottom: none;
    }

    .street-name {
      font-weight: 500;
    }

    .street-count {
      background: var(--primary-light);
      color: var(--primary);
      padding: 0.3rem 0.8rem;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 600;
    }

    .stats-summary {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      margin-bottom: 2rem;
    }

    .stat-card {
      background: var(--white);
      padding: 1.5rem;
      border-radius: 8px;
      box-shadow: var(--shadow);
      text-align: center;
    }

    .stat-value {
      font-size: 2rem;
      font-weight: 700;
      color: var(--primary);
      margin: 0.5rem 0;
    }

    .stat-label {
      color: var(--text-light);
      font-size: 0.9rem;
    }

    .user-list {
      padding: 1.5rem;
      background-color: var(--white);
      border-radius: 8px;
      box-shadow: var(--shadow);
      margin-bottom: 2rem;
    }

    .user-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
    }

    .user-header h3 {
      color: var(--primary);
      margin: 0;
    }

    .back-button {
      background: var(--primary);
      color: var(--white);
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 5px;
      cursor: pointer;
      text-decoration: none;
      font-size: 0.9rem;
    }

    .user-table {
      width: 100%;
      border-collapse: collapse;
    }

    .user-table th, .user-table td {
      padding: 0.75rem;
      text-align: left;
      border-bottom: 1px solid var(--border);
    }

    .user-table th {
      background-color: var(--primary-light);
      color: var(--primary);
      font-weight: 600;
    }

    .user-table tr:hover {
      background-color: var(--primary-light);
    }

    @media (max-width: 600px) {
      .stats-summary {
        grid-template-columns: 1fr;
      }
      
      .user-table {
        display: block;
        overflow-x: auto;
      }
    }
  </style>
</head>
<body>

  <div class="container">
    <header>
      <h1>Ward and Street Report</h1>
      <p class="subtitle">Detailed breakdown of wards and their registered members</p>
    </header>

    <div class="stats-summary">
      <div class="stat-card">
        <div class="stat-value"><?= count($wards) ?></div>
        <div class="stat-label">Total Wards</div>
      </div>
      <div class="stat-card">
        <div class="stat-value"><?= $totalUsers ?></div>
        <div class="stat-label">Total Members</div>
      </div>
      <div class="stat-card">
        <div class="stat-value"><?= count($wards) ?></div>
        <div class="stat-label">Total Streets</div>
      </div>
    </div>

    <?php if (!empty($currentStreet)): ?>
      <div class="user-list">
        <div class="user-header">
          <h3>Members in <?= htmlspecialchars($currentStreet) ?></h3>
          <a href="ward_report.php" class="back-button">Back to Wards</a>
        </div>
        
        <table class="user-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($streetUsers as $user): ?>
              <tr>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['phone']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <?php foreach ($wards as $ward => $wardData): ?>
        <div class="report-card">
          <div class="ward-header">
            <span><?= htmlspecialchars($ward) ?> Ward</span>
            <span class="street-count"><?= $wardData['user_count'] ?> Members</span>
          </div>
          <div class="streets-list">
            <?php foreach ($wardData['streets'] as $street => $count): ?>
              <a href="ward_report.php?street=<?= urlencode($street) ?>" class="street-item">
                <span class="street-name"><?= htmlspecialchars($street) ?></span>
                <span class="street-count"><?= $count ?> Members</span>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

</body>
</html>