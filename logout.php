<?php
// ==========================================
// 1. BACKEND LOGOUT ENGINE (SESSION DESTRUCTION)
// ==========================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Unset all session variables
$_SESSION = array();

// Completely destroy session cookies from the browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// Kill the server-side session allocation
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out...</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-blue: #0076fe;
            --accent-purple: #8b5cf6;
            --text-dark: #1f2937;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            /* Matches your unified background system theme */
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

        /* --- SLEEK FROSTED CARD CONTAINER --- */
        .logout-box {
            max-width: 400px;
            width: 100%;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.25);
            border: 2px solid var(--primary-blue);
            text-align: center;
            animation: cardPop 0.4s ease-out;
        }

        /* Modern Spinning Ring Loader Animation */
        .loader-icon {
            font-size: 3rem;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--accent-purple) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 20px;
            animation: spin 1.2s linear infinite;
        }

        h3 {
            color: var(--text-dark);
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        p {
            color: #6b7280;
            font-size: 0.95rem;
            font-weight: 500;
        }

        /* --- ANIMATION KEYFRAMES --- */
        @keyframes cardPop {
            0% { transform: scale(0.9); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

    <div class="logout-box">
        <div class="loader-icon">
            <i class="fa-solid fa-circle-notch"></i>
        </div>
        <h3>Signing Out Securely</h3>
        <p>Clearing your session data, please wait...</p>
    </div>

    <script>
        setTimeout(function() {
            window.location.href = 'login.php';
        }, 1500); // 1.5 seconds delay allows the user to see the logout animation state
    </script>

</body>
</html>