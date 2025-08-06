<?php 
require_once "config.php";
include("bar.php");

// Get user's street from session/database
$user_street = $_SESSION['user_street'] ?? null; // Make sure you store user's street in session

// Check which filter is selected (default to 'street' if user is logged in)
$filter = $_GET['filter'] ?? ($user_street ? 'street' : 'all');

// Base SQL query
$sql = "SELECT * FROM announcements WHERE announcement_type = 'marriage'";

// Add filter condition
if ($filter === 'street' && $user_street) {
    // Find announcements that include user's street in target_streets
    $sql .= " AND (target_type = 'all' OR target_streets LIKE '%$user_street%')";
}

$sql .= " ORDER BY event_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Matangazo ya Harusi</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #fdf2f8;
            padding: 30px 20px;
            margin: 0;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #db2777;
            font-size: 30px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .filter-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .filter-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            background: #e9d5ff;
            color: #6b21a8;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .filter-btn:hover {
            background: #d8b4fe;
        }

        .filter-btn.active {
            background: #9333ea;
            color: white;
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .card {
            background: #fff;
            padding: 18px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border-left: 5px solid #db2777;
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.12);
        }

        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 12px;
        }

        .card h3 {
            margin: 8px 0 5px;
            font-size: 20px;
            color: #1e40af;
        }

        .card p {
            font-size: 15px;
            color: #444;
            line-height: 1.6;
        }

        .card .tarehe {
            font-size: 14px;
            color: #666;
            margin-top: 8px;
        }

        .card .target-info {
            font-size: 13px;
            color: #888;
            margin-top: 5px;
            font-style: italic;
        }

        @media (max-width: 600px) {
            .card img {
                height: 160px;
            }
        }
    </style>
</head>
<body>

<h2>Matangazo ya Harusi</h2>

<div class="filter-buttons">
    <a href="?filter=all" class="filter-btn <?= $filter === 'all' ? 'active' : '' ?>">
        Matangazo Yote
    </a>
    <?php if ($user_street): ?>
    <a href="?filter=street" class="filter-btn <?= $filter === 'street' ? 'active' : '' ?>">
        Za Mtaa Wangu (<?= htmlspecialchars($user_street) ?>)
    </a>
    <?php endif; ?>
</div>

<div class="cards-container">
<?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='card'>";
        
        // Display image if available
        if (!empty($row['image_path'])) {
            if (filter_var($row['image_path'], FILTER_VALIDATE_URL)) {
                echo "<img src='" . htmlspecialchars($row['image_path']) . "' alt='Picha ya harusi'>";
            } else {
                echo "<img src='uploads/announcements/" . htmlspecialchars($row['image_path']) . "' alt='Picha ya harusi'>";
            }
        } else {
            echo "<img src='https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60' alt='Picha ya harusi'>";
        }
        
        echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
        echo "<p><strong>Maelezo:</strong><br>" . nl2br(htmlspecialchars($row['description'])) . "</p>";
        
        // Format the date nicely
        $event_date = date('j M Y', strtotime($row['event_date']));
        echo "<p class='tarehe'><strong>Tarehe ya Harusi:</strong> " . $event_date . "</p>";
        
        // Show target information
        if ($row['target_type'] === 'all') {
            echo "<p class='target-info'>Inahusu: Maeneo yote</p>";
        } else {
            $streets = explode(',', $row['target_streets']);
            echo "<p class='target-info'>Inahusu: " . implode(', ', array_map('trim', $streets)) . "</p>";
        }
        
        echo "</div>";
    }
} else {
    echo "<p style='text-align:center; color:#777; grid-column:1/-1;'>Hakuna matangazo ya harusi yaliyopatikana kwa sasa.</p>";
}
?>
</div>

</body>
</html>