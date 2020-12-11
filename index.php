<!DOCTYPE html>
<html lang = "ja">
    <head>
        <meta charset = "UTF-8">
        <title>mission_3-5-1</title>
    </head>
    <body>
        <?php
         $filename = "mission_3-5-1.txt";
         $date = date("Y/m/d H:i:s");
        //文章が入っている場合書き込みをする(mission_3-01の要素)(パスはmission_3-05の要素)//
         if(!empty($_POST["str"] && $_POST["name"]) && empty($_POST["erase"]) && empty($_POST["edit"])){
             if(!empty($_POST["pass"])){
                     $sentence = file ($filename,FILE_IGNORE_NEW_LINES);
                     //もしファイルが空っぽだったら番号は1//
                     $number = 1;
                    //もしファイルが空っぽじゃなかったら番号は最後の番号+1//
                    if(!empty($sentence)){
                        foreach($sentence as $lines){
                        $pieces = explode("<>",$lines);
                        $number = $pieces[0] + 1;
                        continue ;
                        }
                    }
                    //書き込みの内容//
                    $str = $number."<>".$_POST["name"]."<>".$_POST["str"]."<>".$date."<>".$_POST["pass"].PHP_EOL;
                    //ファイルを開いて書き込み//
                    $fp = fopen($filename,"a");
                    fwrite ($fp,$str);
                    fclose ($fp);
                     $message = "書き込みました！";
             }
             //パスワードが入ってなかったらエラーを出す//
             else if (empty($_POST["pass"])){
                 $message = "<FONT COLOR = RED SIZE = 5>パスワードを入れてください！</FONT>";
             }
         }
         //削除番号が入っている場合文章を削除する(mission_3-03の要素)(パスはmission_3-05の要素)//
         if (!empty($_POST["erase"] && $_POST["erasepass"]) && empty($_POST["name"] && $_POST["str"])){
             $lines = file($filename, FILE_IGNORE_NEW_LINES);
             $tmp = "";
             //1行ずつファイルから取り出していく//
             foreach ($lines as $line){
                 $pieces = explode("<>",$line);
                 //削除対象番号の書き込みでないとき//
                 //$tmpという変数に書き込みしていく//
                 if ($pieces[0] != $_POST["erase"]){
                 $tmp = $tmp.$line.PHP_EOL ;
                 }
                 //削除対象番号の書き込みのとき//
                 else if ($pieces[0] == $_POST["erase"]){
                     //パスワードが違っていたらエラー//
                     if($pieces[4] != $_POST["erasepass"]){
                         $tmp = $tmp.$line.PHP_EOL ;
                         $message = "<FONT COLOR = RED SIZE = 5>パスワードが違います！</FONT>";
                     }
                     //パスワードが合っていたらその書き込みはスキップ//
                     else if($pieces[4] == $_POST["erasepass"]){
                         $message = $_POST["erase"]."番目の書き込みを削除しました！";
                     }
                 //書き込みがなくなるまで続けます//
                continue ;
                 }
             }
             //$tmpでファイルの中身を上書きすることで、削除になる//
             //削除対象番号の書き込み以外を書き込んだものが$tmp//
             $fp = fopen($filename,"w");
                    fwrite ($fp,$tmp);
                    fclose ($fp);
         }
         //書き込みと削除が両方書き込まれてるときはどっちか選ばせる(なくてもいい要素)//
         if (!empty($_POST["erase"] || $_POST["change"]) && !empty($_POST["name"] || $_POST["str"])){
             echo "<FONT COLOR = RED SIZE = 5>書き込み・削除・編集は同時にできません!</FONT>";
         }
         //編集番号が決まっている場合編集モードにする(mission_3-04の要素)(パスはmission_3-05の要素)//
         //編集ボタンを押したときの挙動//
         if (empty($_POST["erase"] && $_POST["name"] && $_POST["str"])
            && !empty($_POST["change"] && $_POST["editpass"])){
                 $lines = file($filename, FILE_IGNORE_NEW_LINES);
                 foreach ($lines as $line){
                 $pieces = explode("<>",$line);
                     if ($pieces[0] == $_POST["change"]){
                         //編集対象の番号を見つけたら編集モードにする//
                         if($pieces[4] == $_POST["editpass"]){
                             $editnumber = $_POST["change"];
                             $editname = $pieces[1];
                             $editline = $pieces[2];
                             $message = "<FONT COLOR = RED SIZE = 5>編集モードです！</FONT>";
                             }
                         //パスワードが違ったときにエラーを出す//
                         else if($pieces[4] != $_POST["editpass"]){
                                 $message = "<FONT COLOR = RED SIZE = 5>パスワードが違います!</FONT>" ;
                                 }
                        }
                 continue ; 
                 }
            }
        //編集の仕方//
        //考え方は削除と一緒//
         if (empty($_POST["erase"]) && !empty($_POST["name"] && $_POST["str"] && $_POST["edit"])){
             //編集後の文を作っておく//
             $correct = "<>".$_POST["name"]."<>".$_POST["str"]."<>".$date;
             $lines = file($filename, FILE_IGNORE_NEW_LINES);
             $tmp = "";
             foreach ($lines as $line){
                 //編集対象じゃないとき・・・削除と同じような処理//
                 $pieces = explode("<>",$line);
                 if ($pieces[0] != $_POST["edit"]){
                 $tmp = $tmp.$line.PHP_EOL;
                 }
                 //編集対象のとき・・・編集後の文を$tmpに書き込む(パスワードは変えない)//
                 else if($pieces[0] == $_POST["edit"]){
                $tmp = $tmp.$pieces[0].$correct."<>".$pieces[4].PHP_EOL;
                 }
                 continue;
             }
             $fp = fopen($filename,"w");
                    fwrite ($fp,$tmp);
                    fclose ($fp);
             $message = $_POST["edit"]."番目の書き込みを編集しました!";
             }
         //投稿、削除、編集をしたときに出すメッセージ//
          if(!empty($message)){
              echo $message."<br><br>";
             } 
         ?>
     <form action = "" method = "post">
             投稿<br>
             <input type = "text" name = "name" placeholder = "名前" value =
             <?php
                 //編集モードのときは番号をあらかじめ入れておく//
                 if(!empty($_POST["change"])){
                     echo $editname;
             }
             ?>><br>
             <input type = "text" name = "str" placeholder = "コメント" value =
             <?php
                 //編集モードのときは文章をあらかじめ入れておく//
                 if(!empty($_POST["change"])){
                     echo $editline;
             }
             ?>><br>
             <input type = "text" name = "pass" placeholder = "パスワード">
             <input type = "hidden" name = "edit" placeholder = "隠し値" value = 
                <?php 
                     //編集対象番号をここに引き継ぐ//
                     if(!empty($_POST["change"])){
                         echo $editnumber;
                     }
                ?>
            >
             <button type = "submit" name = "submit">投稿</button><br><br>
             削除<br>
             <input type = "number" name = "erase" placeholder = "削除対象番号"><br>
             <input type = "text" name = "erasepass" placeholder = "パスワード">
             <button type = "submit" name = "submit">削除</button><br><br>
             編集<br>
             <input type = "number" name = "change" placeholder = "編集対象番号"><br>
             <input type = "text" name = "editpass" placeholder = "パスワード">
             <button type = "submit" name = "submit">編集</button>
     </form>
     <?php
         //ブラウザにファイルの中身を表示//
         if (file_exists ($filename)){
             echo "書き込み一覧<br><br>";
             $lines = file ($filename , FILE_IGNORE_NEW_LINES);
             foreach($lines as $line){
                $pieces = explode("<>",$line);
                echo $pieces[0]." ".$pieces[1]." ".$pieces[2]." ".$pieces[3]."<br>";
            }
         }
         else if(!file_exists($filename)){
             echo "書き込みがありません！";
         }
     ?>
    </body>
</html>
