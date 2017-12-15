<?php
	include "connect_db.php";
	$lang = $_SESSION['lang'];
	if(isset($_GET['lang'])){
	 	$_SESSION['lang'] = $_GET['lang']; //เก็บค่าของภาษาไว้ใน SESSION
	   	if($_SESSION['lang'] == "en"){
	     	include "lang_en.php";
	   	}
	   	else{
	     	include "lang_th.php";
	   	}
	}
	else if ($_SESSION['lang'] == 'en') {
	 	include "lang_en.php";
	}
	else{
	 	include "lang_th.php";
	}
	$sort_by = $_POST['sort_by'];
	$group_by = $_POST['group_by'];
	

	$header_group = '';

	if($_POST['group_by'] == 'group_book'){
		if($_POST['sort_by'] == 'sort_date'){
			$header_group = $lang_sort_by1;
			$month_start = $_POST['month_start'];
			$month_end = $_POST['month_end'];
			$sql_1 = "SELECT re_sc_station_id,DATE_FORMAT(re_sc_date,'%Y-%m-%d') AS re_sc_date,COUNT(re_sc_type) AS re_sc_type ,
					SUM(CASE WHEN re_sc_type = 'BORROW' THEN 1 ELSE 0 END) AS borrow_1 ,
					SUM(CASE WHEN re_sc_type = 'RETURN' THEN 1 ELSE 0 END) AS return_1 ,
					SUM(CASE WHEN re_sc_type = 'RENEW' THEN 1 ELSE 0 END) AS renew_1 
					FROM report_selfcheck 
					WHERE DATE_FORMAT(re_sc_date,'%Y-%m') BETWEEN '".$_POST['month_start']."' AND '".$_POST['month_end']."' 
					GROUP BY re_sc_station_id,DATE_FORMAT(re_sc_date,'%Y-%m-%d') ORDER BY re_sc_date ASC";
		}elseif ($_POST['sort_by'] == 'sort_month') {
			$header_group = $lang_sort_by2;
			$month_start = $_POST['month_start'];
			$month_end = $_POST['month_end'];
			$sql_1 = "SELECT re_sc_station_id,MONTHNAME(re_sc_date) AS re_sc_date,COUNT(re_sc_type) AS re_sc_type ,
					SUM(CASE WHEN re_sc_type = 'BORROW' THEN 1 ELSE 0 END) AS borrow_1 ,
					SUM(CASE WHEN re_sc_type = 'RETURN' THEN 1 ELSE 0 END) AS return_1 ,
					SUM(CASE WHEN re_sc_type = 'RENEW' THEN 1 ELSE 0 END) AS renew_1 
					FROM report_selfcheck 
					WHERE DATE_FORMAT(re_sc_date,'%Y-%m') BETWEEN '".$_POST['month_start']."' AND '".$_POST['month_end']."' 
					GROUP BY re_sc_station_id,MONTHNAME(re_sc_date) ORDER BY re_sc_date ASC";
		}else{
			$month_start = '';
			$month_end = '';
			$header_group = $lang_sort_by3;
			$sql_1 = "SELECT re_sc_station_id,YEAR(re_sc_date) AS re_sc_date,COUNT(re_sc_type) AS re_sc_type ,
					SUM(CASE WHEN re_sc_type = 'BORROW' THEN 1 ELSE 0 END) AS borrow_1 ,
					SUM(CASE WHEN re_sc_type = 'RETURN' THEN 1 ELSE 0 END) AS return_1 ,
					SUM(CASE WHEN re_sc_type = 'RENEW' THEN 1 ELSE 0 END) AS renew_1 
					FROM report_selfcheck GROUP BY re_sc_station_id,YEAR(re_sc_date) ORDER BY re_sc_date ASC";
		}
	}else{
		if($_POST['sort_by'] == 'sort_date'){
			$header_group = $lang_sort_by1;
			$month_start = $_POST['month_start'];
			$month_end = $_POST['month_end'];
			$sql_1 = "SELECT hardware_id, COUNT(no) AS no , DATE_FORMAT(login_datetime,'%Y-%m-%d') AS login_datetime
					FROM log_login 
					WHERE (DATE_FORMAT(login_datetime,'%Y-%m') BETWEEN '".$_POST['month_start']."' AND '".$_POST['month_end']."') AND hardware_type = 'SC' 
					GROUP BY hardware_id,DATE_FORMAT(login_datetime,'%Y-%m-%d') ORDER BY login_datetime ASC";
		}elseif ($_POST['sort_by'] == 'sort_month') {
			$header_group = $lang_sort_by2;
			$month_start = $_POST['month_start'];
			$month_end = $_POST['month_end'];
			$sql_1 = "SELECT hardware_id,COUNT(no) AS no , MONTHNAME(login_datetime) AS login_datetime
					FROM log_login 
					WHERE (DATE_FORMAT(login_datetime,'%Y-%m') BETWEEN '".$_POST['month_start']."' AND '".$_POST['month_end']."') AND hardware_type = 'SC' 
					GROUP BY hardware_id,MONTHNAME(login_datetime) ORDER BY login_datetime ASC";
		}else{
			$month_start = '';
			$month_end = '';
			$header_group = $lang_sort_by3;
			$sql_1 = "SELECT hardware_id,COUNT(no) AS no , YEAR(login_datetime) AS login_datetime
					FROM log_login GROUP BY hardware_id,YEAR(login_datetime) ORDER BY login_datetime ASC";
		}
		
	}
	$query_1 = mysqli_query($conn,$sql_1);
	$res_1 = mysqli_fetch_all($query_1,MYSQLI_BOTH);

	$num_pdf = 0;
	foreach ($res_1 as $key => $value_sc) {
		$num_pdf++;
	}
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="AdminLTE-2.3.0/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="AdminLTE-2.3.0/plugins/datatables/dataTables.bootstrap.css">
	<link rel="stylesheet" href="AdminLTE-2.3.0/dist/css/AdminLTE.min.css">
	<link rel="stylesheet" href="AdminLTE-2.3.0/dist/css/skins/_all-skins.min.css">

	<script src="AdminLTE-2.3.0/plugins/jQuery/jQuery-2.1.4.min.js"></script>
	<script src="AdminLTE-2.3.0/bootstrap/js/bootstrap.min.js"></script>
	<script src="AdminLTE-2.3.0/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="AdminLTE-2.3.0/plugins/datatables/dataTables.bootstrap.min.js"></script>
