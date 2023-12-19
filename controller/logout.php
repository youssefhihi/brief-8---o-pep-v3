<?php
include("../model/user.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST["logout"])){
    $user = new USER($dbconn->pdo);
    $logout = $user->logout();
    if($logout){
    header("Location: ../views/index.php");
    }
}