<?php
session_start();
include("connect.php");

// Clean check: Require admin or super_admin role clearance
if(!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'super_admin')){
    die("<div style='font-family:Segoe UI; padding:20px; color:#ef4444; font-weight:bold;'>Access Denied. You do not have clearance to view this page.</div>");
}

$alert_triggered = false;

if(isset($_POST['submit'])){
    $employee_id = $_POST['employee_id'];
    $attendance_date = $_POST['attendance_date'];
    $check_in = $_POST['check_in'];
    $status = $_POST['status'];

    // Secure Prepared Statement to safeguard database execution
    $stmt = $conn->prepare("INSERT INTO attendance (employee_id, attendance_date, check_in, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $employee_id, $attendance_date, $check_in, $status);
    
    if($stmt->execute()){
        $alert_triggered = true;
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
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            /* Matches your unified background template layout */
            background-image: url("YOUR_IMAGE_URL_HERE"); 
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
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
            /* Colorful, vibrant structural framing border */
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

        /* --- CUSTOM FORM CONTROL ARCHITECTURE --- */
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

        /* Global formatting adjustments for drop-downs, date, and time elements */
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

        /* Vibrant active state glow effects */
        select:focus, input[type="date"]:focus, input[type="time"]:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(0, 118, 254, 0.15);
            background: #ffffff;
        }

        /* --- BOLD SAVE ACTION BUTTON --- */
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

        /* --- DASHBOARD NAVIGATION FOOTER LINK --- */
        .back-container {
            text-align: center;
            margin-top: 25px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .back-link:hover {
            color: var(--accent-purple);
            transform: translateX(-2px);
        }
    </style>
</head>
<body>

<div class="form-container">

    <h2><i class="fa-solid fa-fingerprint"></i> Record Attendance</h2>

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

    <div class="back-container">
        <a href="<?php echo ($_SESSION['role'] == 'super_admin') ? 'super_admin_dashboard.php' : 'home.php'; ?>" class="back-link">
            <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

</div>

<?php if($alert_triggered): ?>
<script>
    alert('Attendance Recorded Successfully');
</script>
<?php endif; ?>
<br><br>

<a href="view_attendance.php">
    View Attendance Records
</a>

</body>
</html>