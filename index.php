<?php

// change this appropriately

require('../../db.inc.php');

$uri = explode("/",trim($_SERVER['REQUEST_URI']));

echo $_SERVER['REMOTE_ADDR'];

// print_r ($uri);

$returnData = array();


$sql = "select * from users";
$result = mysqli_query($conn, $sql);
print_r(mysqli_fetch_assoc($result));

// close connection
$conn->close();

echo "just checking";
?>
