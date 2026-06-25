<?php
session_start();
include("connect.php");

$error_message = ""; // Variable to hold login error safely

if(isset($_POST['login'])){
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // 1. SECURE STEP: Select user record by email ONLY using a Prepared Statement
    $stmt = $conn->prepare("SELECT username, password, role FROM employees WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $row = $result->fetch_assoc();

        // 2. SECURE STEP: Verify the raw form input against the unreadable BCRYPT database hash string
        if(password_verify($password, $row['password'])){
            
            // Password matches! Initialize system session tracking variables
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            // Divert routing profiles based on the administrative structural tier
            if($row['role'] == 'super_admin'){
                header("Location: super_admin_dashboard.php");
            } else {
                header("Location: home.php");
            }
            exit();
        } else {
            // Keeps errors vague to prevent bad actors from guessing valid emails
            $error_message = "Invalid Email or Password";
        }
    } else {
        $error_message = "Invalid Email or Password";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-blue: #0076fe;
            --primary-hover: #005ecb;
            --text-dark: #333333;
            --error-red: #ef4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            /* Synchronized perfectly with your login & dashboard system imagery */
            background-image: url("image.png"); 
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        /* --- INTERACTIVE CONTAINER --- */
        .container {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            max-width: 420px;
            width: 100%;
            padding: 40px 35px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.6);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .container:hover {
            transform: translateY(-2px); /* Subtle hover lift */
        }

        .logo-area {
            font-size: 3rem;
            color: var(--primary-blue);
            margin-bottom: 15px;
        }

        h2 {
            font-size: 1.8rem;
            color: #111827;
            font-weight: 700;
            margin-bottom: 25px;
        }

        /* --- ERROR BANNER --- */
        .error-banner {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--error-red);
            padding: 12px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 20px;
            border: 1px solid rgba(239, 68, 68, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        /* --- INTERACTIVE INPUT GROUPS --- */
        .input-group {
            position: relative;
            margin-bottom: 20px;
            text-align: left;
        }

        .input-group input {
            width: 100%;
            padding: 14px 45px 14px 16px;
            font-size: 1rem;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.9);
            outline: none;
            transition: all 0.2s ease;
            color: var(--text-dark);
        }

        .input-group input:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(0, 118, 254, 0.15);
            background: #ffffff;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            cursor: pointer;
            transition: color 0.2s;
            font-size: 1.1rem;
        }

        .password-toggle:hover {
            color: var(--primary-blue);
        }

        /* --- INTERACTIVE BUTTON --- */
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
            margin-top: 10px;
            box-shadow: 0 4px 12px rgba(0, 118, 254, 0.2);
        }

        button[type="submit"]:hover {
            background-color: var(--primary-hover);
            box-shadow: 0 6px 16px rgba(0, 118, 254, 0.3);
        }

        button[type="submit"]:active {
            transform: scale(0.98);
        }

        /* --- FOOTER REGISTRATION LINK --- */
        p.register-prompt {
            margin-top: 25px;
            font-size: 0.95rem;
            color: #6b7280;
        }

        p.register-prompt a {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }

        p.register-prompt a:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }
        
    </style>
</head>
<body>

<div class="container">
    <div class="logo-area">
        <i class="fa-solid fa-user-lock"></i>
    </div>
    
    <h2>Employee Login</h2>

    <div class="toast-container" id="toastContainer">
        <?php if(!empty($error_message)): ?>
            <div class="toast-notification">
                <i class="fa-solid fa-triangle-exclamation"></i> 
                <span><?php echo $error_message; ?></span>
            </div>
        <?php endif; ?>
    </div>

    <form method="POST" autocomplete="off">
        <div class="input-group">
            <input type="email" name="email" placeholder="Email Address" required>
        </div>

        <div class="input-group">
            <input type="password" id="passwordField" name="password" placeholder="Password" required>
            <i class="fa-regular fa-eye password-toggle" id="toggleIcon" onclick="togglePasswordVisibility()"></i>
        </div>

        <button type="submit" name="login">Login Securely</button>
    </form>

    <p class="register-prompt">
        No account? <a href="register.php">Register Here</a>
    </p>
</div>



</body>
</html>