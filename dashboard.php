<?php
include("src/functions.php");
include("header.php");


session_start();

if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
    $location = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $location);
    exit;
}

$id = $_GET['id'];
if($id != $_SESSION['id']){
  header("Location:restrict.php");
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
 <body>
   <header>
     <h1>Maverick Rewards</h1>
     <button id="logout" style='background-image: linear-gradient(315deg, #2a2a72 0%, #009ffd 74%);' ><a style='text-decoration:none; color:black;' href="logout.php">Logout</a></button>
     <div>Tokens: <?php echo $_SESSION['tokens']; ?></div>
   </header>
   <nav>
     <ul>
       <div id="nav">
       <li><a onclick="displayHome()">Home</a></li>
       <li><a onclick='displayPurchasePromos()'>Purchase Promotions</a></li>
       <li><a onclick="displayMyPromos()">My Promotions</a></li>
       <li><a onclick="displayLeaderboard()">Leaderboard</a></li>
     </div>
    </ul>
   </nav>

   <main>
     <div id='confirmationForm' class='formAttributes hideform'>
    	<div>Are you sure you want to purchase this promo?</div>

        <button id='confirmPurchase' style='float: right; color: green;'>Yes</button>
        <button id='close' style='float: right; color: green;'>No</button>
    </div>

    <div id='eventMessage' class='formAttributes hideform'>
      Here is a test of data
    </div>

    <div id='redeemForm' class='formAttributes hideform'>
      <div>This is a test div</div>
   </div>
     <div>Events coming up</div>
<!--
     <label for="sort">Sort Events By:</label>
      <select name="sort" id="sort">
       <option value="sport">Sport</option>
       <option value="date">Date</option>
       <option value="amount">Reward Amount</option>
     </select>-->
     <div id="scrollbox">
     </div>
   </main>
   <footer>
     <p>Created by Kelby Kramer</p>
  </footer>
 </body>
</html>

<script>

$( document ).ready(function() {
    displayHome();
});

$('#close').on('click', function () {
    $('.formAttributes').hide();
})

var purchasePromos = "";
var myPromos = "";

//remove confirmation Form when outside the window is clicked
//TODO: refactor this into a singular function
document.addEventListener('mouseup', function(e) {
    var container = document.getElementById('confirmationForm');
    var close = document.getElementById('close');
    if (!container.contains(e.target) || close.contains(e.target)) {
        container.style.display = 'none';
        var q = $("<div>Are you sure you want to purchase this promo?</div>");
        var r= $("<button id='confirmPurchase' style='float: right; color: green;'>Yes</button>");
        var s= $("<button id='close' style='float: right; color: green;'>No</button>");
        $('#confirmationForm').html("");
        $('#confirmationForm').append(q);
        $('#confirmationForm').append(r);
        $('#confirmationForm').append(s);
    }
});

document.addEventListener('mouseup', function(e) {
    var container = document.getElementById('redeemForm');
    //var container = document.querySelector([id^="redeemPromo"]).id;
    var close = document.getElementById('closeRedeemPromo');
    if (!container.contains(e.target) || close.contains(e.target)) {
        container.style.display = 'none';
    }
});

document.addEventListener('mouseup', function(e) {
    var container = document.getElementById('eventMessage');
    //var container = document.querySelector([id^="redeemPromo"]).id;
    //var close = document.getElementById('closeRedeemPromo');
    if (!container.contains(e.target)) {
        container.style.display = 'none';
    }
});


</script>
