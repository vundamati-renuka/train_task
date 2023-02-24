<?php

include("config.php");

include("header.php");

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
//$des=mysqli_query( $con, "select destination_station_name from trains_time_table ");

mysqli_data_seek($res2,0);
while( $row2 = mysqli_fetch_assoc($res2) ){
echo "<option ".($_GET['destination_station']==$row2['station_code']?"selected":"")." value='" . $row2['station_code'] . "' >" . $row2['station_name'] . "</option>";
}

?>

</select> 
Sort By: <select name="sort_by">
<option value="arrival_time" selected>Arrival</option>
<option value="departure_time">Departure</option>
</select>	  	
<input type="submit" value="search" > 
</div>
</div>
<?php

if( $_GET['source_station'] && $_GET['destination_station']){

$query = "select * from trains_time_table where station_code ='" . $_GET['destination_station'] . "'";
//echo $query;
$result = mysqli_query($con, $query);  
if( mysqli_error( $con ) ){
echo "1" . mysqli_error( $con );
echo "<div>". $query . "</div>";
exit;
}
$trains = [];
while( $row = mysqli_fetch_assoc($result) ){
//echo "<div>" . $row['train_no'] . ":"  .  $row['train_name'] . ":"  .  $row['source_station'] . ":"  .  $row['destination_station_name'] . "</div>";
$query = "select * from trains_time_table where train_no = '" . $row['train_no'] . "' and station_code='" . $_GET['source_station'] . "' and seq <".$row['seq'];
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

/*echo "<pre>";
print_r($trains);
echo "</pre>";*/

?>
<table class="table table-bordered table-sm table-hover table-striped">
<thead>
<tr>
<th>Train</th>
<th>Train Source</th>
<th>From </th>
<th>To </th>
<th>Train Destination</th>
<th>Arrival</th>
<th>Departure</th>
</tr>
</thead>

<?php
$trainArr1=[];
function display($trainArr1){
//echo $trainArr1[0]['train_no'];
foreach( $trainArr1 as $j=>$row3 ){
?>
<td><?=htmlspecialchars($row3['train_no'] . ": " . $row3['train_name']) ?></td>
<td><?=$row3['source_station_name'] ?></td>
<td><?=$row3['station_name']?></td>
<td><?= $_GET['destination_station'] ?></td>
<td><?=($row3['destination_station'] . ": " . $row3['destination_station_name']) ?></td>
<td><?=($row3['arrival_time']) ?></td>
<td><?=($row3['departure_time']) ?></td>
</tr>	    
<?php 
}
}
if($_GET['sort_by']){
$array=$trains;
$sorter =[];
$ret =[];
$key=$_GET['sort_by'];
reset($array);
foreach($array as $ii => $va) {
$sorter[$ii] = $va[$key];
}
asort($sorter);
foreach($sorter as $ii => $va) {
$ret[$ii] = $array[$ii];
}
$array = $ret;
display($array);
}
else{
display($trains);
}
?>
</table>

<?php } ?>

</body>
</html>