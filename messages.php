<?php
session_start();
include("connect.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$current_user = $_SESSION['user_id'];

$users = mysqli_query(
    $conn,
    "SELECT id, username, role
     FROM employees
     WHERE id != '$current_user'
     ORDER BY username ASC"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Internal Messaging System</title>

<!-- Font Awesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    :root {
        --primary: #0076fe;
        --secondary: #8b5cf6;
        --dark: #0f172a;
        --text-muted: #64748b;
        --glass-bg: rgba(255, 255, 255, 0.93);
        --glass-border: rgba(255, 255, 255, 0.4);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* --- VIBRANT ANIMATED BACKGROUND --- */
    body {
        background: linear-gradient(-45deg, #0f172a, #7e22ce, #0076fe, #0d9488);
        background-size: 400% 400%;
        animation: gradientMove 15s ease infinite;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px 16px;
    }

    @keyframes gradientMove {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* --- GLASS CONTAINER --- */
    .container {
        max-width: 1050px;
        width: 100%;
        background: var(--glass-bg);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        padding: 40px;
        border-radius: 24px;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.35);
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

    .header-info h1 {
        font-size: 1.8rem;
        color: var(--dark);
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .header-info h1 i {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .header-info p {
        color: var(--text-muted);
        font-size: 0.95rem;
        margin-top: 4px;
    }

    /* --- USERS GRID --- */
    .users-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .user-card {
        text-decoration: none;
        background: #ffffff;
        border-radius: 18px;
        padding: 22px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        transition: all 0.25s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .user-card:hover {
        transform: translateY(-5px);
        border-color: var(--primary);
        box-shadow: 0 12px 25px rgba(0, 118, 254, 0.18);
    }

    .user-top {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .avatar {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        display: flex;
        justify-content: center;
        align-items: center;
        color: white;
        font-size: 1.3rem;
        box-shadow: 0 4px 10px rgba(0, 118, 254, 0.25);
    }

    .username {
        color: var(--dark);
        font-weight: 700;
        font-size: 1.05rem;
    }

    /* --- ROLE BADGES --- */
    .role {
        margin-top: 6px;
        display: inline-block;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: capitalize;
    }

    .role.employee {
        background: rgba(59, 130, 246, 0.12);
        color: #2563eb;
    }

    .role.admin {
        background: rgba(16, 185, 129, 0.12);
        color: #059669;
    }

    .role.super_admin {
        background: rgba(139, 92, 246, 0.12);
        color: #7c3aed;
    }

    .open-chat {
        margin-top: 20px;
        color: var(--primary);
        font-weight: 700;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 8px;
        padding-top: 12px;
        border-top: 1px dashed #f1f5f9;
    }

    /* --- BUTTONS --- */
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

    .empty {
        text-align: center;
        color: var(--text-muted);
        padding: 40px;
        grid-column: 1 / -1;
    }

    /* --- MOBILE RESPONSIVENESS --- */
    @media (max-width: 650px) {
        .container {
            padding: 25px 20px;
        }

        .header-info h1 {
            font-size: 1.4rem;
        }

        .users-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
</head>
<body>

<div class="container">

    <div class="header-bar">
        <div class="header-info">
            <h1>
                <i class="fa-solid fa-comments"></i>
                Internal Messaging
            </h1>
            <p>Select a team member or administrator to start a conversation.</p>
        </div>

        <!-- DIRECT ROUTE TO HOME PAGE FOR USERS, SUPER ADMIN PORTAL FOR SUPER ADMINS -->
        <a href="<?php echo (isset($_SESSION['role']) && $_SESSION['role'] === 'super_admin') ? 'super_admin_dashboard.php' : 'home.php'; ?>" class="back-btn">
            <i class="fa-solid <?php echo (isset($_SESSION['role']) && $_SESSION['role'] === 'super_admin') ? 'fa-arrow-left' : 'fa-house'; ?>"></i>
            Back to <?php echo (isset($_SESSION['role']) && $_SESSION['role'] === 'super_admin') ? 'Dashboard' : 'Home'; ?>
        </a>
    </div>

    <div class="users-grid">
        <?php
        if(mysqli_num_rows($users) > 0){
            while($user = mysqli_fetch_assoc($users)){
                $role_class = strtolower($user['role']);
        ?>
        <a href="chat.php?user=<?php echo $user['id']; ?>" class="user-card">
            <div class="user-top">
                <div class="avatar">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div>
                    <div class="username">
                        <?php echo htmlspecialchars($user['username']); ?>
                    </div>
                    <div class="role <?php echo $role_class; ?>">
                        <?php echo ucfirst(str_replace('_', ' ', $user['role'])); ?>
                    </div>
                </div>
            </div>

            <div class="open-chat">
                <i class="fa-solid fa-paper-plane"></i>
                Open Chat
            </div>
        </a>
        <?php
            }
        } else {
        ?>
        <div class="empty">
            <i class="fa-solid fa-users-slash" style="font-size: 2.5rem; margin-bottom: 12px; display: block; opacity: 0.4;"></i>
            No other users found in the system.
        </div>
        <?php } ?>
    </div>

</div>

</body>
</html>