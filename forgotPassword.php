<?php
include("src/functions.php");
include("src/config.php");

if(isset($_POST) && count($_POST) == 1){
  //an email was provided
  $email = $_POST['email'];
  $str = generateRandomString();
  date_default_timezone_set('America/Chicago'); // CDT
  $time = date('Y-m-d H:i:s');
  $msg = "Click here to reset your password: ".FORGOT_PASSWORD_ADDRESS."?str=".$str." This link expires in 15 minutes.";
  $msg = wordwrap($msg,70);
  updateQuery("users", "forgotPasswordString='".$str."' , forgotPasswordTimestamp='".$time."'", " email='".$email."'");
  sendEmail("kramerkelby@gmail.com", "App Reset Password Link", $msg, 'From: kramerkelby@gmail.com');
}

 ?>
<html>
<link rel="stylesheet" href="style.css">
  <form method='post' action=''>
    <input id="field" name='email' type="text" placeholder='Enter your email' autocomplete='off'/>
    <button type="submit" id="submit">Send email</button>
  </form>
</html>
