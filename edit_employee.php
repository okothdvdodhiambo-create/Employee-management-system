<?php
session_start();
include("connect.php");

// Safety check: ensure user is logged in
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

// SMART TRACKING: Remember the exact filename you came from before the form submits
if(!isset($_POST['update_employee']) && isset($_SERVER['HTTP_REFERER']) && !strpos($_SERVER['HTTP_REFERER'], 'edit_employee.php')) {
    $_SESSION['came_from'] = explode('?', $_SERVER['HTTP_REFERER'])[0];
}

$message = "";
$message_class = "";

// 1. Fetch current record configurations to pre-fill inputs
if(isset($_GET['id'])){
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $query = mysqli_query($conn, "SELECT * FROM details WHERE id = '$id'");
    $employee = mysqli_fetch_assoc($query);
    
    if(!$employee){
        if(isset($_SESSION['came_from'])) {
            header("Location: " . $_SESSION['came_from']);
        } else {
            header("Location: index.php");
        }
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}

// 2. Process form revisions back into the database engine
if(isset($_POST['update_employee'])){
    $fullname   = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email      = mysqli_real_escape_string($conn, $_POST['email']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $position   = mysqli_real_escape_string($conn, $_POST['position']);
    $phone      = mysqli_real_escape_string($conn, $_POST['phone']);
    $salary     = mysqli_real_escape_string($conn, $_POST['salary']);
    
    $update_sql = "UPDATE details SET 
                    fullname = '$fullname', 
                    email = '$email', 
                    department = '$department', 
                    position = '$position', 
                    phone = '$phone', 
                    salary = '$salary' 
                   WHERE id = '$id'";
                   
    if(mysqli_query($conn, $update_sql)){
        // SMART REDIRECT: Go back to the page we saved in the session with the updated status
        if(isset($_SESSION['came_from'])) {
            $redirect_to = $_SESSION['came_from'];
            unset($_SESSION['came_from']); // Clear it out
            header("Location: " . $redirect_to . "?status=updated");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $message = "Operational Error: Failed to execute database update profile.";
        $message_class = "error-msg";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Employee Records</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-blue: #0076fe;
            --accent-purple: #8b5cf6;
            --text-dark: #1f2937;
            --card-bg: rgba(255, 255, 255, 0.9);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }

        body {
            background-image: url("image.png");
            background-size: cover; background-position: center; background-repeat: no-repeat; background-attachment: fixed;
            min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 40px 20px;
        }

        .form-container {
            max-width: 600px; width: 100%; background: var(--card-bg); backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px); padding: 40px; border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.6);
        }

        h2 { font-size: 1.8rem; color: #111827; margin-bottom: 25px; display: flex; align-items: center; gap: 12px; }
        h2 i { color: var(--primary-blue); }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; text-align: left; margin-bottom: 25px; }

        @media (max-width: 600px) { .form-grid { grid-template-columns: 1fr; } }

        .input-group { display: flex; flex-direction: column; }
        .input-group label { font-size: 0.85rem; font-weight: 700; color: #4b5563; margin-bottom: 6px; text-transform: uppercase; }
        .input-group input {
            padding: 12px 14px; border-radius: 10px; border: 1.5px solid #e5e7eb;
            font-size: 0.95rem; outline: none; background: #ffffff; color: var(--text-dark); transition: all 0.3s;
        }
        .input-group input:focus { border-color: var(--primary-blue); box-shadow: 0 0 0 4px rgba(0, 118, 254, 0.12); }

        .btn-save {
            width: 100%; padding: 14px; border: none; border-radius: 10px;
            background: linear-gradient(135deg, var(--primary-blue), var(--accent-purple));
            color: white; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.2s;
        }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,118,254,0.3); }

        .cancel-link { display: inline-block; margin-top: 20px; color: #6b7280; text-decoration: none; font-size: 0.95rem; font-weight: 600; transition: color 0.2s; cursor: pointer; }
        .cancel-link:hover { color: var(--primary-blue); }
    </style>
</head>
<body>

<div class="form-container">
    <h2><i class="fa-solid fa-user-pen"></i> Edit Employee Identity (#<?php echo $id; ?>)</h2>

    <form method="POST">
        <div class="form-grid">
            <div class="input-group">
                <label>Full Name</label>
                <input type="text" name="fullname" value="<?php echo htmlspecialchars($employee['fullname']); ?>" required>
            </div>
            
            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($employee['email']); ?>" required>
            </div>

            <div class="input-group">
                <label>Department</label>
                <input type="text" name="department" value="<?php echo htmlspecialchars($employee['department']); ?>" required>
            </div>

            <div class="input-group">
                <label>Job Position</label>
                <input type="text" name="position" value="<?php echo htmlspecialchars($employee['position']); ?>" required>
            </div>

            <div class="input-group">
                <label>Phone Line</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($employee['phone']); ?>" required>
            </div>

            <div class="input-group">
                <label>Salary (KES)</label>
                <input type="number" name="salary" value="<?php echo htmlspecialchars($employee['salary']); ?>" required>
            </div>
        </div>

        <button type="submit" name="update_employee" class="btn-save">Commit Profile Changes</button>
    </form>

    <a href="#" onclick="window.history.back(); return false;" class="cancel-link">
        <i class="fa-solid fa-xmark"></i> Discard Configuration
    </a>
</div>

</body>
</html>