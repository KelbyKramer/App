<?php

include("src/functions.php");
echo "Here is the landing page for verifying accounts";

//take str parameter
$param =  htmlspecialchars($_GET['str']);
str_replace("\n","",$param);

if(strlen($param) != 12){
  echo "Error";
}
else{
  //$sql = "UPDATE users SET verify=1 WHERE verifyString=$param";
  updateQuery("users", "verify=1", "verifyString='".$param."'");
  echo "Your account has been verified";
  echo "<button> <a href='index.php'>Log into your account</a> </button>";
  //TODO: Make it so that it logs in and redirects to dashborrd
}
//search database where the verifyString is equal


//do the check and then change the verify boolean and redirect to dashboard
 ?>
