<?php
session_start();
require_once "config.php";
require_once 'SmsSender.php';


// Check if leader is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'leader') {
    header("Location: index.php?error=access_denied");
    exit();
}

$announcement_types = [
    'marriage' => ['Marriage Ceremony', 'bx bx-heart'],
    'funeral' => ['Funeral Announcement', 'bx bx-leaf'],
    'cultural' => ['Cultural Event', 'bx bx-mask'],
    'sports' => ['Sports Tournament', 'bx bx-football'],
    'environment' => ['Environmental Drive', 'bx bx-recycle'],
    'meeting' => ['Community Meeting', 'bx bx-group']
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize inputs
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $announcement_type = $_POST['announcement_type'] ?? '';
    $created_by = $_SESSION['user_id'] ?? 0; // Assuming you have user session
    
    // Get target audience values
    $target_type = $_POST['target_type'] ?? 'all';
    $target_streets = null;
    
    if ($target_type === 'specific' && !empty($_POST['target_streets'])) {
        $selected_streets = $_POST['target_streets'];
        if (is_array($selected_streets)) {
            $target_streets = implode(',', array_map('trim', $selected_streets));
        } else {
            $target_streets = trim($selected_streets);
        }
    }

    // Handle file upload
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/announcements/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            $filename = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = $target_file;
            }
        }
    }
    
    // Insert announcement
$stmt = $conn->prepare("INSERT INTO announcements (title, description, event_date, announcement_type, image_path, created_by, target_type, target_streets) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssiss", $title, $description, $event_date, $announcement_type, $image_path, $created_by, $target_type, $target_streets);

if ($stmt->execute()) {
    // Only send SMS if announcement was successfully created
    
    // Build the query based on target type
    $query = "SELECT phone FROM users WHERE 1=1";
    
    if ($target_type === 'specific' && !empty($target_streets)) {
        $streets = explode(',', $target_streets);
        $placeholders = implode(',', array_fill(0, count($streets), '?'));
        $query .= " AND street IN ($placeholders)";
    }
    
    $stmt = $conn->prepare($query);
    
    if ($target_type === 'specific' && !empty($target_streets)) {
        $stmt->bind_param(str_repeat('s', count($streets)), ...$streets);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch all phone numbers
    $phone_numbers = [];
    while ($row = $result->fetch_assoc()) {
        if (!empty($row['phone'])) {
            $phone_numbers[] = $row['phone'];
        }
    }
    
    // Send SMS with formatted announcement details
    if (!empty($phone_numbers)) {
        $smsSender = new SmsSender();
        
        // Format the event date for display
        $formatted_date = date('j M Y', strtotime($event_date));
        
        // Construct the SMS message
        $message = "KIJIJI CHANGU TAARIFA MUHIMU\n";
        $message .= "Title: $title\n";
        $message .= "Date: $formatted_date\n";
        $message .= "Details: " . substr($description, 0, 100); // Limit description to 100 chars
        
        // For long descriptions, add ellipsis
        if (strlen($description) > 100) {
            $message .= "...";
        }
        
        foreach ($phone_numbers as $phone) {
            try {
                // Remove any non-numeric characters from phone number
                $cleaned_phone = preg_replace('/[^0-9]/', '', $phone);
                
                // Add country code if missing (assuming Tanzania +255)
                if (strlen($cleaned_phone) === 9 && substr($cleaned_phone, 0, 1) === '7') {
                    $cleaned_phone = '255' . $cleaned_phone;
                }
                
                $smsSender->sendSms($cleaned_phone, $message);
            } catch (Exception $e) {
                // Log error but don't stop execution
                error_log("Failed to send SMS to $phone: " . $e->getMessage());
            }
        }
    }
    
    // Success response
    $_SESSION['success'] = "Announcement created and notifications sent!";
    header("Location: announcements.php");
    exit();
} else {
    // Error response
    $_SESSION['error'] = "Error creating announcement: " . $conn->error;
    header("Location: announcements.php");
    exit();
}
}
//requirement

// $destinationPhone = '255734105797';
// $messagesent = 'THis os a message';

//call send sms

// $response = $sms_Sender-> sendSms($destinationPhone, $messagesent);
// echo $response;
//         $success = "Announcement posted successfully!";
//     } else {
//         $error = "Error posting announcement: " . $conn->error;
//     }

