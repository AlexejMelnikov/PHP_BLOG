 <?php
 
$title = $_POST['title'];
$description = $_POST['description'];
$source = $_POST['source'];
$category = $_POST['category'];
 if($_POST['title']!="" and $_POST['description']!="" and isset($_POST['submit']))
   {
    if($news -> saveNews($title, $category, $description, $source)){
      header('location:news.php');
 }else{
   $errMsg = " Произошла ошибка при добавлении новости";
      }
   } else {
        $errMsg = " Заполните все поля";
           }    