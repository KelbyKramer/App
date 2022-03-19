<?php
include("dbDefine.php");
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
function insertQuery($statement, $value1, $value2){
  $conn = dbConnect();
  $stmt = $conn->prepare($statement);
  $stmt->bind_param("ss", $value1 , $value2);
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

//TODO: Test this function
function login($username, $password){
  $conn = dbConnect();
  $sanitized_username = mysqli_real_escape_string($conn, $username);
  $sanitized_password = mysqli_real_escape_string($conn, $password);
  $hashed_password = password_hash($sanitized_password, PASSWORD_DEFAULT);
  $sql = "SELECT User_ID, Password, current_tokens, verify FROM users WHERE Username='$sanitized_username'";

  $stmt = $conn->prepare('SELECT User_ID, Password, current_tokens, verify FROM users WHERE Username= ?');
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
    }
    $error = False;
    if($verify == 0){
      echo "Account has not yet been verified";
      $error = True;
    }

    if(!password_verify($password, $hashFromDatabase)){
      echo "Password is invalid";
      $error = True;
    }

    if($error == False){
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
    $ret .= "<button id='".$promo['promo_ID']."'onclick='purchasePromo(".$promo['promo_ID'].")'>Purchase this Promo</button>";
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
    $ret .= "<button id='".$promo['Promo_ID']."'onclick='displayRedeemPromo(".$promo['Promo_ID'].")'>Redeem this Promo</button>";
    $ret .= "</span>";
    $ret .= "</div>";
    $ret .= "</div>";
  }
  return $ret;
}
//TODO: Test this function
function generateHTML($arr){
  $ret = "";
  foreach($arr as $event){
    $ret .= "<div id='event ".$event['Event_ID']."'>";
    $ret .= "<div id='test'>";
    $ret .= "<span>";
    $ret .= "<img src='src/MavLogo.png'></img>";
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


//TODO: Test this function
function registerUser($username, $password, $email){
  $conn = dbConnect();
  $sanitized_username = mysqli_real_escape_string($conn, $username);
  $sanitized_password = mysqli_real_escape_string($conn, $password);
  $sanitized_email = mysqli_real_escape_string($conn, $email);
  $hashed_password = password_hash($sanitized_password, PASSWORD_DEFAULT);
  $sql = "INSERT INTO users (Username, Password, email) VALUES ('$sanitized_username', '$hashed_password', '$sanitized_email')";
  //$sql = "DELETE FROM users WHERE Username='Kelby'";
  echo "Hello register the user please";
  $result = mysqli_query($conn, $sql);
  $_POST = array();
}
 ?>
