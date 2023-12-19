<?php
include("../config/db.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dbconn = new Database();

class USER 
{
    private $db;
 
    function __construct($DB_con)
    {
      $this->db = $DB_con;
    }
    public function notEmptyLogin($email, $pwd){
        if(empty($email) || empty($pwd)){
          return false;
        }
        return true;
      }
    
      public function findUserByEmail($email) {
        $query = "SELECT * FROM users WHERE user_email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 0) {
          return true;
        }
        return false;
      }
    
 
    public function register($name,$mail,$pass)
    {
       try
       {
           $password = password_hash($pass, PASSWORD_BCRYPT);
   
           $stmt = $this->db->prepare("INSERT INTO users(user_name,user_email,user_password) 
                                                       VALUES(:name, :gmail, :pass)");
          
           $stmt->bindparam(":name", $name);
           $stmt->bindparam(":gmail", $mail);
           $stmt->bindparam(":pass", $password);            
           $stmt->execute();   
         
   
           return $stmt; 
         
       }
       catch(PDOException $e)
       {
           echo $e->getMessage();
       }    
    }

   public function updateRole($role, $user_email)
   {
       try {
           $stmt = $this->db->prepare("UPDATE users SET role_id = :role WHERE user_email = :user_email");
           $stmt->bindParam(':role', $role, PDO::PARAM_INT);
           $stmt->bindParam(':user_email', $user_email, PDO::PARAM_STR);

           return $stmt->execute();
       } catch (PDOException $e) {
           echo $e->getMessage();
           return false;
       }
   }
    
   public function login($email, $pwd){
    if($this->notEmptyLogin($email, $pwd)){
      if ($this->findUserByEmail($email)) {
        $query = "SELECT * FROM users WHERE user_email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (password_verify($pwd, $user['user_password'])) {
          $_SESSION["logged"] = true;
          $_SESSION["user_id"] = $user["user_id"];
          $_SESSION["role_id"] = $user["role_id"];
          if ($_SESSION["role_id"] == 1) {
            header("Location: ../views/home.php");
          }else if ($_SESSION["role_id"] == 2) {
            header("Location: ../views/dashboard.php");
          }
        }else{
          $invalidInputsErr = "Your email or password is incorrect!!";
          header("Location: ../views/index.php?error=".$invalidInputsErr);    
        }
      
      }else{
        $invalidInputsErr = "Your email or password is incorrect!!";
        header("Location: ../views/index.php?error=".$invalidInputsErr);  
      }
    }else{
      $emptyInputsErr = "Please fill out all the fields first!!";
      header("Location: ../views/index.php?error=".$emptyInputsErr);
    }
  }

 
 
   public function logout()
   {
    unset($_SESSION['logged']);
    unset($_SESSION['user_id']);
    unset($_SESSION['role_id']);
    
    return true;
   }

 
}






