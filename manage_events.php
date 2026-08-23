<?php
session_start();
include("connect.php");

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'super_admin') {
    header("Location: login.php");
    exit();
}

$message = "";
$message_type = "";

/* --- ADD EVENT --- */
if (isset($_POST['add_event'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    if (!empty($title) && !empty($description) && !empty($start_date) && !empty($end_date)) {
        $stmt_add = mysqli_prepare($conn, "INSERT INTO events (title, description, start_date, end_date) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt_add, "ssss", $title, $description, $start_date, $end_date);

        if (mysqli_stmt_execute($stmt_add)) {
            $message = "Event added successfully!";
            $message_type = "success";
        } else {
            $message = "Error adding event: " . mysqli_error($conn);
            $message_type = "danger";
        }
        mysqli_stmt_close($stmt_add);
    } else {
        $message = "Please fill in all required fields.";
        $message_type = "danger";
    }
}
$employees = mysqli_query(
$conn,
"SELECT id FROM employees
 WHERE role='employee'"
);

while($emp = mysqli_fetch_assoc($employees)){

    mysqli_query(
    $conn,

    "INSERT INTO notifications
    (employee_id,message)

    VALUES

    (
    '".$emp['id']."',

    'New company event:
    ".$title."'
    )"
    );
}

/* --- DELETE EVENT --- */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $stmt_del = mysqli_prepare($conn, "DELETE FROM events WHERE id = ?");
    mysqli_stmt_bind_param($stmt_del, "i", $id);
    mysqli_stmt_execute($stmt_del);
    mysqli_stmt_close($stmt_del);

    header("Location: manage_events.php");
    exit();
}

/* --- FETCH EVENTS --- */
$result = mysqli_query($conn, "SELECT * FROM events ORDER BY start_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events | Admin Panel</title>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary: #0076fe;
            --secondary: #8b5cf6;
            --dark: #0f172a;
            --danger: #ef4444;
            --success: #10b981;
            --glass-bg: rgba(255, 255, 255, 0.94);
            --glass-border: rgba(255, 255, 255, 0.4);
            --dark-bg: #090d16;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --dark-border: rgba(255, 255, 255, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        html, body {
            min-height: 100vh;
            width: 100%;
        }

        body {
            background: linear-gradient(-45deg, #0f172a, #7e22ce, #0076fe, #0d9488);
            background-size: 400% 400%;
            animation: gradientMove 15s ease infinite;
            display: flex;
            flex-direction: column;
        }

        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* --- WRAPPER & CONTAINER --- */
        .main-wrapper {
            flex: 1 0 auto;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 40px 20px;
            width: 100%;
        }

        .container {
            max-width: 1100px;
            width: 100%;
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            padding: 35px 40px;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            border: 1px solid var(--glass-border);
        }

        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .header-bar h1 {
            font-size: 1.8rem;
            color: var(--dark);
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-bar h1 i {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
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

        /* --- ALERT MESSAGES --- */
        .alert {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-weight: 600;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert.success {
            background: rgba(16, 185, 129, 0.12);
            color: #047857;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .alert.danger {
            background: rgba(239, 68, 68, 0.12);
            color: #b91c1c;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        /* --- FORM SECTION --- */
        .form-card {
            background: #ffffff;
            padding: 25px;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04);
            margin-bottom: 35px;
        }

        .form-title {
            font-size: 1.1rem;
            color: var(--dark);
            margin-bottom: 18px;
            font-weight: 700;
        }

        .event-form {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
        }

        .form-group-full {
            grid-column: span 2;
        }

        .event-form label {
            display: block;
            font-size: 0.85rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: 6px;
        }

        .event-form input,
        .event-form textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s ease;
        }

        .event-form input:focus,
        .event-form textarea:focus {
            border-color: var(--primary);
        }

        .event-form textarea {
            min-height: 100px;
            resize: vertical;
        }

        .btn-submit {
            grid-column: span 2;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 14px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 700;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            transition: all 0.25s ease;
            box-shadow: 0 4px 12px rgba(0, 118, 254, 0.25);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 118, 254, 0.35);
        }

        /* --- TABLE DESIGN --- */
        .table-card {
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04);
        }

        .table-card h2 {
            font-size: 1.1rem;
            padding: 20px 25px;
            background: #fafafa;
            border-bottom: 1px solid #e2e8f0;
            color: var(--dark);
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        table th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            padding: 14px 20px;
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
        }

        table td {
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            font-size: 0.95rem;
            vertical-align: middle;
            white-space: nowrap;
        }

        table tr:hover {
            background: #f8fafc;
        }

        .date-badge {
            display: inline-block;
            padding: 4px 10px;
            background: #f1f5f9;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 600;
            color: #475569;
        }

        .delete-btn {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            padding: 8px 14px;
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .delete-btn:hover {
            background: var(--danger);
            color: white;
        }

        .empty {
            text-align: center;
            color: #64748b;
            padding: 40px;
        }

        /* --- FULL-WIDTH FOOTER --- */
        .system-footer {
            width: 100%;
            margin-top: auto;
            background: var(--dark-bg);
            border-top: 1px solid var(--dark-border);
            padding: 35px 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 35px;
            box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.5);
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

        .copyright {
            font-size: 0.85rem;
            color: var(--text-muted);
            border-top: 1px solid var(--dark-border);
            padding-top: 14px;
        }

        .copyright strong {
            color: var(--text-main);
        }

        @media (max-width: 768px) {
            .container {
                padding: 25px 20px;
            }

            .event-form {
                grid-template-columns: 1fr;
            }

            .form-group-full,
            .btn-submit {
                grid-column: span 1;
            }

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

<div class="main-wrapper">
    <div class="container">

        <div class="header-bar">
            <h1>
                <i class="fa-solid fa-calendar-days"></i>
                Manage Company Events
            </h1>

            <a href="super_admin_dashboard.php" class="back-btn">
                <i class="fa-solid fa-arrow-left"></i>
                Back to Dashboard
            </a>
        </div>

        <?php if(!empty($message)){ ?>
            <div class="alert <?php echo $message_type; ?>">
                <i class="fa-solid <?php echo ($message_type === 'success') ? 'fa-circle-check' : 'fa-circle-exclamation'; ?>"></i>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php } ?>

        <!-- ADD EVENT FORM -->
        <div class="form-card">
            <div class="form-title">
                <i class="fa-solid fa-plus-circle" style="color: var(--primary);"></i> Create New Event
            </div>

            <form method="POST" class="event-form">

                <div class="form-group-full">
                    <label>Event Title</label>
                    <input type="text" name="title" placeholder="e.g. Q3 Strategy & Innovation Summit" required>
                </div>

                <div class="form-group-full">
                    <label>Event Description</label>
                    <textarea name="description" placeholder="Brief details regarding the agenda, venue, or online link..." required></textarea>
                </div>

                <div>
                    <label>Start Date</label>
                    <input type="date" name="start_date" required>
                </div>

                <div>
                    <label>End Date</label>
                    <input type="date" name="end_date" required>
                </div>

                <button type="submit" name="add_event" class="btn-submit">
                    <i class="fa-solid fa-plus"></i>
                    Publish Event
                </button>

            </form>
        </div>

        <!-- EVENTS LIST TABLE -->
        <div class="table-card">
            <h2>
                <i class="fa-solid fa-list-check"></i> Published Events
            </h2>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if(mysqli_num_rows($result) > 0){
                            while($row = mysqli_fetch_assoc($result)){ 
                        ?>
                        <tr>
                            <td><strong>#<?php echo $row['id']; ?></strong></td>
                            <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                            <td style="white-space: normal; min-width: 250px; max-width: 320px;"><?php echo nl2br(htmlspecialchars($row['description'])); ?></td>
                            <td>
                                <span class="date-badge">
                                    <i class="fa-regular fa-calendar"></i> <?php echo date('d M Y', strtotime($row['start_date'])); ?>
                                </span>
                            </td>
                            <td>
                                <span class="date-badge">
                                    <i class="fa-regular fa-calendar-check"></i> <?php echo date('d M Y', strtotime($row['end_date'])); ?>
                                </span>
                            </td>
                            <td>
                                <a href="manage_events.php?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this event?')">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                        ?>
                        <tr>
                            <td colspan="6" class="empty">
                                <i class="fa-solid fa-calendar-xmark" style="font-size: 2.2rem; margin-bottom: 10px; display: block; opacity: 0.4;"></i>
                                No company events currently scheduled.
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
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