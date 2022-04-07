function displayHome(){
  $.ajax({
  url: "src/ajax.php",
  type: "POST",
  data: {
    func: "displayEvents"
  },
  success: function(data){
    //console.log(data);
    $('#scrollbox').html(
        data
    );
    colorEvents();
  }
});
}

function colorEvents(){
  $.ajax({
  url: "src/ajax.php",
  type: "POST",
  data: {
    func: "colorEvents"
  },
  success: function(data){
    var parse = JSON.parse(data);
    console.log(parse);

    for(var key in parse){
      var x = document.getElementById("event " + key);
      //x.style.backgroundColor = "pink";
      var y = x.childNodes;
      var div = document.createElement('div');
      div.id = "overlay" + key;

      div.className = 'overlay';
      var button = document.getElementById(key);
      //
      y[0].appendChild(div);
      if(parse[key] == "checked in"){
        x.style.backgroundColor = "#d4ac0d ";
        div.innerHTML = "Checked In";
        button.innerHTML = "Redeem Tokens for this Event";
      }
      if(parse[key] == "redeemed"){
        x.style.backgroundColor = "#922b21 ";
        var oldDiv = document.getElementById("overlay" + key);
        oldDiv.style.display = "none";
        div.innerHTML = "Redeemed";
        button.innerHTML = "You've already redeemed tokens for this event";
        button.disabled = 'disabled';
      }
    }
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

function displayRedeemPromo(id){
  console.log("Have popup come up");
  $('#redeemForm').html($('#redeemPromo' + id).html());
  $('#redeemForm').show();
}

function closeRedeemPromo(){
  $('#redeemForm').hide();
}

function closeEvent(){
  $('#eventMessage').hide();
}

function displayLeaderboard(){

  $.ajax({
  url: "src/ajax.php",
  type: "POST",
  data: {
    func: "displayLeaderboard"
  },
  success: function(data){
    console.log(data);
    $('#scrollbox').html(
        data
    );
  }
});

  $('#scrollbox').html(
      $('<div>').prop({
          id: 'innerdiv',
          innerHTML: 'This feature has not been implemented yet',
          className: 'border pad'
      })
  );
}

function displayAchievements(){
  $('#scrollbox').html(
      $('<div>').prop({
          id: 'innerdiv',
          innerHTML: 'This feature has not been implemented yet',
          className: 'border pad'
      })
  );
}


function ajax(event_id){

  getGeoLocation();
  console.log(event_id);
  var coords = "";

  var checkExist = setInterval(function() {

  if ($('#coords').length) {
      //$('#hello').html("There is content here");
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

        if(data == "Great success!"){
          displayEventMessage("Success!  Your tokens have been redeemed");
        }
        else{
            displayEventMessage(data);
        }
        colorEvents();
        //update the token and session token values

        //TODO: get this done
      }
      });
      updateTokenCountsEvent();
   }
   else{
     console.log("Not accepted yet");
   }
 }, 100); // check every 100ms
}

function updateTokenCountsRedeem(){
  $.ajax({
  url: "src/ajax.php",
  type: "POST",
  data: {
    func: "updateTokenCountsRedeem"
  },
  success: function(data){
    console.log(data);
    $('#tokens').html("Tokens: " + data + " ");
  }
  });
}

function updateTokenCountsEvent(){
  $.ajax({
  url: "src/ajax.php",
  type: "POST",
  data: {
    func: "updateTokenCountsEvent"
  },
  success: function(data){
    console.log(data);
    var parsedValues = JSON.parse(data);
    console.log(parsedValues);
    $('#tokens').html("Tokens: " + parsedValues[0]);
    $('#totalTokens').html("Lifetime Tokens: " + parsedValues[1]);
  }
  });

}

function getGeoLocation(){
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(showPosition);
/*
    navigator.geolocation.watchPosition(function(position) {
      console.log("i'm tracking you!");
    },
    function(error) {
      if (error.code == error.PERMISSION_DENIED){
        console.log("you denied me :-(");
        //TODO: display the deny location error to user
        navigator.geolocation.getCurrentPosition(showPosition);
      }

    });*/
  } else {
    console.log("Geolocation is not supported by this browser.");
  }
}

function showPosition(position) {

  var coords = "<p id='coords'>" + position.coords.latitude + " " + position.coords.longitude + "</p>";
  $("body").append(coords);
  //queryLocPerms();
}

function displayEventMessage(message){
  $('#eventMessage').html(message);
  $('#eventMessage').show();
}

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
      //console.log(data);

      $('#confirmationForm').html(data);
    }
  });
    updateTokenCountsRedeem();
  })
}

function queryLocPerms(){
  navigator.permissions.query({
     name: 'geolocation'
  }).then(function(result) {
     if (result.state == 'granted') {
         //alert(result.state);
         console.log("granted");
         $('#locationStatus').html("Granted");
         //geoBtn.style.display = 'none';
     } else if (result.state == 'prompt') {
         //alert(result.state);
         //geoBtn.style.display = 'none';
         console.log("prompt");
         $('#locationStatus').html("Prompt");
         //navigator.geolocation.getCurrentPosition(revealPosition, positionDenied, geoSettings);
     } else if (result.state == 'denied') {
         //alert(result.state);
         //geoBtn.style.display = 'inline';
         console.log("denied");
         $('#locationStatus').html("Denied");
     }
     result.onchange = function() {
         //alert(result.state);
         $('#locationStatus').html("Changed");
     }
  });
}
