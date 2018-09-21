<?php
require('function.php');

session_start();

if(isset($_COOKIE['PHPSESSID']) && isset($_SESSION["NAME"])){

  $username = $_SESSION["NAME"];

}else{

  header('Location: ./login.php?err=2');

}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>マイページ</title>
  </head>
  <body>
    <fieldset>
      <p>ようこそ <?php echo $username; ?>さん</p>
      <input type="button" onclick="logout()" value="ログアウト">
    </fieldset>
    <script>
      'use strict'
      function logout(){
        location.href="./login.php?logout=1";
      }
    </script>
  </body>
</html>
