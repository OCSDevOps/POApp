<?php $this->load->view('admin/component/header') ?>

<?php $this->load->view('admin/component/menu') ?>


<style>
	.box-body textarea, input, select {
		max-width: 500px;
	}

	.box-body textarea {
		resize: vertical;
	}
</style>

<!-- Page wrapper  -->
<!-- ============================================================== -->
<div class="page-wrapper">
	<!-- ============================================================== -->
	<!-- Bread crumb and right sidebar toggle -->
	<!-- ============================================================== -->
	<div class="page-breadcrumb">
		<div class="row">
			<div class="col-12 d-flex no-block align-items-center">
				<h4 class="page-title">Add New Request Form Quote</h4>
				<div class="ml-auto text-right">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a
										href="<?php echo base_url('admincontrol/dashboard'); ?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Add Request Order</li>
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
		<!-- ============================================================== -->
		<!-- Start Page Content -->
		<!-- ============================================================== -->
		<div class="row">
			<div class="col-md-12">


				<div class="card">
					<?php echo form_open_multipart('', 'class="form-horizontal" id="myForm"'); ?>
					<div class="card-body">
						<?php if (isset($error)) { ?>
							<div class="alert alert-danger alert-error">
								<h4>Error!</h4>
								<?php echo $error; ?>
							</div>
						<?php } ?>
						<div align="center">
							<div class="alert alert-danger alert-error get_error_total9 hide"></div>
							<div class="alert alert-success alert-error get_success_total9 hide"></div>
							<div class="alert alert-success alert-error  div_roller_total9 hide"><img
										src="<?php echo base_url(); ?>style/images/ajax_loader.gif"
										style="max-width: 60px;"/></div>
						</div>
						<!--<h4 class="card-title">Personal Info</h4>-->
						<div class="form-group row">
							<label for="fname" class="col-sm-2 text-right control-label col-form-label">Project</label>
							<div class="col-sm-3">
								<input type="hidden" name="ipack_itm_no" id="ipack_itm_no"
									   value="<?php echo date('dmyHis'); ?>" autocomplete="off"/>
								<input type="hidden" name="itemdtl_counter" id="itemdtl_counter" value="0"
									   autocomplete="off"/>
								<input type="hidden" name="itemdtl_tamount" id="itemdtl_tamount" value="0"
									   autocomplete="off"/>
								<select class="form-control select2 custom-select" name="po_project" id="po_project"
										data-live-search="true" autocomplete="off" onchange="goto_check_project();">
									<option value="">---Select---</option>
									<?php foreach ($proj_list as $p_items) { ?>
										<option
												value="<?php echo $p_items->proj_id; ?>"><?php echo $p_items->proj_name; ?></option>
									<?php } ?>
								</select>
								<small
										class="invalid-feedback po_project"><?php echo form_error('po_project'); ?></small>
							</div>
							<label for="fname" class="col-sm-3 text-right control-label col-form-label">Request Form
								Number</label>
							<div class="col-sm-3">
								<div class="row">
									<input type="text" class="form-control col-5" readonly name="po_number_prefix"
										   id="po_number_prefix"
										   autocomplete="off"/>
									<input type="text" class="form-control col-5" name="po_number" id="po_number"
										   autocomplete="off"/>
									<small
											class="invalid-feedback po_number"><?php echo form_error('po_number'); ?></small>
								</div>
							</div>
						</div>
						<div class="form-group row">
							<label for="fname" class="col-sm-2 text-right control-label col-form-label">Supplier</label>
							<div class="col-sm-3">
								<select class="form-control select2 custom-select" name="po_supp" id="po_supp"
										data-live-search="true" autocomplete="off">
									<option value="">---Select---</option>
									<?php foreach ($supp_list as $sup_items) { ?>
										<option
												value="<?php echo $sup_items->sup_id; ?>"><?php echo $sup_items->sup_name; ?></option>
									<?php } ?>
								</select>
								<small class="invalid-feedback po_supp"><?php echo form_error('po_supp'); ?></small>
							</div>

							<label for="cono1" class="col-sm-3 text-right control-label col-form-label">Request Form
								Description</label>
							<div class="col-sm-3">
			<textarea class="form-control" name="po_desc" id="po_desc"
					  placeholder="Enter Request Form Description" autocomplete="off"></textarea>
								<small
										class="invalid-feedback po_desc"><?php echo form_error('po_description'); ?></small>
							</div>
						</div>
						<div class="form-group row">
							<label for="cono1" class="col-sm-2 text-right control-label col-form-label">Delivery
								Address</label>
							<div class="col-sm-3">
			<textarea class="form-control" name="po_address" id="po_address"
					  placeholder="Enter Delivery Address" autocomplete="off"></textarea>
								<small
										class="invalid-feedback po_address"><?php echo form_error('po_address'); ?></small>
							</div>

							<label for="cono1" class="col-sm-3 text-right control-label col-form-label">Delivery
								Note</label>
							<div class="col-sm-3">
			<textarea class="form-control" name="po_dl_note" id="po_dl_note"
					  placeholder="Enter Delivery Note" autocomplete="off"></textarea>
								<small
										class="invalid-feedback po_dl_note"><?php echo form_error('po_dl_note'); ?></small>
							</div>
						</div>
						<div class="form-group row">
							<label for="cono1" class="col-sm-2 text-right control-label col-form-label">Delivery
								Date</label>
							<div class="col-sm-3">
								<input class="form-control" type="date" name="po_delivery_date" id="po_delivery_date"
									   placeholder="Enter Delivery Date" autocomplete="off">
								<small class="invalid-feedback po_delivery_date"><?php echo form_error('po_delivery_date'); ?></small>
							</div>
						</div>

						<div class="form-group row">
							<div class="col-sm-12">
								<a href="javascript:;" onclick="goto_add_record();" class="btn btn-primary mb-2"
								   style="margin-right: 10px;">Add New Item From Package</a>
								<!--								<a href="javascript:;" class="btn btn-primary mb-2" id="itemtbutton"-->
								<!--								   onclick="gotosubmit_itemset();" disabled>Add Item</a>-->
								<a href="javascript:;" class="btn btn-primary mb-2 add_row" id="itemtbutton">Add
									Item</a>
							</div>
						</div>
						<div class="form-group row justify-content-md-center">
							<div class="col-sm-12">
								<div class="table-responsive1">
									<table class="table table-bordered " id="porder_table">
										<thead>
										<tr>
											<th style="min-width:120px!important;overflow:hidden;word-wrap:break-word;white-space: normal;vertical-align:middle">Item</th>
											<th style="min-width:300px!important;overflow:hidden;word-wrap:break-word;white-space: normal;vertical-align:middle">Description</th>
											<th style="min-width:120px!important;overflow:hidden;word-wrap:break-word;white-space: normal;vertical-align:middle">CostCode</th>
											<!--														<th>SKU</th>-->
											<th style="min-width:90px!important;overflow:hidden;word-wrap:break-word;white-space: normal;vertical-align:middle">UOM</th>
											<!--														<th>Tax Code</th>-->
											<th style="min-width:120px!important;overflow:hidden;word-wrap:break-word;white-space: normal;vertical-align:middle">Quantity</th>
											<th style="min-width:80px!important;overflow:hidden;word-wrap:break-word;white-space: normal;vertical-align:middle">Action</th>
										</tr>
										</thead>
										<tbody class="setall_experiences" id="porder_table_body">
										<tr data-id="1">
											<td>
												<select class="form-control select2 custom-select pk_item1"
														name="pk_item[]"
														data-width="110px"
														data-live-search="true" autocomplete="off" data-id="1"
														onchange="goto_check_item(this);">
													<option value="">---Select---</option>
													<?php foreach ($itm_list as $items) { ?>
														<option
																value="<?php echo $items->item_code; ?>"><?php echo htmlspecialchars($items->item_code . ' - ' . $items->item_name, ENT_QUOTES); ?></option>
													<?php } ?>
												</select><br>
												<a href="javascript:;" onclick="goto_advance_item_lookup(1);" class="btn btn-sm border border-danger " style="font-weight:bold"  data-toggle="tooltip" data-placement="top" title="Advanced Look-Up">...</a>
											</td>
											<td>
												<textarea type="text" class="form-control pk_desc" name="pk_desc[]"
														  id="pk_desc"
														  placeholder="Item Description" autocomplete="off"></textarea>
												<small
														class="invalid-feedback pk_desc_error"><?php echo form_error('pk_desc'); ?></small>
											</td>
											<td>
												<select class="form-control select2 custom-select pk_ccode"
														name="pk_ccode[]"
														data-live-search="true" autocomplete="off" data-id="1">
													<option value="">---Select---</option>
													<?php foreach ($ccode_list as $items) { ?>
														<option value="<?php echo $items->cc_id; ?>"><?php echo $items->cc_no.' - '.$items->cc_description; ?></option>
													<?php } ?>
												</select>

												<small
														class="invalid-feedback pk_ccode_error"><?php echo form_error('pk_ccode'); ?></small>
											</td>

											<td>
												<select class="form-control select2 custom-select pk_uom"
														name="pk_uom[]" id="pk_uom"
														data-live-search="true" autocomplete="off" data-id="1">
													<option value="">---Select---</option>
													<?php foreach ($uom_list as $items) { ?>
														<option value="<?php echo $items->uom_id; ?>"><?php echo $items->uom_name; ?></option>
													<?php } ?>
												</select>
												<small
														class="invalid-feedback pk_uom_error"><?php echo form_error('pk_uom'); ?></small>
											</td>
											<td>
												<input type="number" class="form-control pk_itm_qnty"
													   name="pk_itm_qnty[]"
													   id="pk_itm_qnty" placeholder="Enter Quantity" autocomplete="off"
													   onchange="goto_check_item_amounts(this);" data-id="1"/>
												<small
														class="invalid-feedback pk_itm_qnty_error"><?php echo form_error('pk_itm_qnty'); ?></small>
											</td>
											<td></td>

										</tr>
										</tbody>
										<tbody class="expr_setvalue">
										</tbody>
									</table>
								</div>
							</div>
						</div>

						<div class="form-group row">
							<div class="col-sm-12 text-center">
								<div align="center">
									<div class="get_error_total" align="center"
										 style="background-color: #bf0000;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
									<div class="get_success_total" align="center"
										 style="background-color: #174b10;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
									<div class="div_roller_total" align="center" style="display: none;"><img
												src="<?php echo base_url(); ?>style/images/ajax_loader.gif"
												style="max-width: 60px;"/></div>
								</div>
							</div>
						</div>
					</div>
					<div class="border-top">
						<div class="card-body">
