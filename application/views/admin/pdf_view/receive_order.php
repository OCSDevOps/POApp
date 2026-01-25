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
				<span class="invoice-project child">
						<b>Project:</b><?php echo $receive_order->proj_number; ?> - <?php echo $receive_order->proj_name; ?>
						<br>
						6309 Carpinteria Avenue
						<br>
						Carpinteria, California 93013
	
					</span>
	
			</div>
		</div>
		<div>
			<div class="parent" >
					<span class="invoice-heading child">
						 Receive Order - <?php echo $receive_order->rorder_receipt_no; ?>
	
					</span>
			</div>
		</div>
		<div class="invoice-main">
			<div class="invoice-other">
				<div class="other-child1">
				RECEIPT NO:
				</div>
				<div class="other-child2">
					<?php echo $receive_order->rorder_receipt_no; ?>
				</div>
				<div class="other-child1">
					PACKING SLIP NO:
				</div>
				<div class="other-child2">
					<?php echo $receive_order->rorder_slip_no; ?>
				</div>
			</div>
			<div class="invoice-other">
				<div class="other-child1">
					BILL TO:
				</div>
				<div class="other-child2">
					<?php if($receive_order->billing_address != null ) { ?>
						<?php echo $receive_order->billing_name;?>
						<br>
						<?php echo $receive_order->billing_address;?>
	
					<?php } else { ?>
	
						<?php echo $company->company_name;?>
						<br>
						<?php echo $company->company_address;?>
					<?php } ?>
				</div>
				<div class="other-child1">
					SHIP TO:
				</div>
				<div class="other-child2">
					<b><?php echo $receive_order->porder_address?></b>
				</div>
			</div>
			<div class="invoice-other">
				<div class="other-child1">
					SUPPLIER
				</div>
				<div class="other-child2">
					<b><?php echo $receive_order->sup_name?></b>
				</div>
				<div class="other-child1">
					RECEIVE DATE:
				</div>
				<div class="other-child2">
					<?php echo date('d-m-Y',strtotime($receive_order->rorder_date)); ?>
				</div>
			</div>
			<div class="invoice-other">
				<div class="other-child1">
					DELIVERY NOTE:
				</div>
				<div class="other-child2">
					<?php echo $receive_order->porder_delivery_note; ?>
				</div>
			</div>

		</div>
	
		<div style="padding-top:1cm;margin-left:0cm;margin-right:0cm;">
			<table style="width:100%">
				<tr class="table-heading">
					<th style="text-align:center;width:1%; important;">#</th>
					<th style="text-align:center;padding-left:5px;width:5%; important;">Item</th>
					<th style="text-align:center;padding-left:5px;width:5%; important;">Item Code</th>
					<th style="text-align:center;padding-left:5px;width:2%; important;">Purchased Quantity</th>
					<th style="text-align:center;padding-left:5px;width:2%; important;">Previously Received Quantity</th>
					<th style="text-align:center;width:10%; important;">Receive Quantity</th>
					<th style="text-align:center;padding-right:5px;width:10%; important;">Balance Quantity</th>
				</tr>
				<?php
				
				$slipNoArray = explode('-', $receive_order->rorder_receipt_no); 
				foreach($item_detailsets as $key=>$item) {
					?>
					<tr class="table-heading">
						<td style="text-align:center;width:1%; important;"><?php echo $key+1; ?></td>
						<td style="text-align:left;padding-left:5px;width:3%; important;"><?php echo $item->ro_detail_item; ?></td>
						<td style="text-align:left;padding-left:5px;width:3%; important;"><?php echo $item->item_code; ?></td>
						<td style="text-align:left;padding-left:5px;width:3%; important;"><?php echo $item->ro_detail_total; ?></td>
						<td style="text-align:left;padding-left:5px;width:3%; important;"><?php if($slipNoArray[2]==1){echo '0';}else{echo $this->admin_m->getSUMOfReceiveOrderItem($receive_order->rorder_porder_ms, $item->ro_detail_item, $item->ro_detail_id)->previous_purchase; }?></td>
						<td style="text-align:center;width:5%; important;"><?php echo $item->ro_detail_quantity; ?></td>
						<td style="text-align:right;padding-right:5px;width:5%; important;"><?php echo $item->ro_detail_remaining; ?></td>
					</tr>
				<?php } ?>
	
			</table>
	
		</div>
	</div>
	</body>
	
	</html>
