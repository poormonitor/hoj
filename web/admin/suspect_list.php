<?php
require("admin-header.php");

//if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator'])|| isset($_SESSION[$OJ_NAME.'_'.'contest_creator']))) {
//	echo "<a href='../loginpage.php'>Please Login First!</a>";
//	exit(1);
//}
?>
<center>
	<h3><?php echo $MSG_IP_VERIFICATION ?></h3>
</center>

<div class='container'>

	<?php
	require_once("../include/set_get_key.php");

	$contest_id = intval($_GET['cid']);

	$sql = "select * from (select count(distinct user_id) c,ip from solution where contest_id=? group by ip) suspect inner join (select distinct ip, user_id, in_date from solution where contest_id=? ) u on suspect.ip=u.ip and suspect.c>1 order by c desc, u.ip, in_date, user_id";

	$result = pdo_query($sql, $contest_id, $contest_id);
	?>

	<div>
		<div class='table-responsive'>
			<?php echo $MSG_CONTEST_SUSPECT1 ?>
			<table width=90% class='center table table-condensed'>
				<thead>
					<tr>
						<th class='center'>IP address</th>
						<th class='center'>Used ID</th>
						<th class='center'>Time</th>
						<th class='center'>IP address count</th>
						<th class='center'></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($result as $row) {
						echo "<tr>";
						echo "<td>" . $row['ip'] . "</td>";
						echo "<td>" . $row['user_id'] . "</td>";
						echo "<td>";
						echo "<a href='../userinfo.php?user=" . $row['user_id'] . "'><sub>" . $MSG_USERINFO . "</sub></a> <sub>/</sub> ";
						echo "<a href='../status.php?cid=$contest_id&user_id=" . $row['user_id'] . "'><sub>" . $MSG_CONTEST . " " . $MSG_SUBMIT . "</sub></a>";
						echo "</td>";
						echo "<td>" . $row['in_date'];
						echo "<td>" . $row['c'] . "</td>";
						echo "</tr>";
					}
					?>
				</tbody>
			</table>
		</div>
	</div>

	<br><br>

	<?php
	$start = pdo_query("select start_time from contest where contest_id=?", $contest_id)[0][0];
	$end = pdo_query("select end_time from contest where contest_id=?", $contest_id)[0][0];
	$sql = "select * from (select count(distinct ip) c,user_id from loginlog where time>=? and time<=? group by user_id) suspect inner join (select distinct user_id from solution where contest_id=? ) u on suspect.user_id=u.user_id and suspect.c>1 inner join (select distinct ip, user_id, time from loginlog where time>=? and time<=? ) ips on ips.user_id=u.user_id order by c desc, u.user_id, ips.time, ip";
	$result = pdo_query($sql, $start, $end, $contest_id, $start, $end);
	?>

	<div>
		<div class='table-responsive'>
			<?php echo $MSG_CONTEST_SUSPECT2 ?>
			<table width=90% class='center table table-condensed'>
				<thead>
					<tr>
						<th class='center'>User ID</th>
						<th class='center'>Used IP address</th>
						<th class='center'>Time</th>
						<th class='center'>IP address count</th>
						<th class='center'></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($result as $row) {
						echo "<tr>";
						echo "<td>" . $row['user_id'] . "</td>";
						echo "<td>";
						echo "<a href='../userinfo.php?user=" . $row['user_id'] . "'><sub>" . $MSG_USERINFO . "</sub></a> <sub>/</sub> ";
						echo "<a href='../status.php?cid=$contest_id&user_id=" . $row['user_id'] . "'><sub>" . $MSG_CONTEST . " " . $MSG_SUBMIT . "</sub></a>";
						echo "</td>";
						echo "<td>" . $row['ip'];
						echo "<td>" . $row['time'];
						echo "<td>" . $row['c'];
						echo "</tr>";
					}
					?>

				</tbody>
			</table>
		</div>
	</div>

</div>

<?php
require_once("admin-footer.php");
?>