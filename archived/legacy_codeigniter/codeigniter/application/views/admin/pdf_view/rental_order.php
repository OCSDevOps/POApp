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
<!--                    Purchase Order-->
<!--					<br>-->
<!--			--><?php //echo $purchase_order->porder_no; ?>
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
                    <b>Project:</b><?php echo $project->proj_number; ?> - <?php echo $project->proj_name; ?>
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
                   <?php echo str_replace('PO','',$purchase_order->porder_type); ?> Purchase Order - <?php echo $purchase_order->porder_no; ?>
   				</span>
		</div>
	</div>
	<div class="invoice-main">
		<div class="invoice-other">
			<div class="other-child1">
				PURCHASE ORDER:
			</div>
			<div class="other-child2">
				<?php echo $purchase_order->porder_no; ?>
			</div>
		</div>
		<div class="invoice-other">
			<div class="other-child1">
				BILL TO:
			</div>
			<div class="other-child2">
				<?php if($project->billing_address != null ) { ?>
				<?php echo $project->billing_name;?>
				<br>
				<?php echo $project->billing_address;?>

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
				<b><?php echo $purchase_order->porder_address?></b>
			</div>
		</div>
		<div class="invoice-other">
			<div class="other-child1">
				SUPPLIER:
			</div>
			<div class="other-child2">
				<b><?php echo $supplier->sup_name?></b>
			</div>
			<div class="other-child1">
				DELIVERY DATE:
			</div>
			<div class="other-child2">
				<?php echo date('d-m-Y',strtotime($purchase_order->porder_delivery_date)); ?>
			</div>
		</div>
		<div class="invoice-other">
			<div class="other-child1">
				<?php echo $purchase_order->porder_type == "Rental PO" ? "RENTAL" : ""?> PO ISSUED ON DATE:
			</div>
			<div class="other-child2">
				<?php echo date('d-m-Y',strtotime($purchase_order->porder_createdate)) ?>
			</div>
			<div class="other-child1">
				CREATED BY:
			</div>
			<div class="other-child2">
				simran manesh <b>(Essence Properties)</b>
			</div>
		</div>
		<div class="invoice-other">
			<div class="other-child1">
				<?php echo $purchase_order->porder_type == "Rental PO" ? "RENTAL" : ""?> PO DESCRIPTION:
			</div>
			<div class="other-child2">
				<b><?php echo $purchase_order->porder_description; ?></b>
			</div>
			<div class="other-child1">
				DELIVERY NOTE:
			</div>
			<div class="other-child2">
				<?php echo $purchase_order->porder_delivery_note; ?>
			</div>
		</div>

	</div>
	<div style="border:1px solid black;margin-top:1cm;margin-left:0cm;margin-right:0cm;">
	</div>
	<div style="padding-top:1cm;margin-left:0cm;margin-right:0cm;">

		<?php $total_tax = 0;
		$sub_total = 0;
		?>
		<table style="width: 100%">
			<tr class="table-heading">
				<th style="text-align:center;width:1%; important;">#</th>
				<th style="text-align:left;padding-left:5px;width:5%; important;">Cost Code</th>
				<th style="text-align:left;padding-left:5px;width:5%; important;">SKU</th>
				<th style="text-align:left;padding-left:5px;width:10%; important;">Item Description</th>
				<th style="text-align:right;padding-right:5px;width:5%; important;">Qty</th>
				<?php if($purchase_order->porder_type == "Rental PO") { ?>
				<th style="text-align:center;width:5%; important;">Duration</th>
				<?php } ?>
				<th style="text-align:center;width:5%; important;">UOM</th>
				<th style="text-align:right;padding-right:5px;width:5%; important;">Unit Cost</th>
				<th style="text-align:center;width:5%; important;">Tax Code</th>
				<th style="text-align:right;padding-right:5px;width:5%; important;">Amount(Ex. Tax)</th>
			</tr>
			<?php foreach($item_detailsets as $key=>$item) {
				$total_tax += $item->po_detail_taxamount;
				$sub_total += $item->po_detail_subtotal;
				?>
			<tr class="table-heading">
				<td style="text-align:center;width:1%; important;"><?php echo $key+1; ?></td>
				<td style="text-align:left;padding-left:5px;width:5%; important;"><?php echo $item->cc_no; ?></td>
				<td style="text-align:left;padding-left:5px;width:5%; important;"><?php echo $item->po_detail_sku; ?></td>
				<td style="text-align:left;padding-left:5px;width:10%; important;"><?php echo isset($item->item_description) ? $item->item_description : $item->po_detail_description; ?></td>
				<td style="text-align:right;padding-right:5px;width:5%; important;"><?php echo $item->po_detail_quantity; ?></td>
				<?php if($purchase_order->porder_type == "Rental PO") { ?>
					<td style="text-align:center;width:5%; important;"><?php echo $item->po_detail_duration; ?></td>
				<?php } ?>
				<td style="text-align:center;width:5%; important;"><?php echo $item->uom_name; ?></td>
				<td style="text-align:right;padding-right:5px;width:5%; important;">$<?php echo $item->po_detail_unitprice; ?></td>
				<td style="text-align:center;width:5%; important;"><?php echo $item->tax_group_name; ?></td>
				<td style="text-align:right;padding-right:5px;width:5%; important;">$<?php echo $item->po_detail_subtotal; ?></td>
			</tr>
			<?php } ?>

			<tr class="table-heading">
				<td colspan="<?php echo ($purchase_order->porder_type == "Rental PO") ? "9" : "8"?>" style="text-align:right;padding-right:5px;width:5%; important;font-weight:bold;">Subtotal:</td>
				<td style="text-align:right;padding-right:5px;width:5%; important;font-weight:bold;">$<?php echo $sub_total ?></td>
			</tr>
			<tr class="table-heading">
				<td colspan="<?php echo ($purchase_order->porder_type == "Rental PO") ? "9" : "8"?>" style="text-align:right;padding-right:5px;width:5%; important;">Total Tax:</td>
				<td style="text-align:right;padding-right:5px;width:5%; important;">$<?php echo $total_tax ?></td>
			</tr>
			<tr class="table-heading">
				<td colspan="<?php echo ($purchase_order->porder_type == "Rental PO") ? "9" : "8"?>" style="text-align:right;padding-right:5px;width:5%; important;font-weight:bold;">Grand Total:</td>
				<td style="text-align:right;padding-right:5px;width:5%; important;font-weight:bold;"> $<?php echo $purchase_order->porder_total_amount?></td>
			</tr>
		</table>
	</div>
</div>
</body>

</html>
