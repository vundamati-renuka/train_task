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

if (!isset ($_GET['page']) ) {  
	$page = 1;  
} else {  
	$page = $_GET['page'];  
}    
if( is_numeric($page) == false || !$page ){
	$page = 1;
}
$results_per_page = 10;  
$start_index = ($page-1) * $results_per_page; 

$condition = " where 1=1 ";


if( $_GET['destination_station'] && $_GET['destination_station'] != "all" &&  $_GET['source_station'] && $_GET['source_station'] != "all" ){
	$condition .= " and (  
	destination_station_name ='" . $_GET['destination_station'] . "')
	and (source_station_name ='" . $_GET['source_station'] . "') ";
}else if( $_GET['source_station'] && $_GET['source_station'] != "all" ){
	$condition .= " and (  
	source_station_name ='" . $_GET['source_station'] . "') ";
}else if( $_GET['destination_station'] && $_GET['destination_station'] != "all"  ){
	$condition .= " and (  
	destination_station_name ='" . $_GET['destination_station'] . "') ";
}


$query = "select count(*) as cnt from trains_time_table ". $condition;

$result = mysqli_query($con, $query);  
if( mysqli_error( $con ) ){
	echo "1" . mysqli_error( $con );
	echo $query;
	exit;
}
$row = mysqli_fetch_assoc($result);
$total_records = $row['cnt'];
$number_of_page = ceil ($total_records / $results_per_page); 


$query = "select * from trains_time_table ".$condition." order by train_no LIMIT " . $start_index . ',' . $results_per_page; 
$result = mysqli_query($con, $query);  
if( mysqli_error( $con ) ){
	echo "2" . mysqli_error( $con );
	echo $query;
	exit;
}

function repl($m){
	return "<span class='hl' >".$m[0]."</span>";
}
function highlight_keyword($value){
	if( $_GET['source_station'] != "" && $_GET['source_station'] != "all" && $_GET['destination_station'] != "" && $_GET['destination_station'] != "all"){
		return preg_replace_callback("/(".$_GET['source_station']."|".$_GET['destination_station'] .")/i","repl",htmlspecialchars($value));
	}else if( $_GET['destination_station'] != "" && $_GET['destination_station'] != "all" ){
		return preg_replace_callback("/".$_GET['destination_station']."/i","repl",htmlspecialchars($value));
	}else if( $_GET['source_station'] != "" && $_GET['source_station'] != "all" ){
		return preg_replace_callback("/".$_GET['source_station']."/i","repl",htmlspecialchars($value));
	}else{
		return $value;
	}
}


?>

	
	<form method="GET" >
	<div class="card">
	  <div class="card-body" style="float:left">
	    <div style="float: left;width: 35%;">
	    From: <select name="source_station" class="form-select" style="width:90%">
	    	<option value="all" >Source Station</option>
	    	<?php 
	    	$res2 = mysqli_query( $con, "select distinct(source_station_name) from trains_time_table where train_name != '' order by train_name  ");
	    	while( $row2 = mysqli_fetch_assoc($res2) ){
	    		echo "<option ".($_GET['source_station']==$row2['source_station_name']?"selected":"")." value='" . $row2['source_station_name'] . "' >" . $row2['source_station_name'] . "</option>";
	    	}
	    	?>
	    </select>
		</div>
	    <div style="float:left;width:35%">
	    To: <select name="destination_station" class="form-select" style="width:100%">
	    	<option value="all" >Destination Station</option>
	    	<?php 
	    	$res2 = mysqli_query( $con, "select distinct(destination_station_name) from trains_time_table where train_name != '' order by train_name  ");
	    	while( $row2 = mysqli_fetch_assoc($res2) ){
	    		echo "<option ".($_GET['destination_station']==$row2['destination_station_name']?"selected":"")." value='" . $row2['destination_station_name'] . "' >" . $row2['destination_station_name'] . "</option>";
	    	}
	    	?>
	    </select>
	    </div>
	    <div style="float:left">
	    	<input class="btn btn-outline-info" style="margin: 22px;" value="Fetch Details" type="submit">	    		
	    </div>
	  </div>
	</div>
	</form>

	<table width="100%" style="margin-bottom: 20px;"><tr><td>
		<?php if( $page > 1 ){ ?>
			<a class="btn btn-outline-primary  btn-sm" href="?source_station=<?=urlencode($_GET['source_station']) ?>&destination_station=<?=urlencode($_GET['destination_station']) ?>&page=1" >First</a>
		<?php } ?>
		<?php $cnt = 1; 
			for($i=($page>5?$page-5:$page);$i<=$number_of_page&&$cnt<10;$i++){ ?>
				<a class="btn btn-outline-<?=$i==$page?"info":"primary" ?> btn-sm" href="?source_station=<?=urlencode($_GET['source_station']) ?>&destination_station=<?=urlencode($_GET['destination_station']) ?>&page=<?=$i ?>" ><?=$i ?></a>
		<?php 
				$cnt++;
		} 
		?>
		<?php if( $page < $number_of_page ){ ?>
		<a class="btn btn-outline-primary btn-sm" href="?source_station=<?=urlencode($_GET['source_station']) ?>&destination_station=<?=urlencode($_GET['destination_station']) ?>&page=<?=$page+1 ?>" >Next</a>
		<a class="btn btn-outline-primary btn-sm" href="?source_station=<?=urlencode($_GET['source_station']) ?>&destination_station=<?=urlencode($_GET['destination_station']) ?>&page=<?=$number_of_page ?>" >Last</a>
		<?php } ?>
	</td><td align="right">
		Displaying: <?=$start_index+1 ?> to <?=$page==$number_of_page?$total_records:($start_index+$results_per_page) ?> of <?=$total_records ?>
	</td></tr></table>

	<style>
		.hl{color: orange;}
	</style>


    <div class="table-div" style="overflow: hidden;border-radius: 25px;">
    <table class="table table-bordered table-sm table-hover table-dark" style="text-align: center;">
    	<thead>
	        <tr>
	        	<th>Train Id</th>
	            <th>Train</th>
	            <th>Source</th>
	            <th>Destination</th>
	            <th>Departure Time</th>
	        </tr>
    	</thead>

   <?php
    while ($row = mysqli_fetch_array($result)) {  
        ?>
        <tr>
        	<td><?=htmlspecialchars($row['train_no']) ?></td>
            <td><?=highlight_keyword($row['train_name']) ?></td>
            <td><?=highlight_keyword($row['source_station_name']) ?></td>
            <td><?=highlight_keyword($row['destination_station_name']) ?></td>
            <td><?=htmlspecialchars($row['departure_time']) ?></td>
        </tr> 
        <?php } ?>
      <tfoot>
      	<tr>
      		<td colspan="5"></td>
      	</tr>
      </tfoot>
    </table>
	</div>
 
</body>
</html>