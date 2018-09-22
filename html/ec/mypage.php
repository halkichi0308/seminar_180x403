<?php
require('../xss/function.php');

if(HasSid() === true){

  $chkUname = getUname($_COOKIE['sid']);

  if($chkUname === false || $chkUname === NULL){
    //sidがbase64_decode出来なかった場合のエラー
    header('Location: /login.php?err=1');
  }else{

    $uname = isset($_GET['uname']) ? $_GET['uname'] : '';
  }

}else{

  header('Location: ./login.php');

  exit();
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta caheset="utf-8">
  </head>
  <body>
    <h1>
      <?php
        if(isset($uname))echo 'Welcome ' . $uname;
      ?>
    </h1>
  </body>
</html>
