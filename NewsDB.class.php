<?php
  include "INewsDB.class.php";
  
  define("DB_NAME","news.db");
  
  define("RSS_NAME","rss.xml");
  
  define("RSS_TITLE","Last NEWS");
  
  define("RSS_LINK","http://mysite.local/news/news.php");
  
  class NewsDb  implements INewsDB //extends SQLite3
{
    
    protected $_db;
    function __construct(){     
    /* $this -> db = new SQLite3(DB_NAME); */
     /* ���� ���� ���� ������ NE ������ */
     if(!file_exists(DB_NAME)){
     /* �������� ���y ������ */  
    $_db = new SQLIte3(DB_NAME);
      /* �������� ������� ��������� c ��������� ��� ������� ���*/
       $sqlCreateMsg = "CREATE TABLE if not exists msgs(id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT, category INTEGER, description TEXT, source TEXT, datetime INTEGER )";
       /* ���� ������� �� �����������*/ 
    if(!$_db->exec($sqlCreateMsg)) 
    {
     /* ������� ��������� ������ */
      echo $_db->lastErrorMsg();
    }
       /* �������� ������� ���������  c ��������� ��� ������� ���*/
    $sqlCreateCat = "CREATE TABLE if not exists category( id INTEGER, name TEXT )";
    /* ���� ������� �� �����������*/ 
     if(!$_db->exec($sqlCreateCat))
      {
        /* ������� ��������� ������ */
      echo $_db->lastErrorMsg();
      }
     }  else {
       /* ���� ���� ������ ���� ������ ������������ � ��� */
        $_db = new SQLite3(DB_NAME);
     } 
   }
    public function __get($name){
       return $this->_db;
     }  
    
     function __destruct(){
     $this ->close;
     unset($_db);
   }
       
   function saveNews($title, $category, $description, $source){
     /* ����� ������� */
     $dt = time();
     $this->_db = new SQLite3(DB_NAME);
     
  /* �������� ������� ��������� */
  $sqlInsertMsgs = "insert into msgs (title, category, description, source, datetime) values(:title, :category, :description, :source, $dt)";
  /* �������� ������� ��������� */
  $sqlInsertCat = "INSERT INTO category(id, name)
  SELECT 1 as id, 'Politics' as name
  UNION SELECT 2 as id, 'Culture' as name
UNION SELECT 3 as id, 'Sport' as name ";
  /* ��������� ������� ��������� */
  $this -> _db-> exec($sqlInsertCat);
  /* ����������� ����� ������� */
  $stmt= $this->_db -> prepare($sqlInsertMsgs);
  /* ��������� �������� � ������ ������� */
  $stmt -> bindParam(':title', $title);
  $stmt -> bindParam(':category', $category);
  $stmt -> bindParam(':description', $description);
  $stmt -> bindParam(':source', $source); 
  
  if(!$result = $stmt ->execute())
  { 
    echo $stmt -> lastErrorMsg();
   } 
   
   echo $this->_db->changes();
   return($result);
   }  
    function getNews(){
      $rows = [];
       $this->_db = new SQLite3(DB_NAME); 
      $sqlSelect = "SELECT distinct msgs.id as id, title, category.name as category, 
       description, source, datetime 
  FROM msgs, category 
  WHERE category.id = msgs.category
  
  ORDER BY msgs.id DESC";
        $result = $this -> _db -> query($sqlSelect); 
        $row = [];
        $cols= $result-> numColumns();
        $result ->reset();
         while($row =  $result ->fetchArray()){
           
            for($i = 0; $i < $cols; $i++ ){
              if($result -> columnName($i) == "datetime"){
              echo "Date : ".Date("m-i-h H:i", $row[$i])."<br>".
              "<a href = delete_news.inc.php?idDel=".$row['id']." > Delete news</a><br />";
                continue;
              }
              echo $result -> columnName($i)." : ".
               $row[$i]."<br />";
              
            }
         }
         
         
   }        
   function deleteNews($id){
      $this->_db = new SQLite3(DB_NAME);
      $sqlDeleteMsg = "delete from msgs where id = $id";
      
      return($this->_db ->exec($sqlDeleteMsg));
      
   }
    function createRSS() {
      
    $dom = new DOMDocument("1.0", "utf-8");
      /* ���������� �������������� ��������� */
      /* ����������� ����� �������� ������� � ������ ������� */
      
      $dom -> formatOutput = true;
      /* �������� ������� ������ ������� � ������� */
      $dom -> preserveWhiteSpace = false;
      
      $rss = $dom->appendChild($dom -> createElement('rss'));
      
      $chanel = $rss->appendChild($dom->createElement('chanel'));
      
      $chanel->appendChild($dom->createElement('title', RSS_TITLE));
      
      $chanel->appendChild($dom->createElement('link', RSS_LINK));
      
      
      $sqlSelect = " select distinct msgs.title, msgs.description, msgs.source, msgs.datetime, category.name as category from msgs inner join category 
        on msgs.category = category.id";
      
      $result = $this -> _db -> query($sqlSelect);
      
      /* ��������� ����������� ���������� ������� */
      while($row = $result -> fetchArray()) {
            $item  = $chanel->appendChild($dom->createElement('item'));
            $item->appendChild($dom->createElement('title', $row['title']));
            $item->appendChild($dom->createElement('link', RSS_LINK));
            $textNews = $item->appendChild($dom->createElement('description'));
            $textNews -> appendChild($dom -> createCDATASection($row['description'])); 
            
            $item->appendChild($dom->createElement('Date', date("m-i-h H:i",$row['datetime'])));
            
            $item->appendChild($dom->createElement('Category', $row['category']));
            
            $chanel -> appendChild($item);
          }
        $dom ->save(RSS_NAME);
    }
       
   }
   
 
 
  