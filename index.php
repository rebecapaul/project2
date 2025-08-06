<?php
session_start();
// Clear errors after displaying them
$login_error = $_SESSION['login_error'] ?? '';
$register_error = $_SESSION['register_error'] ?? '';
$success_message = $_SESSION['success_message'] ?? '';
$active_form = $_SESSION['active_form'] ?? 'login';

// Clear session variables to prevent reappearing on refresh
unset($_SESSION['login_error']);
unset($_SESSION['register_error']);
unset($_SESSION['success_message']);
?>

<!DOCTYPE html>
<html lang="sw">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>MFUMO WA JAMII</title>
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

    .step-indicator {
      display: flex;
      justify-content: space-between;
      margin-bottom: 30px;
      position: relative;
    }

    .step-indicator::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 0;
      right: 0;
      height: 2px;
      background: var(--border);
      z-index: 1;
    }

    .step {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background: var(--border);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--text-light);
      font-weight: bold;
      position: relative;
      z-index: 2;
    }

    .step.active {
      background: var(--primary);
      color: var(--white);
    }

    .step.completed {
      background: var(--success);
      color: var(--white);
    }

    .form-step {
      display: none;
    }

    .form-step.active {
      display: block;
      animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
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

    button {
      padding: 12px 25px;
      border-radius: 8px;
      border: none;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
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

    .login-form {
      display: <?= $active_form === 'login' ? 'block' : 'none' ?>;
    }

    .register-form {
      display: <?= $active_form === 'register' ? 'block' : 'none' ?>;
    }

    .toggle-text {
      text-align: center;
      margin-top: 20px;
      color: var(--text-light);
      font-size: 0.9rem;
    }

    .toggle-link {
      color: var(--primary);
      text-decoration: none;
      font-weight: 500;
    }

    .toggle-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="container">
    <!-- Login Form -->
    <div class="form-box login-form">
      <form action="action.php" method="post">
        <h2>Ingia</h2>
        
        <?php if (!empty($login_error)): ?>
          <div class="message error-message"><?= $login_error ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success_message)): ?>
          <div class="message success-message"><?= $success_message ?></div>
        <?php endif; ?>
        
        <div class="form-group">
          <input type="text" name="email" placeholder="Barua Pepe au Namba ya Simu" required>
        </div>
        
        <div class="form-group">
          <input type="password" name="password" placeholder="Nenosiri" required>
        </div>
        
        <button type="submit" name="login" class="btn-primary" style="width:100%">Ingia</button>
        
        <p class="toggle-text">Huna akaunti? <a href="#" class="toggle-link" onclick="showRegister()">Jisajili</a></p>
      </form>
    </div>

    <!-- Registration Form (3 Steps) -->
    <div class="form-box register-form">
      <form id="registrationForm" action="action.php" method="post" enctype="multipart/form-data">
        <h2>Jisajili</h2>
        
        <?php if (!empty($register_error)): ?>
          <div class="message error-message"><?= $register_error ?></div>
        <?php endif; ?>
        
        <div class="step-indicator">
          <div class="step active" id="step1">1</div>
          <div class="step" id="step2">2</div>
          <div class="step" id="step3">3</div>
        </div>

        <!-- Step 1: Personal Info -->
        <div class="form-step active" id="step1-form">
          <div class="form-group">
            <input type="text" name="name" placeholder="Jina Kamili" required>
          </div>
          
          <div class="form-group">
            <input type="email" name="email" placeholder="Barua Pepe" required>
          </div>
          
          <div class="form-group">
            <input type="password" name="password" placeholder="Nenosiri" required>
          </div>
          
          <div class="btn-group">
            <button type="button" class="btn-outline" onclick="showLogin()">Rudi</button>
            <button type="button" class="btn-primary" onclick="nextStep(2)">Endelea</button>
          </div>
        </div>

        <!-- Step 2: Additional Info -->
        <div class="form-step" id="step2-form">
          <div class="form-group">
            <input type="number" name="age" placeholder="Umri" min="1" max="120" required>
          </div>
          
          <div class="form-group">
            <input type="text" name="phone" placeholder="Namba ya Simu" required>
          </div>
          
          <div class="form-group">
            <select name="gender" required>
              <option value="">-- Chagua Jinsia --</option>
              <option value="Me">Me</option>
              <option value="Ke">Ke</option>
            </select>
          </div>
          
          <div class="btn-group">
            <button type="button" class="btn-outline" onclick="prevStep(1)">Rudi</button>
            <button type="button" class="btn-primary" onclick="nextStep(3)">Endelea</button>
          </div>
        </div>

        <!-- Step 3: Location & Profile -->
        <div class="form-step" id="step3-form">
          <div class="form-group">
            <select name="ward" id="ward" required onchange="updateStreets()">
              <option value="">-- Chagua Wilaya --</option>
              <option value="sakina">Sakina</option>
              <option value="meru">Meru</option>
              <option value="karatu">Karatu</option>
              <option value="monduli">Monduli</option>
            </select>
          </div>
          
          <div class="form-group">
            <select name="street" id="street" required>
              <option value="">-- Chagua Mtaa --</option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="profile_image">Picha ya Profaili</label>
            <input type="file" name="profile_image" id="profile_image" accept="image/*" required>
          </div>
          
          <div class="btn-group">
            <button type="button" class="btn-outline" onclick="prevStep(2)">Rudi</button>
            <button type="submit" name="register" class="btn-primary">Maliza Usajili</button>
          </div>
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

    function showLogin() {
      document.querySelector('.login-form').style.display = 'block';
      document.querySelector('.register-form').style.display = 'none';
    }

    function showRegister() {
      document.querySelector('.login-form').style.display = 'none';
      document.querySelector('.register-form').style.display = 'block';
      resetRegistrationForm();
    }

    function resetRegistrationForm() {
      // Reset to step 1
      document.querySelectorAll('.form-step').forEach(step => {
        step.classList.remove('active');
      });
      document.getElementById('step1-form').classList.add('active');
      
      // Reset step indicators
      document.querySelectorAll('.step').forEach((step, index) => {
        step.classList.remove('active', 'completed');
        if (index === 0) step.classList.add('active');
      });
    }

    function nextStep(stepNumber) {
      // Validate current step before proceeding
      if (!validateStep(stepNumber - 1)) return;

      // Hide all steps
      document.querySelectorAll('.form-step').forEach(step => {
        step.classList.remove('active');
      });
      
      // Show next step
      document.getElementById(`step${stepNumber}-form`).classList.add('active');
      
      // Update step indicators
      document.querySelectorAll('.step').forEach((step, index) => {
        step.classList.remove('active', 'completed');
        if (index < stepNumber) step.classList.add('completed');
        if (index === stepNumber - 1) step.classList.add('active');
      });
    }

    function prevStep(stepNumber) {
      // Hide all steps
      document.querySelectorAll('.form-step').forEach(step => {
        step.classList.remove('active');
      });
      
      // Show previous step
      document.getElementById(`step${stepNumber}-form`).classList.add('active');
      
      // Update step indicators
      document.querySelectorAll('.step').forEach((step, index) => {
        step.classList.remove('active', 'completed');
        if (index < stepNumber) step.classList.add('completed');
        if (index === stepNumber - 1) step.classList.add('active');
      });
    }

    function validateStep(stepNumber) {
      const form = document.getElementById('registrationForm');
      const inputs = document.getElementById(`step${stepNumber}-form`).querySelectorAll('[required]');
      
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
  </script>

</body>
</html>