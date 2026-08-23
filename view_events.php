<?php
session_start();
include("connect.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$today = date("Y-m-d");

/* --- FETCH ACTIVE / UPCOMING EVENTS --- */
$stmt_events = mysqli_prepare($conn, "
    SELECT * FROM events 
    WHERE end_date >= ? 
    ORDER BY start_date ASC
");
mysqli_stmt_bind_param($stmt_events, "s", $today);
mysqli_stmt_execute($stmt_events);
$result = mysqli_stmt_get_result($stmt_events);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Company Events</title>

<!-- Font Awesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    :root {
        --primary: #0076fe;
        --secondary: #8b5cf6;
        --dark: #0f172a;
        --card-bg: rgba(255, 255, 255, 0.94);
        --glass-border: rgba(255, 255, 255, 0.4);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* --- ANIMATED GRADIENT BACKGROUND --- */
    body {
        background: linear-gradient(-45deg, #0f172a, #7e22ce, #0076fe, #0d9488);
        background-size: 400% 400%;
        animation: bgMove 15s ease infinite;
        min-height: 100vh;
        padding: 40px 20px;
        display: flex;
        justify-content: center;
        align-items: flex-start;
    }

    @keyframes bgMove {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .container {
        max-width: 950px;
        width: 100%;
    }

    /* --- HEADER TITLE --- */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        color: white;
    }

    .header-text h1 {
        font-size: 2.2rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .header-text p {
        margin-top: 6px;
        opacity: 0.9;
        font-size: 0.95rem;
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        text-decoration: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9rem;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        transition: all 0.25s ease;
    }

    .back-btn:hover {
        background: rgba(255, 255, 255, 0.35);
        transform: translateX(-3px);
    }

    /* --- EVENT CARD --- */
    .event-card {
        background: var(--card-bg);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        margin-bottom: 22px;
        border-radius: 20px;
        padding: 28px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        border: 1px solid var(--glass-border);
        border-left: 6px solid var(--primary);
        transition: all 0.3s ease;
    }

    .event-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
    }

    .event-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 15px;
        margin-bottom: 16px;
    }

    .event-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: var(--dark);
        line-height: 1.3;
    }

    .date-badge {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        padding: 8px 16px;
        border-radius: 30px;
        font-size: 0.85rem;
        font-weight: 700;
        white-space: nowrap;
        box-shadow: 0 4px 12px rgba(0, 118, 254, 0.25);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .event-description {
        color: #475569;
        font-size: 0.98rem;
        line-height: 1.65;
        background: rgba(248, 250, 252, 0.7);
        padding: 16px 20px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }

    .today-tag {
        background: #10b981;
        color: white;
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 6px;
        margin-left: 8px;
        vertical-align: middle;
    }

    /* --- EMPTY STATE --- */
    .no-events {
        background: var(--card-bg);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-radius: 20px;
        text-align: center;
        padding: 50px 30px;
        color: #64748b;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        border: 1px solid var(--glass-border);
    }

    .no-events i {
        font-size: 3rem;
        margin-bottom: 15px;
        opacity: 0.4;
        color: var(--primary);
    }

    .no-events h3 {
        font-size: 1.2rem;
        color: var(--dark);
        margin-bottom: 6px;
    }

    @media (max-width: 640px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }

        .event-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .date-badge {
            align-self: flex-start;
        }
    }
    :root {
    --primary: #0076fe;
    --secondary: #8b5cf6;
    --dark-bg: #090d16;          /* Deep rich black */
    --text-main: #f8fafc;        /* High-contrast white/light text */
    --text-muted: #94a3b8;       /* Subtle muted text */
    --dark-border: rgba(255, 255, 255, 0.1);
}

/* --- Ensure page stretches full height so footer sits at bottom --- */
html, body {
    height: 100%;
    margin: 0;
    padding: 0;
}

body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

/* --- Container wrapper push footer down --- */
.container {
    flex: 1 0 auto; /* Pushes footer to bottom when page content is short */
}

/* --- FULL-WIDTH BLACK FOOTER --- */
.system-footer {
    width: 100vw;                /* Cover screen left to right */
    position: relative;
    left: 50%;
    right: 50%;
    margin-left: -50vw;
    margin-right: -50vw;
    margin-top: auto;            /* Sticks to the bottom */
    
    background: var(--dark-bg);
    border-top: 1px solid var(--dark-border);
    padding: 35px 40px;
    
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 35px;
    box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.5);
    box-sizing: border-box;
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

/* --- SOCIAL LINKS (DARK THEME) --- */
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

/* --- COPYRIGHT BAR --- */
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

    <!-- PAGE HEADER -->
    <div class="page-header">
        <div class="header-text">
            <h1>
                <i class="fa-solid fa-calendar-days"></i>
                Company Events
            </h1>
            <p>Stay updated with meetings, trainings, holidays, and company activities.</p>
        </div>

        <?php
        $back_page = "employee_portal.php";
        if (isset($_SESSION['role']) && $_SESSION['role'] === "super_admin") {
            $back_page = "super_admin_dashboard.php";
        }
        ?>

        <a href="<?php echo $back_page; ?>" class="back-btn">
            <i class="fa-solid fa-arrow-left"></i>
            Back
        </a>
    </div>

    <!-- EVENTS LIST -->
    <?php if (mysqli_num_rows($result) > 0) { ?>

        <?php while ($row = mysqli_fetch_assoc($result)) { 
            $is_today = ($row['start_date'] <= $today && $row['end_date'] >= $today);
        ?>

        <div class="event-card">

            <div class="event-header">
                <div class="event-title">
                    <?php echo htmlspecialchars($row['title']); ?>
                    <?php if ($is_today) { ?>
                        <span class="today-tag">Happening Today</span>
                    <?php } ?>
                </div>

                <div class="date-badge">
                    <i class="fa-regular fa-calendar-check"></i>
                    <?php
                    if ($row['start_date'] === $row['end_date']) {
                        echo date("M d, Y", strtotime($row['start_date']));
                    } else {
                        echo date("M d", strtotime($row['start_date'])) . " - " . date("M d, Y", strtotime($row['end_date']));
                    }
                    ?>
                </div>
            </div>

            <div class="event-description">
                <?php echo nl2br(htmlspecialchars($row['description'])); ?>
            </div>

        </div>

        <?php } ?>

    <?php } else { ?>

        <div class="no-events">
            <i class="fa-solid fa-calendar-xmark"></i>
            <h3>No Active Events</h3>
            <p>There are no company events or activities scheduled at the moment.</p>
        </div>

    <?php } ?>

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