<?php 
session_start();
include('connection/conn.php');
if(!isset($_SESSION['username'])){
$loc='Location: index.php?msg=requires_login '.$_SESSION['username'];
header($loc); }

$new_roomcharge=$_REQUEST['new_roomcharge'];
$room_number=$_REQUEST['room_number'];

$sql="update room set room_rate='$new_roomcharge' where room_no='$room_number'";
$query=mysql_query($sql) or die(mysql_error());
$return='Location: admin.php?level='.$_REQUEST['level'].'&settings=1&roomcharge=1&success=1';
header($return);
?>
