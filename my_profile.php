<?php
session_start();
include("connect.php");

if (!isset($_SESSION['username']) || !isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

/* PREPARED STATEMENT FOR SECURITY */
$stmt = $conn->prepare("SELECT * FROM details WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Employee Management</title>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary: #0076fe;
            --secondary: #8b5cf6;
            --dark: #0f172a;
            --text-muted: #64748b;
            --glass-bg: rgba(255, 255, 255, 0.92);
            --glass-border: rgba(255, 255, 255, 0.3);
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
        }

        /* --- ANIMATED GRADIENT BACKGROUND --- */
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
            padding: 40px 16px;
            width: 100%;
        }

        /* --- GLASS CARD CONTAINER --- */
        .profile-card {
            width: 100%;
            max-width: 750px;
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            border: 1px solid var(--glass-border);
        }

        .profile-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            text-align: center;
            padding: 35px 20px;
            color: white;
        }

        .profile-photo {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            border: 4px solid white;
            object-fit: cover;
            margin-bottom: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .profile-header h2 {
            font-size: 1.7rem;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .profile-header p {
            font-size: 0.95rem;
            opacity: 0.95;
        }

        .profile-body {
            padding: 30px 35px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 10px;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-row:last-of-type {
            border-bottom: none;
        }

        .info-title {
            font-weight: 600;
            color: var(--text-muted);
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-value {
            color: var(--dark);
            font-weight: 600;
            font-size: 0.95rem;
        }

        /* --- ACTION AREA & DIRECT HOME BUTTON --- */
        .action-bar {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: #ffffff;
            color: var(--dark);
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            border: 1px solid #cbd5e1;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
            transition: all 0.2s ease;
        }

        .back-btn:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        /* --- FULL-WIDTH FOOTER --- */
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
            color: var(--text-main);
            margin-bottom: 6px;
        }

        .footer-desc {
            color: var(--text-muted-footer);
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
            color: var(--text-main);
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
            color: var(--text-muted-footer);
            border-top: 1px solid var(--dark-border);
            padding-top: 14px;
        }

        .copyright strong {
            color: var(--text-main);
        }

        @media (max-width: 768px) {
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

        @media (max-width: 600px) {
            .profile-body {
                padding: 20px;
            }
            
            .info-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }
        }
    </style>
</head>
<body>

<div class="main-wrapper">
    <div class="profile-card">

        <div class="profile-header">
            <?php if (!empty($user['photo'])) { ?>
                <img src="uploads/<?php echo htmlspecialchars($user['photo']); ?>" class="profile-photo" alt="Profile Photo">
            <?php } else { ?>
                <img src="https://via.placeholder.com/130" class="profile-photo" alt="Default Profile Photo">
            <?php } ?>

            <h2><?php echo htmlspecialchars($user['fullname'] ?? ''); ?></h2>

            <p>
                Employee Code: 
                <strong><?php echo htmlspecialchars($user['employee_code'] ?? 'N/A'); ?></strong>
            </p>
        </div>

        <div class="profile-body">

            <div class="info-row">
                <span class="info-title"><i class="fa-solid fa-envelope"></i> Email</span>
                <span class="info-value"><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></span>
            </div>

            <div class="info-row">
                <span class="info-title"><i class="fa-solid fa-building"></i> Department</span>
                <span class="info-value"><?php echo htmlspecialchars($user['department'] ?? 'N/A'); ?></span>
            </div>

            <div class="info-row">
                <span class="info-title"><i class="fa-solid fa-briefcase"></i> Position</span>
                <span class="info-value"><?php echo htmlspecialchars($user['position'] ?? 'N/A'); ?></span>
            </div>

            <div class="info-row">
                <span class="info-title"><i class="fa-solid fa-phone"></i> Phone</span>
                <span class="info-value"><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></span>
            </div>

            <div class="info-row">
                <span class="info-title"><i class="fa-solid fa-money-bill-wave"></i> Salary</span>
                <span class="info-value">KES <?php echo isset($user['salary']) ? number_format($user['salary']) : '0'; ?></span>
            </div>

            <div class="action-bar">
                <!-- DIRECT HOME ROUTE -->
                <a href="home.php" class="back-btn">
                    <i class="fa-solid fa-house"></i>
                    Back to Home
                </a>
            </div>

        </div>

    </div>
</div>

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