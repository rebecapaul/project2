<?php
session_start();
require_once "config.php";

// Allow only leaders
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?error=access_denied");
    exit();
}

// Check for ID in URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("User ID is missing.");
}

$userId = intval($_GET['id']);

// Fetch user data
$query = "SELECT * FROM users WHERE id = $userId";
$result = $conn->query($query);

if ($result->num_rows !== 1) {
    die("User not found.");
}

$user = $result->fetch_assoc();

// Define ward and street data (same as in registration form)
$wardData = [
    "sakina" => ["Kisongo", "Oljoro", "Matevesi", "Kiranyi", "Maivo"],
    "meru" => ["Usariver", "Lenguruki", "Nkoaranga"],
    "karatu" => ["Mbulu", "Kilimatembo", "Qurus", "Olden", "Karatu"],
    "monduli" => ["Mto wa Mbu", "Selela"]
];

// Get the current ward and street from user data
$currentWard = '';
$currentStreet = $user['street'];

// Find which ward the current street belongs to
foreach ($wardData as $ward => $streets) {
    if (in_array($currentStreet, $streets)) {
        $currentWard = $ward;
        break;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $street = trim($_POST['street']);
    $role = $_POST['role'];

    // Basic validation
    if (empty($name) || empty($email) || empty($phone) || empty($street)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $update = "UPDATE users SET name = '$name', email = '$email', phone = '$phone', street = '$street', role = '$role' WHERE id = $userId";
        if ($conn->query($update)) {
            header("Location: admin_page.php?success=user_updated");
            exit();
        } else {
            $error = "Failed to update user: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hariri Mtumiaji</title>
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #e6e9ff;
            --text: #333;
            --text-light: #666;
            --background: #f4f6f8;
            --white: #fff;
            --border: #ddd;
            --error: #e74c3c;
            --success: #2ecc71;
            --shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: var(--background);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 450px;
            background: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .form-box {
            padding: 30px;
        }

        h2 {
            text-align: center;
            color: var(--primary);
            margin-bottom: 25px;
            font-size: 1.5rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--text);
            font-size: 0.9rem;
            font-weight: 500;
        }

        input, select {
            width: 100%;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid var(--border);
            font-size: 0.95rem;
        }

        input:focus, select:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        .btn-group {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        button, .btn {
            padding: 12px 25px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: var(--primary);
            color: var(--white);
        }

        .btn-primary:hover {
            background: #364fc7;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text);
        }

        .btn-outline:hover {
            background: var(--background);
        }

        .message {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            text-align: center;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #f5c6cb;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            border-left: 4px solid #c3e6cb;
        }

        @media (max-width: 576px) {
            .btn-group {
                flex-direction: column;
                gap: 10px;
            }
            
            .btn, button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-box">
            <h2>Hariri Mtumiaji</h2>

            <?php if (isset($error)): ?>
                <div class="message error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="form-group">
                    <label for="name">Jina Kamili</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Barua Pepe</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone">Namba ya Simu</label>
                    <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="ward">Wilaya</label>
                    <select id="ward" name="ward" required onchange="updateStreets()">
                        <option value="">-- Chagua Wilaya --</option>
                        <option value="sakina" <?= $currentWard === 'sakina' ? 'selected' : '' ?>>Sakina</option>
                        <option value="meru" <?= $currentWard === 'meru' ? 'selected' : '' ?>>Meru</option>
                        <option value="karatu" <?= $currentWard === 'karatu' ? 'selected' : '' ?>>Karatu</option>
                        <option value="monduli" <?= $currentWard === 'monduli' ? 'selected' : '' ?>>Monduli</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="street">Mtaa</label>
                    <select id="street" name="street" required>
                        <option value="">-- Chagua Mtaa --</option>
                        <?php if ($currentWard && $currentStreet): ?>
                            <?php foreach ($wardData[$currentWard] as $street): ?>
                                <option value="<?= htmlspecialchars($street) ?>" <?= $street === $currentStreet ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($street) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="role">Jukumu</label>
                    <select id="role" name="role" required>
                        <option value="citizen" <?= $user['role'] === 'citizen' ? 'selected' : '' ?>>Raia</option>
                        <option value="leader" <?= $user['role'] === 'leader' ? 'selected' : '' ?>>Kiongozi</option>
                    </select>
                </div>

                <div class="btn-group">
                    <a href="admin_page.php" class="btn btn-outline">Rudi</a>
                    <button type="submit" class="btn-primary">Hifadhi Mabadiliko</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Ward and street data
        const wardData = {
            sakina: ["Kisongo", "Oljoro", "Matevesi", "Kiranyi", "Maivo"],
            meru: ["Usariver", "Lenguruki", "Nkoaranga"],
            karatu: ["Mbulu", "Kilimatembo", "Qurus", "Olden", "Karatu"],
            monduli: ["Mto wa Mbu", "Selela"]
        };

        function updateStreets() {
            const wardSelect = document.getElementById('ward');
            const streetSelect = document.getElementById('street');
            const selectedWard = wardSelect.value;
            
            // Clear existing options
            streetSelect.innerHTML = '<option value="">-- Chagua Mtaa --</option>';
            
            if (selectedWard && wardData[selectedWard]) {
                wardData[selectedWard].forEach(street => {
                    const option = document.createElement('option');
                    option.value = street;
                    option.textContent = street;
                    streetSelect.appendChild(option);
                });
            }
        }

        // Initialize streets when page loads
        document.addEventListener('DOMContentLoaded', function() {
            const wardSelect = document.getElementById('ward');
            if (wardSelect.value) {
                updateStreets();
            }
        });
    </script>
</body>
</html>