<?php
session_start();

if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

<h1>Welcome <?php echo $_SESSION['username']; ?></h1>

<p>Login Successful</p>



</div>
<a href="logout.php">
    <button>Logout</button>
</a>

</body>

</html>