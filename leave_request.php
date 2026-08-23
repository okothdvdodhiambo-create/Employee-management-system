<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("connect.php");

$message = "";
$message_type = ""; // Track status for styling the toast ('success' or 'error')

if(isset($_POST['submit_leave'])){

    $employee_id = intval($_POST['employee_id']);
    $leave_type = $_POST['leave_type'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $reason = trim($_POST['reason']);

    // Input Validation Guard: Ensure chronological accuracy
    if (strtotime($start_date) > strtotime($end_date)) {
        $message = "End date cannot be earlier than start date.";
        $message_type = "error";
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO leave_requests (employee_id, leave_type, start_date, end_date, reason) VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->bind_param("issss", $employee_id, $leave_type, $start_date, $end_date, $reason);

        if($stmt->execute()){
            $message = "Leave Request Submitted Successfully";
            $message_type = "success";
        } else {
            $message = "Error Submitting Request";
            $message_type = "error";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Request Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-blue: #0076fe;
            --primary-hover: #005ecb;
            --text-dark: #333333;
            --error-red: #ef4444;
            --success-green: #10b981;
            --glass-border: rgba(255, 255, 255, 0.25);
            --card-bg: rgba(255, 255, 255, 0.88);

            /* Footer Variables */
            --primary: #0076fe;
            --dark-bg: #22252a;          /* Charcoal main bar */
            --copyright-bg: #191b1f;      /* Darker bottom strip */
            --text-main: #ffffff;
            --text-footer-muted: #94a3b8;
            --dark-border: rgba(255, 255, 255, 0.2);
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
            overflow-x: hidden;
        }

        /* --- MODERN MOVING GRADIENT BACKGROUND --- */
        body {
            background: linear-gradient(-45deg, #0f172a, #1e3a8a, #0d9488, #111827);
            background-size: 400% 400%;
            animation: movingGradient 15s ease infinite;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        @keyframes movingGradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* --- MAIN PAGE CONTENT WRAPPER --- */
        .page-content {
            flex: 1 0 auto;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            width: 100%;
        }

        /* --- GLASSMORPHIC FORM CARD CONTAINER --- */
        .container {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            max-width: 480px;
            width: 100%;
            padding: 40px 35px;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            border: 1px solid var(--glass-border);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .container:hover {
            transform: translateY(-2px);
        }

        .logo-area {
            font-size: 2.8rem;
            color: var(--primary-blue);
            margin-bottom: 12px;
        }

        h2 {
            font-size: 1.65rem;
            color: #111827;
            font-weight: 700;
            margin-bottom: 25px;
        }

        /* --- STRUCTURED STRUCTURAL INPUT GROUPS --- */
        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .input-group label {
            display: block;
            font-size: 0.88rem;
            font-weight: 600;
            color: #4b5563;
            margin-bottom: 6px;
            letter-spacing: 0.3px;
        }

        .input-group input, 
        .input-group select, 
        .input-group textarea {
            width: 100%;
            padding: 13px 16px;
            font-size: 0.95rem;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.9);
            outline: none;
            transition: all 0.2s ease;
            color: var(--text-dark);
        }

        .input-group input:focus, 
        .input-group select:focus, 
        .input-group textarea:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(0, 118, 254, 0.15);
            background: #ffffff;
        }

        /* Flexible inline wrapper for the two dates */
        .date-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        /* --- MODERN SUBMIT BUTTON --- */
        button[type="submit"] {
            width: 100%;
            padding: 14px;
            background-color: var(--primary-blue);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 5px;
            box-shadow: 0 4px 12px rgba(0, 118, 254, 0.2);
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        button[type="submit"]:hover {
            background-color: var(--primary-hover);
            box-shadow: 0 6px 16px rgba(0, 118, 254, 0.3);
        }

        button[type="submit"]:active {
            transform: scale(0.98);
        }

        /* --- SLIDING TOAST BANNER SYSTEM --- */
        .toast-container {
            position: fixed;
            top: 25px;
            right: 25px;
            z-index: 9999;
        }

        .toast-notification {
            background: #ffffff;
            padding: 16px 22px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.95rem;
            font-weight: 600;
            min-width: 320px;
            transform: translateX(120%);
            animation: slideIn 0.4s forwards, fadeOut 0.5s ease 3.5s forwards;
            text-align: left;
        }

        .toast-notification.success {
            border-left: 5px solid var(--success-green);
            color: var(--success-green);
        }

        .toast-notification.error {
            border-left: 5px solid var(--error-red);
            color: var(--error-red);
        }

        @keyframes slideIn { to { transform: translateX(0); } }
        @keyframes fadeOut { to { opacity: 0; transform: translateY(-20px); pointer-events: none; } }

        /* --- FOOTER SEPARATOR --- */
        .footer-note {
            margin-top: 25px;
            padding-top: 15px;
            text-align: center;
            font-size: 0.85rem;
            line-height: 1.6;
            font-style: italic;
            border-top: 1px solid rgba(0, 0, 0, 0.06);
        }
        .footer-note p { font-weight: 600; }
        .footer-note p:nth-of-type(odd) { color: #1e3a8a; }
        .footer-note p:nth-of-type(even) { color: #0d9488; }

        /* --- FULL WIDTH FOOTER --- */
        .system-footer {
            margin-top: auto;
            width: 100%;
            background-color: var(--dark-bg);
            display: flex;
            flex-direction: column;
        }

        .footer-main-row {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 25px 20px;
            gap: 30px;
            width: 100%;
        }

        .footer-logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .footer-logo img {
            height: 45px;
            width: auto;
            object-fit: contain;
        }

        .footer-logo h3 {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .footer-divider {
            width: 1px;
            height: 35px;
            background-color: var(--dark-border);
        }

        .social-links {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .social-links a {
            color: var(--text-main);
            font-size: 1.2rem;
            text-decoration: none;
            transition: color 0.2s ease, transform 0.2s ease;
        }

        .social-links a:hover {
            color: var(--primary);
            transform: translateY(-2px);
        }

        .copyright {
            background-color: var(--copyright-bg);
            text-align: center;
            padding: 14px 20px;
            font-size: 0.85rem;
            color: var(--text-footer-muted);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        @media (max-width: 768px) {
            .footer-main-row {
                flex-direction: column;
                gap: 15px;
            }
            .footer-divider {
                width: 80px;
                height: 1px;
            }
        }
    </style>
</head>
<body>

<!-- SLIDING ENGINE TOAST SYSTEM -->
<div class="toast-container" id="toastContainer">
    <?php if(!empty($message)): ?>
        <div class="toast-notification <?php echo $message_type; ?>">
            <?php if($message_type === 'success'): ?>
                <i class="fa-solid fa-circle-check"></i>
            <?php else: ?>
                <i class="fa-solid fa-triangle-exclamation"></i>
            <?php endif; ?>
            <span><?php echo $message; ?></span>
        </div>
    <?php endif; ?>
</div>

<div class="page-content">

    <div class="container">
        <div class="logo-area">
            <i class="fa-solid fa-calendar-plus"></i>
        </div>
        
        <h2>Leave Request Form</h2>

        <form method="POST" autocomplete="off">
            
            <div class="input-group">
                <label for="employee_id">Employee Identification Number</label>
                <input type="number" id="employee_id" name="employee_id" placeholder="e.g. 1042" required>
            </div>

            <div class="input-group">
                <label for="leave_type">Category of Leave Required</label>
                <select id="leave_type" name="leave_type">
                    <option value="Annual Leave">Annual Leave</option>
                    <option value="Sick Leave">Sick Leave</option>
                    <option value="Maternity Leave">Maternity Leave</option>
                    <option value="Emergency Leave">Emergency Leave</option>
                </select>
            </div>

            <!-- Inline layout configuration for dates alignment -->
            <div class="date-row">
                <div class="input-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" required>
                </div>

                <div class="input-group">
                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date" required>
                </div>
            </div>

            <div class="input-group">
                <label for="reason">Detailed Explanatory Reason</label>
                <textarea id="reason" name="reason" placeholder="Provide contextual validation for your request..." required></textarea>
            </div>

            <button type="submit" name="submit_leave">
                <i class="fa-solid fa-paper-plane"></i> Submit Request
            </button>
        </form>

        <div class="footer-note">
            <p>All requested allocations are evaluated through system protocols.</p>
            <p>Please ensure supporting records are ready for review if requested.</p>
        </div>
    </div>

</div>

<!-- END-TO-END FOOTER -->
<footer class="system-footer">
    <div class="footer-main-row">
        <div class="footer-logo">
            <img src="image.png" alt="EMS Logo">
            <h3>Employee Management System</h3>
        </div>

        <div class="footer-divider"></div>

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
    </div>

    <p class="copyright">
        © <?php echo date('Y'); ?> Employee Management System &bull; 
        Developed by <strong>David Okoth</strong> &bull; 
        All Rights Reserved.
    </p>
</footer>

<script>
    // System clear out timer for sliding toast elements
    const runningToast = document.querySelector('.toast-notification');
    if(runningToast) {
        setTimeout(() => {
            runningToast.remove();
        }, 4100); 
    }
</script>

</body>
</html>