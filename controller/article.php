<?php

include("../model/articles_class.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!isset($_GET['themesearch']) && !isset($_GET['search']) && !isset($_POST['page'])) {
    $article_class = new article($dbconn->pdo);
    $articles = $article_class->getArticle($idtheme);
}

if(isset($_POST['page']) && isset($_POST['theme'])) {
    $page = $_POST['page']; // page 1
    $theme = $_POST['theme']; // theme 1
    $article_class = new article($dbconn->pdo);
    $pagination_result = $article_class->pagination($page,$theme );
}
include_once("../config/db.php");
if(isset($_GET['array']) && $_GET['array'] !=='') {
    $ids = json_decode($_GET['array']);

    $article_class = new article($dbconn->pdo);
    $filter = $article_class->filter($ids);
    
    
}
?>