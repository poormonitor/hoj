<?php
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/memcache.php');
require_once('./include/my_func.inc.php');
require_once('./include/const.inc.php');
require_once('./include/setlang.php');
$view_title = $MSG_QUIZ;

if (!isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
    if (isset($OJ_GUEST) && $OJ_GUEST) {
        $_SESSION[$OJ_NAME . '_' . 'user_id'] = "Guest";
    } else {
        $view_swal = "需要登陆";
        $error_location = "loginpage.php";
        require("template/" . $OJ_TEMPLATE . "/error.php");
        exit(0);
    }
}

if ($OJ_VCODE) {
    $vcode = $_POST["vcode"];
}

$err_str = "";
$err_cnt = 0;

if ($OJ_VCODE && ($_SESSION[$OJ_NAME . '_' . "vcode"] == null || $vcode != $_SESSION[$OJ_NAME . '_' . "vcode"] || $vcode == "" || $vcode == null)) {
    $_SESSION[$OJ_NAME . '_' . "vcode"] = null;
    $view_swal .= "验证码错误！";
    require "template/" . $OJ_TEMPLATE . "/error.php";
    exit(0);
}

$quiz_id = 1000;
if (isset($_POST['qid'])) {
    $id = intval($_POST['qid']);

    $sql = "SELECT * FROM `answer` WHERE `quiz_id`=? AND `user_id`=?";
    $answered = pdo_query($sql, $id, $_SESSION[$OJ_NAME . '_' . 'user_id']);
    $rows_cnt = count($answered);
    if ($rows_cnt) {
        $view_swal = $MSG_ALREADY_SUBMIT;
        $error_location = "quiz.php?qid=$id";
        require("template/" . $OJ_TEMPLATE . "/error.php");
        exit(0);
    }

    if (
        isset($_SESSION[$OJ_NAME . '_' . 'administrator']) ||
        isset($_SESSION[$OJ_NAME . '_' . 'contest_creator']) ||
        isset($_SESSION[$OJ_NAME . '_' . 'problem_editor']) ||
        isset($_SESSION[$OJ_NAME . '_' . "q$id"])
    )
        $sql = "SELECT * FROM `quiz` WHERE `quiz_id`=?";
    else
        $sql = "SELECT * FROM `quiz` WHERE `quiz_id`=? AND `defunct`='N' AND `end_time`> now() AND `private`=0";

    $result = pdo_query($sql, $id);
    if (count($result) != 1) {
        $view_swal = $MSG_NO_SUCH_QUIZ;
        require("template/" . $OJ_TEMPLATE . "/error.php");
        exit(0);
    }
    $result = $result[0];
    $question = explode("@*@", $result['question']);
    $type = explode("/", $result['type']);
    /*
    defination of type:
    0: single choice
    1: multiple choice
    2: short answer
    */
} else {
    $view_swal = "题目不存在！";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

$answer_sheet = array();
for ($i = 1; isset($_POST["q$i"]); $i++) {
    $answer = $_POST["q$i"];
    switch ($type[$i - 1]) {
        case 0:
        case 2:
        case 3:
            break;
        case 1:
            sort($answer);
            $answer = join("", $answer);
            break;
    }
    array_push($answer_sheet, $answer);
}

$answer = join("/", $answer_sheet);

$correct_answer = explode("/", $result['correct_answer']);
$score = explode("/", $result['score']);

$auto = auto_judge_quiz($correct_answer, $answer_sheet, $score, $type);
$total = array_sum($auto);
$result_score = join("/", $auto);

$sql = "INSERT INTO `answer`(`quiz_id`, `user_id`, `answer`, `score`, `in_date`, `total`) VALUES (?,?,?,?,now(),?)";
$ans = pdo_query($sql, $id, $_SESSION[$OJ_NAME . '_' . 'user_id'], $answer, $result_score, $total);
$statusURI = "quiz.php?qid=" . $id;
header("Location: $statusURI");

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <title>提交</title>
</head>
<style>
    @media(prefers-color-scheme: dark) {
        body {
            height: auto;
            background: #242424;
        }
    }
</style>

<body></body>

</html>