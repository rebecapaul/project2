<?php
session_start();
require_once 'config.php';

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login
if (isset($_POST['login'])) {
    $email_or_phone = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Get user with role from users table
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR phone = ?");
    $stmt->bind_param("ss", $email_or_phone, $email_or_phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['user_role'] = $row['role']; // Get role from users table
            $_SESSION['profile_image'] = $row['profile_image'];
            
            // Redirect based on role
           // Redirect based on role
      // Redirect based on role
if ($row['role'] == 'leader') {
    header("Location: admin_page.php");
} else {
    header("Location: user_page.php");
}
            exit();
        } else {
            $_SESSION['login_error'] = "Nenosiri si sahihi.";
        }
    } else {
        $_SESSION['login_error'] = "Barua pepe au namba ya simu haipo.";
    }

    $_SESSION['active_form'] = 'login';
    header("Location: index.php");
    exit();
}

// Handle registration
if (isset($_POST['register'])) {
    // Get form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $age = intval($_POST['age']);
    $phone = trim($_POST['phone']);
    $gender = $_POST['gender'];
    $street = $_POST['street'];
    $ward = $_POST['ward'];

 // Remove all non-digit characters first
$phone = preg_replace('/[^0-9]/', '', $phone);

// Validate phone number (255 followed by 9 digits = 12 total)
if (!preg_match('/^255[0-9]{9}$/', $phone)) {
    $_SESSION['register_error'] = "Namba ya simu lazima iwe na tarakimu 12 (255 ikianza na namba 9 baadae). Mfano: 255712345678";
    $_SESSION['active_form'] = 'register';
    header("Location: index.php");
    exit();
}

    // Check if email or phone exists
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
    $checkStmt->bind_param("ss", $email, $phone);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $_SESSION['register_error'] = "Barua pepe au namba ya simu tayari imesajiliwa.";
        $_SESSION['active_form'] = 'register';
        header("Location: index.php");
        exit();
    }

    // Handle profile image upload
    $profileImage = $_FILES['profile_image'];
    $imageName = time() . '_' . basename($profileImage['name']);
    $targetDir = "uploads/";
    $targetPath = $targetDir . $imageName;

    // Create uploads directory if it doesn't exist
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    if (!move_uploaded_file($profileImage['tmp_name'], $targetPath)) {
        $_SESSION['register_error'] = "Imeshindikana kupakia picha.";
        $_SESSION['active_form'] = 'register';
        header("Location: index.php");
        exit();
    }

$stmt = $conn->prepare("INSERT INTO users (name, email, password, age, phone, gender, street, ward, profile_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssisssss", $name, $email, $password, $age, $phone, $gender, $street, $ward, $imageName);


if ($stmt->execute()) {
    // Set session variables
    $_SESSION['user_id'] = $stmt->insert_id;
    $_SESSION['user_name'] = $name;
    $_SESSION['profile_image'] = $imageName;
    $_SESSION['user_role'] = 'citizen'; // Set default role manually for redirection

    // Redirect based on role
    if ($_SESSION['user_role'] == 'leader') {
        header("Location: admin_page.php");
    } else {
        header("Location: user_page.php");
    }
    exit();
}

    } else {
        $_SESSION['register_error'] = "Kusajili kumeshindikana. Jaribu tena.";
        $_SESSION['active_form'] = 'register';
        header("Location: index.php");
        exit();
    }


// Close connection
$conn->close();



