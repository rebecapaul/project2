<?php
// sidebar.php
?>
<!-- Boxicons CSS for icons -->
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />

<style>
  :root {
    --color-primary: #1abc9c;
    --color-dark: #2c3e50;
    --color-light: #ecf0f1;
    --color-hover: #16a085;
    --transition-speed: 0.3s;
    --sidebar-width: 220px;
  }

  /* Reset */
  * {
    box-sizing: border-box;
  }

  body, ul {
    margin: 0;
    padding: 0;
  }

  ul {
    list-style: none;
  }

  a {
    text-decoration: none;
    color: inherit;
  }

  .side-menu {
    position: fixed;
    top: 0; left: 0;
    height: 100vh;
    width: var(--sidebar-width);
    background: var(--color-dark);
    color: var(--color-light);
    padding: 1.5rem 1rem;
    box-shadow: 2px 0 8px rgba(0,0,0,0.15);
    display: flex;
    flex-direction: column;
    transition: transform var(--transition-speed) ease;
    z-index: 1000;
  }

  .side-menu.closed {
    transform: translateX(calc(-1 * var(--sidebar-width)));
  }

  /* Brand */
  .brand-name {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 2rem;
    text-align: center;
    letter-spacing: 1px;
    user-select: none;
  }

  /* Hamburger */
  .hamburger {
    position: fixed;
    top: 15px;
    left: 15px;
    width: 28px;
    height: 22px;
    display: none;
    flex-direction: column;
    justify-content: space-between;
    cursor: pointer;
    z-index: 1100;
  }

  .hamburger span {
    height: 3px;
    width: 100%;
    background: var(--color-dark);
    border-radius: 2px;
    transition: all 0.3s ease;
  }

  /* Hamburger active state */
  .hamburger.active span:nth-child(1) {
    transform: rotate(45deg) translate(5px, 5px);
  }
  .hamburger.active span:nth-child(2) {
    opacity: 0;
  }
  .hamburger.active span:nth-child(3) {
    transform: rotate(-45deg) translate(5px, -5px);
  }

  /* Menu items */
  .side-menu ul li {
    margin-bottom: 1.25rem;
  }

  .side-menu ul li a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 0.6rem 1rem;
    font-size: 1.3rem;
    border-radius: 6px;
    transition: background var(--transition-speed);
    color: var(--color-light);
    font-weight: 500;
  }

  .side-menu ul li a i {
    font-size: 1.5rem;
    min-width: 24px;
    text-align: center;
  }

  .side-menu ul li a:hover {
    background: var(--color-primary);
    color: white;
  }

  /* Responsive - Mobile */
  @media (max-width: 768px) {
    .side-menu {
      transform: translateX(calc(-1 * var(--sidebar-width)));
      padding-top: 4.5rem;
      width: var(--sidebar-width);
    }

    .side-menu.open {
      transform: translateX(0);
    }

    .hamburger {
      display: flex;
      background: var(--color-light);
      padding: 4px;
      border-radius: 4px;
    }
  }
</style>

<div class="side-menu closed" id="sideMenu">
  <div class="brand-name">Leader Panel</div>
  <ul>
    <li><a href="admin_page.php"><i class='bx bx-grid-alt'></i> Dashboard</a></li>
    <li><a href="leader_profile.php"><i class='bx bx-user'></i> Profile</a></li>
    <li><a href="wards.php"><i class='bx bx-building-house'></i> Wards</a></li>
    <li><a href="view_reports.php"><i class='bx bx-task'></i> Reports</a></li>
    <li><a href="leader_announcement.php"><i class='bx bx-message-alt-detail'></i> Announcements</a></li>
    <li><a href="view.php"><i class='bx bx-calendar-event'></i> Events</a></li>
    <li><a href="settings.php"><i class='bx bx-cog'></i> Settings</a></li>
  </ul>
</div>

<div class="hamburger" id="hamburgerBtn" aria-label="Toggle sidebar" role="button" tabindex="0">
  <span></span>
  <span></span>
  <span></span>
</div>

<script>
  const sideMenu = document.getElementById('sideMenu');
  const hamburger = document.getElementById('hamburgerBtn');

  function toggleSidebar() {
    sideMenu.classList.toggle('open');
    sideMenu.classList.toggle('closed');
    hamburger.classList.toggle('active');
  }

  hamburger.addEventListener('click', toggleSidebar);

  // Optional: close sidebar when clicking outside on mobile
  document.addEventListener('click', (e) => {
    if (!sideMenu.contains(e.target) && !hamburger.contains(e.target) && sideMenu.classList.contains('open')) {
      toggleSidebar();
    }
  });

  // Keyboard accessibility
  hamburger.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      toggleSidebar();
    }
  });
</script>
