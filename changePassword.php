<?php
include("src/functions.php");
$param =  htmlspecialchars($_GET['str']);
str_replace("\n","",$param);

if(strlen($param) != 12){
  echo "Error";
}
else{
  //$sql = "UPDATE users SET verify=1 WHERE verifyString=$param";
  //updateQuery("users", "verify=1", "verifyString='".$param."'");
  //echo "Your account has been verified";
  //echo "<button> <a href='index.php'>Log into your account</a> </button>";
  //TODO: Make it so that it logs in and redirects to dashborrd
}

if(isset($_POST) && count($_POST) > 0){
  $errorCount = 0;
  $errorMessage = "";

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

  $param =  htmlspecialchars($_GET['str']);
  str_replace("\n","",$param);
  str_replace(" ", "", $param);

  if(strlen($param) != 12){
    echo "<div style='color:black;'>There was an error with the verification code</div>";;
    $errorCount++;
  }
  else{
    //get the timestamp
    date_default_timezone_set('America/Chicago');
    $time = date('Y-m-d H:i:s');
    $where = array("forgotPasswordString" => $param);
    $result = query("users", "forgotPasswordTimestamp", $where);

    if ($result->num_rows > 0) {
      //username is taken
      while($row = $result->fetch_assoc()) {
        //var_dump($row);
        $timestamp = $row['forgotPasswordTimestamp'];
      }
      $time = strtotime($time);
      $timestamp = strtotime($timestamp);
      $difference = dateDiff($time, $timestamp);
      if($difference > 900){
        $errorCount++;
        echo $difference;
        echo "<div style='color:black;'>The reset password link has expired, please request another</div>";
      }
    }
    else{
      echo "<div style='color:black;'>Invalid password string was passed in</div>";
      $errorCount++;
    }
  }
  //At this point, the user has created a valid Account
  if($errorCount == 0){
    //update password
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    updateQuery("users", "Password='".$hashed_password."'", "forgotPasswordString='".$param."'");
    echo "Password has been updated";
  }
  else{
    //Display error message
    echo $errorMessage;
  }
}
 ?>


 <html>
 <meta charset="utf-8" />

 <meta name="viewport" content="width=device-width, initial-scale=1"/>
 <link rel="stylesheet" href="style.css">
 <meta charset="utf-8" />

 <meta name="viewport" content="width=device-width, initial-scale=1"/>
 <link rel="stylesheet" href="style.css">
 <form method='post' action=''>
   <input id="password" name='password' type="password" placeholder='Password (Must be at least 6 characters)' autocomplete='off'/>
   <input id="re-password" name='re-password' type="password" placeholder='Re-Enter Password' autocomplete='off'/>
   <button id="submit" type="submit">Change Password</button>
 </form>


 </html>
