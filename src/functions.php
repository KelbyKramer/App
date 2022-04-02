<?php
include("dbDefine.php");
include("config.php");
//TODO: Test this function
function query($table, $columns, $where, $endText = "") {
  $db = dbConnect();
  $sql = "";
  foreach($where as $key => $value) {
    if($sql != "") {
      $sql .= " AND ";
    }
    else {
      $sql = " where ";
    }
    $clean_v = $value;
    if (is_string($value)) {
      $clean_v = "'".mysqli_real_escape_string($db, $value)."'";
    }
    $sql .= "$key=$clean_v";
  }
  $sql = "select $columns from $table $sql $endText";
  $result = mysqli_query($db, $sql);
  //closeDB($db);
  return $result;
}
//TODO: Test this function
function innerJoinQuery($table1, $table2, $fields1, $fields2, $on, $where = "", $order_by=""){
  $db = dbConnect();
  $ret = "SELECT ";
  $ret .= $fields1;
  if($fields2 != ""){
    $ret .= ", ".$fields2;
  }

  $ret .= " FROM ";
  $ret .= $table1." INNER JOIN ".$table2." ON ";
  $ret.= $on;
  if ($where != ""){
    $ret .= " WHERE ".$where;
  }

  if($order_by != ""){
    $ret .= " ORDER BY ".$order_by;
  }
  //return $ret;
  $result = mysqli_query($db, $ret);
  //closeDB($db);
  return $result;
}
//TODO: Test this function
//TODO: make this function agnostic to number of arguments passed in

function insertQuery($statement, $value1, $value2, $value3){
  $conn = dbConnect();
  $stmt = $conn->prepare($statement);
  $stmt->bind_param("sss", $value1 , $value2, $value3);
  $stmt->execute();
}
//TODO: Test this function
function updateQuery($table, $set, $where=""){
  $db = dbConnect();
  $ret = "UPDATE ";
  $ret .= $table;
  $ret .= " SET ".$set;
  if($where != ""){
    $ret .= " WHERE ".$where;
  }
  $ret .= "";
  //echo $ret;
  $result = mysqli_query($db, $ret);
  //closeDB($db);
  return $result;

}
//TODO: Test this function
function deleteQuery($table, $where){
  $db = dbConnect();
  $ret = "DELETE FROM ";
  $ret .= $table." ";
  if ($where != ""){
    $ret .= "WHERE ".$where;
  }
  $ret .= "";
  $result = mysqli_query($db, $ret);

  return $result;
}

function dateCheck($date, $current_date){
  if ($date != $current_date){
    return True;
  }
  return False;
}

function timeCheck($start, $finish, $current_time){
  if($start > $current_time || $finish < $current_time){
    return True;
  }
  return False;
}

function generateRandomString($length = 12) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function sendEmail($address, $subject, $msg, $header){
  mail($address, $subject, $msg, $header);
}
function generateRegisterEmail($address, $str){
  $msg = "Welcome to the app!  Please click the link to verify your account ";
  $msg .= VERIFY_ACCOUNT_ADDRESS;
  $msg .= "?str=".$str;

  $msg = wordwrap($msg,70);

  mail($address,"App Account Verification",$msg, 'From: kramerkelby@gmail.com');
}

function dateDiff($date1, $date2){
  // Formulate the Difference between two dates
  $diff = abs($date2 - $date1);

  // To get the year divide the resultant date into
  // total seconds in a year (365*60*60*24)
  $years = floor($diff / (365*60*60*24));

  // To get the month, subtract it with years and
  // divide the resultant date into
  // total seconds in a month (30*60*60*24)
  $months = floor(($diff - $years * 365*60*60*24)
                                 / (30*60*60*24));

  // To get the day, subtract it with years and
  // months and divide the resultant date into
  // total seconds in a days (60*60*24)
  $days = floor(($diff - $years * 365*60*60*24 -
               $months*30*60*60*24)/ (60*60*24));

  // To get the hour, subtract it with years,
  // months & seconds and divide the resultant
  // date into total seconds in a hours (60*60)
  $hours = floor(($diff - $years * 365*60*60*24
         - $months*30*60*60*24 - $days*60*60*24)
                                     / (60*60));

  // To get the minutes, subtract it with years,
  // months, seconds and hours and divide the
  // resultant date into total seconds i.e. 60
  $minutes = floor(($diff - $years * 365*60*60*24
           - $months*30*60*60*24 - $days*60*60*24
                            - $hours*60*60)/ 60);

  // To get the minutes, subtract it with years,
  // months, seconds, hours and minutes
  $seconds = floor(($diff - $years * 365*60*60*24
           - $months*30*60*60*24 - $days*60*60*24
                  - $hours*60*60 - $minutes*60));

  if($years > 0 || $months > 0){
    return 0;
  }
  else{
    $ret = $days * 86400+ $hours * 3600+ $minutes * 60+ $seconds;
    return $ret;
  }
}

