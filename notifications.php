<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("connect.php");

// Authentication Guard: Fixed to check 'user_id' instead of 'id'
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Updated variable assignment to use 'user_id'
$employee_id = $_SESSION['user_id'];

// 1. Mark unread notifications as read using a prepared statement
$update_stmt = mysqli_prepare(
    $conn,
    "UPDATE notifications SET status = 'Read' WHERE employee_id = ? AND status = 'Unread'"
);
if ($update_stmt) {
    mysqli_stmt_bind_param($update_stmt, "i", $employee_id);
    mysqli_stmt_execute($update_stmt);
    mysqli_stmt_close($update_stmt);
}

// 2. Fetch notifications using a prepared statement
$notifications = [];
$fetch_stmt = mysqli_prepare(
    $conn,
    "SELECT id, message, status, created_at FROM notifications WHERE employee_id = ? ORDER BY created_at DESC"
);

if ($fetch_stmt) {
    mysqli_stmt_bind_param($fetch_stmt, "i", $employee_id);
    mysqli_stmt_execute($fetch_stmt);
    $result = mysqli_stmt_get_result($fetch_stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $notifications[] = $row;
    }
    mysqli_stmt_close($fetch_stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications Center</title>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary: #0076fe;
            --secondary: #8b5cf6;
            --dark: #0f172a;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --glass-bg: rgba(255, 255, 255, 0.92);
            --glass-border: rgba(255, 255, 255, 0.5);
            --card-bg: #ffffff;
            --card-border: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(-45deg, #0f172a, #7e22ce, #e11d48, #0d9488, #1e3a8a);
            background-size: 400% 400%;
            animation: backgroundFlow 18s ease infinite;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 40px 20px;
        }

        @keyframes backgroundFlow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            max-width: 850px;
            width: 100%;
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            padding: 35px;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            border: 1px solid var(--glass-border);
        }

        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
            padding-bottom: 18px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        }

        h2 {
            font-size: 1.6rem;
            color: var(--dark);
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        h2 i {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .count-badge {
            font-size: 0.85rem;
            background: rgba(0, 118, 254, 0.1);
            color: var(--primary);
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
        }

        .notifications-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .notification-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            padding: 20px;
            border-radius: 16px;
            display: flex;
            gap: 16px;
            align-items: flex-start;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }

        .notification-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            border-color: #cbd5e1;
        }

        .notification-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 5px;
            background: linear-gradient(180deg, var(--primary), var(--secondary));
        }

        .icon-box {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: rgba(0, 118, 254, 0.08);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .notification-content {
            flex-grow: 1;
        }

        .notification-text {
            color: var(--text-main);
            font-size: 0.98rem;
            line-height: 1.5;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .notification-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.82rem;
            color: var(--text-muted);
        }

        .time {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* --- EMPTY STATE --- */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 3rem;
            color: #cbd5e1;
            margin-bottom: 15px;
        }

        .empty-state p {
            font-size: 1rem;
            font-weight: 500;
        }

        /* --- FOOTER / ACTIONS --- */
        .actions-bar {
            margin-top: 30px;
            display: flex;
            justify-content: flex-start;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 22px;
            background: var(--card-bg);
            color: var(--dark);
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.92rem;
            border: 1px solid #cbd5e1;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
            transition: all 0.25s ease;
        }

        .back-btn:hover {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 118, 254, 0.3);
        }

        @media (max-width: 600px) {
            .container {
                padding: 22px;
            }
            .header-bar {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>

<div class="container">

    <div class="header-bar">
        <h2>
            <i class="fa-solid fa-bell"></i>
            Notifications
        </h2>
        <span class="count-badge">
            Total: <?php echo count($notifications); ?>
        </span>
    </div>

    <div class="notifications-list">
        <?php if (!empty($notifications)): ?>
            <?php foreach ($notifications as $notification): ?>
                <div class="notification-card">
                    <div class="icon-box">
                        <i class="fa-solid fa-envelope-open"></i>
                    </div>
                    <div class="notification-content">
                        <p class="notification-text">
                            <?php echo htmlspecialchars($notification['message'], ENT_QUOTES, 'UTF-8'); ?>
                        </p>
                        <div class="notification-meta">
                            <span class="time">
                                <i class="fa-regular fa-clock"></i>
                                <?php echo date("M d, Y • h:i A", strtotime($notification['created_at'])); ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fa-solid fa-bell-slash"></i>
                <p>You have no notifications at this time.</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="actions-bar">
        <a href="employee_portal.php" class="back-btn">
            <i class="fa-solid fa-arrow-left"></i>
            Back to Portal
        </a>
    </div>

</div>

</body>
</html>