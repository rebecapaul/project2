<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'leader') {
    header("Location: index.php");
    exit();
}

$leader_id = $_SESSION['user_id'];
$message = "";

if (isset($_POST['change'])) {
    $old = $_POST['old_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($new !== $confirm) {
        $message = "New password and confirm password do not match.";
    } else {
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $leader_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (password_verify($old, $result['password'])) {
            $hashed = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashed, $leader_id);
            $stmt->execute();
            $message = "Password changed successfully!";
        } else {
            $message = "Incorrect old password!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Change Password</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root {
    --primary: #3498db;
    --primary-dark: #2980b9;
    --secondary: #2c3e50;
    --accent: #e67e22;
    --light-gray: #ecf0f1;
    --dark-gray: #7f8c8d;
    --white: #ffffff;
    --error: #e74c3c;
    --success: #2ecc71;
    --shadow: 0 10px 20px rgba(0,0,0,0.1);
    --transition: all 0.3s ease;
  }
  
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }
  
  body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
  }
  
  .password-card {
    background: var(--white);
    width: 100%;
    max-width: 450px;
    border-radius: 16px;
    box-shadow: var(--shadow);
    overflow: hidden;
    transition: var(--transition);
  }
  
  .password-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.15);
  }
  
  .password-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    padding: 25px 20px;
    text-align: center;
    color: var(--white);
  }
  
  .password-header h2 {
    font-size: 1.8rem;
    font-weight: 600;
  }
  
  .password-body {
    padding: 30px;
  }
  
  .message {
    padding: 12px;
    margin-bottom: 20px;
    border-radius: 8px;
    font-weight: 500;
    text-align: center;
  }
  
  .error {
    background-color: rgba(231, 76, 60, 0.1);
    color: var(--error);
    border-left: 4px solid var(--error);
  }
  
  .success {
    background-color: rgba(46, 204, 113, 0.1);
    color: var(--success);
    border-left: 4px solid var(--success);
  }
  
  .input-group {
    position: relative;
    margin-bottom: 20px;
  }
  
  .input-group input {
    width: 100%;
    padding: 14px 20px;
    border: 1px solid var(--light-gray);
    border-radius: 8px;
    font-size: 1rem;
    transition: var(--transition);
    background-color: var(--light-gray);
  }
  
  .input-group input:focus {
    border-color: var(--primary);
    outline: none;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
    background-color: var(--white);
  }
  
  .input-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: var(--secondary);
  }
  
  .btn {
    width: 100%;
    padding: 14px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: var(--transition);
    border: none;
    background: var(--primary);
    color: var(--white);
    margin-top: 10px;
  }
  
  .btn:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
  }
  
  .btn:active {
    transform: translateY(0);
  }
  
  .back-link {
    display: block;
    text-align: center;
    margin-top: 20px;
    color: var(--primary);
    font-weight: 500;
    text-decoration: none;
    transition: var(--transition);
  }
  
  .back-link:hover {
    color: var(--primary-dark);
    text-decoration: underline;
  }
  
  @media (max-width: 480px) {
    .password-card {
      max-width: 100%;
    }
    
    .password-body {
      padding: 20px;
    }
  }
</style>
</head>
<body>
  <div class="password-card">
    <div class="password-header">
      <h2>Change Password</h2>
    </div>
    
    <div class="password-body">
      <?php if($message): ?>
        <div class="message <?= strpos($message, 'successfully') !== false ? 'success' : 'error' ?>">
          <?= htmlspecialchars($message) ?>
        </div>
      <?php endif; ?>
      
      <form method="post" action="">
        <div class="input-group">
          <label for="old_password">Current Password</label>
          <input type="password" id="old_password" name="old_password" placeholder="Enter current password" required>
        </div>
        
        <div class="input-group">
          <label for="new_password">New Password</label>
          <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
        </div>
        
        <div class="input-group">
          <label for="confirm_password">Confirm New Password</label>
          <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
        </div>
        
        <button type="submit" name="change" class="btn">Change Password</button>
      </form>
      
      <a href="wasifu.php" class="back-link">Back to Profile</a>
    </div>
  </div>
</body>
</html>