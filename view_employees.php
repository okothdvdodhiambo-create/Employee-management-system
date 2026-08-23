<?php
session_start();
include("connect.php");

// Safety check: ensure user is logged in
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

// Fetch the user's role from the session (defaults to 'employee' if not explicitly set)
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'employee';

// Fetch details from database
$sql = "SELECT * FROM details";

if(isset($_GET['search']) && $_GET['search'] != ''){
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $sql = "SELECT * FROM details 
            WHERE fullname LIKE '%$search%' 
            OR email LIKE '%$search%' 
            OR department LIKE '%$search%' 
            OR position LIKE '%$search%' 
            OR employee_code LIKE '%$search%'";
}

if(isset($_GET['department']) && $_GET['department'] != ''){
    $department = mysqli_real_escape_string($conn, $_GET['department']);
    $sql = "SELECT * FROM details WHERE department='$department'";
}

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Employees</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-blue: #0076fe;
            --accent-purple: #8b5cf6;
            --danger-red: #ef4444;
            --success-green: #10b981;
            --text-dark: #1f2937;
            --card-bg: rgba(255, 255, 255, 0.95);
            
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

        /* --- CONTAINER PROFILE CARD --- */
        .table-container {
            max-width: 1100px;
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
            color: var(--primary-blue);
        }

        /* --- VIBRANT & INTERACTIVE TABLE --- */
        .custom-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 25px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 2px solid var(--primary-blue); 
        }

        .custom-table th {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--accent-purple) 100%);
            color: #ffffff;
            font-weight: 600;
            padding: 16px;
            text-align: left;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
        }

        .custom-table td {
            padding: 14px 16px;
            background-color: rgba(255, 255, 255, 0.9);
            color: var(--text-dark);
            font-size: 0.95rem;
            border-bottom: 1px solid rgba(0, 118, 254, 0.1);
            transition: background-color 0.2s ease;
        }

        .custom-table tr:hover td {
            background-color: rgba(0, 118, 254, 0.06);
        }

        .custom-table tr:last-child td {
            border-bottom: none;
        }

        /* --- INTERACTIVE ACTION LINK BUTTONS --- */
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            margin-right: 5px;
        }

        .btn-edit {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success-green);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        .btn-edit:hover {
            background-color: var(--success-green);
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);
        }

        .btn-delete {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--danger-red);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        .btn-delete:hover {
            background-color: var(--danger-red);
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2);
        }

        /* --- FOOTER BACK NAVIGATION ACTION --- */
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

        .employee-photo {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-blue);
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
        <h1><i class="fa-solid fa-users"></i> Registered Employees</h1>

        <!-- SEARCH AND FILTER ROW -->
        <div style="display:flex; gap:15px; flex-wrap:wrap; margin-bottom:25px;">
            <form method="GET" style="display:flex; gap:10px; flex-wrap:wrap;">
                <input
                    type="text"
                    name="search"
                    placeholder="Search Employee..."
                    value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                    style="padding:10px 14px; border-radius:8px; border:1px solid #cbd5e1; min-width:250px; outline:none;"
                >
                <button
                    type="submit"
                    style="background:#0076fe; color:white; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:600;"
                >
                    <i class="fa-solid fa-magnifying-glass"></i> Search
                </button>
            </form>

            <form method="GET" style="display:flex; gap:10px;">
                <select
                    name="department"
                    style="padding:10px 14px; border-radius:8px; border:1px solid #cbd5e1; outline:none;"
                >
                    <option value="">All Departments</option>
                    <option value="ICT" <?php if(isset($_GET['department']) && $_GET['department'] == 'ICT') echo 'selected'; ?>>ICT</option>
                    <option value="HR" <?php if(isset($_GET['department']) && $_GET['department'] == 'HR') echo 'selected'; ?>>HR</option>
                    <option value="Finance" <?php if(isset($_GET['department']) && $_GET['department'] == 'Finance') echo 'selected'; ?>>Finance</option>
                    <option value="Marketing" <?php if(isset($_GET['department']) && $_GET['department'] == 'Marketing') echo 'selected'; ?>>Marketing</option>
                </select>
                <button
                    type="submit"
                    style="background:#8b5cf6; color:white; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:600;"
                >
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
            </form>
        </div>

        <!-- TABLE -->
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Photo</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th>Phone</th>
                    <th>Salary</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)){ ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['employee_code']); ?></strong></td>
                    <td>
                        <?php if(!empty($row['photo'])){ ?>
                            <img src="uploads/<?php echo htmlspecialchars($row['photo']); ?>" class="employee-photo" alt="Photo">
                        <?php } else { ?>
                            <span style="color:#94a3b8; font-size:0.85rem;">No Photo</span>
                        <?php } ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['department']); ?></td>
                    <td><?php echo htmlspecialchars($row['position']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td><?php echo htmlspecialchars($row['salary']); ?></td>
                    <td>
                        <?php if($user_role == 'super_admin'){ ?>
                            <a href="edit_employee.php?id=<?php echo $row['id']; ?>" class="btn-action btn-edit">
                                <i class="fa-solid fa-user-pen"></i> Edit
                            </a>
                        <?php } ?>

                        <a href="delete_employee.php?id=<?php echo $row['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Are you sure you want to delete this employee?');">
                            <i class="fa-solid fa-trash-can"></i> Delete
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <a href="<?php echo ($user_role == 'super_admin') ? 'super_admin_dashboard.php' : 'home.php'; ?>" class="back-link">
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