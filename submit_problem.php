<?php
include 'config.php'; // include your existing DB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['citizen_name'];
    $email = $_POST['citizen_email'];
    $description = $_POST['problem_description'];

    // Simple insert query
    $sql = "INSERT INTO report (name, email, description)
            VALUES ('$name', '$email', '$description')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
            alert('Your problem has been reported successfully.');
            window.location.href = 'user_page.php'; // redirect back
        </script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
