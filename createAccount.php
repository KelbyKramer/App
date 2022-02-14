<?php

include("src/functions.php");

if(isset($_POST) && count($_POST) > 0){
  $errorCount = 0;
  if($_POST['userName'] == "" || !(isset($_POST['userName']))){
    echo "Username cannot be blank";
    $errorCount++;
  }

  if($_POST['email'] == "" || !(isset($_POST['email']))){
    echo "Email cannot be blank";
    $errorCount++;
  }

  //passwords dont match
  if($_POST['password'] != $_POST['re-password']){
    echo "Passwords don't match";
    $errorCount++;
  }
  //passwords arent sufficient length
  if(strlen($_POST['password']) < 6 && strlen($_POST['re-password']) < 6){
    echo "Password must be at least 6 characters long";
    $errorCount++;
  }

  $conn = dbConnect();
  $username = $_POST['userName'];
  $password = $_POST['password'];
  $email = $_POST['email'];
  //TODO: This query is unsafe
  $sql = "SELECT * FROM Users WHERE Username='$username'";

  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    //username is taken
    echo "Username is alreay taken";
    $errorCount++;
  }
  //At this point, the user has created a valid Account
  if($errorCount == 0){
    //register the user and log them in
    registerUser($username, $password, $email);
    login($username, $password);
  }
}

 ?>

 <html lang="en">
 <meta charset="utf-8" />

 <meta name="viewport" content="width=device-width, initial-scale=1"/>
 <link rel="stylesheet" href="style.css">

  <form method='post' action=''>
    <input id="field" name='userName' type="text" placeholder='Username' autocomplete='off'/>
    <input id="email" name='email' type="text" placeholder='E-mail' autocomplete='off'/>
    <input id="password" name='password' type="password" placeholder='Password' autocomplete='off'/>
    <input id="re-password" name='re-password' type="password" placeholder='Re-Enter Password' autocomplete='off'/>
    <button id="submit" type="submit">Create Account</button>
  </form>

 </html>
