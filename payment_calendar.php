<?php
session_start();
include("connect.php");

if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

$query = mysqli_query(
    $conn,
    "SELECT * FROM payment_requests
     WHERE employee_email='$email'
     ORDER BY request_year DESC, id DESC"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment Calendar</title>

<!-- Font Awesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    :root {
        --primary: #0076fe;
        --secondary: #8b5cf6;
        --dark: #0f172a;
        --text-muted: #64748b;
        --glass-bg: rgba(255, 255, 255, 0.92);
        --glass-border: rgba(255, 255, 255, 0.4);
        
        --success: #10b981;
        --warning: #f59e0b;
        --info: #3b82f6;
        --danger: #ef4444;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* --- VIBRANT ANIMATED BACKGROUND --- */
    body {
        background: linear-gradient(-45deg, #0f172a, #7e22ce, #e11d48, #0d9488, #1e3a8a);
        background-size: 400% 400%;
        animation: vibrantGradient 18s ease infinite;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px 16px;
    }

    @keyframes vibrantGradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* --- GLASS CONTAINER --- */
    .container {
        max-width: 1100px;
        width: 100%;
        background: var(--glass-bg);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        padding: 40px;
        border-radius: 24px;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.4);
        border: 1px solid var(--glass-border);
    }

    .header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 15px;
    }

    h1 {
        font-size: 1.8rem;
        color: var(--dark);
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    h1 i {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* --- SUMMARY STATS CARDS --- */
    .summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 35px;
    }

    .card {
        padding: 22px;
        border-radius: 18px;
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 10px 20px -5px rgba(0,0,0,0.15);
        transition: transform 0.25s ease;
    }

    .card:hover {
        transform: translateY(-4px);
    }

    .card-info h2 {
        font-size: 2.2rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 6px;
    }

    .card-info p {
        font-size: 0.9rem;
        font-weight: 600;
        opacity: 0.9;
    }

    .card-icon {
        font-size: 2.5rem;
        opacity: 0.35;
    }

    .card1 { background: linear-gradient(135deg, #0076fe, #0284c7); }
    .card2 { background: linear-gradient(135deg, #10b981, #059669); }
    .card3 { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }

    /* --- RESPONSIVE TABLE --- */
    .table-wrapper {
        overflow-x: auto;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .calendar-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .calendar-table th {
        background: #f8fafc;
        color: var(--dark);
        padding: 16px 20px;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0;
    }

    .calendar-table td {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        color: var(--dark);
        font-weight: 600;
        font-size: 0.95rem;
    }

    .calendar-table tbody tr:last-child td {
        border-bottom: none;
    }

    .calendar-table tbody tr:hover {
        background: #f8fafc;
    }

    /* --- BADGES --- */
    .status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.82rem;
        font-weight: 700;
        text-transform: capitalize;
    }

    .status.pending {
        background: rgba(245, 158, 11, 0.15);
        color: #d97706;
    }

    .status.approved {
        background: rgba(59, 130, 246, 0.15);
        color: #2563eb;
    }

    .status.paid {
        background: rgba(16, 185, 129, 0.15);
        color: #059669;
    }

    .status.rejected {
        background: rgba(239, 68, 68, 0.15);
        color: #dc2626;
    }

    /* --- BUTTONS --- */
    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
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

    /* --- MOBILE RESPONSIVENESS --- */
    @media (max-width: 650px) {
        .container {
            padding: 25px 20px;
        }

        h1 {
            font-size: 1.4rem;
        }

        .summary {
            grid-template-columns: 1fr;
        }
    }
    :root {
    --primary: #0076fe;
    --secondary: #8b5cf6;
    --dark: #0f172a;
    --text-muted: #64748b;
    --glass-footer-bg: rgba(255, 255, 255, 0.92);
    --glass-border: rgba(255, 255, 255, 0.4);
}

.system-footer {
    width: 100%;
    max-width: 1100px;
    margin: 40px auto 0 auto;
    background: var(--glass-footer-bg);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    padding: 35px 30px;
    display: flex;
    align-items: center;
    gap: 30px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
}

.footer-logo img {
    width: 90px;
    height: 90px;
    object-fit: contain;
    border-radius: 16px;
    padding: 8px;
    background: rgba(255, 255, 255, 0.8);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.footer-content {
    flex: 1;
}

.footer-content h3 {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 6px;
}

.footer-desc {
    color: #475569;
    font-size: 0.92rem;
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
    background: #f1f5f9;
    color: var(--dark);
    display: flex;
    justify-content: center;
    align-items: center;
    text-decoration: none;
    font-size: 0.95rem;
    border: 1px solid #e2e8f0;
    transition: all 0.25s ease;
}

.social-links a:hover {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: #ffffff;
    border-color: transparent;
    transform: translateY(-3px);
    box-shadow: 0 6px 15px rgba(0, 118, 254, 0.25);
}

.copyright {
    font-size: 0.85rem;
    color: var(--text-muted);
    border-top: 1px solid #e2e8f0;
    padding-top: 14px;
}

.copyright strong {
    color: var(--dark);
}

/* Responsive adjustment for small screens */
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

    <div class="header-bar">
        <h1>
            <i class="fa-solid fa-calendar-days"></i>
            Payment Calendar
        </h1>

        <!-- DIRECT ROUTE TO HOME PAGE -->
        <a href="home.php" class="back-btn">
            <i class="fa-solid fa-house"></i>
            Back to Home
        </a>
    </div>

    <?php
    $total = mysqli_num_rows($query);

    $paid_res = mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total
         FROM payment_requests
         WHERE employee_email='$email'
         AND status='Paid'"
    );
    $paid = mysqli_fetch_assoc($paid_res)['total'] ?? 0;

    $pending_res = mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total
         FROM payment_requests
         WHERE employee_email='$email'
         AND status='Pending'"
    );
    $pending = mysqli_fetch_assoc($pending_res)['total'] ?? 0;
    ?>

    <div class="summary">
        <div class="card card1">
            <div class="card-info">
                <h2><?php echo $total; ?></h2>
                <p>Total Requests</p>
            </div>
            <i class="fa-solid fa-file-invoice-dollar card-icon"></i>
        </div>

        <div class="card card2">
            <div class="card-info">
                <h2><?php echo $paid; ?></h2>
                <p>Paid</p>
            </div>
            <i class="fa-solid fa-circle-check card-icon"></i>
        </div>

        <div class="card card3">
            <div class="card-info">
                <h2><?php echo $pending; ?></h2>
                <p>Pending</p>
            </div>
            <i class="fa-solid fa-hourglass-half card-icon"></i>
        </div>
    </div>

    <div class="table-wrapper">
        <table class="calendar-table">
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Year</th>
                    <th>Amount</th>
                    <th>Currency</th>
                    <th>Method</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if($total > 0){
                    while($row = mysqli_fetch_assoc($query)){
                        $status_str = $row['status'] ?? 'Pending';
                        $status_class = strtolower($status_str);
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['request_month']); ?></td>
                    <td><?php echo htmlspecialchars($row['request_year']); ?></td>
                    <td><?php echo number_format($row['amount'], 2); ?></td>
                    <td><?php echo htmlspecialchars($row['currency']); ?></td>
                    <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                    <td>
                        <span class="status <?php echo $status_class; ?>">
                            <i class="fa-solid fa-circle" style="font-size: 0.5rem;"></i>
                            <?php echo htmlspecialchars($status_str); ?>
                        </span>
                    </td>
                </tr>
                <?php
                    }
                } else {
                ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 35px; color: var(--text-muted);">
                        <i class="fa-solid fa-folder-open" style="font-size: 2rem; margin-bottom: 10px; display: block; opacity: 0.5;"></i>
                        No payment requests found.
                    </td>
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
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