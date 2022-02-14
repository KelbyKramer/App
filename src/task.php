<?php
include("functions.php");

$conn = dbConnect();

$date = date("Y/m/d H:i:s");
$sql = "DELETE FROM events WHERE expireDateTime < ".$date;

$result = mysqli_query($conn, $sql);

 ?>
