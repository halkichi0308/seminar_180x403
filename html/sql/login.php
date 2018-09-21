<?php
require('function.php');

session_start();
$errorMessage = "";
$errFlg = "";
//ログイン時の処理
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    if(isset($_POST['userid']) && isset($_POST['passwd'])){

      $sqlResult = existUserId($_POST['userid'], $_POST['passwd'], $db);

      $existUser = $sqlResult[0];

      $errinfo = $sqlResult[1];

      if($existUser === FALSE){

        if(!empty($errinfo[2])){
          $errorMessage = $errinfo[2];
          $_SESSION['db_err'] = $errinfo[2];
        }
        $_SESSION['err'] = 1;
        //header('Location: ./login.php?err=1');
        header('Location: ./login.php');
        exit;

      }else{

        foreach($existUser as $key){
          $_SESSION["NAME"] = $key;
        }
        session_regenerate_id();
        header('Location: ./mypage.php');
        exit;
      }

      return true;
    }else{
      $_SESSION['err'] = 3;
      header('Location: ./mypage.php');
      exit;
    }

}
//ログアウト時の処理
if(isset($_GET['logout'])){
  $_SESSION = array();
  session_destroy();
}

//新規登録時の処理
if(isset($_GET['signup'])){
  $errFlg = 10;
  $errorMessage = '登録完了しました。';
}

//エラー時の処理
//if(isset($_GET['err'])){
if(isset($_SESSION['err'])){

  $errFlg = $_SESSION['err'];
  switch($errFlg){
    case 0:
      break;
    case 1:
      $errorMessage = 'ユーザIDまたはパスワードが正しくありません。';
      $errorMessage .= catchErrInfo();
      break;
    case 2:
      $errorMessage = 'ログイン認証を行って下さい。';
      break;
    case 3:
      $errorMessage = 'ユーザIDまたはパスワードが入力されていません。';
      break;
    case 4:
      $errorMessage = 'ユーザIDまたはパスワードが入力されていません。';
      break;
    case 5:
      $errorMessage = '既に登録されているユーザ名です。';
      break;
    default:
      $errorMessage = '予期せぬエラーが発生しました。';
      break;
  }
  session_destroy();
}
?>

<!DOCTYPE html>
<html>
    <head>
            <meta charset="UTF-8">
            <title>ログイン</title>
    </head>
    <body>
        <h1>ログイン画面</h1>
        <form id="loginForm" name="loginForm" action="#" method="POST">
            <fieldset>
                <legend>ログインフォーム</legend>
                <div><font color="#ff0000">
                  <?php echo $errFlg <= 3 ? htmlspecialchars($errorMessage, ENT_QUOTES) :''; ?></font>
                </div>
                <label for="userid">ユーザーID</label><input type="text" id="userid" name="userid" placeholder="ユーザーIDを入力" value="<?php echo !empty($_POST["userid"]) ? htmlspecialchars($_POST["userid"], ENT_QUOTES):'';?>">
                <br>
                <label for="passwd">パスワード</label><input type="password" id="passwd" name="passwd" value="" placeholder="パスワードを入力">
                <br>
                <input type="submit" id="login" name="login" value="ログイン">
            </fieldset>
        </form>
        <br>
        <form id="signUpForm" name="signUpForm" action="./signup.php" method="POST">
            <fieldset>
                <legend>新規登録フォーム</legend>
                <div><font color="#ff0000">
                  <?php echo $errFlg > 3 ? htmlspecialchars($errorMessage, ENT_QUOTES) :''; ?></font>
                </div>
                <label for="userid">ユーザーID</label><input type="text" id="userid" name="userid" placeholder="ユーザーIDを入力" value="<?php if (!empty($_POST["userid"])) {echo htmlspecialchars($_POST["userid"], ENT_QUOTES);} ?>">
                <br>
                <label for="passwd">パスワード</label><input type="password" id="passwd" name="passwd" value="" placeholder="パスワードを入力">
                <br>
                <input type="submit" id="login" name="login" value="新規登録">
                <input type="hidden" name="signup" value="true">
            </fieldset>
        </form>
    </body>
</html>
