<?php
//comment
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
<title>KatoRewards</title>
<link rel="icon" href="src/KR.png">
</head>
 <body>
   <header>
     <h1><span>Kato Rewards </span><span class='learnMore' id='learnMore'>?</span></h1>
     <div id='info' class='hide'>Here is the info for how to use KatoRewards</div>
     <button id="logout" style='background-image: linear-gradient(315deg, #2a2a72 0%, #009ffd 74%);' ><a style='text-decoration:none; color:black;' href="logout.php">Logout</a></button>
     <div><span id='tokens'>Tokens: <?php echo $_SESSION['tokens']; ?></span><span id='totalTokens'> Lifetime Tokens: <?php echo $_SESSION['totalTokens'];?></span></div>
     <div><span id='tokens'>User: <?php echo $_SESSION['userName']; ?></span></div>
   </header>
   <nav>
     <ul>
       <div id="nav">
       <li><a onclick="displayHome()">Home</a></li>
       <li><a onclick='displayPurchasePromos()'>Purchase Promotions</a></li>
       <li><a onclick="displayMyPromos()">My Promotions</a></li>
       <li><a onclick="displayLeaderboard()">Leaderboard</a></li>
       <li><a onclick="displayAchievements()">My Achievements</a></li>
     </div>
    </ul>
   </nav>


   <main>
     <div id='confirmationForm' class='formAttributes hideform'>
    	<div>Are you sure you want to purchase this promo?</div>

        <button id='confirmPurchase' style='float: right; color: green;'>Yes</button>
        <button id='close' style='float: right; color: green;'>No</button>
    </div>

    <div id='eventMessage' class='formAttributes hideform'></div>

    <div id='redeemForm' class='formAttributes hideform'></div>
   <!--
     <select class="div-toggle" data-target=".target">
       <option value="orange" data-show=".Athletic">Athletic Events</option>
       <option value="lemon" data-show=".nonAthletic">Non Athletic Events</option>
     </select>-->

     <div class='target' id="scrollbox">
     </div>
   </main>
 </body>
</html>

<script>

$('#learnMore').on('click', function () {
    $('#info').toggle();
})
$( document ).ready(function() {
  $('#nav').slideDown(1000);
  $('#scrollbox').slideDown(700);
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

$(document).on('change', '.div-toggle', function() {
  var target = $(this).data('target');
  var show = $("option:selected", this).data('show');
  $(target).children().addClass('hide');
  $(show).removeClass('hide');
});
$(document).ready(function(){
    $('.div-toggle').trigger('change');
});


</script>
