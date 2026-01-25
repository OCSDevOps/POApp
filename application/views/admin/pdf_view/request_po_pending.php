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
<!--                   Request Purchase Order-->
<!--					<br>-->
<!--			-->
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
                   Request Form Quote - <?php echo $purchase_order->rporder_no; ?>
                </span>
		</div>
	</div>
	<div class="invoice-main">
		<div class="invoice-other">
			<div class="other-child1">
				RFQ Number:
			</div>
			<div class="other-child2">
				<?php echo $purchase_order->rporder_no; ?>
			</div>
		</div>
<!--		<div class="invoice-other">-->
<!--			<div class="other-child1">-->
<!--				BILL TO:-->
<!--			</div>-->
<!--			<div class="other-child2">-->
<!--				Project Information-->
<!--			</div>-->
<!--			<div class="other-child1">-->
<!--				SHIP TO:-->
<!--			</div>-->
<!--			<div class="other-child2">-->
<!--				<b>--><?php //echo $purchase_order->rporder_address?><!--</b>-->
<!--			</div>-->
<!--		</div>-->
		<div class="invoice-other">
			<div class="other-child1">
				SUPPLIER:
			</div>
			<div class="other-child2">
				<b><?php echo $supplier->sup_name?></b>
			</div>
			<div class="other-child1">
				EST DELIVERY DATE:
			</div>
			<div class="other-child2">
				<?php echo date('d-m-Y',strtotime($purchase_order->rporder_delivery_date)); ?>
			</div>
		</div>
		<div class="invoice-other">
			<div class="other-child1">
				REQUEST ISSUED ON DATE:
			</div>
			<div class="other-child2">
				<?php echo date('d-m-Y',strtotime($purchase_order->rporder_createdate)) ?>
			</div>
			<div class="other-child1">
				REQUESTED BY:
			</div>
			<div class="other-child2">
				simran manesh <b>(Essence Properties)</b>
			</div>
		</div>
		<div class="invoice-other">
			<div class="other-child1">
				REQUEST DESCRIPTION:
			</div>
			<div class="other-child2">
				<b><?php echo $purchase_order->rporder_description; ?></b>
			</div>
			<div class="other-child1">
				DELIVERY NOTE:
			</div>
			<div class="other-child2">
				<?php echo $purchase_order->rporder_delivery_note; ?>
			</div>

