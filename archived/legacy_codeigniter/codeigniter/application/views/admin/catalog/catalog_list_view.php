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
				<h4 class="page-title">Supplier Catalog List</h4>
				<div class="ml-auto text-right">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a
										href="<?php echo base_url('admincontrol/dashboard'); ?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Supplier Catalog List</li>
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
			<div class="col-12">
				<div class="card">
					<div class="card-body">
						<form action="" method="get" enctype="multipart/form-data">
							<div class="row">
								<div class="col-sm-5" style="padding:0px">
									<div class="row" style="padding:0px">
										<label class="col-sm-3 text-right control-label col-form-label">Item Category</label>
										<div class="col-sm-3">
											<select name="category" class="form-control selectpicker">
												<option value="">Item Category</option>

												<?php foreach ($cat_list as $cat) {?>
													<option value="<?php echo $cat->icat_id ?>" <?php echo isset($filters['item_cat_ms']) ? ($filters['item_cat_ms'] == $cat->icat_id ? 'selected' : '') : '' ?>><?php echo $cat->icat_name?></option>
												<?php } ?>
											</select>
										</div>
										<label class="col-sm-3 text-right control-label col-form-label">Supplier</label>
										<div class="col-sm-3">
											<select name="supplier" class="form-control selectpicker" <?php if($this->session->userdata('utype')==4){echo 'disabled';}?>>
												<option value="">Select Option</option>
												<?php foreach ($suppliers as $supplier) {?>
													<option value="<?php echo $supplier->sup_id ?>" <?php echo isset($filters['supcat_supplier']) ? ($filters['supcat_supplier'] == $supplier->sup_id ? 'selected' : '') : '' ?>><?php echo $supplier->sup_name?></option>
												<?php } ?>
											</select>
										</div>
									</div>
								</div>
								<div class="col-sm-5" style="padding:0px">
									<div class="row" style="padding:0px">
										<label class="col-sm-3 text-right control-label col-form-label">Cost Code</label>
										<div class="col-sm-3">
											<select name="cost_code" class="form-control selectpicker">
												<option value="">Select Option</option>
												<?php foreach ($ccode_list as $ccode) {?>
													<option value="<?php echo $ccode->cc_id ?>" <?php echo isset($filters['item_ccode_ms']) ? ($filters['item_ccode_ms'] == $ccode->cc_id ? 'selected' : '') : '' ?>><?php echo $ccode->cc_no.' - '.$ccode->cc_description?></option>
												<?php } ?>
											</select>
										</div>
										<label class="col-sm-3 text-right control-label col-form-label">Rentable</label>
										<div class="col-sm-3">
											<select name="rentable" class="form-control selectpicker">
												<option value="">Select Option</option>
												<option value="1" <?php echo isset($filters['supcat_is_rentable']) ? ($filters['supcat_is_rentable'] == "1" ? 'selected' : '') : '' ?>>Yes</option>
												<option value="0" <?php echo isset($filters['supcat_is_rentable']) ? ($filters['supcat_is_rentable'] == "0" ? 'selected' : '') : '' ?>>No</option>
											</select>
										</div>
									</div>
								</div>
								<div class="col-sm-2" style="padding:0px">
									<div class="row" style="padding:0px">
										<div class="col-sm-12" style="text-align:right">
											<button type="submit" class="btn btn-primary">Search</button>
											<button type="button" onclick="window.location.href='<?php echo base_url('admincontrol/sup_catalog/supplier_catalog_list'); ?>'" class="btn btn-success" >Reset</button>
										</div>
									</div>
								</div>
							</div>
						</form>

					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body">
						<?php if ($this->session->flashdata('success')) { ?>
							<div id="alert_msg"
								 class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
							<?php $this->session->unset_userdata('success');
						} elseif ($this->session->flashdata('e_error')) { ?>
							<div id="alert_msg"
								 class="alert alert-danger"><?php echo $this->session->flashdata('e_error'); ?></div>
							<?php $this->session->unset_userdata('e_error');
						} ?>
						<?php 
						if($this->session->userdata('utype')!=4){
						if($this->session->userdata('utype')==1 || $templateDetails->pt_i_supplierc<3){?>
							<a href="javascript:;" onclick="goto_add_record();" class="btn btn-primary float-right mb-2"
							style="margin-right: 10px;">Add New Supplier Catalog</a>
							<a href="javascript:;" onclick="goto_bulkupload_record();"
							class="btn btn-primary float-right mb-2" style="margin-right: 10px;">Bulk upload - Supplier
								Catalog</a>
							<?php }}else{?>
							<a href="javascript:;" onclick="goto_bulkupload_prices();"
							class="btn btn-primary float-right mb-2" style="margin-right: 10px;">Bulk update - Prices
							<?php }?>
						<a href="<?php base_url() ?>export_csv" class="btn btn-primary float-right mb-2" style="margin-right: 10px;">Export CSV</a>

						<div class="table-responsive">
							<table id="zero_config" class="table table-striped table-bordered">
								<thead>
								<tr style="font-weight: bold;">
									<th>Sl No.</th>
									<th>SKU Code</th>
									<th>Supplier</th>
									<th>Item Code</th>
									<th>Item Name</th>
									<th>Item Price</th>
									<th>Item Unit</th>
									<th>Expiry date</th>
									<th>Is Rental</th>
									<th>Status</th>
									<?php 
									if($this->session->userdata('utype')==1 || $this->session->userdata('utype')==4 || $templateDetails->pt_i_supplierc<3){?>
										<th>Action</th>
									<?php }?>
								</tr>
								</thead>
								<tbody>
								<?php foreach ($getrecord_list as $keys => $recorditem) { ?>
									<tr>
										<td><?php echo $keys + 1; ?></td>
										<td><?php echo $recorditem->supcat_sku_no; ?></td>
										<td><?php echo $recorditem->sup_name; ?></td>
										<td><?php echo $recorditem->supcat_item_code; ?></td>
										<td><?php echo $recorditem->item_name; ?></td>
										<td><?php if($recorditem->supcat_is_rentable==1){echo 'Daily : '.$recorditem->supcat_daily_price.'<br>Weekly : '.$recorditem->supcat_weekly_price.'<br>Monthly : '.$recorditem->supcat_monthly_price;}else{echo $recorditem->supcat_price;} ?></td>
										<td><?php echo $recorditem->uom_name; ?></td>
										<td><?php echo date('d-m-Y', strtotime($recorditem->supcat_lastdate)); ?></td>
										<td><?php echo $recorditem->supcat_is_rentable == 1 ? 'Yes' : 'No'; ?></td>
										<td><?php if ($recorditem->supcat_status == 1) { ?>
												<span style="color:green;">Active</span>
											<?php } elseif ($recorditem->supcat_status == 0) { ?>
												<span style="color:red;">InActive</span>
											<?php } ?></td>
										<?php 
										if($this->session->userdata('utype')==1 || $this->session->userdata('utype')==4 || $templateDetails->pt_i_supplierc<3){?>
											<td>
												<a class="btn btn-outline-warning"
												onclick="modify_record(<?php echo $recorditem->supcat_id; ?>);"
												href="javascript:;<?php //echo base_url().'admincontrol/items/edit_user/'.$recorditem->supcat_id;
												?>" title="Edit Record"><i class="fa fa-edit text-primary"></i></a>
												<?php if ($recorditem->supcat_status == 1) { ?>
													<a class="btn btn-outline-warning"
													href="<?php echo base_url() . 'admincontrol/sup_catalog/lock_sup_catlogset/' . $recorditem->supcat_id; ?>"
													title="Lock Record"><i class="fa fa-unlock text-dark"></i></a>
												<?php } else { ?>
													<a class="btn btn-outline-warning"
													href="<?php echo base_url() . 'admincontrol/sup_catalog/unlock_sup_catlogset/' . $recorditem->supcat_id; ?>"
													title="Unock Record"><i class="fa fa-lock text-dark"></i></a>
												<?php } ?>
												<?php 
												if($this->session->userdata('utype')==1 || $this->session->userdata('utype')==4 || $templateDetails->pt_i_supplierc<2){?>
													<a class="btn btn-outline-warning" onclick="return confirm('You are about to delete a record. This cannot be undone. Are you sure?');" href="<?php echo base_url().'admincontrol/sup_catalog/delete_itemset/'.$recorditem->supcat_id;
													?>" title="Delete Record"><i class="fa fa-trash text-danger"></i></a>
												<?php }?>

											</td>
										<?php }?>
									</tr>
								<?php } ?>
								</tbody>
							</table>
						</div>

					</div>
				</div>
			</div>
		</div>
		<!-- ============================================================== -->
		<!-- End PAge Content -->
		<!-- ============================================================== -->
		<!-- ============================================================== -->
	</div>
	<!-- ============================================================== -->
	<!-- End Container fluid  -->
	<!-- ============================================================== -->
	<!-- ============================================================== -->

	<!-- Modal -->
	<div class="modal fade" id="Modal_addrecord" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
		 aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Add New Supplier Catalog</h5>
					<button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="recipient-name" class="col-form-label">Supplier:</label><br/>
						<select class="form-control select22 reset-input" name="sc_supplier" id="sc_supplier" autocomplete="off"
								data-live-search="true">
							<option value="">---Select---</option>
							<?php foreach ($supp_list as $supitem) { ?>
								<option value="<?php echo $supitem->sup_id; ?>"><?php echo $supitem->sup_name; ?></option>
							<?php } ?>
						</select>
						<small class="invalid-feedback sc_supplier"><?php //echo form_error('sc_supplier'); ?></small>
					</div>
					<div class="form-group">
						<label for="recipient-name" class="col-form-label">SKU Code:</label>
						<input type="text" class="form-control reset-input" placeholder="Enter SKU Code" name="sc_sku_code"
							   id="sc_sku_code" autocomplete="off"/>
						<small class="invalid-feedback sc_sku_code"><?php //echo form_error('sc_sku_code'); ?></small>
					</div>
					<div class="form-group">
						<label for="rentable">
							<span class="tag">Rentable?</span>
							<input type="checkbox" class="area_type reset-input" id="rentable" name="rentable" value="1">
						</label>
						<small class="invalid-feedback rentable"><?php //echo form_error('itm_category'); ?></small>
					</div>
					<div class="form-group">
						<label for="recipient-name" class="col-form-label">Item:</label><br/>
						<select class="form-control select33 reset-input" name="sc_itm_code" id="sc_itm_code" autocomplete="off"
								data-live-search="true" onchange=goto_item_check('code');>
							<option value="">---Select---</option>
							<?php foreach ($item_list as $iiitem) { ?>
								<option value="<?php echo $iiitem->item_code; ?>"><?php echo $iiitem->item_name . ' - ' . $iiitem->item_code; ?></option>
							<?php } ?>
						</select>
						<small class="invalid-feedback sc_itm_code"><?php //echo form_error('sc_itm_code'); ?></small>
					</div>


					<!--<div class="form-group">
							<label for="message-text" class="col-form-label">Item Description:</label>
							<textarea class="form-control" placeholder="Enter Item Description" id="itm_desc" name="itm_desc" autocomplete="off"></textarea>
							<small class="invalid-feedback itm_desc"><?php //echo form_error('itm_desc'); ?></small>
						</div>-->

					<div class="form-group row" id="prices_div">
						<div class="col-sm-6">
							<label for="recipient-name" class="col-form-label">Daily Price:</label>
							<input type="text" class="form-control reset-input" placeholder="Enter Daily Price"
								   name="sc_daily_price" id="sc_daily_price" autocomplete="off"/>
							<small class="invalid-feedback sc_daily_price"><?php //echo form_error('sc_price'); ?></small>
						</div>

						<div class="col-sm-6">
							<label for="recipient-name" class="col-form-label">Weekly Price:</label><br/>
							<input type="text" class="form-control reset-input" placeholder="Enter Weekly Price"
								   name="sc_weekly_price" id="sc_weekly_price" autocomplete="off"/>
							<small class="invalid-feedback sc_weekly_price"><?php //echo form_error('sc_uom'); ?></small>
						</div>
					</div>

					<div class="form-group row">
						<div class="col-sm-6" id="price_monthly">
							<label for="recipient-name" class="col-form-label">Monthly Price:</label>
							<input type="text" class="form-control reset-input" placeholder="Enter Monthly Price"
								   name="sc_monthly_price" id="sc_monthly_price" autocomplete="off"/>
							<small class="invalid-feedback sc_monthly_price"><?php //echo form_error('sc_price'); ?></small>
						</div>
						<div class="col-sm-6" id="unit_price_div">
							<label for="recipient-name" class="col-form-label">Unit Price:</label>
							<input type="text" class="form-control reset-input" placeholder="Enter Daily Price"
								   name="sc_price" id="sc_price" autocomplete="off"/>
							<small class="invalid-feedback sc_price"><?php //echo form_error('sc_price'); ?></small>
						</div>
