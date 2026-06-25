<?php
include("connect.php");

$message = "";
$message_class = "";

if(isset($_POST['register'])){

    // 1. Capture and trim user inputs safely
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // 2. SECURE STEP: Hash the password using industry-standard BCRYPT encryption
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // 3. SECURE STEP: Initialize a Prepared Statement to neutralize SQL Injection vulnerabilities
    $stmt = mysqli_prepare($conn, "INSERT INTO employees (username, email, password, role) VALUES (?, ?, ?, 'employee')");
    
    if ($stmt) {
        // "sss" indicates we are binding three string types securely
        mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashed_password);
        
        if(mysqli_stmt_execute($stmt)){
            $message = "<i class='fa-solid fa-circle-check'></i> Registration Successful!";
            $message_class = "success-msg";
        } else {
            // Error handling check to see if database constraints failed (e.g. duplicate username or email)
            if(mysqli_errno($conn) == 1062) {
                $message = "<i class='fa-solid fa-circle-exclamation'></i> Username or Email already registered.";
            } else {
                $message = "<i class='fa-solid fa-circle-exclamation'></i> Registration Failed.";
            }
            $message_class = "error-msg";
        }
        // Close the system statement resource channel
        mysqli_stmt_close($stmt);
    } else {
        $message = "<i class='fa-solid fa-circle-exclamation'></i> Database operational error during execution layout.";
        $message_class = "error-msg";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-blue: #0076fe;
            --accent-purple: #8b5cf6;
            --accent-green: #10b981;
            --text-dark: #1f2937;
            --input-focus: #3b82f6;
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
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        /* --- MODERN CARD CONTAINER --- */
        .container {
            max-width: 450px;
            width: 100%;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            padding: 40px 35px;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.6);
            text-align: center;
        }

        .register-icon-box {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--accent-purple) 100%);
            color: #ffffff;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 auto 20px auto;
            box-shadow: 0 8px 16px rgba(0, 118, 254, 0.3);
        }

        h2 {
            font-size: 1.8rem;
            color: #111827;
            font-weight: 700;
            margin-bottom: 8px;
        }

        p.subtitle {
            font-size: 0.92rem;
            color: #6b7280;
            margin-bottom: 25px;
        }

        /* --- DYNAMIC STATUS ALERTS --- */
        .alert-box {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 0.92rem;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .success-msg { background-color: rgba(16, 185, 129, 0.12); color: var(--accent-green); border: 1px solid rgba(16, 185, 129, 0.2); }
        .error-msg { background-color: rgba(239, 68, 68, 0.12); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }

        /* --- INPUT FIELDS STRUCTURING --- */
        .input-group {
            position: relative;
            margin-bottom: 20px;
            width: 100%;
        }

        .input-group i.field-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 1.1rem;
            transition: color 0.3s;
        }

        .input-group input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border-radius: 12px;
            border: 1.5px solid #e5e7eb;
            background-color: rgba(255, 255, 255, 0.9);
            font-size: 0.98rem;
            color: var(--text-dark);
            outline: none;
            transition: all 0.3s ease;
        }

        .input-group input:focus {
            border-color: var(--input-focus);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
            background-color: #ffffff;
        }

        .input-group input:focus ~ i.field-icon {
            color: var(--input-focus);
        }

        .toggle-password {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            cursor: pointer;
            transition: color 0.2s;
        }
        .toggle-password:hover { color: var(--text-dark); }

        button[type="submit"] {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--accent-purple) 100%);
            color: #ffffff;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 118, 254, 0.25);
            transition: all 0.3s ease;
            margin-top: 5px;
        }

        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 118, 254, 0.4);
            filter: brightness(1.05);
        }

        button[type="submit"]:active {
            transform: translateY(0);
        }

        .footer-text {
            margin-top: 25px;
            font-size: 0.92rem;
            color: #4b5563;
        }

        .footer-text a {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }

        .footer-text a:hover {
            color: var(--accent-purple);
            text-decoration: underline;
        }
        /* Keep all your existing styles exactly as they are above, just paste these at the end */

        /* --- MODERN FLOATING TOAST CONTAINER --- */
        .toast-container {
            position: fixed;
            top: 25px;
            right: 25px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .toast-notification {
            background: #ffffff;
            padding: 16px 20px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.95rem;
            font-weight: 600;
            min-width: 300px;
            transform: translateX(120%);
            animation: slideIn 0.4s forwards, fadeOut 0.5s ease 3.5s forwards;
            border-left: 5px solid;
        }

        .toast-success { border-left-color: var(--success-green); color: var(--success-green); }
        .toast-error { border-left-color: var(--error-red); color: var(--error-red); }

        @keyframes slideIn {
            to { transform: translateX(0); }
        }
        @keyframes fadeOut {
            to { opacity: 0; transform: translateY(-20px); pointer-events: none; }
        }
    </style>
    </style>
</head>
<body>

<div class="container">

    <div class="register-icon-box">
        <i class="fa-solid fa-user-gear"></i>
    </div>

    <h2>Create Account</h2>
    <p class="subtitle">Register a brand new system user identity profile</p>

    <?php if(!empty($message)): ?>
        <div class="alert-box <?php echo $message_class; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">

        <div class="input-group">
            <input type="text" name="username" placeholder="Username" required>
            <i class="fa-solid fa-user field-icon"></i>
        </div>

        <div class="input-group">
            <input type="email" name="email" placeholder="Email Address" required>
            <i class="fa-solid fa-envelope field-icon"></i>
        </div>

        <div class="input-group">
            <input type="password" name="password" id="passwordField" placeholder="Password" required>
            <i class="fa-solid fa-lock field-icon"></i>
            <i class="fa-solid fa-eye toggle-password" id="eyeIcon" onclick="togglePasswordVisibility()"></i>
        </div>

        <button type="submit" name="register">Register Identity</button>

    </form>

    <p class="footer-text">
        Already have an account? 
        <a href="login.php">Login Here</a>
    </p>

</div>

<script>
    function togglePasswordVisibility() {
        const passwordField = document.getElementById("passwordField");
        const eyeIcon = document.getElementById("eyeIcon");
        
        if (passwordField.type === "password") {
            passwordField.type = "text";
            eyeIcon.classList.remove("fa-eye");
            eyeIcon.classList.add("fa-eye-slash");
        } else {
            passwordField.type = "password";
            eyeIcon.classList.remove("fa-eye-slash");
            eyeIcon.classList.add("fa-eye");
        }
    }
</script>
<script>
        // If you already have your AJAX search box script here, 
        // just leave it alone and paste this right under it:

        const runningToast = document.querySelector('.toast-notification');
        if(runningToast) {
            setTimeout(() => {
                runningToast.remove();
            }, 4100); 
        }
    </script>

</body>
</html>

</body>
</html>