<?php
session_start();
include("connect.php");

if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

$message = "";
$message_type = "";

if(isset($_POST['change_password'])){

    $email = $_SESSION['email'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if($new_password !== $confirm_password){
        $message = "New passwords do not match.";
        $message_type = "error";
    } elseif (strlen($new_password) < 6) {
        $message = "Password must be at least 6 characters long.";
        $message_type = "error";
    } else {
        // Fetch current password from database to verify
        $stmt = $conn->prepare("SELECT password FROM employees WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($row = $result->fetch_assoc()){
            if(password_verify($current_password, $row['password'])){
                // Hash the new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Update password in database
                $update_stmt = $conn->prepare("UPDATE employees SET password = ? WHERE email = ?");
                $update_stmt->bind_param("ss", $hashed_password, $email);
                
                if($update_stmt->execute()){
                    $message = "Password updated successfully!";
                    $message_type = "success";
                } else {
                    $message = "Database error. Please try again.";
                    $message_type = "error";
                }
                $update_stmt->close();
            } else {
                $message = "Incorrect current password.";
                $message_type = "error";
            }
        } else {
            $message = "User account not found.";
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
<title>Change Password</title>

<!-- Font Awesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    :root {
        --primary: #0076fe;
        --secondary: #8b5cf6;
        --dark: #0f172a;
        --glass-bg: rgba(255, 255, 255, 0.92);
        --glass-border: rgba(255, 255, 255, 0.4);
        --success-bg: rgba(16, 185, 129, 0.12);
        --success-color: #059669;
        --error-bg: rgba(239, 68, 68, 0.12);
        --error-color: #dc2626;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* --- VIBRANT ANIMATED BACKGROUND --- */
    body {
        background: linear-gradient(-45deg, #0f172a, #7e22ce, #e11d48, #0d9488, #1e3a8a);
        background-size: 400% 400%;
        animation: vibrantGradient 18s ease infinite;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px 16px;
    }

    @keyframes vibrantGradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* --- GLASS CONTAINER --- */
    .container {
        max-width: 520px;
        width: 100%;
        background: var(--glass-bg);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        padding: 40px;
        border-radius: 24px;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.4);
        border: 1px solid var(--glass-border);
    }

    .header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 15px;
    }

    h1 {
        font-size: 1.6rem;
        color: var(--dark);
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    h1 i {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* --- ALERTS --- */
    .message {
        padding: 14px 18px;
        border-radius: 12px;
        margin-bottom: 25px;
        font-weight: 600;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .message.success {
        background: var(--success-bg);
        color: var(--success-color);
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .message.error {
        background: var(--error-bg);
        color: var(--error-color);
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    /* --- FORM FIELDS --- */
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 20px;
    }

    label {
        font-size: 0.88rem;
        font-weight: 700;
        color: var(--dark);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    label i {
        color: var(--primary);
    }

    input {
        width: 100%;
        padding: 13px 16px;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        background: #ffffff;
        font-size: 0.95rem;
        color: var(--dark);
        outline: none;
        transition: all 0.25s ease;
    }

    input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(0, 118, 254, 0.15);
    }

    /* --- BUTTONS & ACTIONS --- */
    .btn-submit {
        width: 100%;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        border: none;
        padding: 16px;
        border-radius: 14px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 10px 20px -5px rgba(0, 118, 254, 0.4);
        transition: all 0.3s ease;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        margin-top: 10px;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 25px -5px rgba(0, 118, 254, 0.5);
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        background: #ffffff;
        color: var(--dark);
        text-decoration: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9rem;
        border: 1px solid #cbd5e1;
        box-shadow: 0 2px 4px rgba(0,0,0,0.04);
        transition: all 0.2s ease;
    }

    .back-btn:hover {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
        transform: translateY(-2px);
    }

    /* --- MOBILE RESPONSIVENESS --- */
    @media (max-width: 650px) {
        .container {
            padding: 25px 20px;
        }

        h1 {
            font-size: 1.35rem;
        }
    }
    :root {
    --primary: #0076fe;
    --secondary: #8b5cf6;
    --dark-bg: #090d16;          /* Deep rich black */
    --text-main: #f8fafc;        /* High-contrast white/light text */
    --text-muted: #94a3b8;       /* Subtle muted text */
    --dark-border: rgba(255, 255, 255, 0.1);
}

/* --- Ensure page stretches full height so footer sits at bottom --- */
html, body {
    height: 100%;
    margin: 0;
    padding: 0;
}

body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

/* --- Container wrapper push footer down --- */
.container {
    flex: 1 0 auto; /* Pushes footer to bottom when page content is short */
}

/* --- FULL-WIDTH BLACK FOOTER --- */
.system-footer {
    width: 100vw;                /* Cover screen left to right */
    position: relative;
    left: 50%;
    right: 50%;
    margin-left: -50vw;
    margin-right: -50vw;
    margin-top: auto;            /* Sticks to the bottom */
    
    background: var(--dark-bg);
    border-top: 1px solid var(--dark-border);
    padding: 35px 40px;
    
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 35px;
    box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.5);
    box-sizing: border-box;
}

.footer-logo img {
    width: 85px;
    height: 85px;
    object-fit: contain;
    border-radius: 16px;
    padding: 8px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--dark-border);
}

.footer-content {
    max-width: 1000px;
    width: 100%;
}

.footer-content h3 {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-main);
    margin-bottom: 6px;
}

.footer-desc {
    color: var(--text-muted);
    font-size: 0.9rem;
    line-height: 1.6;
    margin-bottom: 18px;
    max-width: 750px;
}

/* --- SOCIAL LINKS (DARK THEME) --- */
.social-links {
    display: flex;
    gap: 12px;
    margin-bottom: 18px;
}

.social-links a {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.06);
    color: var(--text-main);
    display: flex;
    justify-content: center;
    align-items: center;
    text-decoration: none;
    font-size: 0.95rem;
    border: 1px solid var(--dark-border);
    transition: all 0.25s ease;
}

.social-links a:hover {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: #ffffff;
    border-color: transparent;
    transform: translateY(-3px);
    box-shadow: 0 6px 18px rgba(0, 118, 254, 0.35);
}

/* --- COPYRIGHT BAR --- */
.copyright {
    font-size: 0.85rem;
    color: var(--text-muted);
    border-top: 1px solid var(--dark-border);
    padding-top: 14px;
}

.copyright strong {
    color: var(--text-main);
}

/* --- RESPONSIVE DESIGN --- */
@media (max-width: 768px) {
    .system-footer {
        flex-direction: column;
        text-align: center;
        padding: 30px 20px;
    }

    .social-links {
        justify-content: center;
    }

    .footer-desc {
        margin-left: auto;
        margin-right: auto;
    }
}
</style>
</head>
<body>

<div class="container">

    <div class="header-bar">
        <h1>
            <i class="fa-solid fa-key"></i>
            Change Password
        </h1>

        <!-- DIRECT ROUTE TO HOME PAGE -->
        <a href="home.php" class="back-btn">
            <i class="fa-solid fa-house"></i>
            Back to Home
        </a>
    </div>

    <?php if(!empty($message)){ ?>
        <div class="message <?php echo $message_type; ?>">
            <i class="fa-solid <?php echo $message_type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'; ?>"></i>
            <?php echo $message; ?>
        </div>
    <?php } ?>

    <form method="POST">

        <div class="form-group">
            <label><i class="fa-solid fa-lock"></i> Current Password</label>
            <input type="password" name="current_password" placeholder="Enter current password" required>
        </div>

        <div class="form-group">
            <label><i class="fa-solid fa-key"></i> New Password</label>
            <input type="password" name="new_password" placeholder="Enter new password" required>
        </div>

        <div class="form-group">
            <label><i class="fa-solid fa-shield-halved"></i> Confirm New Password</label>
            <input type="password" name="confirm_password" placeholder="Re-enter new password" required>
        </div>

        <button type="submit" name="change_password" class="btn-submit">
            <i class="fa-solid fa-arrows-rotate"></i>
            Update Password
        </button>

    </form>

</div>
<footer class="system-footer">

    <div class="footer-logo">
        <img src="image.png" alt="EMS Logo">
    </div>

    <div class="footer-content">

        <h3>Employee Management System</h3>

        <p class="footer-desc">
            Streamlining employee management, attendance tracking,
            leave management, reporting, and organizational productivity.
        </p>

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

        <p class="copyright">
            © <?php echo date('Y'); ?> Employee Management System &bull; 
            Developed by <strong>David Okoth</strong> &bull; 
            All Rights Reserved.
        </p>

    </div>

</footer>

</body>
</html>