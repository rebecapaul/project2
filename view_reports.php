<?php
include 'config.php'; // Database connection

$sql = "SELECT * FROM report ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>View Reports</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<style>
  body {
    font-family: 'Poppins', sans-serif;
    background: #e9efff;
    padding: 40px 20px;
    margin: 0;
  }
  .container {
    max-width: 1200px;
    margin: auto;
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
  }
  h1 {
    text-align: center;
    margin-bottom: 30px;
    color: #333;
    font-size: 30px;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
  }
  th, td {
    padding: 16px 14px;
    border-bottom: 1px solid #ddd;
    text-align: left;
    vertical-align: top;
  }
  th {
    background-color: #7494ec;
    color: white;
    font-weight: 600;
  }
  tr:hover {
    background-color: #f9f9f9;
  }
  /* Description styling */
  .desc-cell {
    max-width: 450px;
    font-size: 14px;
    color: #444;
    line-height: 1.5;
  }
  .desc-short {
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    cursor: default;
  }
  .desc-full {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s ease;
    margin-top: 5px;
    white-space: normal;
  }
  .desc-full.open {
    max-height: 500px; /* large enough for most descriptions */
  }
  .read-more {
    display: inline-block;
    margin-top: 6px;
    color: #7494ec;
    font-weight: 600;
    cursor: pointer;
    user-select: none;
    font-size: 13px;
  }
  .back-btn {
    display: inline-block;
    margin-bottom: 20px;
    padding: 10px 20px;
    background: #7494ec;
    color: #fff;
    text-decoration: none;
    border-radius: 5px;
    font-weight: 600;
    transition: background 0.3s;
  }
  .back-btn:hover {
    background: #5a79d1;
  }
  @media screen and (max-width: 768px) {
    table, thead, tbody, th, td, tr {
      display: block;
    }
    thead {
      display: none;
    }
    tr {
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 10px;
      padding: 15px;
      background: #fdfdfd;
    }
    td {
      padding: 10px;
      text-align: right;
      position: relative;
    }
    td::before {
      content: attr(data-label);
      position: absolute;
      left: 15px;
      text-align: left;
      font-weight: 600;
      color: #333;
    }
  }
</style>
</head>
<body>

<div class="container">
  <a href="admin_page.php" class="back-btn">&#8592; Back to Dashboard</a>
  <h1>All Reported Problems</h1>

  <?php if (mysqli_num_rows($result) > 0): ?>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Citizen Name</th>
        <th>Email</th>
        <th>Description</th>
        <th>Date Reported</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = mysqli_fetch_assoc($result)):
        $desc = htmlspecialchars($row['description']);
        $shortDesc = strlen($desc) > 150 ? substr($desc, 0, 150) : $desc;
        $needsToggle = strlen($desc) > 150;
      ?>
      <tr>
        <td data-label="ID"><?= $row['id']; ?></td>
        <td data-label="Citizen Name"><?= htmlspecialchars($row['name']); ?></td>
        <td data-label="Email"><?= htmlspecialchars($row['email']); ?></td>
        <td data-label="Description" class="desc-cell">
          <span class="desc-short"><?= nl2br($shortDesc) ?><?= $needsToggle ? '...' : '' ?></span>
          <?php if($needsToggle): ?>
            <div class="desc-full"><?= nl2br(substr($desc, 150)) ?></div>
            <span class="read-more">Read more</span>
          <?php endif; ?>
        </td>
        <td data-label="Date Reported"><?= $row['created_at'] ?? 'N/A'; ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  <?php else: ?>
    <p style="text-align:center; font-size: 18px;">No reports found.</p>
  <?php endif; ?>
</div>

<script>
  document.querySelectorAll('.read-more').forEach(function(button){
    button.addEventListener('click', function() {
      const descFull = this.previousElementSibling;
      if (descFull.classList.contains('open')) {
        descFull.classList.remove('open');
        this.textContent = 'Read more';
      } else {
        descFull.classList.add('open');
        this.textContent = 'Show less';
      }
    });
  });
</script>

</body>
</html>
