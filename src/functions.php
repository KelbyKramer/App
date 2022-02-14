<?php

include("dbDefine.php");
//include("src/phpqrcode/qrlib.php");

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
  /*
  foreach($where as $value) {
    $sql .= " OR ";

    $clean_v = $value;
    if (is_string($value)) {
      $clean_v = mysqli_real_escape_string($db, $value);
    }
    $sql .= "Promo_ID=$clean_v";
  }*/
  $sql = "$sql ) $endText";
  //return $sql;
  $result = mysqli_query($db, $sql);
  //closeDB($db);
  return $result;
}


function login($username, $password){
  $conn = dbConnect();
  $sanitized_username = mysqli_real_escape_string($conn, $username);
  $sanitized_password = mysqli_real_escape_string($conn, $password);
  $hashed_password = password_hash($sanitized_password, PASSWORD_DEFAULT);
  $sql = "SELECT User_ID, Password, current_tokens FROM Users WHERE Username='$sanitized_username'";

  $result = mysqli_query($conn, $sql);
  if ($result->num_rows > 0) {
    //login credentials were successful

    while($row = $result->fetch_assoc()) {
      $hashFromDatabase = $row['Password'];
      $id = $row['User_ID'];
      $tokens = $row['current_tokens'];
    }

    if(!password_verify($password, $hashFromDatabase)){
      echo "Password is invalid";
    }
    else{
      session_start();
      $_SESSION['id'] = $id;
      $_SESSION['tokens'] = $tokens;
      echo $_SESSION['id'];
      header("location: dashboard.php?id=".$id);
    }
  }
  else{
    echo "<center>Credentials were not successful</center>";
  }
}

function convertDate($date){
  $newDate = date('F j, Y',strtotime($date));
  return $newDate;
}

function convertTime($time){
  $newTime = date("g:i a", strtotime($time));
  return $newTime;
}

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
    $ret .= "<button id='".$promo['promo_ID']."'onclick='purchasePromo(".$promo['promo_ID'].")'>Purchase this Promo</button>";
    $ret .= "</span>";
    $ret .= "</div>";
    $ret .= "</div>";
  }
  return $ret;
}

function generateQRCode($title, $business_id){
  return $title. $business_id;
}

function generateRedeemPromoHTML($arr){
  $ret = "";
  //TODO: Include redeem promo div in here
  foreach($arr as $promo){
    $ret .= "<div id='redeemPromo".$promo['Promo_ID']."' class='formAttributes hideform'>";
    $ret .= "<button id='closeRedeemPromo' onclick='closeRedeemPromo()' style='float: right; color: red;'>X</button>";
    $ret .= "<div>Redeem this promo From the backend</div>";
    $ret .= "<div>".$promo['business_name']."</div>";
    $ret .= "<div>".$promo['Title']."</div>";
    $qr = generateQRCode($promo['Title'], $promo['business_id']);
    $ret .= "<img src='src/qrCodes/".$qr.".png'></img>";
    $ret .= "<input id='redeem_code' style='width:80%;'></input>";
    $ret .= "<button id='redeem' onclick='redeemPromo(".$promo['Promo_ID'].")' style='float: right; color: green;'>Validate Promo</button>";
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
    $ret .= "<button id='".$promo['Promo_ID']."'onclick='displayRedeemPromo(".$promo['Promo_ID'].")'>Redeem this Promo</button>";
    $ret .= "</span>";
    $ret .= "</div>";
    $ret .= "</div>";
  }
  return $ret;
}

function generateHTML($arr){
  $ret = "";
  foreach($arr as $event){
    $ret .= "<div id='event ".$event['Event_ID']."'>";
    $ret .= "<div id='test'>";
    $ret .= "<span>";
    $ret .= "<img src='src/main_logo.svg'></img>";
    $ret .= "<div>".$event['type']."</div>";
    $ret .= "</span>";
    $ret .= "<span>";
    $date = convertDate($event['date']);
    $time = convertTime($event['start']);
    $ret .= "<div>".$date."</div>";
    $ret .= "<div>".$time."</div>";
    $ret .= "<div>".$event['name']."</div>";
    $ret .= "<div>".$event['address']."</div>";
    $ret .= "</span>";
    $ret .= "<span>";
    $ret .= "<div>Reward: ".$event['tokens']." Tokens</div>";
    $ret .= "<button onclick='ajax(".$event['Event_ID'].")'>I am at the game</button>";
    $ret .= "</span>";
    $ret .= "</div>";
    $ret .= "</div>";
  }
  return $ret;
}

function generateEventArray($result){
  $ret = array();
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      array_push($ret, $row);
    }
  }
  return $ret;
}

function registerUser($username, $password, $email){
  $conn = dbConnect();
  $sanitized_username = mysqli_real_escape_string($conn, $username);
  $sanitized_password = mysqli_real_escape_string($conn, $password);
  $sanitized_email = mysqli_real_escape_string($conn, $email);
  $hashed_password = password_hash($sanitized_password, PASSWORD_DEFAULT);
  $sql = "INSERT INTO Users (Username, Password, email) VALUES ('$sanitized_username', '$hashed_password', '$sanitized_email')";

  $result = mysqli_query($conn, $sql);;
  $_POST = array();
}
 ?>
