<?php $this->load->view('admin/component/header') ?>

<?php 
$this->load->view('admin/component/menu'); 

$ccodeArray=array();
$ccodeDescArray=array();
$itemArray=array();
foreach($ccode_list as $cc){
	$ccodeArray+=array(
		$cc->cc_id => $cc->cc_no
	);
}
foreach($ccode_list as $cc){
	$ccodeDescArray+=array(
		htmlspecialchars($cc->cc_no) => htmlspecialchars($cc->cc_description)
	);
}
foreach($itm_list as $item){
	$itemArray+=array(
		htmlspecialchars($item->item_code, ENT_QUOTES) => htmlspecialchars($item->item_name, ENT_QUOTES)
	);
}
?>


<style>
	.box-body textarea,input,select {max-width: 500px;}
	.box-body textarea { resize: vertical; }
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
						View Purchase Order
					<?php } else {
						?>
						Update Purchase Order
						<?php
					} ?>
					</h4>
				<div class="ml-auto text-right">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="<?php echo base_url('admincontrol/dashboard'); ?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php if($view) { ?>
								View Purchase Order
								<?php } else {
									?>
									Update Purchase Order
								<?php
								} ?>
							</li>
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
										<a href="<?php echo base_url() . 'admincontrol/porder/print_porder_setpdf/' . $porder_list->porder_id; ?>"
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
								<input type="hidden" name="itemdtl_counter" id="itemdtl_counter" value="<?php echo $porder_list->porder_total_item; ?>" autocomplete="off" />
								<input type="hidden" name="itemdtl_tamount" id="itemdtl_tamount" value="<?php echo $porder_list->porder_total_amount; ?>" autocomplete="off" />
								<input type="hidden" name="po_id" id="po_id" value="<?php echo $porder_list->porder_id ?>">

								<select class="form-control select2 custom-select" name="po_project" id="po_project"
										data-live-search="true" autocomplete="off" onchange="goto_check_project();">
									<option value="">---Select---</option>
									<?php foreach ($proj_list as $p_items) { ?>
										<option
												value="<?php echo $p_items->proj_id; ?>" <?php if($p_items->proj_id == $porder_list->porder_project_ms){echo "selected";} ?>><?php echo $p_items->proj_name; ?></option>
									<?php } ?>
								</select>
								<?php foreach ($proj_list as $p_items) { ?>
									<input type="hidden" name="" id="project<?php echo $p_items->proj_id; ?>" value="<?php echo $p_items->proj_name; ?>">
								<?php } ?>
								<small
										class="invalid-feedback po_project"><?php echo form_error('po_project'); ?></small>
							</div>
							<label for="fname" class="col-sm-3 text-right control-label col-form-label">P.O.
								Number</label>
							<div class="col-sm-3">
								<div class="row">
									<?php $order_number = explode('-',$porder_list->porder_no)?>
									<input type="text" class="form-control col-5" readonly name="po_number_prefix"
										   id="po_number_prefix" value="<?php echo $order_number[0] ?>"
										   autocomplete="off"/>
									<input type="text" class="form-control col-5" name="po_number" value="<?php echo $order_number[1] ?>" id="po_number"
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
												value="<?php echo $sup_items->sup_id; ?>" <?php if($sup_items->sup_id == $porder_list->porder_supplier_ms){echo "selected";} ?>><?php echo $sup_items->sup_name; ?></option>
									<?php } ?>
								</select>
								<small class="invalid-feedback po_supp"><?php echo form_error('po_supp'); ?></small>
							</div>

							<label for="cono1" class="col-sm-3 text-right control-label col-form-label">PO
								Description</label>
							<div class="col-sm-3">
			<textarea class="form-control" name="po_desc" id="po_desc"
					  placeholder="Enter PO Description" autocomplete="off"><?php echo $porder_list->porder_description?></textarea>
								<small
										class="invalid-feedback po_desc"><?php echo form_error('po_description'); ?></small>
							</div>
						</div>
						<div class="form-group row">
							<label for="cono1" class="col-sm-2 text-right control-label col-form-label">Delivery
								Address</label>
							<div class="col-sm-3">
			<textarea class="form-control" name="po_address" id="po_address"
					  placeholder="Enter Delivery Address" autocomplete="off"><?php echo $porder_list->porder_address; ?></textarea>
								<small
										class="invalid-feedback po_address"><?php echo form_error('po_address'); ?></small>
							</div>

							<label for="cono1" class="col-sm-3 text-right control-label col-form-label">Delivery
								Note</label>
							<div class="col-sm-3">
			<textarea class="form-control" name="po_dl_note" id="po_dl_note"
					  placeholder="Enter Delivery Note" autocomplete="off"><?php echo $porder_list->porder_delivery_note; ?></textarea>
								<small
										class="invalid-feedback po_dl_note"><?php echo form_error('po_dl_note'); ?></small>
							</div>
						</div>
						<div class="form-group row">
							<label for="cono1" class="col-sm-2 text-right control-label col-form-label">Delivery
								Date</label>
							<div class="col-sm-3">
								<input class="form-control" type="date" value="<?php echo date('Y-m-d',strtotime($porder_list->porder_delivery_date)) ?>" name="po_delivery_date" id="po_delivery_date"
									   placeholder="Enter Delivery Date" autocomplete="off">
								<small class="invalid-feedback po_delivery_date"><?php echo form_error('po_delivery_date'); ?></small>
							</div>
							<label for="cono1" class="col-sm-3 text-right control-label col-form-label">Status</label>
							<div class="col-sm-3">
								<input class="form-control" type="text" disabled value="<?php echo $porder_list->porder_general_status ?>"
									   placeholder="" autocomplete="off">
								<small class="invalid-feedback po_delivery_date"><?php echo form_error('po_delivery_date'); ?></small>
							</div>
						</div>
						<input type="hidden" name="is_emailsent" id="is_emailsent" value="<?php echo $porder_list->is_email_sent;?>">
						<input type="hidden" name="email_sent_check" id="email_sent_check" value="true">
						<?php if(!$view) {
							?>
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
						<?php } ?>
						<div class="form-group row justify-content-md-center">
							<div class="col-sm-12">
								<div class="table-responsive">

									<?php $total_tax = 0;
									 $sub_total = 0;
									?>
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
											<th style="min-width:100px!important;overflow:hidden;word-wrap:break-word;white-space: normal;vertical-align:middle">Tax Group</th>
											<th style="min-width:160px!important;overflow:hidden;word-wrap:break-word;white-space: normal;vertical-align:middle">Pre-Tax Amount</th>
											<th style="min-width:160px!important;overflow:hidden;word-wrap:break-word;white-space: normal;vertical-align:middle">Tax Amount</th>
											<th style="min-width:80px!important;overflow:hidden;word-wrap:break-word;white-space: normal;vertical-align:middle">Action</th>
										</tr>
										</thead>
										<tbody class="setall_experiences" id="porder_table_body">
										<?php foreach($item_detailsets as $key=>$item) {
											$total_tax += $item->po_detail_taxamount;
											$sub_total += $item->po_detail_subtotal;
										?>
											<tr data-id="<?php echo $key+1 ?>">
												<input type="hidden" name="po_detail_autogen[]" id="po_detail_autogen" value="<?php echo $item->po_detail_autogen?>">
												<td>
												<select class="form-control select2 pk_item po_item_required_select po_item_required"
														name="pk_item[]"
														data-width="110px"
														data-container='body'
														id="pk_item<?php echo $key+1 ?>" 
														data-live-search="true" autocomplete="off" data-id="<?php echo $key+1 ?>"
														onchange="goto_check_item(this);">
													<option value="">---Select---</option>
													<?php foreach ($itm_list as $items) { ?>
														<option
																value="<?php echo $items->item_code; ?>" <?php echo $item->po_detail_item == $items->item_code ? 'selected'  : '' ?>><?php echo htmlspecialchars($items->item_code . ' - ' . $items->item_name, ENT_QUOTES); ?></option>
													<?php } ?>
												</select><br>
												<small class="invalid-feedback pk_item1<?php echo $key+1 ?>_error"><?php echo form_error('pk_item'.($key+1)); ?></small>
												<?php if($view){?>
												<?php }else{?>
												<a href="javascript:;" onclick="goto_advance_item_lookup(<?php echo $key+1 ?>);" class="btn btn-sm border border-danger " style="font-weight:bold"  data-toggle="tooltip" data-placement="top" title="Advanced Look-Up">...</a>
												<?php }?>
											</td>
											<td>
												<textarea type="text" class="form-control pk_desc po_item_required" name="pk_desc[]"
														  id="pk_desc<?php echo $key+1 ?>"
														  placeholder="Item Description" autocomplete="off"><?php echo $item->po_detail_description?> </textarea>
												<small
														class="invalid-feedback pk_desc<?php echo $key+1 ?>_error"><?php echo form_error('pk_desc'.($key+1)); ?></small>
											</td>
											<td>
												<select class="form-control select2 custom-select pk_ccode po_item_required po_item_required_select"
														name="pk_ccode[]"
														data-width="110px"
														data-container='body'
														id="pk_ccode<?php echo $key+1 ?>"
														data-live-search="true" autocomplete="off" data-id="<?php echo $key+1 ?>">
													<option value="">---Select---</option>
													<?php foreach ($ccode_list as $items) { ?>
														<option value="<?php echo $items->cc_id; ?>" <?php echo $item->po_detail_cc == $items->cc_id ? 'selected'  : '' ?> ><?php echo $items->cc_no.' - '.$items->cc_description; ?></option>
													<?php } ?>
												</select>

												<small
														class="invalid-feedback pk_ccode<?php echo $key+1 ?>_error"><?php echo form_error('pk_ccode'.($key+1)); ?></small>
											</td>

											<td>
												<select class="form-control select2 custom-select pk_uom po_item_required po_item_required_select"
														name="pk_uom[]" 
														data-container='body' data-width="80px" id="pk_uom<?php echo $key+1 ?>"
														data-live-search="true" autocomplete="off" data-id="<?php echo $key+1 ?>">
													<option value="">---Select---</option>
													<?php foreach ($uom_list as $items) { ?>
														<option value="<?php echo $items->uom_id; ?>" <?php echo $item->porder_detail_uom == $items->uom_id ? 'selected'  : ''?>><?php echo $items->uom_name; ?></option>
													<?php } ?>
												</select>
												<small
														class="invalid-feedback pk_uom<?php echo $key+1 ?>_error"><?php echo form_error('pk_uom'.($key+1)); ?></small>
											</td>
											<td>
												<input type="number" class="form-control pk_itm_qnty po_item_required"
													   name="pk_itm_qnty[]" value="<?php echo $item->po_detail_quantity?>"
													   id="pk_itm_qnty<?php echo $key+1 ?>" placeholder="Qty" autocomplete="off"
													   onchange="goto_check_item_amounts(this);" data-id="<?php echo $key+1 ?>"/>
												<small
														class="invalid-feedback pk_itm_qnty<?php echo $key+1 ?>_error"><?php echo form_error('pk_itm_qnty'.($key+1)); ?></small>
											</td>
											<td>
												<input type="hidden" class="form-control pk_sku" name="pk_sku[]"
													   id="pk_sku" value="<?php echo $item->po_detail_sku?>"
													   placeholder="Item SKU" autocomplete="off" readonly/>
												<input type="hidden" class="form-control pk_taxcode" name="pk_taxcode[]"
													   id="pk_taxcode" placeholder="Enter TaxCode" value="<?php echo $item->po_detail_taxcode?>" autocomplete="off"
													   readonly/>
												<input type="hidden" class="form-control pk_total_amt"
													   name="pk_total_amt[]" value="<?php echo $item->po_detail_total?>"
													   id="pk_total_amt" placeholder="Item Total Amount"
													   autocomplete="off" readonly/>
												<input type="hidden" class="form-control pk_subtotal" value="<?php echo $item->po_detail_subtotal?>"
													   name="pk_subtotal[]"
													   id="pk_subtotal" placeholder="Item Sub Total" autocomplete="off"
													   readonly/>
												<input type="number" class="form-control pk_itm_price po_item_required" value="<?php echo $item->po_detail_unitprice?>"
													   name="pk_itm_price[]"
													   id="pk_itm_price<?php echo $key+1 ?>" placeholder="Price" data-id="<?php echo $key+1 ?>"
													   autocomplete="off"
													   onchange="goto_check_item_amounts(this);"/>
												<small
														class="invalid-feedback pk_itm_price<?php echo $key+1 ?>_error"><?php echo form_error('pk_itm_price'.($key+1)); ?></small>
											</td>

											<td>
												<select class="form-control select2 custom-select pk_tax_group po_item_required po_item_required_select"
														data-live-search="true" data-container="body" data-width="80px" autocomplete="off" name="pk_tax_group[]"
														id="pk_tax_group<?php echo $key+1 ?>"
														data-id="<?php echo $key+1 ?>"
														onchange="goto_addTax(this);">
													<option value="">Select</option>
													<?php foreach ($taxgroup_list as $items) { ?>
														<option
																value="<?php echo $items->id; ?>" <?php echo $item->po_detail_tax_group == $items->id ? 'selected'  : ''?>><?php echo $items->name; ?></option>
													<?php } ?>
												</select>
												<small
														class="invalid-feedback pk_tax_group<?php echo $key+1 ?>_error"><?php echo form_error('pk_tax_group'.($key+1)); ?></small>
											</td>
											<td>
												<input type="text" class="form-control pre_tax_amt" name="pre_tax_amt[]" value="<?php echo $item->po_detail_subtotal?>"
													   id="pre_tax_amt<?php echo $key+1 ?>" placeholder="Pre-Tax Amount" autocomplete="off"
													   readonly/>
												<small
														class="invalid-feedback pre_tax_amt<?php echo $key+1 ?>_error"><?php echo form_error('pre_tax_amt'.($key+1)); ?></small>
											</td>
											<td>
												<input type="text" class="form-control pk_tax_amt" name="pk_tax_amt[]" value="<?php echo $item->po_detail_taxamount?>"
													   id="pk_tax_amt<?php echo $key+1 ?>" placeholder="Item Tax Amount" autocomplete="off"
													   readonly/>
												<small
														class="invalid-feedback pk_tax_amt<?php echo $key+1 ?>_error"><?php echo form_error('pk_tax_amt'.($key+1)); ?></small>
											</td>
											<td>
												<?php if(!$view) { ?>
												<a class="btn btn-outline-warning delete-record" data-id="<?php echo $key+1 ?>"><i class="fa fa-trash text-danger"></i></a>
												<?php } ?>
											</td>
										</tr>
										<?php } ?>
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
											<td><span id="pk_sub_total">
												<?php 
													$formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
													echo $formatter->formatCurrency($sub_total, 'USD');
												?>
											</span></td>
										</tr>
										<tr>
											<th>Total Tax</th>
											<td><span id="pk_total_tax"><?php echo $formatter->formatCurrency($total_tax, 'USD');?></span></td>
										</tr>
										<tr>
											<th>PO Total</th>
											<td><span id="total_po"><?php echo $formatter->formatCurrency($porder_list->porder_total_amount, 'USD');?></span></td>
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
							<?php if(!$view) { ?>
							<button type="button" onclick="gotoclclickbutton('pending');" class="btn btn-primary">Update</button>
							<?php } ?>
							<button type="button" onclick="gotoclclickbutton('submitted');" class="btn btn-primary">Send</button>

							&nbsp;<a href="<?= site_url('admincontrol/porder/all_purchase_order_list') ?>"
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
					<!-- <button type="button" id="submit_record_btn" class="btn btn-primary"
							onclick="goto_submit_record();">Submit
					</button> -->
					<button type="button" id="submit_record_btn" class="btn btn-primary"
							onclick="addItemsFromPackage();">Submit
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

	<!-- Modal -->
	<div class="modal fade" id="Modal_budgetsummary" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
		 aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Budget Overage Warning</h5>
					<button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="table-responsive">
						<table class="table table-bordered" id="po_summary_table">
							<!-- <thead>
								<tr>
									<th>S NO</th>
									<th>COST CODE</th>
									<th>ITEM</th>
									<th>REVISED BUDGET</th>
									<th>COMMITTED COST</th>
									<th>PO COST</th>
									<th>TOTAL COMMITTED COST</th>
									<th>BALANCE</th>
									<th>STATUS</th>
								</tr>
							</thead> -->
							<tbody></tbody>
						</table>
					</div>
				</div>
				<div class="modal-footer">
					<!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>-->
					<!-- <button type="button" id="select_multiple_items_btn" class="btn btn-primary">Submit
					</button> -->
					<div class="col-12 text-center text-danger" style="padding:10px 80px;border:2px solid red">
						<h4>
							Please contact your supervisor to add budget to the cost codes in order to submit this PO.
						</h4>
					</div>
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


	<!-- Modal -->
	<div class="modal fade" id="Modal_reemailcheck" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
		 aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" style="font-size:18px;font-weight:bold">Resend Email Warning</h5>
					<button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body" style="font-size:18px">
					<span>Do you want to send email to supplier again ?</span>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" id="email_check_no_btn">No</button>
					<button type="button" id="email_check_yes_btn" class="btn btn-primary">Yes
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
		var ccArray='<?php echo json_encode($ccodeArray);?>';
		var ccDescArray='<?php echo json_encode($ccodeDescArray);?>';
		var ccArray1=JSON.parse(ccArray);
		var ccArray2=JSON.parse(ccDescArray);
		var itemArray='<?php echo json_encode($itemArray);?>';
		var itemArray1=JSON.parse(itemArray);
		var revisedCostArray=new Array();
		var committedCostArray=new Array();
		var supplierEmailId='';
		var mannualPoStatus=1;
		var poStatusChecKForEmail='';
		$(function () {
			$('.alert-error, .invalid-feedback').delay(8000).fadeOut();
			$(".select2").selectpicker();
			$('.select2').selectpicker('refresh');
		});

		$(".select2").each(function (i, obj) {
			$(this).selectpicker();
		});

		<?php if($view) { ?>

		$(document).ready(function() {
			$("#myForm :input").prop("disabled", true);
		});

		<?php } ?>

		// function goto_check_item_amounts(element) {
		// 	var row_id = $(element).attr('data-id');
		// 	row_id = row_id-1;
		// 	var pk_itm_qnty = $('[name="pk_itm_qnty[]"]').eq((row_id)).val();
		// 	var pk_itm_price = $('[name="pk_itm_price[]"]').eq((row_id)).val();
		// 	var pk_taxcode = $('[name="pk_tax_group[]"] option:selected').eq((row_id)).val();
		// 	var pre_tax_amt = $('[name="pre_tax_amt[]"]').eq((row_id )).val();
		// 	var tax_amt = $('[name="pk_taxcode[]"]').eq((row_id)).val();
		// 	var tax_amount = 0;

		// 	var subtotal_amount = parseFloat(pk_itm_qnty) * parseFloat(pk_itm_price);

		// 	if (isNaN((subtotal_amount))) {
		// 		return subtotal_amount += 0
		// 	}

		// 	if (pk_itm_qnty != "" && pk_itm_price != "" && pk_taxcode != "") {
		// 		tax_amount = (parseFloat(subtotal_amount) / 100) * parseFloat(tax_amt);
		// 	} else {
		// 		tax_amount = 0;
		// 	}

		// 	var total_subamount = 0;
		// 	var total_tax = 0;
		// 	var total_amount = 0;

		// 	if (pk_itm_qnty != "" && pk_itm_price != "") {
		// 		var alltotal = parseFloat(subtotal_amount) + parseFloat(tax_amount);
		// 		$('[name="pk_subtotal[]"]').eq(row_id).val(subtotal_amount);
		// 		$('[name="pre_tax_amt[]"]').eq(row_id).val(parseFloat(pk_itm_price) * parseFloat(pk_itm_qnty));
		// 		$('[name="pk_tax_amt[]"]').eq(row_id).val(tax_amount);
		// 		$('[name="pk_total_amt[]"]').eq(row_id).val(alltotal);
		// 		$('#pk_total_tax').text(parseFloat($('#pk_total_tax').text()) + tax_amount);

		// 		$('input[name="pk_subtotal[]"]').map(function () {
		// 			if (isNaN(parseFloat(this.value))) {
		// 				return total_subamount += 0
		// 			} else {
		// 				return total_subamount += parseFloat(this.value); // $(this).val()
		// 			}

		// 		}).get();
		// 		$('input[name="pk_tax_amt[]"]').map(function () {
		// 			if (isNaN(parseFloat(this.value))) {
		// 				return total_tax += 0
		// 			} else {
		// 				return total_tax += parseFloat(this.value); // $(this).val()
		// 			}
		// 		}).get();
		// 		$('input[name="pk_total_amt[]"]').map(function () {
		// 			if (isNaN(parseFloat(this.value))) {
		// 				return total_amount += 0
		// 			} else {
		// 				return total_amount += parseFloat(this.value); // $(this).val()
		// 			}
		// 		}).get();

		// 		$('#pk_sub_total').text(total_subamount);
		// 		$('#pk_total_tax').text(total_tax);
		// 		$('#total_po').text(total_amount);
		// 		$('#itemdtl_tamount').val(total_amount);
		// 	} else {
		// 		$('[name="pk_subtotal[]"]').eq(row_id).val('');
		// 		$('[name="pk_tax_amt[]"]').eq(row_id).val('');
		// 		$('[name="pk_total_amt[]"]').eq(row_id).val('');
		// 		$('[name="pre_tax_amt[]"]').eq(row_id).val('');
		// 	}
		// }

		function goto_check_item_amounts(element) {
			var row_id = $(element).data('id');
			row_id = row_id-1;
			var pk_itm_qnty = $('[name="pk_itm_qnty[]"]').eq((row_id)).val();
			var pk_itm_price = $('[name="pk_itm_price[]"]').eq((row_id)).val();
			var pk_taxcode = $('[name="pk_tax_group[]"] option:selected').eq((row_id)).val();
			var pre_tax_amt = $('[name="pre_tax_amt[]"]').eq((row_id)).val();
			var tax_amt = $('[name="pk_taxcode[]"]').eq((row_id)).val();
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
				$('[name="pk_subtotal[]"]').eq(row_id).val(subtotal_amount);
				$('[name="pre_tax_amt[]"]').eq(row_id).val(parseFloat(pk_itm_price) * parseFloat(pk_itm_qnty));
				$('[name="pk_tax_amt[]"]').eq(row_id).val(tax_amount);
				$('[name="pk_total_amt[]"]').eq(row_id).val(alltotal);
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
				$('#itemdtl_tamount').val(total_amount.toFixed(2));
			} else {
				$('[name="pk_subtotal[]"]').eq(row_id).val('');
				$('[name="pk_tax_amt[]"]').eq(row_id).val('');
				$('[name="pk_total_amt[]"]').eq(row_id ).val('');
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
			row_id = (row_id - 1);
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
						url: '<?php echo base_url() . "admincontrol/porder/get_alldetails_from_item_find"; ?>',
						data: form_data,
						dataType: 'JSON',
						contentType: false,
						processData: false,
						success: function (data) {
							//alert(data.msg);
							if (data.msg == 1) {

								$('select[name="pk_ccode[]"]').eq(row_id).find('option[value="'+data.s_msg.item_ccode_ms+'"]').attr("selected",true);

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

									alert(data.e_msg);
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
						$('[name="pk_tax[]"]').eq(row_id - 1).val(data.taxgroup.percentage);

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
						$('#itemdtl_tamount').val(total_amount.toFixed(2));
					} else {
						$('[name="pk_tax_amt[]"]').eq(row_id - 1).val("");
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
			$('.packset_item').html('');
			var project_id=$('#po_project').val();
			getProjectPackages(project_id);
		}

		jQuery(document).delegate('a.add_row', 'click', function (e) {
			e.preventDefault();
			var size = jQuery('#porder_table >tbody >tr').length + 1;
			var content = '<tr data-id="' + size + '">\n' +
					'\t\t\t\t<td>\n' +
					'\t\t\t\t\t<select class="form-control select2 custom-select pk_item po_item_required_select  po_item_required" id="pk_item'+size+'" data-id="' + size + '" name="pk_item[]"\n' +
					'\t\t\t\t\t\t\tdata-live-search="true" data-container="body" data-width="110px" autocomplete="off"\n' +
					'\t\t\t\t\t\t\tonchange="goto_check_item(this);">\n' +
					'\t\t\t\t\t\t<option value="">---Select---</option>\n' +
					'\t\t\t\t\t\t<?php foreach ($itm_list as $items) { ?>\n' +
					'\t\t\t\t\t\t<option value="<?php echo $items->item_code; ?>"> <?php echo htmlspecialchars($items->item_code . ' - ' . $items->item_name, ENT_QUOTES); ?></option>\n' +
					'\t\t\t\t\t\t<?php } ?>\n' +
					'\t\t\t\t\t</select><br>\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t<small\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t\tclass="invalid-feedback pk_item'+size+'_error"></small>\n' +
					'\t\t\t\t\t<a href="javascript:;" onclick="goto_advance_item_lookup(' + size + ');" class="btn btn-sm border border-danger " style="font-weight:bold"  data-toggle="tooltip" data-placement="top" title="Advanced Look-Up">...</a>\n' +
					'\t\t\t\t</td>\n' +
					'<td>\n' +
					'\t\t\t\t\t\t<textarea type="text" class="form-control pk_desc po_item_required" name="pk_desc[]"\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t   id="pk_desc'+size+'"\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t\t  placeholder="Item Description" autocomplete="off"></textarea>\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t<small\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t\tclass="invalid-feedback pk_desc'+size+'_error"></small>\n' +
					'\t\t\t\t\t\t\t\t\t\t\t</td>' +
					'\t\t\t\t<td>\n' +
					'\t<select class="form-control select2 custom-select pk_ccode po_item_required  po_item_required_select"\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t\t name="pk_ccode[]"\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t\t id="pk_ccode'+size+'"\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t\t data-live-search="true" data-container="body" data-width="110px" autocomplete="off" data-id="1">\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="">---Select---</option>\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php foreach ($ccode_list as $items) { ?>\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="<?php echo $items->cc_id; ?>"><?php echo $items->cc_no.' - '.$items->cc_description; ?></option>\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php } ?>\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t</select><br>\n' +
					'\t\t\t\t\t<small class="invalid-feedback pk_ccode'+size+'_error"></small>\n' +
					'\t\t\t\t</td>\n' +
					'\t\t\t\t<td>\n' +
					'<select class="form-control select2 custom-select pk_uom po_item_required po_item_required_select"\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t\t name="pk_uom[]" data-width="80px" id="pk_uom'+size+'"\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t\t data-live-search="true" data-container="body" autocomplete="off" data-id="1">\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="">---Select---</option>\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php foreach ($uom_list as $items) { ?>\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="<?php echo $items->uom_id; ?>"><?php echo $items->uom_name; ?></option>\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php } ?>\n' +
					'\t\t\t\t\t\t\t\t\t\t\t\t</select><br>' +
					'\t\t\t\t\t<small class="invalid-feedback pk_uom'+size+'_error"></small>\n' +
					'\t\t\t\t</td>\n' +
					'\t\t\t\t<td>\n' +
					'\t\t\t\t\t<input type="number" class="form-control po_item_required" name="pk_itm_qnty[]"\n' +
					'\t\t\t\t\t\t   id="pk_itm_qnty'+size+'" placeholder="Enter Quantity" data-id="' + size + '" autocomplete="off"\n' +
					'\t\t\t\t\t\t   onchange="goto_check_item_amounts(this);"/>\n' +
					'\t\t\t\t\t<small class="invalid-feedback pk_itm_qnty'+size+'_error"></small>\n' +
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
					'\t\t\t\t\t<input type="number" class="form-control po_item_required" name="pk_itm_price[]"\n' +
					'\t\t\t\t\t\t   id="pk_itm_price'+size+'" placeholder="Enter Price" data-id="' + size + '" autocomplete="off"\n' +
					'\t\t\t\t\t\t   onchange="goto_check_item_amounts(this);"/>\n' +
					'\t\t\t\t\t<small class="invalid-feedback pk_itm_price'+size+'_error"></small>\n' +
					'\t\t\t\t</td>\n' +
					'\t\t\t\t<td>\n' +
					'\t\t\t\t\t<select class="form-control select2 custom-select pk_tax_group po_item_required po_item_required_select"\n' +
					'\t\t\t\t\t\t\t data-live-search="true" data-container="body" data-width="80px" data-id="' + size + '" autocomplete="off" name="pk_tax_group[]"\n' +
					'\t\t\t\t\t\t\t id="pk_tax_group' + size + '" onchange="goto_addTax(this);">\n' +
					'\t\t\t\t\t\t<option value="">---Select---</option>\n' +
					'\t\t\t\t\t\t<?php foreach ($taxgroup_list as $items) { ?>\n' +
					'\t\t\t\t\t\t<option value="<?php echo $items->id; ?>"><?php echo $items->name; ?></option>\n' +
					'\t\t\t\t\t\t<?php } ?>\n' +
					'\t\t\t\t\t</select><br>\n' +
					'\t\t\t\t\t<small class="invalid-feedback pk_tax_group' + size + '_error"></small>\n' +
					'\t\t\t\t</td>\n' +
					'\t\t\t\t<td>\n' +
					'\t\t\t\t\t<input type="text" class="form-control" name="pre_tax_amt[]"\n' +
					'\t\t\t\t\t\t   id="pre_tax_amt'+size+'" placeholder="Pre-Tax Amount" autocomplete="off"\n' +
					'\t\t\t\t\t\t   readonly/>\n' +
					'\t\t\t\t\t<small class="invalid-feedback pre_tax_amt'+size+'_error"></small>\n' +
					'\t\t\t\t</td>\n' +
					'\t\t\t\t<td>\n' +
					'\t\t\t\t\t<input type="text" class="form-control pk_tax_amt" name="pk_tax_amt[]"\n' +
					'\t\t\t\t\t\t   id="pk_tax_amt'+size+'" placeholder="Item Tax Amount" autocomplete="off"\n' +
					'\t\t\t\t\t\t   readonly/>\n' +
					'\t\t\t\t\t<small class="invalid-feedback pk_tax_amt'+size+'_error"></small>\n' +
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
							var mixstring = '<div class="col-sm-12"><input type="hidden" name="totalitem_pkg" id="totalitem_pkg" value="' + data.s_msg.length + '" /><table class="table" id="item_package_table"><thead><tr><th>Item Name</th><th>Item Code</th><th>Item Quantity</th></tr></thead><tbody>';
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
								var icount = jQuery('#porder_table >tbody >tr').length + 1;

								var content = '<tr data-id="' + size + '">\n' +
										'\t\t\t\t<td>\n' +
										'\t\t\t\t\t<select class="form-control select2 custom-select pk_item po_item_required_select  po_item_required" id="pk_item' + size+ '" readonly data-id="' + size + '" name="pk_item[]"\n' +
										'\t\t\t\t\t\t\tdata-live-search="true" data-container="body" data-width="110px" autocomplete="off"\n' +
										'\t\t\t\t\t\t\tonchange="goto_check_item(this);">\n' +
										'\t\t\t\t\t\t<option value="">---Select---</option>\n' +
										'\t\t\t\t\t\t<?php foreach ($itm_list as $items) { ?>\n' +
										'\t\t\t\t\t\t<option value="<?php echo $items->item_code; ?>" > <?php echo htmlspecialchars($items->item_code . ' - ' . $items->item_name, ENT_QUOTES); ?></option>\n' +
										'\t\t\t\t\t\t<?php } ?>\n' +
										'\t\t\t\t\t</select><br>\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t<small\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t\tclass="invalid-feedback pk_item' + size+ '_error"></small>\n' +
										'\t\t\t\t\t<a href="javascript:;" onclick="goto_advance_item_lookup(' + size + ');" class="btn btn-sm border border-danger " style="font-weight:bold"  data-toggle="tooltip" data-placement="top" title="Advanced Look-Up">...</a>\n' +
										'\t\t\t\t</td>\n' +
										'<td>\n' +
										'\t\t\t\t\t\t<textarea type="text" class="form-control pk_desc po_item_required" name="pk_desc[]"\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t   id="pk_desc' + size+ '"\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t\t  placeholder="Item Description" autocomplete="off">'+item.item_description+'</textarea>\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t<small\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t\tclass="invalid-feedback pk_desc' + size+ '_error"></small>\n' +
										'\t\t\t\t\t\t\t\t\t\t\t</td>' +
										'\t\t\t\t<td>\n' +
										'<select class="form-control select2 custom-select pk_ccode po_item_required po_item_required_select"\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t\tname="pk_ccode[]"\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t\id="pk_ccode'+size+'"\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t\tdata-live-search="true" data-container="body" data-width="110px" autocomplete="off" data-id="1">\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="">---Select---</option>\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php foreach ($ccode_list as $items) { ?>\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="<?php echo $items->cc_id; ?>" ><?php echo $items->cc_no.' - '.$items->cc_description; ?></option>\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php } ?>\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t</select><br>'+
										'\t\t\t\t\t\t\t\t\t\t\t\t<small\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t\tclass="invalid-feedback pk_ccode' + size+ '_error"></small>\n' +
										'\t\t\t\t</td>\n' +
										'\t\t\t\t<td>\n' +
										'<select class="form-control select2 custom-select pk_uom po_item_required po_item_required_select"\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t\tname="pk_uom[]" data-width="80px" id="pk_uom' + size+ '"\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t\tdata-live-search="true" data-container="body" autocomplete="off" data-id="1">\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="">---Select---</option>\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php foreach ($uom_list as $items) { ?>\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="<?php echo $items->uom_id; ?>"><?php echo $items->uom_name; ?></option>\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php } ?>\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t</select><br>'+
										'\t\t\t\t\t\t\t\t\t\t\t\t<small\n' +
										'\t\t\t\t\t\t\t\t\t\t\t\t\t\tclass="invalid-feedback pk_uom' + size+ '_error"></small>\n' +
										'\t\t\t\t</td>\n' +
										'\t\t\t\t<td>\n' +
										'\t\t\t\t\t<input type="number" class="form-control po_item_required" value="' + item.po_detail_quantity + '" name="pk_itm_qnty[]"\n' +
										'\t\t\t\t\t\t   id="pk_itm_qnty' + size+ '" placeholder="Enter Quantity" data-id="' + size + '" autocomplete="off"\n' +
										'\t\t\t\t\t\t   onchange="goto_check_item_amounts(this);"/>\n' +
										'\t\t\t\t\t<small class="invalid-feedback pk_itm_qnty' + size+ '_error"></small>\n' +
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
										'\t\t\t\t\t<input type="number" class="form-control po_item_required" value="' + item.po_detail_unitprice + '" name="pk_itm_price[]"\n' +
										'\t\t\t\t\t\t   id="pk_itm_price'+size+'" placeholder="Enter Price" data-id="' + size + '" autocomplete="off"\n' +
										'\t\t\t\t\t\t   onchange="goto_check_item_amounts(this);"/>\n' +
										'\t\t\t\t\t<small class="invalid-feedback pk_itm_price'+size+'_error"></small>\n' +
										'\t\t\t\t</td>\n' +
										'\t\t\t\t<td>\n' +
										'\t\t\t\t\t<select class="form-control select2 custom-select pk_tax_group po_item_required po_item_required_sekect"\n' +
										'\t\t\t\t\t\t\tdata-live-search="true" data-container="body" data-width="60px" data-id="' + size + '"  autocomplete="off" name="pk_tax_group[]"\n' +
										'\t\t\t\t\t\t\tid="pk_tax_group' + size + '" onchange="goto_addTax(this);">\n' +
										'\t\t\t\t\t\t<option value="">---Select---</option>\n' +
										'\t\t\t\t\t\t<?php foreach ($taxgroup_list as $items) { ?>\n' +
										'\t\t\t\t\t\t<option value="<?php echo $items->id; ?>"><?php echo $items->name; ?></option>\n' +
										'\t\t\t\t\t\t<?php } ?>\n' +
										'\t\t\t\t\t</select><br>\n' +
										'\t\t\t\t\t<small class="invalid-feedback pk_tax_group' + size + '_error"></small>\n' +
										'\t\t\t\t</td>\n' +
										'\t\t\t\t<td>\n' +
										'\t\t\t\t\t<input type="text" class="form-control" name="pre_tax_amt[]"\n' +
										'\t\t\t\t\t\t   id="pre_tax_amt' + size + '" placeholder="Pre-Tax Amount" autocomplete="off"\n' +
										'\t\t\t\t\t\t   readonly/>\n' +
										'\t\t\t\t\t<small class="invalid-feedback pre_tax_amt' + size + '_error"></small>\n' +
										'\t\t\t\t</td>\n' +
										'\t\t\t\t<td>\n' +
										'\t\t\t\t\t<input type="text" class="form-control pk_tax_amt" name="pk_tax_amt[]"\n' +
										'\t\t\t\t\t\t   id="pk_tax_amt' + size + '" placeholder="Item Tax Amount" autocomplete="off"\n' +
										'\t\t\t\t\t\t   readonly/>\n' +
										'\t\t\t\t\t<small class="invalid-feedback pk_tax_amt' + size + '_error"></small>\n' +
										'\t\t\t\t</td>\n' +
										'<td><a class="btn btn-outline-warning delete-record" data-id="' + size + '"><i class="fa fa-trash text-danger"></i></a></td>\n' +
										'\t\t\t</tr>';

								$('#porder_table_body').append(content);
								$('#porder_table_body tr[data-id=' + size + ']').find('select[name="pk_item[]"] option[value=' + item.po_detail_item + ']').attr('selected', 'selected');
								$('#porder_table_body tr[data-id=' + size + ']').find('select[name="pk_ccode[]"] option[value=' + item.po_detail_cost_code + ']').attr('selected', 'selected');
								$('#porder_table_body tr[data-id=' + size + ']').find('select[name="pk_uom[]"] option[value=' + item.porder_detail_uom + ']').attr('selected', 'selected');
								$('input[name="pk_itm_price[]"]').trigger('change');
								
								$('.pk_item').selectpicker('refresh');
								$('.pk_tax_group').selectpicker('refresh');
								var size = jQuery('#porder_table >tbody >tr').length;
								assign_data_id();
								add_row_action(size);

								icount++

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
			var is_emialsent = $('#is_emailsent').val();
			if(is_emialsent==1){
				$('#Modal_reemailcheck').modal('show');
			}else{

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
			var po_id = $('#po_id').val();
			var po_dl_note = $('#po_dl_note').val();
			// alert($('#email_sent_check').val());
			// var email_sent=false;
			// if(is_emialsent == 1){
			// 	email_sent=true;
			// } 
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

			var po_detail_autogen = $('input[name="po_detail_autogen[]"]').map(function () {
				return this.value; // $(this).val()
			}).get();

			$('input[name="pk_ccode[]"]').map(function () {
				return pk_ccode.push(this.value); // $(this).val()
			}).get();

			$('input[name="pk_uom[]"]').map(function () {
				return pk_uom.push(this.value); // $(this).val()
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
				alert('itemdtl_counter='+itemdtl_counter+'&---itemdtl_tamount='+itemdtl_tamount)
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

			$('.po_item_required,.po_item_required_select select').each(function(){
				var id=$(this).attr('id');
				var dataId=$(this).attr('data-id');
				if ($('#'+id).val() == "") {
					e_error = 1;
					$('.'+id+'_error').html('Required');
				} else {
					$('.'+id+'_error').html('');
				}
			});

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



				// alert(supplierEmailId);
				if((supplierEmailId=='' || supplierEmailId=='-') && mannualPoStatus==1 && status=='submitted'){
					mannualPoStatus=0;
					poStatusChecKForEmail=status;
					// e_error = 1;
					$('.div_roller_total').fadeOut();
					$('#Modal_supplieremial').modal('show');
				}

				if(mannualPoStatus==1 || mannualPoStatus==2){
					// alert(itemdtl_tamount);
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
				form_data.append("email_include", $('#email_sent_check').val());
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
				form_data.append('po_detail_autogen[]', JSON.stringify(po_detail_autogen));
				form_data.append("status", status);var poTableLength = $('#porder_table >tbody >tr').length;

				
				var projectName=$('#project'+po_project).val();
				var overageCount=0;

				if(projectName!='Sandbox Test Project'){
					$('#po_summary_table >tbody').html('');
					var poCostCodesArray=new Array();
					var poCostCodesDescArray=new Array();
					for(i=1;i<=poTableLength;i++){
						if(jQuery.inArray(ccArray1[$('#pk_ccode'+i).val()],poCostCodesArray) != -1){
						}else{
							poCostCodesArray.push(ccArray1[$('#pk_ccode'+i).val()]);
							poCostCodesDescArray.push($('#pk_desc'+i).val());
						}
					}

						
					for(j=0;j<poCostCodesArray.length;j++){
					var items='';
					var revisedCost=0;
					var committedCost=0;
					var totalItemsCost=0;
					$('.pk_ccode select').each(function(){
						var id= $(this).attr('data-id');
						var value = $('#pk_ccode'+id).val();
						// alert(ccArray1[value]);
						// alert(poCostCodesArray[j]);
						if(ccArray1[value] == poCostCodesArray[j]){
							items+='<tr><td colspan="2">'+$('#pk_item'+id).val()+' - '+itemArray1[$('#pk_item'+id).val()]+'</td><td>'+$('#pre_tax_amt'+id).val()+'</td></tr>';
							totalItemsCost=parseFloat(totalItemsCost)+parseFloat($('#pre_tax_amt'+id).val());
						}
					});
					// alert(committedCostArray[poCostCodesArray[j]]);
					if (poCostCodesArray[j] in revisedCostArray)
					{
					revisedCost=revisedCostArray[poCostCodesArray[j]];
					}else{
					}
					if (poCostCodesArray[j] in committedCostArray)
					{
						committedCost=committedCostArray[poCostCodesArray[j]];
					}
					var balance=(parseFloat(revisedCost)-(parseFloat(totalItemsCost)+parseFloat(committedCost))).toFixed(2);
					// alert(balance);
					if(balance<0){
					$('#po_summary_table >tbody').append('\
						<tr>\
							<td colspan="3" style="font-weight:bold">'+poCostCodesArray[j]+' - '+ccArray2[poCostCodesArray[j]]+'</td>\
						</tr>\
						<tr>\
							<td colspan="2"  style="font-weight:bold">items in the commitment ( commitment # ) that are exceeding cost code budget</td>\
							<td style="font-weight:bold">New Total Amount</td>\
						</tr>'+items+'\
						<tr>\
							<td colspan="2" style="font-weight:bold">Total New Committed Cost</td>\
							<td style="font-weight:bold">'+totalItemsCost+'</td>\
						</tr>\
						<tr>\
							<td style="font-weight:bold">Remaining<br>Budget<br>Balance</td>\
							<td style="font-weight:bold;vertical-align:middle">Revised Budget ( '+revisedCost+' ) - ( Committed Cost ( '+committedCost+' ) + Total New Committed Cost ( '+totalItemsCost+' ) )</td>\
							<td style="font-weight:bold;vertical-align:middle">'+balance+'</td>\
						</tr>\
					');
					overageCount++;
					$('.div_roller_total').fadeOut();
					}
				}
				}
				// alert(overageCount);
				if(overageCount>0){
					$('#Modal_budgetsummary').modal('show');
				}else{

				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/porder/modify_porder_set_submission') ?>",
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
								window.location.replace("<?php echo site_url('admincontrol/porder/all_purchase_order_list') ?>");
							}, 2000);

						} else {
							$('.div_roller_total').fadeOut();
							$('#submit_record_btn').prop('disabled', false);
							$('.close_modal').show();
							error_message = data.e_msg;
							// toastr.error(error_message, 'Error!');
							// $('.get_error_total').html(error_message);
							// $(".get_error_total").fadeIn();
							// setTimeout(function(){ $('.get_error_total').fadeOut(); }, delay);
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
			}}
			}
//$("#myForm").submit();

			}
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
						$('#pk_item'+itemId).val(itemCode).change();
					}else{
						var size = jQuery('#porder_table >tbody >tr').length + 1;
						var content = '<tr data-id="' + size + '">\n' +
						'\t\t\t\t<td>\n' +
						'\t\t\t\t\t<select class="form-control select2 custom-select pk_item po_item_required_select po_item_required" id="pk_item' + size + '" data-id="' + size + '" name="pk_item[]"\n' +
						'\t\t\t\t\t\t\tdata-live-search="true" data-container="body" data-width="110px" autocomplete="off"\n' +
						'\t\t\t\t\t\t\tonchange="goto_check_item(this);">\n' +
						'\t\t\t\t\t\t<option value="">---Select---</option>\n' +
						'\t\t\t\t\t\t<?php foreach ($itm_list as $items) { ?>\n' +
						'\t\t\t\t\t\t<option value="<?php echo $items->item_code; ?>"> <?php echo htmlspecialchars($items->item_code . ' - ' . $items->item_name, ENT_QUOTES); ?></option>\n' +
						'\t\t\t\t\t\t<?php } ?>\n' +
						'\t\t\t\t\t</select><br>\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t<small\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t\tclass="invalid-feedback pk_item'+ size +'_error"></small>\n' +
						'\t\t\t\t\t<a href="javascript:;" onclick="goto_advance_item_lookup(' + size + ');" class="btn btn-sm border border-danger " style="font-weight:bold"  data-toggle="tooltip" data-placement="top" title="Advanced Look-Up">...</a>\n' +
						'\t\t\t\t</td>\n' +
						'<td>\n' +
						'\t\t\t\t\t\t<textarea type="text" class="form-control pk_desc  po_item_required" name="pk_desc[]"\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t   id="pk_desc' + size + '"\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t\t  placeholder="Item Description" autocomplete="off"></textarea>\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t<small\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t\tclass="invalid-feedback pk_desc' + size + '_error"></small>\n' +
						'\t\t\t\t\t\t\t\t\t\t\t</td>' +
						'\t\t\t\t<td>\n' +
						'\t<select class="form-control select2 custom-select pk_ccode po_item_required po_item_required_select"\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t\t name="pk_ccode[]"\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t\t id="pk_ccode' + size + '"\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t\tdata-live-search="true" data-container="body" data-width="110px" autocomplete="off" data-id="1">\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="">---Select---</option>\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php foreach ($ccode_list as $items) { ?>\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="<?php echo $items->cc_id; ?>"><?php echo $items->cc_no.' - '.$items->cc_description; ?></option>\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php } ?>\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t</select><br>\n' +
						'\t\t\t\t\t<small class="invalid-feedback pk_ccode' + size + '_error"></small>\n' +
						'\t\t\t\t</td>\n' +
						'\t\t\t\t<td>\n' +
						'<select class="form-control select2 custom-select pk_uom po_item_required po_item_required_select"\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t\tname="pk_uom[]" data-width="80px" id="pk_uom' + size + '"\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t\tdata-live-search="true" data-container="body" autocomplete="off" data-id="1">\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="">---Select---</option>\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php foreach ($uom_list as $items) { ?>\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="<?php echo $items->uom_id; ?>"><?php echo $items->uom_name; ?></option>\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php } ?>\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t</select><br>' +
						'\t\t\t\t\t<small class="invalid-feedback pk_uom' + size + '_error"></small>\n' +
						'\t\t\t\t</td>\n' +
						'\t\t\t\t<td>\n' +
						'\t\t\t\t\t<input type="number" class="form-control po_item_required" name="pk_itm_qnty[]"\n' +
						'\t\t\t\t\t\t   id="pk_itm_qnty' + size + '" placeholder="Enter Quantity" data-id="' + size + '" autocomplete="off"\n' +
						'\t\t\t\t\t\t   onchange="goto_check_item_amounts(this);"/>\n' +
						'\t\t\t\t\t<small class="invalid-feedback pk_itm_qnty' + size + '_error"></small>\n' +
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
						'\t\t\t\t\t<input type="number" class="form-control po_item_required" name="pk_itm_price[]"\n' +
						'\t\t\t\t\t\t   id="pk_itm_price' + size + '" placeholder="Enter Price" data-id="' + size + '" autocomplete="off"\n' +
						'\t\t\t\t\t\t   onchange="goto_check_item_amounts(this);"/>\n' +
						'\t\t\t\t\t<small class="invalid-feedback pk_itm_price' + size + '_error"></small>\n' +
						'\t\t\t\t</td>\n' +
						'\t\t\t\t<td>\n' +
						'\t\t\t\t\t<select class="form-control select2 custom-select pk_tax_group po_item_required po_item_required_select"\n' +
						'\t\t\t\t\t\t\tdata-live-search="true" data-container="body" data-width="80px" data-id="' + size + '" autocomplete="off" name="pk_tax_group[]"\n' +
						'\t\t\t\t\t\t\tid="pk_tax_group' + size + '" onchange="goto_addTax(this);">\n' +
						'\t\t\t\t\t\t<option value="">---Select---</option>\n' +
						'\t\t\t\t\t\t<?php foreach ($taxgroup_list as $items) { ?>\n' +
						'\t\t\t\t\t\t<option value="<?php echo $items->id; ?>"><?php echo $items->name; ?></option>\n' +
						'\t\t\t\t\t\t<?php } ?>\n' +
						'\t\t\t\t\t</select><br>\n' +
						'\t\t\t\t\t<small class="invalid-feedback pk_tax_group' + size + '_error"></small>\n' +
						'\t\t\t\t</td>\n' +
						'\t\t\t\t<td>\n' +
						'\t\t\t\t\t<input type="text" class="form-control" name="pre_tax_amt[]"\n' +
						'\t\t\t\t\t\t   id="pre_tax_amt' + size + '" placeholder="Pre-Tax Amount" autocomplete="off"\n' +
						'\t\t\t\t\t\t   readonly/>\n' +
						'\t\t\t\t\t<small class="invalid-feedback pre_tax_amt' + size + '_error"></small>\n' +
						'\t\t\t\t</td>\n' +
						'\t\t\t\t<td>\n' +
						'\t\t\t\t\t<input type="text" class="form-control pk_tax_amt" name="pk_tax_amt[]"\n' +
						'\t\t\t\t\t\t   id="pk_tax_amt' + size + '" placeholder="Item Tax Amount" autocomplete="off"\n' +
						'\t\t\t\t\t\t   readonly/>\n' +
						'\t\t\t\t\t<small class="invalid-feedback pk_tax_amt' + size + '_error"></small>\n' +
						'\t\t\t\t</td>\n' +
						'<td><a class="btn btn-outline-warning delete-record" data-id="' + size + '"><i class="fa fa-trash text-danger"></i></a></td>\n' +
						'\t\t\t</tr>';
						$('#porder_table_body').append(content);
						add_row_action(size);
						assign_data_id();
						$('.pk_item').selectpicker('refresh');
						var id=itemIds[itemCount];
						var index=id.match(/\d+/);
						var itemCode=itemValues[itemCount];
						$('#pk_item'+size).val(itemCode).change();
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

		var projId=$('#po_project').val();

		if(projId!=''){
			getProjectBudget(projId);
		}

		function getProjectBudget(projectId){
			// alert(projectId);
			if(projectId!=''){
				var form_data = new FormData();
				form_data.append("project_id", projectId);

				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/porder/get_project_budget') ?>",
					dataType: 'json',
					data: form_data,
					contentType: false,
					cache: true,
					processData: false,
					success: function (data) {
						if (data.msg == 1) {
							console.log(data);
							revisedCostArray=JSON.parse(JSON.stringify(data.rv_msg));
							committedCostArray=JSON.parse(JSON.stringify(data.cc_msg));
							// alert(revisedCostArray["2-01-10 "]);
							// alert(committedCostArray["2-01-10 "]);
							// alert(JSON.stringify(data.cc_msg));
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
						alert(error);
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

		function addItemsFromPackage(){
			var itemCount=1;
			var itemCount1=0;
			// var itemFrom=$('input:radio[name="itemfrom"]:checked').val();
			$('#item_package_table >tbody >tr').each(function(){
				// if($(this).is(':checked')){
					if(itemCount==1 && $('#pk_item1').val()==''){
						// var itemId=$('#hidden-item-id').val();
						// var id=$(this).attr('id');
						// var index=id.match(/\d+/);
						// var itemCode=$('#search-item'+index).val();
						// $('#pk_item'+itemId).val(itemCode).change();
						var size = jQuery('#porder_table >tbody >tr').length;
						$('#pk_item'+size).val($('#ipackitem_code_'+itemCount1).val()).change();
						$('#pk_itm_qnty'+size).val($('#ipackitem_qty_'+itemCount1).val());
						itemCount++;
						itemCount1++;
					}else{
						var size = jQuery('#porder_table >tbody >tr').length + 1;
						var content = '<tr data-id="' + size + '">\n' +
						'\t\t\t\t<td>\n' +
						'\t\t\t\t\t<select class="form-control select2 custom-select po_item_required_select po_item_required pk_item" id="pk_item' + size + '" data-id="' + size + '" name="pk_item[]"\n' +
						'\t\t\t\t\t\t\tdata-live-search="true" data-container="body" data-width="110px" autocomplete="off"\n' +
						'\t\t\t\t\t\t\tonchange="goto_check_item(this);">\n' +
						'\t\t\t\t\t\t<option value="">---Select---</option>\n' +
						'\t\t\t\t\t\t<?php foreach ($itm_list as $items) { ?>\n' +
						'\t\t\t\t\t\t<option value="<?php echo $items->item_code; ?>"> <?php echo htmlspecialchars($items->item_code . ' - ' . $items->item_name, ENT_QUOTES); ?></option>\n' +
						'\t\t\t\t\t\t<?php } ?>\n' +
						'\t\t\t\t\t</select><br>\n' +
						'\t\t\t\t\t<a href="javascript:;" onclick="goto_advance_item_lookup(' + size + ');" class="btn btn-sm border border-danger " style="font-weight:bold"  data-toggle="tooltip" data-placement="top" title="Advanced Look-Up">...</a>\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t<small\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t\tclass="invalid-feedback pk_item' + size + '_error"><?php echo form_error("pk_desc"); ?></small>\n' +
						'\t\t\t\t</td>\n' +
						'<td>\n' +
						'\t\t\t\t\t\t<textarea type="text" class="form-control pk_desc po_item_required" name="pk_desc[]"\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t   id="pk_desc' + size + '"\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t\t  placeholder="Item Description" data-id="' + size + '" autocomplete="off"></textarea>\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t<small\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t\tclass="invalid-feedback pk_desc' + size + '_error"><?php echo form_error("pk_desc"); ?></small>\n' +
						'\t\t\t\t\t\t\t\t\t\t\t</td>' +
						'\t\t\t\t<td>\n' +
						'\t<select class="form-control select2 custom-select pk_ccode po_item_required_select po_item_required"\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t\t name="pk_ccode[]"\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t\ id="pk_ccode' + size + '"\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t\t data-live-search="true" data-container="body" data-width="110px" autocomplete="off" data-id="' + size + '">\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="">---Select---</option>\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php foreach ($ccode_list as $items) { ?>\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="<?php echo $items->cc_id; ?>"><?php echo $items->cc_no.' - '.$items->cc_description; ?></option>\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php } ?>\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t</select><br>\n' +
						'\t\t\t\t\t<small class="invalid-feedback pk_ccode' + size + '_error"><?php echo form_error("pk_ccode"); ?></small>\n' +
						'\t\t\t\t</td>\n' +
						'\t\t\t\t<td>\n' +
						'<select class="form-control select2 custom-select pk_uom po_item_required_select po_item_required"\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t\tname="pk_uom[]" data-width="80px" id="pk_uom' + size + '"\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t\tdata-live-search="true" data-container="body" autocomplete="off" data-id="' + size + '">\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="">---Select---</option>\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php foreach ($uom_list as $items) { ?>\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t\t<option value="<?php echo $items->uom_id; ?>"><?php echo $items->uom_name; ?></option>\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t\t<?php } ?>\n' +
						'\t\t\t\t\t\t\t\t\t\t\t\t</select><br>' +
						'\t\t\t\t\t<small class="invalid-feedback pk_uom' + size + '_error"><?php echo form_error("pk_uom"); ?></small>\n' +
						'\t\t\t\t</td>\n' +
						'\t\t\t\t<td>\n' +
						'\t\t\t\t\t<input type="number" class="form-control pk_itm_qnty po_item_required" name="pk_itm_qnty[]"\n' +
						'\t\t\t\t\t\t   id="pk_itm_qnty' + size + '" placeholder="Enter Quantity" data-id="' + size + '" autocomplete="off"\n' +
						'\t\t\t\t\t\t   onchange="goto_check_item_amounts(this);"/>\n' +
						'\t\t\t\t\t<small class="invalid-feedback pk_itm_qnty' + size + '_error"><?php echo form_error("pk_itm_qnty"); ?></small>\n' +
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
						'\t\t\t\t\t<input type="number" class="form-control pk_itm_price po_item_required" name="pk_itm_price[]"\n' +
						'\t\t\t\t\t\t   id="pk_itm_price' + size + '" placeholder="Enter Price" data-id="' + size + '" autocomplete="off"\n' +
						'\t\t\t\t\t\t   onchange="goto_check_item_amounts(this);"/>\n' +
						'\t\t\t\t\t<small class="invalid-feedback pk_itm_price' + size + '_error"><?php echo form_error("pk_itm_price"); ?></small>\n' +
						'\t\t\t\t</td>\n' +
						'\t\t\t\t<td>\n' +
						'\t\t\t\t\t<select class="form-control select2 custom-select pk_tax_group po_item_required po_item_required_select"\n' +
						'\t\t\t\t\t\t\tdata-live-search="true" data-width="80px" data-container="body" id="pk_tax_group' + size + '" data-id="' + size + '" autocomplete="off" name="pk_tax_group[]"\n' +
						'\t\t\t\t\t\t\tid="pk_tax_group" onchange="goto_addTax(this);">\n' +
						'\t\t\t\t\t\t<option value="">---Select---</option>\n' +
						'\t\t\t\t\t\t<?php foreach ($taxgroup_list as $items) { ?>\n' +
						'\t\t\t\t\t\t<option value="<?php echo $items->id; ?>"><?php echo $items->name; ?></option>\n' +
						'\t\t\t\t\t\t<?php } ?>\n' +
						'\t\t\t\t\t</select><br>\n' +
						'\t\t\t\t\t<small class="invalid-feedback pk_tax_group' + size + '_error"><?php echo form_error("pk_tax_group"); ?></small>\n' +
						'\t\t\t\t</td>\n' +
						'\t\t\t\t<td>\n' +
						'\t\t\t\t\t<input type="text" class="form-control pre_tax_amt" name="pre_tax_amt[]"\n' +
						'\t\t\t\t\t\t   id="pre_tax_amt' + size + '" placeholder="Pre-Tax Amount" autocomplete="off"\n' +
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
						// add_row_action(size);
						// assign_data_id();
						// $('.pk_item').selectpicker('refresh');
						// var id=$(this).attr('id');
						// var index=id.match(/\d+/);
						// var itemCode=$('#search-item'+index).val();
						// $('#pk_item'+size).val(itemCode).change();
						$('#pk_item'+size).val($('#ipackitem_code_'+itemCount1).val()).change();
						$('#pk_itm_qnty'+size).val($('#ipackitem_qty_'+itemCount1).val());
						$('.select2').selectpicker('refresh');
						itemCount++;
						itemCount1++;
					}
					// var id=$('.ipackitem_code').attr('id');
					// alert(id);
					// var index=id.match(/\d+/);
				// }
			});
		// 	var itemFrom=$('input:radio[name="itemfrom"]:checked').val();
		// 	var id=$(this).attr('id');
		// 	var index=id.match(/\d+/);
		// 	var itemId=$('#hidden-item-id').val();
		// 	if(itemFrom==1){
		// 		var itemCode=$('#search-item-code1'+index).val();
		// 	}else{
		// 		var itemCode=$('#search-item-code2'+index).val();
		// 	}
			$('#Modal_addrecord').modal('hide');
			// $('.search-item').prop('checked', false); 
		}

		$('#email_warning_ok_btn').click(function(){
			mannualPoStatus=2;
			$('#Modal_supplieremial').modal('hide');
			gotoclclickbutton(poStatusChecKForEmail);
		});
		$('#email_warning_close_btn').click(function(){
			mannualPoStatus=1;
		});

		$('#email_check_yes_btn').click(function(){
			$('#Modal_reemailcheck').modal('hide');
			$('#is_emailsent').val(0);
			gotoclclickbutton('submitted');
		});

		$('#email_check_no_btn').click(function(){
			$('#Modal_reemailcheck').modal('hide');
			$('#is_emailsent').val(0);
			$('#email_sent_check').val('false');
			gotoclclickbutton('submitted');
		});

		$('#po_supp').change(function(){
			// alert('change');
			getSupplierEmail($(this).val());
		});
		var checkSupplier=$('#po_supp').val();

		getSupplierEmail(checkSupplier);

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
	</script>
