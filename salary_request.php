<?php
session_start();
include("connect.php");

if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

$message = "";
$message_type = "";

if(isset($_POST['submit_request'])){

    $employee_name = $_SESSION['username'];
    $employee_email = $_SESSION['email'] ?? '';

    $employee_code = $_POST['employee_code'];
    $amount = $_POST['amount'];
    $currency = $_POST['currency'];
    $payment_method = $_POST['payment_method'];
    $phone_number = $_POST['phone_number'] ?? '';
    $paypal_email = $_POST['paypal_email'] ?? '';
    $account_number = $_POST['account_number'] ?? '';
    $request_month = $_POST['request_month'];
    $request_year = $_POST['request_year'];

    $stmt = $conn->prepare(
    "INSERT INTO payment_requests
    (
        employee_email,
        employee_name,
        employee_code,
        amount,
        currency,
        payment_method,
        phone_number,
        paypal_email,
        account_number,
        request_month,
        request_year
    )
    VALUES
    (?,?,?,?,?,?,?,?,?,?,?)"
    );

    // FIXED: Type definition updated to match 11 parameters correctly
    // s = string, d = double/decimal, i = integer
    $stmt->bind_param(
        "sssds-sssssi", 
        $employee_email,
        $employee_name,
        $employee_code,
        $amount,
        $currency,
        $payment_method,
        $phone_number,
        $paypal_email,
        $account_number,
        $request_month,
        $request_year
    );

    // Standard string syntax for bind_param
    $stmt->bind_param(
        "sssdssssssi",
        $employee_email,
        $employee_name,
        $employee_code,
        $amount,
        $currency,
        $payment_method,
        $phone_number,
        $paypal_email,
        $account_number,
        $request_month,
        $request_year
    );

    if($stmt->execute()){
        $message = "Payment request submitted successfully!";
        $message_type = "success";
    }else{
        $message = "Error: ".$stmt->error;
        $message_type = "error";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Salary Payment Request</title>

<!-- Font Awesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    :root {
        --primary: #0076fe;
        --secondary: #8b5cf6;
        --accent-teal: #0d9488;
        --dark: #0f172a;
        --text-muted: #64748b;
        --glass-bg: rgba(255, 255, 255, 0.92);
        --glass-border: rgba(255, 255, 255, 0.4);
        --success-bg: rgba(16, 185, 129, 0.12);
        --success-color: #059669;
        --error-bg: rgba(239, 68, 68, 0.12);
        --error-color: #dc2626;
        --dark-bg: #090d16;
        --text-main: #f8fafc;
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
    }

    body {
        background: linear-gradient(-45deg, #0f172a, #7e22ce, #e11d48, #0d9488, #1e3a8a);
        background-size: 400% 400%;
        animation: vibrantGradient 18s ease infinite;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding-top: 40px;
    }

    @keyframes vibrantGradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* --- GLASS CONTAINER --- */
    .container {
        max-width: 850px;
        width: 90%;
        background: var(--glass-bg);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        padding: 40px;
        border-radius: 24px;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.4);
        border: 1px solid var(--glass-border);
        margin-bottom: 50px;
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
        font-size: 1.7rem;
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

    /* --- FORM GRID --- */
    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group.full-width {
        grid-column: span 2;
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

    input, select {
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

    input:focus, select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(0, 118, 254, 0.15);
    }

    /* --- BUTTONS & ACTIONS --- */
    .form-actions {
        margin-top: 30px;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

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
        width: fit-content;
    }

    .back-btn:hover {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
        transform: translateY(-2px);
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

        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-group.full-width {
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
    }
</style>
</head>
<body>

<div class="container">

    <div class="header-bar">
        <h1>
            <i class="fa-solid fa-money-bill-wave"></i>
            Salary Payment Request
        </h1>

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

        <div class="form-grid">

            <div class="form-group">
                <label><i class="fa-solid fa-id-badge"></i> Employee Code</label>
                <input type="text" name="employee_code" placeholder="e.g. EMP-102" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-calendar-days"></i> Request Month</label>
                <select name="request_month" required>
                    <option value="January" <?php if(date('F')=='January') echo 'selected'; ?>>January</option>
                    <option value="February" <?php if(date('F')=='February') echo 'selected'; ?>>February</option>
                    <option value="March" <?php if(date('F')=='March') echo 'selected'; ?>>March</option>
                    <option value="April" <?php if(date('F')=='April') echo 'selected'; ?>>April</option>
                    <option value="May" <?php if(date('F')=='May') echo 'selected'; ?>>May</option>
                    <option value="June" <?php if(date('F')=='June') echo 'selected'; ?>>June</option>
                    <option value="July" <?php if(date('F')=='July') echo 'selected'; ?>>July</option>
                    <option value="August" <?php if(date('F')=='August') echo 'selected'; ?>>August</option>
                    <option value="September" <?php if(date('F')=='September') echo 'selected'; ?>>September</option>
                    <option value="October" <?php if(date('F')=='October') echo 'selected'; ?>>October</option>
                    <option value="November" <?php if(date('F')=='November') echo 'selected'; ?>>November</option>
                    <option value="December" <?php if(date('F')=='December') echo 'selected'; ?>>December</option>
                </select>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-coins"></i> Amount</label>
                <input type="number" step="0.01" name="amount" placeholder="0.00" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-globe"></i> Currency</label>
                <select name="currency" required>
                    <option value="KES">KES - Kenyan Shilling</option>
                    <option value="USD">USD - US Dollar</option>
                    <option value="EUR">EUR - Euro</option>
                    <option value="GBP">GBP - British Pound</option>
                </select>
            </div>

            <div class="form-group full-width">
                <label><i class="fa-solid fa-credit-card"></i> Payment Method</label>
                <select name="payment_method" id="payment_method" required onchange="togglePaymentFields()">
                    <option value="Mpesa">Mpesa</option>
                    <option value="Airtel Money">Airtel Money</option>
                    <option value="PayPal">PayPal</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <!-- DYNAMIC CONDITIONAL FIELDS -->
            <div class="form-group conditional-field full-width" id="phone_group">
                <label><i class="fa-solid fa-phone"></i> Mobile Phone Number</label>
                <input type="text" name="phone_number" placeholder="0712345678 or +254...">
            </div>

            <div class="form-group conditional-field full-width" id="paypal_group" style="display: none;">
                <label><i class="fa-brands fa-paypal"></i> PayPal Email Address</label>
                <input type="email" name="paypal_email" placeholder="name@domain.com">
            </div>

            <div class="form-group conditional-field full-width" id="bank_group" style="display: none;">
                <label><i class="fa-solid fa-building-columns"></i> Bank Account Number</label>
                <input type="text" name="account_number" placeholder="Enter bank account digits">
            </div>

            <div class="form-group full-width">
                <label><i class="fa-solid fa-calendar"></i> Request Year</label>
                <input type="number" name="request_year" value="<?php echo date('Y'); ?>" required>
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" name="submit_request" class="btn-submit">
                <i class="fa-solid fa-paper-plane"></i>
                Submit Payment Request
            </button>
        </div>

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
            <a href="https://facebook.com" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="https://instagram.com" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="https://x.com" target="_blank" aria-label="X (Twitter)"><i class="fab fa-x-twitter"></i></a>
            <a href="https://linkedin.com" target="_blank" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
        </div>

        <p class="copyright">
            © <?php echo date('Y'); ?> Employee Management System &bull; 
            Developed by <strong>David Okoth</strong> &bull; 
            All Rights Reserved.
        </p>
    </div>
</footer>

<script>
function togglePaymentFields() {
    const method = document.getElementById('payment_method').value;
    const phoneGroup = document.getElementById('phone_group');
    const paypalGroup = document.getElementById('paypal_group');
    const bankGroup = document.getElementById('bank_group');

    phoneGroup.style.display = 'none';
    paypalGroup.style.display = 'none';
    bankGroup.style.display = 'none';

    if (method === 'Mpesa' || method === 'Airtel Money') {
        phoneGroup.style.display = 'flex';
    } else if (method === 'PayPal') {
        paypalGroup.style.display = 'flex';
    } else if (method === 'Bank Transfer') {
        bankGroup.style.display = 'flex';
    } else {
        phoneGroup.style.display = 'flex';
        paypalGroup.style.display = 'flex';
        bankGroup.style.display = 'flex';
    }
}

document.addEventListener('DOMContentLoaded', togglePaymentFields);
</script>

</body>
</html>