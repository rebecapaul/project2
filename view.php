<?php
session_start();
require_once "config.php";
// Fetch announcements
$announcements = [];
$query = "SELECT title, description, image_path, event_date FROM announcements 
          ORDER BY event_date DESC";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements</title>
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --text-color: #333;
            --light-gray: #f8f9fa;
            --white: #ffffff;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: var(--light-gray);
            color: var(--text-color);
        }
        
        .announcements-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            padding: 20px;
        }
        
        .announcement-card {
            background: var(--white);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .announcement-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .card-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .card-content {
            padding: 20px;
        }
        
        .card-date {
            display: inline-block;
            background: var(--primary-color);
            color: var(--white);
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-bottom: 10px;
        }
        
        .card-title {
            font-size: 1.4rem;
            margin: 10px 0;
            color: var(--secondary-color);
        }
        
        .card-description {
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        .read-more {
            display: inline-block;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }
        
        .read-more:hover {
            color: var(--secondary-color);
        }
        
        @media (max-width: 768px) {
            .announcements-container {
                grid-template-columns: 1fr;
                padding: 10px;
            }
            
            .card-image {
                height: 180px;
            }
        }
        
        @media (max-width: 480px) {
            .card-title {
                font-size: 1.2rem;
            }
            
            .card-description {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="announcements-container">
        <?php if (!empty($announcements)): ?>
            <?php foreach ($announcements as $announcement): ?>
                <div class="announcement-card">
                    <?php if (!empty($announcement['image_path'])): ?>
                        <img src="<?= htmlspecialchars($announcement['image_path']) ?>" 
                             alt="<?= htmlspecialchars($announcement['title']) ?>" 
                             class="card-image">
                    <?php else: ?>
                        <div class="card-image" style="background: #ddd; display: flex; align-items: center; justify-content: center;">
                            <span>No Image</span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card-content">
                        <span class="card-date">
                            <?= date('M j, Y', strtotime($announcement['event_date'])) ?>
                        </span>
                        <h3 class="card-title"><?= htmlspecialchars($announcement['title']) ?></h3>
                        <p class="card-description">
                            <?= nl2br(htmlspecialchars(mb_strimwidth($announcement['description'], 0, 150, '...'))) ?>
                        </p>
                        <a href="#" class="read-more">Read More</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No announcements available.</p>
        <?php endif; ?>
    </div>
</body>
</html>