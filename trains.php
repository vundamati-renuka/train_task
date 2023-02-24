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
if( $_GET['keyword'] ){
	$condition .= " and ( 
	train_name like '%" . $_GET['keyword'] . "%' 
	or source_station like  '%" . $_GET['keyword'] . "%' 
	or dest_station like '%" . $_GET['keyword'] . "%' ) ";
}
if( $_GET['station'] != "all" && $_GET['station'] != "" ){
	$condition .= " and ( source_station = '" . $_GET['station'] . "' 
	or dest_station = '" . $_GET['station'] . "' ) ";
}

$query = "select count(*) as cnt from trains ". $condition;
$result = mysqli_query($con, $query);  
if( mysqli_error( $con ) ){
	echo "1" . mysqli_error( $con );
	echo "<div>". $query . "</div>";
	exit;
}
$row = mysqli_fetch_assoc($result);
$total_records = $row['cnt'];
$number_of_page = ceil ($total_records / $results_per_page); 


$query = "select * from trains ".$condition." order by train_code LIMIT " . $start_index . ',' . $results_per_page;  
// echo "<div>". $query . "</div>";
$result = mysqli_query($con, $query);  
if( mysqli_error( $con ) ){
	// echo "2" . mysqli_error( $con );
	exit;
}

function repl($m){
	return "<span class='hl' >".$m[0]."</span>";
}
function highlight_keyword($value){
	if( $_GET['station'] != "" && $_GET['station'] != "all" && $_GET['keyword'] ){
		return preg_replace_callback("/(".$_GET['station']."|".$_GET['keyword'] .")/i","repl",htmlspecialchars($value));
	}else if( $_GET['keyword'] ){
		return preg_replace_callback("/".$_GET['keyword']."/i","repl",htmlspecialchars($value));
	}else if( $_GET['station'] != "" && $_GET['station'] != "all" ){
		return preg_replace_callback("/".$_GET['station']."/i","repl",htmlspecialchars($value));
	}else{
		return $value;
	}
}


?>

	
	<form method="GET" >
	<div class="card">
	  <div class="card-body">
	    Search: <select name="station" >
	    	<option value="all" >All Stations</option>
	    	<?php 
	    	$res2 = mysqli_query( $con, "select * from stations where station_name != '' order by station_name  ");
	    	while( $row2 = mysqli_fetch_assoc($res2) ){
	    		echo "<option ".($_GET['station']==$row2['station_name']?"selected":"")." value='" . $row2['station_name'] . "' >" . $row2['station_name'] . "</option>";
	    	}
	    	?>
	    </select> <input type="text" name="keyword" value="<?=$_GET['keyword'] ?>" placeholder="keyword"><input type="submit" value="Search" > 
	  </div>
	</div>
	</form>

	<table width="100%"><tr><td>
		<?php if( $page > 1 ){ ?>
		<a class="btn btn-outline-primary  btn-sm" href="?station=<?=urlencode($_GET['station']) ?>&keyword=<?=urlencode($_GET['keyword']) ?>&page=1" >First</a>
		<?php } ?>
		<?php $cnt = 1; 
		for($i=($page>5?$page-5:$page);$i<=$number_of_page&&$cnt<10;$i++){ ?>
		<a class="btn btn-outline-<?=$i==$page?"info":"primary" ?> btn-sm" href="?station=<?=urlencode($_GET['station']) ?>&keyword=<?=urlencode($_GET['keyword']) ?>&page=<?=$i ?>" ><?=$i ?></a>
		<?php 
		$cnt++;
		} 
		?>
		<?php if( $page < $number_of_page ){ ?>
		<a class="btn btn-outline-primary btn-sm" href="?station=<?=urlencode($_GET['station']) ?>&keyword=<?=urlencode($_GET['keyword']) ?>&page=<?=$page+1 ?>" >Next</a>
		<a class="btn btn-outline-primary btn-sm" href="?station=<?=urlencode($_GET['station']) ?>&keyword=<?=urlencode($_GET['keyword']) ?>&page=<?=$number_of_page ?>" >Last</a>
		<?php } ?>
	</td><td align="right">
		Displaying: <?=$start_index+1 ?> to <?=$page==$number_of_page?$total_records:($start_index+$results_per_page) ?> of <?=$total_records ?>
	</td></tr></table>

	<style>
		.hl{color: orange;}
	</style>


	<table class="table table-bordered table-sm">
		<tr>
			<td>TrainID</td>
			<td>Train</td>
			<td>Source</td>
			<td>Destination</td>
			<td>Action</td>
		</tr>

<?php
	while ($row = mysqli_fetch_array($result)) {  
?>
		<tr>
			<td><?=htmlspecialchars($row['train_code']) ?></td>
			<td><?=highlight_keyword($row['station_name']) ?></td>
			<td><?=htmlspecialchars($row['source_station_code']) ?> - <?=highlight_keyword($row['source_station']) ?></td>
			<td><?=htmlspecialchars($row['dest_station_code']) ?> - <?=highlight_keyword($row['dest_station']) ?></td>
			<td> </td>
		</tr> 
<?php } ?>
	</table>
 
	</body>
</html>