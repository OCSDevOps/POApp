<?php $this->load->view('admin/component/header') ?>

<?php $this->load->view('admin/component/menu') ?>


		<!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
             <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-12 d-flex no-block align-items-center">
                        <h4 class="page-title">Dashboard</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url('admincontrol/dashboard'); ?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
				<?php if($this->session->userdata('utype')!=4){?>
				<div class="row">
					<!-- Column -->
					<div class="col-md-12 col-lg-12 col-xlg-12">
						<div class="card">
							<div class="card-body">
								<h5 class="card-title">Filter</h5>
								<div class="row">
									<label for="fname" class="col-md-1 text-right control-label col-form-label">Project</label>
									<div class="col-sm-3">
										<select class="form-control select2 custom-select" name="po_project" id="po_project"
												data-live-search="true" autocomplete="off" onchange="filterCharts(this);">
											<option value="">---Select---</option>
											<?php foreach ($proj_list as $p_items) { ?>
												<option
														value="<?php echo $p_items->proj_id; ?>"><?php echo $p_items->proj_name; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php }?>

                <div class="row">
                    <!-- Column -->
				<?php if($this->session->userdata('utype')!=4){?>
                    <div class="col-md-6 col-lg-3 col-xlg-3">
                        <div class="card card-hover">
                            <div class="box bg-cyan text-center">
                                <h1 class="font-light text-white" id="total_po">
                                    <?php 
                                     echo $total_po
                                    ?>
                                </h1>
                                <h6 class="text-white">Total PO</h6>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <div class="col-md-6 col-lg-3 col-xlg-3">
                        <div class="card card-hover">
                            <div class="box bg-success text-center">
                                <h1 class="font-light text-white" id="pending_po">
									<?php echo $pending_po ?>
								</h1>
                                <h6 class="text-white">Total Pending PO</h6>
                            </div>
                        </div>
                    </div>
                     <!-- Column -->
                    <div class="col-md-6 col-lg-3 col-xlg-3">
                        <div class="card card-hover">
                            <div class="box bg-warning text-center">
                                <h1 class="font-light text-white" id="submitted_po"><?php echo $submitted_po ?></h1>
                                <h6 class="text-white">Total Submitted PO</h6>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <div class="col-md-6 col-lg-3 col-xlg-3">
                        <div class="card card-hover">
                            <div class="box bg-danger text-center">
                                <h1 class="font-light text-white" id="rte_po"><?php echo $rte_po ?></h1>
                                <h6 class="text-white">Total Ready To Export PO</h6>
                            </div>
                        </div>
                    </div>
					<?php }else{?>
						<div class="col-md-6 col-lg-3 col-xlg-3">
                        <div class="card card-hover">
                            <div class="box bg-cyan text-center">
                                <h1 class="font-light text-white" id="total_po">
                                    <?php 
                                     echo $total_rfqs
                                    ?>
                                </h1>
                                <h6 class="text-white">Total RFQ'S</h6>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <div class="col-md-6 col-lg-3 col-xlg-3">
                        <div class="card card-hover">
                            <div class="box bg-success text-center">
                                <h1 class="font-light text-white" id="pending_po">
									<?php echo $waiting_rfqs ?>
								</h1>
                                <h6 class="text-white">Waiting for Response RFQ</h6>
                            </div>
                        </div>
                    </div>
                     <!-- Column -->
                    <div class="col-md-6 col-lg-3 col-xlg-3">
                        <div class="card card-hover">
                            <div class="box bg-warning text-center">
                                <h1 class="font-light text-white" id="submitted_po"><?php echo $total_items ?></h1>
                                <h6 class="text-white">Total No of Items</h6>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <div class="col-md-6 col-lg-3 col-xlg-3">
                        <div class="card card-hover">
                            <div class="box bg-danger text-center">
                                <h1 class="font-light text-white" id="rte_po"><?php echo $expiring_items ?></h1>
                                <h6 class="text-white">Items near Price Expiry</h6>
                            </div>
                        </div>
                    </div>
					<?php }?>
                    <!-- Column -->
                    <!-- Column -->
<!--                    <div class="col-md-6 col-lg-4 col-xlg-3">-->
<!--                        <div class="card card-hover">-->
<!--                            <div class="box bg-danger text-center">-->
<!--                                <h1 class="font-light text-white">--><?php //echo $total_receive ?><!--</h1>-->
<!--                                <h6 class="text-white">Total Receive Order</h6>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
                    <!-- Column -->
				<?php if($this->session->userdata('utype')!=4){?>
                    <div class="col-md-6 col-lg-2 col-xlg-3">
                        <div class="card card-hover">
                            <div class="box bg-info text-center">
                                <h1 class="font-light text-white" id="partially_received_po"><?php echo $partially_received ?></h1>
                                <h6 class="text-white">Total Partially Receive</h6>
                            </div>
                        </div>
                    </div>
                     <!-- Column -->
                    <div class="col-md-6 col-lg-2 col-xlg-3">
                        <div class="card card-hover">
                            <div class="box bg-cyan text-center">
                                <h1 class="font-light text-white" id="fully_received_po"><?php echo $fully_received ?></h1>
                                <h6 class="text-white">Total Fully Receive</h6>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <div class="col-md-6 col-lg-2 col-xlg-3">
                        <div class="card card-hover">
                            <div class="box bg-success text-center">
                                <h1 class="font-light text-white" id="not_received_po"><?php echo $not_received ?></h1>
                                <h6 class="text-white">Total Not Receive</h6>
                            </div>
                        </div>
                    </div>
					<?php }?>
                    <!-- Column -->
                </div>
				

				<?php if($this->session->userdata('utype')!=4){?>
				<div class="row">
					<div class="col-md-6">
						<div class="card">
							<div class="card-body">
								<h5 class="card-title">Purchase Order Chart</h5>
								<div id="chart_div" style="height: 400px"></div>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="card">
							<div class="card-body">
								<h5 class="card-title">Intergration Puchase Order Chart</h5>
								<div id="second_chart_div" style="height: 400px"></div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="card">
							<div class="card-body">
								<h5 class="card-title">Receive Order Chart</h5>
								<div id="third_chart_div" style="height: 400px"></div>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="card">
							<div class="card-body">
								<h5 class="card-title">Purchase Order Chart By Type</h5>
								<div id="fourth_chart_div" style="height: 400px"></div>
							</div>
						</div>
					</div>
				</div>
				<?php }?>

			</div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            


<?php $this->load->view('admin/component/footer') ?>

<script>
	$(document).ready(function (){
		$(".select2").selectpicker();
	})

	function filterCharts(element)
	{
		google.charts.load('current', {'packages':['corechart']});

		// Set a callback to run when the Google Visualization API is loaded.
		google.charts.setOnLoadCallback(drawChart(element.value));
	}
	function drawChart(po_id = 0) {

		//Pie Chart
		var jsonData = $.ajax({
			url: "dashboard/getPODataForChart",
			dataType: "json",
			data: {proj_id: po_id},
			async: false
		}).responseText;
		var Jsondata = JSON.parse(jsonData);

		$('#total_po').text(Jsondata.total_po);
		$('#pending_po').text(Jsondata.pending_po);
		$('#submitted_po').text(Jsondata.submitted_po);
		$('#rte_po').text(Jsondata.rte_po);
		$('#partially_received_po').text(Jsondata.partially_received);
		$('#fully_received_po').text(Jsondata.fully_received);
		$('#not_received_po').text(Jsondata.not_received);
		// Create the data table.
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Topping');
		data.addColumn('number', 'Slices');
		data.addRows([
			['Pending PO', (Jsondata.pending_po)],
			['Submitted PO', (Jsondata.submitted_po)],
		]);
		// Set chart options
		var options = {'title':'Total Purchase Order '+Jsondata.total_po,
			'width':'100%',
			'height':'100%'};

		// Instantiate and draw our chart, passing in some options.
		var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
		chart.draw(data, options);

		///--------------------------/////////

		var data2 = new google.visualization.DataTable();
		data2.addColumn('string', 'Topping');
		data2.addColumn('number', 'Slices');
		data2.addRows([
			['Pending PO', (Jsondata.integration_pending)],
			['Ready To Export PO', (Jsondata.rte_po)],
			['Synced PO', (Jsondata.integration_sync_po)],
		]);
		// Set chart options
		var options2 = {'title':'Total Purchase Order Integration '+Jsondata.total_po,
			'width':'100%',
			'height':'100%',
			colors:['red','blue','orange']
		};

		// Instantiate and draw our chart, passing in some options.
		var chart2 = new google.visualization.PieChart(document.getElementById('second_chart_div'));
		chart2.draw(data2, options2);


		///--------------------------/////////

		var data3 = new google.visualization.DataTable();
		data3.addColumn('string', 'Topping');
		data3.addColumn('number', 'Slices');
		data3.addRows([
			['Not Received', (Jsondata.not_received)],
			['Partially Received', (Jsondata.partially_received)],
			['Fully Received', (Jsondata.fully_received)],
		]);
		// Set chart options
		var options3 = {'title':'Total Purchase Orders '+Jsondata.total_po,
			'width':'100%',
			'height':'100%',
			colors:['red','orange','green']
		};

		// Instantiate and draw our chart, passing in some options.
		var chart3 = new google.visualization.PieChart(document.getElementById('third_chart_div'));
		chart3.draw(data3, options3);


		///--------------------------/////////

		// var data4 = new google.visualization.DataTable();
		// data4.addColumn('string', 'Topping');
		// data4.addColumn('number', 'Slices');
		// data4.addRows([
		// 	['Not Received', (Jsondata.not_received)],
		// 	['Partially Received', (Jsondata.partially_received)],
		// 	['Fully Received', (Jsondata.fully_received)],
		// ]);
		// // Set chart options
		// var options4 = {'title':'Total Purchase Orders '+Jsondata.total_po,
		// 	'width':'100%',
		// 	'height':'100%',
		// 	colors:['red','orange','green']
		// };

		var data4 = google.visualization.arrayToDataTable([
			["Element", "Density", { role: "style" } ],
			["Material PO", Jsondata.material_po, "red"],
			["Rental PO", Jsondata.rental_po, "orange"]
		]);

		var view = new google.visualization.DataView(data4);
		view.setColumns([0, 1,
			{ calc: "stringify",
				sourceColumn: 1,
				type: "string",
				role: "annotation" },
			2]);

		var options4 = {
			title: "Purchase Order By Type",
			width: "100%",
			height: "100%",
			bar: {groupWidth: "95%"},
			legend: { position: "none" },
		};

		// Instantiate and draw our chart, passing in some options.
		var chart4 = new google.visualization.ColumnChart(document.getElementById('fourth_chart_div'));
		chart4.draw(view, options4);

	}

</script>