<!--			<div class="invoice-other">-->
<!--				<div class="other-child1">-->
<!--					STATUS:-->
<!--				</div>-->
<!--				<div class="other-child2">-->
<!--					<b>--><?php //echo $purchase_order->rporder_status; ?><!--</b>-->
<!--				</div>-->
<!--			</div>-->
		</div>

		<div class="invoice-other">

		</div>
	</div>
	<div style="border:1px solid black;margin-top:1cm;margin-left:0cm;margin-right:0cm;">
	</div>
	<div style="padding-top:1cm;margin-left:0cm;margin-right:0cm;">

		<?php $total_tax = 0;
		$sub_total = 0;
		$total = 0;
		?>
		<table style="width: 100%">
			<tr class="table-heading">
				<th style="text-align:center;width:1%; important;">#</th>
				<th style="text-align:left;padding-left:5px;width:5%; important;">Cost Code</th>
				<th style="text-align:left;padding-left:5px;width:5%; important;">SKU</th>
				<th style="text-align:left;padding-left:5px;width:10%; important;">Item Description</th>
				<th style="text-align:right;padding-right:5px;width:5%; important;">Qty</th>
				<th style="text-align:center;width:5%; important;">UOM</th>
				<?php if($purchase_order->rporder_status!='waiting for response'){?>
					<th style="text-align:right;padding-right:5px;width:5%; important;">Unit Cost</th>
					<th style="text-align:center;width:5%; important;">Tax Code</th>
					<th style="text-align:right;padding-right:5px;width:5%; important;">Amount(Ex. Tax)</th>
					<th style="text-align:right;padding-right:5px;width:5%; important;">Tax Amount</th>
					<th style="text-align:right;padding-right:5px;width:5%; important;">Total</th>
				<?php }?>
			</tr>
			<?php foreach($item_detailsets as $key=>$item) {
				$total_tax += $item->rfq_detail_taxamount;
				$sub_total += $item->rfq_detail_subtotal;
				$total += $item->rfq_detail_total;
				?>
			<tr class="table-heading">
				<td style="text-align:center;width:1%; important;"><?php echo $key+1; ?></td>
				<td style="text-align:left;padding-left:5px;width:5%; important;"><?php echo $item->cc_no; ?></td>
				<td style="text-align:left;padding-left:5px;width:5%; important;"><?php if($item->supcat_sku_no!=''){echo $item->supcat_sku_no;}else{echo '-';} ?></td>
				<td style="text-align:left;padding-left:5px;width:10%; important;"><?php echo isset($item->item_description) ? $item->item_description : $item->rfq_detail_description; ?></td>
				<td style="text-align:right;padding-right:5px;width:5%; important;"><?php echo $item->rfq_detail_quantity; ?></td>
				<td style="text-align:center;width:5%; important;"><?php echo $item->uom_name; ?></td>
				<?php if($purchase_order->rporder_status!='waiting for response'){?>
					<td style="text-align:right;padding-right:5px;width:5%; important;"><?php echo $item->rfq_detail_unitprice; ?></td>
					<td style="text-align:center;width:5%; important;"><?php echo $item->tax_group_name; ?></td>
					<td style="text-align:right;padding-right:5px;width:5%; important;"><?php echo $item->rfq_detail_subtotal; ?></td>
					<td style="text-align:right;padding-right:5px;width:5%; important;"><?php echo $item->rfq_detail_taxamount; ?></td>
					<td style="text-align:right;padding-right:5px;width:5%; important;"><?php echo $item->rfq_detail_total; ?></td>
				<?php }?>
			</tr>

			<?php } ?>

			<?php if($purchase_order->rporder_status!='waiting for response'){
				
				$formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
				?>
				<tr>
					<td colspan="8" style="text-align:right;padding-right:10px;font-weight:bold">Sub Total</td>
					<td colspan="3" style="text-align:right;padding-right:10px;font-weight:bold"><?php echo $formatter->formatCurrency($sub_total, 'USD');?></td>
				</tr>
				<tr>
					<td colspan="8" style="text-align:right;padding-right:10px;font-weight:bold">Total Tax</td>
					<td colspan="3" style="text-align:right;padding-right:10px;font-weight:bold"><?php echo $formatter->formatCurrency($total_tax, 'USD');?></td>
				</tr>
				<tr>
					<td colspan="8" style="text-align:right;padding-right:10px;font-weight:bold">Total</td>
					<td colspan="3" style="text-align:right;padding-right:10px;font-weight:bold"><?php echo $formatter->formatCurrency($total, 'USD');?></td>
				</tr>
			<?php }?>

<!--			<tr class="table-heading">-->
<!--				<td colspan="8" style="text-align:right;padding-right:5px;width:5%; important;font-weight:bold;">Subtotal:</td>-->
<!--				<td style="text-align:right;padding-right:5px;width:5%; important;font-weight:bold;">$--><?php //echo $sub_total ?><!--</td>-->
<!--			</tr>-->
<!--			<tr class="table-heading">-->
<!--				<td colspan="8" style="text-align:right;padding-right:5px;width:5%; important;">Total Tax:</td>-->
<!--				<td style="text-align:right;padding-right:5px;width:5%; important;">$--><?php //echo $total_tax ?><!--</td>-->
<!--			</tr>-->
<!--			<tr class="table-heading">-->
<!--				<td colspan="8" style="text-align:right;padding-right:5px;width:5%; important;font-weight:bold;">Grand Total:</td>-->
<!--				<td style="text-align:right;padding-right:5px;width:5%; important;font-weight:bold;"> $--><?php //echo $purchase_order->rporder_total_amount?><!--</td>-->
<!--			</tr>-->
		</table>
	</div>
</div>
</body>

</html>