</head>
<body>
	<div class="row">
		<!--<div class="col-lg-12" align="right">
			<?=$lang_download; ?> : &nbsp;
			<a href="report_sc_static_1_to_word.php?sort_by=<?=$sort_by;?>&group_by=<?=$group_by;?>&month_start=<?=$month_start;?>&month_end=<?=$month_end;?>"><img src="img/word-icon.png" style="width:30px"></a>&nbsp;
			<a href="report_sc_static_1_to_excel.php?sort_by=<?=$sort_by;?>&group_by=<?=$group_by;?>&month_start=<?=$month_start;?>&month_end=<?=$month_end;?>"><img src="img/excel-icon.png" style="width:30px"></a>&nbsp;
			<?php if($num_pdf >= '400'){ ?>
				<img src="img/pdf-icon.png" style="width:30px">
				<br><font color="red"><?=$lang_remark_1; ?></font>

			<?php }else{ ?>
				<a href="#" onClick="javascript:window.open('report_sc_static_1_to_pdf.php?sort_by=<?=$sort_by;?>&group_by=<?=$group_by;?>&month_start=<?=$month_start;?>&month_end=<?=$month_end;?>')"><img src="img/pdf-icon.png" style="width:30px"></a>
			<?php } ?>
		</div>-->
		<div class="col-md-12">
			<table class="table table-bordered table-hover" id="example">
		    	<?php if($_POST['group_by'] == 'group_book'){ ?>
			    	<thead>
			    		<tr>
			    			<th><?=$lang_station_id; ?></th>
			    			<th><?=$header_group; ?></th>
			    			<th><?=$lang_borrow; ?></th>
			    			<th><?=$lang_return; ?></th>
			    			<th><?=$lang_renew; ?></th>
			    			<th><?=$lang_total; ?></th>
			    		</tr>
			    	</thead>
			    	<tbody>
			    		<?php
			    			$num = 0;
			    			foreach ($res_1 as $key => $value_sc) {
			    				$num++;
			    		?>
			    		<tr>
			    			<td><?php echo $value_sc['re_sc_station_id']; ?></td>
			    			<td><?php echo $value_sc['re_sc_date']; ?></td>
			    			<td><?php echo $value_sc['borrow_1']; ?></td>
			    			<td><?php echo $value_sc['return_1']; ?></td>
			    			<td><?php echo $value_sc['renew_1']; ?></td>
			    			<td><?php echo $value_sc['re_sc_type']; ?></td>
			    		</tr>
			    		<?php } ?>
			    	</tbody>
			    	<tfoot style="background-color: #F5F5F5;">
			            <tr>
			                <th style="text-align:right"><?=$lang_total; ?> : </th>
			                <th></th>
			                <th></th>
			                <th></th>
			                <th></th>
			                <th></th>
			            </tr>
			        </tfoot>

		        <?php }else{ ?>
		        	<thead>
			    		<tr>
			    			<th><?=$lang_station_id; ?></th>
			    			<th><?=$header_group; ?></th>
			    			<th><?=$lang_total; ?></th>
			    		</tr>
			    	</thead>
			    	<tbody>
			    		<?php
			    			$num = 0;
			    			foreach ($res_1 as $key => $value_sc) {
			    				$num++;
			    		?>
			    		<tr>
			    			<td><?php echo "SCT1000".$value_sc['hardware_id']; ?></td>
			    			<td><?php echo $value_sc['login_datetime']; ?></td>
			    			<td><?php echo $value_sc['no']; ?></td>
			    		</tr>
			    		<?php } ?>
			    	</tbody>
			    	<tfoot style="background-color: #F5F5F5;">
			            <tr>
			                <th style="text-align:right"><?=$lang_total; ?> : </th>
			                <th></th>
			                <th></th>
			            </tr>
			        </tfoot>
		        <?php } ?>
		    </table>
		</div>
		<div class="overlay" id="loading">
		        <i class="fa fa-refresh fa-spin"></i>
		</div>
	</div>
</body>
</html>
<script type="text/javascript">
	$(window).load(function(){
		$("#loading").hide();
	})

$(document).ready(function() {
    var table = $('#example').DataTable({
    	responsive: true,
	    "searching" : false,
	    "pageLength": 10,
	    "lengthChange": false
    });
    for(var num_col=1;num_col<6;num_col++){
		var column = table.column( num_col );

		var intVal = function ( i ) {
		    			return typeof i === 'string' ?i*1 : typeof i === 'number' ?i : 0;
		    		};
		 
		$( column.footer() ).html(
		    column.data().reduce( function (a,b) {
		        return intVal(a) + intVal(b);
		    } )
		);
	}
} );
</script>
