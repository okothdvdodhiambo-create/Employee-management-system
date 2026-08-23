<?php
session_start();
include("connect.php");

// Safety Check: Ensure user is logged in
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

// Fetch user's active role fallback configuration
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'employee';

/* 1. Total Employees Count */
$employee_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM details");
$employee_data = mysqli_fetch_assoc($employee_query);
$totalEmployees = $employee_data['total'];

/* 2. Total Unique Departments Count */
$dept_query = mysqli_query($conn, "SELECT COUNT(DISTINCT department) AS total FROM details");
$dept_data = mysqli_fetch_assoc($dept_query);
$totalDepartments = $dept_data['total'];

/* 3. Total Admins / System Users Count */
$admin_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM employees");
$admin_data = mysqli_fetch_assoc($admin_query);
$totalAdmins = $admin_data['total'];

/* 4. Fetch the Latest 5 Attendance Logs for the Overview Panel */
$attendance_sql = "SELECT attendance.*, details.fullname 
                  FROM attendance 
                  INNER JOIN details ON attendance.employee_id = details.id
                  ORDER BY attendance.attendance_date DESC, attendance.check_in DESC 
                  LIMIT 5";
$attendance_result = mysqli_query($conn, $attendance_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Reports</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-blue: #0076fe;
            --accent-purple: #8b5cf6;
            --accent-cyan: #06b6d4;
            --accent-orange: #f59e0b;
            --text-dark: #1f2937;
            --card-bg: rgba(255, 255, 255, 0.9);
            
            /* Status Profile Palette */
            --status-present: #10b981;
            --status-absent: #ef4444;
            --status-late: #f59e0b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-image: url("YOUR_IMAGE_URL_HERE"); 
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* --- DASHBOARD WRAPPER PANEL --- */
        .dashboard-container {
            max-width: 1100px;
            width: 100%;
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 35px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.25);
            border: 2px solid var(--primary-blue);
        }

        h1 {
            font-size: 2rem;
            color: #111827;
            font-weight: 700;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        h1 i {
            color: var(--primary-blue);
        }

        /* --- STATISTICS GRID METRICS LAYOUT --- */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 35px;
        }

        .metric-card {
            background: rgb(255, 255, 255);
            padding: 24px;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.05);
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .metric-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px -5px rgba(0,0,0,0.15);
        }

        /* Top card color accent indicators */
        .metric-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
        }
        .card-emp::before { background-color: var(--primary-blue); }
        .card-dept::before { background-color: var(--accent-cyan); }
        .card-adm::before { background-color: var(--accent-purple); }

        .metric-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #6b7280;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 12px;
        }

        .metric-icon {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }
        .card-emp .metric-icon { background: rgba(0, 118, 254, 0.1); color: var(--primary-blue); }
        .card-dept .metric-icon { background: rgba(6, 182, 212, 0.1); color: var(--accent-cyan); }
        .card-adm .metric-icon { background: rgba(139, 92, 246, 0.1); color: var(--accent-purple); }

        .metric-number {
            font-size: 2rem;
            font-weight: 700;
            color: #111827;
        }

        /* --- ATTENDANCE RECENT LOGS SECTION --- */
        .section-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .view-all-btn {
            font-size: 0.85rem;
            background-color: rgba(0, 118, 254, 0.08);
            color: var(--primary-blue);
            padding: 6px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.2s;
        }
        .view-all-btn:hover {
            background-color: var(--primary-blue);
            color: #ffffff;
        }

        /* Table structural border accents */
        .custom-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 30px;
            border-radius: 12px;
            overflow: hidden;
            border: 2px solid var(--primary-blue); 
        }

        .custom-table th {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--accent-purple) 100%);
            color: #ffffff;
            font-weight: 600;
            padding: 14px 16px;
            text-align: left;
            font-size: 0.9rem;
        }

        .custom-table td {
            padding: 12px 16px;
            background-color: rgba(255, 255, 255, 0.65);
            color: var(--text-dark);
            font-size: 0.9rem;
            border-bottom: 1px solid rgba(0, 118, 254, 0.08);
        }

        .custom-table tr:hover td {
            background-color: rgba(0, 118, 254, 0.04);
        }

        .custom-table tr:last-child td {
            border-bottom: none;
        }

        /* Dynamic badges for statuses inside the log card */
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
        }
        .badge-present { background: rgba(16, 185, 129, 0.12); color: var(--status-present); border: 1px solid rgba(16, 185, 129, 0.2); }
        .badge-absent { background: rgba(239, 68, 68, 0.12); color: var(--status-absent); border: 1px solid rgba(239, 68, 68, 0.2); }
        .badge-late { background: rgba(245, 158, 11, 0.12); color: var(--status-late); border: 1px solid rgba(245, 158, 11, 0.2); }

        /* --- BACK TO DASHBOARD NAVIGATION FOOTER LINK --- */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
            font-size: 1rem;
        }

        .back-link:hover {
            color: var(--accent-purple);
            transform: translateX(-3px);
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

<div class="dashboard-container">

    <h1><i class="fa-solid fa-chart-pie"></i> System Analytical Reports</h1>

    <div class="metrics-grid">
        
        <div class="metric-card card-emp">
            <div class="metric-header">
                <span>TOTAL EMPLOYEES</span>
                <div class="metric-icon"><i class="fa-solid fa-users"></i></div>
            </div>
            <div class="metric-number"><?php echo $totalEmployees; ?></div>
        </div>

        <div class="metric-card card-dept">
            <div class="metric-header">
                <span>TOTAL DEPARTMENTS</span>
                <div class="metric-icon"><i class="fa-solid fa-building"></i></div>
            </div>
            <div class="metric-number"><?php echo $totalDepartments; ?></div>
        </div>

        <div class="metric-card card-adm">
            <div class="metric-header">
                <span>SYSTEM ADMINS</span>
                <div class="metric-icon"><i class="fa-solid fa-user-shield"></i></div>
            </div>
            <div class="metric-number"><?php echo $totalAdmins; ?></div>
        </div>

    </div>

    <div class="section-title">
        <span><i class="fa-solid fa-clock-rotate-left"></i> Recent Attendance Logs</span>
        <a href="attendance_records.php" class="view-all-btn">View All Records</a>
    </div>

    <table class="custom-table">
        <thead>
            <tr>
                <th>Employee Name</th>
                <th>Logging Date</th>
                <th>Check In Time</th>
                <th>Status Profile</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if(mysqli_num_rows($attendance_result) > 0) {
                while($row = mysqli_fetch_assoc($attendance_result)){ 
                    $status = $row['status'];
                    $badge_class = 'badge-present'; // default fallback
                    
                    if(strcasecmp($status, 'Absent') == 0 || strpos($status, 'Absent') !== false) {
                        $badge_class = 'badge-absent';
                    } else if(strcasecmp($status, 'Late') == 0 || strpos($status, 'Late') !== false) {
                        $badge_class = 'badge-late';
                    }
                    
                    $clean_text = str_replace(array('🟢', '🔴', '🟡'), '', $status);
            ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($row['fullname']); ?></strong></td>
                <td><?php echo date('M d, Y', strtotime($row['attendance_date'])); ?></td>
                <td><?php echo date('h:i A', strtotime($row['check_in'])); ?></td>
                <td>
                    <span class="status-badge <?php echo $badge_class; ?>">
                        <?php echo htmlspecialchars(trim($clean_text)); ?>
                    </span>
                </td>
            </tr>
            <?php 
                } 
            } else { 
            ?>
            <tr>
                <td colspan="4" style="text-align: center; color: #9ca3af; padding: 20px;">
                    No recent attendance logs discovered.
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <a href="<?php echo ($user_role == 'super_admin') ? 'super_admin_dashboard.php' : 'home.php'; ?>" class="back-link">
        <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
    </a>

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