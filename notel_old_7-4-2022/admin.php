<?php
date_default_timezone_set("Asia/Manila");
include('connection/conn.php');
session_start();
if(!isset($_SESSION['username'])){
$loc='Location: index.php?msg=requires_login '.$_SESSION['username'];
header($loc); }
$s="select * from company";
$q=mysql_query($s) or die(mysql_error());
$r=mysql_fetch_assoc($q);
?>
<!DOCTYPE html>
<html>
<title><?php echo $r['company_name']; ?></title>
<meta charset="UTF-8">
<meta http-equiv="Refresh" content="300">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/w3.css">
<link rel="stylesheet" href="css/font-awesome.min.css">
<link rel="stylesheet" href="css/bootstrap.min.css">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>

<body class="w3-light-grey">

<!-- Top container -->
<div class="w3-container w3-top w3-green w3-large w3-padding" style="z-index:4">
  <button class="w3-button w3-hide-large w3-padding-0 w3-green w3-hover-none w3-hover-text-light-grey" onclick="w3_open();"><i class="fa fa-bars"></i>  Menu</button>
<span class="w3-right"><?php echo $r['company_name']; ?></span>
</div>

<!-- Sidenav/menu -->
<nav class="w3-sidenav w3-collapse w3-white w3-animate-left" style="z-index:3;width:300px;" id="mySidenav">
  <br/>
  <div class="w3-container w3-row">
    <div class="w3-col s4"><i class="fa fa-user-circle-o w3-xxxlarge text-muted"></i></div>
    <div class="w3-col s8 w3-bar">
	<span class="text-primary">Current User:</span><br>
   <?php $current_user=$_SESSION['username'];
	     $s9="select * from users where username='$current_user'";
		 $q9=mysql_query($s9) or die(mysql_error());
		 $r9=mysql_fetch_assoc($q9);
		 
		 $current_position=$r9['position'];
	     $s8="select * from user_positions where position='$current_position'";
		 $q8=mysql_query($s8) or die(mysql_error());
		 $r8=mysql_fetch_assoc($q8);
	?>
   <span class="text-primary"><strong><?php echo $r9['first_name']." ".$r9['last_name'];?></strong><br>
   <?php echo $r8['pos_description'];?></span>
     </div>
	</div>
<hr>
  <a href="admin.php?bookings=1&level=<?php echo $_REQUEST['level'];?>" class="w3-padding" ><i class="fa fa-bed fa-fw"></i>  Bookings</a>
  <a href="admin.php?reports=1&level=<?php echo $_REQUEST['level'];?>" class="w3-padding" ><i class="fa fa-search fa-fw"></i>  Reports</a>
  <a href="admin.php?settings=1&level=<?php echo $_REQUEST['level'];?>" class="w3-padding" ><i class="fa fa-gear fa-fw"></i>  Settings</a>
  <a href="index.php?logout=1" class="w3-padding" ><i class="fa fa-power-off fa-fw"></i>  Logout</a>
</nav>
<!-- Overlay effect when opening sidenav on small screens -->
<div class="w3-overlay w3-hide-large w3-animate-opacity" onclick="w3_close()" style="cursor:pointer" title="close side menu" id="mySidenav"></div>