<!--							<button type="button" onclick="gotoclclickbutton('waiting for response');" class="btn btn-primary">Save</button>-->
							<button type="button" onclick="gotoclclickbutton('waiting for response');" class="btn btn-primary">Send</button>
							&nbsp;<a href="<?= site_url('admincontrol/rfqorder/all_rfq_list') ?>"
									 class="btn btn-danger">Cancel</a>
						</div>
					</div>
					<?php form_close(); ?>
				</div>

			</div>
		</div>

	</div>
	<!-- ============================================================== -->
	<!-- End Container fluid  -->
	<!-- ============================================================== -->
	<!-- ============================================================== -->


	<!-- Modal -->
	<div class="modal fade" id="Modal_addrecord" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
		 aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Add Item from Item Package</h5>
					<button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="recipient-name" class="col-form-label">Package Name:</label>
						<select class="form-control select2 custom-select" name="itmpk_id" id="itmpk_id"
								data-live-search="true" autocomplete="off" onchange="goto_check_package();">
							<option value="">---Select---</option>
							<?php foreach ($pak_list as $pkitems) { ?>
								<option
										value="<?php echo $pkitems->ipack_id; ?>"><?php echo $pkitems->ipack_name; ?></option>
							<?php } ?>
						</select>
						<small class="invalid-feedback itmpk_id"><?php //echo form_error('itmpk_id'); ?></small>
					</div>
					<div class="form-group row packset_item">

					</div>
					<div class="col-sm-12 text-center">
						<div align="center">
							<div class="get_error_total2" align="center"
								 style="background-color: #bf0000;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
							<div class="get_success_total2" align="center"
								 style="background-color: #174b10;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
							<div class="div_roller_total2" align="center" style="display: none;"><img
										src="<?php echo base_url(); ?>style/images/ajax_loader.gif"
										style="max-width: 60px;"/></div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>-->
					<button type="button" id="submit_record_btn" class="btn btn-primary"
							onclick="goto_submit_record();">Submit
					</button>
				</div>
			</div>
		</div>
	</div>

		<!-- Modal -->
		<div class="modal fade" id="Modal_advanceitemlookup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
		 aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Advance Item lookup</h5>
					<button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<input type="hidden" name="" id="hidden-item-id">
					<div class="row">
						<div class="col-12 text-center">
							<div class="form-check-inline">
								<label class="form-check-label">
									<input type="radio" class="form-check-input" name="itemfrom" value="1">Item Master
								</label>
							</div>
							<div class="form-check-inline">
								<label class="form-check-label">
									<input type="radio" class="form-check-input" name="itemfrom" checked value="2">Supplier Catalog
								</label>
							</div>
						</div>
					</div>
					<div class="row">
						<?php foreach($icat_list as $icat){?>
							<input type="hidden" id="catName<?php echo $icat->icat_id?>" value="<?php echo $icat->icat_name?>">
							<!-- <option value="<?php echo $icat->icat_id?>"><?php echo $icat->icat_name?></option> -->
						<?php }?>
						<div class="col-12 col-md-6">
							<label for="search_category"></label>
							<select class="form-control" name="search_category" id="search_category">
								<option value="">--Select Category--</option>
								<?php foreach($icat_list as $icat){?>
									<option value="<?php echo $icat->icat_id?>"><?php echo $icat->icat_name?></option>
								<?php }?>
							</select>
						</div>
						<div class="col-12 col-md-6">
							<label for="search_cc"></label>
							<select class="form-control" name="search_cc" id="search_cc">
								<option value="">--Select Cost Code--</option>
								<?php foreach($ccode_list as $ccode){?>
									<option value="<?php echo $ccode->cc_id?>"><?php echo $ccode->cc_no?></option>
								<?php }?>
							</select>
						</div>
					</div>
					<div class="item_row row mt-4" style="display:none;">
						<div class="col-12 table-responsive">
							<table class="table table-bordered" id="itemmaster_search_table" style="width:100%!important">
								<thead>
									<tr>
										<th>S No</th>
										<th>Item Code</th>
										<th>Item Name</th>
										<th>Category</th>
										<th>Cost Code</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
					<div class="supc_row row mt-4">
						<div class="col-12 table-responsive">
							<table class="table table-bordered" id="supplierc_search_table">
								<thead>
									<tr>
										<th>S No</th>
										<th>Item Code</th>
										<th>Item Name</th>
										<th>Category</th>
										<th>Cost Code</th>
										<th>SKU No</th>
										<th>Price</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>-->
					<button type="button" id="select_multiple_items_btn" class="btn btn-primary">Submit
					</button>
				</div>
			</div>
		</div>
	</div>


	<?php $this->load->view('admin/component/footer') ?>

	<script type="text/javascript">
		$(function () {
			$('.alert-error, .invalid-feedback').delay(8000).fadeOut();
			$(".select2").selectpicker();
		});

		$(".select2").each(function (i, obj) {
			$(this).selectpicker();
		});


		function goto_check_project() {
			var po_project = $('#po_project option:selected').val();

			if (po_project != "") {
				var form_data = new FormData();
				form_data.append('po_project', po_project);

				$.ajax({
					method: 'POST',
					url: '<?php echo base_url() . "admincontrol/rfqorder/get_address_from_porject_find"; ?>',
					data: form_data,
					dataType: 'JSON',
					contentType: false,
					processData: false,
					success: function (data) {
						//alert(data.msg);
						if (data.msg == 1) {
							//console.log(data);
							//alert(data.msg[0].space_rate);
							$('.div_roller_total9').fadeOut();
							$('#po_address').val(data.s_msg.proj_address);
							$('#po_number_prefix').val(data.po_number_prefix);
							$('#po_number').val(data.po_number);

						} else {
							$('#po_address').val('');
							$('#po_number').val('');
						}
					}
				});

			} else {
				$('#po_address').val('');
				$('#po_number').val('');
			}
		}

		function goto_check_item(element) {

			var row_id = $(element).data('id');
			var supp_set = $('#po_supp option:selected').val();
//alert(supp_set);return;
			if (supp_set != "") {
				// var pk_item = $('.pk_item:eq(' + row_id + ') option:selected').val();
				var pk_item = element.value;

				if (pk_item != "") {
					var form_data = new FormData();
					form_data.append('pk_item', pk_item);
					form_data.append('supp_set', supp_set);

					$.ajax({
						method: 'POST',
						url: '<?php echo base_url() . "admincontrol/porder/get_alldetails_from_item_find"; ?>',
						data: form_data,
						dataType: 'JSON',
						contentType: false,
						processData: false,
						success: function (data) {
							//alert(data.msg);
							if (data.msg == 1) {
								//console.log(data);
								//alert(data.msg[0].space_rate);
								//$('.div_roller_total9').fadeOut();

								$('select[name="pk_ccode[]"]').eq(row_id - 1).find('option[value="'+data.s_msg.item_ccode_ms+'"]').attr("selected",true);

								$('.pk_ccode').selectpicker('refresh');

								if (data.supp_set) {
								}
								$('select[name="pk_uom[]"]').eq(row_id - 1).find('option:contains("'+data.s_msg.uom_name+'")').attr("selected",true);
								$('.pk_uom').selectpicker('refresh');

								$('[name="pk_desc[]"]').eq(row_id - 1).val(data.s_msg.item_description);

								if ("e_msg" in data) {

									// alert(data.e_msg);
									// $('.get_error_total9').html(data.e_msg);
									// $(".get_error_total9").fadeIn();
									// setTimeout(function () {
									// 	$('.invalid-feedback, .get_error_total9').fadeOut();
									// }, 5000);
								}

							} else {
								$('.pk_ccode, .pk_sku, .pk_uom, .pk_taxcode, .pk_itm_price, .pk_subtotal, .pk_tax_amt, .pk_total_amt').val('');
								$('.get_error_total9').html(data.e_msg);
								$(".get_error_total9").fadeIn();
								setTimeout(function () {
									$('.invalid-feedback, .get_error_total9').fadeOut();
								}, 5000);
							}
						}
					});

				} else {
					$('.pk_ccode, .pk_sku, .pk_uom, .pk_taxcode, .pk_itm_price, .pk_subtotal, .pk_tax_amt, .pk_total_amt').val('');
				}
			} else {
				$('.pk_item').val('');
				$('.pk_item').selectpicker('refresh');
				$('.pk_ccode, .pk_sku, .pk_uom, .pk_taxcode, .pk_itm_price, .pk_subtotal, .pk_tax_amt, .pk_total_amt').val('');
				var error_message = "Supplier is needed. please select.";
				$('.get_error_total9').html(error_message);
				$(".get_error_total9").fadeIn();
				setTimeout(function () {
					$('.invalid-feedback, .get_error_total9').fadeOut();
				}, 5000);
			}
		}

		function gotosubmit_itemset() {
			$('.div_roller_total9').fadeIn();
			var delay = 5000;
			var e_error = 0;
			var error_message = 'There have some errors plese check above, Try again.';
			var onlynumerics = /^[0-9]+$/;
			var onlynumerics_withdot = /^[0-9.]+$/;
			var alphaletters = /^[A-Za-z]+$/;

			var pk_item = $('#pk_item option:selected').val();
			var pk_sku = $('#pk_sku').val();
			var pk_uom = $('#pk_uom').val();
			var pk_taxcode = $('#pk_taxcode').val();
			var pk_itm_qnty = $('#pk_itm_qnty').val();
			var ipack_itm_no = $('#ipack_itm_no').val();

			if (ipack_itm_no == "") {
				e_error = 1;
				pk_total_amt
				error_message = error_message + "<br/>ID missing, Refresh the page";
			}

			// if (pk_sku == "") {
			// 	e_error = 1;
			// 	$('.pk_sku').html('SKU is Required.');
			// } else {
			// 	$('.pk_sku').html('');
			// }
			if (pk_uom == "") {
				e_error = 1;
				$('.pk_uom').html('UOM is Required.');
			} else {
				$('.pk_uom').html('');
			}

			if (pk_itm_qnty == "") {
				e_error = 1;
				$('.pk_itm_qnty').html('Item Quantity is Required.');
			} else {
				if (!pk_itm_qnty.match(onlynumerics)) {
					e_error = 1;
					$('.pk_itm_qnty').html('Item Quantity only use numeric values, Check again.');
				} else if (parseInt(pk_itm_qnty) <= 0) {
					e_error = 1;
					$('.pk_itm_qnty').html('Item Quantity always greater than 0, Check again.');
				} else {
					$('.pk_itm_qnty').html('');
				}
			}

			if (e_error == 1) {
				$('.div_roller_total9').fadeOut();
				$('.get_error_total9').html(error_message);
				$(".get_error_total9").fadeIn();
				$(".invalid-feedback").fadeIn();
//$(".text-error").fadeIn();
				setTimeout(function () {
					$('.invalid-feedback, .get_error_total9').fadeOut();
				}, delay);
			} else {
//alert("Reached");exit();
				var form_data = new FormData();
				form_data.append('ipack_itm_no', ipack_itm_no);
				form_data.append('pk_ccode', pk_ccode);
				form_data.append('pk_sku', pk_sku);
				form_data.append('pk_uom', pk_uom);
				form_data.append('pk_itm_qnty', pk_itm_qnty);
//form_data.append("files", files[0]);
				$.ajax({
					method: 'POST',
					url: '<?php echo base_url() . "admincontrol/porder/new_porder_item_submission"; ?>',
					data: form_data,
					dataType: 'JSON',
					contentType: false,
					processData: false,
					success: function (data) {
						//alert(data.msg);
						//console.log(data);
						var expr_string = '<tr class="expset_' + data.cat_set.po_detail_id + '">' +
								'<td>' + data.cat_set.po_detail_item + ' ' + data.cat_set.item_name + '</td> ' +
								'<td>' + data.cat_set.cc_no + '</td>' +
								'<td>' + data.cat_set.uom_name + '</td>' +
								'<td>' + data.cat_set.po_detail_unitprice + '</td>' +
								'<td>' + data.cat_set.po_detail_quantity + '</td>' +
								'<td>' + data.cat_set.po_detail_tax_group + '</td>' +
								'<td>' + data.cat_set.po_detail_subtotal + '</td>' +
								// '<td>' + data.cat_set.po_detail_taxcode + '</td>' +
								'<td>' + data.cat_set.po_detail_taxamount + '</td>' +
								// '<td>' + data.cat_set.po_detail_total + '</td>' +
								'<td><a href="javascript:;" onclick="gotodelete_items(' + data.cat_set.po_detail_id + ');"><i class="fa fa-trash text-danger"></i></a></td>' +
								'</tr>';
						if (data.msg == 1) {
							//alert(data.msg[0].space_rate);
							$('.div_roller_total9').fadeOut();
							$('.get_success_total9').html('Item is Added in the List Successfully.');
							$(".get_success_total9").fadeIn();
							$('.expr_setvalue').append(expr_string);
							var sub_total = 0;
							var total_tax = 0;

							$('.pk_subtotal').each(function () {
								sub_total += Number($(this).val());
							});

							$('.pk_tax_amt').each(function () {
								total_tax += Number($(this).val());
							});

							$('#pk_sub_total').text(sub_total);
							$('#pk_total_tax').text(total_tax);
							$('#total_po').text(sub_total + total_tax);

							var item_counter = $('#itemdtl_counter').val();
							var item_amount = $('#itemdtl_tamount').val();
							item_counter = Number(item_counter) + 1;
							item_amount = parseFloat(item_amount) + parseFloat(data.cat_set.po_detail_total);
							$('#itemdtl_counter').val(item_counter);
							$('#itemdtl_tamount').val(item_amount);
							$('#pk_ccode, #pk_sku, #pk_uom, #pk_taxcode, #pk_itm_price, #pk_itm_qnty, #pk_subtotal, #pk_tax_amt, #pk_total_amt').val('');
							$('#pk_code , ').val('');
							$('#pk_code , ').selectpicker('refresh');
							setTimeout(function () {
								$('.get_success_total9').fadeOut();
							}, 3000);

						} else {
							$('.div_roller_total9').fadeOut();
							//error_message = "There have some problem to Store Data, Try after some time.";
							error_message = data.e_msg;
							$('.get_error_total9').html(error_message);
							$(".get_error_total9").fadeIn();
							setTimeout(function () {
								$('.get_error_total9').fadeOut();
							}, delay);
						}
					}
				});
			}


		}

		function gotodelete_items(exid) {
			if (exid != "") {
				var conf_answer = confirm("You are about to Delete a record. Are you sure?")
				if (conf_answer) {
					$('.div_roller_total9').fadeIn();
					$.ajax({
						method: 'POST',
						url: '<?php echo base_url() . "admincontrol/porder/delete_itemset_update"; ?>',
						data: {
							qid: exid
						},
						dataType: 'JSON',
						success: function (data) {
							//alert(data.msg);
							if (data.msg == 1) {
								//console.log(data);
								//alert(data.msg[0].option_set);
								$('.div_roller_total9').fadeOut();
								$('.get_success_total9').html('Item is Deleted Successfully.');
								$(".get_success_total9").fadeIn();

								var item_counter = $('#itemdtl_counter').val();
								item_counter = Number(item_counter) - 1;
								$('#itemdtl_counter').val(item_counter);

								var item_amount = $('#itemdtl_tamount').val();
								item_amount = parseFloat(item_amount) - parseFloat(data.expmarks.po_detail_total);
								$('#itemdtl_tamount').val(item_amount);

								$(".expset_" + exid).remove();

								var sub_total = 0;
								var total_tax = 0;

								$('.pk_subtotal').each(function () {
									sub_total += Number($(this).val());
								});

								$('.pk_tax_amt').each(function () {
									total_tax += Number($(this).val());
								});

								$('#pk_sub_total').text(sub_total);
								$('#pk_total_tax').text(total_tax);
								$('#total_po').text(sub_total + total_tax);

								setTimeout(function () {
									$('.get_success_total9').fadeOut();
								}, 3000);
							} else {
								$('.div_roller_total9').fadeOut();
								error_message = "There have some problem to Update Data, Try again.";
								error_message = error_message + "<br/>" + data.e_msg;
								$('.get_error_total9').html(error_message);
								$(".get_error_total9").fadeIn();
								setTimeout(function () {
									$('.get_error_total9').fadeOut();
								}, 3000);
							}

						}
					});
				}
			}
		}

		function goto_add_record() {
			$('.packset_item').html('');
			var project_id=$('#po_project').val();
			getProjectPackages(project_id);
		}

		jQuery(document).delegate('a.add_row', 'click', function (e) {
			e.preventDefault();
			var size = jQuery('#porder_table >tbody >tr').length + 1;
			var content = '<tr data-id="' + size + '">\n' +
					'\t\t\t\t<td>\n' +
					'\t\t\t\t\t<select class="form-control select2 custom-select pk_item' + size + '" data-id="' + size + '" name="pk_item[]"\n' +
					'\t\t\t\t\t\t\tdata-live-search="true" data-width="110px" autocomplete="off"\n' +
					'\t\t\t\t\t\t\tonchange="goto_check_item(this);">\n' +
					'\t\t\t\t\t\t<option value="">---Select---</option>\n' +
					'\t\t\t\t\t\t<?php foreach ($itm_list as $items) { ?>\n' +
					'\t\t\t\t\t\t<option value="<?php echo $items->item_code; ?>"> <?php echo htmlspecialchars($items->item_code . ' - ' . $items->item_name, ENT_QUOTES); ?></option>\n' +
					'\t\t\t\t\t\t<?php } ?>\n' +
					'\t\t\t\t\t</select><br>\n' +
					'\t\t\t\t\t<a href="javascript:;" onclick="goto_advance_item_lookup(' + size + ');" class="btn btn-sm border border-danger " style="font-weight:bold"  data-toggle="tooltip" data-placement="top" title="Advanced Look-Up">...</a>\n' +
					'\t\t\t\t</td>\n' +
					'<td>\n' +
					'\t\t\t\t\t\t<textarea type="text" class="form-control pk_desc" name="pk_desc[]"\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t   id="pk_desc"\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t\t  placeholder="Item Description" autocomplete="off"></textarea>\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t<small\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t\tclass="invalid-feedback pk_desc_error"><?php echo form_error("pk_desc"); ?></small>\n' +
					'\t\t\t\t\t\t\t\t\t\t\t</td>' +
					'\t\t\t\t<td>\n' +
					'\t<select class="form-control select2 custom-select pk_ccode"\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t\tname="pk_ccode[]"\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t\tdata-live-search="true" autocomplete="off" data-id="1">\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="">---Select---</option>\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php foreach ($ccode_list as $items) { ?>\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="<?php echo $items->cc_id; ?>"><?php echo $items->cc_no.' - '.$items->cc_description; ?></option>\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php } ?>\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t</select>\n' +
					'\t\t\t\t\t<small class="invalid-feedback pk_ccode"><?php echo form_error("pk_ccode"); ?></small>\n' +
					'\t\t\t\t</td>\n' +
					'\t\t\t\t<td>\n' +
					'<select class="form-control select2 custom-select pk_uom"\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t\tname="pk_uom[]" id="pk_uom"\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t\tdata-live-search="true" autocomplete="off" data-id="1">\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="">---Select---</option>\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php foreach ($uom_list as $items) { ?>\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="<?php echo $items->uom_id; ?>"><?php echo $items->uom_name; ?></option>\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php } ?>\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t</select>' +
					'\t\t\t\t\t<small class="invalid-feedback pk_uom"><?php echo form_error("pk_uom"); ?></small>\n' +
					'\t\t\t\t</td>\n' +
					'\t\t\t\t<td>\n' +
					'\t\t\t\t\t<input type="number" class="form-control" name="pk_itm_qnty[]"\n' +
					'\t\t\t\t\t\t   id="pk_itm_qnty" placeholder="Enter Quantity" data-id="' + size + '" autocomplete="off"\n' +
					'\t\t\t\t\t\t   onchange="goto_check_item_amounts(this);"/>\n' +
					'\t\t\t\t\t<small class="invalid-feedback pk_itm_qnty"><?php echo form_error("pk_itm_qnty"); ?></small>\n' +
					'\t\t\t\t</td>\n' +
					'<td><a class="btn btn-outline-warning delete-record" data-id="' + size + '"><i class="fa fa-trash text-danger"></i></a></td>\n' +
					'\t\t\t</tr>';
			$('#porder_table_body').append(content);
			add_row_action(size);

			$(".select2").selectpicker();
			assign_data_id();
		});

		jQuery(document).delegate('a.delete-record', 'click', function (e) {
			e.preventDefault();
			var didConfirm = confirm("Are you sure You want to delete");
			if (didConfirm == true) {
				var id = jQuery(this).attr('data-id');
				var targetDiv = jQuery(this).attr('targetDiv');
				$('#porder_table_body tr[data-id="' + id + '"]').remove();
				for(i=0;i<jQuery('#porder_table >tbody >tr').length; i++) {
					$('#porder_table >tbody >tr:nth-child('+(i+1)+')').find('.pk_new_row').val(i);
				}
				var size = jQuery('#porder_table >tbody >tr').length;
				assign_data_id();
				add_row_action(size);
				goto_check_item_amounts(jQuery(this).element);
				return true;
			} else {
				return false;
			}
		});

		function goto_add_row() {
			$('#Modal_addrecord').modal('show');
		}

		function goto_check_item_amounts(element) {
			var row_id = $(element).data('id');
			var pk_itm_qnty = $('[name="pk_itm_qnty[]"]').eq(row_id - 1).val();
			var pk_itm_price = $('[name="pk_itm_price[]"]').eq(row_id - 1).val();
			var pk_taxcode = $('[name="pk_tax_group[]"] option:selected').eq(row_id - 1).val();
			var pre_tax_amt = $('[name="pre_tax_amt[]"]').eq(row_id - 1).val();
			var tax_amt = $('[name="pk_taxcode[]"]').eq(row_id - 1).val();
			var tax_amount = 0;

			var subtotal_amount = parseFloat(pk_itm_qnty) * parseFloat(pk_itm_price);

			if (isNaN((subtotal_amount))) {
				return subtotal_amount += 0
			}

			if (pk_itm_qnty != "" && pk_itm_price != "" && pk_taxcode != "") {
				tax_amount = (parseFloat(subtotal_amount) / 100) * parseFloat(tax_amt);
			} else {
				tax_amount = 0;
			}

			var total_subamount = 0;
			var total_tax = 0;
			var total_amount = 0;

			if (pk_itm_qnty != "" && pk_itm_price != "") {
				var alltotal = parseFloat(subtotal_amount) + parseFloat(tax_amount);
				$('[name="pk_subtotal[]"]').eq(row_id - 1).val(subtotal_amount);
				$('[name="pre_tax_amt[]"]').eq(row_id - 1).val(parseFloat(pk_itm_price) * parseFloat(pk_itm_qnty));
				$('[name="pk_tax_amt[]"]').eq(row_id - 1).val(tax_amount);
				$('[name="pk_total_amt[]"]').eq(row_id - 1).val(alltotal);
				$('#pk_total_tax').text(parseFloat($('#pk_total_tax').text()) + tax_amount);

				$('input[name="pk_subtotal[]"]').map(function () {
					if (isNaN(parseFloat(this.value))) {
						return total_subamount += 0
					} else {
						return total_subamount += parseFloat(this.value); // $(this).val()
					}

				}).get();
				$('input[name="pk_tax_amt[]"]').map(function () {
					if (isNaN(parseFloat(this.value))) {
						return total_tax += 0
					} else {
						return total_tax += parseFloat(this.value); // $(this).val()
					}
				}).get();
				$('input[name="pk_total_amt[]"]').map(function () {
					if (isNaN(parseFloat(this.value))) {
						return total_amount += 0
					} else {
						return total_amount += parseFloat(this.value); // $(this).val()
					}
				}).get();

				$('#pk_sub_total').text(total_subamount);
				$('#pk_total_tax').text(total_tax);
				$('#total_po').text(total_amount);
				$('#itemdtl_tamount').val(total_amount);
			} else {
				$('[name="pk_subtotal[]"]').eq(row_id - 1).val('');
				$('[name="pk_tax_amt[]"]').eq(row_id - 1).val('');
				$('[name="pk_total_amt[]"]').eq(row_id - 1).val('');
				$('[name="pre_tax_amt[]"]').eq(row_id - 1).val('');
			}
		}


		function goto_check_package() {
			var itmpk_id = $('#itmpk_id option:selected').val();
			var supp_set = $('#po_supp option:selected').val();

			if (itmpk_id != "" && supp_set != "") {
				var form_data = new FormData();
				form_data.append('itmpk_id', itmpk_id);

				$.ajax({
					method: 'POST',
					url: '<?php echo base_url() . "admincontrol/porder/get_packageitems_from_package_find"; ?>',
					data: form_data,
					dataType: 'JSON',
					contentType: false,
					processData: false,
					success: function (data) {
						//alert(data.msg);
						if (data.msg == 1) {
							//console.log(data);
							//alert(data.msg[0].space_rate);
							var mixstring = '<div class="col-sm-12"><input type="hidden" name="totalitem_pkg" id="totalitem_pkg" value="' + data.s_msg.length + '" /><table class="table"><thead><tr><th>Item Name</th><th>Item Code</th><th>Item Quantity</th></tr></thead><tbody>';
							var ii = 0;
							for (ii = 0; ii < data.s_msg.length; ii++) {
								mixstring = mixstring + '<tr><td>' + data.s_msg[ii].item_name + '</td><td>' + data.s_msg[ii].ipdetail_item_ms + '</td><td><input type="hidden" name="ipackitem_code_' + ii + '" id="ipackitem_code_' + ii + '" value="' + data.s_msg[ii].ipdetail_item_ms + '" /><input type="text" name="ipackitem_qty_' + ii + '" id="ipackitem_qty_' + ii + '" value="' + data.s_msg[ii].ipdetail_quantity + '" /></td></tr>';
							}
							mixstring = mixstring + '</tbody></table></div>';
							$('.packset_item').html(mixstring);

						} else {
							var error_message = data.e_msg;
							toastr.error(error_message, 'Error!');
							$('#packset_item').html('');
						}
					}
				});

			} else {
				$('.div_roller_total2').fadeOut();
				var error_message = "Supplier need to Select First.";
				toastr.error(error_message, 'Error!');
				$('#packset_item').html('');
			}
		}

		function add_row_action(size) {
			if(size >= 2) {
				$('#porder_table >tbody >tr:first-child >td:last-child').html('<a class="btn btn-outline-warning delete-record" data-id="1"><i class="fa fa-trash text-danger"></i></a>');
			} else {
				$('#porder_table >tbody >tr >td:last-child').html('');

			}
		}

		function assign_data_id() {
			$('#porder_table tr').each(function(index_m) {
				$(this).attr('data-id', index_m );
				$(this).find('td:last-child').html('<a class="btn btn-outline-warning delete-record" data-id="'+index_m+'"><i class="fa fa-trash text-danger"></i></a>');
				// console.log( $(this).html() )
				$(this).find('input').each(function() {
					$(this).attr('data-id',index_m);
				});
				$(this).find('textarea').each(function() {
					$(this).attr('data-id',index_m);
				});
				$(this).find('select').each(function() {
					$(this).attr('data-id',index_m);
				});
			});

		}

		function goto_submit_record() {
			$('.div_roller_total2').fadeIn();
// $('#submit_record_btn').prop('disabled', true);

			var delay = 8000;
			var e_error = 0;
			var error_message = 'There have some errors plese check above, Try again.';
			var alphaletters_spaces = /^[A-Za-z ]+$/;
			var alphaletters = /^[A-Za-z]+$/;
			var alphanumerics = /^[A-Za-z0-9/() ]+$/;
			var alphanumerics_spaces = /^[A-Za-z0-9_.,\- ]+$/;
			var alphanumerics_no = /^[A-Za-z0-9_/&(@):.,\- ]+$/;
			var onlynumerics = /^[0-9]+$/;
			var onlynumerics_withdot = /^[0-9.]+$/;
			var specials_char = /[~`!#$%\^&*+=\[\]\\';./{}()|\\":<>\?]/g;
			var emailpattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;

			var totalitem_pkg = $('#totalitem_pkg').val();
			var supp_set = $('#po_supp option:selected').val();
			var name_supp_cp = $('#name_supp_cp').val();
			var supp_phone = $('#supp_phone').val();
			var supp_email = $('#supp_email').val();
			var supp_address = $('#supp_address').val();
			var ipack_itm_no = $('#ipack_itm_no').val();

			if (ipack_itm_no == "") {
				e_error = 1;
				error_message = error_message + "<br/>ID missing, Refresh the page";
			}

			if (totalitem_pkg == "" || parseInt(totalitem_pkg) == 0) {
				e_error = 1;
				error_message = error_message + '<br/>Item is Missing, Check Again.';
			}

			if (supp_set == "") {
				e_error = 1;
				error_message = error_message + '<br/>Supplier is Missing, Check Again.';
			}

			if (parseInt(totalitem_pkg) > 0) {

				var ii = 0;
				for (ii = 0; ii < parseInt(totalitem_pkg); ii++) {
					var itmcode = $("input[name='ipackitem_code_" + ii + "']").val();
					var itmqty = $("input[name='ipackitem_qty_" + ii + "']").val();

					if (itmcode == "") {
						e_error = 1;
						error_message = error_message + '<br/>Item ID is Missing, Check Again.';
					}
					if (itmqty == "") {
						e_error = 1;
						$('.ipackitem_qty_' + ii).html('Quantity is Required');
					} else {
						if (!itmqty.match(onlynumerics)) {
							e_error = 1;
							$('.ipackitem_qty_' + ii).html('Quantity use only Numeric Value');
						} else if (parseInt(itmqty) <= 0) {
							e_error = 1;
							$('.ipackitem_qty_' + ii).html('Quantity always greater than 0');
						} else {
							$('.ipackitem_qty_' + ii).html('');
						}
					}

				}

			}

			if (e_error == 1) {
				$('.div_roller_total2').fadeOut();
				$('#submit_record_btn').prop('disabled', false);
				$('.close_modal').show();
//$('.get_error_total').html(error_message);
//$(".get_error_total").fadeIn();
				toastr.error(error_message, 'Error!');
				$(".invalid-feedback").fadeIn();
				/*e_error = 0;
				error_message = '';*/
				setTimeout(function () {
					$('.invalid-feedback, .get_error_total2').fadeOut();
				}, delay);
			} else {

				var form_data = new FormData();
				form_data.append("totalitem_pkg", totalitem_pkg);
				form_data.append("supp_set", supp_set);

				form_data.append("ipack_itm_no", ipack_itm_no);
				for (ii = 0; ii < parseInt(totalitem_pkg); ii++) {
					var itmcode = $("input[name='ipackitem_code_" + ii + "']").val();
					var itmqty = $("input[name='ipackitem_qty_" + ii + "']").val();

					var itmprice = $("input[name='ipackitem_price_" + ii + "']").val();
					form_data.append('itmcode[]', itmcode);
					form_data.append('itmqty[]', itmqty);
					form_data.append('itmprice[]', itmprice);

				}
				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/porder/add_multiple_items_from_package_sets') ?>",
					dataType: 'json',
					data: form_data,
					contentType: false,
					cache: true,
					processData: false,
					success: function (data) {
						//alert(data.msg);
						if (data.msg == 1) {
							console.log(data);
							//alert(data.msg[0].space_rate);
							$('.div_roller_total2').fadeOut();
							$('#packset_item').html('');
							$('#Modal_addrecord').modal('hide');
							//toastr.success('Item is Added Successfully!', 'Success');
							$('.get_success_total9').html('Item is Added in the List Successfully.');
							$(".get_success_total9").fadeIn();
							$('.expr_setvalue').append(data.s_msg);

							$('#itemdtl_counter').val(data.titem);
							$('#itemdtl_tamount').val(data.tamount);

							var itemCount=1;
							var size=1;
							data.s_msg.forEach(function (item) {
								// alert(item.po_detail_item);

									if(itemCount==1 && $('select[name="pk_item[]"]').val()==''){
									size = jQuery('#porder_table >tbody >tr').length;
									$('#porder_table_body tr[data-id=' + size + ']').find('textarea').val(item.item_description);
									$('#porder_table_body tr[data-id=' + size + ']').find('select[name="pk_item[]"] option[value=' + item.po_detail_item + ']').attr('selected', 'selected');
									$('#porder_table_body tr[data-id=' + size + ']').find('select[name="pk_ccode[]"] option[value=' + item.po_detail_cost_code + ']').attr('selected', 'selected');
									$('#porder_table_body tr[data-id=' + size + ']').find('select[name="pk_uom[]"] option[value=' + item.porder_detail_uom + ']').attr('selected', 'selected');
									$('#porder_table_body tr[data-id=' + size + ']').find('input[name="pk_itm_qnty[]"]').val(item.po_detail_quantity).trigger('change');
									itemCount++;
								}else{
									size = jQuery('#porder_table >tbody >tr').length+1;
									var content = '<tr data-id="' + size + '">\n' +
										'\t\t\t\t<td>\n' +
										'\t\t\t\t\t<select class="form-control select2 custom-select pk_item' + size + '" data-id="' + size + '" name="pk_item[]"\n' +
										'\t\t\t\t\t\t\tdata-live-search="true" data-width="110px" autocomplete="off"\n' +
										'\t\t\t\t\t\t\tonchange="goto_check_item(this);">\n' +
										'\t\t\t\t\t\t<option value="">---Select---</option>\n' +
										'\t\t\t\t\t\t<?php foreach ($itm_list as $items) { ?>\n' +
										'\t\t\t\t\t\t<option value="<?php echo $items->item_code; ?>" > <?php echo htmlspecialchars($items->item_code . ' - ' . $items->item_name, ENT_QUOTES); ?></option>\n' +
										'\t\t\t\t\t\t<?php } ?>\n' +
										'\t\t\t\t\t</select><br>\n' +
					'\t\t\t\t\t<a href="javascript:;" onclick="goto_advance_item_lookup(' + size + ');" class="btn btn-sm border border-danger " style="font-weight:bold"  data-toggle="tooltip" data-placement="top" title="Advanced Look-Up">...</a>\n' +
										'\t\t\t\t</td>\n' +
										'<td>\n' +
										'\t\t\t\t\t\t<textarea type="text" class="form-control pk_desc" name="pk_desc[]"\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t   id="pk_desc"\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t\t  placeholder="Item Description" autocomplete="off"></textarea>\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t<small\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t\tclass="invalid-feedback pk_desc_error"><?php echo form_error("pk_desc"); ?></small>\n' +
										'\t\t\t\t\t\t\t\t\t\t\t</td>' +
									'<td>\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t<select class="form-control select2 custom-select pk_ccode"\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t\tname="pk_ccode[]"\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t\tdata-live-search="true" autocomplete="off" data-id="1">\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="">---Select---</option>\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php foreach ($ccode_list as $items) { ?>\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="<?php echo $items->cc_id; ?>"><?php echo $items->cc_no.' - '.$items->cc_description; ?></option>\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php } ?>\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t</select>\n' +
										'\n'+
										'\t\t\t\t\t\t\t\t\t\t\t</td>\n'+
										'\n'+
										'\t\t\t\t\t\t\t\t\t\t\t<td>\n'+
										'\t\t\t\t\t\t\t\t\t\t\t\t<select class="form-control select2 custom-select pk_uom"\n'+
										'\t\t\t\t\t\t\t\t\t\t\t\t\t\tname="pk_uom[]" id="pk_uom"\n'+
										'\t\t\t\t\t\t\t\t\t\t\t\t\t\tdata-live-search="true" autocomplete="off" data-id="1">\n'+
										'\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="">---Select---</option>\n'+
										'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php foreach ($uom_list as $items) { ?>\n'+
										'\t\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="<?php echo $items->uom_id; ?>"><?php echo $items->uom_name; ?></option>\n'+
										'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php } ?>\n'+
										'\t\t\t\t\t\t\t\t\t\t\t\t</select>\n'+
										'\t\t\t\t\t\t\t\t\t\t\t</td>'+
										'\t\t\t\t<td>\n' +
										'\t\t\t\t\t<input type="number" class="form-control" name="pk_itm_qnty[]"\n' +
										'\t\t\t\t\t\t   id="pk_itm_qnty" placeholder="Enter Quantity" data-id="' + size + '" autocomplete="off"\n' +
										'\t\t\t\t\t\t   onchange="goto_check_item_amounts(this);"/>\n' +
										'\t\t\t\t</td>\n' +
										'<td><a class="btn btn-outline-warning delete-record" data-id="' + size + '"><i class="fa fa-trash text-danger"></i></a></td>\n' +
										'\t\t\t</tr>';


									$('#porder_table_body').append(content);
									alert(size);
									$('#porder_table_body tr[data-id=' + size + ']').find('textarea').val(item.item_description);
									$('#porder_table_body tr[data-id=' + size + ']').find('select[name="pk_item[]"] option[value=' + item.po_detail_item + ']').attr('selected', 'selected');
									$('#porder_table_body tr[data-id=' + size + ']').find('select[name="pk_ccode[]"] option[value=' + item.po_detail_cost_code + ']').attr('selected', 'selected');
									$('#porder_table_body tr[data-id=' + size + ']').find('select[name="pk_uom[]"] option[value=' + item.porder_detail_uom + ']').attr('selected', 'selected');
									$('#porder_table_body tr[data-id=' + size + ']').find('input[name="pk_itm_qnty[]"]').val(item.po_detail_quantity).trigger('change');
									itemCount++;
								}


								$('select').selectpicker('refresh');
								assign_data_id();
								add_row_action(size);
							});
							setTimeout(function () {
								$('.get_success_total9').fadeOut();
							}, 3000);

						} else {
							$('.div_roller_total2').fadeOut();
							$('#submit_record_btn').prop('disabled', false);
							$('.close_modal').show();
							error_message = data.e_msg;
							toastr.error(error_message, 'Error!');
							//$('.get_error_total').html(error_message);
							//$(".get_error_total").fadeIn();
							//setTimeout(function(){ $('.get_error_total').fadeOut(); }, delay);
						}

					}
				});
			}

		}


		function gotoclclickbutton(status) {
			$('.div_roller_total').fadeIn();

			var size = jQuery('#porder_table >tbody >tr').length;
			$('#itemdtl_counter').val(size);

			var total_amount = $('input[name="itemdtl_tamount"]').val();

			var delay = 8000;
			var e_error = 0;
			var error_message = 'There have some errors plese check above, Try again.';
			var alphaletters_spaces = /^[A-Za-z ]+$/;
			var alphaletters = /^[A-Za-z]+$/;
			var alphanumerics = /^[A-Za-z0-9/() ]+$/;
			var alphanumerics_spaces = /^[A-Za-z0-9_,.\- ]+$/;
			var alphanumerics_no = /^[A-Za-z0-9_/&(@):.,\- ]+$/;
			var onlynumerics = /^[0-9]+$/;
			var specials_char = /[~`!#$%\^&*+=\[\]\\';./{}()|\\":<>\?]/g;
			var emailpattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;
			var onlynumerics_withdot = /^[0-9.]+$/;
			var ipack_itm_no = $('#ipack_itm_no').val();
			var itemdtl_counter = $('#itemdtl_counter').val();
			var itemdtl_tamount = $('#itemdtl_tamount').val();
			var po_project = $('#po_project option:selected').val();
			var po_number = $('#po_number').val();
			var po_number_prefix = $('#po_number_prefix').val();
			var po_supp = $('#po_supp option:selected').val();
			var po_address = $('#po_address').val();
			var po_delivery_date = $('#po_delivery_date').val();
			var po_desc = $('#po_desc').val();
			var po_dl_note = $('#po_dl_note').val();
//var ap_quaran = $("input[name='ap_quaran']:checked").val();

			var pk_item = $('select[name="pk_item[]"] option:selected').map(function () {
				return this.value; // $(this).val()
			}).get();

			var pk_ccode = $('select[name="pk_ccode[]"] option:selected').map(function () {
				return this.value; // $(this).val()
			}).get();

			var pk_uom = $('select[name="pk_uom[]"] option:selected').map(function () {
				return this.value; // $(this).val()
			}).get();
			var pk_desc = $('textarea[name="pk_desc[]"]').map(function () {
				return this.value; // $(this).val()
			}).get();

			var pk_itm_qnty = $('input[name="pk_itm_qnty[]"]').map(function () {
				return this.value; // $(this).val()
			}).get();

			var pk_total_amt = $('input[name="pk_total_amt[]"]').map(function () {
				return this.value; // $(this).val()
			}).get();


			if (ipack_itm_no == "") {
				e_error = 1;
				error_message = error_message + "<br/>ID missing, Refresh the page";
			}

			if (pk_itm_qnty == "") {
				e_error = 1;
				$('.pk_itm_qnty').html('Item Quantity is Required.');
			} else {
				$('.pk_itm_qnty').html('');
			}


			if (ipack_itm_no == "") {
				e_error = 1;
				error_message = error_message + "<br/>ID missing, Refresh the page";
			}

			if (itemdtl_counter == "" || itemdtl_counter <= 0) {
				e_error = 1;
				error_message = error_message + "<br/>Item not found in the Purchase Order, Add some Item.";
			}

			if (po_project == "") {
				e_error = 1;
				$('.po_project').html('Project is Required.');
			} else {
				if (!po_project.match(onlynumerics)) {
					e_error = 1;
					$('.po_project').html('Project only use Numeric value, Check again.');
				} else {
					$('.po_project').html('');
				}
			}
			if (po_number == "") {
				e_error = 1;
				$('.po_number').html('Purchase Order No. is Required.');
			} else {
				if (!po_number.match(alphanumerics_spaces)) {
					e_error = 1;
					$('.po_number').html('Purchase Order No. not use special carecters [without _ . , -], Check again.');
				} else {
					$('.po_number').html('');
				}
			}
			if (po_supp == "") {
				e_error = 1;
				$('.po_supp').html('Project is Required.');
			} else {
				if (!po_supp.match(onlynumerics)) {
					e_error = 1;
					$('.po_supp').html('Project only use Numeric value, Check again.');
				} else {
					$('.po_supp').html('');
				}
			}
			if (po_address == "") {
				e_error = 1;
				$('.po_address').html('Delivery Address is Required.');
			} else {
				$('.po_address').html('');
			}

			if (po_delivery_date == "") {
				e_error = 1;
				$('.po_delivery_date').html('Delivery Date is Required.');
			} else {
				$('.po_delivery_date').html('');
			}

			if (po_dl_note != "") {
				if (!po_dl_note.match(alphanumerics_no)) {
					e_error = 1;
					$('.po_dl_note').html('Delivery Note not use special carecters [without _ / : ( @ . & ) , -], Check again.');
				} else {
					$('.po_dl_note').html('');
				}
			}

			if (po_desc != "") {
				$('.po_desc').html('');
			} else {
				e_error = 1;
				$('.po_desc').html('PO Description is required');
			}



//alert(pr_user);return;
			if (e_error == 1) {
				$('.div_roller_total').fadeOut();
				$('.get_error_total').html(error_message);
				$(".get_error_total").fadeIn();
				$(".invalid-feedback").fadeIn();
				/*e_error = 0;
				error_message = '';*/
				setTimeout(function () {
					$('.invalid-feedback, .get_error_total').fadeOut();
				}, delay);
			} else {

				var form_data = new FormData();
				form_data.append("ipack_itm_no", ipack_itm_no);
				form_data.append("itemdtl_counter", itemdtl_counter);
				form_data.append("itemdtl_tamount", itemdtl_tamount);
				form_data.append("po_project", po_project);
				form_data.append("po_number", po_number);
				form_data.append("po_number_prefix", po_number_prefix);
				form_data.append("po_supp", po_supp);

				form_data.append("po_address", po_address);
				form_data.append("po_delivery_date", po_delivery_date);
				form_data.append("po_desc", po_desc);
				form_data.append("po_dl_note", po_dl_note);
				form_data.append('pk_ccode[]', JSON.stringify(pk_ccode));
				form_data.append('pk_item[]', JSON.stringify(pk_item));
				form_data.append('pk_uom[]', JSON.stringify(pk_uom));
				form_data.append('pk_desc[]', JSON.stringify(pk_desc));
				form_data.append('pk_itm_qnty[]', JSON.stringify(pk_itm_qnty));
				form_data.append("po_type", 'Material RPO');
				form_data.append("po_status", status);

				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/rfqorder/new_rfq_Set_submission') ?>",
					dataType: 'json',
					data: form_data,
					contentType: false,
					cache: true,
					processData: false,
					success: function (data) {
						if (data.msg == 1) {
							console.log(data);
							// alert(JSON.stringify(data.e_msg));
							$('.div_roller_total').fadeOut();
							toastr.success('Record is Inserted Successfully!', 'Success');
							setTimeout(function () {
								window.location.replace("<?php echo site_url('admincontrol/rfqorder/all_rfq_list') ?>");
							}, 2000);

						} else {
							$('.div_roller_total').fadeOut();
							$('#submit_record_btn').prop('disabled', false);
							$('.close_modal').show();
							error_message = data.e_msg;
							// alert(error_message);
							toastr.error(error_message, 'Error!');
							//$('.get_error_total').html(error_message);
							//$(".get_error_total").fadeIn();
							//setTimeout(function(){ $('.get_error_total').fadeOut(); }, delay);
						}

					},
					error: function (error) {
						console.log(error);
					},
					//complete: function (error) {
					//	toastr.success('Record is Inserted Successfully!', 'Success');
					//	setTimeout(function () {
					//		window.location.replace("<?php //echo site_url('admincontrol/porder/all_purchase_order_list') ?>//");
					//	}, 2000);
					//}
				});
			}
