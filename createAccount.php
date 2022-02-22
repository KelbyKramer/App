<?php

include("src/functions.php");

if(isset($_POST) && count($_POST) > 0){
  $errorCount = 0;
  $errorMessage = "";
  if($_POST['userName'] == "" || !(isset($_POST['userName']))){
    echo "<div style='color:black;'>Username cannot be blank</div>";
    $errorCount++;
  }

  if($_POST['email'] == "" || !(isset($_POST['email']))){
    echo "<div style='color:black;'>Email cannot be blank</div>";
    $errorCount++;
  }

  if(strpos($_POST['email'], "mnsu.edu") == False){
    $errorMessage .= "<div style='color:black;'>Email must be your mnsu.edu email</div>";
    $errorCount++;
  }

  //passwords dont match
  if($_POST['password'] != $_POST['re-password']){
    echo "<div style='color:black;'>Passwords don't match</div>";
    $errorCount++;
  }
  //passwords arent sufficient length
  if(strlen($_POST['password']) < 6 && strlen($_POST['re-password']) < 6){
    echo "<div style='color:black;'>Password must be at least 6 characters long</div>";
    $errorCount++;
  }

  $conn = dbConnect();
  $username = $_POST['userName'];
  $password = $_POST['password'];
  $email = $_POST['email'];
  //TODO: This query is unsafe
  //$sql = "SELECT * FROM Users WHERE Username='$username'";
  $result = query("Users", "*", array('Username' => $username));

  //$result = $conn->query($sql);
  if ($result->num_rows > 0) {
    //username is taken
    echo "<div style='color:black;'>Username is alreay taken</div>";
    $errorCount++;
  }

  $result = query("Users", "*", array('Email' => $email));

  if ($result->num_rows > 0) {
    //username is taken
    echo "<div style='color:black;'>Email is alreay taken</div>";
    $errorCount++;
  }
  //At this point, the user has created a valid Account
  if($errorCount == 0){
    //register the user and log them in
    echo "An email has been sent, click the link to confirm your registration";
    //TODO: Create field in user table that is boolean if their account is verified
    //or not and check upon login if they are verified
    registerUser($username, $password, $email);
    login($username, $password);
  }
  else{
    //Display error message
    echo $errorMessage;
  }
}

 ?>

 <html lang="en">
 <meta charset="utf-8" />

 <meta name="viewport" content="width=device-width, initial-scale=1"/>
 <link rel="stylesheet" href="style.css">

  <form method='post' action=''>
    <input id="field" name='userName' type="text" placeholder='Username' autocomplete='off'/>
    <input id="email" name='email' type="text" placeholder='E-mail (Use your @mnsu.edu email)' autocomplete='off'/>
    <input id="password" name='password' type="password" placeholder='Password (Must be at least 6 characters)' autocomplete='off'/>
    <input id="re-password" name='re-password' type="password" placeholder='Re-Enter Password' autocomplete='off'/>
    <button id="submit" type="submit">Create Account</button>
  </form>

 </html>
