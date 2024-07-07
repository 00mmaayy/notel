<?php
include('connection/conn.php');
session_start();
$s="select * from company";
$q=mysql_query($s) or die(mysql_error());
$r=mysql_fetch_assoc($q);
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title><?php echo $r['company_name']; ?></title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<br/><br/>
<div class="login-page">
  <div class="form">
  <div align="center">
      <?php echo $r['company_name']; ?>
      <!--<img src="img/yourlogo.jpg" height="100" width="170"/>-->
	  <br><br/><br/>
  </div>
    <form class="login-form" method="post">
      <input name="username" type="text" placeholder="username"/>
      <input name="password" type="password" placeholder="password"/>
      <button>login</button>  
    </form>
  </div>
</div>
<script src="js/jquery.min.js"></script>
<script src="js/index.js"></script>
<?php
if (isset($_REQUEST['username']))
   {   $username=$_REQUEST['username'];
       $username=addslashes($username);
		 		 
	        if(isset($_REQUEST['password'])) 
	           { $password= md5($_REQUEST['password']);  } 
		  
		  	     $sql1="select * from users where username='$username' and password='$password'" ;
		         $result1= mysql_query($sql1) or die(mysql_error());
	             $row1=mysql_fetch_assoc($result1);
		         
				 $level= $row1['access_level'];
				 $position= $row1['position'];
				 
				 $_SESSION['username']= $username;
				 
			if($username!=$row1['username'] || $password!=$row1['password'])
			  { 
		        if($level!=0)
				  {
		            echo "<div class='styles' align='center' style='color:#FF0000'>Login Failed! username or password is incorrect!</div>";
				  }	
			  }	  
							  
		    if($level!=0)
			  { $activity= "login";
                $sql1="INSERT INTO access_log (username,activity,date) VALUES ('$username','$activity',now())" ;
                $result1= mysql_query($sql1) or die(mysql_error());
				header("location: admin.php?bookings=1&level=$level"); }
			 else { echo "<div class='styles' align='center' style='color:#FF0000'>Login Failed! username or password is incorrect!</div>"; }	
}
?>

<?php
if (isset($_REQUEST['logout']))
   {
   $username= $_SESSION['username'];
   $activity= "logout";
   $sql1="INSERT INTO access_log (username,activity,date) VALUES ('$username','$activity',now())" ;
   $result1= mysql_query($sql1) or die(mysql_error());
   
   session_destroy($_SESSION['username']);
   header("Location: index.php");
   }   
?>     
</body>
</html>