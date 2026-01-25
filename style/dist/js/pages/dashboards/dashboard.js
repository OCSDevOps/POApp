/*
Template Name: Admin Pro Admin
Author: Wrappixel
Email: niravjoshi87@gmail.com
File: js
*/
$(function() {
    "use strict";

	// Load the Visualization API and the corechart package.
	google.charts.load('current', {'packages':['corechart']});

	// Set a callback to run when the Google Visualization API is loaded.
	google.charts.setOnLoadCallback(drawChart);

	// Callback that creates and populates a data table,
	// instantiates the pie chart, passes in the data and
	// draws it.
	function drawChart(po_id= 0) {

		//Pie Chart
		var jsonData = $.ajax({
			url: "https://oneclicksolutions.ca/admincontrol/dashboard/getPODataForChart",
			dataType: "json",
			data: {proj_id: po_id},
			async: false
		}).responseText;
		var Jsondata = JSON.parse(jsonData);

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
});
