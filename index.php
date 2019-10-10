<?php

// change this appropriately
include('/home/stu/superuser/db.inc.php');

$uri = explode("/",trim($_SERVER['REQUEST_URI']));

// print_r ($uri);

$returnData = array();

echo "<br/>";
echo "hello";


$sql = "select * from orders";
$result = mysqli_query($conn, $sql);
print_r(mysqli_fetch_assoc($result));

// close connection
$conn->close();

echo "just checking";
?>
