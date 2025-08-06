<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'leader') {
    header("Location: index.php");
    exit();
}

$leader_id = $_SESSION['user_id'];

if (isset($_POST['update'])) {
    $name = trim($_POST['name']);
    $street = trim($_POST['street']);
    $village = trim($_POST['village']);

    $stmt = $conn->prepare("UPDATE users SET name = ?, street = ?, village = ? WHERE id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sssi", $name, $street, $village, $leader_id);

    if ($stmt->execute()) {
        header("Location: wasifu.php?success=profile_updated");
        exit();
    } else {
        die("Execute failed: " . $stmt->error);
    }
}

$stmt = $conn->prepare("SELECT name, street, village FROM users WHERE id = ?");
$stmt->bind_param("i", $leader_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Edit Profile</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<style>
  :root {
    --primary: #4361ee;
    --primary-dark: #3a56d4;
    --secondary: #3f37c9;
    --text: #2b2d42;
    --text-light: #8d99ae;
    --background: #f8f9fa;
    --white: #ffffff;
    --border: #e9ecef;
    --success: #4cc9f0;
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }
  
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }
  
  body {
    font-family: 'Poppins', sans-serif;
    background: var(--background);
    color: var(--text);
    line-height: 1.6;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 1rem;
  }
  
  .edit-card {
    background: var(--white);
    padding: 2.5rem;
    border-radius: 1rem;
    box-shadow: var(--shadow);
    width: 100%;
    max-width: 420px;
    transition: var(--transition);
  }
  
  .edit-card:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  }
  
  h2 {
    margin-bottom: 1.75rem;
    color: var(--text);
    font-weight: 600;
    font-size: 1.75rem;
    text-align: center;
    position: relative;
    padding-bottom: 0.75rem;
  }
  
  h2::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 4px;
    background: var(--primary);
    border-radius: 2px;
  }
  
  label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: var(--text);
    font-size: 0.95rem;
  }
  
  .input-group {
    position: relative;
    margin-bottom: 1.5rem;
  }
  
  input[type="text"] {
    width: 100%;
    padding: 0.85rem 1.25rem;
    border: 1px solid var(--border);
    border-radius: 0.5rem;
    font-size: 0.95rem;
    color: var(--text);
    transition: var(--transition);
    background: var(--background);
  }
  
  input[type="text"]:focus {
    border-color: var(--primary);
    outline: none;
    box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
    background: var(--white);
  }
  
  button {
    background: var(--primary);
    color: var(--white);
    padding: 0.85rem;
    border: none;
    border-radius: 0.5rem;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    width: 100%;
    margin-top: 0.5rem;
    letter-spacing: 0.5px;
  }
  
  button:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
  }
  
  button:active {
    transform: translateY(0);
  }
  
  .back-link {
    display: inline-block;
    margin-top: 1.5rem;
    color: var(--primary);
    font-weight: 500;
    text-decoration: none;
    font-size: 0.9rem;
    text-align: center;
    width: 100%;
    transition: var(--transition);
    padding: 0.5rem;
    border-radius: 0.25rem;
  }
  
  .back-link:hover {
    color: var(--primary-dark);
    background: rgba(67, 97, 238, 0.1);
    text-decoration: none;
  }
  
  .back-link i {
    margin-right: 0.5rem;
  }
  
  @media (max-width: 480px) {
    .edit-card {
      padding: 1.75rem;
    }
    
    h2 {
      font-size: 1.5rem;
    }
  }
</style>
</head>
<body>
  <div class="edit-card">
    <h2>Edit Profile</h2>
    <form method="post" action="">
      <div class="input-group">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($data['name']) ?>" required />
      </div>
      
      <div class="input-group">
        <label for="street">Street Address</label>
        <input type="text" id="street" name="street" value="<?= htmlspecialchars($data['street']) ?>" required />
      </div>
      
      <div class="input-group">
        <label for="village">Village</label>
        <input type="text" id="ward" name="ward" value="<?= htmlspecialchars($data['ward']) ?>" required />
      </div>

      <button type="submit" name="update">
        Update Profile
      </button>
    </form>
    <a href="wasifu.php" class="back-link">
      <i class="fas fa-arrow-left"></i> Back to Profile
    </a>
  </div>
</body>
</html>