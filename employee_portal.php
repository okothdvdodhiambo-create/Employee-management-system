<?php
session_start();
include("connect.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'] ?? '';

/* FETCH USER DETAILS */
$stmt_user = $conn->prepare("SELECT * FROM employees WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();
$stmt_user->close();

/* CHECK PAID PAYMENT STATUS */
$stmt_paid = $conn->prepare("SELECT id FROM payment_requests WHERE employee_email = ? AND status = 'Paid' ORDER BY id DESC LIMIT 1");
$stmt_paid->bind_param("s", $email);
$stmt_paid->execute();
$payment_received = $stmt_paid->get_result()->num_rows > 0;
$stmt_paid->close();

$count_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) as total
     FROM notifications
     WHERE employee_id='".$_SESSION['user_id']."'
     AND status='Unread'"
);

$count = mysqli_fetch_assoc($count_query);
?>

<a href="notifications.php" class="menu-card">
    <i class="fa-solid fa-bell"></i>
    <span>
        Notifications
        (<?php echo $count['total']; ?>)
    </span>
</a>

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Portal</title>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --glass-bg: rgba(255, 255, 255, 0.88);
            --glass-border: rgba(255, 255, 255, 0.3);
            --text-main: #0f172a;
            --text-muted: #64748b;
            --primary-blue: #0076fe;
            --primary: #0076fe;
            --secondary: #8b5cf6;
            --danger-red: #ef4444;
            --dark-bg: #090d16;
            --text-footer-main: #f8fafc;
            --text-footer-muted: #94a3b8;
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
        }

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

        /* --- WRAPPER & CONTAINER --- */
        .main-wrapper {
            flex: 1 0 auto;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            width: 100%;
        }

        .portal-container {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            max-width: 850px;
            width: 100%;
            padding: 45px 40px;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid var(--glass-border);
            text-align: center;
        }

        .portal-header {
            margin-bottom: 35px;
            border-bottom: 2px solid rgba(0, 0, 0, 0.06);
            padding-bottom: 20px;
        }

        .portal-header h1 {
            font-size: 2rem;
            color: var(--text-main);
            font-weight: 700;
        }

        .portal-header p {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-top: 6px;
        }

        /* --- NAVIGATION GRID --- */
        .portal-menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .menu-card {
            background: #ffffff;
            text-decoration: none;
            padding: 22px 18px;
            border-radius: 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            color: var(--text-main);
            font-weight: 600;
            font-size: 1rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .menu-card i {
            font-size: 1.8rem;
            color: var(--primary-blue);
            transition: transform 0.25s ease;
        }

        .menu-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px -5px rgba(0, 118, 254, 0.15);
            border-color: var(--primary-blue);
        }

        .menu-card:hover i {
            transform: scale(1.15);
        }

        /* LOGOUT CARD */
        .menu-card.logout {
            background: rgba(239, 68, 68, 0.05);
            border-color: rgba(239, 68, 68, 0.2);
            color: var(--danger-red);
        }

        .menu-card.logout i {
            color: var(--danger-red);
        }

        .menu-card.logout:hover {
            background: var(--danger-red);
            color: #ffffff;
            border-color: var(--danger-red);
            box-shadow: 0 12px 20px -5px rgba(239, 68, 68, 0.3);
        }

        .menu-card.logout:hover i {
            color: #ffffff;
        }

        /* --- FOOTER SEPARATOR NOTE --- */
        .footer-note {
            margin-top: 35px;
            padding-top: 18px;
            text-align: center;
            font-size: 0.88rem;
            line-height: 1.7;
            font-style: italic;
            border-top: 1px solid rgba(0, 0, 0, 0.06);
        }

        .footer-note p { font-weight: 600; }
        .footer-note p:nth-of-type(odd) { color: #1e3a8a; }
        .footer-note p:nth-of-type(even) { color: #0d9488; }

        /* --- SYSTEM FOOTER --- */
        .system-footer {
            width: 100%;
            margin-top: auto;
            background: var(--dark-bg);
            border-top: 1px solid var(--dark-border);
            padding: 35px 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 35px;
            box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.5);
        }

        .footer-logo img {
            width: 85px;
            height: 85px;
            object-fit: contain;
            border-radius: 16px;
            padding: 8px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--dark-border);
        }

        .footer-content {
            max-width: 1000px;
            width: 100%;
        }

        .footer-content h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-footer-main);
            margin-bottom: 6px;
        }

        .footer-desc {
            color: var(--text-footer-muted);
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 18px;
            max-width: 750px;
        }

        .social-links {
            display: flex;
            gap: 12px;
            margin-bottom: 18px;
        }

        .social-links a {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.06);
            color: var(--text-footer-main);
            display: flex;
            justify-content: center;
            align-items: center;
            text-decoration: none;
            font-size: 0.95rem;
            border: 1px solid var(--dark-border);
            transition: all 0.25s ease;
        }

        .social-links a:hover {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: #ffffff;
            border-color: transparent;
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(0, 118, 254, 0.35);
        }

        .copyright {
            font-size: 0.85rem;
            color: var(--text-footer-muted);
            border-top: 1px solid var(--dark-border);
            padding-top: 14px;
        }

        .copyright strong {
            color: var(--text-footer-main);
        }

        @media (max-width: 768px) {
            .portal-container {
                padding: 30px 20px;
            }

            .system-footer {
                flex-direction: column;
                text-align: center;
                padding: 30px 20px;
            }

            .social-links {
                justify-content: center;
            }

            .footer-desc {
                margin-left: auto;
                margin-right: auto;
            }
        }
    </style>
