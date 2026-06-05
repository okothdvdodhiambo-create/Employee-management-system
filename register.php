<?php
include("connect.php");

if(isset($_POST['register'])){

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "INSERT INTO employees(username, email, password)
            VALUES('$username', '$email', '$password')";

    $result = mysqli_query($conn, $sql);

    if($result){
        echo "Registration Successful";
    }else{
        echo "Registration Failed";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

<h2>Employee Registration</h2>

<form method="POST">

    <input type="text" name="username" placeholder="Username" required>

    <input type="email" name="email" placeholder="Email" required>

    <input type="password" name="password" placeholder="Password" required>

    <button type="submit" name="register">Register</button>

</form>

<p>
    Already have account?
    <a href="login.php">Login Here</a>
</p>

</div>

</body>
</html>