//$("#myForm").submit();


		}

		function goto_advance_item_lookup(element){
			itemIds=[];
			$('#hidden-item-id').val(element);
			$('#Modal_advanceitemlookup').modal('show');
		}

		function getItemMasterItemsdata(){
			$('#itemmaster_search_table').DataTable({
				ajax:{
					url: '<?php echo base_url("admincontrol/porder/get_itemmaster_list")?>',
					type : "post",
					data :{
						category : $('#search_category').val(),
						cc : $('#search_cc').val()
					}
				},
				bDestroy : true
			});
		}

		function getSupplierCatelogItemsdata(){
			$('#supplierc_search_table').DataTable({
				ajax:{
					url: '<?php echo base_url("admincontrol/porder/get_suppliercatalog_list")?>',
					type : "post",
					data :{
						category : $('#search_category').val(),
						cc : $('#search_cc').val(),
						supplier : $('#po_supp').val()
					}
				},
				bDestroy : true
			});
		}

		getItemMasterItemsdata();
		getSupplierCatelogItemsdata();

		$('#search_category').change(function(){
			getItemMasterItemsdata();
			getSupplierCatelogItemsdata();
		});

		$('#search_cc').change(function(){
			getItemMasterItemsdata();
			getSupplierCatelogItemsdata();
		});

		$('#po_supp').change(function(){
			getSupplierCatelogItemsdata();
		});

		// $('#itemmaster_search_table,#supplierc_search_table').DataTable();

		$('input:radio[name="itemfrom"]').change(function(){
			var value=$(this).val();
			if(value==1){
				$('.item_row').css('display','block');
				$('.supc_row').css('display','none');
			}else{
				$('.item_row').css('display','none');
				$('.supc_row').css('display','block');
			}
		});

		// $('#supplierc_search_table,#itemmaster_search_table').on('click','.select-search-item',function(){
		// 	var itemFrom=$('input:radio[name="itemfrom"]:checked').val();
		// 	var id=$(this).attr('id');
		// 	var index=id.match(/\d+/);
		// 	var itemId=$('#hidden-item-id').val();
		// 	if(itemFrom==1){
		// 		var itemCode=$('#search-item-code1'+index).val();
		// 	}else{
		// 		var itemCode=$('#search-item-code2'+index).val();
		// 	}
		// 	$('.pk_item'+itemId).val(itemCode).change();
		// 	$('#Modal_advanceitemlookup').modal('hide');
		// });
		var itemIds=new Array();
		var itemValues=new Array();
		jQuery(document).delegate('.search-item','click',function(){
			if($(this).is(':checked')){
				itemIds.push($(this).attr('id'));
				itemValues.push($(this).val());
			}else{
				itemIds.splice( $.inArray($(this).attr('id'), itemIds), 1 );
				itemvalues.splice( $.inArray($(this).val(), itemValues), 1 );
			}
		});

		$('#select_multiple_items_btn').click(function(){
			// var itemCount=1;
			var itemFrom=$('input:radio[name="itemfrom"]:checked').val();
			// $('.search-item').each(function(){
			// 	if($(this).is(':checked')){
				for(itemCount=0;itemCount<itemIds.length;itemCount++){
					if(itemCount==0){
						var itemId=$('#hidden-item-id').val();
						var id=itemIds[itemCount];
						var index=id.match(/\d+/);
						var itemCode=itemValues[itemCount];
						$('.pk_item'+itemId).val(itemCode).change();
					}else{
						var size = jQuery('#porder_table >tbody >tr').length + 1;
						var content = '<tr data-id="' + size + '">\n' +
								'\t\t\t\t<td>\n' +
								'\t\t\t\t\t<select class="form-control select2 custom-select pk_item' + size + '" data-id="' + size + '" name="pk_item[]"\n' +
								'\t\t\t\t\t\t\tdata-live-search="true" data-width="110px" autocomplete="off"\n' +
								'\t\t\t\t\t\t\tonchange="goto_check_item(this);">\n' +
								'\t\t\t\t\t\t<option value="">---Select---</option>\n' +
								'\t\t\t\t\t\t<?php foreach ($itm_list as $items) { ?>\n' +
								'\t\t\t\t\t\t<option value="<?php echo $items->item_code; ?>"> <?php echo htmlspecialchars($items->item_code . ' - ' . $items->item_name, ENT_QUOTES); ?></option>\n' +
								'\t\t\t\t\t\t<?php } ?>\n' +
								'\t\t\t\t\t</select><br>\n' +
								'\t\t\t\t\t<a href="javascript:;" onclick="goto_advance_item_lookup(' + size + ');" class="btn btn-sm border border-danger " style="font-weight:bold"  data-toggle="tooltip" data-placement="top" title="Advanced Look-Up">...</a>\n' +
								'\t\t\t\t</td>\n' +
								'<td>\n' +
								'\t\t\t\t\t\t<textarea type="text" class="form-control pk_desc" name="pk_desc[]"\n' +
								'\t\t\t\t\t\t\t\t\t\t\t\t\t   id="pk_desc"\n' +
								'\t\t\t\t\t\t\t\t\t\t\t\t\t\t  placeholder="Item Description" autocomplete="off"></textarea>\n' +
								'\t\t\t\t\t\t\t\t\t\t\t\t<small\n' +
								'\t\t\t\t\t\t\t\t\t\t\t\t\t\tclass="invalid-feedback pk_desc_error"><?php echo form_error("pk_desc"); ?></small>\n' +
								'\t\t\t\t\t\t\t\t\t\t\t</td>' +
								'\t\t\t\t<td>\n' +
								'\t<select class="form-control select2 custom-select pk_ccode"\n' +
								'\t\t\t\t\t\t\t\t\t\t\t\t\t\tname="pk_ccode[]"\n' +
								'\t\t\t\t\t\t\t\t\t\t\t\t\t\tdata-live-search="true" autocomplete="off" data-id="1">\n' +
								'\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="">---Select---</option>\n' +
								'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php foreach ($ccode_list as $items) { ?>\n' +
								'\t\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="<?php echo $items->cc_id; ?>"><?php echo $items->cc_no.' - '.$items->cc_description; ?></option>\n' +
								'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php } ?>\n' +
								'\t\t\t\t\t\t\t\t\t\t\t\t</select>\n' +
								'\t\t\t\t\t<small class="invalid-feedback pk_ccode"><?php echo form_error("pk_ccode"); ?></small>\n' +
								'\t\t\t\t</td>\n' +
								'\t\t\t\t<td>\n' +
								'<select class="form-control select2 custom-select pk_uom"\n' +
								'\t\t\t\t\t\t\t\t\t\t\t\t\t\tname="pk_uom[]" id="pk_uom"\n' +
								'\t\t\t\t\t\t\t\t\t\t\t\t\t\tdata-live-search="true" autocomplete="off" data-id="1">\n' +
								'\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="">---Select---</option>\n' +
								'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php foreach ($uom_list as $items) { ?>\n' +
								'\t\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="<?php echo $items->uom_id; ?>"><?php echo $items->uom_name; ?></option>\n' +
								'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php } ?>\n' +
								'\t\t\t\t\t\t\t\t\t\t\t\t</select>' +
								'\t\t\t\t\t<small class="invalid-feedback pk_uom"><?php echo form_error("pk_uom"); ?></small>\n' +
								'\t\t\t\t</td>\n' +
								'\t\t\t\t<td>\n' +
								'\t\t\t\t\t<input type="number" class="form-control" name="pk_itm_qnty[]"\n' +
								'\t\t\t\t\t\t   id="pk_itm_qnty" placeholder="Enter Quantity" data-id="' + size + '" autocomplete="off"\n' +
								'\t\t\t\t\t\t   onchange="goto_check_item_amounts(this);"/>\n' +
								'\t\t\t\t\t<small class="invalid-feedback pk_itm_qnty"><?php echo form_error("pk_itm_qnty"); ?></small>\n' +
								'\t\t\t\t</td>\n' +
								'<td><a class="btn btn-outline-warning delete-record" data-id="' + size + '"><i class="fa fa-trash text-danger"></i></a></td>\n' +
								'\t\t\t</tr>';
						$('#porder_table_body').append(content);
						add_row_action(size);
						assign_data_id();
						var id=itemIds[itemCount];
						var index=id.match(/\d+/);
						var itemCode=itemValues[itemCount];
						$('.pk_item'+size).val(itemCode).change();
					}
					// itemCount++;
				}
			// });
		// 	var itemFrom=$('input:radio[name="itemfrom"]:checked').val();
		// 	var id=$(this).attr('id');
		// 	var index=id.match(/\d+/);
		// 	var itemId=$('#hidden-item-id').val();
		// 	if(itemFrom==1){
		// 		var itemCode=$('#search-item-code1'+index).val();
		// 	}else{
		// 		var itemCode=$('#search-item-code2'+index).val();
		// 	}
		// 	$('.pk_item'+itemId).val(itemCode).change();
			$('#Modal_advanceitemlookup').modal('hide');
			$('.search-item').prop('checked', false); 
		});

		// $('input:radio[name="itemfrom"]').change(function(){
		// 	$('.search-item').prop('checked', false); 
		// });

		function getProjectPackages(projectId){
			if(projectId!=''){
				// alert(projectId);
				var form_data = new FormData();
				form_data.append("project_id", projectId);

				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/porder/get_project_packages') ?>",
					dataType: 'json',
					data: form_data,
					contentType: false,
					cache: true,
					processData: false,
					success: function (data) {
						if (data.msg == 1) {
							console.log(data);
							// alert(JSON.stringify(data.pk_msg));
							var pkLength= data.pk_msg.length;
							$('#itmpk_id').html('');
							$('#itmpk_id').append('\
							<option value=""></option>\
							');
							for(i=0;i<pkLength;i++){
							$('#itmpk_id').append('\
							<option value="'+data.pk_msg[i]['ipack_id']+'">'+data.pk_msg[i]['ipack_name']+'</option>\
							');
							}
							$('#itmpk_id').selectpicker('refresh');
							$('#Modal_addrecord').modal('show');
						} else {
						}

					},
					error: function (error) {
						console.log(error);
						alert('No package Found.');
					},
					//complete: function (error) {
					//	toastr.success('Record is Inserted Successfully!', 'Success');
					//	setTimeout(function () {
					//		window.location.replace("<?php //echo site_url('admincontrol/porder/all_purchase_order_list') ?>//");
					//	}, 2000);
					//}
				});
			}else{
				alert('Please Select Project First.');
			}
		}
	</script>
