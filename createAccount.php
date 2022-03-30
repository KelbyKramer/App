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
  //$sql = "SELECT * FROM users WHERE Username='$username'";
  $result = query("users", "*", array('Username' => $username));

  //$result = $conn->query($sql);
  if ($result->num_rows > 0) {
    //username is taken
    echo "<div style='color:black;'>Username is alreay taken</div>";
    $errorCount++;
  }

  $result = query("users", "*", array('Email' => $email));

  if ($result->num_rows > 0) {
    //username is taken
    echo "<div style='color:black;'>Email is alreay taken</div>";
    $errorCount++;
  }
  //At this point, the user has created a valid Account
  if($errorCount == 0){
    //register the user and log them in
    echo "An email has been sent, click the link to confirm your registration";
    //generate 12 character string to put into database and send in mail link
    $str = generateRandomString();
    echo $str;
    generateRegisterEmail("kramerkelby@gmail.com", $str);
    registerUser($username, $password, $email, $str);
    //login($username, $password);
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

 <head>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
 <script src="src/script.js"></script>
 </head>

  <form method='post' action=''>
    <input id="field" name='userName' type="text" placeholder='Username' autocomplete='off'/>
    <input id="email" name='email' type="text" placeholder='E-mail (Use your @mnsu.edu email)' autocomplete='off'/>
    <input id="password" name='password' type="password" placeholder='Password (Must be at least 6 characters)' autocomplete='off'/>
    <input id="re-password" name='re-password' type="password" placeholder='Re-Enter Password' autocomplete='off'/>
    <input style='display:none;' id="age" name='age' type="text" placeholder='Age (Optional)' autocomplete='off'/>
    <input style='display:none;' id="major" name='major' type="text" placeholder='Current Major (Optional)' autocomplete='off'/>
    <input style='display:none;' id="living" name='living' type="text" placeholder='Living Accomodations (Optional)' autocomplete='off'/>
    <button id="submit" type="submit">Create Account</button>
  </form>
  <button id="expand" onclick="showExtraFields();">Want 100 free tokens?  Click here to provide more information</button>

 </html>

 <script>
 function showExtraFields(){

   if($('#age:visible').length == 0){
     $('#age').show();
     $('#major').show();
     $('#living').show();
     $('#expand').html("Hide extra fields");
   }
   else{
     $('#age').hide();
     $('#major').hide();
     $('#living').hide();
     $('#expand').html("Show extra fields");
   }


 }


 </script>
