<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("connect.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Secure prepared statement against SQL Injection
$stmt = mysqli_prepare($conn, "SELECT * FROM leave_requests WHERE employee_id = ? ORDER BY id DESC");
mysqli_stmt_bind_param($stmt, "s", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Leave Status</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-blue: #0076fe;
            --primary-hover: #005ecb;
            --secondary: #8b5cf6;
            --success-green: #10b981;
            --danger-red: #ef4444;
            --warning-amber: #f59e0b;
            --glass-border: rgba(255, 255, 255, 0.25);
            --card-bg: rgba(255, 255, 255, 0.88);
            --text-slate: #475569;
            --dark-bg: #090d16;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --dark-border: rgba(255, 255, 255, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        html, body {
            height: 100%;
            overflow-x: hidden;
        }

        body {
            background: linear-gradient(-45deg, #0f172a, #1e3a8a, #0d9488, #111827);
            background-size: 400% 400%;
            animation: movingGradient 15s ease infinite;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        @keyframes movingGradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* --- GLASSMORPHIC MAIN CARD CONTAINER --- */
        .container {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            max-width: 1000px;
            width: 90%;
            margin: 40px auto;
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid var(--glass-border);
            flex: 1 0 auto;
        }

        /* --- TOP HEADER NAVIGATION ZONE --- */
        .header-area {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid rgba(0, 0, 0, 0.06);
            padding-bottom: 20px;
            margin-bottom: 25px;
        }

        .header-area h2 {
            font-size: 1.65rem;
            color: #0f172a;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-area h2 i {
            color: var(--primary-blue);
        }

        .back-btn {
            background: #ffffff;
            color: var(--text-slate);
            text-decoration: none;
            padding: 11px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            transition: all 0.2s ease;
        }

        .back-btn:hover {
            background: #f8fafc;
            color: #0f172a;
            border-color: #cbd5e1;
            transform: translateX(-2px);
        }

        /* --- DATA TABLE FRAMEWORK --- */
        .table-wrapper {
            width: 100%;
            overflow-x: auto;
            border-radius: 14px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            text-align: left;
        }

        th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 0.88rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 16px 20px;
            border-bottom: 2px solid #edf2f7;
        }

        td {
            padding: 16px 20px;
            color: #334155;
            font-size: 0.95rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background-color: #f8fafc;
        }

        /* --- SYSTEM PILL STATUS BADGES --- */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.8rem;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .status-badge.approved {
            background: rgba(16, 185, 129, 0.12);
            color: var(--success-green);
        }

        .status-badge.rejected {
            background: rgba(239, 68, 68, 0.12);
            color: var(--danger-red);
        }

        .status-badge.pending {
            background: rgba(245, 158, 11, 0.12);
            color: var(--warning-amber);
        }

        /* --- FOOTER NOTE --- */
        .footer-note {
            margin-top: 40px;
            padding-top: 20px;
            text-align: center;
            font-size: 0.9rem;
            line-height: 1.8;
            font-style: italic;
            border-top: 1px solid rgba(0, 0, 0, 0.06);
        }
        .footer-note p { font-weight: 600; margin-bottom: 6px; }
        .footer-note p:last-child { margin-bottom: 0; }
        .footer-note p:nth-of-type(odd) { color: #1e3a8a; }
        .footer-note p:nth-of-type(even) { color: #0d9488; }

        /* --- FULL-WIDTH BLACK FOOTER --- */
        .system-footer {
            width: 100%;
            background: var(--dark-bg);
            border-top: 1px solid var(--dark-border);
            padding: 35px 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 35px;
            box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.5);
            margin-top: auto;
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
            color: var(--text-muted);
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 18px;
            max-width: 750px;
        }

        /* --- SOCIAL LINKS --- */
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
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary));
            color: #ffffff;
            border-color: transparent;
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(0, 118, 254, 0.35);
        }

        .copyright {
            font-size: 0.85rem;
            color: var(--text-muted);
            border-top: 1px solid var(--dark-border);
            padding-top: 14px;
        }

        .copyright strong {
            color: var(--text-main);
        }

        /* --- RESPONSIVE DESIGN --- */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
                margin: 20px auto;
            }

            .header-area {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
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

<div class="container">
    <div class="header-area">
        <h2><i class="fa-solid fa-clock-rotate-left"></i> My Leave Requests History</h2>
        <a href="home.php" class="back-btn">
            <i class="fa-solid fa-arrow-left-long"></i> Back to Home
        </a>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Leave Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Reason / Explanation</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td style="font-weight: 600; color: #0f172a;"><?php echo htmlspecialchars($row['leave_type'] ?? ''); ?></td>
                        <td><i class="fa-regular fa-calendar" style="color: #94a3b8; margin-right: 4px;"></i> <?php echo htmlspecialchars($row['start_date'] ?? ''); ?></td>
                        <td><i class="fa-regular fa-calendar-check" style="color: #94a3b8; margin-right: 4px;"></i> <?php echo htmlspecialchars($row['end_date'] ?? ''); ?></td>
                        <td style="max-width: 300px; color: #64748b; font-size: 0.9rem; line-height: 1.5;">
                            <?php echo htmlspecialchars($row['reason'] ?? ''); ?>
                        </td>
                        <td>
                            <?php
                            $status = $row['status'] ?? 'Pending';
                            if ($status === 'Approved') {
                                echo "<span class='status-badge approved'><i class='fa-solid fa-circle-check'></i> Approved</span>";
                            } elseif ($status === 'Rejected') {
                                echo "<span class='status-badge rejected'><i class='fa-solid fa-circle-xmark'></i> Rejected</span>";
                            } else {
                                echo "<span class='status-badge pending'><i class='fa-solid fa-circle-dot'></i> Pending</span>";
                            }
                            ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: #94a3b8; padding: 40px;">
                            <i class="fa-solid fa-folder-open" style="font-size: 2rem; display: block; margin-bottom: 12px; color: #cbd5e1;"></i>
                            You haven't submitted any leave request records yet.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="footer-note">
        <p>This tracking interface reflects real-time status transitions within the application stack.</p>
        <p>Please contact your department supervisor if an allocation review is urgently delayed.</p>
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