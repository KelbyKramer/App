<?php
include("src/functions.php");

if(isset($_POST) && count($_POST) == 2){

  $username = $_POST['userName'];
  $password = $_POST['password'];

  login($username, $password);
}

 ?>

 <html lang="en">
 <meta charset="utf-8" />

 <meta name="viewport" content="width=device-width, initial-scale=1"/>
 <link rel="stylesheet" href="style.css">

 <form method='post' action=''>
  <input id="field" name='userName' type="text" placeholder='Username' autocomplete='off'/>
  <input id="password" name='password' type="password" placeholder='Password' autocomplete='off'/>
  <button type="submit" id="submit">Login</button>
</form>

<a href='createAccount.php' class='d-flex justify-content-center'>Create Account</a>
</html>
