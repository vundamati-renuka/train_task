<?php

include("config.php");

include("header.php");


/*
insert into stations 
SELECT '' as id, source_station_code as station_code, source_station as station_name
FROM `trains` 
WHERE source_station_code!='' 
group by source_station_code;
*/

?>

	
	<form method="GET" >
	<div class="card">
	  <div class="card-body">
	   From: <select name="source_station" >
	    	<option value="all" >Source Station</option>
	    	<?php 
	    	$res2 = mysqli_query( $con, "select station_code,station_name from trains_time_table where station_name != '' group by station_name  ");
	    	while( $row2 = mysqli_fetch_assoc($res2) ){
	    		echo "<option ".($_GET['source_station']==$row2['station_code']?"selected":"")." value='" . $row2['station_code'] . "' >" . $row2['station_name'] . "</option>";
	    	}
	    	?>
    	
	    </select> 
	    To: <select name="destination_station" >
	    	<option value="all" >Destination station</option>
	    	<?php 
	    	mysqli_data_seek($res2,0);
	    	while( $row2 = mysqli_fetch_assoc($res2) ){
	    		echo "<option ".($_GET['destination_station']==$row2['station_code']?"selected":"")." value='" . $row2['station_code'] . "' >" . $row2['station_name'] . "</option>";
	    	}
	    	?>
    	
	    </select> 

	   <input type="submit" value="search" > 
	  </div>
	</div>
	</form>

<?php

if( $_GET['source_station'] && $_GET['destination_station'] ){

	$query = "select train_no,train_name from trains_time_table where station_code ='" . $_GET['source_station'] . "' and source_station = '".$_GET['source_station']."' ";
	$result = mysqli_query($con, $query);   
	if( mysqli_error( $con ) ){
		echo "1" . mysqli_error( $con );
		echo "<div>". $query . "</div>";
		exit;
	}
	$trains = [];
	while( $row = mysqli_fetch_assoc($result) ){

	//	echo "<div>" . $row['train_no'] . ":"  .  $row['train_name'] . ":"  .  $row['source_station'] . ":"  .  $row['destination_station_name'] . "</div>";

		$query = "select * from trains_time_table where train_no = '" . $row['train_no'] . "' and destination_station ='" . $_GET['destination_station'] . "' ";
		//echo "<div>". $query."</div>";
		$res2 = mysqli_query($con, $query);  
		if( mysqli_error( $con ) ){
			echo "1" . mysqli_error( $con );
			echo "<div>". $query . "</div>";
			exit;
		}
		$schedule = mysqli_fetch_assoc($res2);
		if( $schedule ){
			$trains[] = $schedule;
		}

	}

	?>


    <table class="table table-bordered table-sm">
        <tr>
        	<td>Train</td>
            <td>Source</td>
            <td>Destination</td>
            <td>Arrival</td>
            <td>Departure</td>
        </tr>

   <?php
    foreach( $trains as $i=>$row ){
        ?>
        <tr>
        	<td><?=htmlspecialchars($row['train_no'] . ": " . $row['train_name']) ?></td>
            <td><?=($row['source_station'] . ": " . $row['source_station_name']) ?></td>
            <td><?=($row['destination_station'] . ": " . $row['destination_station_name']) ?></td>
            <td><?=($row['arrival_time']) ?></td>
            <td><?=($row['departure_time']) ?></td>
        </tr>
        <?php } ?>
    </table>
 
 <?php } ?>

</body>
</html>