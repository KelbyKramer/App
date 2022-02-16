<?php
include("../functions.php");
//TODO: Write tests for every function
//TODO: Undergo code review and refactor code
//TODO: Refactor all SQL queries to undergo in a function and make them safe queries



//TODO: Thought: require school email and verification for each account to prevent
//misuse of promos (can't go to a game and redeem tokens on 10 accounts and get hella
//free food)
//TODO: Make logout button look nice
//TODO: Separate section for Events in Progress
//TODO: change logo pic from opposing school to the sport
//TODO: Have tokens counter update instantly when amount is changed

echo "Test page";
runTests();
$time = date('H:i:s');
echo gettype($time);

function testDateCheck1(){
  $date = "2022/01/15";
  $date2 = "2022/01/15";
  $ret = dateCheck($date, $date2);

  if($ret){
    return 1;
  }
  return 0;
}

function testDateCheck2(){
  $date = "2022/01/19";
  $date2 = "2022/01/15";
  $ret = dateCheck($date, $date2);

  if($ret){
    return 0;
  }
  return 1;
}

function testTimeCheck1(){
  $time = "14:50:35";
  $start = "12:00:00";
  $finish = "15:00:00";

  $ret = timeCheck($start, $finish, $time);

  if($ret == True){
    return 1;
  }
  return 0;
}

function testTimeCheck2(){
  $time = "10:50:35";
  $start = "12:00:00";
  $finish = "15:00:00";

  $ret = timeCheck($start, $finish, $time);

  if($ret == False){
    return 1;
  }
  return 0;
}

function generateTestHTML($var, $title){
  if($var == 0){
    $ret = "<div style='background-color: green;'>";
  }
  else{
    $ret = "<div style='background-color: red;'>";
  }
  $ret .= $title;
  $ret .= "</div>";
  return $ret;
}

function runTests(){
  $var = testDateCheck1();
  echo generateTestHTML($var, "TESTDATECHECK1");
  $var = testDateCheck2();
  echo generateTestHTML($var, "TESTDATECHECK2");
  $var = testTimeCheck1();
  echo generateTestHTML($var, "TESTTIMECHECK1");
  $var = testTimeCheck2();
  echo generateTestHTML($var, "TESTTIMECHECK2");
}

 ?>
