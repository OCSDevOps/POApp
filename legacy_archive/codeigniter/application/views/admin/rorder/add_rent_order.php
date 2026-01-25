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
				<h4 class="page-title">Add New Rental Order</h4>
				<div class="ml-auto text-right">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a
										href="<?php echo base_url('admincontrol/dashboard'); ?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Add Rental Order</li>
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
							<label for="fname" class="col-sm-3 text-right control-label col-form-label">P.O.
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

							<label for="cono1" class="col-sm-3 text-right control-label col-form-label">PO
								Description</label>
							<div class="col-sm-3">
			<textarea class="form-control" name="po_desc" id="po_desc"
					  placeholder="Enter PO Description" autocomplete="off"></textarea>
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
							<label for="cono1" class="col-sm-2 text-right control-label col-form-label">Delivery Date</label>
							<div class="col-sm-3">
								<input class="form-control" type="date" name="po_delivery_date" id="po_delivery_date"
									   placeholder="Enter Delivery Date" autocomplete="off">
								<small class="invalid-feedback po_delivery_date"><?php echo form_error('po_delivery_date'); ?></small>
							</div>
							<label for="cono1" class="col-sm-3 text-right control-label col-form-label">Bill To</label>
							<div class="col-sm-3">
								<input class="form-control" type="text" readonly name="po_billing_name" id="po_billing_name"
									   placeholder="Enter Billing Name" autocomplete="off">
								<small class="invalid-feedback po_billing_name"><?php echo form_error('po_billing_name'); ?></small>

								<textarea class="form-control mt-3" type="text" name="po_billing_address" id="po_billing_address"
										  placeholder="Enter Billing Address" readonly autocomplete="off"></textarea>
								<small class="invalid-feedback po_billing_address"><?php echo form_error('po_billing_address'); ?></small>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-sm-12">
