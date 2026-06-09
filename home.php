<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Employee Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="header">
        <h1>Employee Management System</h1>
        <p>Welcome <?php echo $_SESSION['username']; ?></p>
    </div>

    <div class="menu">
        <a href="home.php">Home</a>
        <a href="employees.php">Employees</a>
        <a href="#">Attendance</a>
        <a href="#">Reports</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <h2>Dashboard</h2>

        <div class="card">
            <h3>Total Employees</h3>
            <p>0</p>
        </div>

        <div class="card">
            <h3>Departments</h3>
            <p>0</p>
        </div>

        <div class="card">
            <h3>Attendance Today</h3>
            <p>0</p>
        </div>
    </div>

</body>
</html>