// // Fetch existing announcements
// $announcements = [];
// $query = "SELECT * FROM announcements ORDER BY event_date DESC LIMIT 10";
// $result = $conn->query($query);
// if ($result) {
//     $announcements = $result->fetch_all(MYSQLI_ASSOC);
// }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Announcements</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #e0e7ff;
            --primary-dark: #4338ca;
            --secondary: #1e293b;
            --success: #10b981;
            --error: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --light: #f8fafc;
            --dark: #0f172a;
            --gray: #64748b;
            --gray-light: #e2e8f0;
            --white: #ffffff;
            --radius: 0.5rem;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--light);
            color: var(--dark);
            min-height: 100vh;
            line-height: 1.5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--gray-light);
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* Step Indicator */
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3rem;
            position: relative;
            counter-reset: step;
        }

        .step-indicator::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--gray-light);
            z-index: 1;
            transform: translateY(-50%);
        }

        .step {
            position: relative;
            z-index: 2;
            text-align: center;
            width: 100%;
        }

        .step-number {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background: var(--gray-light);
            color: var(--gray);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
            font-weight: 600;
            position: relative;
            border: 3px solid var(--white);
        }

        .step.active .step-number {
            background: var(--primary);
            color: var(--white);
            box-shadow: 0 0 0 4px var(--primary-light);
        }

        .step.completed .step-number {
            background: var(--success);
            color: var(--white);
            box-shadow: 0 0 0 4px #d1fae5;
        }

        .step.completed .step-number::after {
            content: '\2713';
        }

        .step-label {
            font-size: 0.875rem;
            color: var(--gray);
            font-weight: 500;
        }

        .step.active .step-label {
            color: var(--primary);
            font-weight: 600;
        }

        /* Form Steps */
        .form-steps {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .form-step {
            display: none;
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 2rem;
        }

        .form-step.active {
            display: block;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .step-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--secondary);
        }

        input, select, textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--gray-light);
            border-radius: var(--radius);
            font-size: 0.875rem;
            transition: var(--transition);
            background-color: var(--white);
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        /* File Upload */
        .file-upload {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .file-upload-input {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-upload-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            border: 2px dashed var(--gray-light);
            border-radius: var(--radius);
            background-color: var(--light);
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .file-upload-label:hover {
            border-color: var(--primary);
            background-color: var(--primary-light);
        }

        .file-upload-icon {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .file-upload-text {
            font-size: 0.875rem;
            color: var(--gray);
        }

        .file-upload-text strong {
            color: var(--primary);
            font-weight: 500;
        }

        .file-preview {
            margin-top: 1rem;
            display: none;
        }

        .file-preview img {
            max-width: 100%;
            max-height: 200px;
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
        }

        /* Button Group */
        .btn-group {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            gap: 1rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius);
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background-color: var(--primary);
            color: var(--white);
            border: none;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--gray-light);
            color: var(--gray);
        }

        .btn-outline:hover {
            background-color: var(--gray-light);
            color: var(--dark);
        }

        /* Announcements List */
        .announcements-list {
            display: grid;
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .announcement-card {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 1.5rem;
            transition: var(--transition);
        }

        .announcement-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .announcement-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .announcement-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--secondary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .announcement-type {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            margin-left: 0.5rem;
        }

        .announcement-date {
            font-size: 0.75rem;
            color: var(--gray);
        }

        .announcement-description {
            color: var(--dark);
            margin: 1rem 0;
            font-size: 0.875rem;
            line-height: 1.6;
        }

        .announcement-image {
            width: 100%;
            max-height: 300px;
            object-fit: cover;
            border-radius: var(--radius);
            margin-top: 1rem;
            box-shadow: var(--shadow-sm);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--gray);
        }

        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--gray-light);
        }

        /* Messages */
        .message {
            padding: 1rem;
            border-radius: var(--radius);
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .message.success {
            background-color: #d1fae5;
            color: #065f46;
            border-left: 4px solid var(--success);
        }

        .message.error {
            background-color: #fee2e2;
            color: #b91c1c;
            border-left: 4px solid var(--error);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .step-label {
                display: none;
            }
            
            .btn-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">
                <i class='bx bx-message-alt-detail'></i>
                Announcement Management
            </h1>
        </div>

        <?php if (isset($success)): ?>
            <div class="message success">
                <i class='bx bx-check-circle'></i>
                <span><?= $success ?></span>
            </div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="message error">
                <i class='bx bx-error-circle'></i>
                <span><?= $error ?></span>
            </div>
        <?php endif; ?>

        <!-- Step Indicator -->
        <div class="step-indicator">
            <div class="step active" id="step1-indicator">
                <div class="step-number">1</div>
                <div class="step-label">Basic Info</div>
            </div>
            <div class="step" id="step2-indicator">
                <div class="step-number">2</div>
                <div class="step-label">Details</div>
            </div>
            <div class="step" id="step3-indicator">
                <div class="step-number">3</div>
                <div class="step-label">Review</div>
            </div>
        </div>

        <!-- Multi-step Form -->
        <form id="announcementForm" method="POST" enctype="multipart/form-data" class="form-steps">
            <!-- Step 1: Basic Information -->
            <div class="form-step active" id="step1">
                <h2 class="step-title">
                    <i class='bx bx-info-circle'></i>
                    Basic Information
                </h2>
                
                <div class="form-group">
                    <label for="title">Announcement Title</label>
                    <input type="text" id="title" name="title" placeholder="Enter announcement title" required>
                </div>
                
                <div class="form-group">
                    <label for="announcement_type">Announcement Type</label>
                    <select id="announcement_type" name="announcement_type" required>
                        <?php foreach ($announcement_types as $value => [$label, $icon]): ?>
                            <option value="<?= $value ?>"><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-outline" disabled>
                        <i class='bx bx-chevron-left'></i> Previous
                    </button>
                    <button type="button" class="btn btn-primary" onclick="nextStep(2)">
                        Next <i class='bx bx-chevron-right'></i>
                    </button>
                </div>
            </div>

            <!-- Step 2: Details -->
            <div class="form-step" id="step2">
                <h2 class="step-title">
                    <i class='bx bx-detail'></i>
                    Announcement Details
                </h2>
                
                <div class="form-group">
                    <label for="description">Announcement Details</label>
                    <textarea id="description" name="description" placeholder="Enter announcement details" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="event_date">Event Date</label>
                    <input type="date" id="event_date" name="event_date" required>
                </div>
               <div class="form-group">
    <label>Target Audience</label>
    <div class="target-options" style="display: flex; gap: 1rem; margin-bottom: 1rem;">
        <label style="display: flex; align-items: center; gap: 0.5rem;">
            <input type="radio" name="target_type" value="all" checked> All Areas
        </label>
        <label style="display: flex; align-items: center; gap: 0.5rem;">
            <input type="radio" name="target_type" value="specific"> Specific Areas
        </label>
    </div>
    
    <div id="area-selection" style="display: none; margin-top: 1rem;">
        <select name="target_streets[]" multiple style="width: 100%; padding: 0.5rem; min-height: 150px;">
            <optgroup label="Sakina">
                <option value="Kisongo">Kisongo</option>
                <option value="Oljoro">Oljoro</option>
                <option value="Matevesi">Matevesi</option>
                <option value="Kiranyi">Kiranyi</option>
                <option value="Maivo">Maivo</option>
            </optgroup>
            <optgroup label="Meru">
                <option value="Usariver">Usariver</option>
                <option value="Lenguruki">Lenguruki</option>
                <option value="Nkoaranga">Nkoaranga</option>
            </optgroup>
            <optgroup label="Karatu">
                <option value="Mbulu">Mbulu</option>
                <option value="Kilimatembo">Kilimatembo</option>
                <option value="Qurus">Qurus</option>
                <option value="Olden">Olden</option>
                <option value="Karatu">Karatu</option>
            </optgroup>
            <optgroup label="Monduli">
                <option value="Mto wa Mbu">Mto wa Mbu</option>
                <option value="Selela">Selela</option>
            </optgroup>
        </select>
        <small style="color: #666; display: block; margin-top: 0.5rem;">
            Hold Ctrl (Windows) or Command (Mac) to select multiple areas
        </small>
    </div>
</div>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline" onclick="prevStep(1)">
                        <i class='bx bx-chevron-left'></i> Previous
                    </button>
                    <button type="button" class="btn btn-primary" onclick="nextStep(3)">
                        Next <i class='bx bx-chevron-right'></i>
                    </button>
                </div>
            </div>

            <!-- Step 3: Media & Review -->
            <div class="form-step" id="step3">
                <h2 class="step-title">
                    <i class='bx bx-image-add'></i>
                    Media & Review
                </h2>
                
                <div class="file-upload">
                    <input type="file" id="image" name="image" class="file-upload-input" accept="image/*">
                    <label for="image" class="file-upload-label">
                        <i class='bx bx-cloud-upload file-upload-icon'></i>
                        <span class="file-upload-text">
                            <strong>Click to upload</strong> or drag and drop<br>
                            (Max. 5MB, JPG, PNG, GIF)
                        </span>
                    </label>
                    <div class="file-preview" id="filePreview"></div>
                </div>
                
                <div class="review-section">
                    <h3 class="step-title">
                        <i class='bx bx-check-circle'></i>
                        Review Your Announcement
                    </h3>
                    
                    <div class="form-group">
                        <label>Title:</label>
                        <div id="review-title" class="review-value"></div>
                    </div>
                    
                    <div class="form-group">
                        <label>Type:</label>
                        <div id="review-type" class="review-value"></div>
                    </div>
                    
                    <div class="form-group">
                        <label>Description:</label>
                        <div id="review-description" class="review-value"></div>
                    </div>
                    
                    <div class="form-group">
                        <label>Event Date:</label>
                        <div id="review-date" class="review-value"></div>
                    </div>
                </div>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-outline" onclick="prevStep(2)">
                        <i class='bx bx-chevron-left'></i> Previous
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class='bx bx-send'></i> Publish Announcement
                    </button>
                </div>
            </div>
            <div class="button-container">
    <a href="admin_page.php" class="back-button">Back to Admin</a>
    <a href="view.php" class="view-button">View Announcements</a>
</div>

<style>
    .button-container {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }
    
    .back-button, .view-button {
        padding: 10px 15px;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
    }
    
    .back-button {
        background-color: #f0f0f0;
        color: #333;
        border: 1px solid #ccc;
    }
    
    .view-button {
        background-color: #4f46e5;
        color: white;
        border: 1px solid #4f46e5;
    }
    
    .back-button:hover {
        background-color: #e0e0e0;
    }
    
    .view-button:hover {
        background-color: #4338ca;
    }
    /* Popup Message Styles */
.popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0,0,0,0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.popup-overlay.active {
    opacity: 1;
    visibility: visible;
}

.popup-message {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    max-width: 400px;
    width: 90%;
    text-align: center;
    transform: translateY(20px);
    transition: transform 0.3s ease;
}

.popup-overlay.active .popup-message {
    transform: translateY(0);
}

.popup-message.success {
    border-left: 4px solid #10b981;
}

.popup-message.error {
    border-left: 4px solid #ef4444;
}

.popup-message h3 {
    margin-top: 0;
    color: #1e293b;
}

.popup-message p {
    margin-bottom: 20px;
    color: #64748b;
}

.popup-close {
    background-color: #4f46e5;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 500;
}
</style>
<div class="popup-overlay" id="popupOverlay">
    <div class="popup-message" id="popupMessage">
        <h3 id="popupTitle">Success</h3>
        <p id="popupText">Your announcement has been sent successfully!</p>
        <button class="popup-close" onclick="hidePopup()">OK</button>
    </div>
</div>
        </form>

        <!-- Recent Announcements -->
        <!-- <h2 class="step-title" style="margin-top: 3rem;">
            <i class='bx bx-list-ul'></i>
            Recent Announcements
        </h2>
        
        <div class="announcements-list">
            <?php if (empty($announcements)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class='bx bx-info-circle'></i>
                    </div>
                    <h3>No announcements yet</h3>
                    <p>Create your first announcement using the form above</p>
                </div>
            <?php else: ?>
                <?php foreach ($announcements as $announcement): ?>
    <div class="announcement-card">
        <div style="margin-top: 1rem; font-size: 0.875rem; color: #666;">
            <strong>Target:</strong> 
            <?php if ($announcement['target_type'] === 'all'): ?>
                All streets
            <?php else: ?>
                Specific streets:
                <ul style="margin: 0.25rem 0; padding-left: 1.25rem;">
                    <?php 
                    $streets = explode(',', $announcement['target_streets']);
                    foreach ($streets as $street): 
                    ?>
                        <li><?= htmlspecialchars(trim($street)) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <div class="announcement-header">
            <div class="announcement-title">
                <i class='bx <?= $announcement_types[$announcement['announcement_type']][1] ?? 'bx-news' ?>'></i>
                <?= htmlspecialchars($announcement['title']) ?>
                <span class="announcement-type">
                    <?= $announcement_types[$announcement['announcement_type']][0] ?? 'General Announcement' ?>
                </span>
            </div>
            <div class="announcement-date">
                <?= date('M j, Y', strtotime($announcement['event_date'])) ?>
            </div>
        </div>
        <div class="announcement-description">
            <?= nl2br(htmlspecialchars($announcement['description'])) ?>
        </div>
        <?php if ($announcement['image_path']): ?>
            <img src="<?= $announcement['image_path'] ?>" alt="Announcement Image" class="announcement-image">
        <?php endif; ?>
    </div>
<?php endforeach; ?>
    </ul>
<?php endif; ?>
</div> -->
   <script>
    // Form Step Navigation
    function goToStep(step) {
        // Hide all steps
        document.querySelectorAll('.form-step').forEach(formStep => {
            formStep.classList.remove('active');
        });
        
        // Show current step
        document.getElementById(`step${step}`).classList.add('active');
        
        // Update step indicators
        document.querySelectorAll('.step').forEach((stepEl, index) => {
            stepEl.classList.remove('active', 'completed');
            if (index < step - 1) stepEl.classList.add('completed');
            if (index === step - 1) stepEl.classList.add('active');
        });
    }
    
    function nextStep(step) {
        // Validate current step before proceeding
        if (!validateStep(step - 1)) return;
        
        // Update review section if going to final step
        if (step === 3) {
            updateReviewSection();
        }
        
        goToStep(step);
    }
    
    function prevStep(step) {
        goToStep(step);
    }
    
    // Form Validation
    function validateStep(step) {
        const inputs = document.getElementById(`step${step}`).querySelectorAll('[required]');
        
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.value) {
                input.style.borderColor = 'var(--error)';
                isValid = false;
            } else {
                input.style.borderColor = '';
            }
        });
        
        return isValid;
    }
    
    // Update Review Section
    function updateReviewSection() {
        document.getElementById('review-title').textContent = document.getElementById('title').value;
        
        const typeSelect = document.getElementById('announcement_type');
        document.getElementById('review-type').textContent = typeSelect.options[typeSelect.selectedIndex].text;
        
        document.getElementById('review-description').textContent = document.getElementById('description').value;
        document.getElementById('review-date').textContent = document.getElementById('event_date').value;
        
        // Add target audience review
        const targetType = document.querySelector('input[name="target_type"]:checked').value;
        const targetDisplay = targetType === 'all' 
            ? 'All areas' 
            : 'Specific areas: ' + Array.from(document.querySelectorAll('select[name="target_streets[]"] option:checked'))
                                     .map(opt => opt.value).join(', ');
        
        // Create or update target audience display
        let targetReview = document.getElementById('review-target');
        if (!targetReview) {
            targetReview = document.createElement('div');
            targetReview.id = 'review-target';
            document.querySelector('.review-section').appendChild(targetReview);
        }
        targetReview.innerHTML = `<label>Target Audience:</label><div class="review-value">${targetDisplay}</div>`;
    }
    
    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        // File Upload Preview
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('filePreview');
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });
        
        // Toggle area selection
        document.querySelectorAll('input[name="target_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('area-selection').style.display = 
                    this.value === 'specific' ? 'block' : 'none';
            });
            
            // Initialize visibility based on default selection
            if (radio.checked) {
                document.getElementById('area-selection').style.display = 
                    radio.value === 'specific' ? 'block' : 'none';
            }
        });
        
        // Trigger initial validation if needed
        if (document.querySelector('.form-step.active').id === 'step3') {
            updateReviewSection();
        }
    });
    // Show popup function
function showPopup(title, message, isSuccess) {
    const popup = document.getElementById('popupOverlay');
    const popupMessage = document.getElementById('popupMessage');
    
    document.getElementById('popupTitle').textContent = title;
    document.getElementById('popupText').textContent = message;
    
    // Set success or error styling
    popupMessage.className = 'popup-message ' + (isSuccess ? 'success' : 'error');
    
    // Show popup
    popup.classList.add('active');
    
    // Auto-hide after 3 seconds
    setTimeout(hidePopup, 3000);
}

// Hide popup function
function hidePopup() {
    document.getElementById('popupOverlay').classList.remove('active');
}

// Call this when you want to show the popup
// Example: showPopup('Success', 'Announcement sent successfully!', true);
</script>
</body>
</html>



