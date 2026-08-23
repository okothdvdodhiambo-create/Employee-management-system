<?php
session_start();
include("connect.php");

// Safety & Security Role Check
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'super_admin'){
    header("Location: home.php");
    exit();
}

// Fetch employees list
$result = mysqli_query($conn, "SELECT * FROM employees");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admins</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-blue: #0076fe;
            --accent-purple: #8b5cf6;
            --text-dark: #1f2937;
            --card-bg: rgba(255, 255, 255, 0.9);
            
            /* Dynamic Badge Colors */
            --badge-super-admin: #8b5cf6;
            --badge-admin: #3b82f6;
            --badge-employee: #6c757d;

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

        body {
            background-color: #f4f6f9;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* --- MAIN PAGE CONTENT WRAPPER --- */
        .page-content {
            flex: 1 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
            width: 100%;
        }

        /* --- CONTAINER HOLDER --- */
        .table-container {
            max-width: 1000px;
            width: 100%;
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 35px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            border: 1px solid rgba(255,255,255,0.6);
        }

        h1 {
            font-size: 2rem;
            color: #111827;
            font-weight: 700;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        h1 i {
            color: var(--accent-purple);
        }

        /* --- VIBRANT BOLD TABLE --- */
        .custom-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 25px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 2px solid var(--accent-purple); 
        }

        /* Table Headers */
        .custom-table th {
            background: linear-gradient(135deg, var(--accent-purple) 0%, var(--primary-blue) 100%);
            color: #ffffff;
            font-weight: 600;
            padding: 16px;
            text-align: left;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
        }

        /* Table Body Data rows */
        .custom-table td {
            padding: 15px 16px;
            background-color: rgba(255, 255, 255, 0.9);
            color: var(--text-dark);
            font-size: 0.95rem;
            border-bottom: 1px solid rgba(139, 92, 246, 0.1);
            transition: background-color 0.2s ease;
        }

        /* Interactive row illumination effect on hover */
        .custom-table tr:hover td {
            background-color: rgba(139, 92, 246, 0.06);
        }

        .custom-table tr:last-child td {
            border-bottom: none;
        }

        /* --- DYNAMIC ROLE STATUS BADGES --- */
        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .role-super_admin {
            background-color: rgba(139, 92, 246, 0.15);
            color: var(--badge-super-admin);
            border: 1px solid rgba(139, 92, 246, 0.3);
        }

        .role-admin {
            background-color: rgba(59, 130, 246, 0.15);
            color: var(--badge-admin);
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .role-employee {
            background-color: rgba(108, 117, 125, 0.15);
            color: var(--badge-employee);
            border: 1px solid rgba(108, 117, 125, 0.3);
        }

        /* --- BACK NAVIGATION LINK --- */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--accent-purple);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
            font-size: 1rem;
        }

        .back-link:hover {
            color: var(--primary-blue);
            transform: translateX(-3px);
        }

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

<div class="page-content">

    <div class="table-container">

        <h1><i class="fa-solid fa-user-shield"></i> Manage Admins</h1>

        <table class="custom-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role System Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)){ 
                    // Determine current role class for dynamic badge coloring
                    $role = $row['role'];
                    $badge_class = 'role-employee'; // Default
                    $icon_class = 'fa-user';

                    if($role == 'super_admin') {
                        $badge_class = 'role-super_admin';
                        $icon_class = 'fa-shield-halved';
                    } else if($role == 'admin') {
                        $badge_class = 'role-admin';
                        $icon_class = 'fa-user-gear';
                    }
                ?>
                <tr>
                    <td><strong>#<?php echo $row['id']; ?></strong></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td>
                        <span class="role-badge <?php echo $badge_class; ?>">
                            <i class="fa-solid <?php echo $icon_class; ?>"></i>
                            <?php echo str_replace('_', ' ', $role); ?>
                        </span>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <a href="super_admin_dashboard.php" class="back-link">
            <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
        </a>

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

</body>
</html>