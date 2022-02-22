<?php
include("src/dbDefine.php");
echo "Account has been confirmed";
$conn = dbConnect();
//$sql = "SELECT User_ID, Password, current_tokens, verify FROM Users WHERE Username='$sanitized_username'";
$sql = "UPDATE users SET verify = 1 WHERE Email='".$_GET['email']."'";
$result = mysqli_query($conn, $sql);
echo "You've been successfully verified!";
 ?>
