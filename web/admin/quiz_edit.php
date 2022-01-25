<?php
require_once("../include/db_info.inc.php");
require_once("../lang/$OJ_LANG.php");
require_once("../include/const.inc.php");
require_once("admin-header.php");
$description = "";
if (isset($_POST['qid'])) {
  require_once("../include/check_post_key.php");
  $qid = intval($_POST['qid']);

  $starttime = $_POST['startdate'] . " " . intval($_POST['shour']) . ":" . intval($_POST['sminute']) . ":00";
  $endtime = $_POST['enddate'] . " " . intval($_POST['ehour']) . ":" . intval($_POST['eminute']) . ":00";
  //echo $starttime;
  //echo $endtime;

  $title = $_POST['title'];
  $private = intval($_POST['private']);

  $question = array();
  for ($i = 1; isset($_POST['qc' . $i]); $i++) {
    array_push($question, $_POST['qc' . $i]);
  }
  $question = join("<sep />", $question);
  $type = array();
  for ($i = 0; isset($_POST['qt' . $i]); $i++) {
    array_push($type, $_POST['qt' . $i]);
  }
  $type = join("/", $type);
  $score = array();
  for ($i = 0; isset($_POST['qs' . $i]); $i++) {
    array_push($score, $_POST['qs' . $i]);
  }
  $score = join("/", $score);
  $correct_answer = array();
  for ($i = 0; isset($_POST['qca' . $i]); $i++) {
    array_push($correct_answer, $_POST['qca' . $i]);
  }
  $correct_answer = join("/", $correct_answer);

  $description = $_POST['description'];

  $title = stripslashes($title);
  $description = stripslashes($description);

  $sql = "UPDATE `quiz` SET `title`=?,`start_time`=?,`end_time`=?,`private`=?,`description`=?,`user_id`=?,`question`=?,`type`=?,`score`=?,`correct_answer`=? WHERE `quiz_id`=?";

  $description = str_replace("<p>", "", $description);
  $description = str_replace("</p>", "<br />", $description);
  $description = str_replace(",", "&#44; ", $description);
  $user_id = $_SESSION[$OJ_NAME . '_' . 'user_id'];
  pdo_query($sql, $title, $starttime, $endtime, $private, $description, $user_id, $question, $type, $score, $correct_answer, $qid);
  echo "Edit Quiz " . $qid;

  $sql = "DELETE FROM `privilege` WHERE `rightstr`=?";
  pdo_query($sql, "q$qid");

  $sql = "INSERT INTO `privilege` (`user_id`,`rightstr`) VALUES(?,?)";
  pdo_query($sql, $_SESSION[$OJ_NAME . '_' . 'user_id'], "mq$qid");

  $_SESSION[$OJ_NAME . '_' . "mq$qid"] = true;

  $pieces = array();
  $glist = $_POST["gid"];
  if (isset($_POST["gid"]) and $_POST["gid"] != '') {
    foreach ($glist as $i) {
      $sql = "SELECT `user_id` FROM `users` WHERE `gid`=?;";
      $result = pdo_query($sql, trim($i));
      foreach ($result as $p) {
        array_push($pieces, $p["user_id"]);
      }
    }
  }

  if (count($pieces) > 0 && strlen($pieces[0]) > 0) {
    $sql_1 = "INSERT INTO `privilege`(`user_id`,`rightstr`) VALUES (?,?)";
    for ($i = 0; $i < count($pieces); $i++) {
      pdo_query($sql_1, trim($pieces[$i]), "q$qid");
    }
  }
  header("Location: quiz_list.php");
  exit(0);
} else if (isset($_GET['qid'])) {

  $qid = intval($_GET['qid']);
  $sql = "SELECT * FROM quiz WHERE `quiz_id`=?";
  $result = pdo_query($sql, $qid);
  $row = $result[0];
  $title = $row['title'] . "-Copy";

  $private = $row['private'];
  $description = $row['description'];
  $type = explode("/", $row['type']);
  $question = explode("<sep />", $row['question']);
  $answer = explode("/", $row['correct_answer']);
  $score = explode("/", $row['score']);
  $num = count($type);
  $starttime = $row['start_time'];
  $endtime = $row['end_time'];

  $ulist = "";
  $sql = "SELECT `users`.`gid` FROM `privilege` JOIN `users` ON `privilege`.user_id = `users`.user_id 
              WHERE `privilege`.`rightstr`=? group BY `users`.`gid` order BY `users`.`gid`";
  $result = pdo_query($sql, "q$qid");
  $glist = array();
  foreach ($result as $row) {
    array_push($glist, $row['gid']);
  }
} else {
  header("Location: quiz_list.php");
  exit(0);
}

?>

<!DOCTYPE html>
<?php
header("Cache-control:private");
?>
<html>

<head>
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Cache-Control" content="no-cache">
  <meta http-equiv="Content-Language" content="zh-cn">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Quiz Add</title>

</head>
<hr>
<?php echo "<center><h3>" . $MSG_QUIZ . "-" . $MSG_EDIT . "</h3></center>"; ?>

