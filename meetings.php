<?php
include("bar.php");
include("config.php");

$leo = date("Y-m-d");

// Fetch mikutano iliyopita
$done_query = "SELECT * FROM mikutano WHERE tarehe < '$leo' ORDER BY tarehe DESC";
$done_result = $conn->query($done_query);

// Fetch mikutano ijayo
$upcoming_query = "SELECT * FROM mikutano WHERE tarehe >= '$leo' ORDER BY tarehe ASC";
$upcoming_result = $conn->query($upcoming_query);
?>
<!DOCTYPE html>
<html lang="sw">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mikutano</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
    }

    body {
      background: #f1f5f9;
      padding: 30px 10px;
      color: #1e293b;
    }

    .container {
      max-width: 1100px;
      margin: auto;
      background: #ffffff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.05);
    }

    h1 {
      text-align: center;
      margin-bottom: 30px;
      color: #1e40af;
      font-size: 32px;
    }

    .section-title {
      font-size: 20px;
      margin: 30px 0 15px;
      color: #2563eb;
      display: flex;
      align-items: center;
      gap: 10px;
      border-bottom: 2px solid #2563eb;
      padding-bottom: 5px;
    }

    .section-title i {
      font-size: 22px;
    }

    .meetings {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 20px;
    }

    .meeting-card {
      background: #f8fafc;
      border-left: 5px solid #3b82f6;
      padding: 20px;
      border-radius: 10px;
      position: relative;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
      transition: all 0.3s ease;
    }

    .meeting-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 6px 16px rgba(0,0,0,0.08);
    }

    .meeting-icon {
      position: absolute;
      top: 15px;
      right: 15px;
      font-size: 26px;
      color: #60a5fa;
    }

    .meeting-title {
      font-size: 18px;
      font-weight: 600;
      margin-bottom: 8px;
      color: #1e40af;
    }

    .meeting-date {
      font-size: 14px;
      color: #0284c7;
      background: #e0f2fe;
      display: inline-block;
      padding: 4px 12px;
      border-radius: 20px;
      margin-bottom: 10px;
    }

    .meeting-agenda {
      font-size: 15px;
      color: #334155;
      line-height: 1.5;
    }

    @media (max-width: 600px) {
      .container {
        padding: 20px;
      }

      h1 {
        font-size: 24px;
      }

      .meeting-title {
        font-size: 16px;
      }
    }
  </style>
</head>
<body>

  <div class="container">
    <h1>Orodha ya Mikutano</h1>

    <!-- Mikutano Iliyopita -->
    <div class="section-title"><i class='bx bx-history'></i> Mikutano Iliyofanyika Karibuni</div>
    <div class="meetings">
      <?php if ($done_result->num_rows > 0): ?>
        <?php while ($row = $done_result->fetch_assoc()): ?>
          <div class="meeting-card">
            <i class='bx bx-check-circle meeting-icon'></i>
            <div class="meeting-title"><?= htmlspecialchars($row['kichwa']) ?></div>
            <div class="meeting-date">
              <?= date("d M Y", strtotime($row['tarehe'])) ?> - <?= date("H:i", strtotime($row['muda'])) ?>
            </div>
            <div class="meeting-agenda">
              <?= htmlspecialchars($row['maelezo']) ?><br>
              <strong>Mahali:</strong> <?= htmlspecialchars($row['mahali']) ?>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>Hakuna mikutano iliyopita kwa sasa.</p>
      <?php endif; ?>
    </div>

    <!-- Mikutano Ijayo -->
    <div class="section-title"><i class='bx bx-calendar-event'></i> Mikutano Ijayo</div>
    <div class="meetings">
      <?php if ($upcoming_result->num_rows > 0): ?>
        <?php while ($row = $upcoming_result->fetch_assoc()): ?>
          <div class="meeting-card">
            <i class='bx bx-time-five meeting-icon'></i>
            <div class="meeting-title"><?= htmlspecialchars($row['kichwa']) ?></div>
            <div class="meeting-date">
              <?= date("d M Y", strtotime($row['tarehe'])) ?> - <?= date("H:i", strtotime($row['muda'])) ?>
            </div>
            <div class="meeting-agenda">
              <?= htmlspecialchars($row['maelezo']) ?><br>
              <strong>Mahali:</strong> <?= htmlspecialchars($row['mahali']) ?>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>Hakuna mikutano ijayo kwa sasa.</p>
      <?php endif; ?>
    </div>
  </div>

</body>
</html>
