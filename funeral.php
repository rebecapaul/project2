<?php 
include 'config.php';
include("bar.php"); 

// Get user's street from session
$user_street = $_SESSION['user_street'] ?? null;

// Base SQL query for funeral announcements
$sql = "SELECT * FROM announcements WHERE announcement_type = 'funeral'";

// Add filter condition if user has a street
if ($user_street) {
    $sql .= " AND (target_type = 'all' OR FIND_IN_SET('".$conn->real_escape_string($user_street)."', target_streets) > 0)";
}

$sql .= " ORDER BY event_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="sw">
<head>
  <meta charset="UTF-8">
  <title>Matangazo ya Mazishi</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f1f5f9;
      margin: 0;
      padding: 30px 15px;
      color: #1e293b;
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
      font-size: 28px;
      color: #1e40af;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .cards-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 25px;
      max-width: 1100px;
      margin: auto;
    }

    .card {
      background: #ffffff;
      padding: 18px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      transition: transform 0.3s ease;
      border-left: 5px solid #dc2626;
    }

    .card:hover {
      transform: translateY(-6px);
    }

    .card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-radius: 8px;
      margin-bottom: 15px;
    }

    .card h3 {
      font-size: 20px;
      margin-bottom: 10px;
      color: #dc2626;
    }

    .card p {
      font-size: 15px;
      color: #374151;
      line-height: 1.6;
    }

    .card .date {
      font-size: 14px;
      color: #6b7280;
      margin-top: 10px;
      font-style: italic;
    }
    
    .card .target-info {
      font-size: 13px;
      color: #6b7280;
      margin-top: 8px;
    }

    @media (max-width: 600px) {
      .card img {
        height: 150px;
      }

      h2 {
        font-size: 22px;
      }
    }
  </style>
</head>
<body>

  <h2>Matangazo ya Mazishi</h2>

  <div class="cards-container">
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='card'>";
            
            // Display image if available
            if (!empty($row['image_path'])) {
                if (filter_var($row['image_path'], FILTER_VALIDATE_URL)) {
                    echo "<img src='" . htmlspecialchars($row['image_path']) . "' alt='Picha ya mazishi'>";
                } else {
                    echo "<img src='uploads/announcements/" . htmlspecialchars($row['image_path']) . "' alt='Picha ya mazishi'>";
                }
            } else {
                // Default funeral image
                echo "<img src='https://images.unsplash.com/photo-1580336272340-cd5a9507e3e6?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60' alt='Picha ya mazishi'>";
            }
            
            echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
            echo "<p>" . nl2br(htmlspecialchars($row['description'])) . "</p>";
            
            // Format the date
            $event_date = date('j M Y', strtotime($row['event_date']));
            echo "<p class='date'><strong>Tarehe ya Mazishi:</strong> " . $event_date . "</p>";
            
            // Show target information if needed
            if ($row['target_type'] === 'specific') {
                $streets = explode(',', $row['target_streets']);
                echo "<p class='target-info'>Inahusu: " . implode(', ', array_map('trim', $streets)) . "</p>";
            }
            
            echo "</div>";
        }
    } else {
        echo "<p style='text-align:center; color:#dc2626; grid-column:1/-1;'>Hakuna tangazo la mazishi lililopatikana kwa sasa.</p>";
    }
    $conn->close();
    ?>
  </div>

</body>
</html>