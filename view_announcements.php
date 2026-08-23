<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("connect.php");

/* AUTHENTICATION GUARD */
if (empty($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$today = date("Y-m-d");

/* FETCH ACTIVE ANNOUNCEMENTS */
$stmt = $conn->prepare("
    SELECT title, message, start_date, end_date, created_at
    FROM announcements
    WHERE start_date <= ? AND end_date >= ?
    ORDER BY created_at DESC
");

$stmt->bind_param("ss", $today, $today);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Announcements</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #0076fe;
            --primary-hover: #0056c6;
            --bg-gradient: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            --card-bg: rgba(255, 255, 255, 0.96);
            --text-main: #0f172a;
            --text-muted: #64748b;
            --radius: 16px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        body {
            min-height: 100vh;
            padding: 40px 20px;
            background: var(--bg-gradient);
            color: var(--text-main);
        }

        .container {
            max-width: 850px;
            margin: auto;
        }

        .header {
            text-align: center;
            color: white;
            margin-bottom: 35px;
        }

        .header h1 {
            font-size: 2.2rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 8px;
        }

        .header p {
            color: #cbd5e1;
            font-size: 1rem;
        }

        /* ANNOUNCEMENT CARD */
        .announcement {
            background: var(--card-bg);
            border-radius: var(--radius);
            padding: 28px;
            margin-bottom: 24px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            border-left: 6px solid var(--primary);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .announcement:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
        }

        .announcement-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 16px;
        }

        .title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 10px;
            line-height: 1.3;
        }

        .date-badge {
            background: #eff6ff;
            color: var(--primary);
            border: 1px solid #bfdbfe;
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 0.825rem;
            font-weight: 600;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .message {
            color: #334155;
            line-height: 1.7;
            font-size: 0.975rem;
            white-space: pre-line;
            border-top: 1px solid #f1f5f9;
            padding-top: 16px;
        }

        /* EMPTY STATE */
        .no-announcements {
            background: var(--card-bg);
            padding: 60px 20px;
            text-align: center;
            border-radius: var(--radius);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .no-announcements i {
            font-size: 55px;
            margin-bottom: 18px;
            color: #94a3b8;
        }

        .no-announcements h2 {
            font-size: 1.4rem;
            color: #1e293b;
        }

        .no-announcements p {
            color: var(--text-muted);
            margin-top: 8px;
        }

        /* ACTIONS */
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 20px;
            padding: 12px 22px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.2s ease;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(-2px);
        }

        @media(max-width: 650px) {
            .announcement-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .header h1 {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>

<div class="container">

    <div class="header">
        <h1>
            <i class="fa-solid fa-bullhorn"></i> Company Announcements
        </h1>
        <p>Stay informed with the latest updates and notices.</p>
    </div>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <?php 
                $start_formatted = date("M d, Y", strtotime($row['start_date']));
                $end_formatted = date("M d, Y", strtotime($row['end_date']));
                $date_display = ($start_formatted === $end_formatted) 
                    ? $start_formatted 
                    : $start_formatted . ' - ' . $end_formatted;
            ?>
            <div class="announcement">
                <div class="announcement-header">
                    <div class="title">
                        <i class="fa-solid fa-circle-info" style="color: var(--primary);"></i>
                        <?= htmlspecialchars($row['title']); ?>
                    </div>

                    <div class="date-badge">
                        <i class="fa-regular fa-calendar-days"></i>
                        <?= $date_display; ?>
                    </div>
                </div>

                <div class="message">
                    <?= nl2br(htmlspecialchars($row['message'])); ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="no-announcements">
            <i class="fa-solid fa-bell-slash"></i>
            <h2>No Active Announcements</h2>
            <p>There are currently no company announcements posted for today.</p>
        </div>
    <?php endif; ?>

    <a href="employee_portal.php" class="back-btn">
        <i class="fa-solid fa-arrow-left"></i> Back to Employee Portal
    </a>

</div>

</body>
</html>