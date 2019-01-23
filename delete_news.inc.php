<?php
  include "NewsDB.class.php";
  $news =new NewsDB();
  $idDel = $_GET['idDel'];
  echo ($_GET['idDel']);
  /* if(isset($_GET['idDel'])){ */
      /* print_r($idDel); */
    $news -> deleteNews($idDel);
  
    header('location:news.php');    
  //}
  
  