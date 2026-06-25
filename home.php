<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

// Check role safely if it exists, otherwise default to normal employee
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'employee';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-blue: #0076fe;
            --dark-nav: #222222;
            --text-light: #ffffff;
            --text-dark: #333333;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            /* --- BACKGROUND IMAGE CONFIGURATION --- */
            background-image: url("image.png");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed; /* Keeps the background static while scrolling */
            
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* --- HEADER SECTION --- */
        .header {
            background-color: rgba(0, 118, 254, 0.9); /* Added transparency so background shows through slightly */
            color: var(--text-light);
            text-align: center;
            padding: 45px 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
            font-weight: 400;
        }
        
        .header p span {
            font-weight: 600;
            text-decoration: underline;
        }

        /* --- NAVIGATION MENU --- */
        .menu {
            background-color: rgba(34, 34, 34, 0.95); /* Semi-transparent navigation */
            display: flex;
            justify-content: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.15);
        }

        .menu a {
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            padding: 16px 28px;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .menu a:hover, .menu a.active {
            color: var(--text-light);
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .menu a.active {
            border-bottom: 3px solid var(--primary-blue);
        }

        /* --- CONTENT DESCRIPTION CARD --- */
        .content {
            max-width: 1000px;
            width: 90%;
            margin: 60px auto;
            /* Frosted glass effect makes text readable on any background image */
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            border: 1px solid rgba(255,255,255,0.7);
            text-align: center;
        }

        /* Watermark Logo Icon Styling */
        .system-logo-icon {
            font-size: 4rem;
            color: var(--primary-blue);
            margin-bottom: 20px;
            opacity: 0.9;
            animation: pulse 3s infinite ease-in-out;
        }

        .content h2 {
            font-size: 1.8rem;
            color: #111827;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .content p.main-description {
            color: #374151;
            font-size: 1.1rem;
            line-height: 1.7;
            margin-bottom: 35px;
        }

        /* --- ABOUT SYSTEM DYNAMIC FEATURES GRID --- */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
            text-align: left;
            margin-top: 25px;
        }

        .feature-card {
            background: #ffffff;
            padding: 22px;
            border-radius: 14px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.02);
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.08);
        }

        .feature-card h3 {
            font-size: 1.15rem;
            color: #1f2937;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .feature-card p {
            font-size: 0.9rem;
            color: #6b7280;
            line-height: 1.6;
        }

        /* --- FEATURE CATEGORY ICON HEADERS --- */
        .feature-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 15px;
        }

        .registration { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .attendance { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .reports { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .roles { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
        .secure { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.04); }
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Employee Management System</h1>
        <p>Welcome, <span><?php echo htmlspecialchars($_SESSION['username']); ?></span></p>
    </div>

    <div class="menu">
        <a href="home.php" class="active"><i class="fa-solid fa-house"></i> Home</a>
        <a href="#" onclick="checkAdminAccess(event, 'employees.php')"><i class="fa-solid fa-users"></i> Employees</a>
        <a href="attendance.php"><i class="fa-solid fa-fingerprint"></i> Attendance</a>
        <a href="#" onclick="checkAdminAccess(event, 'reports.php')"><i class="fa-solid fa-chart-line"></i> Reports</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>

    <div class="content">
        <div class="system-logo-icon">
            <i class="fa-solid fa-laptop-code"></i>
        </div>
        <h2>About Employee Management System</h2>
        <p class="main-description">
            Welcome to the centralized Employee Management Portal. This application streamlines workforce operations, handles identity logging, manages dynamic system administration assignments, and compiles localized analytics. Navigate through the top controls to access your tools relative to your access clearance profile.
        </p>

        <div class="features-grid">

            <div class="feature-card">
                <div class="feature-icon registration"><i class="fa-solid fa-user-plus"></i></div>
                <h3>Employee Registration</h3>
                <p>Onboard new staff members instantly with a secure input architecture. Capture details including departments, assigned job profiles, and contact data safely.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon attendance"><i class="fa-solid fa-fingerprint"></i></div>
                <h3>Attendance Tracking</h3>
                <p>Log accurate check-in records daily. System features drop-down status mapping selectors (Present, Absent, Late) to build clear, reliable workforce timelines seamlessly.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon reports"><i class="fa-solid fa-chart-pie"></i></div>
                <h3>Report Generation</h3>
                <p>Generate clean, professional analytical breakdowns. Pull live data counts across your overall employee registry, actively tracked business departments, and active admin profiles.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon roles"><i class="fa-solid fa-user-shield"></i></div>
                <h3>Role Management</h3>
                <p>Enforce strict operational permission layers. Regular employees view standard dashboards, while Admins and Super Admins unlock backend control configurations.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon secure"><i class="fa-solid fa-lock"></i></div>
                <h3>Secure Login</h3>
                <p>Protected by active PHP session verification systems. Enforces authentication boundaries to keep system routes inaccessible to anonymous or malicious web traffic.</p>
            </div>

        </div>
    </div>

    <script>
    const currentUserRole = "<?php echo $user_role; ?>";

    function checkAdminAccess(event, redirectUrl) {
        event.preventDefault();
        
        // If they are trying to go to employees.php, strictly restrict it to super_admin
        if (redirectUrl === 'employees.php') {
            if (currentUserRole === 'super_admin') {
                window.location.href = redirectUrl;
            } else {
                alert("sorry only super amin is allowed to acces this page THANK YOU");
            }
        } 
        // For other pages (like reports.php), allow both admin and super_admin
        else {
            if (currentUserRole === 'admin' || currentUserRole === 'super_admin') {
                window.location.href = redirectUrl;
            } else {
                alert("Sorry, only admins and super admin are allowed to use this page.");
            }
        }
    }

    function collectBiometric(event) {
        event.preventDefault();
        alert("Initializing Biometric Scan...\nScanning fingerprint sensor... Success! Attendance recorded securely for today.");
    }
</script>

</body>
</html>