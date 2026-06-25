<?php
session_start();
include("connect.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'super_admin'){
    header("Location: home.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg-primary: #f4f6f9;
            --sidebar-bg: #1e1e2f;
            --sidebar-hover: #2b2b40;
            --text-light: #ffffff;
            --text-dark: #333333;
            --text-muted: #6c757d;
            --accent-blue: #3b82f6;
            --accent-purple: #8b5cf6;
            --accent-green: #10b981;
            --card-bg: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            display: flex;
            background-color: var(--bg-primary);
            color: var(--text-dark);
            min-height: 100vh;
        }

        /* --- SIDEBAR NAVIGATION --- */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            color: var(--text-light);
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            transition: all 0.3s ease;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
            z-index: 100;
        }

        .sidebar-brand {
            padding: 24px;
            font-size: 1.25rem;
            font-weight: bold;
            letter-spacing: 0.5px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-menu {
            list-style: none;
            padding: 20px 0;
            display: flex;
            flex-direction: column;
            gap: 5px;
            flex-grow: 1;
        }

        .sidebar-menu li {
            width: 100%;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 14px 24px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
        }

        .sidebar-menu a:hover, .sidebar-menu a.active {
            color: var(--text-light);
            background-color: var(--sidebar-hover);
            border-left-color: var(--accent-blue);
        }

        .sidebar-menu a.logout-btn {
            margin-top: auto;
            border-left-color: #ef4444;
        }
        
        .sidebar-menu a.logout-btn:hover {
            background-color: #3b1e22;
            color: #f87171;
        }

        /* --- MAIN CONTENT AREA --- */
        .main-content {
            margin-left: 260px;
            flex-grow: 1;
            padding: 40px;
            width: calc(100% - 260px);
        }

        /* Top Header Area */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
            background: var(--card-bg);
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }

        .header h1 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #111827;
        }

        .user-welcome {
            font-weight: 600;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .user-welcome span {
            color: var(--accent-blue);
            font-weight: 700;
        }

        /* --- DASHBOARD GRID --- */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }

        /* Interactive Dashboard Cards */
        .card {
            background-color: var(--card-bg);
            padding: 25px;
            border-radius: 14px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.06);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0,0,0,0.03);
            cursor: pointer;
        }

        /* Dynamic elevation on hover */
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
        }

        /* Top accent border bar line */
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
        }

        .card.employees::before { background-color: var(--accent-blue); }
        .card.admins::before { background-color: var(--accent-purple); }
        .card.reports::before { background-color: var(--accent-green); }

        .card-header-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .card h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
        }

        .card p {
            font-size: 0.95rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 25px;
        }

        /* Colored Icons Container Boxes */
        .card-icon {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .card.employees .card-icon { background-color: rgba(59, 130, 246, 0.1); color: var(--accent-blue); }
        .card.admins .card-icon { background-color: rgba(139, 92, 246, 0.1); color: var(--accent-purple); }
        .card.reports .card-icon { background-color: rgba(16, 185, 129, 0.1); color: var(--accent-green); }

        /* Animated Action Text links */
        .card-action-text {
            font-size: 0.88rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: gap 0.2s ease;
        }

        .card.employees .card-action-text { color: var(--accent-blue); }
        .card.admins .card-action-text { color: var(--accent-purple); }
        .card.reports .card-action-text { color: var(--accent-green); }

        .card:hover .card-action-text {
            gap: 12px; /* Smooth arrow push animation */
        }

        /* --- MOBILE VIEW RESPONSIVENESS --- */
        @media (max-width: 768px) {
            .sidebar { width: 70px; }
            .sidebar-brand span, .sidebar-menu span { display: none; }
            .main-content { margin-left: 70px; padding: 20px; width: calc(100% - 70px); }
            .sidebar-menu a { justify-content: center; padding: 18px 0; }
        }
    </style>
</head>
<body>

    <nav class="sidebar">
        <div class="sidebar-brand">
            <i class="fa-solid fa-shield-halved"></i>
            <span>Control Panel</span>
        </div>

        <ul class="sidebar-menu">
            <li>
                <a href="super_admin_dashboard.php" class="active">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="employees.php">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Add Employee</span>
                </a>
            </li>
            <li>
                <a href="view_employees.php">
                    <i class="fa-solid fa-users"></i>
                    <span>View Employees</span>
                </a>
            </li>
            <li>
                <a href="manage_admins.php">
                    <i class="fa-solid fa-user-gear"></i>
                    <span>Manage Admins</span>
                </a>
            </li>
            <li>
                <a href="home.php" style="border-left-color: var(--accent-green);">
                    <i class="fa-solid fa-house"></i>
                    <span>View Home Page</span>
                </a>
            </li>
            <li>
                <a href="logout.php" class="logout-btn">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>

    <main class="main-content">

        <header class="header">
            <h1>Super Admin Dashboard</h1>
            <div class="user-welcome">
                <i class="fa-regular fa-circle-user"></i>
                Welcome, <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </div>
        </header>

        <section class="dashboard-grid">

            <div class="card employees" onclick="window.location.href='view_employees.php';">
                <div>
                    <div class="card-header-box">
                        <h3>Total Employees</h3>
                        <div class="card-icon">
                            <i class="fa-solid fa-users-gear"></i>
                        </div>
                    </div>
                    <p>Manage, monitor, and update active system employee records easily.</p>
                </div>
                <div class="card-action-text">
                    Manage Records <i class="fa-solid fa-arrow-right"></i>
                </div>
            </div>

            <div class="card admins" onclick="window.location.href='manage_admins.php';">
                <div>
                    <div class="card-header-box">
                        <h3>System Admins</h3>
                        <div class="card-icon">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>
                    </div>
                    <p>Control full administrative credentials, system roles, and platform permissions.</p>
                </div>
                <div class="card-action-text">
                    Manage Admins <i class="fa-solid fa-arrow-right"></i>
                </div>
            </div>

            <div class="card reports" onclick="window.location.href='reports.php';">
                <div>
                    <div class="card-header-box">
                        <h3>Reports</h3>
                        <div class="card-icon">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                    </div>
                    <p>Generate analytical summaries, inspect logs, and view operational data.</p>
                </div>
                <div class="card-action-text">
                    View Reports <i class="fa-solid fa-arrow-right"></i>
                </div>
            </div>

        </section>

    </main>

</body>
</html>