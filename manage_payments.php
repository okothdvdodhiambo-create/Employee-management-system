<?php
session_start();
include("connect.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'super_admin'){
    header("Location: login.php");
    exit();
}

$success_message = "";

/* APPROVE PAYMENT */
if(isset($_GET['approve'])){
    $id = (int)$_GET['approve'];
    $stmt = $conn->prepare("UPDATE payment_requests SET status='Approved' WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $success_message = "Payment request approved successfully.";
}

/* REJECT PAYMENT */
if(isset($_GET['reject'])){
    $id = (int)$_GET['reject'];
    $stmt = $conn->prepare("UPDATE payment_requests SET status='Rejected' WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $success_message = "Payment request rejected.";
}

/* MARK AS PAID */
if(isset($_GET['pay'])){
    $id = (int)$_GET['pay'];
    $stmt = $conn->prepare("UPDATE payment_requests SET status='Paid' WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $success_message = "Salary payment disbursed successfully.";
}

/* GET ALL REQUESTS */
$result = mysqli_query(
    $conn,
    "SELECT * FROM payment_requests ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Payments - Admin Portal</title>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary: #0076fe;
            --secondary: #8b5cf6;
            --dark: #0f172a;
            --text-muted: #64748b;
            --glass-bg: rgba(255, 255, 255, 0.93);
            --glass-border: rgba(255, 255, 255, 0.4);
            
            --success: #10b981;
            --warning: #f59e0b;
            --info: #3b82f6;
            --danger: #ef4444;

            --dark-bg: #090d16;
            --text-main: #f8fafc;
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

        /* --- CONTAINER & WRAPPER --- */
        .main-wrapper {
            flex: 1 0 auto;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 40px 16px;
            width: 100%;
        }

        .container {
            max-width: 1350px;
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

        /* --- RESPONSIVE TABLE --- */
        .table-wrapper {
            overflow-x: auto;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            background: #f8fafc;
            color: var(--dark);
            padding: 16px;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
            white-space: nowrap;
        }

        td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            color: var(--dark);
            font-weight: 600;
            font-size: 0.92rem;
            vertical-align: middle;
            white-space: nowrap;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background: #f8fafc;
        }

        /* --- STATUS BADGES --- */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.82rem;
            font-weight: 700;
            text-transform: capitalize;
        }

        .badge.pending {
            background: rgba(245, 158, 11, 0.15);
            color: #d97706;
        }

        .badge.approved {
            background: rgba(59, 130, 246, 0.15);
            color: #2563eb;
        }

        .badge.paid {
            background: rgba(16, 185, 129, 0.15);
            color: #059669;
        }

        .badge.rejected {
            background: rgba(239, 68, 68, 0.15);
            color: #dc2626;
        }

        /* --- ACTION BUTTONS --- */
        .btn-action-group {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn {
            text-decoration: none;
            color: white;
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 0.82rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .btn.approve {
            background: linear-gradient(135deg, #2563eb, #3b82f6);
        }

        .btn.reject {
            background: linear-gradient(135deg, #dc2626, #ef4444);
        }

        .btn.pay {
            background: linear-gradient(135deg, #059669, #10b981);
        }

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

        /* --- FULL-WIDTH SYSTEM FOOTER --- */
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
            color: var(--text-footer-muted);
            border-top: 1px solid var(--dark-border);
            padding-top: 14px;
        }

        .copyright strong {
            color: var(--text-main);
        }

        @media (max-width: 768px) {
            .container {
                padding: 25px 20px;
            }

            h1 {
                font-size: 1.4rem;
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
    <div class="container">

        <div class="header-bar">
            <h1>
                <i class="fa-solid fa-money-bill-transfer"></i>
                Manage Employee Payments
            </h1>

            <a href="super_admin_dashboard.php" class="back-btn">
                <i class="fa-solid fa-arrow-left"></i>
                Back to Dashboard
            </a>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Employee Name</th>
                        <th>Code</th>
                        <th>Amount</th>
                        <th>Currency</th>
                        <th>Method</th>
                        <th>Month / Year</th>
                        <th>Status</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if(mysqli_num_rows($result) > 0){
                        while($row = mysqli_fetch_assoc($result)){ 
                            $status_str = $row['status'] ?? 'Pending';
                            $status_class = strtolower($status_str);
                    ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($row['employee_name']); ?></strong></td>
                        <td><code><?php echo htmlspecialchars($row['employee_code']); ?></code></td>
                        <td style="color: var(--primary); font-weight: 700;"><?php echo number_format($row['amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($row['currency']); ?></td>
                        <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                        <td><?php echo htmlspecialchars($row['request_month']) . " " . htmlspecialchars($row['request_year']); ?></td>
                        <td>
                            <span class="badge <?php echo $status_class; ?>">
                                <i class="fa-solid fa-circle" style="font-size: 0.45rem;"></i>
                                <?php echo htmlspecialchars($status_str); ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-action-group" style="justify-content: center;">
                                <?php if($status_str == 'Pending'){ ?>
                                    <a href="?approve=<?php echo $row['id']; ?>" class="btn approve">
                                        <i class="fa-solid fa-check"></i> Approve
                                    </a>
                                    <a href="?reject=<?php echo $row['id']; ?>" class="btn reject">
                                        <i class="fa-solid fa-xmark"></i> Reject
                                    </a>
                                <?php } ?>

                                <?php if($status_str == 'Approved'){ ?>
                                    <a href="?pay=<?php echo $row['id']; ?>" class="btn pay">
                                        <i class="fa-solid fa-sack-dollar"></i> Mark Paid
                                    </a>
                                <?php } ?>

                                <?php if($status_str == 'Paid' || $status_str == 'Rejected'){ ?>
                                    <span style="color: var(--text-muted); font-size: 0.85rem; font-style: italic;">No actions</span>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                    ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 35px; color: var(--text-muted);">
                            <i class="fa-solid fa-folder-open" style="font-size: 2rem; margin-bottom: 10px; display: block; opacity: 0.5;"></i>
                            No payment requests found in the system.
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php if(!empty($success_message)){ ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Action Complete',
    text: '<?php echo $success_message; ?>',
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