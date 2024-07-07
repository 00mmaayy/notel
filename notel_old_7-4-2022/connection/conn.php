<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname = "localhost";
$database = "notel";
$username= "root";
$password = "";
$syshubconn = mysql_connect($hostname,$username,$password) or trigger_error(mysql_error(),E_USER_ERROR); 
mysql_set_charset('utf8',$syshubconn);
$db=mysql_select_db($database) or die("I Couldn't select your database");
?>
