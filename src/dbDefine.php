<?php

function dbConnect(){
  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname = "app";

  /*$servername = "localhost";
  $username = "scrudu50_root";
  $password = "+byia,hZ]-}y";
  $dbname = "scrudu50_app";*/

  // Create connection
  $conn = new mysqli($servername, $username, $password, $dbname);
  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  return $conn;
}

 ?>
