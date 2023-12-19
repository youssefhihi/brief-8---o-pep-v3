<?php
include ("../model/user.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start();

if (isset($_POST["signup"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $l3ayba = new USER($dbconn->pdo);
    $registrationResult = $l3ayba->register($name, $email, $password);

    if ($registrationResult) {
        $_SESSION["user_email"] = $email ;
        header('location: ../views/role.php');
        exit; 
    } else {
        echo 'Registration failed';
    }   
}else if (isset($_POST["login"])){
  $email = $_POST["email"];
  $password = $_POST["password"];
  $l3ayba = new USER($dbconn->pdo);
  $l3ayba->login($email,  $password ); 
   
}



