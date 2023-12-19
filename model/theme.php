<?php
include_once("../config/db.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dbconn = new Database();

class theme
{
    private $db;
 
    function __construct($DB_con)
    {
      $this->db = $DB_con;
    }

    public function gettheme(){
                try {
                    $stmt = $this->db->prepare("SELECT * FROM THEME");
                    $stmt->execute();
                    $themes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
                    return $themes;
                } catch (PDOException $e) {
                    echo $e->getMessage();
                    return false;
                }
    }
   public function themetag($idtheme){
            $tag = $this->db->prepare("SELECT * FROM theme_tag
            JOIN tag ON tag.tag_id = theme_tag.tag_id
            WHERE theme_tag.theme_id = :idtheme
            ");
            $tag->bindParam(':idtheme',$idtheme,PDO::PARAM_INT);
            $tag->execute();
            $result = $tag->get_result();
            $count = 0;

            while($result->fetch_assoc()){
                $count++;
            }

            }














            // public function addTheme($data)
            // {
            //     $file = $_FILES['theme_img']['name'];
            //     $folder = '../asset/images/' . $file;
            //     $fileTmp = $_FILES['theme_img']['tmp_name'];
            //     $query = "INSERT INTO theme(theme_name, theme_img) VALUES(?,?)";
            //     $stmt = $this->db->prepare($query);
        
            //     if ($stmt) {
            //         $stmt->bind_param("ss", $data["theme_name"], $data["theme_img"]);
            //         if ($stmt->execute()) {
            //             move_uploaded_file($fileTmp, $folder);
            //         }
            //         if ($stmt->affected_rows > 0) {
            //             return true;
            //         } else {
            //             return false;
            //         }
            //         $stmt->close();
            //     } else {
            //         return "Error preparing statement: " . $this->db->error;
            //     }
            // }
        
            // public function getAllThemes()
            // {
            //     $query = "SELECT * FROM theme";
            //     $stmt = $this->db->prepare($query);
            //     $stmt->execute();
        
            //     return $stmt->get_result();
            // }
        
            // public function updateTheme($id, $theme_name)
            // {
            //     $query = "UPDATE theme SET theme_name = ? WHERE theme_id = ?";
            //     $stmt = $this->db->prepare($query);
            //     $stmt->bind_param("si", $theme_name, $id);
            //     if ($stmt->execute()) {
            //         return true;
            //     } else {
            //         header("Location: index.php");
            //     }
            // }
        
            // public function deleteTheme($id)
            // {
            //     $query = "DELETE FROM theme WHERE theme_id = ?";
            //     $stmt = $this->db->prepare($query);
            //     $stmt->bind_param("i", $id);
            //     $stmt->execute();
            //     if ($stmt->affected_rows > 0) {
            //         return true;
            //     } else {
            //         return false;
            //     }
            // }
}