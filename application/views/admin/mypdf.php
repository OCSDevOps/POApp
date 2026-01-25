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
		text-align: center;
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
		height: 3cm;
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
		top:0.1cm;
	}

	.invoice-project {
		font-size: 15px;
		font-weight:400;
		color: black;
		right: 0cm;
		text-align:right;
		top:0.1cm;
	}

	.invoice-heading {
		font-size: 30px;
		font-weight:700;
		color: black;
		text-align:center;
		top:2.5cm;
		width:100%;
		border-bottom: solid 2px;
		border-top: solid 2px;

	}

	.invoice-Other {
		font-size: 15px;
		color: black;
		border-bottom: 1px solid #d2d6d3;
		padding-top:5px;
		padding-bottom:5px;
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
			<img class="invoice-logo child" src="PoCss/logo.png" alt="" width="175" height="100"/>
			<span class="invoice-type child">
                    Purchase Order
					<br>
					1234-2
                </span>

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
                    <b>Project:</b> 1234 - Sandbox Test Project
					<br>
					6309 Carpinteria Avenue
					<br>
					Carpinteria, California 93013

                </span>

		</div>
	</div>
	<div>
		<div class="parent" style="margin-left:0cm;margin-right:0cm;">
				<span class="invoice-heading child">
                    Material Purchase Order
                </span>
		</div>
	</div>
	<div style="margin-top:3.5cm;margin-left:0cm;margin-right:0cm;">
		<div class="invoice-other">
			<div class="other-child1">
				DATE CREATED:
			</div>
			<div class="other-child2">
				07/03/2022
			</div>
		</div>
		<div class="invoice-other">
			<div class="other-child1">
				BILL TO:
			</div>
			<div class="other-child2">
				Project Information
			</div>
			<div class="other-child1">
				SHIP TO:
			</div>
			<div class="other-child2">
				<b>Delivery Address</b>
			</div>
		</div>
		<div class="invoice-other">
			<div class="other-child1">
				CONTRACT COMPANY:
			</div>
			<div class="other-child2">
				<b>Procore (Test Companies)</b>
			</div>
			<div class="other-child1">
				DELIVERY DATE:
			</div>
			<div class="other-child2">
				09/03/2022
			</div>
		</div>
		<div class="invoice-other">
			<div class="other-child1">
				ISSUED ON DATE:
			</div>
			<div class="other-child2">
				Date Submitted
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
				PO DESCRIPTION:
			</div>
			<div class="other-child2">
				<b>Po description here</b>
			</div>
		</div>
		<div class="invoice-other">
			<div class="other-child1">
				ATTACHMENTS:
			</div>
			<div class="other-child2">
			</div>
			<div class="other-child1">
				DELIVERY NOTE:
			</div>
			<div class="other-child2">
				Delivery Note Information
			</div>
		</div>
	</div>
	<div style="border:1px solid black;margin-top:1cm;margin-left:0cm;margin-right:0cm;">
	</div>
	<div style="margin-top:0.5cm;margin-left:0cm;margin-right:0cm;">
		<table>
			<tr class="table-heading">
				<th style="text-align:center;width:1%; important;">#</th>
				<th style="text-align:left;padding-left:5px;width:5%; important;">Cost Code</th>
				<th style="text-align:left;padding-left:5px;width:5%; important;">SKU</th>
				<th style="text-align:left;padding-left:5px;width:10%; important;">Item Description</th>
				<th style="text-align:right;padding-right:5px;width:5%; important;">Qty</th>
				<th style="text-align:center;width:5%; important;">UOM</th>
				<th style="text-align:right;padding-right:5px;width:5%; important;">Unit Cost</th>
				<th style="text-align:center;width:5%; important;">Tax Code</th>
				<th style="text-align:right;padding-right:5px;width:5%; important;">Amount(Ex. Tax)</th>
			</tr>
			<tr class="table-heading">
				<td style="text-align:center;width:1%; important;">1</td>
				<td style="text-align:left;padding-left:5px;width:5%; important;">2-10-04</td>
				<td style="text-align:left;padding-left:5px;width:5%; important;">12345</td>
				<td style="text-align:left;padding-left:5px;width:10%; important;">1 X 4 - 12 PRIMED FASCIA C1S</td>
				<td style="text-align:right;padding-right:5px;width:5%; important;">50.0</td>
				<td style="text-align:center;width:5%; important;">Each</td>
				<td style="text-align:right;padding-right:5px;width:5%; important;">$10.16</td>
				<td style="text-align:center;width:5%; important;">G</td>
				<td style="text-align:right;padding-right:5px;width:5%; important;">$508.00</td>
			</tr>
			<tr class="table-heading">
				<td style="text-align:center;width:1%; important;">2</td>
				<td style="text-align:left;padding-left:5px;width:5%; important;">2-10-04</td>
				<td style="text-align:left;padding-left:5px;width:5%; important;">123456</td>
				<td style="text-align:left;padding-left:5px;width:10%; important;">Test Samiran PRIMED FASCIA C1S</td>
				<td style="text-align:right;padding-right:5px;width:5%; important;">50.0</td>
				<td style="text-align:center;width:5%; important;">Each</td>
				<td style="text-align:right;padding-right:5px;width:5%; important;">$37</td>
				<td style="text-align:center;width:5%; important;">G</td>
				<td style="text-align:right;padding-right:5px;width:5%; important;">$1,850.00</td>
			</tr>
			<tr class="table-heading">
				<td colspan="8" style="text-align:right;padding-right:5px;width:5%; important;font-weight:bold;">Subtotal:</td>
				<td style="text-align:right;padding-right:5px;width:5%; important;font-weight:bold;">$2,358.00</td>
			</tr>
			<tr class="table-heading">
				<td colspan="8" style="text-align:right;padding-right:5px;width:5%; important;">Total Tax:</td>
				<td style="text-align:right;padding-right:5px;width:5%; important;">$117</td>
			</tr>
			<tr class="table-heading">
				<td colspan="8" style="text-align:right;padding-right:5px;width:5%; important;font-weight:bold;">Grand Total:</td>
				<td style="text-align:right;padding-right:5px;width:5%; important;font-weight:bold;"> $2,358.00</td>
			</tr>
		</table>
	</div>
</div>
</body>

</html>
