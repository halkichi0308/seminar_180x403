<?php
require('../xss/function.php');

if(HasPostParams() === true){

  $sid = EncyptIds($_POST['uid'], $_POST['pw']);

  setcookie('sid', $sid);

  header('Location: ./mypage.php?uname='. $_POST['uid']);
  exit();
}

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <style>
      .loginForm{
        width: 60%
      }
    </style>
  </head>
  <body>
    <p>Please login.</p>
    <fieldset class="loginForm">
      <form action="#" method="post">
        <input type="text" name="uid" value=""><br>
        <input type="password" name="pw" value=""><br>
        <input type="submit" name="btn" value="submit">
      </form>
    </fieldset>
  </body>
</html>
