<?php
include("src/functions.php");
include("header.php");

if(isset($_POST) && count($_POST) == 3){
  session_start();
  if($_SESSION['captcha_text'] != $_POST['captcha_challenge']){
    echo "<div class='error'>The CAPTCHA was entered incorrectly</div>";
  }
  else{
    $username = $_POST['userName'];
    $password = $_POST['password'];
    login($username, $password);
  }
}
else{

}

/*
$servername = "localhost";
$username = "scrudu50_root";
$password = "+byia,hZ]-}y";
$dbname = "scrudu50_app";
*/
// Create connection
/*
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  echo "Connection failed";
}
else{
  echo "Connection success";
}


$conn = dbConnect();
$sql = "SELECT * FROM locations";

$result = mysqli_query($conn, $sql);
var_dump($result);*/
 ?>

 <head>
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
   <script src="src/script.js"></script>
 </head>
<header>
 <html lang="en">
 <meta charset="utf-8" />
 <meta name="viewport" content="width=device-width, initial-scale=1"/>
 <link rel="stylesheet" href="style.css">
 <h1>Maverick Rewards</h1>
 <h4> As easy as 1, 2, 3</h4>
</header>

 <body class='login'>
   <center>

   <ul class='login'>
     <div>Click each option to learn more:</div>
     <li id='1' onclick='popup()' class='login hidden trigger'>1. Attend Maverick Events</li>
     <li id='2' onclick='popup()' class='login hidden trigger'>2. Earn Tokens</li>
     <li id='3' onclick='popup()' class='login hidden trigger'>3. Redeem at your favorite restaurants</li>
   </ul>
   <div id='popup'>
     <p id='content'>Content is here</p>
   </div>

 </center>
   <form method='post' action=''>
    <input id="field" name='userName' type="text" placeholder='Username' autocomplete='off'/>
    <input id="password" name='password' type="password" placeholder='Password' autocomplete='off'/>
    <div class="elem-group">
      <img src="captcha.php" alt="CAPTCHA" class="captcha-image"><i class="fas fa-redo refresh-captcha"></i>
      <br>
      <input type="text" id="captcha" placeholder="Enter the above CAPTCHA text" name="captcha_challenge">
    </div>
    <button type="submit" id="submit">Login</button>
  </form>
  <div>
    <a href='createAccount.php'>Create Account</a>
    <a href='forgotPassword.php'>Forgot Password</a>
  </div>
 </body>
</html>

<script>

$( document ).ready(function() {
    $('#how').attr('class', 'visible');
    $('#1').toggleClass('hidden visible');
    $('#2').toggleClass('hidden visible2');
    $('#3').toggleClass('hidden visible3');
});

function delay(time) {
  return new Promise(resolve => setTimeout(resolve, time));
}

function popup(){
  $('#popup').show();
  if(event.target.id == "1"){
    $('#content').html("Create an account with your MSU email and attend some of the listed events.  Just click 'I am at the game' and allow location detection.  If you are at the event while it's going on, your tokens will be automatically redeemed.");
  }
  else if (event.target.id == "2"){
    $('#content').html("Use the tokens you earn at these events to redeem promos at restaurants");
  }
  else{
    $('#content').html("Go to the restaurant and show your promo to redeem your reward");
  }
}

$(document).ready(function() {
    // This WILL work because we are listening on the 'document',
    // for a click on an element with an ID of #test-element
    var vis = false;
    $(document).on("click",function() {
      console.log("hello");
      if($('#popup').is(":visible") && vis==true){
        $('#popup').hide();
        vis = false;
      }
      else{
        vis=true;
      }

    });
});


function displayDiv(){
  $('#content').attr('class', 'visible');
  console.log("Hello");
}


</script>
