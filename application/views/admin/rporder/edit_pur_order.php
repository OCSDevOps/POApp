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
				<h4 class="page-title">
					<?php if($view) { ?>
					View Request Order
				<?php } else { ?>
						Edit Request Order
					<?php } ?></h4>
				<div class="ml-auto text-right">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a
										href="<?php echo base_url('admincontrol/dashboard'); ?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page"><?php if($view) { ?>
									View Request Order
								<?php } else { ?>
									Edit Request Order
								<?php } ?></li>
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
					<div class="border-bottom">
						<div class="card-body">
							<div class="row">
								<div class="col-md-12">
									<?php if($view) { ?>
										<a href="<?php echo base_url() . 'admincontrol/rfqorder/print_porder_setpdf/' . $rporder_list->rporder_id; ?>"
										   target="_blank" class="btn btn-primary float-right mb-2" style="margin-right: 10px;">PRINT</a>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
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
								<input type="hidden" name="ipack_itm_no" id="ipack_itm_no" value="<?php echo $at_no; ?>" autocomplete="off" />
								<input type="hidden" name="itemdtl_counter" id="itemdtl_counter" value="<?php echo $rporder_list->rporder_total_item; ?>" autocomplete="off" />
								<input type="hidden" name="itemdtl_tamount" id="itemdtl_tamount" value="<?php echo $rporder_list->rporder_total_amount; ?>" autocomplete="off" />
								<input type="hidden" name="po_id" id="po_id" value="<?php echo $rporder_list->rporder_id ?>"
									   autocomplete="off"/>


								<?php foreach ($proj_list as $p_items) { ?>
										<?php if($rporder_list->rporder_project_ms == $p_items->proj_id)  { ?>
												<input type="text" name="" disabled value="<?php echo $p_items->proj_name; ?>" class="form-control">
												<input type="hidden" name="po_project" id="po_project" disabled value="<?php echo $p_items->proj_id; ?>" class="form-control">
								<?php }
								}
								?>

								<small
										class="invalid-feedback po_project"><?php echo form_error('po_project'); ?></small>
							</div>
							<label for="fname" class="col-sm-3 text-right control-label col-form-label">Request Form
								Number</label>
							<div class="col-sm-3">
								<div class="row">
									<?php $order_number = explode('-',$rporder_list->rporder_no)?>
									<input type="text" class="form-control col-5" readonly value="<?php echo $order_number[0] ?>" name="po_number_prefix"
										   id="po_number_prefix"
										   autocomplete="off"/>
									<input type="text" class="form-control col-5" readonly name="po_number" value="<?php echo $order_number[1] ?>" id="po_number"
										   autocomplete="off"/>
									<small
											class="invalid-feedback po_number"><?php echo form_error('po_number'); ?></small>
								</div>
							</div>
						</div>
						<div class="form-group row">
							<label for="fname" class="col-sm-2 text-right control-label col-form-label">Supplier</label>
							<div class="col-sm-3">
								<?php foreach ($supp_list as $sup_items) {
									if($rporder_list->rporder_supplier_ms == $sup_items->sup_id) {
										?>
											<input type="text" class="form-control" disabled value="<?php echo $sup_items->sup_name; ?>">
											<input type="hidden" class="form-control" name="po_supp" id="po_supp" disabled value="<?php echo $sup_items->sup_id; ?>">
								<?php } } ?>
								<small class="invalid-feedback po_supp"><?php echo form_error('po_supp'); ?></small>
							</div>

							<label for="cono1" class="col-sm-3 text-right control-label col-form-label">Request Form
								Description</label>
							<div class="col-sm-3">
			<textarea class="form-control" name="po_desc" disabled id="po_desc"
					  placeholder="Enter PO Description" autocomplete="off"><?php echo $rporder_list->rporder_description ?></textarea>
								<small
										class="invalid-feedback po_desc"><?php echo form_error('po_description'); ?></small>
							</div>
						</div>
						<div class="form-group row">
							<label for="cono1" class="col-sm-2 text-right control-label col-form-label">Delivery
								Address</label>
							<div class="col-sm-3">
			<textarea class="form-control" name="po_address" disabled id="po_address"
					  placeholder="Enter Delivery Address" autocomplete="off"><?php echo $rporder_list->rporder_address ?></textarea>
								<small
										class="invalid-feedback po_address"><?php echo form_error('po_address'); ?></small>
							</div>

							<label for="cono1" class="col-sm-3 text-right control-label col-form-label">Delivery
								Note</label>
							<div class="col-sm-3">
			<textarea class="form-control" name="po_dl_note" disabled id="po_dl_note"
					  placeholder="Enter Delivery Note" autocomplete="off"><?php echo $rporder_list->rporder_delivery_note ?></textarea>
								<small
										class="invalid-feedback po_dl_note"><?php echo form_error('po_dl_note'); ?></small>
							</div>
						</div>
						<div class="form-group row">
							<label for="cono1" class="col-sm-2 text-right control-label col-form-label">Delivery
								Date</label>
							<div class="col-sm-3">
								<input class="form-control" type="date" disabled name="po_delivery_date" value="<?php echo date('Y-m-d',strtotime($rporder_list->rporder_delivery_date)) ?>" id="po_delivery_date"
									   placeholder="Enter Delivery Date" autocomplete="off">
								<small class="invalid-feedback po_delivery_date"><?php echo form_error('po_delivery_date'); ?></small>
							</div>
						</div>

						<div class="form-group row justify-content-md-center">
							<div class="col-sm-12">
								<div class="table-responsive1">
									<?php $total_tax = 0;
									$sub_total = 0;
									?>
									<table width="100%" class="table table-bordered d-block" id="porder_table">
										<thead>
										<tr>

											<th style="min-width:120px!important;overflow:hidden;word-wrap:break-word;white-space: normal;vertical-align:middle;<?php if($this->session->userdata('utype')==4){echo 'display:none';}?>">Item</th>
											<th style="min-width:120px!important;overflow:hidden;word-wrap:break-word;white-space: normal;vertical-align:middle;<?php if($this->session->userdata('utype')!=4){echo 'display:none';}?>">Sku No</th>
											<th style="min-width:300px!important;overflow:hidden;word-wrap:break-word;white-space: normal;vertical-align:middle">Description</th>
											<th style="min-width:120px!important;overflow:hidden;word-wrap:break-word;white-space: normal;vertical-align:middle;<?php if($this->session->userdata('utype')==4){echo 'display:none';}?>">CostCode</th>
											<th style="min-width:90px!important;overflow:hidden;word-wrap:break-word;white-space: normal;vertical-align:middle">UOM</th>
											<th style="min-width:120px!important;overflow:hidden;word-wrap:break-word;white-space: normal;vertical-align:middle">Quantity</th>
											<th style="min-width:120px!important;overflow:hidden;word-wrap:break-word;white-space: normal;vertical-align:middle">Unit Price</th>
											<th style="min-width:120px!important;overflow:hidden;word-wrap:break-word;white-space: normal;vertical-align:middle">Tax Group</th>
											<th style="min-width:160px!important;overflow:hidden;word-wrap:break-word;white-space: normal;vertical-align:middle">Pre-Tax Amount</th>
											<th style="min-width:160px!important;overflow:hidden;word-wrap:break-word;white-space: normal;vertical-align:middle">Tax Amount</th>
											<th style="min-width:80px!important;overflow:hidden;word-wrap:break-word;white-space: normal;vertical-align:middle">Action</th>
										</tr>
										</thead>
										<tbody class="setall_experiences" id="porder_table_body">
								<?php foreach($item_detailsets as $key=>$item) {
									$total_tax += $item->rfq_detail_taxamount;
									$sub_total += $item->rfq_detail_subtotal;
									?>
									<tr data-id="<?php echo $key+1 ?>">
										<input type="hidden" name="rfq_detail_autogen[]" id="rfq_detail_autogen" value="<?php echo $item->rfq_detail_autogen?>">
											<td style="<?php if($this->session->userdata('utype')==4){echo 'display:none';}?>">
												<?php foreach ($itm_list as $items) {
													if($item->rfq_detail_item == $items->item_code) {?>
												<input type="text" disabled class="form-control" value="<?php echo $items->item_code . ' - ' . $items->item_name; ?>">
												<input type="hidden" name="pk_item[]" value="<?php echo $items->item_code; ?>">

													<?php }
												} ?>
												<?php if(($item->rfq_detail_item == $item->rfq_detail_description) || $item->rfq_detail_item == "") { ?>
												<input type="hidden" name="pk_item[]" value="">
												<?php } ?>
											</td>
											<td style="<?php if($this->session->userdata('utype')!=4){echo 'display:none';}?>">
												<input type="text" class="form-control pk_sku_no" name="pk_sku_no[]" readonly
														  id="pk_sku_no"
														  placeholder="Sku No" autocomplete="off" value="<?php if($item->supcat_sku_no!=''){echo $item->supcat_sku_no;}else{echo '-';}?>"/>
												<small
														class="invalid-feedback pk_sku_no_error"><?php echo form_error('pk_sku_no'); ?></small>
											</td>
											<td>
												<textarea type="text" class="form-control pk_desc" name="pk_desc[]" readonly
														  id="pk_desc"
														  placeholder="Item Description" autocomplete="off"><?php echo $item->rfq_detail_description?></textarea>
												<small
														class="invalid-feedback pk_desc_error"><?php echo form_error('pk_desc'); ?></small>
											</td>
											<td style="<?php if($this->session->userdata('utype')==4){echo 'display:none';}?>">
													<?php foreach ($ccode_list as $items) {
													if($item->rfq_detail_cc == $items->cc_id) {?>
													<input type="text" disabled class="form-control" value="<?php echo $items->cc_no; ?>">
													<input type="hidden" name="pk_ccode[]" value="<?php echo $items->cc_id; ?>">
													<?php }
													} ?>
											</td>

											<td>
													<?php foreach ($uom_list as $items) {
													if($item->rfq_detail_uom == $items->uom_id) {?>
													<input type="text" disabled class="form-control" value="<?php echo $items->uom_name; ?>">
													<input type="hidden" name="pk_uom[]" value="<?php echo $items->uom_id; ?>">
													<?php }
													} ?>
											</td>
											<td>
												<input type="number" class="form-control pk_itm_qnty"
													   name="pk_itm_qnty[]" value="<?php echo $item->rfq_detail_quantity?>"
													   id="pk_itm_qnty" placeholder="Enter Quantity" autocomplete="off"
													   onchange="goto_check_item_amounts(this);" data-id="<?php echo $key+1 ?>"/>
												<small
														class="invalid-feedback pk_itm_qnty_error"><?php echo form_error('pk_itm_qnty'); ?></small>
											</td>
											<td>
												<input type="hidden" class="form-control pk_sku" name="pk_sku[]"
													   id="pk_sku" value="<?php echo $item->rfq_detail_sku?>"
													   placeholder="Item SKU" autocomplete="off" readonly/>
												<input type="hidden" class="form-control pk_taxcode" name="pk_taxcode[]"
													   id="pk_taxcode" placeholder="Enter TaxCode" value="<?php echo $item->rfq_detail_taxcode?>" autocomplete="off"
													   readonly/>
												<input type="hidden" class="form-control pk_total_amt"
													   name="pk_total_amt[]" value="<?php echo $item->rfq_detail_total?>"
													   id="pk_total_amt" placeholder="Item Total Amount"
													   autocomplete="off" readonly/>
												<input type="hidden" class="form-control pk_subtotal"
													   name="pk_subtotal[]" value="<?php echo $item->rfq_detail_subtotal?>"
													   id="pk_subtotal" placeholder="Item Sub Total" autocomplete="off"
													   readonly/>
												<input type="number" class="form-control pk_itm_price"
													   name="pk_itm_price[]" value="<?php echo $item->rfq_detail_unitprice?>"
													   id="pk_itm_price" placeholder="Enter Price" data-id="<?php echo $key+1 ?>"
													   autocomplete="off"
													   onchange="goto_check_item_amounts(this);"/>
												<small
														class="invalid-feedback pk_itm_price_error"><?php echo form_error('pk_itm_price'); ?></small>
											</td>

											<td>
												<select class="form-control select2 custom-select pk_tax_group"
														data-live-search="true" autocomplete="off" name="pk_tax_group[]"
														id="pk_tax_group"
														data-id="<?php echo $key+1 ?>"
														onchange="goto_addTax(this);">
													<option value="">---Select---</option>
													<?php foreach ($taxgroup_list as $items) { ?>
														<option
																value="<?php echo $items->id; ?>" <?php echo $item->rfq_detail_tax_group == $items->id ? 'selected'  : ''?>><?php echo $items->name; ?></option>
													<?php } ?>
												</select>
												<small
														class="invalid-feedback pk_tax_group_error"><?php echo form_error('pk_tax_group'); ?></small>
											</td>
											<td>
												<input type="text" class="form-control pre_tax_amt" name="pre_tax_amt[]"
													   id="pre_tax_amt" placeholder="Pre-Tax Amount" autocomplete="off" value="<?php echo $item->rfq_detail_subtotal?>"
													   readonly/>
												<small
														class="invalid-feedback pre_tax_amt_error"><?php echo form_error('pre_tax_amt'); ?></small>
											</td>
											<td>
												<input type="text" class="form-control pk_tax_amt" name="pk_tax_amt[]" value="<?php echo $item->rfq_detail_taxamount?>"
													   id="pk_tax_amt" placeholder="Item Tax Amount" autocomplete="off"
													   readonly/>
												<small
														class="invalid-feedback pk_tax_amt_error"><?php echo form_error('pk_tax_amt'); ?></small>
											</td>
										</tr>
										<?php  } ?>
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
											<td><span id="pk_sub_total"><?php 
												$formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
												echo $formatter->formatCurrency($sub_total, 'USD');?></span></td>
										</tr>
										<tr>
											<th>Total Tax</th>
											<td><span id="pk_total_tax"><?php echo $formatter->formatCurrency($total_tax, 'USD');?></span></td>
										</tr>
										<tr>
											<th>PO Total</th>
											<td><span id="total_po"><?php echo $formatter->formatCurrency($rporder_list->rporder_total_amount, 'USD');?></span></td>
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
							<?php if($this->session->userdata('utype')!=4){?>
								<?php if($rporder_list->rporder_status=='waiting for response' || $rporder_list->rporder_status=='updated by supplier'){?>
									<button type="button" onclick="gotoclclickbutton('completed');" class="btn btn-primary">Received</button>
								<?php }?>
							<button type="button" onclick="gotoclclickbutton('completed & converted to po')" class="btn btn-primary">	
								<?php if($rporder_list->rporder_status=='waiting for response' || $rporder_list->rporder_status=='updated by supplier'){?>
									Receive & Convert to PO
								<?php }else{?>
									Convert to PO
								<?php }?>
							</button>
							<?php }else{?>
								<button type="button" onclick="gotoclclickbutton('updated by supplier');" class="btn btn-primary">Update</button>
							<?php }?>

							&nbsp;<a href="<?= site_url('admincontrol/rfqorder/all_rfq_list') ?>"
									 class="btn btn-danger"><?php echo $view == 1 ? "Back" : "Cancel" ?></a>
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
	<?php $this->load->view('admin/component/footer') ?>

	<script type="text/javascript">
		$(function () {
			$('.alert-error, .invalid-feedback').delay(8000).fadeOut();
			$(".select2").selectpicker();
		});

		$(".select2").each(function (i, obj) {
			$(this).selectpicker();
		});


		<?php if($view) { ?>

		$(document).ready(function() {
			$("#myForm :input").prop("disabled", true);
		});

		<?php } ?>

		

		function goto_check_item_amounts(element) {
			var row_id = $(element).data('id');
			var pk_itm_qnty = $('[name="pk_itm_qnty[]"]').eq(row_id - 1).val();
			var pk_itm_price = $('[name="pk_itm_price[]"]').eq(row_id - 1).val();
			var pk_taxcode = $('[name="pk_tax_group[]"] option:selected').eq(row_id - 1).val();
			var pre_tax_amt = $('[name="pre_tax_amt[]"]').eq(row_id - 1).val();
			var tax_amt = $('[name="pk_taxcode[]"]').eq(row_id-1).val();
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

			if (pk_itm_qnty != "" && pk_itm_price != "") {
				var alltotal = parseFloat(subtotal_amount) + parseFloat(tax_amount);
				$('[name="pk_subtotal[]"]').eq(row_id - 1).val(subtotal_amount);
				$('[name="pre_tax_amt[]"]').eq(row_id - 1).val(parseFloat(pk_itm_price) * parseFloat(pk_itm_qnty));
				$('[name="pk_tax_amt[]"]').eq(row_id - 1).val(tax_amount);
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
				$('#pk_sub_total').text('$ '+total_subamount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
				$('#pk_total_tax').text('$ '+total_tax.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
				$('#total_po').text('$ '+total_amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
				$('#itemdtl_tamount').val('$ '+total_amount.toFixed(2));
			} else {
				$('[name="pk_subtotal[]"]').eq(row_id - 1).val('');
				$('[name="pk_tax_amt[]"]').eq(row_id - 1).val('');
				$('[name="pk_total_amt[]"]').eq(row_id - 1).val('');
				$('[name="pre_tax_amt[]"]').eq(row_id - 1).val('');
			}
		}

		function goto_check_project() {
			var po_project = $('#po_project option:selected').val();

			if (po_project != "") {
				var form_data = new FormData();
				form_data.append('po_project', po_project);

				$.ajax({
					method: 'POST',
					url: '<?php echo base_url() . "admincontrol/porder/get_address_from_porject_find"; ?>',
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
				var pk_item = $('.pk_item:eq(' + row_id + ') option:selected').val();

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
									$('[name="pk_sku[]"]').eq(row_id - 1).val(data.supp_set.supcat_sku_no);
									$('[name="pk_itm_price[]"]').eq(row_id - 1).val(data.supp_set.supcat_price);
								}
								$('select[name="pk_uom[]"]').eq(row_id - 1).find('option:contains("'+data.s_msg.uom_name+'")').attr("selected",true);
								$('.pk_uom').selectpicker('refresh');

								$('[name="pk_desc[]"]').eq(row_id - 1).val(data.s_msg.item_description);

								if ("e_msg" in data) {

									$('.get_error_total9').html(data.e_msg);
									$(".get_error_total9").fadeIn();
									setTimeout(function () {
										$('.invalid-feedback, .get_error_total9').fadeOut();
									}, 5000);
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

		function goto_addTax(element) {

			var row_id = $(element).data('id');
			var tax_group = $(element).children("option:selected").val();

			var form_data = new FormData();
			form_data.append('tax_group', tax_group);

			$.ajax({
				method: 'POST',
				url: '<?php echo base_url() . "admincontrol/porder/get_tax_detail"; ?>',
				data: form_data,
				dataType: 'JSON',
				contentType: false,
				processData: false,
				success: function (data) {
					//alert(data.msg);
					if (data.msg == 1) {
						var pre_tax_amt = $('[name="pre_tax_amt[]"]').eq(row_id - 1).val();
						var tax_amount = (parseFloat(pre_tax_amt)/100) * data.taxgroup.percentage;
						if (isNaN(parseFloat(tax_amount))) {
							return tax_amount = 0
						}
						$('[name="pk_taxcode[]"]').eq(row_id - 1).val(data.taxgroup.percentage);
						$('[name="pk_tax_amt[]"]').eq(row_id - 1).val(tax_amount);

						var alltotal = parseFloat(pre_tax_amt) + parseFloat(tax_amount);
						if (isNaN(parseFloat(alltotal))) {
							return alltotal = 0
						}
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
						$('#pk_sub_total').text('$ '+total_subamount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
						$('#pk_total_tax').text('$ '+total_tax.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
						$('#total_po').text('$ '+total_amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
						$('#itemdtl_tamount').val('$ '+total_amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));

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
			$('#Modal_addrecord').modal('show');
		}

		jQuery(document).delegate('a.add_row', 'click', function (e) {
			e.preventDefault();
			var size = jQuery('#porder_table >tbody >tr').length + 1;
			var content = '<tr data-id="' + size + '">\n' +
					'\t\t\t\t<td>\n' +
					'\t\t\t\t\t<select class="form-control select2 custom-select pk_item" data-id="' + size + '" name="pk_item[]"\n' +
					'\t\t\t\t\t\t\tdata-live-search="true" autocomplete="off"\n' +
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
					'\t\t\t\t\t\t\t\t\t\t\t\t\t\tdata-live-search="true" autocomplete="off" data-id="1">\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="">---Select---</option>\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php foreach ($ccode_list as $items) { ?>\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="<?php echo $items->cc_id; ?>"><?php echo $items->cc_no; ?></option>\n' +
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
					'\t\t\t\t\t<input type="number" class="form-control" name="pk_itm_price[]"\n' +
					'\t\t\t\t\t\t   id="pk_itm_price" placeholder="Enter Price" data-id="' + size + '" autocomplete="off"\n' +
					'\t\t\t\t\t\t   onchange="goto_check_item_amounts(this);"/>\n' +
					'\t\t\t\t\t<small class="invalid-feedback pk_itm_price"><?php echo form_error("pk_itm_price"); ?></small>\n' +
					'\t\t\t\t</td>\n' +
					'\t\t\t\t<td>\n' +
					'\t\t\t\t\t<select class="form-control select2 custom-select pk_tax_group"\n' +
					'\t\t\t\t\t\t\tdata-live-search="true" data-id="' + size + '" autocomplete="off" name="pk_tax_group[]"\n' +
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
							data.s_msg.forEach(function (item) {
								var size = jQuery('#porder_table >tbody >tr').length + 1;

								var content = '<tr data-id="' + size + '">\n' +
										'\t\t\t\t<td>\n' +
										'\t\t\t\t\t<select class="form-control select2 custom-select pk_item" data-id="' + size + '" name="pk_item[]"\n' +
										'\t\t\t\t\t\t\tdata-live-search="true" autocomplete="off"\n' +
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
										'\t\t\t\t\t\t\t\t\t\t\t\t\t\t  placeholder="Item Description" autocomplete="off"></textarea>\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t<small\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t\tclass="invalid-feedback pk_desc_error"><?php echo form_error("pk_desc"); ?></small>\n' +
										'\t\t\t\t\t\t\t\t\t\t\t</td>' +
										'\t\t\t\t<td>\n' +
										'\t\t\t\t\t<input type="text" class="form-control" name="pk_ccode[]" value="' + item.po_detail_cost_code + '" id="pk_ccode"\n' +
										'\t\t\t\t\t\t   placeholder="Item Cost Code" autocomplete="off" readonly/>\n' +
										'\t\t\t\t\t<small class="invalid-feedback pk_ccode"><?php echo form_error("pk_ccode"); ?></small>\n' +
										'\t\t\t\t</td>\n' +
										'\t\t\t\t<td>\n' +
										'\t\t\t\t\t<input type="text" class="form-control" name="pk_uom[]" value="' + item.po_detail_uom + '" id="pk_uom"\n' +
										'\t\t\t\t\t\t   placeholder="Item UOM" autocomplete="off" readonly/>\n' +
										'\t\t\t\t\t<small class="invalid-feedback pk_uom"><?php echo form_error("pk_uom"); ?></small>\n' +
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
										'\t\t\t\t\t\t\tdata-live-search="true" data-id="' + size + '"  autocomplete="off" name="pk_tax_group[]"\n' +
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
								$('#porder_table_body tr[data-id=' + size + ']').find('select option[value=' + item.po_detail_item + ']').attr('selected', 'selected');
								$('input[name="pk_itm_price[]"]').trigger('change');
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
			var po_project = $('#po_project').val();
			var po_number = $('#po_number').val();
			var po_number_prefix = $('#po_number_prefix').val();
			var po_supp = $('#po_supp').val();
			var po_address = $('#po_address').val();
			var po_delivery_date = $('#po_delivery_date').val();
			var po_desc = $('#po_desc').val();
			var po_dl_note = $('#po_dl_note').val();
			var po_id = $('#po_id').val();
//var ap_quaran = $("input[name='ap_quaran']:checked").val();

			var pk_item = $('input[name="pk_item[]"]').map(function () {
				return this.value; // $(this).val()
			}).get();

			var pk_ccode = $('input[name="pk_ccode[]"]').map(function () {
				return this.value; // $(this).val()
			}).get();
			var pk_sku = $('input[name="pk_sku[]"]').map(function () {
				return this.value; // $(this).val()
			}).get();
			var pk_uom = $('input[name="pk_uom[]"]').map(function () {
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
			var rfq_detail_autogen = $('input[name="rfq_detail_autogen[]"]').map(function () {
				return this.value; // $(this).val()
			}).get();


			if (ipack_itm_no == "") {
				e_error = 1;
				error_message = error_message + "<br/>ID missing, Refresh the page";
			}

			// if (pk_sku == "") {
			// 	e_error = 1;
			// 	$('.pk_sku').html('SKU is Required.');
			// } else {
			// 	$('.pk_sku').html('');
			// }


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
				$('.pk_itm_price').html('Price is Required.');
			} else {
				$('.pk_itm_price').html('');
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
				error_message = error_message + "<br/>Item not found in the Purchase Order, Add some Item.";
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
				form_data.append("po_id", po_id);

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
				form_data.append('rfq_detail_autogen[]', JSON.stringify(rfq_detail_autogen));
				form_data.append("status", status);

				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/rfqorder/modify_rfq_set_submission') ?>",
					dataType: 'json',
					data: form_data,
					contentType: false,
					cache: true,
					processData: false,
					success: function (data) {
						// alert(JSON.stringify(data));
						if (data.msg == 1) {
							console.log(data);
							//alert(data.msg[0].space_rate);
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

		function receiveAndPO(status) {
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
			var po_id = $('#po_id').val();
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
			var rfq_detail_autogen = $('input[name="rfq_detail_autogen[]"]').map(function () {
				return this.value; // $(this).val()
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
				$('.pk_itm_price').html('Price is Required.');
			} else {
				$('.pk_itm_price').html('');
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
				form_data.append("po_id", po_id);

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
				form_data.append('rfq_detail_autogen[]', JSON.stringify(rfq_detail_autogen));
				form_data.append("status", status);

				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/rfqorder/receive_make_porder/'.$rporder_list->rporder_id) ?>",
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
								window.location.replace("<?php echo site_url('admincontrol/rfqorder/all_rfq_list') ?>");
							}, 2000);

						} else {
							$('.div_roller_total').fadeOut();
							$('#submit_record_btn').prop('disabled', false);
							$('.close_modal').show();
							error_message = data.e_msg;
							toastr.error(error_message, 'Error!');

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
	</script>
