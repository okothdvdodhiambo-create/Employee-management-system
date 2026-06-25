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
$result = mysqli_query($conn, "SELECT * FROM details");
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
            --card-bg: rgba(255, 255, 255, 0.9);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            /* Matches the stunning background format from your home and login configurations */
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

        /* --- CONTAINER PROFILE CARD --- */
        .table-container {
            max-width: 1100px;
            width: 100%;
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 35px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
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
            /* Creating the vibrant colorful structural outer boundary wrapper border */
            border: 2px solid var(--primary-blue); 
        }

        /* Header Layout Styling */
        .custom-table th {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--accent-purple) 100%);
            color: #ffffff;
            font-weight: 600;
            padding: 16px;
            text-align: left;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
        }

        /* Row Data Items styling */
        .custom-table td {
            padding: 14px 16px;
            background-color: rgba(255, 255, 255, 0.7);
            color: var(--text-dark);
            font-size: 0.95rem;
            border-bottom: 1px solid rgba(0, 118, 254, 0.1);
            transition: background-color 0.2s ease;
        }

        /* Interactive row illumination effect on hover */
        .custom-table tr:hover td {
            background-color: rgba(0, 118, 254, 0.06);
        }

        /* Eliminate the trailing row boundary line */
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
    </style>
</head>
<body>

<div class="table-container">

    <h1><i class="fa-solid fa-folder-users"></i> Employee Records</h1>

    <table class="custom-table">
        <thead>
            <tr>
                <th>ID</th>
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
                <td><strong>#<?php echo $row['id']; ?></strong></td>
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

</body>
</html>