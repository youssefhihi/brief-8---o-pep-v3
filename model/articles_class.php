<?php
include_once("../config/db.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dbconn = new Database();

class article
{
    private $db;
 
    function __construct($DB_con)
    {
      $this->db = $DB_con;
    }

    public function getArticle($idtheme){
        try {
            $stmt = $this->db->prepare("SELECT * FROM article WHERE theme_id = $idtheme LIMIT 10");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
       
    }

    public function theme($idtheme){
        try{
        $stmt = $this->db->prepare("SELECT * FROM tag
        JOIN theme_tag ON theme_tag.tag_id = tag.tag_id
        JOIN theme ON theme.theme_id = theme_tag.theme_id
        WHERE theme.theme_id = $idtheme;
        ");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }
    public function addarticle($idtheme){
        try{
        $stmt = $this->db->prepare("SELECT * FROM theme_tag
        JOIN tag ON tag.tag_id = theme_tag.tag_id
        WHERE theme_tag.theme_id = :id_theme");
        $stmt->bindParam(':id_theme',$idtheme,PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    } catch (PDOException $e) {
        echo $e->getMessage();
        return false;
    }
       
    }

    public function getTotalPages($themeId) {
        $page = $this->db->prepare("SELECT COUNT(article_id) as totalarticle FROM article WHERE theme_id = :theme_id");
        $page->bindParam(":theme_id", $themeId, PDO::PARAM_INT);
        $page->execute();
        $row = $page->fetch(PDO::FETCH_ASSOC);
        if ($row == true) {
            $pagination = $row['totalarticle'];
            return ceil($pagination / 10);
        } else {           
            return 0;
        }
    }
    
    
    

    public function renderPaginationButtons($totalPages) {
        if ($totalPages > 1) {
            ?>
            <div class="pagination flex justify-center mt-7 mb-7">
                <?php
                for ($i = 1; $i <= $totalPages; $i++) {
                    ?>
                    <button class="page w-14 rounded-xl border border-green-500 hover:bg-green-500 hover:text-white duration-200 ease-in-out" value="<?php echo $i ?>"><?php echo $i ?></button>
                    <?php
                }
                ?>
            </div>
            <?php
        }
    }

    public function pagination($page, $theme){
        $pagination = ($page - 1) * 10;
 
        $select = $this->db->prepare("SELECT * FROM article WHERE theme_id = $theme LIMIT $pagination,10");
        $select->execute();
     
            while($row = $select->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <div onclick="attachClickListeners(<?php echo $row['article_id']?>)" class="card   ml-7 border border-green-500 rounded-xl transition-transform duration-300 ease-in-out transform hover:scale-105 hover:shadow-2xl mr-4" data-key="<?php echo $row['article_id']?>">
                <div class="ml-5 mr-5 mt-5 mb-5">
                            <h1  class="text-2xl text-center font-semibold mb-3 "><?php echo $row['article_title']?></h1>
                            <img class="h-96 w-96 mb-5" src="./assets/imgs/uploads/<?php echo $row['article_img'] ?>" alt="">
                            <h3 class=" font-sans"><?php echo $row['article_text']?></h3>
                        </div> 
                        </div>
                <?php
    }
    }


    public function filter($ids){
 
            $placeholders = rtrim(str_repeat(':id,', count($ids)), ',');
            
            $filter =  $this->db->prepare("SELECT DISTINCT article.* FROM article 
            JOIN article_tag ON article_tag.article_id=article.article_id
            JOIN tag ON tag.tag_id = article_tag.tag_id
            WHERE article_tag.tag_id IN ($placeholders);");

            $filter->bindParam(str_repeat(':id',count($ids),PDO::PARAM_INT),...$ids);
            $filter->execute();
            
            while($row = $filter->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <div onclick="attachClickListeners(<?php echo $row['article_id']?>)" class="card ml-7 border border-green-500 rounded-xl transition-transform duration-300 ease-in-out transform hover:scale-105 hover:shadow-2xl mr-4" data-key="<?php echo $row['article_id']?>">
                            <div class="ml-5 mr-5 mt-5 mb-5">
                                <h1  class="text-2xl text-center font-semibold mb-3 "><?php echo $row['article_title']?></h1>
                                <img class=" h-96 w-96 mb-5" src="./assets/imgs/uploads/<?php echo $row['article_img'] ?>" alt="">
                                <h3 class=" font-sans"><?php echo $row['article_text']?></h3>
                            </div>  
                            </div>
                <?php
            }
            
    }
   
}
