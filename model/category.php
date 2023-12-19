<?php
include("../config/db.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dbconn = new Database();
class categoryDAO{
    private $ID;
    private $name;
  
    public function __construct($ID, $name){
      $this->ID = $ID;
      $this->name = $name;
    }
    public function getId(){
      return $this->ID;
    }
    public function getName(){
      return $this->name;
    }
}

class category
{
    private $db;
 
    function __construct($DB_con)
    {
      $this->db = $DB_con;
    }
       
    public function getCategories() {
        try {
            $stmt = $this->db->prepare("SELECT category_id, category_name FROM category");
            $stmt->execute();
            $allCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $categories = array();
                foreach($allCategories as $C){
                $categories[] = new CategoryDAO($C['category_id'], $C['category_name']);
                }
                return $categories;
            
           
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function addC($nameC)
{
    try {
        $stmt = $this->db->prepare("INSERT INTO category(category_name) VALUES(:name)");

        if ($stmt) {
            $stmt->bindParam(":name", $nameC, PDO::PARAM_STR);
            $success = $stmt->execute();

            if ($success) {
                return true;
            }
        } else {
            return "Error preparing statement: ";
        }

    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

public function deleteC($id)
{
    try {
        $stmt = $this->db->prepare("DELETE FROM category WHERE category_id = :id");

        if ($stmt) {
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $success = $stmt->execute();

            if ($success) {
                return true;
            } else {
                return "Error executing statement: " . implode(", ", $stmt->errorInfo());
            }
        } else {
            return "Error preparing statement: " . implode(", ", $this->db->errorInfo());
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}


public function updateC($id, $nameC)
{
    try {
        $stmt = $this->db->prepare("UPDATE category SET category_name = :name WHERE category_id = :id");

        if ($stmt) {
            $stmt->bindParam(":name", $nameC, PDO::PARAM_STR);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);

            $success = $stmt->execute();

            if ($success) {
                return true;
            } else {
                return "Error executing statement: " . implode(", ", $stmt->errorInfo());
            }
        } else {
            return "Error preparing statement: " . implode(", ", $this->db->errorInfo());
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}


}