<body>
  <style>
    input[type=date],
    input[type=time],
    input[type=datetime-local],
    input[type=month] {
      line-height: normal;
    }
  </style>
  <div class="container">
    <form method=POST>
      <p align=left>
        <input type='hidden' name='qid' value='<?php echo $qid; ?>'>
        <?php echo "<h3>" . $MSG_QUIZ . "-" . $MSG_TITLE . "</h3>" ?>
        <input class="input form-control" style="width:100%;" type=text name=title value="<?php echo isset($title) ? $title : "" ?>"><br /><br />
      <p align=left>
        <?php echo $MSG_QUIZ . $MSG_Start ?>:
        <input class=input-large type=date name='startdate' value='<?php echo substr($starttime, 0, 10) ?>' size=4>
        Hour: <input class=input-mini type=text name=shour size=2 value='<?php echo substr($starttime, 11, 2) ?>'>&nbsp;
        Minute: <input class=input-mini type=text name=sminute value='<?php echo substr($starttime, 14, 2) ?>' size=2>
      </p>
      <p align=left>
        <?php echo $MSG_QUIZ . $MSG_End ?>:
        <input class=input-large type=date name='enddate' value='<?php echo substr($endtime, 0, 10) ?>' size=4>
        Hour: <input class=input-mini type=text name=ehour size=2 value='<?php echo substr($endtime, 11, 2) ?>'>&nbsp;
        Minute: <input class=input-mini type=text name=eminute value='<?php echo substr($endtime, 14, 2) ?>' size=2>
      </p>
      <p align=left>
        <?php echo "<h4>" . $MSG_QUIZ . "-" . $MSG_Description . "</h4>" ?>
        <textarea id="tinymce0" rows=13 name=description cols=80><?php echo isset($description) ? $description : "" ?></textarea>
      </p>
      <p>
        <?php
        for ($i = 0; $i < $num; $i++) {
          $pid = $i + 1;
          echo "<h4>" . $MSG_QUIZ . "-" . $MSG_QUIZ_PROBLEM . " " . $pid . "</h4>";
          echo "<textarea id='tinymce$pid' rows=13 name='qc$pid' cols=80>";
          if (isset($question)) {
            echo $question[$i];
          }
          echo "</textarea><br />";
          echo "<div class='form-inline'>";
          for ($t = 0; $t <= 3; $t++) {
            if (isset($type) && $type[$i] == $t || !isset($type) && $t == 0) {
              $checked = "checked";
            } else {
              $checked = "";
            }
            echo "<input type=radio class='form-control' name='qt$i' value='$t' $checked>&nbsp;&nbsp;<label> " . $MSG_QUIZ_TYPE[$t] . "</label>&nbsp;&nbsp;";
          }
          echo "<br /><br />";
          $c_score = isset($score) ? $score[$i] : 2;
          $qa = isset($answer) ? $answer[$i] : "";
          echo "<label>" . $MSG_SCORE . "</label>&nbsp;&nbsp;";
          echo "<input type=text name='qs$i' class='form-control' value='" . $c_score . "'>";
          echo "&nbsp;&nbsp;<label>" . $MSG_CORRECT_ANSWER . "</label>&nbsp;&nbsp;";
          echo "<input type=text name='qca$i' class='form-control' value='" . $qa . "'>";
          echo "</div>";
        }
        ?>
      </p>
      <br />
      <table width="100%">
        <tr>
          <td height="10px" style="padding:10px;">
            <p align=left>
              <?php echo $MSG_QUIZ . "-" . $MSG_Public ?>:
              <select name='private' style="width:150px;">
                <option value='0' <?php echo isset($private) && $private == '0' ? 'selected' : '' ?>><?php echo $MSG_Public ?></option>
                <option value='1' <?php echo isset($private) && $private == '1' ? 'selected' : '' ?>><?php echo $MSG_Private ?></option>
              </select>
            </p>
          </td>
        </tr>
        <tr>
          <td height="*" style="padding:20px;">
            <p align=left>
              <?php echo $MSG_QUIZ . "-" . $MSG_GROUP ?>
              <br />
              <select name="gid[]" class="selectpicker show-menu-arrow form-control" size='8' multiple>
                <?php
                require_once("../include/my_func.inc.php");
                $sql_all = "SELECT * FROM `group`;";
                $result = pdo_query($sql_all);
                $all_group = $result;
                foreach ($all_group as $i) {
                  $show_id = $i["gid"];
                  $show_name = $i["name"];
                  if (isset($glist) && in_array($show_id, $glist)) {
                    echo "<option value='$show_id' selected>$show_name</option>";
                  } else {
                    echo "<option value='$show_id'>$show_name</option>";
                  }
                }
                ?>
              </select>&nbsp;&nbsp;
            </p>
          </td>
        </tr>
      </table>

      <div align=center>
        <?php require_once("../include/set_post_key.php"); ?>
        <input type=submit value='<?php echo $MSG_SAVE ?>' name=submit>
      </div>
    </form>
  </div>

  <?php
  require_once("../oj-footer.php");
  require_once('../tinymce/tinymce.php'); ?>
</body>

</html>