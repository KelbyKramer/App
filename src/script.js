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
  /*
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
});*/
}

function displayRedeemPromo(id){
  console.log("Have popup come up");
  $('#redeemForm').html($('#redeemPromo' + id).html());
  $('#redeemForm').show();
}

function closeRedeemPromo(){
  $('#redeemForm').hide();
}

function displayLeaderboard(){
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
      console.log(data);

      $('#confirmationForm').html(data);
    }
  });
  })
}
