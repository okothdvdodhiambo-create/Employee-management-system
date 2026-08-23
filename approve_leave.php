<?php

include("connect.php");

$id = $_GET['id'];

mysqli_query(
$conn,

"UPDATE leave_requests

SET status='Approved'

WHERE id='$id'"
);
mysqli_query(
$conn,
"INSERT INTO notifications
(employee_id,message)

VALUES

(
'$employee_id',
'Your leave request has been approved.'
)"
);
header(
"Location: manage_leave.php"
);