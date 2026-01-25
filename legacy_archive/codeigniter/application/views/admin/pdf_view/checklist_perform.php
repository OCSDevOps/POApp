<html>
	
	<head>
	<style>
		body {
			font-family: 'Calibri'
		}
	
		.border {
			background-color: white;
			width: 19cm;
			margin-left: 1cm;
			margin-top: 1cm;
			margin-bottom: 1cm;
			border-color: teal;
			border-width: 1px;
			border-style: solid;
		}
	
		.parent-border {
			background-color: white;
			width: 21cm;
			height: auto;
			border-color: teal;
			border-width: 1px;
			border-style: solid;
		}
	
		.table-row-border>th {
			border-left-color: white;
			border-right-color: white;
			border-bottom-color: teal;
			border-top-color: teal;
			border-width: 2px;
			border-style: solid;
			margin: 0;
			padding: 0;
			width: 4cm;
			height: 1cm;
			text-align: center;
			color: teal;
		}
	
		td {
			height: 1cm;
			text-align: center;
		}
	
		.table-addresses>th {
			color: teal;
			width: 4cm;
			text-align: left;
		}
	
	
		.parent {
			position: relative;
		}
	
		.child {
			position: absolute;
		}
	
		.parent-invoice-logo-type {
			height: 0cm;
			/* border: teal 1px solid; */
		}
	
		.invoice-type {
			font-size: 30px;
			font-weight: 700;
			color: black;
			right: 0.0cm;
			text-align:right;
			bottom:0cm;
		}
	
		.invoice-address {
			font-size: 15px;
			font-weight:400;
			color: black;
			left: 0.0cm;
			text-align:left;
			top:1.1cm;
		}
	
		.invoice-project {
			font-size: 15px;
			font-weight:400;
			color: black;
			right: 0cm;
			text-align:right;
			top:1.1cm;
		}
	
		.invoice-heading {
			font-size: 30px;
			font-weight:700;
			color: black;
			text-align:center;
			top:0;
			width:100%;
			border-bottom: solid 2px;
			border-top: solid 2px;
	
		}
	
		.invoice-other {
			font-size: 15px;
			color: black;
			border-bottom: 1px solid #d2d6d3;
			padding-top:20px;
			padding-bottom:5px;
		}
	
		.invoice-main {
			padding-top:120px;
		}
	
		.other-child1 {
			display: inline-block;
			width:15%;
			font-weight:bold;
		}
		.other-child2 {
			display: inline-block;
			width:32%;
		}
		.invoice-logo {
			left: 0.0cm;
			bottom: 0cm;
		}
	
	
		table, th, td {
			border: 1px solid black;
			border-collapse: collapse;
		}
		.table-heading{
			text-align:left;
		}
	</style>
	</head>
	
	<body>
	<div>
	
		<div>
			<div class="parent parent-invoice-logo-type">
	<!--			<img class="invoice-logo child" src="PoCss/logo.png" alt="" width="175" height="100"/>-->
	<!--			<span class="invoice-type child">-->
	<!--                   -->
	<!--                </span>-->
	
			</div>
		</div>
		<div>
			<div class="parent">
					<span class="invoice-address child">
						Essence Properties Inc.
						<br>
						3950 191 St Unit 118
						<br>
						Surrey, British Columbia V3Z 0Y6
						<br>
						Phone: (778) 574-0777
					</span>
			</div>
		</div>
		<div>
			<div class="parent" >
                <span class="invoice-heading child">
                        Equipment Checklist Performed- <?php echo $checklist->cl_name; ?>
                </span>
			</div>
		</div>
		<div class="invoice-main">
			<div class="invoice-other">
				<div class="other-child1">
				    PERFORMED BY:
				</div>
				<div class="other-child2">
					<?php 
						echo $checklist_user->firstname.' '.$checklist_user->lastname.'<br>'; 
					?>
				</div>
				<div class="other-child1">
					FREQUENCY:
				</div>
				<div class="other-child2">
					<?php 
						if($checklist->cl_frequency==1){
							echo 'Daily';
						}else if($checklist->cl_frequency==2){
							echo 'Weekly';
						}else if($checklist->cl_frequency==3){
							echo 'Monthly';
						}else if($checklist->cl_frequency==4){
							echo 'Half Yearly';
						}else if($checklist->cl_frequency==5){
							echo 'Yearly';
						} 
					?>
				</div>
			</div>
			<div class="invoice-other">
				<div class="other-child1">
					EQUIPMENT
				</div>
				<div class="other-child2">
					<?php 
						echo $checklist_equipment->eqm_asset_name.'<br>'; 
					?>
				</div>
				<div class="other-child1">
					DATE:
				</div>
				<div class="other-child2">
					<?php echo date('d-m-Y',strtotime($checklist_perform->cl_p_date)); ?>
				</div>
			</div>

		</div>
	
		<div style="padding-top:1cm;margin-left:0cm;margin-right:0cm;">
			<table style="width:100%">
				<tr class="table-heading">
					<th style="text-align:center;">S NO</th>
					<th style="text-align:center;padding-left:5px;">CHECKLIST ITEM</th>
					<th style="text-align:center;padding-left:5px;">ACTION</th>
					<th style="text-align:center;padding-left:5px;">NOTES</th>
					<th style="text-align:center;padding-left:5px;">ATTACHMENT</th>
				</tr>
				<?php
				
				// $slipNoArray = explode('-', $receive_order->rorder_receipt_no); 
				foreach($checklist_perform_detailsets as $key=>$item) {
					$src='/upload_file/checklist_perform/184550pexels-photo-1078884.jpeg';
					?>
					<tr class="table-heading">
						<td style="text-align:center;"><?php echo $key+1; ?></td>
						<td style="text-align:left;padding-left:5px;"><?php echo $item->cli_item; ?></td>
						<td style="text-align:left;padding-left:5px;"><?php if($item->cl_pd_cli_value=='P'){echo 'PASS';}else if($item->cl_pd_cli_value=='F'){echo 'FAIL';}else{echo 'NA';} ?></td>
						<td style="text-align:left;padding-left:5px;"><?php echo $item->cl_pd_cli_notes; ?></td>
						<td style="text-align:left;padding-left:5px;"><?php if($item->cl_pd_cli_attachment!=''){echo $item->cl_pd_cli_attachment;}else{echo '-';} ?></td>
					</tr>
				<?php } ?>
	
			</table>
	
		</div>
	</div>
	</body>
	
	</html>
