<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("connect.php");

$error_message = ""; // Variable to hold login error safely

// =========================================================
// CHANGED: STEP 2 - CHECK FOR PRE-EXISTING REMEMBER ME COOKIE
// =========================================================
$saved_email = "";
if (isset($_COOKIE['remember_email'])) {
    $saved_email = htmlspecialchars($_COOKIE['remember_email']);
}

if(isset($_POST['login'])){
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // 1. SECURE STEP: Select user record by email ONLY using a Prepared Statement
    $stmt = $conn->prepare("SELECT id, username, password, role FROM employees WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
    
        // 2. SECURE STEP: Verify the raw form input against the unreadable BCRYPT database hash string
        if(password_verify($password, $row['password'])){
            
            // Password matches! Initialize system session tracking variables
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['email'] = $email;

            // =========================================================
            // CHANGED: STEP 1 - MANAGE REMEMBER ME COOKIE STATE ON SUCCESS
            // =========================================================
            if (isset($_POST['remember_me'])) {
                // Set secure cookie named 'remember_email' for 30 days (86400 seconds * 30 days)
                // The final 'true' parameter turns on HttpOnly to shield against XSS attacks!
                setcookie("remember_email", $email, time() + (86400 * 30), "/", "", false, true); 
            } else {
                // If the user logging in unchecked the box, clear out any old cookie
                if (isset($_COOKIE['remember_email'])) {
                    setcookie("remember_email", "", time() - 3600, "/", "", false, true);
                }
            }

            // Divert routing profiles based on the administrative structural tier
            if($row['role'] == 'super_admin'){

                header("Location: super_admin_dashboard.php");

            }elseif($row['role'] == 'admin'){

                header("Location: home.php");

            }elseif($row['role'] == 'employee'){

                header("Location: employee_portal.php");

            }else{

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
            --success-green: #10b981;
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

        /* ========================================================= */
        /* CHANGED: ADDED MODERN SLIDING TOAST BANNER CORE STYLES    */
        /* ========================================================= */
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
            min-width: 320px;
            transform: translateX(120%);
            animation: slideIn 0.4s forwards, fadeOut 0.5s ease 3.5s forwards;
            border-left: 5px solid var(--error-red);
            color: var(--error-red);
            text-align: left;
        }

        @keyframes slideIn {
            to { transform: translateX(0); }
        }
        @keyframes fadeOut {
            to { opacity: 0; transform: translateY(-20px); pointer-events: none; }
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
            <input type="email" name="email" placeholder="Email Address" value="<?php echo $saved_email; ?>" required>
        </div>

        <div class="input-group">
            <input type="password" id="passwordField" name="password" placeholder="Password" required>
            <i class="fa-regular fa-eye password-toggle" id="toggleIcon" onclick="togglePasswordVisibility()"></i>
        </div>

        <div class="remember-me-wrapper" style="display: flex; align-items: center; gap: 8px; margin: -5px 0 20px 2px; user-select: none; text-align: left;">
            <input type="checkbox" name="remember_me" id="rememberMe" <?php echo !empty($saved_email) ? 'checked' : ''; ?> 
                   style="width: 16px; height: 16px; accent-color: var(--primary-blue); cursor: pointer;">
            <label for="rememberMe" style="margin: 0; color: #4b5563; font-size: 0.9rem; cursor: pointer; font-weight: 500;">
                Remember my email on this device
            </label>
        </div>

        <button type="submit" name="login">Login Securely</button>
    </form>

    <p class="register-prompt">
        No account? <a href="register.php">Register Here</a>
    </p>
</div>

<script>
    // Existing password eye visibility toggle logic
    function togglePasswordVisibility() {
        const passwordField = document.getElementById('passwordField');
        const toggleIcon = document.getElementById('toggleIcon');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }

    // Automatically remove toast alert markup from layout branch after cycle timeout expires
    const runningToast = document.querySelector('.toast-notification');
    if(runningToast) {
        setTimeout(() => {
            runningToast.remove();
        }, 4100); 
    }
</script>

</body>
</html>