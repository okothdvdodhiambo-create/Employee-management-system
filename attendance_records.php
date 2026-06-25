<?php
session_start();
include("connect.php");

// Safety Check: Ensure user is logged in
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

// Fetch the user's role safely from the session
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'employee';

// Relational database lookup to pull ALL logs
$sql = "SELECT attendance.*, details.fullname 
        FROM attendance 
        INNER JOIN details ON attendance.employee_id = details.id
        ORDER BY attendance.attendance_date DESC, attendance.check_in DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Attendance Records</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-blue: #0076fe;
            --accent-purple: #8b5cf6;
            --text-dark: #1f2937;
            --card-bg: rgba(255, 255, 255, 0.9);
            --status-present: #10b981;
            --status-absent: #ef4444;
            --status-late: #f59e0b;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }

        body {
            background-image: url("YOUR_IMAGE_URL_HERE"); 
            background-size: cover; background-position: center; background-repeat: no-repeat; background-attachment: fixed;
            min-height: 100vh; padding: 40px 20px; display: flex; flex-direction: column; align-items: center;
        }

        .report-container {
            max-width: 1000px; width: 100%; background: var(--card-bg); backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px); padding: 35px; border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.25); border: 2px solid var(--primary-blue);
        }

        h2 { font-size: 2rem; color: #111827; font-weight: 700; margin-bottom: 25px; display: flex; align-items: center; gap: 12px; }
        h2 i { color: var(--primary-blue); }

        .custom-table { width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 25px; border-radius: 12px; overflow: hidden; border: 2px solid var(--primary-blue); }
        .custom-table th { background: linear-gradient(135deg, var(--primary-blue) 0%, var(--accent-purple) 100%); color: #ffffff; font-weight: 600; padding: 16px; text-align: left; }
        .custom-table td { padding: 15px 16px; background-color: rgba(255, 255, 255, 0.7); color: var(--text-dark); border-bottom: 1px solid rgba(0, 118, 254, 0.1); }
        .custom-table tr:hover td { background-color: rgba(0, 118, 254, 0.05); }
        .custom-table tr:last-child td { border-bottom: none; }

        .status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 14px; border-radius: 50px; font-size: 0.85rem; font-weight: 700; }
        .badge-present { background-color: rgba(16, 185, 129, 0.12); color: var(--status-present); border: 1px solid rgba(16, 185, 129, 0.25); }
        .badge-absent { background-color: rgba(239, 68, 68, 0.12); color: var(--status-absent); border: 1px solid rgba(239, 68, 68, 0.25); }
        .badge-late { background-color: rgba(245, 158, 11, 0.12); color: var(--status-late); border: 1px solid rgba(245, 158, 11, 0.25); }

        .back-link { display: inline-flex; align-items: center; gap: 8px; color: var(--primary-blue); text-decoration: none; font-weight: 600; transition: all 0.2s; }
        .back-link:hover { color: var(--accent-purple); transform: translateX(-3px); }
    </style>
</head>
<body>

<div class="report-container">
    <h2><i class="fa-solid fa-list-check"></i> Complete Attendance Records</h2>

    <table class="custom-table">
        <thead>
            <tr>
                <th>Employee</th>
                <th>Date</th>
                <th>Check In</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if(mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)){ 
                    $current_status = $row['status'];
                    $badge_style = 'badge-present';
                    
                    if(strcasecmp($current_status, 'Absent') == 0 || strpos($current_status, 'Absent') !== false) {
                        $badge_style = 'badge-absent';
                    } else if(strcasecmp($current_status, 'Late') == 0 || strpos($current_status, 'Late') !== false) {
                        $badge_style = 'badge-late';
                    }
                    
                    $clean_text = str_replace(array('🟢', '🔴', '🟡'), '', $current_status);
            ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($row['fullname']); ?></strong></td>
                <td><?php echo date('M d, Y', strtotime($row['attendance_date'])); ?></td>
                <td><?php echo date('h:i A', strtotime($row['check_in'])); ?></td>
                <td>
                    <span class="status-badge <?php echo $badge_style; ?>">
                        <?php echo htmlspecialchars(trim($clean_text)); ?>
                    </span>
                </td>
            </tr>
            <?php 
                } 
            } else { 
            ?>
            <tr>
                <td colspan="4" style="text-align: center; color: #9ca3af; padding: 30px;">No records found.</td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <a href="reports.php" class="back-link">
        <i class="fa-solid fa-arrow-left"></i> Back to Reports Summary
    </a>
</div>

</body>
</html>