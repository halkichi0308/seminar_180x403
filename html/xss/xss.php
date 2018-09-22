<?php
  require('./function.php');
  session_start();
  date_default_timezone_set('Asia/Tokyo');//timezone
  ini_set('display_errors', 0);


  //なんちゃってDB
  $search_arry = [
    "test1","test2","test3","test4"
  ];
  $result_matches = [];

  //パラメータ=> 変数
  if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mode'])){

    $xss1 = $_POST['mode'] == "xss1" ? $_POST['keyword'] : '';
    $xss2 = $_POST['mode'] == 'xss2' ? $_POST['keyword'] : '';
    $xss3 = $_POST['mode'] == 'xss3' ? $_POST['keyword'] : '';
    $id = ($xss3 !== '' && $_POST['userid']) ? $_POST['userid'] : '';

    //なんちゃって検索処理
    $result_matches = pseudoSearch($search_arry, $_POST['keyword']);

  }elseif($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['mode'])){


    switch($_GET['mode']){

      case 'dom2':
        $dom2 = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        break;

      case 'xss4':
          $req = [];
          //POSTでJSON投げる時用
          //$req = json_decode(file_get_contents('php://input'), true);
          $req['keyword'] = $_GET['keyword'];
          $req['mode'] = $_GET['mode'];

          $regExp = '/' . $req['keyword'] . '.*$/';

          if($req['mode'] === 'xss4'){
            $case_xss4 = '';
            $case_xss4 = pseudoSearch($search_arry, $req['keyword'], 'JSON');

            //対策
            //header('Content-Type: application/json');
            header('Content-Type: text/html');

            exit($case_xss4);
          }
        break;

      default:
        break;
    }

  }
?>

