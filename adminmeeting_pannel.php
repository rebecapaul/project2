<?php
include("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $kichwa = $_POST['kichwa'];
  $maelezo = $_POST['maelezo'];
  $tarehe = $_POST['tarehe'];
  $muda = $_POST['muda'];
  $mahali = $_POST['mahali'];

  $sql = "INSERT INTO mikutano (kichwa, maelezo, tarehe, muda, mahali)
          VALUES ('$kichwa', '$maelezo', '$tarehe', '$muda', '$mahali')";

  if ($conn->query($sql) === TRUE) {
    header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
    exit;
  } else {
    $error = $conn->error;
  }
}
?>

<!DOCTYPE html>
<html lang="sw">
<head>
  <meta charset="UTF-8">
  <title>Ingiza Mkutano</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f9f9f9;
    }

    form {
      width: 400px;
      margin: 50px auto;
      background: #fff;
      padding: 25px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      border-radius: 10px;
    }

    h2 {
      text-align: center;
      color: #2c3e50;
    }

    label {
      display: block;
      margin-top: 10px;
      font-weight: bold;
    }

    input, textarea {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    button {
      margin-top: 15px;
      background-color: #2c3e50;
      color: white;
      padding: 10px 15px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    button:hover {
      background-color: #1abc9c;
    }

    /* Popup (toast style) */
    .popup {
      position: fixed;
      top: 20px;
      right: 20px;
      background-color: #2ecc71;
      color: white;
      padding: 15px 25px;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
      font-weight: bold;
      opacity: 0;
      transform: translateY(-20px);
      transition: all 0.5s ease-in-out;
      z-index: 9999;
    }

    .popup.show {
      opacity: 1;
      transform: translateY(0);
    }
  </style>
</head>
<body>

<?php if (isset($_GET['success'])): ?>
  <div class="popup" id="popupSuccess">✅ Mkutano umehifadhiwa kwa mafanikio!</div>
  <script>
    const popup = document.getElementById("popupSuccess");
    popup.classList.add("show");
    setTimeout(() => {
      popup.classList.remove("show");
    }, 4000); // popup disappears after 4 seconds
  </script>
<?php endif; ?>

<?php if (isset($error)): ?>
  <div class="popup" style="background-color:#e74c3c;" id="popupError">❌ <?= $error ?></div>
  <script>
    const popupError = document.getElementById("popupError");
    popupError.classList.add("show");
    setTimeout(() => {
      popupError.classList.remove("show");
    }, 5000);
  </script>
<?php endif; ?>

<form method="POST" action="">
  <h2>Ingiza Taarifa za Mkutano</h2>

  <label for="kichwa">Kichwa cha Mkutano:</label>
  <input type="text" id="kichwa" name="kichwa" required>

  <label for="maelezo">Maelezo:</label>
  <textarea id="maelezo" name="maelezo" rows="4" required></textarea>

  <label for="tarehe">Tarehe:</label>
  <input type="date" id="tarehe" name="tarehe" required>

  <label for="muda">Muda:</label>
  <input type="time" id="muda" name="muda" required>

  <label for="mahali">Mahali:</label>
  <input type="text" id="mahali" name="mahali" required>

  <button type="submit">Hifadhi Mkutano</button>
</form>

</body>
</html>
