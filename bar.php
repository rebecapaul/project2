<?php
session_start();
require_once "config.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch user details from database
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    // Handle profile image - set default if empty or invalid
    if (empty($user['profile_image']) || !filter_var($user['profile_image'], FILTER_VALIDATE_URL)) {
        $user['profile_image'] = 'https://i.pravatar.cc/150?img=0';
    }
} else {
    // Default values if user not found
    $user = [
        'name' => 'Guest',
        'street' => 'Mtaa haijasajiliwa',
        'bio' => 'No information available',
        'profile_image' => 'https://i.pravatar.cc/150?img=0'
    ];
}
?>

<!DOCTYPE html>
<html lang="sw" scroll-behavior: smooth;>
<head>
  <meta charset="UTF-8">
  <title>KIJIJI CHANGU</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f4f4f4;
    }

    .navbar {
      background-color: #2c3e50;
      padding: 10px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 1000;
    }

    .logo {
      color: #ecf0f1;
      font-size: 22px;
      font-weight: bold;
    }

    .nav-items {
      display: flex;
      align-items: center;
      gap: 25px;
    }

    .nav-items a {
      text-decoration: none;
      color: #ecf0f1;
      font-size: 16px;
      transition: color 0.3s;
      cursor: pointer;
    }

    .nav-items a:hover {
      color: #1abc9c;
    }

    .logout-button {
      background-color: #e74c3c;
      color: white;
      border: none;
      padding: 8px 14px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 15px;
      transition: background 0.3s;
    }

    .logout-button:hover {
      background-color: #c0392b;
    }

    /* Profile Panel */
    .profile-panel {
      position: fixed;
      top: 0;
      right: -400px;
      width: 350px;
      height: 100%;
      background-color: white;
      box-shadow: -2px 0 10px rgba(0,0,0,0.2);
      padding: 30px 20px;
      transition: right 0.4s ease;
      z-index: 2000;
      overflow-y: auto;
      padding-top: 80px;
    }

    .profile-panel.active {
      right: 0;
    }

    .profile-panel .close-btn {
      font-size: 24px;
      color: #333;
      position: absolute;
      top: 15px;
      right: 20px;
      cursor: pointer;
    }

    .profile-panel img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid #2c3e50;
      margin: 50px auto 20px;
      display: block;
    }

    .profile-panel h2, .profile-panel p {
      text-align: center;
      margin: 5px 0;
    }

    .profile-panel .edit-btn {
      display: block;
      margin: 20px auto 0;
      background-color: #1abc9c;
      color: white;
      border: none;
      padding: 10px 18px;
      font-size: 15px;
      border-radius: 5px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .profile-panel .edit-btn:hover {
      background-color: #16a085;
    }
  </style>
</head>
<body>

  <div class="navbar">
    <div class="logo">KIJIJI CHANGU</div>
    <div class="nav-items">
      <a href="user_page.php">Nyumbani</a>
      <a href="view.php">Dashibodi</a>
      <a id="open-profile">Wasifu</a>
      <a href="#">Mipangilio</a>
      <a href="logout.php"><button class="logout-button">Toka</button></a>
    </div>
  </div>

  <!-- Profile Panel with dynamic data -->
  <div id="profilePanel" class="profile-panel">
    <span class="close-btn" id="closeProfile">&times;</span>
    <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Picha ya Mtumiaji" onerror="this.src='https://i.pravatar.cc/150?img=0">
    <h2><?php echo htmlspecialchars($user['name']); ?></h2>
    <p><?php echo htmlspecialchars($user['street'] ?? 'Mtaa haijasajiliwa'); ?></p>
    <p><?php echo htmlspecialchars($user['bio'] ?? 'Haijasajiliwa'); ?></p>
    <button class="edit-btn">Hariri Wasifu</button>
  </div>

  <script>
    // JavaScript to open and close profile panel
    const profilePanel = document.getElementById("profilePanel");
    const openBtn = document.getElementById("open-profile");
    const closeBtn = document.getElementById("closeProfile");

    openBtn.onclick = () => {
      profilePanel.classList.add("active");
    };

    closeBtn.onclick = () => {
      profilePanel.classList.remove("active");
    };

    // Close panel when clicking outside
    window.addEventListener('click', (e) => {
      if (!profilePanel.contains(e.target) && e.target !== openBtn) {
        profilePanel.classList.remove("active");
      }
    });
  </script>

</body>
</html>