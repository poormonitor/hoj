<?php
if ($OJ_MEMCACHE) {
  $private = mysql_query_cache("SELECT private FROM contest WHERE contest_id=$cid")[0][0];
  if ($private == "1" and basename($_SERVER['PHP_SELF'])!="contestrank2.php") {
    $sql = "SELECT privilege.user_id,users.nick,solution.result,solution.num,solution.in_date,solution.pass_rate FROM privilege LEFT JOIN users ON users.user_id=privilege.user_id LEFT JOIN solution on solution.user_id = privilege.user_id AND solution.contest_id=$cid WHERE privilege.rightstr='c$cid' ORDER BY user_id,solution_id";
  } else {
    $sql = "SELECT user_id,nick,solution.result,solution.num,solution.in_date,solution.pass_rate FROM solution where solution.contest_id=$cid and num>=0 and problem_id>0 ORDER BY user_id,solution_id";
  }
  $result = mysql_query_cache($sql);
  if ($result) $rows_cnt = count($result);
  else $rows_cnt = 0;
} else {
  $private = pdo_query("SELECT private FROM contest WHERE contest_id=$cid")[0][0];
  if ($private == "1" and basename($_SERVER['PHP_SELF'])!="contestrank2.php") {
    $sql = "SELECT privilege.user_id,users.nick,solution.result,solution.num,solution.in_date,solution.pass_rate FROM privilege LEFT JOIN users ON users.user_id=privilege.user_id LEFT JOIN solution on solution.user_id = privilege.user_id AND solution.contest_id=$cid WHERE privilege.rightstr='c$cid' ORDER BY user_id,solution_id";
  } else {
    $sql = "SELECT user_id,nick,solution.result,solution.num,solution.in_date,solution.pass_rate FROM solution where solution.contest_id=$cid and num>=0 and problem_id>0 ORDER BY user_id,solution_id";
  }
  $result = pdo_query($sql);
  if ($result) $rows_cnt = count($result);
  else $rows_cnt = 0;
}