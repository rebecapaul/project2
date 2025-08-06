<?php
session_start();
require_once "config.php";

// Check if user is logged in and is a leader
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'leader') {
    header("Location: index.php?error=access_denied");
    exit();
}

// Fetch user list
$query = "SELECT id, name, email, street, age, gender, role FROM users";
$result = $conn->query($query);

// Count statistics
$statsQuery = "SELECT 
    COUNT(*) AS total,
    SUM(CASE WHEN role = 'citizen' THEN 1 ELSE 0 END) AS citizens,
    SUM(CASE WHEN age >= 18 THEN 1 ELSE 0 END) AS adults,
    SUM(CASE WHEN age < 18 THEN 1 ELSE 0 END) AS children,
    SUM(CASE WHEN gender = 'Me' THEN 1 ELSE 0 END) AS males,
    SUM(CASE WHEN gender = 'Ke' THEN 1 ELSE 0 END) AS females
FROM users";
$statsResult = $conn->query($statsQuery);
$stats = $statsResult->fetch_assoc();

// Total reports
$reportsQuery = "SELECT COUNT(*) AS total_reports FROM report";
$reportsResult = $conn->query($reportsQuery);
$totalReports = $reportsResult->fetch_assoc()['total_reports'];

// Total wards
$wardsQuery = "SELECT COUNT(DISTINCT street) AS total_wards FROM users";
$wardsResult = $conn->query($wardsQuery);
$totalWards = $wardsResult->fetch_assoc()['total_wards'];

// Upcoming events
$currentDate = date('Y-m-d');
$eventsQuery = "SELECT COUNT(*) AS upcoming_events FROM announcements WHERE event_date >= '$currentDate'";
$eventsResult = $conn->query($eventsQuery);
$upcomingEvents = $eventsResult->fetch_assoc()['upcoming_events'];

// Fetch short list of wards (first 5)
$wardsListQuery = "SELECT DISTINCT street FROM users ORDER BY street ASC LIMIT 5";
$wardsListResult = $conn->query($wardsListQuery);
?>

