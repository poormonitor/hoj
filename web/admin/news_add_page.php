<?php
require_once("admin-header.php");
if(!(isset($_SESSION[$OJ_NAME.'_'.'administrator']))){
  echo "<a href='../loginpage.php'>Please Login First!</a>";
  exit(1);
}

echo "<hr>";
echo "<center><h3>".$MSG_NEWS."-".$MSG_ADD."</h3></center>";


?>

<?php
if(isset($_GET['cid'])){
  $cid = intval($_GET['cid']);
  $sql = "SELECT * FROM news WHERE `news_id`=?";
  $result = pdo_query($sql,$cid);
  $row = $result[0];
  $title = $row['title'];
  $content = $row['content'];
  $defunct = $row['defunct'];
}
?>

<div class="container">
  <form method=POST action=news_add.php>
    <p align=left>
      <label class="col control-label"><?php echo $MSG_TITLE?></label>
      <input type=text name=title size=71 value='<?php echo isset($title)?$title."-Copy":""?>'>
    </p>
    <p align=left>
      <textarea id="tinymce0" name=content>
        <?php echo isset($content)?$content:""?>
      </textarea>
    </p>
    <p>
      <center>
      <input type=submit value='<?php echo $MSG_SAVE?>' name=submit>
      </center>
    </p>
    <?php require_once("../include/set_post_key.php");?>
  </form>
</div>
<?php require_once('../tinymce/tinymce.php'); ?>