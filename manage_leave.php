<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("connect.php");

// Fetching leave requests from the data tier
$result = mysqli_query(
    $conn,
    "SELECT * FROM leave_requests ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Leave Requests</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-blue: #0076fe;
            --primary-hover: #005ecb;
            --success-green: #10b981;
            --success-hover: #059669;
            --danger-red: #ef4444;
            --danger-hover: #dc2626;
            --warning-amber: #f59e0b;
            --glass-border: rgba(255, 255, 255, 0.25);
            --card-bg: rgba(255, 255, 255, 0.88);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* --- MODERN MOVING GRADIENT BACKGROUND --- */
        body {
            background: linear-gradient(-45deg, #0f172a, #1e3a8a, #0d9488, #111827);
            background-size: 400% 400%;
            animation: movingGradient 15s ease infinite;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        @keyframes movingGradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* --- GLASSMORPHIC MAIN CONTAINER --- */
        .container {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            max-width: 1100px;
            width: 100%;
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid var(--glass-border);
        }

        /* --- TITLE LOGIC --- */
        .header-area {
            border-bottom: 2px solid rgba(0, 0, 0, 0.06);
            padding-bottom: 20px;
            margin-bottom: 25px;
        }

        .header-area h2 {
            font-size: 1.7rem;
            color: #0f172a;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-area h2 i {
            color: var(--primary-blue);
        }

        /* --- MODERN RESPONSIVE DATA TABLE --- */
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

        /* --- DINAMIC STATUS BADGES --- */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.8rem;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .status-badge.pending {
            background: rgba(245, 158, 11, 0.12);
            color: var(--warning-amber);
        }

        .status-badge.approved {
            background: rgba(16, 185, 129, 0.12);
            color: var(--success-green);
        }

        .status-badge.rejected {
            background: rgba(239, 68, 68, 0.12);
            color: var(--danger-red);
        }

        /* --- ACTION WORKFLOW BUTTONS --- */
        .action-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .btn-action {
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 0.88rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .btn-action.approve {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-green);
        }

        .btn-action.approve:hover {
            background: var(--success-green);
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        .btn-action.reject {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-red);
        }

        .btn-action.reject:hover {
            background: var(--danger-red);
            color: white;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }

        /* --- SYSTEM FOOTER --- */
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
    <!-- TOP WORKSPACE HEADER -->
    <div class="header-area">
        <h2><i class="fa-solid fa-list-check"></i> Manage Leave Requests Workspace</h2>
    </div>

    <!-- MAIN DATA TABLE ELEMENT -->
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Employee ID</th>
                    <th>Leave Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Administrative Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <?php 
                        // Normalize database status text for selector styling classes
                        $current_status = strtolower($row['status'] ?? 'pending'); 
                    ?>
                    <tr>
                        <td style="font-weight: 600; color: #94a3b8;">#<?php echo $row['id']; ?></td>
                        <td style="font-weight: 600; color: #0f172a;">
                            <i class="fa-solid fa-user-tie" style="color: #64748b; margin-right: 6px;"></i>
                            <?php echo htmlspecialchars($row['employee_id']); ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['leave_type']); ?></td>
                        <td><i class="fa-regular fa-calendar-minus" style="color: #94a3b8;"></i> <?php echo $row['start_date']; ?></td>
                        <td><i class="fa-regular fa-calendar-check" style="color: #94a3b8;"></i> <?php echo $row['end_date']; ?></td>
                        <td>
                            <span class="status-badge <?php echo $current_status; ?>">
                                <?php if($current_status === 'approved'): ?>
                                    <i class="fa-solid fa-circle-check"></i>
                                <?php elseif($current_status === 'rejected'): ?>
                                    <i class="fa-solid fa-circle-xmark"></i>
                                <?php else: ?>
                                    <i class="fa-solid fa-circle-dot"></i>
                                <?php endif; ?>
                                <?php echo htmlspecialchars($row['status'] ?? 'Pending'); ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-actions">
                                <a href="approve_leave.php?id=<?php echo $row['id']; ?>" class="btn-action approve">
                                    <i class="fa-solid fa-check"></i> Approve
                                </a>
                                <a href="reject_leave.php?id=<?php echo $row['id']; ?>" class="btn-action reject">
                                    <i class="fa-solid fa-xmark"></i> Reject
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: #94a3b8; padding: 40px;">
                            <i class="fa-solid fa-inbox" style="font-size: 2rem; display: block; margin-bottom: 10px; color: #cbd5e1;"></i>
                            No leave allocation records found in this partition stack.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- INTEGRATED SYSTEM FOOTER -->
    <div class="footer-note">
        <p>All administrative requests updated here will automatically update the tracking logs.</p>
        <p>Make sure to review internal coverage allocations before approving requests.</p>
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