//TODO: Test this function
function coordinateCheck($event_coords, $user_coordinates){
  $topLeftLat = floatval($event_coords[0]);
  $topLeftLong = floatval($event_coords[1]);
  $botRightLat = floatval($event_coords[2]);
  $botRightLong = floatval($event_coords[3]);

  $user_latitude = floatval($user_coordinates[0]);
  $user_longitude = floatval($user_coordinates[1]);

  $Loc_error = False;
  if($topLeftLat < $user_latitude){
    $Loc_error = True;
  }

  if($botRightLat > $user_latitude){
    $Loc_error = True;
  }

  if($topLeftLong > $user_longitude){
    $Loc_error = True;
  }

  if($botRightLong < $user_longitude){
    $Loc_error = True;
  }

  return $Loc_error;
}
//TODO: Test this function
function queryUserPromos($sql, $where, $endText = "") {
  $db = dbConnect();
  $sql .= '(';
  for($i = 0; $i < count($where); $i++){
    if($i != 0){
      $sql .= " OR ";
    }

    $clean_v = $where[$i];
    if (is_string($where[$i])) {
      $clean_v = mysqli_real_escape_string($db, $where[$i]);
    }
    $sql .= "Promo_ID=$clean_v";
  }
  $sql = "$sql ) $endText";
  //return $sql;
  $result = mysqli_query($db, $sql);
  //closeDB($db);
  return $result;
}

//TODO: Test this function
function login($username, $password){
  $conn = dbConnect();
  $sanitized_username = mysqli_real_escape_string($conn, $username);
  $sanitized_password = mysqli_real_escape_string($conn, $password);
  $hashed_password = password_hash($sanitized_password, PASSWORD_DEFAULT);
  //$sql = "SELECT User_ID, Password, current_tokens, verify, total_tokens FROM users WHERE Username='$sanitized_username'";

  $stmt = $conn->prepare('SELECT User_ID, Password, current_tokens, verify, total_tokens FROM users WHERE Username= ?');
  $stmt->bind_param('s', $sanitized_username); // 's' specifies the variable type => 'string'

  $stmt->execute();

  $result = $stmt->get_result();
  //$result = mysqli_query($conn, $sql);
  if ($result->num_rows > 0) {
    //login credentials were successful

    while($row = $result->fetch_assoc()) {
      $hashFromDatabase = $row['Password'];
      $id = $row['User_ID'];
      $tokens = $row['current_tokens'];
      $verify = $row['verify'];
      $totalTokens = $row['total_tokens'];
    }
    $error = False;
    if($verify == 0){
      echo "<div class='error'>Account has not yet been verified.  Click the link sent to your email</div>";
      $error = True;
    }

    if(!password_verify($password, $hashFromDatabase)){
      echo "<div class='error'>Password is invalid</div>";
      $error = True;
    }

    if($error == False){
      session_start();
      $_SESSION['id'] = $id;
      $_SESSION['tokens'] = $tokens;
      $_SESSION['total_tokens'] = $totalTokens;
      //echo $_SESSION['id'];
      header("location: dashboard.php?id=".$id);
    }
  }
  else{
    echo "<center><div class='error'>Credentials were not successful</div></center>";
  }
}
//TODO: Test this function
function convertDate($date){
  $newDate = date('F j, Y',strtotime($date));
  return $newDate;
}

