<?php
$db['dsn']      = 'mysql:host=' . gethostbyname('mysql-server');
$db['user']     = 'root';
$db['password'] = 'pass';
$db['dbname']   = 'test';
$db['tblname']  = 'user_tbl';

//DBの接続まとめ
function connectDB($db){

  return new PDO($db['dsn'], $db['user'], $db['password']);
}


function existDB($dbh, $dbname){

  $hasDB = $dbh->query('show DATABASES;');

  foreach ($hasDB as $key) {

    if($key[0] === $dbname)return TRUE;
  }
  return FALSE;
}


function existTable($dbh, $db){

  $tblname = $db['dbname'].'.'.$db['tblname'];

  try {
        $result = $dbh->query("SELECT 1 FROM $tblname LIMIT 1");

    } catch (Exception $e) {
        return FALSE;
    }
    // Result is either boolean FALSE (no table found) or PDOStatement Object (table found)
    return $result !== FALSE;
}


function existUserId($uid, $pw, $db){

  try{

    $dbh = connectDB($db);

    $tblname = $db['dbname'].'.'.$db['tblname'];

    //これは今現在、非推奨。password_hashを使う
    $pw = hash('sha512', $pw);

    //それっぽいプレースホルダをしているが脆弱性ありの場合
    $sql = "SELECT name FROM {$tblname} WHERE name ='%s' AND password ='%s';";
    $stmt = $dbh->prepare(sprintf($sql, $uid, $pw));
    $stmt->execute();

    /*bind関数でプレースホルダをした場合
    $sql = "SELECT name FROM {$tableName} WHERE name=:name AND password=:password;";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':name', $uid, PDO::PARAM_STR);
    $stmt->bindParam(':password', $pw, PDO::PARAM_STR);
    $stmt->execute();
    */

    return array ($stmt->fetch(PDO::FETCH_ASSOC), $stmt->errorInfo());

  }catch(Exception $e){
    return FALSE;

  }
}

//If SESSION has an error message, Return it. DBの特定の説明の為だけに実装
function catchErrInfo(){

  if(isset($_SESSION['db_err']) && !empty($_SESSION['db_err'])){

    $_tmp = $_SESSION['db_err'];
    $_SESSION['db_err'] = array();

    return $_tmp;
  }else{
    return '';
  }
}

function existUser($uname, $db){

  $dbh = connectDB($db);

  $tblname = $db['dbname'].'.'.$db['tblname'];

  $sql = "SELECT count(*) FROM {$tblname} WHERE name=:name;";

  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':name', $uname, PDO::PARAM_STR);
  $stmt->execute();

  return $stmt->fetch(PDO::FETCH_ASSOC);
}

?>
