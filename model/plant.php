<?php
include_once("../config/db.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dbconn = new Database();

class plantDAO{
    private $ID;
  private $name;
  private $categoryID;
  private $IMG;
  private $price;
  private $quantity;

  public function __construct($ID, $name, $categoryID, $IMG, $price, $quantity){
    $this->ID = $ID;
    $this->name = $name;
    $this->categoryID = $categoryID;
    $this->IMG = $IMG;
    $this->price = $price;
    $this->quantity = $quantity;
  }
  public function getID(){
    return $this->ID;
  }
  public function getName(){
    return $this->name;
  }
  public function getCategoryID(){
    return $this->categoryID;
  }
  public function getIMG(){
    return $this->IMG;
  }
  public function getPrice(){
    return $this->price;
  }
  public function getQuantity(){
    return $this->quantity;
  }
}

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
            $allcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $categories = array();
            foreach($allcategories as $P){
                $categories[] = new PlantDAO($P['plant_id'], $P['plant_name'], $P['category_id'], $P['plant_img'], $P['plant_price'], $P['quantity']);
              }
         

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
            $allplants = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $plants = array();
            foreach($allplants as $P){
                $plants[] = new PlantDAO($P['plant_id'], $P['plant_name'], $P['category_id'], $P['plant_img'], $P['plant_price'], $P['quantity']);
              }
            return $plants;
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
    $stmt->execute();    
    $allplants = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $plants = array();
    foreach($allplants as $P){
        $plants[] = new PlantDAO($P['plant_id'], $P['plant_name'], $P['category_id'], $P['plant_img'], $P['plant_price'], $P['quantity']);
      }
    return $plants;
    
}

    
}