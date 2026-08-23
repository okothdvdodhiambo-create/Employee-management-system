<?php
session_start();
include("connect.php");

// Clean check: Require admin or super_admin role clearance
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'super_admin')) {
    die("<div style='font-family:Segoe UI; padding:20px; color:#ef4444; font-weight:bold;'>Access Denied. You do not have clearance to view this page.</div>");
}

$alert_triggered = false;
$error_message = "";

if (isset($_POST['submit'])) {
    $employee_id = $_POST['employee_id'];
    $attendance_date = $_POST['attendance_date'];
    $check_in = $_POST['check_in'];
    $status = $_POST['status'];

    // Secure Prepared Statement
    $stmt = $conn->prepare("INSERT INTO attendance (employee_id, attendance_date, check_in, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $employee_id, $attendance_date, $check_in, $status);
    
    if ($stmt->execute()) {
        $alert_triggered = true;
    } else {
        $error_message = "Failed to record attendance: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Attendance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-blue: #0076fe;
            --accent-purple: #8b5cf6;
            --text-dark: #1f2937;
            --card-bg: rgba(255, 255, 255, 0.88);
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
            min-height: 100vh;
        }

        body {
            background-image: url("YOUR_IMAGE_URL_HERE"); 
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            display: flex;
            flex-direction: column;
        }

        .main-wrapper {
            flex: 1 0 auto;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        /* --- FROSTED CONTAINER BOX --- */
        .form-container {
            max-width: 500px;
            width: 100%;
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.25);
            border: 2px solid var(--primary-blue); 
            transition: transform 0.3s ease;
        }

        .form-container:hover {
            transform: translateY(-2px);
        }

        h2 {
            text-align: center;
            font-size: 1.8rem;
            color: #111827;
            font-weight: 700;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        h2 i {
            color: var(--primary-blue);
        }

        .form-group {
            margin-bottom: 18px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        label {
            font-weight: 600;
            color: #4b5563;
            font-size: 0.9rem;
        }

        select, input[type="date"], input[type="time"] {
            width: 100%;
            padding: 12px 14px;
            font-size: 0.95rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.9);
            outline: none;
            color: var(--text-dark);
            transition: all 0.2s ease;
        }

        select:focus, input[type="date"]:focus, input[type="time"]:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(0, 118, 254, 0.15);
            background: #ffffff;
        }

        button[type="submit"] {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--accent-purple) 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 118, 254, 0.2);
            transition: all 0.2s ease;
            margin-top: 15px;
        }

        button[type="submit"]:hover {
            box-shadow: 0 6px 16px rgba(0, 118, 254, 0.35);
        }

        button[type="submit"]:active {
            transform: scale(0.99);
        }

        /* --- ACTION LINKS INSIDE CONTAINER --- */
        .navigation-links {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid rgba(0, 0, 0, 0.08);
            font-size: 0.9rem;
        }

        .nav-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
        }

        .nav-link:hover {
            color: var(--accent-purple);
        }

        .error-alert {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 0.9rem;
            border: 1px solid #fca5a5;
        }

        /* --- FULL-WIDTH FOOTER --- */
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
            box-sizing: border-box;
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
            background: linear-gradient(135deg, var(--primary-blue), var(--accent-purple));
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

            .navigation-links {
                flex-direction: column;
                gap: 12px;
            }
        }
    </style>
</head>
<body>

<div class="main-wrapper">
    <div class="form-container">

        <h2><i class="fa-solid fa-fingerprint"></i> Record Attendance</h2>

        <?php if(!empty($error_message)): ?>
            <div class="error-alert">
                <i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label>Employee Name</label>
                <select name="employee_id" required>
                    <option value="">-- Choose Active Employee --</option>
                    <?php
                    $employees = mysqli_query($conn, "SELECT id, fullname FROM details");
                    while($row = mysqli_fetch_assoc($employees)){
                        echo "<option value='".htmlspecialchars($row['id'])."'>".htmlspecialchars($row['fullname'])."</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>Attendance Logging Date</label>
                <input type="date" name="attendance_date" value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="form-group">
                <label>Check In Time</label>
                <input type="time" name="check_in" required>
            </div>

            <div class="form-group">
                <label>Attendance Status Profile</label>
                <select name="status">
                    <option value="Present">🟢 Present</option>
                    <option value="Absent">🔴 Absent</option>
                    <option value="Late">🟡 Late</option>
                </select>
            </div>

            <button type="submit" name="submit">Save Log Record</button>

        </form>

        <div class="navigation-links">
            <a href="<?php echo ($_SESSION['role'] == 'super_admin') ? 'super_admin_dashboard.php' : 'home.php'; ?>" class="nav-link">
                <i class="fa-solid fa-arrow-left"></i> Dashboard
            </a>
            <a href="view_attendance.php" class="nav-link">
                View Records <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

    </div>
</div>

<?php if($alert_triggered): ?>
<script>
    alert('Attendance Recorded Successfully');
</script>
<?php endif; ?>

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
            <a href="https://facebook.com" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="https://instagram.com" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="https://x.com" target="_blank" aria-label="X (Twitter)"><i class="fab fa-x-twitter"></i></a>
            <a href="https://linkedin.com" target="_blank" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
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