<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("connect.php");

/* ONLY SUPER ADMIN GUARD */
if (empty($_SESSION['username']) || empty($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header("Location: login.php");
    exit();
}

$message = $_SESSION['flash_message'] ?? "";
$message_type = $_SESSION['flash_type'] ?? "";
unset($_SESSION['flash_message'], $_SESSION['flash_type']);

/* =====================================================
   ADD ANNOUNCEMENT
   ===================================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_announcement'])) {
    $title = trim($_POST['title'] ?? '');
    $announcement_message = trim($_POST['message'] ?? '');
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';

    if (empty($title) || empty($announcement_message) || empty($start_date) || empty($end_date)) {
        $_SESSION['flash_message'] = "Please fill in all required fields.";
        $_SESSION['flash_type'] = "error";
    } elseif ($end_date < $start_date) {
        $_SESSION['flash_message'] = "End date cannot be earlier than start date.";
        $_SESSION['flash_type'] = "error";
    } else {
        $created_by = $_SESSION['user_id'] ?? null;

        $stmt = $conn->prepare("
            INSERT INTO announcements (title, message, start_date, end_date, created_by)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssi", $title, $announcement_message, $start_date, $end_date, $created_by);

        if ($stmt->execute()) {
            /* OPTIMIZED: Insert notifications for all relevant employees in a single batch query */
            $notification_text = "New announcement: " . $title;
            $notif_stmt = $conn->prepare("
                INSERT INTO notifications (employee_id, message, status)
                SELECT id, ?, 'Unread' FROM employees WHERE role != 'super_admin'
            ");
            $notif_stmt->bind_param("s", $notification_text);
            $notif_stmt->execute();
            $notif_stmt->close();

            $_SESSION['flash_message'] = "Announcement published successfully!";
            $_SESSION['flash_type'] = "success";
        } else {
            $_SESSION['flash_message'] = "Database error: " . $stmt->error;
            $_SESSION['flash_type'] = "error";
        }
        $stmt->close();
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

/* =====================================================
   DELETE ANNOUNCEMENT
   ===================================================== */
if (isset($_GET['delete'])) {
    $id = filter_var($_GET['delete'], FILTER_VALIDATE_INT);

    if ($id) {
        $stmt = $conn->prepare("DELETE FROM announcements WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['flash_message'] = "Announcement deleted successfully.";
        $_SESSION['flash_type'] = "success";
    }

    header("Location: manage_announcements.php");
    exit();
}

/* =====================================================
   FETCH ANNOUNCEMENTS
   ===================================================== */
$result = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Announcements</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --bg-gradient: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            --card-bg: rgba(255, 255, 255, 0.98);
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --radius: 12px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        body {
            min-height: 100vh;
            padding: 40px 20px;
            background: var(--bg-gradient);
            color: var(--text-main);
        }

        .container {
            max-width: 1100px;
            margin: auto;
            background: var(--card-bg);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(10px);
        }

        .header {
            text-align: center;
            margin-bottom: 35px;
        }

        .header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .header p {
            color: var(--text-muted);
            margin-top: 6px;
        }

        /* ALERTS */
        .alert {
            padding: 14px 18px;
            border-radius: var(--radius);
            margin-bottom: 25px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert.success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .alert.error { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

        /* FORM CARD */
        .form-card {
            background: #f8fafc;
            padding: 28px;
            border-radius: var(--radius);
            margin-bottom: 40px;
            border: 1px solid var(--border-color);
        }

        .form-card h2 {
            font-size: 1.25rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #1e293b;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 8px;
            color: #334155;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            outline: none;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            background: #ffffff;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .date-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: var(--radius);
            cursor: pointer;
            font-weight: 600;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .btn:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
        }

        /* TABLE STYLING */
        .table-wrapper {
            overflow-x: auto;
            border-radius: var(--radius);
            border: 1px solid var(--border-color);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            text-align: left;
        }

        th {
            background: #f1f5f9;
            color: #475569;
            font-weight: 600;
            padding: 16px;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border-color);
        }

        td {
            padding: 16px;
            border-bottom: 1px solid var(--border-color);
            color: #334155;
            font-size: 0.95rem;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: #f8fafc;
        }

        /* BADGES */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-active { background: #dcfce7; color: #15803d; }
        .badge-expired { background: #fef2f2; color: #b91c1c; }

        .delete-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #ef4444;
            background: #fef2f2;
            padding: 6px 12px;
            border-radius: var(--radius);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }

        .delete-btn:hover {
            background: #ef4444;
            color: white;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 30px;
            padding: 10px 20px;
            background: #0f172a;
            color: white;
            text-decoration: none;
            border-radius: var(--radius);
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .back-btn:hover {
            background: #1e293b;
        }

        @media(max-width: 768px) {
            .container { padding: 20px; }
            .date-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="container">

    <div class="header">
        <h1><i class="fa-solid fa-bullhorn"></i> Company Announcements</h1>
        <p>Create, issue, and manage company-wide notices.</p>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert <?= $message_type; ?>">
            <i class="fa-solid <?= $message_type === 'success' ? 'fa-circle-check' : 'fa-triangle-exclamation'; ?>"></i>
            <?= htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <!-- ADD ANNOUNCEMENT FORM -->
    <div class="form-card">
        <h2><i class="fa-solid fa-plus-circle"></i> Create New Announcement</h2>
        <form method="POST">
            <div class="form-group">
                <label for="title">Announcement Title</label>
                <input type="text" id="title" name="title" placeholder="e.g. Scheduled System Maintenance" required>
            </div>

            <div class="form-group">
                <label for="message">Announcement Message</label>
                <textarea id="message" name="message" placeholder="Provide full details here..." required></textarea>
            </div>

            <div class="date-grid">
                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" required>
                </div>
                <div class="form-group">
                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date" required>
                </div>
            </div>

            <button type="submit" name="add_announcement" class="btn">
                <i class="fa-solid fa-paper-plane"></i> Publish Announcement
            </button>
        </form>
    </div>

    <!-- EXISTING ANNOUNCEMENTS -->
    <h2 style="font-size: 1.25rem; margin-bottom: 15px; color: #1e293b;">
        <i class="fa-solid fa-list"></i> Existing Announcements
    </h2>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Message</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php 
                        $is_active = $row['end_date'] >= date("Y-m-d");
                    ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['title']); ?></strong></td>
                        <td><?= htmlspecialchars($row['message']); ?></td>
                        <td><?= date("M d, Y", strtotime($row['start_date'])); ?></td>
                        <td><?= date("M d, Y", strtotime($row['end_date'])); ?></td>
                        <td>
                            <span class="badge <?= $is_active ? 'badge-active' : 'badge-expired'; ?>">
                                <?= $is_active ? 'Active' : 'Expired'; ?>
                            </span>
                        </td>
                        <td>
                            <a href="manage_announcements.php?delete=<?= $row['id']; ?>" 
                               class="delete-btn" 
                               onclick="return confirm('Are you sure you want to delete this announcement?');">
                                <i class="fa-solid fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 30px; color: var(--text-muted);">
                        No announcements found.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <a href="super_admin_dashboard.php" class="back-btn">
        <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
    </a>

</div>

</body>
</html>