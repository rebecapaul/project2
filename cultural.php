<?php 
require_once "config.php";
include("bar.php");

// Get user's street from session
$user_street = $_SESSION['user_street'] ?? null;

// Base SQL query for cultural events
$sql = "SELECT * FROM announcements WHERE announcement_type = 'cultural'";

// Add filter condition if user has a street
if ($user_street) {
    $sql .= " AND (target_type = 'all' OR FIND_IN_SET('".$conn->real_escape_string($user_street)."', target_streets) > 0)";
}

$sql .= " ORDER BY event_date DESC";
$result = $conn->query($sql);

// Set default cultural event image
$default_image = 'https://images.unsplash.com/photo-1499793983690-e29da59ef1c2?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60';
?>

<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>MATUKIO YA KITAMADUNI</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #fffbeb; /* Light yellow background for cultural events */
            padding: 30px 20px;
            margin: 0;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #b45309; /* Brown-orange color for cultural events */
            font-size: 30px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .info-banner {
            background: #fef3c7;
            padding: 15px;
            border-radius: 8px;
            margin: 0 auto 30px;
            max-width: 800px;
            text-align: center;
            color: #92400e;
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
            border-left: 5px solid #b45309; /* Brown-orange border */
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

<h2>MATUKIO YA KITAMADUNI</h2>

<?php if ($user_street): ?>
    <div class="info-banner">
        Unaangalia matukio ya kitamaduni ya mtaa wako wa <strong><?= htmlspecialchars($user_street) ?></strong> na maeneo yote.
    </div>
<?php else: ?>
    <div class="info-banner">
        Unaangalia matukio ya kitamaduni kwa maeneo yote.
    </div>
<?php endif; ?>

<div class="cards-container">
<?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='card'>";
        
        // Display image if available
        if (!empty($row['image_path'])) {
            if (filter_var($row['image_path'], FILTER_VALIDATE_URL)) {
                echo "<img src='" . htmlspecialchars($row['image_path']) . "' alt='Picha ya tukio'>";
            } else {
                echo "<img src='uploads/announcements/" . htmlspecialchars($row['image_path']) . "' alt='Picha ya tukio'>";
            }
        } else {
            echo "<img src='{$default_image}' alt='Picha ya tukio'>";
        }
        
        echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
        echo "<p><strong>Maelezo:</strong><br>" . nl2br(htmlspecialchars($row['description'])) . "</p>";
        
        // Format the date nicely
        $event_date = date('j M Y', strtotime($row['event_date']));
        echo "<p class='tarehe'><strong>Tarehe ya Tukio:</strong> " . $event_date . "</p>";
        
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
    echo "<p style='text-align:center; color:#777; grid-column:1/-1;'>";
    echo $user_street 
        ? "Hakuna matukio ya kitamaduni yanayohusu mtaa wako wa " . htmlspecialchars($user_street) . " kwa sasa."
        : "Hakuna matukio ya kitamaduni yaliyopatikana kwa sasa.";
    echo "</p>";
}
?>
</div>

</body>
</html>