function convertTime($time){
  $newTime = date("g:i a", strtotime($time));
  return $newTime;
}
//TODO: Test this function
function generatePromoHTML($arr){
  $ret = "";
  /*$ret .= "<div id='confirmationForm' class='formAttributes hideform'>";
  $ret .= "<div>Are you sure you want to purchase this promo for 100 tokens?</div>";
  $ret .= "<button id='close' style='float: right; color: green;'>Yes</button>";
  $ret .= "</div>";*/
  foreach($arr as $promo){

    $ret .= "<div id='".$promo['promo_ID']."'>";
    $ret .= "<div id='test'>";
    $ret .= "<span>";
    $ret .= '<img src="src/'.$promo['business_name'].'.png"></img>';
    $ret .= "<div>".$promo['business_name']."</div>";
    $ret .= "</span>";
    $ret .= "<span>";
    //TODO: Add column to db that is different than the Title
    //TODO: Remake Title so that it is uniquue for every promo
    $ret .= "<div>".$promo['Title']."</div>";
    $ret .= "</span>";
    $ret .= "<span>";
    $ret .= "<div>Cost: ".$promo['cost']." Tokens</div>";
    $ret .= "<button class='eventButton' id='".$promo['promo_ID']."'onclick='purchasePromo(".$promo['promo_ID'].")'>Purchase this Promo</button>";
    $ret .= "</span>";
    $ret .= "</div>";
    $ret .= "</div>";
  }
  return $ret;
}
//TODO: Test this function
function generateQRCode($title, $business_id){
  return $title. $business_id;
}
//TODO: Test this function
function generateRedeemPromoHTML($arr){
  $ret = "";
  //TODO: Include redeem promo div in here
  foreach($arr as $promo){
    $ret .= "<div id='redeemPromo".$promo['Promo_ID']."' class='formAttributes hideform'>";
    $ret .= "<button id='closeRedeemPromo' onclick='closeRedeemPromo()' style='float: right; color: red;'>X</button>";
    $ret .= "<div>".$promo['business_name']."</div>";
    $ret .= "<div>".$promo['Title']."</div>";
    $ret .= "<div>Press this button to go to the redeem promo page</div>";
    $ret .= "<button id='redeem' style='float: left; font-size: 2 rem; background-color: #19f316;'><a style='text-decoration:none' href='redeemPromo.php?id=".$_SESSION['id']."&promo_id=".$promo['Promo_ID']."'>Validate Promo</a></button>";
    $ret .= "</div>";

    $ret .= "<div id='".$promo['Promo_ID']."'>";
    $ret .= "<div id='test'>";
    $ret .= "<span>";
    $ret .= '<img src="src/'.$promo['business_name'].'.png"></img>';
    $ret .= "<div>".$promo['business_name']."</div>";
    $ret .= "</span>";
    $ret .= "<span>";
    //TODO: Add column to db that is different than the Title
    //TODO: Remake Title so that it is uniquue for every promo
    $ret .= "<div>".$promo['Title']."</div>";
    $ret .= "</span>";
    $ret .= "<span>";
    $ret .= "<button class='eventButton' id='".$promo['Promo_ID']."'onclick='displayRedeemPromo(".$promo['Promo_ID'].")'>Redeem this Promo</button>";
    $ret .= "</span>";
    $ret .= "</div>";
    $ret .= "</div>";
  }
  return $ret;
}

function convertAddressToLink($address){
  $ret = str_replace(' ', '+', $address);
  return $ret;
}
//TODO: Test this function
function generateHTML($arr){
  $ret = "";
  foreach($arr as $event){
    $link = convertAddressToLink($event['address']);
    //$ret .= "<div id='event ".$event['Event_ID']."'>";
    $ret .= "<div id='event ".$event['Event_ID']."'>";
    $ret .= "<div id='test'>";
    $ret .= "<span>";
    $ret .= "<img src='src/MavLogo.png'></img>";
    $ret .= "<div>".$event['type']."</div>";
    $ret .= "</span>";
    $ret .= "<span>";
    $date = convertDate($event['date']);
    $time = convertTime($event['start']);
    $ret .= "<div class='eventField'>Date ".$date."</div>";
    $ret .= "<div class='eventField'>Time ".$time."</div>";
    $ret .= "<div class='eventField'>".$event['name']."</div>";
    $ret .= "<div class='eventField'><a style='color: white; text-decoration:underline;' href='http://maps.apple.com/?q=$link' target='_blank'>".$event['address']."</a></div>";
    $ret .= "</span>";
    $ret .= "<span>";
    $ret .= "<div>Reward: ".$event['tokens']." Tokens</div>";
    $ret .= "<button class='eventButton' onclick='ajax(".$event['Event_ID'].")'>I am at the game</button>";
    $ret .= "</span>";
    $ret .= "</div>";
    $ret .= "</div>";
  }
  return $ret;
}
//TODO: Test this function
function generateEventArray($result){
  $ret = array();
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      array_push($ret, $row);
    }
  }
  return $ret;
}

function determineTokens($age, $major, $living){
  $tokens = 0;
  if($age != NULL){
    $tokens += 25;
  }
  if($major != NULL){
    $tokens += 25;
  }
  if($living != NULL){
    $tokens += 50;
  }
  return $tokens;
}
//TODO: Test this function
function registerUser($username, $password, $email, $str, $age=NULL, $major=NULL, $living=MULL){
  $conn = dbConnect();
  $sanitized_username = mysqli_real_escape_string($conn, $username);
  $sanitized_password = mysqli_real_escape_string($conn, $password);
  $sanitized_email = mysqli_real_escape_string($conn, $email);
  $sanitized_age = mysqli_real_escape_string($conn, $age);
  $sanitized_major = mysqli_real_escape_string($conn, $major);
  $sanitized_living = mysqli_real_escape_string($conn, $living);
  $hashed_password = password_hash($sanitized_password, PASSWORD_DEFAULT);
  $tokens = determineTokens($age, $major, $living);

  $sql = "INSERT INTO users (Username, Password, email, verifyString, age, major, living, current_tokens, total_tokens) VALUES ('$sanitized_username', '$hashed_password', '$sanitized_email', '$str', '$sanitized_age', '$sanitized_major', '$sanitized_living', '$tokens', '$tokens')";
  $result = mysqli_query($conn, $sql);
  $_POST = array();
}
 ?>
