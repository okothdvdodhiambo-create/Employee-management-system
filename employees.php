<?php
include("connect.php");

$message = "";

if(isset($_POST['register'])){

    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $department = $_POST['department'];
    $position = $_POST['position'];
    $phone = $_POST['phone'];
    $salary = $_POST['salary'];

    $sql = "INSERT INTO details
    (fullname, email, department, position, phone, salary)

    VALUES

    ('$fullname', '$email', '$department',
     '$position', '$phone', '$salary')";

    $result = mysqli_query($conn, $sql);

    if($result){
        $message = "Employee Registered Successfully!";
    }else{
        $message = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Employee Registration</title>

<style>

body{
    font-family: Arial, sans-serif;
    background:#f4f4f4;
}

.container{
    width:500px;
    margin:40px auto;
    background:white;
    padding:20px;
    border-radius:10px;
    box-shadow:0 0 10px rgba(0,0,0,0.2);
}

h2{
    text-align:center;
}

input{
    width:100%;
    padding:10px;
    margin-top:8px;
    margin-bottom:15px;
}

button{
    width:100%;
    padding:12px;
    background:#007bff;
    color:white;
    border:none;
    cursor:pointer;
}

button:hover{
    background:#0056b3;
}

.message{
    color:green;
    text-align:center;
    margin-bottom:10px;
}

</style>

</head>

<body>

<div class="container">

<h2>Employee Registration Form</h2>

<?php
if($message != ""){
    echo "<p class='message'>$message</p>";
}
?>

<form method="POST">

<label>Full Name</label>
<input type="text"
name="fullname"
required>

<label>Email Address</label>
<input type="email"
name="email"
required>

<label>Department</label>
<input type="text"
name="department"
required>

<label>Position</label>
<input type="text"
name="position"
required>

<label>Phone Number</label>
<input type="text"
name="phone">

<label>Salary</label>
<input type="number"
step="0.01"
name="salary">

<button type="submit" name="register">
Register Employee
</button>

</form>

</div>

</body>
</html>