<!--						<div class="col-sm-6">-->
<!--							<label for="recipient-name" class="col-form-label">Unit of Measure:</label><br/>-->
<!--							<select class="form-control select22" name="sc_uom" id="sc_uom" autocomplete="off"-->
<!--									data-live-search="true">-->
<!--								<option value="">---Select---</option>-->
<!--								--><?php //foreach ($uom_list as $u_item) { ?>
<!--									<option value="--><?php //echo $u_item->uom_id; ?><!--">--><?php //echo $u_item->uom_name; ?><!--</option>-->
<!--								--><?php //} ?>
<!--							</select>-->
<!--							<small class="invalid-feedback sc_uom">--><?php ////echo form_error('sc_uom'); ?><!--</small>-->
<!--						</div>-->
					</div>

					<div class="form-group row">
						<div class="col-sm-6">
							<label for="item-category" class="col-form-label">Category :</label>
							<input type="text" class="form-control reset-input" placeholder="Category" name="info_add_item_category"
								id="info_add_item_category" autocomplete="off" readonly/>
						</div>
						<div class="col-sm-6">
							<label for="item-cc" class="col-form-label">Cost Code:</label>
							<input type="text" class="form-control reset-input" placeholder="Cost Code" name="info_add_item_cc"
								id="info_add_item_cc" autocomplete="off" readonly/>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-6">
							<label for="item-uom" class="col-form-label">UOM:</label>
							<input type="text" class="form-control reset-input" placeholder="Unit Of Measure" name="info_add_item_uom"
								id="info_add_item_uom" autocomplete="off" readonly/>
						</div>
					</div>

					<div class="form-group">
						<label for="recipient-name" class="col-form-label">Expiry Date:</label>
						<input type="text" class="form-control reset-input" placeholder="dd-mm-yyyy" name="sc_lastdate"
							   id="sc_lastdate" autocomplete="off"/>
						<small class="invalid-feedback sc_lastdate"><?php //echo form_error('sc_lastdate'); ?></small>
					</div>

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
	<div class="modal fade" id="Modal_editrecord" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2"
		 aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Update Supplier Catalog Details</h5>
					<button type="button" class="close close_modal2" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="recipient-name" class="col-form-label">Supplier:</label><br/>
						<input type="hidden" name="update_id_spcat" id="update_id_spcat" value="" autocomplete="off"/>
						<select class="form-control select22" name="update_sc_supplier" id="update_sc_supplier"
								autocomplete="off" data-live-search="true" <?php if($this->session->userdata('utype')==4){echo 'disabled';}?>>
							<option value="">---Select---</option>
							<?php foreach ($supp_list as $supitem) { ?>
								<option value="<?php echo $supitem->sup_id; ?>"><?php echo $supitem->sup_name; ?></option>
							<?php } ?>
						</select>
						<small class="invalid-feedback update_sc_supplier"><?php //echo form_error('update_sc_supplier'); ?></small>
					</div>
					<div class="form-group">
						<label for="recipient-name" class="col-form-label">SKU Code:</label>
						<input type="text" class="form-control" placeholder="Enter SKU Code" name="update_sc_sku_code"
							   id="update_sc_sku_code" autocomplete="off"/>
						<small class="invalid-feedback update_sc_sku_code"><?php //echo form_error('update_sc_sku_code'); ?></small>
					</div>
					<div class="form-group">
						<label for="rentable">
							<span class="tag">Rentable?</span>
							<input type="checkbox" class="area_type" id="update_rentable" name="update_rentable" value="1" <?php if($this->session->userdata('utype')==4){echo 'disabled';}?>>
						</label>
						<small class="invalid-feedback rentable"><?php //echo form_error('itm_category'); ?></small>
					</div>
					<div class="form-group">
						<label for="recipient-name" class="col-form-label">Item:</label><br/>
						<select class="form-control select33" name="update_sc_itm_code" id="update_sc_itm_code"
								autocomplete="off" data-live-search="true" onchange="goto_item_check_v2('code');" <?php if($this->session->userdata('utype')==4){echo 'disabled';}?>>
							<option value="">---Select---</option>
							<?php foreach ($item_list as $iiitem) { ?>
								<option value="<?php echo $iiitem->item_code; ?>"><?php echo $iiitem->item_name . ' - ' . $iiitem->item_code; ?></option>
							<?php } ?>
						</select>
						<small class="invalid-feedback update_sc_itm_code"><?php //echo form_error('update_sc_itm_code'); ?></small>
					</div>

					<!--<div class="form-group">
							<label for="message-text" class="col-form-label">Item Description:</label>
							<textarea class="form-control" placeholder="Enter Item Description" id="itm_desc" name="itm_desc" autocomplete="off"></textarea>
							<small class="invalid-feedback itm_desc"><?php //echo form_error('itm_desc'); ?></small>
						</div>-->
					<div class="form-group row" id="update_prices_div">
						<div class="col-sm-6">
							<label for="recipient-name" class="col-form-label">Daily Price:</label>
							<input type="text" class="form-control" placeholder="Enter Price" name="update_sc_daily_price"
								   id="update_sc_daily_price" autocomplete="off"/>
							<small class="invalid-feedback update_sc_daily_price"><?php //echo form_error('update_sc_daily_price'); ?></small>
						</div>

						<div class="col-sm-6">
							<label for="recipient-name" class="col-form-label">Weekly Price:</label><br/>
							<input type="text" class="form-control" placeholder="Enter Weekly Price"
								   name="update_sc_weekly_price" id="update_sc_weekly_price" autocomplete="off"/>
							<small class="invalid-feedback update_sc_weekly_price"><?php //echo form_error('sc_uom'); ?></small>
						</div>

					</div>
					<div class="form-group row">
						<div class="col-sm-6" id="update_price_monthly">
							<label for="recipient-name" class="col-form-label">Monthly Price:</label>
							<input type="text" class="form-control" placeholder="Enter Monthly Price"
								   name="update_sc_monthly_price" id="update_sc_monthly_price" autocomplete="off"/>
							<small class="invalid-feedback update_sc_monthly_price"><?php //echo form_error('sc_price'); ?></small>
						</div>
						<div class="col-sm-6" id="update_unit_price_div">
							<label for="recipient-name" class="col-form-label">Unit Price:</label>
							<input type="text" class="form-control" placeholder="Enter Unit Price"
								   name="update_sc_price" id="update_sc_price" autocomplete="off"/>
							<small class="invalid-feedback update_sc_price"><?php //echo form_error('sc_price'); ?></small>
						</div>
					 </div>

					<div class="form-group row">
						<div class="col-sm-6">
							<label for="item-category" class="col-form-label">Category :</label>
							<input type="text" class="form-control" placeholder="Category" name="info_item_category"
								id="info_item_category" autocomplete="off" readonly/>
						</div>
						<div class="col-sm-6">
							<label for="item-cc" class="col-form-label">Cost Code:</label>
							<input type="text" class="form-control" placeholder="Cost Code" name="info_item_cc"
								id="info_item_cc" autocomplete="off" readonly/>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-6">
							<label for="item-uom" class="col-form-label">UOM:</label>
							<input type="text" class="form-control" placeholder="Unit Of Measure" name="info_item_uom"
								id="info_item_uom" autocomplete="off" readonly/>
						</div>
					</div>

				<div class="form-group">
					<label for="recipient-name" class="col-form-label">Expiry Date:</label>
					<input type="text" class="form-control" placeholder="dd-mm-yyyy" name="update_sc_lastdate"
						   id="update_sc_lastdate" autocomplete="off"/>
					<small class="invalid-feedback update_sc_lastdate"><?php //echo form_error('update_sc_lastdate'); ?></small>
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
					<button type="button" id="update_record_btn" class="btn btn-primary"
							onclick="goto_update_record();">Update
					</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal -->
	<div class="modal fade" id="Modal_bulkupload_record" tabindex="-1" role="dialog"
		 aria-labelledby="exampleModalLabel3" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Bulk upload - Supplier Catalog</h5>
					<button type="button" class="close close_modal3" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="recipient-name" class="col-form-label">Upload CSV :</label>
						<input type="hidden" name="update_cat" id="update_cat" value="BULK"/>
						<input type="file" class="form-control" name="upload_item_cat" id="upload_item_cat"
							   autocomplete="off"/>
						<small class="invalid-feedback upload_item_cat"></small>
					</div>
					<div class="col-sm-12 text-center">
						<div align="center">
							<div class="get_error_total3" align="center"
								 style="background-color: #bf0000;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
							<div class="get_success_total3" align="center"
								 style="background-color: #174b10;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
							<div class="div_roller_total3" align="center" style="display: none;"><img
										src="<?php echo base_url(); ?>style/images/ajax_loader.gif"
										style="max-width: 60px;"/></div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>-->
					<button type="button" id="upload_bulk_btn" class="btn btn-primary"
							onclick="goto_upload_setof_record();">Upload
					</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal -->
	<div class="modal fade" id="Modal_bulkupload_prices" tabindex="-1" role="dialog"
		 aria-labelledby="exampleModalLabel3" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Bulk update - Prices</h5>
					<button type="button" class="close close_modal3" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="recipient-name" class="col-form-label">Upload CSV :</label>
						<input type="hidden" name="update_cat" id="update_cat" value="BULK"/>
						<input type="file" class="form-control" name="upload_item_prices" id="upload_item_prices"
							   autocomplete="off"/>
						<small class="invalid-feedback upload_item_prices"></small>
					</div>
					<div class="col-sm-12 text-center">
						<div align="center">
							<div class="get_error_total3" align="center"
								 style="background-color: #bf0000;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
							<div class="get_success_total3" align="center"
								 style="background-color: #174b10;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
							<div class="div_roller_total3" align="center" style="display: none;"><img
										src="<?php echo base_url(); ?>style/images/ajax_loader.gif"
										style="max-width: 60px;"/></div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>-->
					<button type="button" id="upload_bulk_btn_prices" class="btn btn-primary"
							onclick="goto_upload_setof_prices();">Upload
					</button>
				</div>
			</div>
		</div>
	</div>


	<?php $this->load->view('admin/component/footer') ?>


	<script type="text/javascript">

		var itemCheck='';
		function goto_add_record() {
			$('.reset-input').val('').change();
			$('#rentable').prop('checked',false);
			if ($('#rentable').prop("checked") == true) {
				$('#price_monthly').show();
				$('#prices_div').show();
				$('#unit_price_div').hide();
			} else if ($('#rentable').prop("checked") == false) {
				$('#price_monthly').hide();
				$('#prices_div').hide();
				$('#unit_price_div').show();
			}
			$('#Modal_addrecord').modal('show');
		}
		
		$(function () {
			$('#alert_msg').delay(6000).fadeOut();
			$('.select22, .select33').selectpicker();
			$('.alert-error, .invalid-feedback').delay(6000).fadeOut();
			$('#update_sc_lastdate, #sc_lastdate').datepicker({
				autoclose: true,
				todayHighlight: true,
				format: 'dd-mm-yyyy'
			});
			if ($("#rentable").is(":checked") == true) {
				$('#price_monthly').show();
				$('#unit_price_div').hide();
				$('#prices_div').show();
			} else if ($("#rentable").is(":checked") == false) {
				$('#price_monthly').hide();
				$('#prices_div').hide();
				$('#unit_price_div').show();
			}

			if ($("#update_rentable").is(":checked") == true) {
				$('#update_price_monthly').show();
				$('#update_unit_price_div').hide();
				$('#update_prices_div').show();
			} else if ($("#rentable").is(":checked") == false) {
				$('#update_price_monthly').hide();
				$('#update_prices_div').hide();
				$('#update_unit_price_div').show();
			}

		});

		
		/****************************************
		 *       Basic Table                   *
		 ****************************************/
		$('#zero_config').DataTable();

		function goto_item_check_v2(recordid) {
			if (recordid != "") {
				if (recordid == "code") {
					var update_sc_itm_code = $('#update_sc_itm_code option:selected').val();
					if (update_sc_itm_code != "") {
						$('#update_sc_itm_name').val(update_sc_itm_code);
					}
				}
			}
		}

		function goto_item_check(recordid) {
			if (recordid != "") {
				if (recordid == "code") {
					var sc_itm_code = $('#sc_itm_code option:selected').val();
					if (sc_itm_code != "") {
						$('#sc_itm_name').val(sc_itm_code);
						$('#sc_itm_name').selectpicker('refresh');
					}
				} else if (recordid == "name") {
					var sc_itm_name = $('#sc_itm_name option:selected').val();
					if (sc_itm_name != "") {
						$('#sc_itm_code').val(sc_itm_name);
						$('#sc_itm_code').selectpicker('refresh');
					}
				}
			}
		}

		
		function goto_submit_record() {
			$('.div_roller_total').fadeIn();
			$('.close_modal').hide();
			$('#submit_record_btn').prop('disabled', true);

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

			var sc_sku_code = $('#sc_sku_code').val();
			var sc_supplier = $('#sc_supplier option:selected').val();
			var sc_itm_code = $('#sc_itm_code option:selected').val();
			var sc_daily_price = $('#sc_daily_price').val();
			var sc_price = $('#sc_price').val();
			var sc_weekly_price = $('#sc_weekly_price').val();
			var sc_monthly_price = $('#sc_monthly_price').val();
			var sc_uom = $('#sc_uom option:selected').val();
			var sc_lastdate = $('#sc_lastdate').val();

			if (sc_sku_code == "") {
				e_error = 1;
				$('.sc_sku_code').html('SKU Code is Required.');
			} else {
				if (!sc_sku_code.match(alphanumerics_spaces)) {
					e_error = 1;
					$('.sc_sku_code').html('SKU Code not use special carecters [without _ . , -], Check again.');
				} else {
					$('.sc_sku_code').html('');
				}
			}
			if (sc_supplier == "") {
				e_error = 1;
				$('.sc_supplier').html('Supplier Name is Required.');
			} else {
				if (!sc_supplier.match(onlynumerics)) {
					e_error = 1;
					$('.sc_supplier').html('Supplier Name needs only Numeric Value.');
				} else {
					$('.sc_supplier').html('');
				}
			}
			if (sc_itm_code == "") {
				e_error = 1;
				$('.sc_itm_code').html('Item Code is Required.');
			} else {
				$('.sc_itm_code').html('');
			}


			// if (sc_uom == "") {
			// 	e_error = 1;
			// 	$('.sc_uom').html('Unit of Measure is Required.');
			// } else {
			// 	if (!sc_uom.match(onlynumerics)) {
			// 		e_error = 1;
			// 		$('.sc_uom').html('Unit of Measure needs only Numeric Value.');
			// 	} else {
			// 		$('.sc_uom').html('');
			// 	}
			// }
			if (sc_lastdate == "") {
				e_error = 1;
				$('.sc_lastdate').html('Last Date is Required.');
			} else {
				$('.sc_uom').html('');
			}

			if (e_error == 1) {
				$('.div_roller_total').fadeOut();
				$('#submit_record_btn').prop('disabled', false);
				$('.close_modal').show();
				//$('.get_error_total').html(error_message);
				//$(".get_error_total").fadeIn();
				toastr.error(error_message, 'Error!');
				$(".invalid-feedback").fadeIn();
				/*e_error = 0;
				error_message = '';*/
				setTimeout(function () {
					$('.invalid-feedback, .get_error_total').fadeOut();
				}, delay);
			} else {

				var form_data = new FormData();
				form_data.append("sc_sku_code", sc_sku_code);
				if($('#rentable').is(":checked")){
					form_data.append("sc_is_rentable", 1);
				}else{
					form_data.append("sc_is_rentable", 0);
				}
				form_data.append("sc_supplier", sc_supplier);
				form_data.append("sc_itm_code", sc_itm_code);
				form_data.append("sc_price", sc_price);
				form_data.append("sc_daily_price", sc_daily_price);
				form_data.append("sc_weekly_price", sc_weekly_price);
				form_data.append("sc_monthly_price", sc_monthly_price);
				form_data.append("sc_uom", sc_uom);
				form_data.append("sc_lastdate", sc_lastdate);

				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/sup_catalog/add_new_supplier_catalog_sets') ?>",
					dataType: 'json',
					data: form_data,
					contentType: false,
					cache: true,
					processData: false,
					success: function (data) {
						//alert(data.msg);
						if (data.msg == 1) {
							//console.log(data);
							//alert(data.msg[0].space_rate);
							$('.div_roller_total').fadeOut();
							toastr.success('Record is Inserted Successfully!', 'Success');
							setTimeout(function () {
								window.location.replace("<?php echo site_url('admincontrol/sup_catalog/supplier_catalog_list') ?>");
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

					}
				});
			}

		}

		
		$('#rentable').on('change', function () {
			var is_rentable = 0;

			if ($(this).prop("checked") == true) {
				is_rentable = 1
				$('#price_monthly').show();
				$('#prices_div').show();
				$('#unit_price_div').hide();
			} else if ($(this).prop("checked") == false) {
				is_rentable = 0;
				$('#price_monthly').hide();
				$('#prices_div').hide();
				$('#unit_price_div').show();
			}

			var form_data = new FormData();
			form_data.append("is_rentable", is_rentable);
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/sup_catalog/get_rentable_items') ?>",
				dataType: 'json',
				data: form_data,
				contentType: false,
				cache: true,
				processData: false,
				success: function (data) {
					//alert(data.msg);
					if (data.msg == 1) {
						$("#sc_itm_code").empty().trigger('change');
						var newOption = new Option('Select an option', '', false, false);
						$('#sc_itm_code').append(newOption).trigger('change');

						for (var index = 0; index < data.e_msg.length; index++) {
							var newOption = new Option('' + data.e_msg[index].item_code + ' - ' + data.e_msg[index].item_name + '', data.e_msg[index].item_code, false, false);
							$('#sc_itm_code').append(newOption).trigger('change');
						}

						$("#sc_itm_code").trigger('change')
						$("#sc_itm_code").selectpicker('refresh')

					}
				}
			});

		});

		$('#update_rentable').on('change', function () {
			var is_rentable = 0;

			if ($(this).prop("checked") == true) {
				is_rentable = 1
				$('#update_price_monthly').show();
				$('#update_prices_div').show();
				$('#update_unit_price_div').hide();
			} else if ($(this).prop("checked") == false) {
				is_rentable = 0;
				$('#update_price_monthly').hide();
				$('#update_prices_div').hide();
				$('#update_unit_price_div').show();
			}

			var itemVal = itemCheck;
			// alert('itemVal='+itemVal);

			var form_data = new FormData();
			form_data.append("is_rentable", is_rentable);
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/sup_catalog/get_rentable_items') ?>",
				dataType: 'json',
				data: form_data,
				contentType: false,
				cache: true,
				processData: false,
				success: function (data) {
					//alert(data.msg);
					if (data.msg == 1) {
						$("#update_sc_itm_code").empty().trigger('change');
						var newOption = new Option('Select an option', '', false, false);
						$('#update_sc_itm_code').append(newOption).trigger('change');

						for (var index = 0; index < data.e_msg.length; index++) {
							// if(data.e_msg[index].item_code == itemVal){
							// 	var newOption = new Option('' + data.e_msg[index].item_code + ' - ' + data.e_msg[index].item_name + '', data.e_msg[index].item_code, false, selected);
							// }else{
								var newOption = new Option('' + data.e_msg[index].item_code + ' - ' + data.e_msg[index].item_name + '', data.e_msg[index].item_code, false, false);
							// }
							$('#update_sc_itm_code').append(newOption).trigger('change');
						}
						if(itemVal!=''){
							$("#update_sc_itm_code").val(itemVal);
							$("#update_sc_itm_code").selectpicker('refresh');
						}	
						$("#update_sc_itm_code").trigger('change');
						$("#update_sc_itm_code").selectpicker('refresh');
					}
				}
			});

		});
		
		function modify_record(element) {
			if (element != "") {
							// $("#update_sc_itm_code").empty(); 
				var form_data = new FormData();
				form_data.append("name_scid", element);
				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/sup_catalog/get_details_of_sup_catlog_sets') ?>",
					dataType: 'json',
					data: form_data,
					contentType: false,
					cache: true,
					processData: false,
					success: function (data) {
		// 				//alert(data.msg);
						if (data.msg == 1) {
							// alert('in');
		// 					//console.log(data);
		// 					//alert(data.msg[0].space_rate);
							// alert(JSON.stringify(data.s_msg));
							// alert(data.s_msg.supcat_item_code);
							itemCheck=data.s_msg.supcat_item_code;
							if(data.s_msg.supcat_is_rentable == "1") {
								$('#update_rentable').prop('checked',true);
								$('#update_rentable').trigger('change');
								$('#update_sc_daily_price').val(data.s_msg.supcat_daily_price);
								$('#update_sc_weekly_price').val(data.s_msg.supcat_weekly_price);
								$('#update_sc_monthly_price').val(data.s_msg.supcat_monthly_price);
							} else {
								$('#update_rentable').prop('checked',false);
								$("#update_sc_itm_code").val(data.s_msg.supcat_item_code);
								$('#update_rentable').trigger('change');
								$('#update_sc_price').val(data.s_msg.supcat_price);
							}

							$('#update_id_spcat').val(element);
							$('#info_item_category').val(data.s_msg.icat_name);
							$('#info_item_cc').val(data.s_msg.cc_no+' - '+data.s_msg.cc_description);
							$('#info_item_uom').val(data.s_msg.uom_name);
							$('#update_sc_sku_code').val(data.s_msg.supcat_sku_no);
							var ndate = data.s_msg.supcat_lastdate.split("-").reverse().join("-");
							$('#update_sc_lastdate').datepicker('setDate', ndate);

							var s_sup = '<option value="">---Select---</option>';
							var s_item = '<option value="">---Select---</option>';
							var s_code = '<option value="">---Select---</option>';
							var s_uom = '<option value="">---Select---</option>';
							var sup_setlock = '';
							var item_setlock = '';
							var code_setlock = '';
							var uom_setlock = '';

							<?php foreach($supp_list as $supitem){ ?>
							var supp1 = "<?php echo $supitem->sup_id; ?>";
							var supname1 = "<?php echo $supitem->sup_name; ?>";
							if (parseInt(supp1) == parseInt(data.s_msg.supcat_supplier)) {
								sup_setlock = parseInt(supp1);
								s_sup = s_sup + '<option value="' + supp1 + '" selected="selected">' + supname1 + '</option>';
							} else {
								s_sup = s_sup + '<option value="' + supp1 + '">' + supname1 + '</option>';
							}
							<?php } ?>
							$('#update_sc_supplier').html(s_sup);


							<?php foreach($uom_list as $u_item){ ?>
							var uuom3 = "<?php echo $u_item->uom_id; ?>";
							var uuomname3 = "<?php echo $u_item->uom_name; ?>";
							if (parseInt(uuom3) == parseInt(data.s_msg.supcat_uom)) {
								uom_setlock = parseInt(uuom3);
								s_uom = s_uom + '<option value="' + uuom3 + '" selected="selected">' + uuomname3 + '</option>';
							} else {
								s_uom = s_uom + '<option value="' + uuom3 + '">' + uuomname3 + '</option>';
							}
							<?php } ?>


							$('.select22, .select33').selectpicker('refresh');
							// $('.select22').selectpicker('refresh');
							// alert(data.s_msg.supcat_item_code);
							$('#update_sc_supplier').val(sup_setlock);
							// $('.select33 > .dropdown-toggle > .filter-option > .filter-option-inner > .filter-option-inner-inner').html(data.s_msg.supcat_item_code);
							// $("#update_sc_itm_code").val(data.s_msg.supcat_item_code);

							$('.select22, .select33').selectpicker('refresh');
							// $('.select22').selectpicker('refresh');
							$('#Modal_editrecord').modal('show');
							$("#update_sc_itm_code").val('');


						} else {
							alert('out');
							$('#update_id_spcat').val('');
							$('#Modal_editrecord').modal('hide');
						}

					}
				});
			} else {
				$('#update_id_spcat').val('');
				$('#Modal_editrecord').modal('hide');
			}
		}

		
		function goto_update_record() {
			$('.div_roller_total2').fadeIn();
			$('.close_modal2').hide();
			$('#update_record_btn').prop('disabled', true);

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

			var update_id_spcat = $('#update_id_spcat').val();
			var update_sc_supplier = $('#update_sc_supplier option:selected').val();
			var update_sc_sku_code = $('#update_sc_sku_code').val();
			var update_sc_itm_code = $('#update_sc_itm_code option:selected').val();
			var update_sc_daily_price = $('#update_sc_daily_price').val();
			var update_sc_weekly_price = $('#update_sc_weekly_price').val();
			var update_sc_monthly_price = $('#update_sc_monthly_price').val();
			var update_sc_lastdate = $('#update_sc_lastdate').val();
			var update_sc_price = $('#update_sc_price').val();

			if($("#update_rentable").is(':checked')) {
				var update_rentable = 1;
			} else
			{
				var update_rentable = 0;
			}

			if (update_id_spcat == "") {
				e_error = 1;
				error_message = error_message + "<br/>ID not Found, Refresh the Page and Try again.";
			}
			if (update_sc_sku_code == "") {
				e_error = 1;
				$('.update_sc_sku_code').html('SKU Code is Required.');
			} else {
				if (!update_sc_sku_code.match(alphanumerics_spaces)) {
					e_error = 1;
					$('.update_sc_sku_code').html('SKU Code not use special carecters [without _ . , -], Check again.');
				} else {
					$('.update_sc_sku_code').html('');
				}
			}
			if (update_sc_supplier == "") {
				e_error = 1;
				$('.update_sc_supplier').html('Supplier Name is Required.');
			} else {
				if (!update_sc_supplier.match(onlynumerics)) {
					e_error = 1;
					$('.update_sc_supplier').html('Supplier Name needs only Numeric Value.');
				} else {
					$('.update_sc_supplier').html('');
				}
			}
			if (update_sc_itm_code == "") {
				e_error = 1;
				$('.update_sc_itm_code').html('Item Code is Required.');
			} else {
				$('.update_sc_itm_code').html('');
			}

			if (update_sc_lastdate == "") {
				e_error = 1;
				$('.update_sc_lastdate').html('Last Date is Required.');
			} else {
				$('.sc_uom').html('');
			}

			if (e_error == 1) {
				$('.div_roller_total2').fadeOut();
				$('#update_record_btn').prop('disabled', false);
				$('.close_modal2').show();
				//$('.get_error_total').html(error_message);
				//$(".get_error_total").fadeIn();'');
				//			}
				//
				//			if (
				toastr.error(error_message, 'Error!');
				$(".invalid-feedback").fadeIn();
				/*e_error = 0;
				error_message = '';*/
				setTimeout(function () {
					$('.invalid-feedback, .get_error_total2').fadeOut();
				}, delay);
			} else {

				var form_data = new FormData();
				form_data.append("update_id_spcat", update_id_spcat);
				if($('#update_rentable').is(":checked")){
					form_data.append("update_sc_is_rentable", 1);
				}else{
					form_data.append("update_sc_is_rentable", 0);
				}
				form_data.append("update_sc_sku_code", update_sc_sku_code);
				form_data.append("update_sc_supplier", update_sc_supplier);
				form_data.append("update_sc_itm_code", update_sc_itm_code);
				form_data.append("update_sc_daily_price", update_sc_daily_price);
				form_data.append("update_sc_weekly_price", update_sc_weekly_price);
				form_data.append("update_sc_monthly_price", update_sc_monthly_price);
				form_data.append("update_sc_lastdate", update_sc_lastdate);
				form_data.append("update_rentable", update_rentable);
				form_data.append("update_sc_price", update_sc_price);

				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/sup_catalog/modify_sup_catlog_sets') ?>",
					dataType: 'json',
					data: form_data,
					contentType: false,
					cache: true,
					processData: false,
					success: function (data) {
						//alert(data.msg);
						if (data.msg == 1) {
							//console.log(data);
							//alert(data.msg[0].space_rate);
							$('.div_roller_total2').fadeOut();
							toastr.success('Record is Updated Successfully!', 'Success');
							setTimeout(function () {
								window.location.replace("<?php echo site_url('admincontrol/sup_catalog/supplier_catalog_list') ?>");
							}, 2000);

						} else {
							$('.div_roller_total2').fadeOut();
							$('#update_record_btn').prop('disabled', false);
							$('.close_modal2').show();
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

		
		function goto_bulkupload_record() {
			$('#Modal_bulkupload_record').modal('show');
		}

		function goto_bulkupload_prices() {
			$('#Modal_bulkupload_prices').modal('show');
		}

		
		function goto_upload_setof_record() {
			$('.div_roller_total3').fadeIn();
			$('.close_modal3').hide();
			$('#upload_bulk_btn').prop('disabled', true);

			var delay = 8000;
			var e_error = 0;
			var error_message = 'There have some errors plese check above, Try again.';
			var alphaletters_spaces = /^[A-Za-z ]+$/;
			var alphaletters = /^[A-Za-z]+$/;
			var alphanumerics = /^[A-Za-z0-9/() ]+$/;
			var alphanumerics_spaces = /^[A-Za-z0-9_.,\- ]+$/;
			var alphanumerics_no = /^[A-Za-z0-9_/&(@):.,\- ]+$/;
			var onlynumerics = /^[0-9]+$/;
			var specials_char = /[~`!#$%\^&*+=\[\]\\';./{}()|\\":<>\?]/g;
			var emailpattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;
			var allowedExtensions = /(\.csv|)$/i;

			var update_cat = $('#update_cat').val();
			var files = $('#upload_item_cat')[0].files;

			if (document.getElementById("upload_item_cat").files.length == 0) {
				e_error = 1;
				$('.upload_item_cat').html('Upload File is Required.');
			} else {
				var fileInput = document.getElementById('upload_item_cat');
				var filePath = fileInput.value;
				if (!allowedExtensions.exec(filePath)) {
					e_error = 1;
					$('.upload_item_cat').html('Upload File type Invalid.(Use Excel File Only)');
				} else {
					$('.upload_item_cat').html('');
				}
			}

			if (e_error == 1) {
				$('.div_roller_total3').fadeOut();
				$('#upload_bulk_btn').prop('disabled', false);
				$('.close_modal3').show();
				//$('.get_error_total').html(error_message);
				//$(".get_error_total").fadeIn();
				toastr.error(error_message, 'Error!');
				$(".invalid-feedback").fadeIn();
				/*e_error = 0;
				error_message = '';*/
				setTimeout(function () {
					$('.invalid-feedback, .get_error_total3').fadeOut();
				}, delay);
			} else {

				var form_data = new FormData();
				form_data.append("update_cat", update_cat);
				form_data.append("files", files[0]);

				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/sup_catalog/bulkitem_upload_section_sets') ?>",
					dataType: 'json',
					data: form_data,
					contentType: false,
					cache: true,
					processData: false,
					success: function (data) {
						//alert(data.msg);
						if (data.msg == 1) {
							//console.log(data);
							//alert(data.msg[0].space_rate);
							// alert(JSON.stringify(data.s_msg));
							$('.div_roller_total3').fadeOut();
							toastr.success('Record is Uploaded Successfully!', 'Success');
							setTimeout(function () {
								window.location.replace("<?php echo site_url('admincontrol/sup_catalog/supplier_catalog_list') ?>");
							}, 2000);

						} else {
							$('.div_roller_total3').fadeOut();
							$('#upload_bulk_btn').prop('disabled', false);
							$('.close_modal3').show();
							error_message = data.e_msg;
							toastr.error(error_message, 'Error!');
							$('#Modal_bulkupload_record').modal('hide');
							setTimeout(function () {
								window.location.replace("<?php echo site_url('admincontrol/sup_catalog/supplier_catalog_list') ?>");
							}, 2000);
							//$('.get_error_total').html(error_message);
							//$(".get_error_total").fadeIn();
							//setTimeout(function(){ $('.get_error_total').fadeOut(); }, delay);
						}

					}
				});
			}
		}

		function goto_upload_setof_prices() {
			$('.div_roller_total3').fadeIn();
			$('.close_modal3').hide();
			$('#upload_bulk_btn').prop('disabled', true);

			var delay = 8000;
			var e_error = 0;
			var error_message = 'There have some errors plese check above, Try again.';
			var alphaletters_spaces = /^[A-Za-z ]+$/;
			var alphaletters = /^[A-Za-z]+$/;
			var alphanumerics = /^[A-Za-z0-9/() ]+$/;
			var alphanumerics_spaces = /^[A-Za-z0-9_.,\- ]+$/;
			var alphanumerics_no = /^[A-Za-z0-9_/&(@):.,\- ]+$/;
			var onlynumerics = /^[0-9]+$/;
			var specials_char = /[~`!#$%\^&*+=\[\]\\';./{}()|\\":<>\?]/g;
			var emailpattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;
			var allowedExtensions = /(\.csv|)$/i;

			var update_cat = $('#update_cat').val();
			var files = $('#upload_item_prices')[0].files;

			if (document.getElementById("upload_item_prices").files.length == 0) {
				e_error = 1;
				$('.upload_item_prices').html('Upload File is Required.');
			} else {
				var fileInput = document.getElementById('upload_item_prices');
				var filePath = fileInput.value;
				if (!allowedExtensions.exec(filePath)) {
					e_error = 1;
					$('.upload_item_prices').html('Upload File type Invalid.(Use Excel File Only)');
				} else {
					$('.upload_item_prices').html('');
				}
			}

			if (e_error == 1) {
				$('.div_roller_total3').fadeOut();
				$('#upload_bulk_btn').prop('disabled', false);
				$('.close_modal3').show();
				//$('.get_error_total').html(error_message);
				//$(".get_error_total").fadeIn();
				toastr.error(error_message, 'Error!');
				$(".invalid-feedback").fadeIn();
				/*e_error = 0;
				error_message = '';*/
				setTimeout(function () {
					$('.invalid-feedback, .get_error_total3').fadeOut();
				}, delay);
			} else {

				var form_data = new FormData();
				form_data.append("update_cat", update_cat);
				form_data.append("files", files[0]);

				// alert(JSON.stringify(form_data));

				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/sup_catalog/bulkitem_upload_section_sets_prices') ?>",
					dataType: 'json',
					data: form_data,
					contentType: false,
					cache: true,
					processData: false,
					success: function (data) {
						// alert(data.msg);
						if (data.msg == 1) {
							//console.log(data);
							// alert(JSON.stringify(data.s_msg));
							$('.div_roller_total3').fadeOut();
							toastr.success('Record is Uploaded Successfully!', 'Success');
							setTimeout(function () {
								window.location.replace("<?php echo site_url('admincontrol/sup_catalog/supplier_catalog_list') ?>");
							}, 2000);

						} else {
							$('.div_roller_total3').fadeOut();
							$('#upload_bulk_btn').prop('disabled', false);
							$('.close_modal3').show();
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

		$('#sc_itm_code').on('change',function(){
			if($(this).val()!=''){
				var itemVal=$(this).val();
				var form_data = new FormData();
				form_data.append("item_value", itemVal);
				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/sup_catalog/get_info_details') ?>",
					dataType: 'json',
					data: form_data,
					contentType: false,
					cache: true,
					processData: false,
					success: function (data) {
						// alert(data.msg);
						if (data.msg == 1) {
							// alert(JSON.stringify(data.s_msg));
							$('#info_add_item_category').val(data.s_msg.icat_name);
							$('#info_add_item_cc').val(data.s_msg.cc_no+' - '+data.s_msg.cc_description);
							$('#info_add_item_uom').val(data.s_msg.uom_name);
						} else {
						}

					},
					error : function (request, error){
						alert(request.msg);
					}
				});
			}
		});



	</script>
        
