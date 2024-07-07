<?php
include('connection/conn.php');
session_start();
if(!isset($_SESSION['username'])){
$loc='Location: index.php?msg=requires_login '.$_SESSION['username'];
header($loc); }

$level=$_REQUEST['level'];
$useroption=$_REQUEST['useroption'];

//Start for Users Only
if(isset($_REQUEST['update_password']))
{   $newpassword1=md5($_REQUEST['new_password']);
    $newpassword2=md5($_REQUEST['password_repeat']);
    $user_account=$_SESSION['username'];
   
   if($newpassword1==$newpassword2)
     {
	   $s1="update users set password='$newpassword1' where username='$user_account' ";
       $q1=mysql_query($s1) or die(mysql_error());
       
	   $loc1='Location: admin.php?settings=1&level='.$_REQUEST['level'].'&passwordupdated=1';
       header($loc1);
	 }
   else	 
     {  
       $loc1='Location: admin.php?settings=1&level='.$_REQUEST['level'].'&passworderror=1';
       header($loc1); 
     }	  
}
//End for Users Only

//Start for administrator only
if(isset($_REQUEST['resetpass']))
{
$s1="update users set password='e10adc3949ba59abbe56e057f20f883e' where username='$useroption' ";
$q1=mysql_query($s1) or die(mysql_error());

$loc1='Location: admin.php?settings=1&createuser=1&level=1';
header($loc1);
}

if(isset($_REQUEST['disableuser']))
{ 
$s1="update users set access_level=0 where username='$useroption' ";
$q1=mysql_query($s1) or die(mysql_error());

$loc2='Location: admin.php?settings=1&createuser=1&level=1';
header($loc2);
}

if(isset($_REQUEST['enableuser']))
{ 
$s1="update users set access_level=1 where username='$useroption' ";
$q1=mysql_query($s1) or die(mysql_error());

$loc2='Location: admin.php?settings=1&createuser=1&level=1';
header($loc2);
}
//End for administrator only
?>