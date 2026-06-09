<?php
session_start();
include("connect.php");

if(isset($_POST['login'])){

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM employees
            WHERE email='$email'
            AND password='$password'";

    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) > 0){

        $row = mysqli_fetch_assoc($result);

        $_SESSION['username'] = $row['username'];

        header("Location: home.php");
        exit();

    }else{
        echo "Invalid Email or Password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

<h2>Employee Login</h2>

<form method="POST">

    <input type="email" name="email" placeholder="Email" required>

    <input type="password" name="password" placeholder="Password" required>

    <button type="submit" name="login">Login</button>

</form>

<p>
    No account?
    <a href="register.php">Register Here</a>
</p>

</div>

</body>
</html>