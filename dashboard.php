<?php
//include("/src/qrcode/qrlib.php");
//TODO: Thought: require school email and verification for each account to prevent
//misuse of promos (can't go to a game and redeem tokens on 10 accounts and get hella
//free food)
//TODO: Make each navbar element pop up a bit on hover
//TODO: Make logout button look nice
//TODO: Separate section for Events in Progress
//TODO: change logo pic from opposing school to the sport
//Database
//TODO: Create the backlog table (allows for stats of events)
//MVP TODOS
//Backend
//TODO: Make it so that all data is loaded one time on document load from backend,
//then just display it accordingly and update the scrollbox HTML
//TODO: execute check for date and time and give user tokens
//TODO: Once event expires, remove from database
//TODO: Keep a backlog of redeemed events for a user, on redemption check that the event isn't in
//the redeemed backlog
//TODO: Change the "I am at the game button" to have an id of the event_ID in database
//TODO: Check and sanitize the ID when sent to the backend so it only contains the digits and
//no other sql commands
//FrontEnd
//TODO: generate HTML for each of the events using information from the Events
//and location database queries
//TODO: do the same for linking userPromos
//TODO: create mechanism for a user to redeem a promo at a restaurant

//TODO: Have tokens counter update instantly when amount is changed
include("src/phpqrcode/qrlib.php");
include("src/functions.php");
//QRcode::png('PHP QR Code :)');

session_start();
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
</head>
 <body>
   <header>
     <h1>Maverick Rewards</h1>
     <button id="logout"><a href="logout.php">Logout</a></button>
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

    <div id='redeemForm' class='formAttributes hideform'>
      <div>This is a test div</div>
   </div>
    <!--
    <div id='redeemPromo' class='formAttributes hideform'>
      <button id='closeRedeemPromo' style='float: right; color: red;'>X</button>
      <div>Are you sure you want to purchase this promo?</div>
       <div>Restaurant name</div>
       <div>Promo Name</div>
       <div>QR Code</div>
       <input style='width:80%;'></input>
       <button id='redeemPromo' style='float: right; color: green;'>Validate Promo</button>
   </div>-->
     <div>Events coming up</div>

     <label for="sort">Sort Events By:</label>
      <select name="sort" id="sort">
       <option value="sport">Sport</option>
       <option value="date">Date</option>
       <option value="amount">Reward Amount</option>
      </select>
     <div id="scrollbox">
     </div>
   </main>
   <footer>
     <p>Created by Kelby Kramer</p>
  </footer>
 </body>
</html>

<script>

var puchasePromos = "";
var myPromos = "";
var leaderboard = "";
$( document ).ready(function() {
    displayHome();
});

$('#close').on('click', function () {
    $('.formAttributes').hide();
})

var purchasePromos = "";
var myPromos = "";

function displayHome(){
  $.ajax({
  url: "src/ajax.php",
  type: "POST",
  data: {
    func: "displayEvents"
  },
  success: function(data){
    console.log(data);
    $('#scrollbox').html(
        data
    );
  }
});
}

function displayPurchasePromos(){

  $.ajax({
  url: "src/ajax.php",
  type: "POST",
  data: {
    func: "displayPurchasePromos"
  },
  success: function(data){
    console.log(data);
    $('#scrollbox').html(
        data
    );
  }
});
}

function displayMyPromos(){
  $.ajax({
  url: "src/ajax.php",
  type: "POST",
  data: {
    func: "displayMyPromos"
  },
  success: function(data){
    console.log(data);
    $('#scrollbox').html(
        data
    );
  }
});
}

function redeemPromo(id){
  console.log("Promo is ready to be redeemed" + id);
  var code = $('#redeem_code').val();

  console.log(code);
  $.ajax({
  url: "src/ajax.php",
  type: "POST",
  data: {
    func: "redeemPromo",
    promo_id: id,
    redeem_code: code
  },
  success: function(data){
    console.log(data);
    $('#redeemForm').html(data);
  }
});
}

function displayRedeemPromo(id){
  console.log("Have popup come up");
  $('#redeemForm').html($('#redeemPromo' + id).html());
  $('#redeemForm').show();
  //$('#'+ id).show();
}

function closeRedeemPromo(){
  $('#redeemForm').hide();
}

function displayLeaderboard(){
  $('#scrollbox').html(
      $('<div>').prop({
          id: 'innerdiv',
          innerHTML: 'This is the global token Leaderboard',
          className: 'border pad'
      })
  );
}

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

function purchasePromo(button_id){
  $('#confirmationForm').show();
  $('#confirmPurchase').on('click', function () {
    $.ajax({
    url: "src/ajax.php",
    type: "POST",
    data: {
      func: "purchasePromo",
      promo_id: button_id,
    },
    success: function(data){
      console.log(data);

      $('#confirmationForm').html(data);
    }
  });
  })
}

function ajax(event_id){
  getGeoLocation();
  console.log(event_id);
  var coords = "";
  var checkExist = setInterval(function() {
  if ($('#coords').length) {
      console.log("Exists!");
      coords = $('#coords').text();
      clearInterval(checkExist);

      $.ajax({
      url: "src/ajax.php",
      type: "POST",
      data: {
        coordinates: coords,
        func: "verifyDateAndLoc",
        eventID: event_id
      },
      success: function(data){
        if(data == "There was an error"){
          alert("You can't redeem this game");
        }

        if(data == "Great success!"){
          alert("Your tokens have been redeemed");
        }
        console.log(data);
      }
      });
   }
   else{
     console.log("Not accepted yet");
   }
 }, 100); // check every 100ms



}

function getGeoLocation(){
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(showPosition);
  } else {
    console.log("Geolocation is not supported by this browser.");
  }
}
function showPosition(position) {
  var coords = "<p id='coords'>" + position.coords.latitude + " " + position.coords.longitude + "</p>";
  $("body").append(coords);

}

</script>
