<?php
session_start();
include("connect.php");

// Safety check: ensure user is logged in
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

if(isset($_GET['id'])){
    // Sanitize the input parameter
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Execute query targeting your 'details' table layout
    $sql = "DELETE FROM details WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);
    
    if($result){
        // SMART REDIRECT: Looks up the exact page name you came from and links right back
        if(isset($_SERVER['HTTP_REFERER'])) {
            $referer = explode('?', $_SERVER['HTTP_REFERER'])[0];
            header("Location: " . $referer . "?status=deleted");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    header("Location: index.php");
    exit();
}
?>