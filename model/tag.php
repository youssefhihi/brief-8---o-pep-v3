<?php
include_once("../config/db.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dbconn = new Database();
class Tag {
    private $db;
 
    function __construct($DB_con)
    {
      $this->db = $DB_con;
    }
    public function getTagsByTheme($themeId) {
        $stmt = $this->db->pdo->prepare("SELECT * FROM tag
                JOIN theme_tag ON theme_tag.tag_id = tag.tag_id
                JOIN theme ON theme.theme_id = theme_tag.theme_id
                WHERE theme.theme_id = ?");
        $stmt->execute([$themeId]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
}
?>
