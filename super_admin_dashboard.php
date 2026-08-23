<?php
session_start();
include("connect.php");
$chart_query = mysqli_query($conn,"
SELECT department,
COUNT(*) AS total
FROM details
GROUP BY department
");

$departments = [];
$totals = [];

while($row = mysqli_fetch_assoc($chart_query)){
    $departments[] = $row['department'];
    $totals[] = $row['total'];
}

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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

            /* Footer Colors */
            --primary: #0076fe;
            --dark-bg: #22252a;          /* Charcoal main bar */
            --copyright-bg: #191b1f;      /* Darker bottom strip */
            --text-main: #ffffff;
            --text-footer-muted: #94a3b8;
            --dark-border: rgba(255, 255, 255, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        html, body {
            min-height: 100vh;
            width: 100%;
            background-color: var(--bg-primary);
            color: var(--text-dark);
        }

        body {
            display: flex;
        }

        /* --- SIDEBAR NAVIGATION --- */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            color: var(--text-light);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            height: 100vh;
            transition: all 0.3s ease;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
            z-index: 100;
            overflow-y: auto;
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

        .sidebar-menu a:hover, 
        .sidebar-menu a.active {
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

        .sidebar-chart-box {
            background: white;
            padding: 15px;
            margin: 15px;
            border-radius: 12px;
            color: #333;
        }

        .sidebar-chart-box h2 {
            font-size: 0.9rem;
            margin-bottom: 10px;
            text-align: center;
            color: #1e1e2f;
        }

        /* --- MAIN CONTENT AREA --- */
        .main-content {
            margin-left: 260px;
            width: calc(100% - 260px);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 40px 40px 0 40px;
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
            margin-bottom: 40px;
        }

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

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
        }

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
            gap: 12px;
        }

        /* --- FOOTER RULES FRAME SECTION --- */
        .footer-rules {
            margin-top: 20px;
            margin-bottom: 40px;
            padding: 30px;
            background: #ffffff;
            border-radius: 16px;
            border: 2px solid #e5e7eb;
            border-left: 6px solid #0076fe;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .footer-rules h3 {
            color: #0076fe;
            margin-bottom: 20px;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .footer-rules ol {
            padding-left: 25px;
        }

        .footer-rules li {
            margin-bottom: 10px;
            line-height: 1.6;
            color: #374151;
            font-size: 14.5px;
        }

        /* --- FULL WIDTH FOOTER AT BOTTOM FROM RIGHT TO LEFT --- */
        .system-footer {
            margin-left: -40px;
            margin-right: -40px;
            margin-top: auto;
            background-color: var(--dark-bg);
            display: flex;
            flex-direction: column;
            width: calc(100% + 80px);
        }

        .footer-main-row {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 25px 20px;
            gap: 30px;
            width: 100%;
        }

        .footer-logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .footer-logo img {
            height: 45px;
            width: auto;
            object-fit: contain;
        }

        .footer-logo h3 {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .footer-divider {
            width: 1px;
            height: 35px;
            background-color: var(--dark-border);
        }

        .social-links {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .social-links a {
            color: var(--text-main);
            font-size: 1.2rem;
            text-decoration: none;
            transition: color 0.2s ease, transform 0.2s ease;
        }

        .social-links a:hover {
            color: var(--primary);
            transform: translateY(-2px);
        }

        .copyright {
            background-color: var(--copyright-bg);
            text-align: center;
            padding: 14px 20px;
            font-size: 0.85rem;
            color: var(--text-footer-muted);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* --- MOBILE RESPONSIVENESS --- */
        @media (max-width: 768px) {
            .sidebar { 
                width: 70px; 
            }
            .sidebar-brand span, 
            .sidebar-menu span,
            .sidebar-chart-box { 
                display: none; 
            }
            .main-content { 
                margin-left: 70px; 
                padding: 20px 20px 0 20px; 
                width: calc(100% - 70px); 
            }
            .system-footer {
                margin-left: -20px;
                margin-right: -20px;
                width: calc(100% + 40px);
            }
            .footer-main-row {
                flex-direction: column;
                gap: 15px;
            }
            .footer-divider {
                width: 80px;
                height: 1px;
            }
        }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
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
                <a href="leave_request.php">
                    <i class="fa-solid fa-envelope-open-text"></i>
                    <span>Leave Request</span>
                </a>
            </li>
            <li>
                <a href="manage_payments.php">
                    <i class="fa-solid fa-money-check-dollar"></i>
                    <span>Manage Payments</span>
                </a>
            </li>
            <li>
                <a href="messages.php">
                    <i class="fa-solid fa-comments"></i>
                    <span>Messages</span>
                </a>
            </li>
            <li>
                <a href="manage_events.php">
                    <i class="fa-solid fa-calendar-gear"></i>
                    <span>Manage Events</span>
                </a>
            </li>
            <li>
                <a href="manage_announcements.php">
    <i class="fa-solid fa-bullhorn"></i>
    <span>Manage Announcements</span>
</a>
                <a href="logout.php" class="logout-btn">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-chart-box">
            <h2>Employees by Department</h2>
            <canvas id="departmentChart"></canvas>
        </div>
    </nav>

    <!-- MAIN CONTENT AREA -->
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

        <!-- FRAMED RULES & GUIDELINES -->
        <div class="footer-rules">
            <h3>
                <i class="fa-solid fa-shield-halved"></i>
                Employee Management System Rules & Guidelines
            </h3>
            <ol>
                <li>Keep login credentials secure and never share passwords with others.</li>
                <li>Ensure all employee information entered into the system is accurate.</li>
                <li>Record attendance daily and verify entries before submission.</li>
                <li>Access only the modules and features assigned to your role.</li>
                <li>Do not attempt to access restricted administrative functions.</li>
                <li>Protect confidential employee and organizational information.</li>
                <li>Update personal and contact information whenever changes occur.</li>
                <li>Use the system strictly for official organizational activities.</li>
                <li>Report system errors, bugs, or suspicious activities immediately.</li>
                <li>Maintain professionalism when using the system.</li>
                <li>Do not alter or delete records without proper authorization.</li>
                <li>Follow all company policies and procedures while using the system.</li>
                <li>Change passwords regularly to improve account security.</li>
                <li>Respect the integrity and accuracy of employee records.</li>
                <li>Always log out after use, especially on shared computers.</li>
            </ol>
        </div>

        <!-- END-TO-END FOOTER -->
        <footer class="system-footer">
            <div class="footer-main-row">
                <div class="footer-logo">
                    <img src="image.png" alt="EMS Logo">
                    <h3>Employee Management System</h3>
                </div>

                <div class="footer-divider"></div>

                <div class="social-links">
                    <a href="https://facebook.com" target="_blank" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://instagram.com" target="_blank" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://x.com" target="_blank" aria-label="X (Twitter)">
                        <i class="fab fa-x-twitter"></i>
                    </a>
                    <a href="https://linkedin.com" target="_blank" aria-label="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>

            <p class="copyright">
                © <?php echo date('Y'); ?> Employee Management System &bull; 
                Developed by <strong>David Okoth</strong> &bull; 
                All Rights Reserved.
            </p>
        </footer>

    </main>

    <script>
        const ctx = document.getElementById('departmentChart');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($departments); ?>,
                datasets: [{
                    label: 'Employees',
                    data: <?php echo json_encode($totals); ?>,
                    backgroundColor: [
                        '#0076fe',
                        '#8b5cf6',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
</body>
</html>