<?php
session_start();
include("connect.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* PREPARED STATEMENT FOR SECURITY */
$stmt = $conn->prepare("SELECT * FROM attendance WHERE employee_id = ? ORDER BY attendance_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Attendance Records | Employee Management System</title>

<!-- Font Awesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    :root {
        --primary-blue: #0076fe;
        --accent-purple: #8b5cf6;
        --accent-teal: #0d9488;
        --text-dark: #0f172a;
        --text-muted: #64748b;
        --glass-bg: rgba(255, 255, 255, 0.88);
        --glass-border: rgba(255, 255, 255, 0.3);
        --status-present: #10b981;
        --status-absent: #ef4444;
        --status-late: #f59e0b;
        --dark-bg: #090d16;
        --text-main: #f8fafc;
        --text-muted-footer: #94a3b8;
        --dark-border: rgba(255, 255, 255, 0.1);
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
        margin: 0;
        padding: 0;
        overflow-x: hidden;
    }

    /* --- VIBRANT MOVING GRADIENT BACKGROUND --- */
    body {
        background: linear-gradient(-45deg, #0f172a, #7e22ce, #e11d48, #0d9488, #1e3a8a);
        background-size: 400% 400%;
        animation: vibrantGradient 18s ease infinite;
        display: flex;
        flex-direction: column;
    }

    @keyframes vibrantGradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* --- FLEX MAIN CONTENT WRAPPER --- */
    .main-wrapper {
        flex: 1 0 auto;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding: 40px 16px;
        width: 100%;
    }

    /* --- GLASSMORPHIC CONTAINER --- */
    .report-container {
        max-width: 950px;
        width: 100%;
        background: var(--glass-bg);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        padding: 35px;
        border-radius: 24px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
        border: 1px solid var(--glass-border);
    }

    .header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        flex-wrap: wrap;
        gap: 15px;
    }

    h2 {
        font-size: 1.6rem;
        color: var(--text-dark);
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    h2 i {
        color: var(--primary-blue);
    }

    /* --- TABLE STYLING --- */
    .table-wrapper {
        overflow-x: auto;
        border-radius: 14px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        background: #ffffff;
    }

    .custom-table th {
        background: linear-gradient(
            135deg,
            var(--primary-blue) 0%,
            var(--accent-purple) 100%
        );
        color: white;
        padding: 16px;
        text-align: left;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .custom-table td {
        padding: 15px 16px;
        color: var(--text-dark);
        font-weight: 500;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.95rem;
    }

    .custom-table tr:last-child td {
        border-bottom: none;
    }

    .custom-table tr:hover td {
        background: #f8fafc;
    }

    /* --- BADGES --- */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 0.82rem;
        font-weight: 700;
    }

    .badge-present {
        background-color: rgba(16, 185, 129, 0.12);
        color: var(--status-present);
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .badge-absent {
        background-color: rgba(239, 68, 68, 0.12);
        color: var(--status-absent);
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .badge-late {
        background-color: rgba(245, 158, 11, 0.12);
        color: var(--status-late);
        border: 1px solid rgba(245, 158, 11, 0.3);
    }

    /* --- DIRECT HOME PAGE BUTTON --- */
    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #ffffff;
        color: var(--text-dark);
        text-decoration: none;
        padding: 10px 18px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9rem;
        border: 1px solid #cbd5e1;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        transition: all 0.2s ease;
    }

    .back-btn:hover {
        background: var(--primary-blue);
        color: #ffffff;
        border-color: var(--primary-blue);
        transform: translateY(-2px);
    }

    /* --- END-TO-END BOTTOM FOOTER --- */
    .system-footer {
        width: 100%;
        margin-top: auto;
        background: var(--dark-bg);
        border-top: 1px solid var(--dark-border);
        padding: 40px 20px 20px;
        box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.5);
        box-sizing: border-box;
    }

    .footer-container {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 30px;
        padding-bottom: 30px;
        border-bottom: 1px solid var(--dark-border);
    }

    .footer-brand {
        flex: 1 1 300px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .footer-brand-header {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .footer-logo img {
        width: 45px;
        height: 45px;
        object-fit: contain;
        border-radius: 10px;
        padding: 4px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--dark-border);
    }

    .footer-brand h3 {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--text-main);
    }

    .footer-desc {
        color: var(--text-muted-footer);
        font-size: 0.88rem;
        line-height: 1.5;
    }

    .footer-column {
        flex: 1 1 160px;
    }

    .footer-column h4 {
        color: var(--text-main);
        font-size: 0.95rem;
        font-weight: 600;
        margin-bottom: 16px;
        position: relative;
    }

    .footer-column h4::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: -5px;
        width: 25px;
        height: 2px;
        background: linear-gradient(90deg, var(--primary-blue), var(--accent-purple));
        border-radius: 2px;
    }

    .footer-links {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .footer-links a {
        color: var(--text-muted-footer);
        text-decoration: none;
        font-size: 0.88rem;
        transition: color 0.2s ease, transform 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .footer-links a:hover {
        color: #ffffff;
        transform: translateX(4px);
    }

    .footer-contact-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        color: var(--text-muted-footer);
        font-size: 0.88rem;
        margin-bottom: 10px;
    }

    .footer-contact-item i {
        color: var(--primary-blue);
        margin-top: 2px;
    }

    .social-links {
        display: flex;
        gap: 8px;
        margin-top: 10px;
    }

    .social-links a {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.06);
        color: var(--text-main);
        display: flex;
        justify-content: center;
        align-items: center;
        text-decoration: none;
        font-size: 0.88rem;
        border: 1px solid var(--dark-border);
        transition: all 0.25s ease;
    }

    .social-links a:hover {
        background: linear-gradient(135deg, var(--primary-blue), var(--accent-purple));
        color: #ffffff;
        border-color: transparent;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 118, 254, 0.3);
    }

    .copyright-bar {
        max-width: 1200px;
        margin: 15px auto 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        font-size: 0.85rem;
        color: var(--text-muted-footer);
    }

    .copyright-bar strong {
        color: var(--text-main);
    }

    /* --- RESPONSIVE DESIGN --- */
    @media (max-width: 600px) {
        .footer-container {
            flex-direction: column;
            gap: 25px;
        }

        .copyright-bar {
            flex-direction: column;
            text-align: center;
        }

        .report-container {
            padding: 20px 15px;
        }

        h2 {
            font-size: 1.3rem;
        }

        .custom-table th, .custom-table td {
            padding: 12px 10px;
            font-size: 0.85rem;
        }

        .status-badge {
            padding: 4px 10px;
            font-size: 0.75rem;
        }
    }
</style>
</head>

<body>

<div class="main-wrapper">
    <div class="report-container">

        <div class="header-bar">
            <h2>
                <i class="fa-solid fa-calendar-check"></i>
                My Attendance Records
            </h2>

            <!-- DIRECT HOME ROUTE -->
            <a href="home.php" class="back-btn">
                <i class="fa-solid fa-house"></i>
                Back to Home
            </a>
        </div>

        <div class="table-wrapper">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $current_status = $row['status'];
                        $badge_style = 'badge-present';

                        if (stripos($current_status, 'Absent') !== false) {
                            $badge_style = 'badge-absent';
                        } elseif (stripos($current_status, 'Late') !== false) {
                            $badge_style = 'badge-late';
                        }

                        $clean_text = str_replace(
                            array('🟢', '🔴', '🟡'),
                            '',
                            $current_status
                        );
                ?>
                    <tr>
                        <td><?php echo date('M d, Y', strtotime($row['attendance_date'])); ?></td>
                        <td><?php echo date('h:i A', strtotime($row['check_in'])); ?></td>
                        <td>
                            <?php
                            if (!empty($row['check_out'])) {
                                echo date('h:i A', strtotime($row['check_out']));
                            } else {
                                echo "--";
                            }
                            ?>
                        </td>
                        <td>
                            <span class="status-badge <?php echo $badge_style; ?>">
                                <?php echo htmlspecialchars(trim($clean_text)); ?>
                            </span>
                        </td>
                    </tr>
                <?php
                    }
                } else {
                ?>
                    <tr>
                        <td colspan="4" style="text-align:center; padding:35px; color: var(--text-muted);">
                            No attendance records found.
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- FULL END-TO-END SYSTEM FOOTER -->
<footer class="system-footer">
    <div class="footer-container">
        
        <!-- COLUMN 1: BRAND -->
        <div class="footer-brand">
            <div class="footer-brand-header">
                <div class="footer-logo">
                    <img src="image.png" alt="EMS Logo">
                </div>
                <h3>Employee Management System</h3>
            </div>
            <p class="footer-desc">
                Streamlining employee management, attendance tracking, leave applications, 
                and performance analytics into a singular high-productivity workspace.
            </p>
            <div class="social-links">
                <a href="https://facebook.com" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="https://instagram.com" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="https://x.com" target="_blank" aria-label="X (Twitter)"><i class="fab fa-x-twitter"></i></a>
                <a href="https://linkedin.com" target="_blank" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>

        <!-- COLUMN 2: QUICK NAVIGATION -->
        <div class="footer-column">
            <h4>Quick Links</h4>
            <ul class="footer-links">
                <li><a href="home.php"><i class="fa-solid fa-angle-right"></i> Home</a></li>
                <li><a href="my_attendance.php"><i class="fa-solid fa-angle-right"></i> My Attendance</a></li>
                <li><a href="leave_request.php"><i class="fa-solid fa-angle-right"></i> Request Leave</a></li>
                <li><a href="profile.php"><i class="fa-solid fa-angle-right"></i> Profile Settings</a></li>
            </ul>
        </div>

        <!-- COLUMN 3: SYSTEM PORTALS -->
        <div class="footer-column">
            <h4>Portals</h4>
            <ul class="footer-links">
                <li><a href="home.php"><i class="fa-solid fa-angle-right"></i> Home Portal</a></li>
                <li><a href="reports.php"><i class="fa-solid fa-angle-right"></i> Reports</a></li>
                <li><a href="help.php"><i class="fa-solid fa-angle-right"></i> Support Desk</a></li>
                <li><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
            </ul>
        </div>

        <!-- COLUMN 4: SYSTEM CONTACT -->
        <div class="footer-column">
            <h4>Contact & Support</h4>
            <div class="footer-contact-item">
                <i class="fa-solid fa-location-dot"></i>
                <span>Headquarters & Operations, Nairobi, Kenya</span>
            </div>
            <div class="footer-contact-item">
                <i class="fa-solid fa-envelope"></i>
                <span>support@ems.internal</span>
            </div>
            <div class="footer-contact-item">
                <i class="fa-solid fa-phone"></i>
                <span>+254 (0) 700 000 000</span>
            </div>
        </div>

    </div>

    <!-- COPYRIGHT BAR -->
    <div class="copyright-bar">
        <p>&copy; <?php echo date('Y'); ?> Employee Management System &bull; All Rights Reserved.</p>
        <p>Developed by <strong>David Okoth</strong></p>
    </div>
</footer>

</body>
</html>