<!DOCTYPE html>
  <html>
  <head>
  <title><? echo $HOST ;?></title>
  <script type="text/javascript" src="./js/function.js?_=<? echo date('U');?>"></script>
  <script>
    //DOM-Base用のダミー関数
    function chkTargetUser(user_name){
      user_name = '';
      if(user_name){

        location.href = '<? echo $HOST.$_SERVER['SCRIPT_NAME'];?>?uid=' + user_name;

      }
    }
  </script>
  <style>
    form{
      margin: 10vw 0 10vw 0;
    }
    li{
      list-style: none;
    }
    fieldset{
      width: 30%
    }
  </style>
  </head>
  <body>
    <a href="/">←TOPへ戻る</a>
    <div id="container">
      <h1>XSS</h1>
      <form id="form1" action="<?php echo $FileName;?>" method="post">
        <label>The first:</label>
        <?php
        //対策
        //<input type="text" name="keyword" placeholder="search" value=<?echo htmlspecialchars($xss1,ENT_QUOTES);
        ?>
        <input type="text" name="keyword" placeholder="search" value=<?echo $xss1;?>>
        <input type="hidden" name="mode" value="xss1">
        <input type="submit" id="btn" value="-search-">
      </form>

      <?

        if($_POST['mode'] === 'xss1'){
          echo '<p>' . htmlspecialchars($xss1) . 'の検索結果</p>';
          echo writeResult($result_matches);
        }
      ?>
      <hr>




      <form id="form2" action="<?php echo $FileName;?>#form2" method="post">
        <label>The second:</label>
        <input type="text" name="keyword" placeholder="search" value="<?php echo htmlspecialchars($xss2);?>">
        <input type="hidden" name="mode" value="xss2">
        <input type="submit" id="btn2" value="-search-">
      </form>

      <script>
      <?php
          //対策
          //htmlspecialchars($xss2,ENT_QUOTES)
      ?>
        let msg = '<?echo htmlspecialchars($xss2);?>';
        if(msg !== ''){
          document.write(msg + "の検索結果");
        }
      </script>
      <?
        if($_POST['mode'] === 'xss2'){
          echo writeResult($result_matches);
        }
      ?>
      <hr>

      <form id="form3" action="<?php echo $FileName;?>#form3" method="post">
        <label>The third:</label>
        <input type="text" name="keyword" placeholder="search"
        value="<?php echo htmlspecialchars($xss3,ENT_QUOTES);?>">
        <input type="hidden" name="userid" value="kensa_user">
        <input type="hidden" name="mode" value="xss3">
        <input type="submit" id="btn3" value="-search-">
      </form>

      <?
        if($_POST['mode'] === 'xss3'){
          echo '<p>' . htmlspecialchars($xss3) . 'の検索結果</p>';
          echo writeResult($result_matches,'xss3',$id);
        }
      ?>
      <hr>




      <form id="form4" name="form4">
        <label>The fourth:</label>
        <input type="text" name="keyword" placeholder="search">
        <input type="hidden" name="mode" value="xss4">
        <input type="button" id="btn4" value="-search-">
      </form>
      <p id="keyword_result"></p>
      <span id="search_result"></span>
      <script>
        let submit_btn = document.getElementById('btn4');
        let search_result = document.getElementById('search_result');
        let keyword_result_elem = document.getElementById('keyword_result');

        submit_btn.addEventListener('click',function(){

          let keyword = document.querySelector('#form4').elements['keyword'];
          let queryStrings = `?mode=xss4&keyword=${keyword.value}`;

          let xhr = new XMLHttpRequest();
          xhr.open('GET','<?php echo $FileName;?>' + queryStrings);

          //POSTでJSONを送る場合
          //let body = `{"mode":"xss4","keyword":"${keyword.value}"}`;
          xhr.addEventListener('load',function(evt){

            let keyword_result = JSON.parse(evt.target.response).keyword;

            keyword_result_elem.innerText = keyword_result + 'の検索結果';

            //検索結果のレンダリング
            if(evt.target.status === 200){
                renderAry(search_result, evt.target.response);
            }
          })

          //xhr.send(body);
          xhr.send();
        });
      </script>
      <hr>



      <h1>DOM-Based-XSS</h1>

      <!--
      IEでのみ確認できるDOM-Base-XSSの脆弱性です。サポート終了が決定したためオミットしています。
      検証用として残しておきますが、IEがES6をサポートしていないため検証するにはソースの修正が必要です。

      <form id="form7" action="<?php echo $FileName;?>#form7" method="get">
        <label>The first:</label>
        <input type="text" name="keyword" placeholder="search">
        <input type="hidden" name="mode" value="dom1">
        <input type="submit" id="btn7" value="-search-">
        <p>※IE限定
      </form>


      <?
        if(isset($_GET['mode']) && $_GET['mode'] === 'dom1'){


          $dom1_result = isset($_GET['keyword']) ? pseudoSearch($search_arry, $_GET['keyword'], 'JSON', true) :'';
          echo '<p class="forResult"><span class="result"></span>の検索結果</p>';

$writeHtml = <<<EOF
<script>
  let resultAry = '{$dom1_result}';
  let forResult = document.querySelector('.forResult');
  let dom_search_result = location.search.match(/\?keyword\=(.*)(?=&)/);
  renderAry(forResult, resultAry, render_callback, 'dom1');
</script>
EOF;
          echo $writeHtml;
        }
      ?>
      <hr>
    -->

      <form id="form8" action="<?php echo $FileName;?>#form8" method="get">
        <label>The second:</label>
        <input type="text" name="keyword" id="dom_target" placeholder="search" value="<? echo htmlspecialchars($dom2);?>">
        <input type="hidden" name="mode" value="dom2">
        <input type="submit" id="btn8" value="-search-">
      </form>
      <?
        if(isset($_GET['mode']) && $_GET['mode'] === 'dom2'){
          $dom2_result = isset($_GET['keyword']) ? pseudoSearch($search_arry, $_GET['keyword'], 'JSON', true) :'';
          echo '<p class="forResult"><span class="result"></span></p>';

$writeHtml = <<<EOF
<script>
  let resultAry = '{$dom2_result}';
  let forResult = document.querySelector('.forResult');
  let dom_target_elem = document.querySelector('#dom_target');
  renderAry(forResult, resultAry, render_callback, 'dom2');
</script>
EOF;
          echo $writeHtml;
        }
      ?>


    </div>
  </body>
</html>