</head>
<body>

<div class="main-wrapper">
    <div class="portal-container">

        <div class="portal-header">
            <h1>Welcome, <?php echo htmlspecialchars($user['username'] ?? 'Employee'); ?> 👋</h1>
            <p>Access your workspace services and manage your requests</p>
        </div>

        <div class="portal-menu">

            <a href="my_profile.php" class="menu-card">
                <i class="fa-solid fa-id-card"></i>
                <span>My Profile</span>
            </a>

            <a href="attendance_records.php" class="menu-card">
                <i class="fa-solid fa-user-check"></i>
                <span>My Attendance</span>
            </a>

            <a href="leave_request.php" class="menu-card">
                <i class="fa-solid fa-calendar-plus"></i>
                <span>Request Leave</span>
            </a>

            <a href="my_leave.php" class="menu-card">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>My Leave Status</span>
            </a>

            <a href="salary_request.php" class="menu-card">
                <i class="fa-solid fa-money-bill-wave"></i>
                <span>Salary & Payments</span>
            </a>

            <a href="view_events.php" class="menu-card">
                <i class="fa-solid fa-calendar-days"></i>
                <span>Company Events</span>
            </a>

            <a href="messages.php" class="menu-card">
                <i class="fa-solid fa-comments"></i>
                <span>Messages</span>
            </a>

            <a href="change_password.php" class="menu-card">
                <i class="fa-solid fa-key"></i>
                <span>Change Password</span>
            </a>
            <a href="view_announcements.php" class="menu-card">

    <i class="fa-solid fa-bullhorn"></i>

    <span>
        Company Announcements
    </span>

</a>

            <a href="logout.php" class="menu-card logout">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </a>

        </div>

        <div class="footer-note">
            <p>Select a portal module to access your personal dashboard services.</p>
            <p>System security parameters are active for this user session.</p>
        </div>

    </div>
</div>

<?php if($payment_received){ ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Salary Received',
    text: 'Your salary payment has been processed successfully.',
    confirmButtonColor: '#0076fe'
});
</script>
<?php } ?>

<footer class="system-footer">
    <div class="footer-logo">
        <img src="image.png" alt="EMS Logo">
    </div>

    <div class="footer-content">
        <h3>Employee Management System</h3>

        <p class="footer-desc">
            Streamlining employee management, attendance tracking,
            leave management, reporting, and organizational productivity.
        </p>

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

        <p class="copyright">
            © <?php echo date('Y'); ?> Employee Management System &bull; 
            Developed by <strong>David Okoth</strong> &bull; 
            All Rights Reserved.
        </p>
    </div>
</footer>

</body>
</html>