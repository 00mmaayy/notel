<?php 
session_start();
include('connection/conn.php');
if(!isset($_SESSION['username'])){
$loc='Location: index.php?msg=requires_login '.$_SESSION['username'];
header($loc); }

$new_user=$_GET['new_user'];
$password=$_GET['password'];
$new_first_name=$_GET['f_name'];
$new_middle_name=$_GET['m_name'];
$new_last_name=$_GET['l_name'];
$new_position=$_GET['position'];
$new_level=$_GET['access_level'];
$username=$_SESSION['username'];

$sql="insert into users (username,password,first_name,middle_name,last_name,position,access_level,date_created,time_created,created_by)
     values ('$new_user','$password','$new_first_name','$new_middle_name','$new_last_name',$new_position,$new_level,curdate(),curtime(),'$username')";
$query=mysql_query($sql) or die(mysql_error());
$return='Location: admin.php?level='.$_REQUEST['level'].'&settings=1&createuser=1&success=1';
header($return);
?>
