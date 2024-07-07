<?php 
session_start();
include('connection/conn.php');
if(!isset($_SESSION['username'])){
$loc='Location: index.php?msg=requires_login '.$_SESSION['username'];
header($loc); }

$level=$_GET['level'];
$settings=$_GET['settings'];
$new_position=$_GET['new_position'];
$creator=$_SESSION['username'];

$sql="insert into user_positions (pos_description,created_by,created_date) values ('$new_position','$creator',curdate())";
$query=mysql_query($sql) or die(mysql_error());
$return='Location: admin.php?level='.$_REQUEST['level'].'&settings=1&createposition=1&possuccess=1';
header($return);
?>
