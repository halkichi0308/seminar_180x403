<?php
require('function.php');
session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])){

  if(isset($_POST['userid']) && isset($_POST['passwd'])){

    $input['userid'] = (string)$_POST['userid'];
    $input['passwd'] = (string)$_POST['passwd'];

    if(empty($input['userid'])||empty($input['passwd'])){
      $_SESSION['err'] = 4;
      header('Location: ./login.php');
      exit;
    }

    $dbh = connectDB($db);

    //DBが存在するかチェック
    if(existTable($dbh, $db) === FALSE){

      if(existDB($dbh, $db) === FALSE){

        $dbh->exec('CREATE DATABASE IF NOT EXISTS test');

      }

      $sql = 'CREATE TABLE %s.%s(id int AUTO_INCREMENT NOT NULL PRIMARY KEY, name varchar(100), password varchar(255));';
      $dbh->exec(sprintf($sql, $db['dbname'], $db['tblname'])); //not bind

    }

    try{

      foreach(existUser($input['userid'], $db) as $keys){
        if($keys[0] > 0){
          $_SESSION['err'] = 5;
          header('Location: ./login.php');
          exit;
        };
      }
      //パスワードハッシュ化
      //$pwHash = password_hash($input['passwd'], PASSWORD_BCRYPT);
      //hash関数は非推奨。
      $pwHash = hash('sha512', $input['passwd']);

      $tblname = $db['dbname'].'.'.$db['tblname'];
      $sql = "INSERT INTO {$tblname}(name,password)VALUES(:name, :password)";

      $stmt = $dbh->prepare($sql);

      $stmt->bindParam(':name', $input['userid'], PDO::PARAM_STR);
      $stmt->bindParam(':password', $pwHash, PDO::PARAM_STR);

      $result = $stmt->execute();

      if($result !== FALSE){
        header('Location: ./login.php?signup=true');
        exit;
      }else{
        throw new Exception('insert err.');
      }

    }catch(Exception $e){
      $_SESSION['err'] = 'dberr';
      header('Location: ./login.php?err=dberr');
      exit;
    }
  }else{
    $_SESSION['err'] = 3;
    header('Location: ./login.php');
    exit;
  }
}else{
  header('Location: ./login.php');
  exit;
}
?>
