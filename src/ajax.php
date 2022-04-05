<?php
include("functions.php");

if($_SERVER['REQUEST_METHOD'] === 'POST'){
  if($_POST['func'] == "verifyDateAndLoc"){

    date_default_timezone_set('America/Chicago'); // CDT

    $current_date = date('Y-m-d');
    $current_time = date('H:i:s');
    $error = False;
    //backlog check
    session_start();
    $where = array("User_ID" => $_SESSION['id'], "event_id" => $_POST['eventID']);
    $result = query("eventbacklog", "*", $where);

    //Initial check for redeemTime2 and 1 and error throwing
    echo "<button id='closeEvent' onclick='closeEvent()' style='float: right; color: red;'>X</button>";
    if($result->num_rows > 0){
      while($row = $result->fetch_assoc()) {
        //var_dump($row);
        $redeemTime2 = $row['redeemTime2'];
        $redeemTime1 = $row['redeemTime1'];
      }

      if($redeemTime2 != NULL){
        //echo "This promo already exists in the backlog with RT1 and RT2";
        echo "This event has already been redeemed";
      }
      else if($redeemTime1 != NULL){
        //redeemTime2 is null, but redeemTime1 is not, so token redemption
        //process will be checked
        $date2 = date('Y-m-d H:i:s');
        //$date2 = date('2022-03-26 15:44:15');
        $date1 = strtotime($redeemTime1);
        $date2 = strtotime($date2);
        //check to see if there is more than 15 minute difference between two dates
        $dateDifference = dateDiff($date1, $date2);

        if($dateDifference > 900){
          //user has been at game for 15 minutes and can redeem tokens
          //echo "The date difference is legit";

          $result = innerJoinQuery("events", "locations", "events.*", "locations.latLong", "events.Loc_ID=locations.Loc_ID", "events.Event_ID=".$_POST['eventID']);

          if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
              //var_dump($row);
              $start = $row['start'];
              $finish = $row['finish'];
              $date = $row['date'];
              $reward = $row['tokens'];
              $coords = $row['latLong'];
            }
          }

          $event_coords = explode(",", $coords);
          $user_coordinates = explode(" ", $_POST['coordinates']);
          $Loc_error = coordinateCheck($event_coords, $user_coordinates);

          $time_Error = False;
          $date_Error = False;
          $time_Error = timeCheck($start, $finish, $current_time);
          $date_Error = dateCheck($date, $current_date);

          if ($Loc_error || $time_Error || $date_Error){
            echo "<div style='border:1px solid black;'>There was an error</div>";
            if($Loc_error){
              echo "<div style='border:1px solid black;'>You are not in the location radius".$coords." ".$_POST['coordinates']."</div>";
            }

            if($time_Error){
              echo "<div style='border:1px solid black;'>You are not within the specified time for this event</div>";
            }

            if($date_Error){
              echo "<div style='border:1px solid black;'>You are not within the specified date for this event</div>";
            }
          }
          else{
            echo "Tokens are being redeemed ";
            //not an error, do token redemption
            //not an error, insert into event backlog with redeemTime1
            $tokens = $_SESSION['tokens'];
            $newTokens = $tokens + $reward;
            $newTotalTokens = $tokens + $reward;
            $conn = dbConnect();
            $result = updateQuery("users", "current_tokens=".$newTokens.", total_tokens=".$newTotalTokens, "User_ID=".$_SESSION['id']);

            $_SESSION['tokens'] = $newTokens;
            $_SESSION['totalTokens'] = $newTotalTokens;

            $user_id = $_SESSION['id'];
            $event_id = $_POST['eventID'];
            $redeemTime2 = date('Y-m-d H:i:s');
            //$sql = "INSERT INTO eventbacklog (User_ID, event_ID, redeemTime1) VALUES (?, ?, ?)";
            updateQuery("eventbacklog", "redeemTime2='".$redeemTime2."'", " event_ID=".$event_id." AND User_ID=".$user_id);
            echo "<div style='border:1px solid black;'>You have checked into this event.  Redeem again 15 minutes later to receive tokens.</div>";
          }
          //token insertion process
        }
        else{
          $userDiff = 900 - $dateDifference;
          $minutes = intval($userDiff / 60);
          $seconds = $userDiff - ($minutes * 60);
          echo "<div style='border:1px solid black;'>You have to be at an event for at least 15 minutes to redeem</div>";
          echo "<div style='border:1px solid black;'>Wait another ".$minutes." minutes and ".$seconds." seconds!</div>";
        }
      }
      else{
        //both redeemTime1 and 2 were NULL, so the event needs to be inserted into
        //backlog with redeemTime1 being the current Time
      }
    }
    else{
      //event is not in backlog

      $result = innerJoinQuery("events", "locations", "events.*", "locations.latLong", "events.Loc_ID=locations.Loc_ID", "events.Event_ID=".$_POST['eventID']);

      if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          //var_dump($row);
          $start = $row['start'];
          $finish = $row['finish'];
          $date = $row['date'];
          $reward = $row['tokens'];
          $coords = $row['latLong'];
        }
      }

      $event_coords = explode(",", $coords);
      $user_coordinates = explode(" ", $_POST['coordinates']);
      $Loc_error = coordinateCheck($event_coords, $user_coordinates);

      $time_Error = False;
      $date_Error = False;
      $time_Error = timeCheck($start, $finish, $current_time);
      $date_Error = dateCheck($date, $current_date);

      if ($Loc_error || $time_Error || $date_Error){
        echo "<div style='border:1px solid black;'>There was an error</div>";
        if($Loc_error){
          echo "<div style='border:1px solid black;'>You are not in the location radius".$coords." ".$_POST['coordinates']."</div>";
        }

        if($time_Error){
          echo "<div style='border:1px solid black;'>You are not within the specified time for this event</div>";
        }

        if($date_Error){
          echo "<div style='border:1px solid black;'>You are not within the specified date for this event</div>";
        }
      }
      else{
        //not an error, do token redemption
        //not an error, insert into event backlog with redeemTime1
        $user_id = $_SESSION['id'];
        $event_id = $_POST['eventID'];
        $redeemTime1 = date('Y-m-d H:i:s');
        $sql = "INSERT INTO eventbacklog (User_ID, event_ID, redeemTime1) VALUES (?, ?, ?)";
        insertQuery($sql, $user_id, $event_id, $redeemTime1);
        echo $event_id;
        echo "<div style='border:1px solid black;'>You have checked into this event.  Redeem again 15 minutes later to receive tokens.</div>";

        //TODO: Do an AJAX request here changing color of div
      }
    }
  }
  else if ($_POST['func'] == "updateTokenCountsEvent"){
    session_start();
    $currentTokens = $_SESSION['tokens'];
    $totalTokens = $_SESSION['total_tokens'];

    echo json_encode($currentTokens);
    echo json_encode($totalTokens);
  }
  else if ($_POST['func'] == "updateTokenCountsRedeem"){
    session_start();
    $currentTokens = $_SESSION['tokens'];
    echo json_encode($currentTokens);

  }
  else if($_POST['func'] == "purchasePromo"){
    $promo_id = $_POST['promo_id'];
    session_start();
    $id = $_SESSION['id'];
    $tokens = $_SESSION['tokens'];

    $conn = dbConnect();
    $sql = "SELECT cost FROM promos WHERE promo_ID=".$promo_id;
    $sanitized_sql = mysqli_real_escape_string($conn, $sql);
    $result = mysqli_query($conn, $sql);
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $cost = $row['cost'];
      }
    }
    else{
      echo "<center>This Promo does not exist</center>";
      //TODO: Figure out a better way to handle this

      //$cost = 3000000;
    }

    $sql = "SELECT * FROM userpromos WHERE User_ID=".$id." AND Promo_ID=".$promo_id;
    $sanitized_sql = mysqli_real_escape_string($conn, $sql);
    $result = mysqli_query($conn, $sql);
    $error = False;
    if ($result->num_rows > 0) {
      $html = "You already have this promo, use it first before you purchase it again!";
      $error = True;
    }

    if($tokens < $cost){
      $html = "You don't have enough tokens to purchase this!";
      $error = True;
    }

    if($error == False){
      //purchase the promo
      $_SESSION['tokens'] -= $cost;
      $sql = "INSERT INTO userpromos (User_ID, Promo_ID) VALUES ($id, $promo_id)";
      $sanitized_sql = mysqli_real_escape_string($conn, $sql);
      $result = mysqli_query($conn, $sql);
      $sql = "UPDATE users SET current_tokens=".$_SESSION['tokens']." WHERE User_ID=".$id;
      $sanitized_sql = mysqli_real_escape_string($conn, $sql);
      $result = mysqli_query($conn, $sql);
      $html = "Promo has been purchased!";

    }
    echo $html;
  }
  else if($_POST['func'] == "displayPurchasePromos"){
    $result = innerJoinQuery("promos", "promolinks", "promos.promo_ID, promos.cost, promos.Title, promolinks.business_name", "", "promos.business_id=promolinks.business_id");

    $arr = generateEventArray($result);
    $html = generatePromoHTML($arr);
    echo $html;
  }
  else if($_POST['func'] == "displayEvents"){

    $conn = dbConnect();
    $result = innerJoinQuery("events", "locations", "events.Event_ID, events.type, events.tokens, events.start, events.finish, events.date, locations.name, locations.address, locations.latLong", "", "events.Loc_ID=locations.Loc_ID AND events.expireDateTime >= CURDATE()", "", "events.date, events.start");
    //echo $result;
    $arr = generateEventArray($result);
    $html =  generateHTML($arr);
    echo $html;
  }
  else if($_POST['func'] == "displayMyPromos"){

    $conn = dbConnect();
    session_start();
    $where = array("User_ID" => $_SESSION['id']);
    $result2 = query("userpromos", "Promo_ID", $where);

    $ret = array();

    if ($result2->num_rows > 0) {
      while($row = $result2->fetch_assoc()) {
        array_push($ret, $row['Promo_ID']);
      }
    }

    $sql = "SELECT promos.Promo_ID, promos.Title, promos.business_id, promolinks.business_name FROM promos INNER JOIN promolinks ON promos.business_id=promolinks.business_id WHERE";
    $final = queryUserPromos($sql, $ret);

    $ret = array();
    if ($final) {
      while($row = $final->fetch_assoc()) {
        array_push($ret, $row);
      }
      $html3= generateRedeemPromoHTML($ret);
      echo $html3;
    }
    else{
      echo "You haven't purchased any promos";
    }
  }
  else if($_POST['func'] == "colorEvents"){
    session_start();
    if(is_int($_SESSION['id'])){
      $arr = array();
      $result = query("eventbacklog", "*", array("User_ID" => $_SESSION['id']));
      if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          $time1 = $row['redeemTime1'];
          $time2 = $row['redeemTime2'];
          $event_id = $row['event_ID'];

          if($time2 == NULL){
            //checked in
            $arr[$event_id] = "checked in";
          }
          else{
            //event has been redeemed
            $arr[$event_id] = "redeemed";
          }
        }
      }

      //TODO: test if no rows are seen
      echo json_encode($arr);
    }
    else{
      echo "Error";
    }
  }
}
else{
  header("Location: ../restrict.php");
}

 ?>
