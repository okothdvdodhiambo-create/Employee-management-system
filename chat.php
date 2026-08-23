<?php
session_start();
include("connect.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$current_user = $_SESSION['user_id'];

if (!isset($_GET['user'])) {
    header("Location: messages.php");
    exit();
}

$other_user = (int)$_GET['user'];

/* --- GET CHAT RECIPIENT --- */
$stmt_user = mysqli_prepare($conn, "SELECT id, username, role FROM employees WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt_user, "i", $other_user);
mysqli_stmt_execute($stmt_user);
$user_result = mysqli_stmt_get_result($stmt_user);

if (mysqli_num_rows($user_result) == 0) {
    header("Location: messages.php");
    exit();
}

$chat_user = mysqli_fetch_assoc($user_result);

/* --- SEND MESSAGE --- */
if (isset($_POST['send'])) {
    $message = trim($_POST['message']);

    if (!empty($message)) {
        $stmt_send = mysqli_prepare($conn, "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt_send, "iis", $current_user, $other_user, $message);
        mysqli_stmt_execute($stmt_send);

        header("Location: chat.php?user=" . $other_user);
        exit();
    }
}

/* --- LOAD CONVERSATION --- */
$stmt_msg = mysqli_prepare($conn, "
    SELECT * FROM messages 
    WHERE (sender_id = ? AND receiver_id = ?) 
       OR (sender_id = ? AND receiver_id = ?) 
    ORDER BY sent_at ASC
");
mysqli_stmt_bind_param($stmt_msg, "iiii", $current_user, $other_user, $other_user, $current_user);
mysqli_stmt_execute($stmt_msg);
$messages = mysqli_stmt_get_result($stmt_msg);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Chat with <?php echo htmlspecialchars($chat_user['username']); ?></title>

<!-- Font Awesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    :root {
        --primary-blue: #0076fe;
        --accent-purple: #8b5cf6;
        --sent-bg: #0076fe;
        --received-bg: #ffffff;
        --dark-text: #0f172a;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background: linear-gradient(-45deg, #0f172a, #7e22ce, #0076fe, #0d9488);
        background-size: 400% 400%;
        animation: gradientMove 15s ease infinite;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    @keyframes gradientMove {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* --- CHAT CONTAINER --- */
    .chat-container {
        width: 100%;
        max-width: 950px;
        height: 88vh;
        background: rgba(255, 255, 255, 0.94);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
        border: 1px solid rgba(255, 255, 255, 0.4);
        display: flex;
        flex-direction: column;
    }

    /* --- HEADER --- */
    .chat-header {
        background: linear-gradient(135deg, var(--primary-blue), var(--accent-purple));
        color: white;
        padding: 18px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .chat-user {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: white;
        color: var(--primary-blue);
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 1.2rem;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }

    .user-details h3 {
        font-size: 1.1rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .user-details small {
        opacity: 0.85;
        font-size: 0.8rem;
        text-transform: capitalize;
    }

    .back-btn {
        color: white;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.2);
        padding: 8px 16px;
        border-radius: 12px;
        transition: all 0.2s ease;
    }

    .back-btn:hover {
        background: rgba(255, 255, 255, 0.35);
        transform: translateX(-3px);
    }

    /* --- CHAT BODY --- */
    .chat-body {
        flex: 1;
        overflow-y: auto;
        padding: 25px;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .message {
        display: flex;
        width: 100%;
    }

    .sent {
        justify-content: flex-end;
    }

    .received {
        justify-content: flex-start;
    }

    .bubble {
        max-width: 68%;
        padding: 12px 18px;
        border-radius: 18px;
        word-wrap: break-word;
        font-size: 0.95rem;
        line-height: 1.45;
        position: relative;
        box-shadow: 0 2px 5px rgba(0,0,0,0.04);
    }

    .sent .bubble {
        background: var(--sent-bg);
        color: white;
        border-bottom-right-radius: 4px;
    }

    .received .bubble {
        background: var(--received-bg);
        color: var(--dark-text);
        border-bottom-left-radius: 4px;
        border: 1px solid #e2e8f0;
    }

    .time {
        font-size: 0.7rem;
        margin-top: 6px;
        opacity: 0.75;
        text-align: right;
    }

    .received .time {
        color: #64748b;
    }

    /* --- CHAT FOOTER --- */
    .chat-footer {
        padding: 18px;
        background: white;
        border-top: 1px solid #e2e8f0;
    }

    .chat-form {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .chat-form textarea {
        flex: 1;
        resize: none;
        border: 1px solid #cbd5e1;
        border-radius: 14px;
        padding: 12px 16px;
        height: 52px;
        font-size: 0.95rem;
        outline: none;
        transition: border-color 0.2s ease;
    }

    .chat-form textarea:focus {
        border-color: var(--primary-blue);
    }

    .chat-form button {
        border: none;
        background: var(--primary-blue);
        color: white;
        padding: 0 24px;
        height: 52px;
        border-radius: 14px;
        cursor: pointer;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .chat-form button:hover {
        background: #0060d4;
        transform: translateY(-1px);
    }

    .empty {
        text-align: center;
        color: #64748b;
        margin: auto;
        font-size: 0.95rem;
    }

    @media (max-width: 600px) {
        .chat-container {
            height: 95vh;
            border-radius: 16px;
        }

        .bubble {
            max-width: 82%;
        }
    }
    :root {
    --primary: #0076fe;
    --secondary: #8b5cf6;
    --dark: #0f172a;
    --text-muted: #64748b;
    --glass-footer-bg: rgba(255, 255, 255, 0.92);
    --glass-border: rgba(255, 255, 255, 0.4);
}

.system-footer {
    width: 100%;
    max-width: 1100px;
    margin: 40px auto 0 auto;
    background: var(--glass-footer-bg);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    padding: 35px 30px;
    display: flex;
    align-items: center;
    gap: 30px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
}

.footer-logo img {
    width: 90px;
    height: 90px;
    object-fit: contain;
    border-radius: 16px;
    padding: 8px;
    background: rgba(255, 255, 255, 0.8);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.footer-content {
    flex: 1;
}

.footer-content h3 {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 6px;
}

.footer-desc {
    color: #475569;
    font-size: 0.92rem;
    line-height: 1.6;
    margin-bottom: 18px;
    max-width: 750px;
}

.social-links {
    display: flex;
    gap: 12px;
    margin-bottom: 18px;
}

.social-links a {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: #f1f5f9;
    color: var(--dark);
    display: flex;
    justify-content: center;
    align-items: center;
    text-decoration: none;
    font-size: 0.95rem;
    border: 1px solid #e2e8f0;
    transition: all 0.25s ease;
}

.social-links a:hover {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: #ffffff;
    border-color: transparent;
    transform: translateY(-3px);
    box-shadow: 0 6px 15px rgba(0, 118, 254, 0.25);
}

.copyright {
    font-size: 0.85rem;
    color: var(--text-muted);
    border-top: 1px solid #e2e8f0;
    padding-top: 14px;
}

.copyright strong {
    color: var(--dark);
}

/* Responsive adjustment for small screens */
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

<div class="chat-container">

    <!-- HEADER -->
    <div class="chat-header">
        <div class="chat-user">
            <div class="avatar">
                <i class="fa-solid fa-user"></i>
            </div>
            <div class="user-details">
                <h3><?php echo htmlspecialchars($chat_user['username']); ?></h3>
                <small><?php echo ucfirst(str_replace('_', ' ', $chat_user['role'])); ?></small>
            </div>
        </div>

        <a href="messages.php" class="back-btn">
            <i class="fa-solid fa-arrow-left"></i>
            Back
        </a>
    </div>

    <!-- CHAT BODY -->
    <div class="chat-body" id="chatBody">
        <?php
        if (mysqli_num_rows($messages) > 0) {
            while ($msg = mysqli_fetch_assoc($messages)) {
                $class = ($msg['sender_id'] == $current_user) ? "sent" : "received";
        ?>
        <div class="message <?php echo $class; ?>">
            <div class="bubble">
                <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                <div class="time">
                    <?php echo date('d M Y, h:i A', strtotime($msg['sent_at'])); ?>
                </div>
            </div>
        </div>
        <?php
            }
        } else {
        ?>
        <div class="empty">
            <i class="fa-solid fa-comments" style="font-size: 2.5rem; margin-bottom: 10px; display: block; opacity: 0.4;"></i>
            No messages yet. Send a message to start the conversation!
        </div>
        <?php } ?>
    </div>

    <!-- FOOTER -->
    <div class="chat-footer">
        <form method="POST" class="chat-form" id="chatForm">
            <textarea 
                name="message" 
                id="messageInput"
                placeholder="Type your message..." 
                required></textarea>

            <button type="submit" name="send">
                <i class="fa-solid fa-paper-plane"></i>
                <span>Send</span>
            </button>
        </form>
    </div>

</div>

<script>
    // Auto-scroll to the newest message on page load
    const chatBody = document.getElementById('chatBody');
    chatBody.scrollTop = chatBody.scrollHeight;

    // Submit form on Enter key (Shift + Enter for line breaks)
    document.getElementById('messageInput').addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (this.value.trim() !== '') {
                document.getElementById('chatForm').submit();
            }
        }
    });
</script>
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