<?php
session_start();
include("connect.php");

$message = "";
$message_type = ""; // To switch colors between success and error banners

if(isset($_POST['register'])){

    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $department = $_POST['department'];
    $position = $_POST['position'];
    $phone = $_POST['phone'];
    $salary = $_POST['salary'];

    // Secure Prepared Statement to prevent SQL Injection
    $stmt = $conn->prepare("INSERT INTO details (fullname, email, department, position, phone, salary) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssd", $fullname, $email, $department, $position, $phone, $salary);
    
    if($stmt->execute()){
        $message = "Employee Registered Successfully!";
        $message_type = "success";
    }else{
        $message = "Error: " . $stmt->error;
        $message_type = "error";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Registration & Directory</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-blue: #0076fe;
            --accent-purple: #8b5cf6;
            --success-green: #10b981;
            --error-red: #ef4444;
            --text-dark: #1f2937;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-image: url("image.png"); 
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            gap: 30px;
        }

        /* --- STUNNING FROSTED CARD WRAPPER --- */
        .container {
            max-width: 550px;
            width: 100%;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.25);
            border: 2px solid var(--primary-blue); 
            transition: transform 0.3s ease;
        }

        .container-wide {
            max-width: 1000px;
        }

        .container:hover {
            transform: translateY(-3px);
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

        /* --- NOTIFICATION BANNERS --- */
        .message-banner {
            padding: 12px 15px;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .banner-success {
            background-color: rgba(16, 185, 129, 0.12);
            color: var(--success-green);
            border: 1px solid rgba(16, 185, 129, 0.25);
        }

        .banner-error {
            background-color: rgba(239, 68, 68, 0.12);
            color: var(--error-red);
            border: 1px solid rgba(239, 68, 68, 0.25);
        }

        /* --- FORM STRUCTURE --- */
        label {
            display: block;
            font-weight: 600;
            color: #4b5563;
            margin-bottom: 6px;
            font-size: 0.9rem;
        }

        .input-field-wrapper {
            position: relative;
            margin-bottom: 18px;
        }

        .input-field-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            transition: color 0.2s;
        }

        input {
            width: 100%;
            padding: 12px 15px 12px 42px;
            font-size: 0.95rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.9);
            outline: none;
            color: var(--text-dark);
            transition: all 0.2s ease;
        }

        input:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(0, 118, 254, 0.15);
            background: #ffffff;
        }

        input:focus + i {
            color: var(--primary-blue);
        }

        /* --- BUTTONS --- */
        button {
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
            margin-top: 10px;
        }

        button:hover {
            box-shadow: 0 6px 16px rgba(0, 118, 254, 0.35);
            transform: translateY(-1px);
        }

        /* --- MODERN AJAX TABLE DESIGN --- */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            margin-top: 15px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }

        .modern-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            background: #ffffff;
            font-size: 0.92rem;
        }

        .modern-table th {
            background-color: #f3f4f6;
            color: #374151;
            padding: 14px 16px;
            font-weight: 700;
            border-bottom: 2px solid #e5e7eb;
        }

        .modern-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #edf2f7;
            color: var(--text-dark);
        }

        .modern-table tr:hover {
            background-color: #f9fafb;
        }

        .btn-action {
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .btn-edit { background-color: rgba(0, 118, 254, 0.1); color: var(--primary-blue); }
        .btn-delete { background-color: rgba(239, 68, 68, 0.1); color: var(--error-red); }

        .back-container { text-align: center; margin-top: 20px; }
        .back-link {
            display: inline-flex; align-items: center; gap: 6px; color: var(--primary-blue);
            text-decoration: none; font-weight: 600; font-size: 0.95rem; transition: all 0.2s;
        }
        .back-link:hover { color: var(--accent-purple); transform: translateX(-2px); }
    </style>
</head>
<body>

<div class="container">
    <h2><i class="fa-solid fa-user-plus"></i> Employee Registration Form</h2>

   <h2><i class="fa-solid fa-user-plus"></i> Employee Registration Form</h2>

    <div class="toast-container" id="toastContainer">
        <?php if(!empty($message)): ?>
            <div class="toast-notification <?php echo ($message_type == 'success') ? 'toast-success' : 'toast-error'; ?>">
                <i class="fa-solid <?php echo ($message_type == 'success') ? 'fa-circle-check' : 'fa-triangle-exclamation'; ?>"></i>
                <span><?php echo $message; ?></span>
            </div>
        <?php endif; ?>
    </div>

    <form method="POST" autocomplete="off">
        <label>Full Name</label>
        <div class="input-field-wrapper">
            <input type="text" name="fullname" placeholder="John Doe" required>
            <i class="fa-solid fa-user"></i>
        </div>

        <label>Email Address</label>
        <div class="input-field-wrapper">
            <input type="email" name="email" placeholder="johndoe@company.com" required>
            <i class="fa-solid fa-envelope"></i>
        </div>

        <label>Department</label>
        <div class="input-field-wrapper">
            <input type="text" name="department" placeholder="Human Resources" required>
            <i class="fa-solid fa-building"></i>
        </div>

        <label>Position</label>
        <div class="input-field-wrapper">
            <input type="text" name="position" placeholder="Senior Specialist" required>
            <i class="fa-solid fa-briefcase"></i>
        </div>

        <label>Phone Number</label>
        <div class="input-field-wrapper">
            <input type="text" name="phone" placeholder="+123 456 7890">
            <i class="fa-solid fa-phone"></i>
        </div>

        <label>Salary</label>
        <div class="input-field-wrapper">
            <input type="number" step="0.01" name="salary" placeholder="5500.00">
            <i class="fa-solid fa-money-bill-wave"></i>
        </div>

        <button type="submit" name="register">Register Employee</button>
    </form>

    <div class="back-container">
        <a href="super_admin_dashboard.php" class="back-link">
            <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>

<div class="container container-wide">
    <h2><i class="fa-solid fa-users-viewfinder"></i> Real-Time Registry Database</h2>
    
    <div class="search-wrapper" style="margin-bottom: 20px; max-width: 400px; position: relative;">
        <input type="text" id="tableSearch" placeholder="Type name, email, department to filter live..." 
               style="width: 100%; padding: 12px 16px 12px 45px; border-radius: 10px; border: 1.5px solid #d1d5db; outline: none; font-size: 0.95rem; transition: all 0.3s;">
        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 1rem;"></i>
    </div>

    <div class="table-responsive">
        <table class="modern-table">
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
            <tbody id="employeeTableBody">
                <?php
                $user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'employee';
                $result = mysqli_query($conn, "SELECT * FROM details");
                if(mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)){ ?>
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
                    <?php }
                } else { ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 20px; color: #6b7280;">No records found.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('tableSearch').addEventListener('input', function() {
    const searchQuery = this.value;
    const tableBody = document.getElementById('employeeTableBody');

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'search_employee.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        if(this.status === 200) {
            tableBody.innerHTML = this.responseText;
        }
    };

    xhr.send('query=' + encodeURIComponent(searchQuery));
});
</script>

</body>
</html>