<!-- !PAGE CONTENT! -->
<div class="w3-main" style="margin-left:300px;margin-top:35px;">

  <!-- Header -->
  <header class="w3-container" style="padding-top:22px"></header>

  <div class="w3-row-padding w3-margin-bottom">
  
  <!--Bookings Start-->
  <?php if(isset($_REQUEST['bookings'])) 
          { ?>
	        	<div class="w3-col">
					<div class="w3-container w3-blue w3-padding-15">
						<div class="w3-left w3-xlarge">
						   <i class="fa fa-bed w3-xlarge"></i>  Bookings
						</div>
                    </div>
	                
					<br>
					
		<?php			
			if(isset($_REQUEST['book_now']))
			  {
				 //insert new booking
				 $username=$_SESSION['username'];
				 $room_no=$_REQUEST['room_no'];
				 $q3=mysql_query("select * from room where room_no=$room_no") or die(mysql_error());
				 $r3=mysql_fetch_assoc($q3);
							
				 $time_3hours=date('Y-m-d G:i:s',strtotime('+180 minute',strtotime(date('Y-m-d G:i:s'))));
				 
				 //insert checkin time
				 $room_rate=$r3['room_rate'];
				 mysql_query("insert into room_status (room,timein,timeout_assumption,timein_by) values ($room_no,now(),'$time_3hours','$username')") or die(mysql_error());
				 
				 //get id and room for 3 hour room charge
				 $q2=mysql_query("select id,room from room_status where room=$room_no order by id desc") or die(mysql_error());
				 $r2=mysql_fetch_assoc($q2);
				 
				 //insert 3 hour room charge
				 $id=$r2['id'];
				 $charge_desc="3 Hours Rate";
				 mysql_query("insert into room_charges (id,room_no,charge_date,amount,discount,charge_desc,charge_by) values ($id,$room_no,now(),$room_rate,1,'$charge_desc','$username')") or die(mysql_error());
				 
				 //change room to occupied
				 mysql_query("update room set room_status=1 where room_no=$room_no") or die(mysql_error());
				 
				 //return to booking area
				 header('Location: admin.php?bookings=1&level='.$_REQUEST['level']);
			  }
			  
			  
			  if(isset($_REQUEST['pre_checkout_now']))
			  {  
		         $id=$_REQUEST['id'];
				 $room_no=$_REQUEST['room_no'];
		  
		         $q1=mysql_query("select * from room_status where room=$room_no and id=$id and status=0") or die(mysql_error());	
				 $r1=mysql_fetch_assoc($q1);
		      
				 $date1=$r1['timeout_assumption'];
			     $date2=date('Y-m-d G:i:s');
															  
					$to_time = strtotime($date2);
					$from_time = strtotime($date1);
                   
					$overtime1=$to_time-$from_time;
					$overtime=round($overtime1) / 60;
					
					if($overtime<0)
					  { echo "no charge<br>"; }	
					
				  else{ echo "with Overtime Charge of 1.25 per minute after 5mins of due time";
					//should input to charges
					
					$overtime_less_5mins=$overtime - 5;
					
					$ot_less_5mins=number_format($overtime - 5, 2);
					$ot_hour=number_format($overtime_less_5mins / 60, 2);
					
					$charge_amount=$overtime_less_5mins * 1.25;
					$level=$_REQUEST['level'];
				    $id=$_REQUEST['id'];
				    $room_no=$_REQUEST['room_no'];
				    $charge_name="$ot_less_5mins mins or $ot_hour Hrs Overtime Charge(1.25/min) after 5mins of time due";
				    $username=$_SESSION['username'];
				    mysql_query("insert into room_charges (id,room_no,charge_date,amount,charge_desc,charge_by) values ($id,$room_no,now(),$charge_amount,'$charge_name','$username')") or die(mysql_error());
				    }
				 
				 mysql_query("update room_status set pre_checkout=1 where room=$room_no and id=$id") or die(mysql_error());
				 
				 header('Location: admin.php?bookings=1&level='.$_REQUEST['level']);
			  }
			  
			  
			  if(isset($_REQUEST['checkout_now']))
			  {
				 //room change to vacant
				 $username=$_SESSION['username'];
				 $id=$_REQUEST['id'];
				 $room_no=$_REQUEST['room_no'];
				 mysql_query("update room set room_status=0 where room_no=$room_no") or die(mysql_error());
				 
				 //insert check out time
				 mysql_query("update room_status set timeout=now(),timeout_by='$username',status=1 where room=$room_no and id=$id") or die(mysql_error());
				 
				 header('Location: admin.php?bookings=1&level='.$_REQUEST['level']);
			  }
			
			  if(isset($_REQUEST['add_charge']))
			  {	 
                //idividual charge add 		  
			    $level=$_REQUEST['level'];
				$id=$_REQUEST['id'];
				$room_no=$_REQUEST['room_no'];
				$charge_name=$_REQUEST['charge_name'];
				$charge_amount=$_REQUEST['charge_amount'];
				$username=$_SESSION['username'];
				mysql_query("insert into room_charges (id,room_no,charge_date,amount,charge_desc,charge_by) values ($id,$room_no,now(),$charge_amount,'$charge_name','$username')") or die(mysql_error());
				header('Location: admin.php?bookings=1&level='.$_REQUEST['level']);
			  }
			  
			  //if(isset($_REQUEST['add_30mins']))
			  //{	
		        //automatic add of 30 minutes
                //$level=$_REQUEST['level'];
				//$id=$_REQUEST['id'];
				//$room_no=$_REQUEST['room_no'];
				//$charge_name=$_REQUEST['charge_name'];
				//$charge_amount=$_REQUEST['charge_amount'];
				//$username=$_SESSION['username'];
				//mysql_query("insert into room_charges (id,room_no,charge_date,amount,charge_desc,add_time,charge_by) values ($id,$room_no,now(),$charge_amount,'$charge_name',30,'$username')") or die(mysql_error());
				//header('Location: admin.php?bookings=1&level='.$_REQUEST['level']);
			  //}
			  
			  //if(isset($_REQUEST['add_1hour']))
			  //{	
                 //automatic add of 1 hour		  
                //$level=$_REQUEST['level'];
				//$id=$_REQUEST['id'];
				//$room_no=$_REQUEST['room_no'];
				//$charge_name=$_REQUEST['charge_name'];
				//$charge_amount=$_REQUEST['charge_amount'];
				//$username=$_SESSION['username'];
				//mysql_query("insert into room_charges (id,room_no,charge_date,amount,charge_desc,add_time,charge_by) values ($id,$room_no,now(),$charge_amount,'$charge_name',60,'$username')") or die(mysql_error());
				//header('Location: admin.php?bookings=1&level='.$_REQUEST['level']);
			  //}
			  
			  if(isset($_REQUEST['add_vip']))
			  {	
		        //automatic add of 20% discount
                $level=$_REQUEST['level'];
				$id=$_REQUEST['id'];
				$room_no=$_REQUEST['room_no'];
				$charge_name=$_REQUEST['charge_name'];
				$charge_amount=$_REQUEST['charge_amount'];
				$username=$_SESSION['username'];
				
				$qr=mysql_query("select * from room_charges where id=$id and room_no=$room_no and discount=1") or die(mysql_error());
				$rx=mysql_fetch_assoc($qr);
				$discount=$rx['amount']*$_REQUEST['charge_amount'];
				mysql_query("insert into room_charges (id,room_no,charge_date,amount,charge_desc,charge_by) values ($id,$room_no,now(),-$discount,'$charge_name','$username')") or die(mysql_error());
				header('Location: admin.php?bookings=1&level='.$_REQUEST['level']);
			  }
			  
			  if(isset($_REQUEST['add_1pax']))
			  {	 
                //automatic add of 1 pax
				$level=$_REQUEST['level'];
				$id=$_REQUEST['id'];
				$room_no=$_REQUEST['room_no'];
				$charge_name=$_REQUEST['charge_name'];
				$charge_amount=$_REQUEST['charge_amount'];
				$username=$_SESSION['username'];
				mysql_query("insert into room_charges (id,room_no,charge_date,amount,charge_desc,charge_by) values ($id,$room_no,now(),$charge_amount,'$charge_name','$username')") or die(mysql_error());
				header('Location: admin.php?bookings=1&level='.$_REQUEST['level']);
			  }
			  
			  
			  if(isset($_REQUEST['add_overnight']))
			  {	
                //automatic add of overnight
				$level=$_REQUEST['level'];
				$id=$_REQUEST['id'];
				$room_no=$_REQUEST['room_no'];
				$room_rate=$_REQUEST['room_rate'];
				$charge_name=$_REQUEST['charge_name'];
				$charge_amount=$_REQUEST['charge_amount'];
				$username=$_SESSION['username'];
				$charge_desc="3 Hours Rate";
				mysql_query("delete from room_charges where id=$id and room_no=$room_no and charge_desc='$charge_desc' ") or die(mysql_error());
				mysql_query("insert into room_charges (id,room_no,charge_date,amount,discount,charge_desc,add_time,charge_by) values ($id,$room_no,now(),$charge_amount,1,'$charge_name',1260,'$username')") or die(mysql_error());
				header('Location: admin.php?bookings=1&level='.$_REQUEST['level']);
			  }
			  
			  
			  if(isset($_REQUEST['charges']))
			  {
				 //room change to vacant
				 $id=$_REQUEST['id'];
				 $room_no=$_REQUEST['room_no'];
				 
				 echo "<div class='col-xs'>";
					 echo "<table class='table'>
							 <tr>"; ?>
							 <form>
								<input name='level' type='hidden' value='<?php echo $_REQUEST['level']; ?>'>
								<input name='id' type='hidden' value='<?php echo $_REQUEST['id']; ?>'>
								<input name='room_no' type='hidden' value='<?php echo $_REQUEST['room_no']; ?>'>
								<input name='bookings' type='hidden' value='1'>
								    
							 <td><b class='w3-large'>Room No. <span class='w3-text-red w3-xxxlarge'><?php echo $room_no; ?></span></b></td>
							 
							 <td>
							    <b class='w3-large'>Charge Name</b><br>
								<input class='form-control' name='charge_name' type='text'>
							 </td>
							 
							 <td>
							    <b class='w3-large'>Charge Amount</b><br>
								<input class='form-control' name='charge_amount' type='number' step='any'>
							 </td>
							 
							 <td>
							    <b class='w3-large'>&nbsp;</b><br>
							    <input name='add_charge' class='btn btn-warning' type='submit' value='ADD CHARGE' onclick='return confirm("ADD CHARGES NOW?")'>
							 </td>
					         </form>
				             </tr>
						   </table>

                           <table class='table'>						   
							 <tr align='center'>
							     
							    <!---------	 
								 <td>
								   <form>
								   <input name='level' type='hidden' value='<?php //echo $_REQUEST['level']; ?>'>
								   <input name='id' type='hidden' value='<?php //echo $_REQUEST['id']; ?>'>
								   <input name='room_no' type='hidden' value='<?php //echo $_REQUEST['room_no']; ?>'>
								   <input name='room_rate' type='hidden' value='<?php //echo $_REQUEST['room_rate']; ?>'>
								   <input name='bookings' type='hidden' value='1'>
								   <input name='charge_name' type='hidden' value='Additional 30 Minutes'>
								   <input name='charge_amount' type='hidden' value='50'>
								   <input name='add_30mins' class='btn btn-warning' type='submit' value='30 MINUTES ADD' onclick='return confirm("ADD 30 MINUTES CHARGE?")'>
								   </form>
								 </td>
								 
								 <td>
								   <form>
								   <input name='level' type='hidden' value='<?php //echo $_REQUEST['level']; ?>'>
								   <input name='id' type='hidden' value='<?php //echo $_REQUEST['id']; ?>'>
								   <input name='room_no' type='hidden' value='<?php //echo $_REQUEST['room_no']; ?>'>
								   <input name='room_rate' type='hidden' value='<?php //echo $_REQUEST['room_rate']; ?>'>
								   <input name='bookings' type='hidden' value='1'>
								   <input name='charge_name' type='hidden' value='Additional 1 Hour'>
								   <input name='charge_amount' type='hidden' value='100'>
								   <input name='add_1hour' class='btn btn-warning' type='submit' value='1 HOUR ADD' onclick='return confirm("ADD 1 HOUR CHARGE?")'>
								   </form>
								 </td>
							    --------->	 
								
								<td>
								   <form>
								   <input name='level' type='hidden' value='<?php echo $_REQUEST['level']; ?>'>
								   <input name='id' type='hidden' value='<?php echo $_REQUEST['id']; ?>'>
								   <input name='room_no' type='hidden' value='<?php echo $_REQUEST['room_no']; ?>'>
								   <input name='room_rate' type='hidden' value='<?php echo $_REQUEST['room_rate']; ?>'>
								   <input name='bookings' type='hidden' value='1'>
								   <input name='charge_name' type='hidden' value='VIP Discount 20 percent'>
								   <input name='charge_amount' type='hidden' value='0.20'>
								   <input name='add_vip' class='btn btn-warning' type='submit' value='VIP DISCOUNT' onclick='return confirm("APPLY VIP DISCOUNT OF 20 PERCENT?")'>
								   </form>
								 </td>
								 
								 <td>
								   <form>
								   <input name='level' type='hidden' value='<?php echo $_REQUEST['level']; ?>'>
								   <input name='id' type='hidden' value='<?php echo $_REQUEST['id']; ?>'>
								   <input name='room_no' type='hidden' value='<?php echo $_REQUEST['room_no']; ?>'>
								   <input name='room_rate' type='hidden' value='<?php echo $_REQUEST['room_rate']; ?>'>
								   <input name='bookings' type='hidden' value='1'>
								   <input name='charge_name' type='hidden' value='1 Pax Additional'>
								   <input name='charge_amount' type='hidden' value='100'>
								   <input name='add_1pax' class='btn btn-warning' type='submit' value='1 PAX ADD' onclick='return confirm("ADD 1 PAX?")'>
								   </form>
								 </td>
								 
								 <td>
								   <form>
								   <input name='level' type='hidden' value='<?php echo $_REQUEST['level']; ?>'>
								   <input name='id' type='hidden' value='<?php echo $_REQUEST['id']; ?>'>
								   <input name='room_no' type='hidden' value='<?php echo $_REQUEST['room_no']; ?>'>
								   <input name='room_rate' type='hidden' value='<?php echo $_REQUEST['room_rate']; ?>'>
								   <input name='bookings' type='hidden' value='1'>
								   <input name='charge_name' type='hidden' value='Overnight'>
								   <input name='charge_amount' type='hidden' value='1800'>
								   <input name='add_overnight' class='btn btn-warning' type='submit' value='UPGRADE TO OVERNIGHT' onclick='return confirm("APPLY OVER NIGHT CHARGES NOW?")'>
								   </form>
								 </td>
							 </tr>
							 
			 <?php echo "</table>";
				 echo "</div>";
				 
			  }
			
			?> 		
		            
					<div class="col-xs">
	            <?php $q=mysql_query("select * from room") or die(mysql_error());
			          $r=mysql_fetch_assoc($q); 
	                
				    echo "<table class='table' border='1'>
					        <tr class='w3-tiny w3-dark-gray' align='center'>
							    <td><b>ROOM NUMBER</b></td>
								<td><b>ROOM NAME</b></td>
								<td><b>RATE / 3 HOURS</b></td>
								<td><b>CHECK IN</b></td>
								<td><b>CHECK OUT</b></td>
								<td><b>CHARGES</b></td>
								<td><b>ROOM STATUS</b></td>
							</tr>";
					    
					  do{
						  
						    $room=$r['room_no'];
						    
							  if($r['room_status']==0)
							    { echo "<tr class='w3-hover-khaki'><td align='center'><b class='w3-large'>$room</b></td>"; }
							else{ echo "<tr class='w3-hover-khaki w3-pale-red'><td align='center'><b class='w3-large w3-text-red'>$room</b></td>"; }
								  
								  echo "<td align='center'>".$r['room_name']."</td>
										<td align='center'>".$r['room_rate']."</td>";
										
								  $q1=mysql_query("select * from room_status where room=$room and status=0") or die(mysql_error());	
								  $r1=mysql_fetch_assoc($q1);
								 
							  if($r1['timein']=="" && $r1['status']==0)
							    { ?>
  							      <td align='center'>
								  <form>
								    <input name='bookings' type='hidden' value='1'>
								    <input name='level' type='hidden' value='<?php echo $_REQUEST['level']; ?>'>
								    <input name='id' type='hidden' value='<?php echo $r1['id']; ?>'>
									<input name='room_no' type='hidden' value='<?php echo $room; ?>'>
									<input class='btn btn-info w3-tiny' name='book_now' type='submit' value='BOOK NOW' onclick='return confirm("Confirm Booking: Book Now?")'>
								  </form>
								  </td>
								  <td></td>
								  <td></td>
						  <?php }								  
							elseif($r1['timein']!="" && $r1['status']==0)
							    { 
							      echo "<td align='center'>
								          <b class='w3-text-indigo'>".date('h:i A',strtotime($r1['timein']))."</b><br>"
								       .date('l',strtotime($r1['timein']))."<br>
								       <span class='w3-tiny'>".date('F d, Y',strtotime($r1['timein']))."</span>";
								  
							      if($r1['timein']!="0000-00-00 00:00:00" && $r1['timeout']=="0000-00-00 00:00:00")
								    { ?> 
									   <td align='center'>
									       
												   <?php
															$id=$r1['id'];
															$s5="select * from room_charges where room_no=$room and id=$id";
															$q5=mysql_query($s5) or die(mysql_error());
															$r5=mysql_fetch_assoc($q5);
															
															$q5=mysql_query("select sum(add_time) as time from room_charges where room_no=$room and id=$id") or die(mysql_error());
															$r5=mysql_fetch_assoc($q5);
														
														    $time=$r5['time']+180;
															
															$final_time="+$time minute";
															
															$time_3hours=date('Y-m-d H:i:s',strtotime($final_time,strtotime($r1['timein'])));
															
															echo "<b class='w3-text-red'>".date('h:i A',strtotime($final_time,strtotime($r1['timein'])))."</b><br>";
															echo date('l',strtotime($final_time,strtotime($r1['timein'])))."<br>";
															echo "<span class='w3-tiny'>".date('F d, Y',strtotime($final_time,strtotime($r1['timein'])))."</span><br>";
													
												    	  if($r1['pre_checkout']==1)
															{  ?>	
															<form>
															  <input name='bookings' type='hidden' value='1'>
															  <input name='level' type='hidden' value='<?php echo $_REQUEST['level']; ?>'>
															  <input name='id' type='hidden' value='<?php echo $r1['id']; ?>'>
															  <input name='room_no' type='hidden' value='<?php echo $room; ?>'>
															  <input class='btn btn-danger w3-tiny' name='checkout_now' type='submit' value='CHECK OUT' onclick='return confirm("Confirm Check Out?")'>
															</form>
													  <?php }
													   else {  ?>
															<form>
															  <input name='bookings' type='hidden' value='1'>
															  <input name='level' type='hidden' value='<?php echo $_REQUEST['level']; ?>'>
															  <input name='id' type='hidden' value='<?php echo $r1['id']; ?>'>
															  <input name='room_no' type='hidden' value='<?php echo $room; ?>'>
															  <input class='btn btn-info w3-tiny' name='pre_checkout_now' type='submit' value='PRE CHECK OUT' onclick='return confirm("Pre Check Out?")'>
															</form>
													   <?php } ?>
									   </td>
									   
									   <td align='center'>
									   
											   <?php
											   
											    $s3="select * from room_charges where room_no=$room and id=$id";
															$q3=mysql_query($s3) or die(mysql_error());
															$r3=mysql_fetch_assoc($q3);
															
															$q4=mysql_query("select sum(amount) as total from room_charges where room_no=$room and id=$id") or die(mysql_error());
												            $r4=mysql_fetch_assoc($q4);
															
										  echo "<table>";
												do{
											    echo "<tr>
												         <td align='right'>
														    <b class='w3-text-red'>".number_format($r3['amount'],2)."</b>
														 </td>
														 <td>&nbsp;</td>
														 <td>
														    <span class='w3-tiny'>".$r3['charge_desc']."</span>
														 </td>
													  </tr>";
											    }while($r3=mysql_fetch_assoc($q3));
										        echo "<tr>
												         <td align='center' colspan='3'><b class='w3-large w3-red'>&nbsp;total: &nbsp;".number_format($r4['total'],2)."&nbsp;</b></td>
													  </tr>
										        </table>";	   
											   ?>
											   <form>
												<input name='bookings' type='hidden' value='1'>
												<input name='level' type='hidden' value='<?php echo $_REQUEST['level']; ?>'>
												<input name='id' type='hidden' value='<?php echo $r1['id']; ?>'>
												<input name='room_rate' type='hidden' value='<?php echo $r['room_rate']; ?>'>
												<input name='room_no' type='hidden' value='<?php echo $room; ?>'>
												<input class='btn btn-warning w3-tiny' name='charges' type='submit' value='ADD CHARGES / DISCOUNTS'>
											   </form>
											   
									   </td>
							<?php }else { echo "<td></td><td></td>";}
							       
							  
								} ?>
							        
								     
								   
					  <?php  if($r['room_status']==0)
							    { echo "<td align='center' class='w3-text-blue'>VACANT</td>"; }
							else{ echo "<td align='center' class='w3-text-red'><b>OCCUPIED</b></td>"; }
							
							echo "</tr>";
				        
						} while($r=mysql_fetch_assoc($q));
						
					echo "</table>";
						
				?>	
					</div>
	  
                </div>
  <?php } ?>
  <!--Bookings End-->
  
  <!--Reports Start-->
  <?php if(isset($_REQUEST['reports'])) 
          { 

		//DELETE DATA OF 1 MONTH OLDER START
		$datekiller=date('Y-m-d',strtotime('-1 month'))." 00:00:00";
		mysql_query("delete from room_charges where charge_date<='$datekiller'");
		mysql_query("delete from room_status where timein<='$datekiller'");
		//DELETE DATA END
	
		?>
   
	<div class="w3-col">
      <div class="w3-container w3-blue w3-padding-15"><div class="w3-left w3-xlarge"><i class="fa fa-search w3-xlarge"></i>  Reports</div> </div>
      <br>
		<form align='center'>
			<input name='level' type='hidden' value='<?php echo $_REQUEST['level']; ?>'>
			<input name='reports' type='hidden' value='1'>
			<input class='btn btn-danger' name='date_range' type='submit' value='SEARCH SALE PER DATE AND TIME RANGE'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input class='btn btn-danger' name='room_reports' type='submit' value='ROOM REPORTS'>
			
		</form>
	  <br>
<?php
//ROOM REPORTS AREA START -----
if(isset($_REQUEST['room_reports']))
{
	  //DATE RANGE
	  echo "<table class='table'>";
       echo "<tr><td class='w3-teal w3-xlarge' colspan='8'>ROOM REPORTS PER DATE RANGE</td></tr>";	  
	   echo "<tr><td>Date Start</td><td>Time Start</td><td>Date End</td><td>Time End</td><td>Cashier</td></tr>";
	   echo "<tr>
	         <form method='get'>
			    <input name='reports' type='hidden' value='1'>
				<input name='room_reports' type='hidden' value=''>
				<input name='level' type='hidden' value='".$_REQUEST['level']."'>";
				
	            if(isset($_REQUEST['start']))
				  { echo "<td width='250'><input required class='form-control' name='start' type='date' value='".$_REQUEST['start']."'></td>"; }
			 else { echo "<td width='250'><input required class='form-control' name='start' type='date'></td>"; }
			    
				if(isset($_REQUEST['time_start']))
				  { echo "<td width='250'><input required class='form-control' name='time_start' type='time' value='".$_REQUEST['time_start']."'></td>"; }
             else {	echo "<td width='250'><input required class='form-control' name='time_start' type='time' value='07:00:00'></td>"; }		
			
			if(isset($_REQUEST['end']))
				  { echo "<td width='250'><input required class='form-control' name='end' type='date' value='".$_REQUEST['end']."'></td>"; }
			 else { echo "<td width='250'><input required class='form-control' name='end' type='date'></td>"; }
			    
				if(isset($_REQUEST['time_end']))
				  { echo "<td width='250'><input required class='form-control' name='time_end' type='time' value='".$_REQUEST['time_end']."'></td>"; }
             else {	echo "<td width='250'><input required class='form-control' name='time_end' type='time' value='19:00:00'></td>"; }	
			   
  			    $qc=mysql_query("select * from users") or die(mysql_error());
				$rc=mysql_fetch_assoc($qc);
				echo "<td width='250'>
				        <select required class='form-control' name='cashier'>
						  <option>ALL</option>";
				do{ echo "<option>".$rc['username']."</option>"; } while($rc=mysql_fetch_assoc($qc));
				     echo "</select>
					   </td>";
			
		  echo "<td><input name='search_room_reports' class='from-control btn btn-success' type='submit' value='SEARCH DATE RANGE'></td>
				<td></td>
				<td></td>
			 </from>
			</tr>
			<tr align='center' class='w3-amber'>
			   <td>ROOM NO</td>
			   <td>CHECK IN</td>
			   <td>CHECK IN BY</td>
			   <td>CHECK OUT</td>
			   <td>CHECK OUT BY</td>
			   <td>CHARGE DETAILS</td>
			   <td>ROOM SALE</td>";
			   
     if(isset($_REQUEST['search_room_reports']))
	   {
		  mysql_query("truncate table room_sale_temp") or die(mysql_error);
          
		  $start=$_REQUEST['start']." ".$_REQUEST['time_start'];
	      $end=$_REQUEST['end']." ".$_REQUEST['time_end'];
		  $cashier=$_REQUEST['cashier'];
		  
		  if($_REQUEST['cashier']=="ALL")
		    {  
		      $sxx1="select * from room_status where timein>='$start' and timeout<='$end' order by room asc, timein desc";
		    }
	   else {
              $sxx1="select * from room_status where timein>='$start' and timeout<='$end' and timein_by='$cashier' order by room asc, timein desc";
			}
			
		  $qxx1=mysql_query($sxx1) or die(mysql_error());
		  $rxx1=mysql_fetch_assoc($qxx1);
		  
		  do{
			   $id1=$rxx1['id'];
			   $room1=$rxx1['room'];
			   
			   $qx11=mysql_query("select * from room_charges where id=$id1 and room_no=$room1 order by charge_date desc") or die(mysql_error());
			   $rx11=mysql_fetch_assoc($qx11);
			   
			   $qx=mysql_query("select sum(amount) as roomsumtotal from room_charges where id=$id1 and room_no=$room1") or die(mysql_error());
			   $rx=mysql_fetch_assoc($qx);
			   
			   echo "<tr>
			         <td align='center'><b class='w3-large'>".$rxx1['room']."</b></td>
					 
					 <td class='w3-small' align='center'>".date('l h:i A',strtotime($rxx1['timein']))."<br>"
					                                      .date('M d, Y',strtotime($rxx1['timein']))."</td>
					 
					 <td align='center'>".$rxx1['timein_by']."</td>
					 
					 <td class='w3-small' align='center'>".date('l h:i A',strtotime($rxx1['timeout']))."<br>"
					                                      .date('M d, Y',strtotime($rxx1['timeout']))."</td>
														  
					 <td align='center'>".$rxx1['timeout_by']."</td>
					 <td>";
                        
					 do{
						 echo "<span class='w3-tiny'>".date('l h:i A M d, Y',strtotime($rx11['charge_date']))."</span>
						   <br><span class='w3-tiny'>".$rx11['charge_desc']."</span>
						   <br><b class='w3-tiny w3-text-red'>".number_format($rx11['amount'],2)."</b><br>"; 
					   } while($rx11=mysql_fetch_assoc($qx11));	 
					 
			   echo "</td>
					 <td align='center'><b class='w3-text-red'>".number_format($rx['roomsumtotal'],2)."</b></td>
					 </tr>";
					 
				$temp_total=$rx['roomsumtotal'];
				mysql_query("insert into room_sale_temp (amount) values ($temp_total)") or die(mysql_error());
				
			}while($rxx1=mysql_fetch_assoc($qxx1));     
	   
	   $qk=mysql_query("select sum(amount) as sale_total from room_sale_temp") or die(mysql_error());
	   $rk=mysql_fetch_assoc($qk);
	   echo "<tr><td colspan='6' align='right'><b>TOTAL:</b></td><td align='right' class='w3-text-red w3-large'><b>".number_format($rk['sale_total'],2)."</b></td></tr>";
	   
	   }
	  echo "</table>";
	  
} 
//ROOM REPORTS AREA END -----

//DATE RANGE AREA START -----
if(isset($_REQUEST['date_range']))
{
	  echo "<table class='table'>";
       echo "<tr><td class='w3-deep-orange w3-xlarge' colspan='6'>SEARCH DATA PER DATE RANGE</td></tr>";	  
	   echo "<tr><td>Date Start</td><td>Time Start</td><td>Date End</td><td>Time End</td><td>Cashier</td></tr>";
	   echo "<tr>
	         <form method='get'>
			    <input name='reports' type='hidden' value='1'>
				<input name='date_range' type='hidden' value=''>
				<input name='level' type='hidden' value='".$_REQUEST['level']."'>
	            
				<td width='250'>";
				      if(isset($_REQUEST['start']))
					    { echo "<input required class='form-control' name='start' type='date' value='".$_REQUEST['start']."'>"; }
			       else { echo "<input required class='form-control' name='start' type='date'>"; }
          echo "</td>
				
				<td width='250'>";
				      if(isset($_REQUEST['time_start']))
					    { echo "<input required class='form-control' name='time_start' type='time' value='".$_REQUEST['time_start']."'>"; }
				   else { echo "<input required class='form-control' name='time_start' type='time' value='07:00:00'>"; }
		  echo "</td>
				
				
				<td width='250'>";
				      if(isset($_REQUEST['end']))
					    { echo "<input required class='form-control' name='end' type='date' value='".$_REQUEST['end']."'>"; }
			       else { echo "<input required class='form-control' name='end' type='date'>"; }
          echo "</td>
				
				<td width='250'>";
				      if(isset($_REQUEST['time_start']))
					    { echo "<input required class='form-control' name='time_end' type='time' value='".$_REQUEST['time_end']."'>"; }
				   else { echo "<input required class='form-control' name='time_end' type='time' value='19:00:00'>"; }
		  echo "</td>";
				
				$qc=mysql_query("select * from users") or die(mysql_error());
				$rc=mysql_fetch_assoc($qc);
				echo "<td width='250'>
				        <select required class='form-control' name='cashier'>
						  <option>ALL</option>";
				do{ echo "<option>".$rc['username']."</option>"; } while($rc=mysql_fetch_assoc($qc));
				     echo "</select>
					   </td>";
				
		  echo "<td><input name='search_date_range' class='from-control btn btn-success' type='submit' value='SEARCH DATE RANGE'></td>
				
			 </from>
			</tr>
			<tr align='center' class='w3-amber'>
			   <td>ROOM NO</td>
			   <td>DATE & TIME</td>
			   <td>AMOUNT</td>
			   <td colspan='2'>PARTICULARS</td>
			   <td>CHARGE BY</td>";
     if(isset($_REQUEST['search_date_range']))
	   {
          $start=$_REQUEST['start']." ".$_REQUEST['time_start'];
	      $end=$_REQUEST['end']." ".$_REQUEST['time_end'];
		  $cashier=$_REQUEST['cashier'];
		  
		  if($_REQUEST['cashier']=="ALL")
		    {  
		       $sxx="select * from room_charges where charge_date>='$start' and charge_date<='$end' order by charge_date desc";
			   $sxx1="select sum(amount) as atotal from room_charges where charge_date>='$start' and charge_date<='$end'";
			}
	   else {
		       $sxx="select * from room_charges where charge_date>='$start' and charge_date<='$end' and charge_by='$cashier' order by charge_date desc";
			   $sxx1="select sum(amount) as atotal from room_charges where charge_date>='$start' and charge_date<='$end' and charge_by='$cashier'";
	        } 
		  $qxx=mysql_query($sxx) or die(mysql_error());
		  $rxx=mysql_fetch_assoc($qxx);
		  
		  $qxx1=mysql_query($sxx1) or die(mysql_error());
		  $rxx1=mysql_fetch_assoc($qxx1);
		  
		  do{
			   echo "<tr>
			         <td align='center'><b class='w3-large'>".$rxx['room_no']."</b></td>
			         <td class='w3-small' align='center'>".date('l h:i A',strtotime($rxx['charge_date']))."<br>"
					                       .date('M d, Y',strtotime($rxx['charge_date']))."</td>
					 <td align='center'><b class='w3-text-red'>".number_format($rxx['amount'],2)."</b></td>
					 <td colspan='2' align='center'>".$rxx['charge_desc']."</td>
					 <td align='center'>".$rxx['charge_by']."</td>
					 </tr>";
			}while($rxx=mysql_fetch_assoc($qxx));     
       
	   echo "<tr class='w3-xlarge'>
		         <td colspan='6' align='center'>TOTAL: &nbsp;&nbsp;<b class='w3-text-red'>".number_format($rxx1['atotal'],2)."</b></td>
		     </tr>";		  
	   }
	  echo "</table>";
	  
	} 
//DATE RANGE AREA END -----
?>
    </div>
    <?php } ?>
  <!--Reports End-->

  <!---Settings Start--->
  <?php if(isset($_REQUEST['settings'])) { ?>   
	<div class="w3-col">
      <div class="w3-container w3-blue w3-padding-15">
        <div class="w3-left w3-xlarge"><i class="fa fa-gear w3-xlarge"></i>  Settings</div>
      </div>
     
      <br />
     <!--settings menu-->
	  <?php if($_REQUEST['level']==1){ ?>
      <a href="admin.php?settings=1&createuser=1&level=<?php echo $_REQUEST['level'];?>" class="w3-padding"><i class="fa fa-user fa-fw"></i> User Maintenance</a>
      <a href="admin.php?settings=1&createposition=1&level=<?php echo $_REQUEST['level'];?>" class="w3-padding" ><i class="fa fa-cubes fa-fw"></i> Position</a>
      <a href="admin.php?settings=1&updatecompany=1&level=<?php echo $_REQUEST['level'];?>" class="w3-padding" ><i class="fa fa-address-card fa-fw"></i> Company Details</a>
	  <a href="admin.php?settings=1&backupdatabase=1&level=<?php echo $_REQUEST['level'];?>" class="w3-padding" ><i class="fa fa-support fa-fw"></i> Backup Database</a>
      <?php } ?>
	  
	    <!--settings for user level-->
	  <?php if($_REQUEST['level']!=1){ ?>
	  <form method="get" action="script_user_resetanddisable_option.php">
	  <div class="col-xs-4">
	  <div class="w3-left w3-xlarge">Change Password</div><br/><br/>
	   
	  <?php if(isset($_REQUEST['passwordupdated'])){ ?><div style="color:#0066FF" class="w3-left w3-large">Update Password Success!</div><br/><?php } ?>
	  <?php if(isset($_REQUEST['passworderror'])){ ?><div style="color:#FF0000" class="w3-left w3-large">Password Error! (input not equal)</div><br/><?php } ?>
	  
	   <input name="level" type="hidden" value="<?php echo $_REQUEST['level'];?>">
	   <input name="settings" type="hidden" value="<?php echo $_REQUEST['settings'];?>">
	   <input required class="form-control" id="ex1" placeholder="new password" name="new_password" type="password" /><br/>
	   <input required class="form-control" id="ex1" placeholder="repeat new password" name="password_repeat" type="password" /><br/>
	   <input name="update_password" type="submit" class="btn btn-primary" value="Change Password Now!" onclick="return confirm('Are you sure?')">
	  </form>
      <?php } ?>
      
	  
	  <!--create user start for admin level-->
	  <?php 
	  if($_REQUEST['level']==1){
	  if(isset($_REQUEST['createuser'])) { ?>  
	  
	  <br><br>
	  <form method="get" action="script_user_resetanddisable_option.php">
	  <div class="col-xs-4">
	  <div class="w3-left w3-xlarge">Change admin Password</div><br/><br/>
	   
	  <?php if(isset($_REQUEST['passwordupdated'])){ ?><div style="color:#0066FF" class="w3-left w3-large">Update Password Success!</div><br/><?php } ?>
	  <?php if(isset($_REQUEST['passworderror'])){ ?><div style="color:#FF0000" class="w3-left w3-large">Password Error! (input not equal)</div><br/><?php } ?>
	  
	   <input name="level" type="hidden" value="<?php echo $_REQUEST['level'];?>">
	   <input name="settings" type="hidden" value="<?php echo $_REQUEST['settings'];?>">
	   <input required class="form-control" placeholder="new password" name="new_password" type="password" /><br/>
	   <input required class="form-control" placeholder="repeat new password" name="password_repeat" type="password" /><br/>
	   <input name="update_password" type="submit" class="btn btn-primary" value="Change Password Now!" onclick="return confirm('Are you sure?')">
	  </form>
	  
	  <form method="get" action="script_adduser.php">
	   <div class="col-xs-8"><br/>
	   <?php if(isset($_REQUEST['success'])){ ?><div style="color:#0066FF" class="w3-left w3-large">User Successfully Created!</div><?php } ?>
	   <div class="w3-left w3-xlarge">Create New User</div><br/>
	   <br/>
	   <input name="level" type="hidden" value="<?php echo $_REQUEST['level'];?>">
	   <input name="settings" type="hidden" value="<?php echo $_REQUEST['settings'];?>">
	   <input name="createuser" type="hidden" value="<?php echo $_REQUEST['createuser'];?>">
	   <input name="password" type="hidden" value="e10adc3949ba59abbe56e057f20f883e">
       <input required class="form-control" id="ex1" placeholder="username" name="new_user" type="text" /><br/>
	   <input required class="form-control" id="ex1" placeholder="first name" name="f_name" type="text" /><br/>
	   <input required class="form-control" id="ex1" placeholder="middle name" name="m_name" type="text" /><br/>
	   <input required class="form-control" id="ex1" placeholder="last name" name="l_name" type="text" /><br/>
	   
	   <div class="form-group">
       <label for="sel1">Position (select one):</label>
       <select required name="position" class="form-control" id="sel1">
	   <option></option>
       <?php
           $sq2="select * from user_positions" ;
		   $rlt2= mysql_query($sq2) or die(mysql_error());
		   $rw2=mysql_fetch_assoc($rlt2);
		   do { echo "<option value='".$rw2['position']."'>".$rw2['pos_description']."</option>"; } while ($rw2=mysql_fetch_assoc($rlt2));
        ?>
       </select>
       </div>
	
	   <div class="form-group">
       <label for="sel1">Access Level (select one):</label>
       <select required name="access_level" class="form-control" id="sel1">
	   <option></option>
        <?php
           $sql2="select * from user_access_level order by access_level desc" ;
		   $result2= mysql_query($sql2) or die(mysql_error());
		   $row2=mysql_fetch_assoc($result2);
		   do { echo "<option value='".$row2['access_level']."'>".$row2['access_level_desc']."</option>"; } while ($row2=mysql_fetch_assoc($result2));
        ?>
	   </select>
       </div>
	
       <input type="submit" class="btn btn-primary" value="Create User Now" onclick="return confirm('Are you sure?')">
       </div>
	  </form>
	  
   <div class="col-xs-4"><br/>
	  <div class="w3-left w3-xlarge">User list</div><br/><br/>
	  
	  <?php if(isset($_REQUEST['resetpass'])){ ?><div style="color:#0066FF" class="w3-left w3-large">Password Reset to 123456 Successfully!</div><?php } ?>
	  <?php if(isset($_REQUEST['disable'])){ ?><div style="color:#0066FF" class="w3-left w3-large">User Successfully Disabled!</div><?php } ?>
	  <?php if(isset($_REQUEST['enable'])){ ?><div style="color:#0066FF" class="w3-left w3-large">User Successfully Enabled!</div><?php } ?>
	  
	<!--User Options for admin Only-->  
    <?php if(isset($_REQUEST['useroption'])){ ?>
	<span class="label w3-large label-warning">user options for "<?php echo $_REQUEST['useroption'];?>"</span>
	</br></br>
	<form method="get" action="script_user_resetanddisable_option.php">
	<input name="level" type="hidden" value="<?php echo $_REQUEST['level'];?>">
	<input name="useroption" type="hidden" value="<?php echo $_REQUEST['useroption'];?>">
	<input name="resetpass" type="submit" class="btn btn-success" value="Reset Password" onclick="return confirm('Reset Password to 123456 -Are you sure?')">
	
	     <?php
            $useroption1=$_REQUEST['useroption'];		 
			$s12="select * from users where username='$useroption1' ";
	        $q12=mysql_query($s12) or die(mysql_error());
			$r12=mysql_fetch_assoc($q12);
          ?>		 
	<?php if($r12['access_level']!=0){ ?><input name="disableuser" type="submit" class="btn btn-danger" value="Disable User" onclick="return confirm('Are you sure?')"><?php } ?>
	<?php if($r12['access_level']==0){ ?><input name="enableuser" type="submit" class="btn btn-info" value="Enable User" onclick="return confirm('Are you sure?')"><?php } ?>
	</form><br/>
	<?php } ?>
 
 <div class="container">
  <table class="table table-hover">
    <thead>
      <tr>
        <th width=200>Username</th>
        <th width=200>Position</th>
        <th>Fullname</th>
      </tr>
	</thead> 
    <tbody>	
	  <?php $s1="select * from users"; 
	        $q1=mysql_query($s1) or die(mysql_error());
			$r1=mysql_fetch_assoc($q1);
			
			do{
			$position=$r1['position'];
			$s2="select * from user_positions where position=$position"; 
	        $q2=mysql_query($s2) or die(mysql_error());
			$r2=mysql_fetch_assoc($q2);
				
				echo "<tr>
			           <td><a href='admin.php?settings=1&createuser=1&level=".$_REQUEST['level']."&useroption=".$r1['username']."'>".$r1['username']."</a></td>
					   <td>";
				if($r1['access_level']==0){ echo "<span style='color:#FF0000'>Disabled</span>"; }
		      	else{echo $r2['pos_description'];}
				echo "</td><td>".$r1['first_name']." ".$r1['middle_name']." ".$r1['last_name']."</td>
				</tr>";
			 }while($r1=mysql_fetch_assoc($q1));?>
	</tbody>
  </table>
 </div>
</div>
	  
	  <?php } } ?>
      <!--create user end-->	

      <!--create position start-->
	  <?php if(isset($_REQUEST['createposition'])) { ?>  
	  <form method="get" action="script_create_position.php">
	   <div class="col-xs-4"><br/><div class="w3-left w3-xlarge">Create New Position</div><br/>
	   <?php if(isset($_REQUEST['possuccess'])){ ?><div style="color:#0066FF" class="w3-left w3-large">Position Successfully Created!</div><?php } ?>
	   <br/>
	   <input name="level" type="hidden" value="<?php echo $_REQUEST['level'];?>">
	   <input name="settings" type="hidden" value="<?php echo $_REQUEST['settings'];?>">
       <input required class="form-control" id="ex1" placeholder="position" name="new_position" type="text" /><br/>
	   <input type="submit" class="btn btn-primary" value="Create Position Now" onclick="return confirm('Are you sure?')">
       </div>
	  </form>

<div class="col-xs-4"><br/>
<div class="w3-left w3-xlarge">Position List</div><br/><br/>	  
<div class="container">
  <table class="table table-hover">
    <thead>
      <tr>
        <th width=200>Position Code</th>
        <th width=200>Description</th>
		<th></th>
      </tr>
	</thead> 
    <tbody>	
	  <?php $s1="select * from user_positions"; 
	        $q1=mysql_query($s1) or die(mysql_error());
			$r1=mysql_fetch_assoc($q1);
			do{ echo "<tr><td>".$r1['position']."</td><td>".$r1['pos_description']."</td></tr>";}while($r1=mysql_fetch_assoc($q1));?>
	</tbody>
  </table>
 </div>
</div>

	  <?php } ?>
      <!--create position end-->	  
	 
	  <!--update company start-->
	  <?php if(isset($_REQUEST['updatecompany'])) {
	  $ss1="select * from company";
      $qq1=mysql_query($ss1) or die(mysql_error());
      $rr1=mysql_fetch_assoc($qq1);
 	  ?><br/><br/>
	  <div class="col-xs-4">
	  <!--<img src="img/yourlogo.jpg" />-->
	   </br></br>
	   <div class="w3-xlarge"><?php echo $rr1['company_name']; ?></div>
	   <?php echo $rr1['company_address']; ?><br/>
	   <?php echo $rr1['company_tin']; ?><br/>
	   <?php echo $rr1['company_email']; ?><br/>
	   <?php echo $rr1['company_tel']; ?><br/>
	   <?php echo $rr1['company_mobile']; ?>
	   </div>
      <?php } ?>
      <!--update company end-->	  
	  
	  <!--backup database start-->
	  <?php if(isset($_REQUEST['backupdatabase'])) { ?><br/><br/>
	  
	  <form method="get" action="script_database_backup.php">
	  <div class="col-xs-4">
	  <div class="w3-left w3-xlarge">Backup Database</div><br/><br/>
	  <input name="level" type="hidden" value="<?php echo $_REQUEST['level'];?>">
	  <input name="settings" type="hidden" value="<?php echo $_REQUEST['settings'];?>">
	  <input name="backupdatabase" type="submit" class="btn btn-primary" value="Create Backup Now!" onclick="return confirm('Are you sure?')">
	  </form>
	  
	  
      <?php } ?>
      <!--backup database end-->	  
	 
	 
	</div>
    <?php } ?> 
<!---Settings End--->	
		
  </div>

</div>
</body>
</html>

<script>
// Get the Sidenav
var mySidenav = document.getElementById("mySidenav");

// Get the DIV with overlay effect
var overlayBg = document.getElementById("myOverlay");

// Toggle between showing and hiding the sidenav, and add overlay effect
function w3_open() {
    if (mySidenav.style.display === 'block') {
        mySidenav.style.display = 'none';
        overlayBg.style.display = "none";
    } else {
        mySidenav.style.display = 'block';
        overlayBg.style.display = "block";
    }
}

// Close the sidenav with the close button
function w3_close() {
    mySidenav.style.display = "none";
    overlayBg.style.display = "none";
}
</script>  