<!-- START OF HTML PART -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Leader Dashboard</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;300;400;600;700&display=swap" rel="stylesheet">
   <style>
    :root {
      --green: #27ae60;
      --black: #192a56;
      --light-color: #666;
      --box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.1);
    }

    * {
      font-family: 'Nunito', sans-serif;
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      text-decoration: none;
      outline: none;
      border: none;
      transition: all .2s linear;
    }

    html {
      font-size: 62.5%;
      overflow-x: hidden;
      scroll-behavior: smooth;
    }

    body {
      background: #f7f7f7;
      min-height: 100vh;
      display: flex;
    }

    /* Sidebar Styles */
    .side-menu {
      width: 250px;
      background: var(--black);
      color: white;
      height: 100vh;
      position: fixed;
      padding: 2rem 0;
      transition: all 0.3s;
      z-index: 1000;
    }

    .brand-name {
      padding: 0 2rem 2rem;
      font-size: 2.2rem;
      font-weight: 700;
      color: white;
      border-bottom: 1px solid rgba(255,255,255,0.1);
      margin-bottom: 2rem;
    }

    .side-menu ul {
      list-style: none;
    }

    .side-menu ul li {
      margin-bottom: 1rem;
    }

    .side-menu ul li a {
      display: block;
      padding: 1.2rem 2rem;
      color: white;
      font-size: 1.6rem;
      transition: all 0.3s;
    }

    .side-menu ul li a:hover {
      background: var(--green);
      padding-left: 2.5rem;
    }

    .side-menu ul li a i {
      margin-right: 1rem;
      font-size: 1.8rem;
    }

    /* Main Content Styles */
    .main-content {
      flex: 1;
      margin-left: 250px;
      transition: all 0.3s;
    }

    header {
      background: white;
      padding: 1.5rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: var(--box-shadow);
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .search-wrapper {
      position: relative;
      width: 300px;
    }

    .search-wrapper input {
      width: 100%;
      padding: 1rem 1.5rem 1rem 4rem;
      border-radius: 5rem;
      background: #f0f0f0;
      font-size: 1.4rem;
    }

    .search-wrapper .ti-search {
      position: absolute;
      left: 1.5rem;
      top: 50%;
      transform: translateY(-50%);
      color: var(--light-color);
    }

    .user-wrapper {
      display: flex;
      align-items: center;
    }

    .user-wrapper .logout-btn {
      background: var(--green);
      color: white;
      padding: 0.8rem 1.5rem;
      border-radius: 5rem;
      font-size: 1.4rem;
      margin-left: 2rem;
    }

    .user-wrapper .logout-btn:hover {
      background: #219653;
    }

    /* Dashboard Cards */
    .dash-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 2rem;
      padding: 2rem;
    }

    .card-single {
      background: white;
      border-radius: 1rem;
      box-shadow: var(--box-shadow);
      overflow: hidden;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card-single:hover {
      transform: translateY(-5px);
      box-shadow: 0 1rem 2rem rgba(0,0,0,.15);
    }

    .card-body {
      padding: 2rem;
      display: flex;
      align-items: center;
    }

    .card-body span {
      font-size: 3rem;
      color: var(--green);
      margin-right: 1.5rem;
    }

    .card-body div h5 {
      font-size: 1.4rem;
      color: var(--light-color);
      text-transform: uppercase;
      margin-bottom: 0.5rem;
    }

    .card-body div h4 {
      font-size: 2.4rem;
      color: var(--black);
    }

    .card-footer {
      background: #f9f9f9;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      font-size: 1.2rem;
      color: var(--light-color);
    }

    .card-footer span {
      display: flex;
      align-items: center;
    }

    .card-footer span strong {
      color: var(--black);
      margin-right: 0.5rem;
      font-size: 1.4rem;
    }

    .card-footer a {
      color: var(--green);
      font-weight: 600;
    }

    /* Recent Activity */
    .recent {
      padding: 0 2rem 2rem;
    }

    .activity-card {
      background: white;
      border-radius: 1rem;
      box-shadow: var(--box-shadow);
      padding: 2rem;
      margin-bottom: 2rem;
    }

    .activity-card h3 {
      font-size: 1.8rem;
      color: var(--black);
      margin-bottom: 2rem;
    }

    .table-responsive {
      overflow-x: auto;
      border-radius: 1rem;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    table th, table td {
      padding: 1.2rem 1.5rem;
      text-align: left;
      border-bottom: 1px solid #eee;
      font-size: 1.4rem;
    }

    table th {
      background: #f9f9f9;
      font-weight: 600;
      color: var(--black);
    }

    table tr:hover {
      background: #f5f5f5;
    }

    .badge {
      padding: 0.5rem 1rem;
      border-radius: 5rem;
      font-size: 1.2rem;
      font-weight: 600;
    }

    .badge.success {
      background: #d4edda;
      color: #155724;
    }

    .badge.warning {
      background: #fff3cd;
      color: #856404;
    }

    .btn {
      padding: 0.5rem 1rem;
      border-radius: 0.3rem;
      font-size: 1.2rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s;
    }

    .btn:hover {
      opacity: 0.9;
      transform: translateY(-1px);
    }

    .edit-btn {
      background: #3498db;
      color: white;
    }

    .delete-btn {
      background: #e74c3c;
      color: white;
    }

    /* Summary Section */
    .summary {
      background: white;
      border-radius: 1rem;
      box-shadow: var(--box-shadow);
      padding: 2rem;
    }

    .summary-single {
      display: flex;
      align-items: center;
      margin-bottom: 1.5rem;
    }

    .summary-single span {
      font-size: 2.5rem;
      color: var(--green);
      margin-right: 1.5rem;
    }

    .summary-single div h5 {
      font-size: 1.6rem;
      color: var(--black);
      margin-bottom: 0.3rem;
    }

    .summary-single div small {
      font-size: 1.2rem;
      color: var(--light-color);
    }

    .bday-card {
      background: linear-gradient(to right, var(--green), #2ecc71);
      border-radius: 1rem;
      padding: 2rem;
      color: white;
      margin-top: 2rem;
    }

    .bday-flex {
      display: flex;
      align-items: center;
      margin-bottom: 1.5rem;
    }

    .bday-img {
      width: 6rem;
      height: 6rem;
      border-radius: 50%;
      background: rgba(255,255,255,0.3);
      margin-right: 1.5rem;
    }

    .bday-info h5 {
      font-size: 1.6rem;
      margin-bottom: 0.3rem;
    }

    .bday-info small {
      font-size: 1.2rem;
      opacity: 0.8;
    }

    .text-center {
      text-align: center;
    }

    .text-center button {
      background: white;
      color: var(--green);
      border: none;
      padding: 0.8rem 2rem;
      border-radius: 5rem;
      font-size: 1.4rem;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      transition: all 0.2s;
    }

    .text-center button:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 10px rgba(0,0,0,0.1);
    }

    .text-center button span {
      margin-right: 0.5rem;
      font-size: 1.6rem;
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
      .side-menu {
        transform: translateX(-100%);
        width: 80%;
      }
      
      .side-menu.active {
        transform: translateX(0);
      }
      
      .main-content {
        margin-left: 0;
        width: 100%;
      }
      
      .dash-cards {
        grid-template-columns: 1fr;
      }
      
      .search-wrapper {
        width: 100%;
        margin-right: 1rem;
      }
      
      .brand-name span {
        display: none;
      }
      
      .hamburger {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        width: 24px;
        height: 18px;
        cursor: pointer;
      }
      
      .hamburger span {
        display: block;
        width: 100%;
        height: 2px;
        background: white;
        transition: all 0.3s;
      }
      
      .hamburger.active span:nth-child(1) {
        transform: rotate(45deg) translate(5px, 5px);
      }
      
      .hamburger.active span:nth-child(2) {
        opacity: 0;
      }
      
      .hamburger.active span:nth-child(3) {
        transform: rotate(-45deg) translate(5px, -5px);
      }
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="side-menu" id="sideMenu">
    <div class="brand-name">
      <div class="hamburger" id="hamburger">
        <span></span><span></span><span></span>
      </div>
      <span>Leader Panel</span>
    </div>
    <ul>
      <li><a href="wasifu.php"><i class='bx bx-user'></i> Profile</a></li>
      <li><a href="wards.php"><i class='bx bx-building-house'></i> Wards</a></li>
      <li><a href="admin_page.php" class="active"><i class='bx bx-grid-alt'></i> Dashboard</a></li>
      <li><a href="view_reports.php"><i class='bx bx-task'></i> Reports</a></li>
      <li><a href="leader_announcement.php"><i class='bx bx-message-alt-detail'></i> Announcements</a></li>
      <li><a href="view.php"><i class='bx bx-calendar-event'></i> Events</a></li>
      <li><a href="settings.php"><i class='bx bx-cog'></i> Settings</a></li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main-content" id="mainContent">
    <header>
      <div class="search-wrapper">
        <span class="ti-search"></span>
        <input type="search" placeholder="Search citizens...">
      </div>
      <div class="user-wrapper">
        <a href="logout.php" class="logout-btn">Logout</a>
      </div>
    </header>

    <main>
      <h2 class="dash-title">Community Overview</h2>

      <div class="dash-cards">
        <div class="card-single">
          <div class="card-body">
            <span class="ti-user"></span>
            <div>
              <h5>Total Citizens</h5>
              <h4><?= $stats['citizens'] ?></h4>
            </div>
          </div>
          <div class="card-footer">
            <span><strong><?= $stats['adults'] ?></strong> Adults</span>
            <span><strong><?= $stats['children'] ?></strong> Children</span>
          </div>
        </div>

        <div class="card-single">
          <div class="card-body">
            <span class="ti-id-badge"></span>
            <div>
              <h5>Gender Distribution</h5>
              <h4><?= $stats['total'] ?></h4>
            </div>
          </div>
          <div class="card-footer">
            <span><strong><?= $stats['males'] ?></strong> Male</span>
            <span><strong><?= $stats['females'] ?></strong> Female</span>
          </div>
        </div>

        <div class="card-single">
          <div class="card-body">
            <span class="ti-map-alt"></span>
            <div>
              <h5>Wards</h5>
              <h4><?= $totalWards ?></h4>
            </div>
          </div>
          <div class="card-footer"><a href="wards.php">Manage Wards</a></div>
        </div>

        <div class="card-single">
          <div class="card-body">
            <span class="ti-flag-alt"></span>
            <div>
              <h5>Total Reports</h5>
              <h4><?= $totalReports ?></h4>
            </div>
          </div>
          <div class="card-footer"><a href="view_reports.php">View all</a></div>
        </div>

        <div class="card-single">
          <div class="card-body">
            <span class="ti-calendar"></span>
            <div>
              <h5>Upcoming Events</h5>
              <h4><?= $upcomingEvents ?></h4>
            </div>
          </div>
          <div class="card-footer"><a href="leader_announcement.php">View all</a></div>
        </div>

        <!-- Profile Quick Access -->
        <div class="card-single">
          <div class="card-body">
            <span class="ti-user"></span>
            <div>
              <h5>Your Profile</h5>
              <h4>View & Update</h4>
            </div>
          </div>
          <div class="card-footer"><a href="wasifu.php">Go to Profile</a></div>
        </div>
      </div>

      <!-- Registered Citizens and Wards Overview -->
      <section class="recent">
        <div class="activity-grid">

          <!-- Citizens Table -->
          <div class="activity-card">
            <h3>Registered Citizens</h3>
            <div class="table-responsive">
              <?php if ($result && $result->num_rows > 0): ?>
                <table>
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Street</th>
                      <th>Age</th>
                      <th>Gender</th>
                      <th>Role</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php while($user = $result->fetch_assoc()): ?>
                      <tr>
                        <td><?= htmlspecialchars($user['name']); ?></td>
                        <td><?= htmlspecialchars($user['email']); ?></td>
                        <td><?= htmlspecialchars($user['street']); ?></td>
                        <td><?= htmlspecialchars($user['age']); ?></td>
                        <td><?= htmlspecialchars($user['gender']); ?></td>
                        <td>
                          <span class="badge <?= $user['role'] === 'leader' ? 'warning' : 'success' ?>">
                            <?= htmlspecialchars($user['role']); ?>
                          </span>
                        </td>
                        <td>
                          <a href="edit.php?id=<?= $user['id'] ?>" class="btn edit-btn">Edit</a>
                          <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn delete-btn" onclick="return confirm('Are you sure?');">Delete</a>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              <?php else: ?>
                <p>No users found.</p>
              <?php endif; ?>
            </div>
          </div>

          <!-- Wards Overview -->
          <div class="activity-card">
            <h3>Wards Overview</h3>
            <div class="table-responsive">
              <?php if ($wardsListResult && $wardsListResult->num_rows > 0): ?>
                <table>
                  <thead><tr><th>Ward Name</th></tr></thead>
                  <tbody>
                    <?php while ($ward = $wardsListResult->fetch_assoc()): ?>
                      <tr>
                        <td><?= htmlspecialchars($ward['street']); ?></td>
                      </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              <?php else: ?>
                <p>No wards found.</p>
              <?php endif; ?>
              <div class="text-center" style="margin-top: 1rem;">
                <a href="wards.php">
                  <button><span class="ti-map-alt"></span> Manage Wards</button>
                </a>
              </div>
            </div>
          </div>

        </div>
      </section>
    </main>
  </div>

  <!-- Your existing JavaScript toggler -->
  <script>
    const hamburger = document.getElementById('hamburger');
    const sideMenu = document.getElementById('sideMenu');
    const mainContent = document.getElementById('mainContent');

    hamburger.addEventListener('click', () => {
      hamburger.classList.toggle('active');
      sideMenu.classList.toggle('active');
      mainContent.classList.toggle('active');
    });

    document.addEventListener('click', (e) => {
      if (window.innerWidth <= 768) {
        if (!sideMenu.contains(e.target) && e.target !== hamburger) {
          hamburger.classList.remove('active');
          sideMenu.classList.remove('active');
          mainContent.classList.remove('active');
        }
      }
    });

    sideMenu.addEventListener('click', (e) => {
      e.stopPropagation();
    });

    function handleResize() {
      if (window.innerWidth > 768) {
        hamburger.classList.remove('active');
        sideMenu.classList.remove('active');
        mainContent.classList.remove('active');
      }
    }

    window.addEventListener('resize', handleResize);
  </script>
</body>
</html>
