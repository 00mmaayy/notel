<?php 
session_start();
include('connection/conn.php');
if(!isset($_SESSION['username'])){
$loc='Location: index.php?msg=requires_login '.$_SESSION['username'];
header($loc); }

$new_roomovercharge=$_REQUEST['new_roomovercharge'];

$sql="update room_over_charge_rate set over_rate='$new_roomovercharge'";
$query=mysql_query($sql) or die(mysql_error());
$return='Location: admin.php?level='.$_REQUEST['level'].'&settings=1&roomovercharge=1&success=1';
header($return);
?>
