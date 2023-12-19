<?php
include_once("../config/db.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dbconn = new Database();

class plant
{
    private $db;
 
    function __construct($DB_con)
    {
      $this->db = $DB_con;
    }

    public function getCategoryfilter($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM plants WHERE category_id = :id");
            $stmt->bindParam(":id", $id,PDO::PARAM_INT);
          
            $stmt->execute();
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $categories;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function getPlants() {
        try {
            $stmt = $this->db->prepare("SELECT plants.*, category.category_name FROM plants JOIN category ON plants.category_id = category.category_id");
            $stmt->execute();
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $categories;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function addP($nameP, $imgP, $priceP, $categoryP)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO plants(plant_name, plant_img, plant_price, category_id) VALUES(:name, :img, :price, :category_id)");
    
            if ($stmt) {
                $stmt->bindParam(":name", $nameP, PDO::PARAM_STR);
                $stmt->bindParam(":img", $imgP, PDO::PARAM_STR);
                $stmt->bindParam(":price", $priceP, PDO::PARAM_INT);
                $stmt->bindParam(":category_id", $categoryP, PDO::PARAM_INT);
    
                $success = $stmt->execute();
    
                if ($success) {
                    move_uploaded_file($_FILES['plant_img']['tmp_name'], '../asset/images/' . $imgP);
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
    

    public function deleteP($id){
        
        try {
            $stmt = $this->db->prepare("DELETE FROM plants WHERE plant_id =  :id");
    
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
    public function search($plant_name)
{
    $stmt = $this->db->prepare("SELECT * FROM plants WHERE plant_name LIKE :name");
    $param = '%' . $plant_name . '%';
    $stmt->bindParam(":name", $param, PDO::PARAM_STR);
    $success = $stmt->execute();
    if($success){
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

    
}