<!--								<a href="javascript:;" onclick="goto_add_record();" class="btn btn-primary mb-2"-->
<!--								   style="margin-right: 10px;">Add New Item From Package</a>-->
								<!--								<a href="javascript:;" class="btn btn-primary mb-2" id="itemtbutton"-->
								<!--								   onclick="gotosubmit_itemset();" disabled>Add Item</a>-->
								<a href="javascript:;" class="btn btn-primary mb-2 add_row" id="itemtbutton">Add
									Item</a>
							</div>
						</div>
						<div class="form-group row justify-content-md-center">
							<div class="col-sm-12">
								<div class="table-responsive">
									<table width="100%" class="table table-bordered" id="porder_table">
										<thead>
										<tr>
											<th style="min-width:120px!important;overflow:hidden;word-wrap:break-word;white-space: normal;vertical-align:middle">Item</th>
											<th style="min-width:260px!important;overflow:hidden;word-wrap:break-word;white-space: normal;vertical-align:middle">Description</th>
											<th style="min-width:120px!important;overflow:hidden;word-wrap:break-word;white-space: normal;vertical-align:middle">CostCode</th>
											<!--														<th>SKU</th>-->
											<th style="min-width:90px!important;overflow:hidden;word-wrap:break-word;white-space: normal;vertical-align:middle">UOM</th>
											<!--														<th>Tax Code</th>-->
											<th style="min-width:120px!important;overflow:hidden;word-wrap:break-word;white-space: normal;vertical-align:middle">Quantity</th>
											<th style="min-width:120px!important;overflow:hidden;word-wrap:break-word;white-space: normal;vertical-align:middle">Unit Price</th>
											<th style="min-width:120px!important;overflow:hidden;word-wrap:break-word;white-space: normal;vertical-align:middle">Duration</th>
											<th style="min-width:100px!important;overflow:hidden;word-wrap:break-word;white-space: normal;vertical-align:middle">Tax Group</th>
											<th style="min-width:160px!important;overflow:hidden;word-wrap:break-word;white-space: normal;vertical-align:middle">Pre-Tax Amount</th>
											<th style="min-width:160px!important;overflow:hidden;word-wrap:break-word;white-space: normal;vertical-align:middle">Tax Amount</th>
											<th style="min-width:80px!important;overflow:hidden;word-wrap:break-word;white-space: normal;vertical-align:middle">Action</th>
										</tr>
										</thead>
										<tbody class="setall_experiences" id="porder_table_body">
										<tr data-id="1">
											<td>
												<select class="form-control select2 custom-select pk_item"
														name="pk_item[]"
														data-width="110px" data-container="body"
														data-live-search="true" autocomplete="off" data-id="1"
														onchange="goto_check_item(this);">
													<option value="">---Select---</option>
													<?php foreach ($itm_list as $items) { ?>
														<option
																value="<?php echo $items->item_code; ?>"><?php echo htmlspecialchars($items->item_code . ' - ' . $items->item_name, ENT_QUOTES); ?></option>
													<?php } ?>
												</select>
											</td>
											<td>
												<textarea type="text" class="form-control pk_desc" name="pk_desc[]"
														  id="pk_desc"
														  placeholder="Item Description" autocomplete="off"></textarea>
												<small
														class="invalid-feedback pk_desc_error"><?php echo form_error('pk_desc'); ?></small>
											</td>
											<td>
												<input type="hidden" class="form-control pk_sku" name="pk_sku[]"
													   id="pk_sku"
													   placeholder="Item SKU" autocomplete="off" readonly/>
												<input type="hidden" class="form-control pk_taxcode" name="pk_taxcode[]"
													   id="pk_taxcode" placeholder="Enter TaxCode" autocomplete="off"
													   readonly/>
												<input type="hidden" class="form-control pk_total_amt"
													   name="pk_total_amt[]"
													   id="pk_total_amt" placeholder="Item Total Amount"
													   autocomplete="off" readonly/>
												<input type="hidden" class="form-control pk_subtotal"
													   name="pk_subtotal[]"
													   id="pk_subtotal" placeholder="Item Sub Total" autocomplete="off"
													   readonly/>
												<select class="form-control select2 custom-select pk_ccode" readonly
														name="pk_ccode[]"
														data-live-search="true" data-container="body" autocomplete="off" data-id="1">
													<option value="">---Select---</option>
													<?php foreach ($ccode_list as $items) { ?>
														<option value="<?php echo $items->cc_id; ?>"><?php echo $items->cc_no.' - '.$items->cc_description; ?></option>
													<?php } ?>
												</select>
												<small class="invalid-feedback pk_ccode_error"><?php echo form_error('pk_ccode'); ?></small>
											</td>
											<td>
												<select class="form-control select2 custom-select pk_uom" readonly
														name="pk_uom[]" id="pk_uom" data-container="body"
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
											<td>

												<input type="number" class="form-control pk_itm_price"
													   name="pk_itm_price[]"
													   id="pk_itm_price" placeholder="Enter Price" data-id="1"
													   autocomplete="off"
													   onchange="goto_check_item_amounts(this);"/>
												<small
														class="invalid-feedback pk_itm_price_error"><?php echo form_error('pk_itm_price'); ?></small>
											</td>
											<td>
												<input type="number" class="form-control pk_rent_duration" name="pk_rent_duration[]"
													   id="pk_rent_duration" data-id="1" onchange="goto_check_item_amounts(this);" placeholder="Duration in days" autocomplete="off"
												/>
												<small class="invalid-feedback "><?php echo form_error('pk_rent_duration'); ?></small>
											</td>
											<td>
												<select class="form-control select2 custom-select pk_tax_group"
														data-live-search="true" autocomplete="off" name="pk_tax_group[]"
														id="pk_tax_group"
														data-id="1" data-container="body"
														onchange="goto_addTax(this);">
													<option value="">---Select---</option>
													<?php foreach ($taxgroup_list as $items) { ?>
														<option
																value="<?php echo $items->id; ?>"><?php echo $items->name; ?></option>
													<?php } ?>
												</select>
												<small
														class="invalid-feedback pk_tax_group_error"><?php echo form_error('pk_tax_group'); ?></small>
											</td>
											<td>
												<input type="text" class="form-control pre_tax_amt" name="pre_tax_amt[]"
													   id="pre_tax_amt" placeholder="Pre-Tax Amount" autocomplete="off"
													   readonly/>
												<small
														class="invalid-feedback pre_tax_amt_error"><?php echo form_error('pre_tax_amt'); ?></small>
											</td>
											<td>
												<input type="text" class="form-control pk_tax_amt" name="pk_tax_amt[]"
													   id="pk_tax_amt" placeholder="Item Tax Amount" autocomplete="off"
													   readonly/>
												<small
														class="invalid-feedback pk_tax_amt_error"><?php echo form_error('pk_tax_amt'); ?></small>
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

						<div class="form-group row justify-content-md-end">
							<div class="col-sm-4">
								<div class="table-responsive1">
									<table width="100%" class="table table-bordered">

										<tbody class="setall_experiences">
										<tr>
											<th>Sub-Total</th>
											<td>$ <span id="pk_sub_total">0.00</span></td>
										</tr>
										<tr>
											<th>Total Tax</th>
											<td>$ <span id="pk_total_tax">0.00</span></td>
										</tr>
										<tr>
											<th>PO Total</th>
											<td>$ <span id="total_po">0.00</span></td>
										</tr>
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
							<button type="button" onclick="gotoclclickbutton('pending');" class="btn btn-primary">Save</button>
							<button type="button" onclick="gotoclclickbutton('submitted');" class="btn btn-primary">Send</button>

							&nbsp;<a href="<?= site_url('admincontrol/rorder/all_rental_order_list') ?>"
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
	<div class="modal fade" id="Modal_supplieremial" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
		 aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" style="font-size:18px;font-weight:bold">Supplier Email Warning</h5>
					<button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body" style="font-size:18px">
					<span>Supplier Don't have assosiated email, please click ok and make sure you to send hard copy of PO. Otherwise click on close to cancel.</span>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" id="email_warning_close_btn" data-dismiss="modal">Close</button>
					<button type="button" id="email_warning_ok_btn" class="btn btn-primary">Ok
					</button>
					<!-- <div class="col-12 text-center text-danger" style="padding:10px 80px;border:2px solid red">
						<h4>
							Please contact your supervisor to add budget to the cost codes in order to submit this PO.
						</h4>
					</div> -->
				</div>
			</div>
		</div>
	</div>


	<?php $this->load->view('admin/component/footer') ?>

	<script type="text/javascript">

		var supplierEmailId='';
		var mannualPoStatus=1;
		var poStatusChecKForEmail='';
		$(function () {
			$('.alert-error, .invalid-feedback').delay(8000).fadeOut();
			$(".select2").selectpicker();
		});

		$(".select2").each(function (i, obj) {
			$(this).selectpicker();
		});

		function goto_check_item_amounts(element) {
			var row_id = $(element).attr('data-id');
			row_id = row_id-1;
			var pk_itm_qnty = $('[name="pk_itm_qnty[]"]').eq((row_id)).val();
			var pk_itm_price = $('[name="pk_itm_price[]"]').eq((row_id)).val();
			var pk_taxcode = $('[name="pk_tax_group[]"] option:selected').eq((row_id)).val();
			var pre_tax_amt = $('[name="pre_tax_amt[]"]').eq((row_id )).val();
			var tax_amt = $('[name="pk_taxcode[]"]').eq((row_id)).val();
			var duration = $('[name="pk_rent_duration[]"]').eq(row_id).val();
			var tax_amount = 0;

			var subtotal_amount = parseFloat(pk_itm_qnty) * parseFloat(pk_itm_price) * parseFloat(duration);

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
				// alert('alltotal='+alltotal);
				$('[name="pk_subtotal[]"]').eq(row_id).val(subtotal_amount);
				$('[name="pre_tax_amt[]"]').eq(row_id).val(parseFloat(pk_itm_price) * parseFloat(pk_itm_qnty) * parseFloat(duration));
				$('[name="pk_tax_amt[]"]').eq(row_id).val(tax_amount);
				$('[name="pk_total_amt[]"]').eq(row_id).val(alltotal);
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

				// alert('total_amount='+total_amount)

				$('#pk_sub_total').text(total_subamount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
				$('#pk_total_tax').text(total_tax.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
				$('#total_po').text(total_amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
				$('#itemdtl_tamount').val(total_amount.toFixed());
				// alert('totalinput='+$('#itemdtl_tamount').val());
			} else {
				$('[name="pk_subtotal[]"]').eq(row_id).val('');
				$('[name="pk_tax_amt[]"]').eq(row_id).val('');
				$('[name="pk_total_amt[]"]').eq(row_id).val('');
				$('[name="pre_tax_amt[]"]').eq(row_id).val('');
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

		function goto_check_project() {
			var po_project = $('#po_project option:selected').val();

			if (po_project != "") {
				var form_data = new FormData();
				form_data.append('po_project', po_project);

				$.ajax({
					method: 'POST',
					url: '<?php echo base_url() . "admincontrol/rorder/get_address_from_porject_find"; ?>',
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
							$('#po_billing_name').val(data.s_msg.billing_name);
							$('#po_billing_address').val(data.s_msg.billing_address);

						} else {
							$('#po_address').val('');
							$('#po_number').val('');
							$('#po_billing_name').val('');
							$('#po_billing_address').val('');
						}
					}
				});

			} else {
				$('#po_address').val('');
				$('#po_number').val('');
			}
		}

		function goto_check_item(element) {

			var row_id = $(element).attr('data-id');
			row_id = row_id-1;

			var supp_set = $('#po_supp option:selected').val();
//alert(supp_set);return;
			if (supp_set != "") {
				var pk_item = element.value;

				if (pk_item != "") {
					var form_data = new FormData();
					form_data.append('pk_item', pk_item);
					form_data.append('supp_set', supp_set);

					$.ajax({
						method: 'POST',
						url: '<?php echo base_url() . "admincontrol/rorder/get_alldetails_from_item_find"; ?>',
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

								if(data.prices) {
									var prices = "<select name='pk_itm_price[]' data-id='1' onchange='goto_check_item_amounts(this)' class='form-control pk_itm_price'>";

									$.each(data.prices,function (key,value) {
										prices += "<option value='"+value+"'>"+key+" - "+value+"</option>";

									});
									prices += "</select>";
									prices += '<small class="invalid-feedback pk_itm_price_error"></small>';
								}

								$('[name="pk_itm_price[]"]').eq(row_id).parent().html(prices);
								$('select[name="pk_ccode[]"]').eq(row_id).find('option[value="'+data.s_msg.item_ccode_ms+'"]').attr("selected",true);
								$('select[name="pk_ccode[]"]').eq(row_id).attr("readonly",true);
								$('.pk_ccode').selectpicker('refresh');

								if (data.supp_set) {
									$('[name="pk_sku[]"]').eq(row_id).val(data.supp_set.supcat_sku_no);
									$('[name="pk_itm_price[]"]').eq(row_id).val(data.supp_set.supcat_price).trigger('change');
								}
								$('select[name="pk_uom[]"]').eq(row_id).find('option:contains("'+data.s_msg.uom_name+'")').attr("selected",true);
								$('.pk_uom').selectpicker('refresh');
								$('select[name="pk_ccode[]"]').eq(row_id).attr("readonly",true);

								$('[name="pk_desc[]"]').eq(row_id).val(data.s_msg.item_description);
								$('[name="pk_taxcode[]"]').eq(row_id).val('<?php isset($taxcode_set->tc_tax_value) ? $taxcode_set->tc_tax_value : 0 ?>');

								if ("e_msg" in data) {

									// alert(data.e_msg);
									// $('.get_error_total9').html(data.e_msg);
									// $(".get_error_total9").fadeIn();
									// setTimeout(function () {
									// 	$('.invalid-feedback, .get_error_total9').fadeOut();
									// }, 5000);
								}

							} else {

							}
						}
					});

				} else {
					$('.pk_ccode:eq(' + (row_id) + ') option:selected').prop("selected", false);
					$('.pk_uom:eq(' + (row_id) + ') option:selected').prop("selected", false);
					$('[name="pk_subtotal[]"]').eq(row_id).val(0);
					$('[name="pk_tax_amt[]"]').eq(row_id).val(0);
					$('[name="pk_total_amt[]"]').eq(row_id).val(0);
					$('[name="pre_tax_amt[]"]').eq(row_id).val(0);
					$('[name="pk_itm_qnty[]"]').eq(row_id).val(0);
					$('[name="pk_itm_price[]"]').eq(row_id).val('');
					$('[name="pk_desc[]"]').eq(row_id).val('');
					$('[name="pk_itm_price[]"]').eq(row_id).trigger('change');

				}
			} else {
				$('.pk_item').val('');
				$('.pk_item').selectpicker('refresh');
				var error_message = "Supplier is needed. please select.";
				$('.get_error_total9').html(error_message);
				$(".get_error_total9").fadeIn();
				setTimeout(function () {
					$('.invalid-feedback, .get_error_total9').fadeOut();
				}, 5000);
			}
		}

		function goto_addTax(element) {

			var row_id = $(element).data('id');
			var tax_group = $(element).children("option:selected").val();

			var form_data = new FormData();
			form_data.append('tax_group', tax_group);

				$.ajax({
					method: 'POST',
					url: '<?php echo base_url() . "admincontrol/rorder/get_tax_detail"; ?>',
					data: form_data,
					dataType: 'JSON',
					contentType: false,
					processData: false,
					success: function (data) {
						//alert(data.msg);
						if (data.msg == 1) {
							var pre_tax_amt = $('[name="pre_tax_amt[]"]').eq(row_id - 1).val();
							var tax_amount = parseFloat(pre_tax_amt / 100) * data.taxgroup.percentage ;
							if (isNaN(parseFloat(tax_amount))) {
								return tax_amount = 0
							}
							$('[name="pk_taxcode[]"]').eq(row_id - 1).val(data.taxgroup.percentage);
							$('[name="pk_tax_amt[]"]').eq(row_id - 1).val(tax_amount);

							var alltotal = parseFloat(pre_tax_amt) + parseFloat(tax_amount);
							$('[name="pk_subtotal[]"]').eq(row_id - 1).val(pre_tax_amt);
							$('[name="pk_total_amt[]"]').eq(row_id - 1).val(alltotal);
							$('#pk_total_tax').text(parseFloat($('#pk_total_tax').text()) + tax_amount);
							var total_subamount = 0;
							var total_tax = 0;
							var total_amount = 0;
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
							$('#pk_sub_total').text(total_subamount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
							$('#pk_total_tax').text(total_tax.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
							$('#total_po').text(total_amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
							$('#itemdtl_tamount').val(total_amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
							goto_check_item_amounts(element);

						} else {
							$('[name="pk_tax_amt[]"]').eq(row_id - 1).val("");
							goto_check_item_amounts(element);
						}
					}
				});
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
			var pk_itm_price = $('#pk_itm_price').val();
			var pk_subtotal = $('#pk_subtotal').val();
			var pk_tax_group = $('#pk_tax_group').val();
			var pk_tax_amt = $('#pk_tax_amt').val();
			var pk_total_amt = $('#pk_total_amt').val();
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
			if (pk_taxcode == "") {
				e_error = 1;
				$('.pk_taxcode').html('Tax Code is Required.');
			} else {
				$('.pk_taxcode').html('');
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
			if (pk_itm_price == "") {
				e_error = 1;
				$('.pk_itm_price').html('Price is Required.');
			} else {
				if (!pk_itm_price.match(onlynumerics_withdot)) {
					e_error = 1;
					$('.pk_itm_price').html('Price only use numeric values, Check again.');
				} else if (parseInt(pk_itm_price) <= 0) {
					e_error = 1;
					$('.pk_itm_price').html('Price always greater than 0, Check again.');
				} else {
					$('.pk_itm_price').html('');
				}
			}

			if (pk_subtotal == "") {
				e_error = 1;
				$('.pk_subtotal').html('Sub Total is Required.');
			} else {
				if (!pk_subtotal.match(onlynumerics_withdot)) {
					e_error = 1;
					$('.pk_subtotal').html('Sub Total only use numeric values, Check again.');
				} else if (parseInt(pk_subtotal) <= 0) {
					e_error = 1;
					$('.pk_subtotal').html('Sub Total always greater than 0, Check again.');
				} else {
					$('.pk_subtotal').html('');
				}
			}
			if (pk_tax_amt == "") {
				e_error = 1;
				$('.pk_tax_amt').html('Tax Amount is Required.');
			} else {
				if (!pk_tax_amt.match(onlynumerics_withdot)) {
					e_error = 1;
					$('.pk_tax_amt').html('Tax Amount only use numeric values, Check again.');
				} else if (parseInt(pk_tax_amt) < 0) {
					e_error = 1;
					$('.pk_tax_amt').html('Tax Amount always greater than or Equal to 0, Check again.');
				} else {
					$('.pk_tax_amt').html('');
				}
			}
			if (pk_total_amt == "") {
				e_error = 1;
				$('.pk_total_amt').html('Total Amount is Required.');
			} else {
				if (!pk_total_amt.match(onlynumerics_withdot)) {
					e_error = 1;
					$('.pk_total_amt').html('Total Amount only use numeric values, Check again.');
				} else if (parseInt(pk_total_amt) <= 0) {
					e_error = 1;
					$('.pk_total_amt').html('Total Amount always greater than 0, Check again.');
				} else {
					$('.pk_total_amt').html('');
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
				form_data.append('pk_taxcode', pk_taxcode);
				form_data.append('pk_itm_qnty', pk_itm_qnty);
				form_data.append('pk_itm_price', pk_itm_price);
				form_data.append('pk_subtotal', pk_subtotal);
				form_data.append('pk_tax_group', pk_tax_group);
				form_data.append('pk_tax_amt', pk_tax_amt);
				form_data.append('pk_total_amt', pk_total_amt);
//form_data.append("files", files[0]);
				$.ajax({
					method: 'POST',
					url: '<?php echo base_url() . "admincontrol/rorder/new_rorder_item_submission"; ?>',
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
						url: '<?php echo base_url() . "admincontrol/rorder/delete_itemset_update"; ?>',
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
			$('#Modal_addrecord').modal('show');
		}

		jQuery(document).delegate('a.add_row', 'click', function (e) {
			e.preventDefault();
			var size = jQuery('#porder_table >tbody >tr').length + 1;
			var content = '<tr data-id="' + size + '">\n' +
					'\t\t\t\t<td>\n' +
					'\t\t\t\t\t<select class="form-control select2 custom-select pk_item" data-id="' + size + '" name="pk_item[]"\n' +
					'\t\t\t\t\t\t\tdata-live-search="true" data-container="body" autocomplete="off"\n' +
					'\t\t\t\t\t\t\tonchange="goto_check_item(this);">\n' +
					'\t\t\t\t\t\t<option value="">---Select---</option>\n' +
					'\t\t\t\t\t\t<?php foreach ($itm_list as $items) { ?>\n' +
					'\t\t\t\t\t\t<option value="<?php echo $items->item_code; ?>"> <?php echo htmlspecialchars($items->item_code . ' - ' . $items->item_name, ENT_QUOTES); ?></option>\n' +
					'\t\t\t\t\t\t<?php } ?>\n' +
					'\t\t\t\t\t</select>\n' +
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
					'\t\t\t\t\t\t\t\t\t\t\t\t\t\tdata-live-search="true" data-container="body" autocomplete="off" data-id="1">\n' +
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
					'\t\t\t\t\t\t\t\t\t\t\t\t\t\tdata-live-search="true" data-container="body" autocomplete="off" data-id="1">\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="">---Select---</option>\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php foreach ($uom_list as $items) { ?>\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="<?php echo $items->uom_id; ?>"><?php echo $items->uom_name; ?></option>\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php } ?>\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t</select>' +
					'\t\t\t\t\t<small class="invalid-feedback pk_uom"><?php echo form_error("pk_uom"); ?></small>\n' +
					'\t\t\t\t</td>\n' +
					'\t\t\t\t<td>\n' +
					'\t\t\t\t\t<input type="hidden" class="form-control" name="pk_sku[]" id="pk_sku"\n' +
					'\t\t\t\t\t\t   placeholder="Item SKU" autocomplete="off" readonly/>\n' +
					'\t\t\t\t\t<input type="hidden" class="form-control" name="pk_taxcode[]"\n' +
					'\t\t\t\t\t\t   id="pk_taxcode" placeholder="Enter TaxCode" autocomplete="off"\n' +
					'\t\t\t\t\t\t   readonly/>\n' +
					'\t\t\t\t\t<input type="hidden" class="form-control" name="pk_total_amt[]"\n' +
					'\t\t\t\t\t\t   id="pk_total_amt" placeholder="Item Total Amount"\n' +
					'\t\t\t\t\t\t   autocomplete="off" readonly/>\n' +
					'\t\t\t\t\t<input type="hidden" class="form-control pk_subtotal" name="pk_subtotal[]"\n' +
					'\t\t\t\t\t\t   id="pk_subtotal" placeholder="Item Sub Total" autocomplete="off"\n' +
					'\t\t\t\t\t\t   readonly/>\n' +
					'\t\t\t\t\t<input type="number" class="form-control" name="pk_itm_qnty[]"\n' +
					'\t\t\t\t\t\t   id="pk_itm_qnty" placeholder="Enter Quantity" data-id="' + size + '" autocomplete="off"\n' +
					'\t\t\t\t\t\t   onchange="goto_check_item_amounts(this);"/>\n' +
					'\t\t\t\t\t<small class="invalid-feedback pk_itm_qnty"><?php echo form_error("pk_itm_qnty"); ?></small>\n' +
					'\t\t\t\t</td>\n' +
					'\t\t\t\t<td>\n' +
					'\t\t\t\t\t<input type="number" class="form-control" name="pk_itm_price[]"\n' +
					'\t\t\t\t\t\t   id="pk_itm_price" placeholder="Enter Price" data-id="' + size + '" autocomplete="off"\n' +
					'\t\t\t\t\t\t   onchange="goto_check_item_amounts(this);"/>\n' +
					'\t\t\t\t\t<small class="invalid-feedback pk_itm_price"><?php echo form_error("pk_itm_price"); ?></small>\n' +
					'\t\t\t\t</td>\n' +
					'\t<td>\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t<input type="number" class="form-control pk_rent_duration" name="pk_rent_duration[]"\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t   id="pk_rent_duration" data-id="1" onchange="goto_check_item_amounts(this);" placeholder="Duration in days" autocomplete="off"\n' +
					'\t\t\t\t\t\t\t\t\t\t\t</td>'+
					'\t\t\t\t<td>\n' +
					'\t\t\t\t\t<select class="form-control select2 custom-select pk_tax_group"\n' +
					'\t\t\t\t\t\t\tdata-live-search="true" data-container="body" data-id="' + size + '" autocomplete="off" name="pk_tax_group[]"\n' +
					'\t\t\t\t\t\t\tid="pk_tax_group" onchange="goto_addTax(this);">\n' +
					'\t\t\t\t\t\t<option value="">---Select---</option>\n' +
					'\t\t\t\t\t\t<?php foreach ($taxgroup_list as $items) { ?>\n' +
					'\t\t\t\t\t\t<option value="<?php echo $items->id; ?>"><?php echo $items->name; ?></option>\n' +
					'\t\t\t\t\t\t<?php } ?>\n' +
					'\t\t\t\t\t</select>\n' +
					'\t\t\t\t\t<small class="invalid-feedback pk_tax_group"><?php echo form_error("pk_tax_group"); ?></small>\n' +
					'\t\t\t\t</td>\n' +
					'\t\t\t\t<td>\n' +
					'\t\t\t\t\t<input type="text" class="form-control" name="pre_tax_amt[]"\n' +
					'\t\t\t\t\t\t   id="pre_tax_amt" placeholder="Pre-Tax Amount" autocomplete="off"\n' +
					'\t\t\t\t\t\t   readonly/>\n' +
					'\t\t\t\t\t<small class="invalid-feedback pre_tax_amt"><?php echo form_error("pre_tax_amt"); ?></small>\n' +
					'\t\t\t\t</td>\n' +
					'\t\t\t\t<td>\n' +
					'\t\t\t\t\t<input type="text" class="form-control pk_tax_amt" name="pk_tax_amt[]"\n' +
					'\t\t\t\t\t\t   id="pk_tax_amt" placeholder="Item Tax Amount" autocomplete="off"\n' +
					'\t\t\t\t\t\t   readonly/>\n' +
					'\t\t\t\t\t<small class="invalid-feedback pk_tax_amt"><?php echo form_error("pk_tax_amt"); ?></small>\n' +
					'\t\t\t\t</td>\n' +
					'<td><a class="btn btn-outline-warning delete-record" data-id="' + size + '"><i class="fa fa-trash text-danger"></i></a></td>\n' +
					'\t\t\t</tr>';
			$('#porder_table_body').append(content);
			add_row_action(size);
			assign_data_id();
			$('.pk_item').selectpicker('refresh');
			$('.select2').selectpicker('refresh');
		});

		jQuery(document).delegate('a.delete-record', 'click', function (e) {
			e.preventDefault();
			var didConfirm = confirm("Are you sure You want to delete");
			if (didConfirm == true) {
				var id = jQuery(this).attr('data-id');
				$('#porder_table_body tr[data-id="' + id + '"]').remove();
				for(i=0;i<jQuery('#porder_table >tbody >tr').length; i++) {
					$('#porder_table >tbody >tr:nth-child('+(i+1)+')').find('.pk_new_row').val(i);
				}
				assign_data_id();
				var size = jQuery('#porder_table >tbody >tr').length;
				if(size == 1) {
					$('#porder_table_body tr[data-id="' + (size) + '"]').find('[name="pk_itm_price[]"]').trigger('change');
				} else {
					$('#porder_table_body tr[data-id="' + (size-1) + '"]').find('[name="pk_itm_price[]"]').trigger('change');
				}
				add_row_action(size);

				return true;
			} else {
				return false;
			}
		});

		function goto_add_row() {
			$('#Modal_addrecord').modal('show');
		}

		function goto_check_package() {
			var itmpk_id = $('#itmpk_id option:selected').val();
			var supp_set = $('#po_supp option:selected').val();

			if (itmpk_id != "" && supp_set != "") {
				var form_data = new FormData();
				form_data.append('itmpk_id', itmpk_id);

				$.ajax({
					method: 'POST',
					url: '<?php echo base_url() . "admincontrol/rorder/get_packageitems_from_package_find"; ?>',
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
					url: "<?php echo site_url('admincontrol/rorder/add_multiple_items_from_package_sets') ?>",
					dataType: 'json',
					data: form_data,
					contentType: false,
					cache: true,
					processData: false,
					success: function (data) {
						//alert(data.msg);
						if (data.msg == 1) {
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
							data.s_msg.forEach(function (item) {
								var size = jQuery('#porder_table >tbody >tr').length + 1;

								var content = '<tr data-id="' + size + '">\n' +
										'\t\t\t\t<td>\n' +
										'\t\t\t\t\t<select class="form-control select2 custom-select pk_item" readonly data-id="' + size + '" name="pk_item[]"\n' +
										'\t\t\t\t\t\t\tdata-live-search="true" data-container="body" autocomplete="off"\n' +
										'\t\t\t\t\t\t\tonchange="goto_check_item(this);">\n' +
										'\t\t\t\t\t\t<option value="">---Select---</option>\n' +
										'\t\t\t\t\t\t<?php foreach ($itm_list as $items) { ?>\n' +
										'\t\t\t\t\t\t<option value="<?php echo $items->item_code; ?>" > <?php echo htmlspecialchars($items->item_code . ' - ' . $items->item_name, ENT_QUOTES); ?></option>\n' +
										'\t\t\t\t\t\t<?php } ?>\n' +
										'\t\t\t\t\t</select>\n' +
										'\t\t\t\t</td>\n' +
										'<td>\n' +
										'\t\t\t\t\t\t<textarea type="text" class="form-control pk_desc" name="pk_desc[]"\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t   id="pk_desc"\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t\t  placeholder="Item Description" autocomplete="off">'+item.item_description+'</textarea>\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t<small\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t\tclass="invalid-feedback pk_desc_error"><?php echo form_error("pk_desc"); ?></small>\n' +
										'\t\t\t\t\t\t\t\t\t\t\t</td>' +
										'\t\t\t\t<td>\n' +
										'<select class="form-control select2 custom-select pk_ccode"\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t\tname="pk_ccode[]"\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t\tdata-live-search="true" data-container="body" autocomplete="off" data-id="1">\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="">---Select---</option>\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php foreach ($ccode_list as $items) { ?>\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="<?php echo $items->cc_id; ?>" ><?php echo $items->cc_no.' - '.$items->cc_description; ?></option>\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php } ?>\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t</select>'+
										'\t\t\t\t</td>\n' +
										'\t\t\t\t<td>\n' +
										'<select class="form-control select2 custom-select pk_uom"\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t\tname="pk_uom[]" id="pk_uom"\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t\tdata-live-search="true" data-container="body" autocomplete="off" data-id="1">\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="">---Select---</option>\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php foreach ($uom_list as $items) { ?>\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="<?php echo $items->uom_id; ?>"><?php echo $items->uom_name; ?></option>\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php } ?>\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t</select>'+
										'\t\t\t\t</td>\n' +
										'\t\t\t\t<td>\n' +
										'\t\t\t\t\t<input type="number" class="form-control" value="' + item.po_detail_quantity + '" name="pk_itm_qnty[]"\n' +
										'\t\t\t\t\t\t   id="pk_itm_qnty" placeholder="Enter Quantity" data-id="' + size + '" autocomplete="off"\n' +
										'\t\t\t\t\t\t   onchange="goto_check_item_amounts(this);"/>\n' +
										'\t\t\t\t\t<small class="invalid-feedback pk_itm_qnty"><?php echo form_error("pk_itm_qnty"); ?></small>\n' +
										'\t\t\t\t</td>\n' +
										'\t\t\t\t<td>\n' +
										'\t\t\t\t\t<input type="hidden" class="form-control" name="pk_sku[]" value="' + item.po_detail_sku + '" id="pk_sku"\n' +
										'\t\t\t\t\t\t   placeholder="Item SKU" autocomplete="off" readonly/>\n' +
										'\t\t\t\t\t<input type="hidden" class="form-control" value="' + item.po_detail_taxcode + '" name="pk_taxcode[]"\n' +
										'\t\t\t\t\t\t   id="pk_taxcode" placeholder="Enter TaxCode" autocomplete="off"\n' +
										'\t\t\t\t\t\t   readonly/>\n' +
										'\t\t\t\t\t<input type="hidden" class="form-control" value="' + item.po_detail_total + '" name="pk_total_amt[]"\n' +
										'\t\t\t\t\t\t   id="pk_total_amt" placeholder="Item Total Amount"\n' +
										'\t\t\t\t\t\t   autocomplete="off" readonly/>\n' +
										'\t\t\t\t\t<input type="hidden" class="form-control pk_subtotal" value="' + item.po_detail_subtotal + '" name="pk_subtotal[]"\n' +
										'\t\t\t\t\t\t   id="pk_subtotal" placeholder="Item Sub Total" autocomplete="off"\n' +
										'\t\t\t\t\t\t   readonly/>\n' +
										'\t\t\t\t\t<input type="number" class="form-control" value="' + item.po_detail_unitprice + '" name="pk_itm_price[]"\n' +
										'\t\t\t\t\t\t   id="pk_itm_price" placeholder="Enter Price" data-id="' + size + '" autocomplete="off"\n' +
										'\t\t\t\t\t\t   onchange="goto_check_item_amounts(this);"/>\n' +
										'\t\t\t\t\t<small class="invalid-feedback pk_itm_price"><?php echo form_error("pk_itm_price"); ?></small>\n' +
										'\t\t\t\t</td>\n' +
										'\t\t\t\t<td>\n' +
										'\t\t\t\t\t<select class="form-control select2 custom-select pk_tax_group"\n' +
										'\t\t\t\t\t\t\tdata-live-search="true" data-container="body" data-id="' + size + '"  autocomplete="off" name="pk_tax_group[]"\n' +
										'\t\t\t\t\t\t\tid="pk_tax_group" onchange="goto_addTax(this);">\n' +
										'\t\t\t\t\t\t<option value="">---Select---</option>\n' +
										'\t\t\t\t\t\t<?php foreach ($taxgroup_list as $items) { ?>\n' +
										'\t\t\t\t\t\t<option value="<?php echo $items->id; ?>"><?php echo $items->name; ?></option>\n' +
										'\t\t\t\t\t\t<?php } ?>\n' +
										'\t\t\t\t\t</select>\n' +
										'\t\t\t\t\t<small class="invalid-feedback pk_tax_group"><?php echo form_error("pk_tax_group"); ?></small>\n' +
										'\t\t\t\t</td>\n' +
										'\t\t\t\t<td>\n' +
										'\t\t\t\t\t<input type="text" class="form-control" name="pre_tax_amt[]"\n' +
										'\t\t\t\t\t\t   id="pre_tax_amt" placeholder="Pre-Tax Amount" autocomplete="off"\n' +
										'\t\t\t\t\t\t   readonly/>\n' +
										'\t\t\t\t\t<small class="invalid-feedback pre_tax_amt"><?php echo form_error("pre_tax_amt"); ?></small>\n' +
										'\t\t\t\t</td>\n' +
										'\t\t\t\t<td>\n' +
										'\t\t\t\t\t<input type="text" class="form-control pk_tax_amt" name="pk_tax_amt[]"\n' +
										'\t\t\t\t\t\t   id="pk_tax_amt" placeholder="Item Tax Amount" autocomplete="off"\n' +
										'\t\t\t\t\t\t   readonly/>\n' +
										'\t\t\t\t\t<small class="invalid-feedback pk_tax_amt"><?php echo form_error("pk_tax_amt"); ?></small>\n' +
										'\t\t\t\t</td>\n' +
										'<td><a class="btn btn-outline-warning delete-record" data-id="' + size + '"><i class="fa fa-trash text-danger"></i></a></td>\n' +
										'\t\t\t</tr>';

								$('#porder_table_body').append(content);
								$('#porder_table_body tr[data-id=' + size + ']').find('select[name="pk_item[]"] option[value=' + item.po_detail_item + ']').attr('selected', 'selected');
								$('#porder_table_body tr[data-id=' + size + ']').find('select[name="pk_ccode[]"] option[value=' + item.po_detail_cost_code + ']').attr('selected', 'selected');
								$('#porder_table_body tr[data-id=' + size + ']').find('select[name="pk_uom[]"] option[value=' + item.porder_detail_uom + ']').attr('selected', 'selected');
								$('input[name="pk_itm_price[]"]').trigger('change');

								var size = jQuery('#porder_table >tbody >tr').length;
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
			// alert('total_amount='+total_amount);

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
			// alert('itemdtl_tamount='+itemdtl_tamount);
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

			var pk_sku = $('input[name="pk_sku[]"]').map(function () {
				return this.value; // $(this).val()
			}).get();
			var pk_uom = $('select[name="pk_uom[]"] option:selected').map(function () {
				return this.value; // $(this).val()
			}).get();

			var pk_desc = $('textarea[name="pk_desc[]"]').map(function () {
				return this.value; // $(this).val()
			}).get();
			var pk_taxcode = $('input[name="pk_taxcode[]"]').map(function () {
				return this.value; // $(this).val()
			}).get();
			var pk_itm_qnty = $('input[name="pk_itm_qnty[]"]').map(function () {
				return this.value; // $(this).val()
			}).get();

			var pk_itm_price = $('input[name="pk_itm_price[]"]').map(function () {
				return this.value; // $(this).val()
			}).get();

			var pk_subtotal = $('input[name="pk_subtotal[]"]').map(function () {
				return this.value; // $(this).val()
			}).get();
			var pk_tax_group = $('select[name="pk_tax_group[]"] option:selected').map(function () {
				return this.value; // $(this).val()
			}).get();
			var pk_tax_amt = $('input[name="pk_tax_amt[]"]').map(function () {
				return this.value; // $(this).val()
			}).get();
			var pk_total_amt = $('input[name="pk_total_amt[]"]').map(function () {
				return this.value; // $(this).val()
			}).get();
			var pk_rent_duration = $('input[name="pk_rent_duration[]"]').map(function () {
				return this.value; // $(this).val()
			}).get();

			$('select[name="pk_itm_price[]"]').map(function () {
				return pk_itm_price.push(this.value); // $(this).val()
			}).get();

			if (ipack_itm_no == "") {
				e_error = 1;
				error_message = error_message + "<br/>ID missing, Refresh the page";
			}

			if (pk_taxcode == "") {
				e_error = 1;
				$('.pk_taxcode').html('Tax Code is Required.');
			} else {
				$('.pk_taxcode').html('');
			}

			if (pk_itm_qnty == "") {
				e_error = 1;
				$('.pk_itm_qnty').html('Item Quantity is Required.');
			} else {
				$('.pk_itm_qnty').html('');
			}

			if (pk_itm_price == "") {
				e_error = 1;
				$('.pk_itm_price_error').html('Price is Required.');
			} else {
				$('.pk_itm_price_error').html('');
			}

			if (pk_subtotal == "") {
				e_error = 1;
				$('.pk_subtotal').html('Sub Total is Required.');
			} else {
				$('.pk_subtotal').html('');
			}

			if (pk_tax_amt == "") {
				e_error = 1;
				$('.pk_tax_amt').html('Tax Amount is Required.');
			} else {
				$('.pk_tax_amt').html('');
			}

			if (pk_total_amt == "") {
				e_error = 1;
				$('.pk_total_amt').html('Total Amount is Required.');
			} else {
				$('.pk_total_amt').html('');
			}

			if (ipack_itm_no == "") {
				e_error = 1;
				error_message = error_message + "<br/>ID missing, Refresh the page";
			}

			if (itemdtl_counter == "" || itemdtl_counter <= 0 || itemdtl_tamount == "" || itemdtl_tamount <= 0) {
				e_error = 1;
				error_message = error_message + "<br/>Item not found in the Rental Order, Add some Item.";
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

			if (pk_item == "") {
				e_error = 1;
				$('.div_roller_total9').fadeOut();
				//error_message = "There have some problem to Store Data, Try after some time.";
				$('.get_error_total9').html("All item field's is required");
				$(".get_error_total9").fadeIn();
				setTimeout(function () {
					$('.get_error_total9').fadeOut();
				}, delay);
			} else {
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

				if((supplierEmailId=='' || supplierEmailId=='-') && mannualPoStatus==1 && status=='submitted'){
					mannualPoStatus=0;
					poStatusChecKForEmail=status;
					// e_error = 1;
					$('.div_roller_total').fadeOut();
					$('#Modal_supplieremial').modal('show');
				}

				if(mannualPoStatus==1 || mannualPoStatus==2){
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
				form_data.append('pk_sku[]', JSON.stringify(pk_sku));
				form_data.append('pk_uom[]', JSON.stringify(pk_uom));
				form_data.append('pk_desc[]', JSON.stringify(pk_desc));
				form_data.append('pk_taxcode[]', JSON.stringify(pk_taxcode));
				form_data.append('pk_itm_qnty[]', JSON.stringify(pk_itm_qnty));
				form_data.append('pk_itm_price[]', JSON.stringify(pk_itm_price));
				form_data.append('pk_subtotal[]', JSON.stringify(pk_subtotal));
				form_data.append('pk_tax_group[]', JSON.stringify(pk_tax_group));
				form_data.append('pk_tax_amt[]', JSON.stringify(pk_tax_amt));
				form_data.append('pk_total_amt[]', JSON.stringify(pk_total_amt));
				form_data.append('pk_rent_duration[]', JSON.stringify(pk_rent_duration));
				form_data.append("po_type", 'Rental PO');
				form_data.append("po_status", status);

				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/rorder/new_p_order_Set_submission') ?>",
					dataType: 'json',
					data: form_data,
					contentType: false,
					cache: true,
					processData: false,
					success: function (data) {
						if (data.msg == 1) {
							console.log(data);
							//alert(data.msg[0].space_rate);
							$('.div_roller_total').fadeOut();
							toastr.success('Record is Inserted Successfully!', 'Success');
							setTimeout(function () {
								window.location.replace("<?php echo site_url('admincontrol/rorder/all_rental_order_list') ?>");
							}, 2000);

						} else {
							$('.div_roller_total').fadeOut();
							$('#submit_record_btn').prop('disabled', false);
							$('.close_modal').show();
							error_message = data.e_msg;
							alert(error_message);
							// toastr.error(error_message, 'Error!');
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
			}
//$("#myForm").submit();


		}


		$('#po_supp').change(function(){
			// alert('change');
			getSupplierEmail($(this).val());
		});

		function getSupplierEmail(supplierId){
			// alert('email');
			// alert(projectId);
			if(supplierId!=''){
				var form_data = new FormData();
				form_data.append("sup_id", supplierId);

				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/porder/get_supplier_email') ?>",
					dataType: 'json',
					data: form_data,
					contentType: false,
					cache: true,
					processData: false,
					success: function (data) {
						// alert(JSON.stringify(data));
						if (data.msg == 1) {
							console.log(data);
							// revisedCostArray=JSON.parse(JSON.stringify(data.rv_msg));
							// committedCostArray=JSON.parse(JSON.stringify(data.cc_msg));
							// alert(revisedCostArray["2-01-10 "]);
							// alert(committedCostArray["2-01-10 "]);
							// alert(JSON.stringify(data.se_msg));

							supplierEmailId = data.se_msg.sup_email;
							// alert(supplierEmailId);
							// alert(JSON.stringify(data.rv_msg));
							// $('.div_roller_total').fadeOut();
							// toastr.success('Record is Inserted Successfully!', 'Success');
							// setTimeout(function () {
							// 	window.location.replace("<?php echo site_url('admincontrol/porder/all_purchase_order_list') ?>");
							// }, 2000);

						} else {
							// $('.div_roller_total').fadeOut();
							// $('#submit_record_btn').prop('disabled', false);
							// $('.close_modal').show();
							error_message = JSON.stringify(data.e_msg);
							// alert(error_message);
							// toastr.error(error_message, 'Error!');
							//$('.get_error_total').html(error_message);
							//$(".get_error_total").fadeIn();
							//setTimeout(function(){ $('.get_error_total').fadeOut(); }, delay);
						}

					},
					error: function (error) {
						console.log(error);
						// alert(JSON.stringify(error));
					},
					//complete: function (error) {
					//	toastr.success('Record is Inserted Successfully!', 'Success');
					//	setTimeout(function () {
					//		window.location.replace("<?php //echo site_url('admincontrol/porder/all_purchase_order_list') ?>//");
					//	}, 2000);
					//}
				});
			}
		}

		$('#email_warning_ok_btn').click(function(){
			mannualPoStatus=2;
			$('#Modal_supplieremial').modal('hide');
			gotoclclickbutton(poStatusChecKForEmail);
		});
		$('#email_warning_close_btn').click(function(){
			mannualPoStatus=1;
		});
	</script>
