<?php
include "NewsDB.class.php";
$news = new NewsDb();
global  $news; 
 $errMsg ="";
 if(isset($_GET['idDel']))
   include 'delete_news.inc.php';
 
 
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
	<title>Новостная лента</title>
	<meta charset="utf-8" />
</head>
<body> 
  <h1>Последние новости</h1>
  <?php
  if(isset($_GET['idDel'])){
 
  /* include "delete_news.inc.php"; */
}
  $category = $_POST['category'];
    if(isset($_POST['submit'])){
     require "save_news.inc.php"; 
      
  }
  if($errMsg != "")
    echo("<h2>".$errMsg."</h2>");
  ?>
  <form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST">
    Заголовок новости:<br />
    <input type="text" name="title" /><br />
    Выберите категорию:<br />
    <select name="category">
      <option value="1">Политика</option>
      <option value="2">Культура</option>
      <option value="3">Спорт</option>
    </select>
    <br />
    Текст новости:<br />
    <textarea name="description" cols="50" rows="5"></textarea><br />
    Источник:<br />
    <input type="text" name="source" /><br />
    <br />
    <input type="submit" value="Добавить!" name = "submit"/>
</form>
<?php
include "get_news.inc.php";
$news -> createRSS();
?>
</body>
</html>