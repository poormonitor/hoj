<?php
$cache_time = 90;
$OJ_CACHE_SHARE = false;
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/setlang.php');
$view_title = "Source Code";

require_once("./include/const.inc.php");

if (!isset($_GET['left'])) {
	$view_errors = "No such code!\n";
	require("template/error.php");
	exit(0);
}
$ok = false;
$sid = intval($_GET['left']);
$sql = "SELECT * FROM `solution` WHERE `solution_id`=?";
$result = pdo_query($sql, $sid);
$row = $result[0];
$slanguage = $row['language'];
$snick = $row['nick'];
$sresult = $row['result'];
$stime = $row['time'];
$smemory = $row['memory'];
$sproblem_id = $row['problem_id'];
$view_user_id = $suser_id = $row['user_id'];


if (!isset($_GET['right'])) {
	$view_errors = "No such code!\n";
	require("template/error.php");
	exit(0);
}
$ok = false;
$rid = intval($_GET['right']);
$sql = "SELECT * FROM `solution` WHERE `solution_id`=?";
$result = pdo_query($sql, $rid);
$row = $result[0];
$rlanguage = $row['language'];
$rnick = $row["nick"];
$rresult = $row['result'];
$rtime = $row['time'];
$rmemory = $row['memory'];
$rproblem_id = $row['problem_id'];
$rview_user_id = $ruser_id = $row['user_id'];


if (isset($OJ_AUTO_SHARE) && $OJ_AUTO_SHARE && isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
	$sql = "SELECT 1 FROM solution where 
			result=4 and problem_id=? and user_id=?";
	$rrs = pdo_query($sql, $sproblem_id, $_SESSION[$OJ_NAME . '_' . 'user_id']);
	$ok = (count($rrs) > 0);
}
$view_source = "No source code available!";
if (isset($_SESSION[$OJ_NAME . '_' . 'user_id']) && $row && $row['user_id'] == $_SESSION[$OJ_NAME . '_' . 'user_id']) $ok = true;
if (isset($_SESSION[$OJ_NAME . '_' . 'source_browser'])) $ok = true;

$sql = "SELECT `source` FROM `source_code` WHERE `solution_id`=?";
$result = pdo_query($sql, $sid);
$row = $result[0];
if ($row)
	$sview_source = $row['source'];

$sql = "SELECT `source` FROM `source_code` WHERE `solution_id`=?";
$result = pdo_query($sql, $rid);
$row = $result[0];
if ($row)
	$rview_source = $row['source'];


require("template/comparesource.php");

if (file_exists('./include/cache_end.php'))
	require_once('./include/cache_end.php');
