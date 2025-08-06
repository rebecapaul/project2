<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'leader') {
    header("Location: index.php?error=access_denied");
    exit();
}

$leader_id = $_SESSION['user_id'];
$query = "SELECT name, role, street, ward , profile_image FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $leader_id);
$stmt->execute();
$result = $stmt->get_result();
$leader = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leader Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #3498db;
            --primary-dark: #2980b9;
            --secondary: #2c3e50;
            --accent: #e67e22;
            --light-gray: #f8f9fa;
            --medium-gray: #e9ecef;
            --dark-gray: #6c757d;
            --white: #ffffff;
            --error: #dc3545;
            --success: #28a745;
            --shadow: 0 4px 12px rgba(0,0,0,0.08);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-gray);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: var(--secondary);
        }
        
        .profile-container {
            width: 100%;
            max-width: 520px;
        }
        
        .profile-card {
            background: var(--white);
            border-radius: 10px;
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: var(--transition);
        }
        
        .profile-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 30px 20px;
            text-align: center;
            color: var(--white);
        }
        
        .profile-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid rgba(255,255,255,0.2);
            object-fit: cover;
            margin: 0 auto 15px;
            display: block;
        }
        
        .profile-name {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .profile-role {
            font-size: 1rem;
            font-weight: 400;
            opacity: 0.9;
            letter-spacing: 0.5px;
        }
        
        .profile-details {
            padding: 25px 30px;
        }
        
        .detail-item {
            display: flex;
            margin-bottom: 18px;
        }
        
        .detail-content {
            flex: 1;
        }
        
        .detail-label {
            font-size: 0.8rem;
            color: var(--dark-gray);
            font-weight: 500;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .detail-value {
            font-size: 1rem;
            color: var(--secondary);
            font-weight: 500;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--medium-gray);
        }
        
        .action-buttons {
            display: flex;
            justify-content: center;
            padding: 0 30px 20px;
            gap: 12px;
        }
        
        .btn {
            padding: 12px 20px;
            border-radius: 6px;
            font-weight: 500;
            text-decoration: none;
            transition: var(--transition);
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
            flex: 1;
            text-align: center;
        }
        
        .btn-toggle {
            background: var(--medium-gray);
            color: var(--secondary);
        }
        
        .btn-toggle.active {
            background: var(--primary);
            color: var(--white);
            box-shadow: 0 2px 8px rgba(41, 128, 185, 0.3);
        }
        
        .btn-back {
            background: var(--secondary);
            color: var(--white);
        }
        
        .btn-back:hover {
            background: #1a252f;
        }
        
        .form-container {
            display: none;
            padding: 0 30px 25px;
        }
        
        .form-container.active {
            display: block;
            animation: fadeIn 0.3s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .input-group {
            margin-bottom: 18px;
        }
        
        .input-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--secondary);
            font-size: 0.9rem;
        }
        
        .input-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--medium-gray);
            border-radius: 6px;
            font-size: 0.95rem;
            transition: var(--transition);
            background-color: var(--white);
        }
        
        .input-group input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        .form-btn {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            background: var(--primary);
            color: var(--white);
            margin-top: 5px;
        }
        
        .form-btn:hover {
            background: var(--primary-dark);
        }
        
        .message {
            padding: 12px 15px;
            margin: 0 30px 20px;
            border-radius: 6px;
            font-weight: 500;
            text-align: center;
            font-size: 0.9rem;
            display: none;
        }
        
        .error {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--error);
            border-left: 3px solid var(--error);
        }
        
        .success {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success);
            border-left: 3px solid var(--success);
        }
        
        @media (max-width: 576px) {
            .profile-card {
                max-width: 100%;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 10px;
            }
            
            .btn {
                width: 100%;
            }
            
            .profile-header {
                padding: 25px 15px;
            }
            
            .profile-details {
                padding: 20px;
            }
            
            .form-container {
                padding: 0 20px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-header">
                <img src="uploads/<?= htmlspecialchars($leader['profile_image']) ?>" alt="Profile Image" class="profile-image">
                <h1 class="profile-name"><?= htmlspecialchars($leader['name']) ?></h1>
                <p class="profile-role"><?= htmlspecialchars($leader['role']) ?></p>
            </div>
            
            <div class="profile-details">
                <div class="detail-item">
                    <div class="detail-content">
                        <div class="detail-label">Street</div>
                        <div class="detail-value"><?= htmlspecialchars($leader['street']) ?></div>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-content">
                        <div class="detail-label">Village</div>
                        <div class="detail-value"><?= htmlspecialchars($leader['ward']) ?></div>
                    </div>
                </div>
            </div>
            
            <div class="action-buttons">
                <button type="button" class="btn btn-toggle" id="editToggle">Edit Profile</button>
                <button type="button" class="btn btn-toggle" id="passwordToggle">Change Password</button>
            </div>
            
            <!-- Edit Profile Form -->
            <div class="form-container" id="editForm">
                <form id="editProfileForm" method="post" action="update_profile.php">
                    <div class="input-group">
                        <label for="editName">Full Name</label>
                        <input type="text" id="editName" name="name" value="<?= htmlspecialchars($leader['name']) ?>" required>
                    </div>
                    
                    <div class="input-group">
                        <label for="editStreet">Street</label>
                        <input type="text" id="editStreet" name="street" value="<?= htmlspecialchars($leader['street']) ?>" required>
                    </div>
                    
                    <div class="input-group">
                        <label for="editVillage">Village</label>
                        <input type="text" id="editVillage" name="ward" value="<?= htmlspecialchars($leader['ward']) ?>" required>
                    </div>
                    
                    <button type="submit" class="form-btn">Update Profile</button>
                </form>
            </div>
            
            <!-- Change Password Form -->
            <div class="form-container" id="passwordForm">
                <form id="changePasswordForm" method="post" action="change_password.php">
                    <div class="input-group">
                        <label for="currentPassword">Current Password</label>
                        <input type="password" id="currentPassword" name="old_password" required>
                    </div>
                    
                    <div class="input-group">
                        <label for="newPassword">New Password</label>
                        <input type="password" id="newPassword" name="new_password" required>
                    </div>
                    
                    <div class="input-group">
                        <label for="confirmPassword">Confirm New Password</label>
                        <input type="password" id="confirmPassword" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="form-btn">Change Password</button>
                </form>
            </div>
            
            <div class="action-buttons">
                <a href="admin_page.php" class="btn btn-back">Back to Dashboard</a>
            </div>
        </div>
        
        <!-- Message Display -->
        <div class="message" id="formMessage"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editToggle = document.getElementById('editToggle');
            const passwordToggle = document.getElementById('passwordToggle');
            const editForm = document.getElementById('editForm');
            const passwordForm = document.getElementById('passwordForm');
            const messageBox = document.getElementById('formMessage');
            
            // Handle URL parameters for success/error messages
            const urlParams = new URLSearchParams(window.location.search);
            const success = urlParams.get('success');
            const error = urlParams.get('error');
            
            if (success) {
                showMessage(success, 'success');
            }
            if (error) {
                showMessage(error, 'error');
            }
            
            // Toggle functionality
            editToggle.addEventListener('click', function() {
                toggleActive(this, editForm, passwordToggle, passwordForm);
            });
            
            passwordToggle.addEventListener('click', function() {
                toggleActive(this, passwordForm, editToggle, editForm);
            });
            
            function toggleActive(activeBtn, activeForm, inactiveBtn, inactiveForm) {
                if (activeBtn.classList.contains('active')) {
                    activeBtn.classList.remove('active');
                    activeForm.classList.remove('active');
                } else {
                    activeBtn.classList.add('active');
                    activeForm.classList.add('active');
                    inactiveBtn.classList.remove('active');
                    inactiveForm.classList.remove('active');
                }
            }
            
            // Form submission handling
            document.getElementById('editProfileForm').addEventListener('submit', function(e) {
                e.preventDefault();
                submitForm(this, 'Profile updated successfully!');
            });
            
            document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
                e.preventDefault();
                if (this.new_password.value !== this.confirm_password.value) {
                    showMessage('New password and confirmation do not match!', 'error');
                    return;
                }
                submitForm(this, 'Password changed successfully!');
            });
            
            function submitForm(form, successMessage) {
                const formData = new FormData(form);
                
                fetch(form.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage(successMessage, 'success');
                        if (form.id === 'editProfileForm') {
                            // Update displayed profile info
                            document.querySelector('.profile-name').textContent = form.name.value;
                            document.querySelectorAll('.detail-value')[0].textContent = form.street.value;
                            document.querySelectorAll('.detail-value')[1].textContent = form.village.value;
                        }
                        form.reset();
                    } else {
                        showMessage(data.error || 'An error occurred', 'error');
                    }
                })
                .catch(error => {
                    showMessage('Network error: ' + error, 'error');
                });
            }
            
            function showMessage(message, type) {
                messageBox.textContent = message;
                messageBox.className = 'message ' + type;
                messageBox.style.display = 'block';
                
                setTimeout(() => {
                    messageBox.style.display = 'none';
                }, 5000);
            }
        });
    </script>
</body>
</html>