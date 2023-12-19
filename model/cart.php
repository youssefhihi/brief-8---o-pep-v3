<?php
include_once("../config/db.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dbconn = new Database();

class cart
{
    private $db;
 
    function __construct($DB_con)
    {
      $this->db = $DB_con;
    }
    public function userinfo( $email)
    {
        try{
        $stmt = $this->db->prepare("SELECT user_id FROM users WHERE user_email = :email");   
        $stmt->bindParam(":email", $email,PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $_SESSION["user_id"] = $user["user_id"];
        return  $user;
    } catch (PDOException $e) {
        echo $e->getMessage();
        return false;
    }
    }
 




    public function findPlantInCart($plant_id)
    {
        $query = "SELECT * FROM cart_items ci JOIN cart c ON ci.cart_id = c.cart_id WHERE ci.plant_id = :plante_id AND ci.status = 'PENDING' AND c.user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $user_id = $_SESSION["user_id"];
        $stmt->bindParam(":plante_id", $plant_id, PDO::PARAM_INT);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function updateQte($plant_id, $qte)
    {
        $query = "UPDATE cart_items SET quantity = :qte WHERE plant_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":qte", $qte);
        $stmt->bindParam(":id", $plant_id);
        return $stmt->execute();
    }

    public function checkPlantAvailability($plant_id)
    {
        $query = "SELECT quantity FROM plants WHERE plant_id = :plante_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":plante_id", $plant_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        if (count($result) > 0) {
            $plant = $result[0];
            $available_quantity = $plant['quantity'];
    
            return $available_quantity > 1;
        }
    
        return false;
    }

    public function addToCart($plantID){
      $plant = $this->findPlantInCart($plantID);
      if ($this->checkPlantAvailability($plantID)) {
        if (count($plant) > 0) {
          $this->updateQte($plantID, $plant["quantity"] + 1);
        } else {
          $query = "INSERT INTO cart(user_id) VALUES(:userID)";
          $stmt = $this->db->prepare($query);
          $userID = $_SESSION["user_id"];
          $stmt->bindParam(":userID", $userID);
          $stmt->execute();
    
          $cartID = $this->db->lastInsertId();
          $query2 = "INSERT INTO cart_items(cart_id , plant_id ) VALUES(:cartID, :plantID)";
          $stmt2 = $this->db->prepare($query2);
          $stmt2->bindParam(":cartID", $cartID);
          $stmt2->bindParam(":plantID", $plantID);
          $stmt2->execute();
        }
      } else {
        die("Quantity not enough");
      }
    }
    


    public function cartShow()
    {
        $query = "SELECT * FROM plants p JOIN cart_items ci ON p.plant_id = ci.plant_id JOIN cart c ON c.cart_id = ci.cart_id JOIN users u ON u.user_id = c.user_id WHERE c.user_id = :user_id AND status = 'PENDING'";
        $stmt = $this->db->prepare($query);
        $user_id = $_SESSION["user_id"];
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
    
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }

    
    

    public function clearCart()
    {
        $updateQuery = "UPDATE cart_items ci
                        JOIN cart c ON ci.cart_id = c.cart_id
                        SET ci.status = 'SOLD'
                        WHERE c.user_id = :user_id AND ci.status = 'PENDING'";

        $stmt = $this->db->prepare($updateQuery);
        $user_id = $_SESSION["user_id"];
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function calculateTotalAmount()
{
    $query = "SELECT SUM(p.plant_price * ci.quantity) AS total_amount
              FROM cart_items ci
              JOIN plants p ON ci.plant_id = p.plant_id
              JOIN cart c ON ci.cart_id = c.cart_id
              WHERE c.user_id = :user_id AND ci.status = 'PENDING'";

    $stmt = $this->db->prepare($query);
    $user_id = $_SESSION["user_id"];
    $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
    $stmt->execute();
    
    // Fetch the result directly
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if a row was fetched
    if ($row !== false) {
        return $row['total_amount'];
    } else {
        return 0;
    }
}


public function updatePlantQuantity()
{
    $updateQuery = "UPDATE plants p
                    JOIN cart_items ci ON p.plant_id = ci.plant_id
                    JOIN cart c ON ci.cart_id = c.cart_id
                    SET p.quantity = p.quantity - ci.quantity
                    WHERE c.user_id = :user_id AND ci.status = 'SOLD'";

    $stmtUpdate = $this->db->prepare($updateQuery);
    $user_id = $_SESSION["user_id"];
    $stmtUpdate->bindParam(":user_id", $user_id, PDO::PARAM_INT);

    if (!$stmtUpdate->execute()) {
        return false;
    }

    return true;
}


public function order()
{
    $cartItems = $this->cartShow();
    $user_id = $_SESSION["user_id"];
    $totalAmount = $this->calculateTotalAmount();
    $clear = $this->clearCart();
    $update = $this->updatePlantQuantity();

    
    if (!empty($cartItems)) {
        $insertOrderQuery = "INSERT INTO orders (user_id, total_amount, cart_item_id) VALUES (:user_id, :total_amount, :cart_item_id)";
        $stmtOrder = $this->db->prepare($insertOrderQuery);
        foreach ($cartItems as $cartItem) {
            if (isset($cartItem['cartitem_id']) && !is_null($cartItem['cartitem_id'])) {
                $cartItemID = $cartItem['cartitem_id'];
                echo $cartItemID ;
                           
                $stmtOrder->bindParam(":user_id", $user_id, PDO::PARAM_INT);
                $stmtOrder->bindParam(":total_amount", $totalAmount, PDO::PARAM_INT);
                $stmtOrder->bindParam(":cart_item_id", $cartItemID, PDO::PARAM_INT);

              
                if (!$stmtOrder->execute()) {
                   
                    return false;
                }
                return true;
            }
           
          }
        }

        
        if (  $clear && $update ) {
          return true;
        }
    

}

    

    public function deleteAllPlantsInCart()
    {
        $deleteQuery = "DELETE FROM cart_items WHERE status = 'PENDING' AND cart_id IN (SELECT cart_id FROM cart WHERE user_id = :id)";

        $stmt = $this->db->prepare($deleteQuery);
        $user_id = $_SESSION["user_id"];
        $stmt->bindParam(":id", $user_id);
        $stmt->execute();

        return true;
    }
}
        
    
