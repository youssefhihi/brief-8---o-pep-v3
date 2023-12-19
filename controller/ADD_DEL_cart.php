<?php

include("../model/cart.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
$user_id =  $_SESSION["user_id"];

if (isset($_POST['add'])) {
  $plant_id = $_POST['plant_id'];
  $cart = new cart($dbconn->pdo);
 $cart->addToCart($plant_id);
  header("location: ../views/home.php");

  

}

if (isset($_POST["order"])) {
  $cart = new cart($dbconn->pdo);
  $success = $cart->order();
  if ($success) {
      header("location: ../views/home.php");

  } else {
      
      echo "Error processing the order.";
  }
}
else if (isset($_POST["clear"])) {
  $cart = new cart($dbconn->pdo);
  $sucess = $cart->clearCart();
  if( $sucess ) {
    header("Location: ../views/home.php");
  }else{
    echo "error";
  }
}

?>