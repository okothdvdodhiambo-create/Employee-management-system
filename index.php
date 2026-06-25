<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff System - Home</title>
    <!-- Modern web graphics fontawesome icon pack styling -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-blue: #0076fe;
            --accent-purple: #8b5cf6;
            --text-dark: #1f2937;
            --text-muted: #4b5563;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            /* Synchronized system workspace background wallpaper */
            background-image: url("image.png");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        /* --- LANDING PORTAL LAYOUT CONTAINER --- */
        .portal-wrapper {
            max-width: 1100px;
            width: 100%;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.6);
            overflow: hidden;
            display: grid;
            grid-template-columns: 1.2fr 0.8fr; /* Responsive multi-column layout template alignment */
        }

        /* --- LEFT SIDE: WELCOME & SYSTEM DETAILS --- */
        .content-side {
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: left;
        }

        .hero-title h1 {
            font-size: 2.6rem;
            color: #111827;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 12px;
        }

        .hero-title h1 span {
            background: linear-gradient(135deg, var(--primary-blue), var(--accent-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-title p {
            font-size: 1.05rem;
            color: var(--text-muted);
            margin-bottom: 35px;
            line-height: 1.6;
        }

        /* --- ACTION ROUTING BUTTONS --- */
        .actions-group {
            display: flex;
            gap: 15px;
            margin-bottom: 40px;
        }

        .actions-group a {
            text-decoration: none;
        }

        .btn {
            padding: 14px 30px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--accent-purple) 100%);
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(0, 118, 254, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 118, 254, 0.45);
        }

        .btn-register {
            background: #ffffff;
            color: var(--primary-blue);
            border: 2px solid var(--primary-blue);
        }

        .btn-register:hover {
            background: rgba(0, 118, 254, 0.05);
            transform: translateY(-2px);
        }

        /* --- MINI EXPLANATORY FEATURE CARDS --- */
        .mini-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            border-top: 1px solid rgba(0, 0, 0, 0.08);
            padding-top: 30px;
        }

        .item-card h4 {
            font-size: 1rem;
            color: #111827;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .item-card h4 i { color: var(--primary-blue); }
        .item-card p { font-size: 0.85rem; color: #6b7280; line-height: 1.5; }

        /* --- RIGHT SIDE: PHOTO BANNER / QR INTEGRATION --- */
        .media-side {
            background: linear-gradient(135deg, rgba(0, 118, 254, 0.03), rgba(139, 92, 246, 0.03));
            border-left: 1px solid rgba(0, 0, 0, 0.05);
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .qr-card {
            background: #ffffff;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
            border: 2px solid var(--primary-blue);
            max-width: 260px;
            width: 100%;
        }

        .qr-image {
            width: 100%;
            height: auto;
            aspect-ratio: 1/1;
            border-radius: 12px;
            object-fit: cover;
            margin-bottom: 15px;
            background-color: #f3f4f6;
        }

        .media-side h3 {
            font-size: 1.15rem;
            color: #111827;
            margin-bottom: 6px;
            font-weight: 700;
        }

        .media-side p {
            font-size: 0.85rem;
            color: var(--text-muted);
            padding: 0 10px;
            line-height: 1.4;
        }

        /* --- RESPONSIVE MOBILE VIEWPORT CHROME F12 LAYER --- */
        @media (max-width: 900px) {
            .portal-wrapper {
                grid-template-columns: 1fr;
            }
            .media-side {
                border-left: none;
                border-top: 1px solid rgba(0, 0, 0, 0.05);
                padding: 50px;
            }
            .content-side {
                padding: 40px 30px;
            }
        }
    </style>
</head>
<body>

<div class="portal-wrapper">

    <!-- Left Side Content Panel Container -->
    <div class="content-side">
        <div class="hero-title">
            <h1>Welcome to <br><span>Staff System Portal</span></h1>
            <p>Please choose an option below to securely initialize your session credentials, authenticate operational clearance levels, or establish secondary identity registration profiles.</p>
        </div>

        <!-- Action Option Triggers -->
        <div class="actions-group">
            <a href="login.php">
                <button class="btn btn-login"><i class="fa-solid fa-right-to-bracket"></i> Login Account</button>
            </a>
            <a href="register.php">
                <button class="btn btn-register"><i class="fa-solid fa-user-plus"></i> Register Profile</button>
            </a>
        </div>

        <!-- System Architecture Mini Descriptions -->
        <div class="mini-features">
            <div class="item-card">
                <h4><i class="fa-solid fa-fingerprint"></i> Identity Logging</h4>
                <p>Track secure check-in operations dynamically across the global timeline structure.</p>
            </div>
            <div class="item-card">
                <h4><i class="fa-solid fa-chart-line"></i> Summary Logs</h4>
                <p>Review real-time calculations of employees, role permissions, and active records cleanly.</p>
            </div>
        </div>
    </div>

    <!-- Right Side Graphic / QR Node Presentation Grid -->
    <div class="media-side">
        <div class="qr-card">
            <!-- Dynamic fallback API generates a QR link if your 'qr_code.png' graphic doesn't exist yet -->
            <img src="qr_code.png" alt="System QR Code Access" class="qr-image" onerror="this.src='https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=http://localhost/staff/'">
            <h3>Quick Access QR Node</h3>
            <p>Scan this QR terminal box with any smartphone configuration matrix to inspect mobile responsivity layout metrics seamlessly.</p>
        </div>
    </div>

</div>

</body>
</html>