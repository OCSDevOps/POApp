<?php $this->load->view('admin/component/header') ?>


<?php $this->load->view('admin/component/menu') ?>

<style>

/* To chnage model width */
.modal-dialog {
	max-width: 80%!important;
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
				<h4 class="page-title">Maintenance List</h4>
				<div class="ml-auto text-right">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a
										href="<?php echo base_url('admincontrol/dashboard'); ?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Maintenance List</li>
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
						if($this->session->userdata('utype')==1 || $templateDetails->pt_e_eqm<3){?>
							<a href="javascript:;" onclick="goto_add_record();" class="btn btn-primary float-right mb-2"
							style="margin-right: 10px;">Add New Miantenance</a>
						<?php }?>
						<div class="table-responsive">
							<table id="zero_config" class="table table-striped table-bordered">
								<thead>
								<tr style="font-weight: bold;">
									<th>Sl No.</th>
									<th>Asset Name</th>
									<th>Vendor</th>
									<th>Date</th>
									<th>Type</th>
									<th>Notes</th>
									<th>Attachment</th>
									<th>Total</th>
									<?php 
									if($this->session->userdata('utype')==1 || $templateDetails->pt_e_eqm<3){?>
										<th>Action</th>
									<?php }?>
								</tr>
								</thead>
								<tbody>
								<?php foreach ($getrecord_list as $keys => $recorditem) { ?>
									<tr>
										<td><?php echo $keys + 1; ?></td>
										<td><?php echo $recorditem->eqm_asset_name; ?></td>
										<td><?php echo $recorditem->sup_name; ?></td>
										<td><?php echo $recorditem->service_date; ?></td>
										<td><?php echo $recorditem->service_type; ?></td>
										<td><?php echo $recorditem->maintenance_notes; ?></td>
										<td><img class="img-responsive" style="width:200px" src="<?=base_url('/upload_file/maintenance/'.$recorditem->attachment)?>"></td>
										<td><?php echo $recorditem->maintenance_total; ?></td>
										</td>
										<?php 
										if($this->session->userdata('utype')==1 || $templateDetails->pt_e_eqm<3){?>
											<td>
												<a class="btn btn-outline-warning"
												onclick="modify_record(<?php echo $recorditem->eqm_id; ?>);"
												href="javascript:;" title="Edit Record"><i
															class="fa fa-edit text-primary"></i></a>
												<?php if ($recorditem->status == 1) { ?>
													<!-- <a class="btn btn-outline-warning"
													href="<?php echo base_url() . 'admincontrol/projects/lock_project_set/' . $recorditem->proj_id; ?>"
													title="Lock Record"><i class="fa fa-unlock text-dark"></i></a> -->
												<?php } else { ?>
													<!-- <a class="btn btn-outline-warning"
													href="<?php echo base_url() . 'admincontrol/projects/unlock_project_set/' . $recorditem->proj_id; ?>"
													title="Unock Record"><i class="fa fa-lock text-dark"></i></a> -->
												<?php } ?>
												<?php 
												if($this->session->userdata('utype')==1 || $templateDetails->pt_e_eqm<2){?>
													<a onclick="return confirm('You are about to delete a record. This cannot be undone. Are you sure?');" href="<?php echo base_url().'admincontrol/maintenance/delete_maintenance/'.$recorditem->eqm_id;
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

	<!-- Add Maintenance Model Start -->
	<!-- Modal -->
	<div class="modal fade" id="Modal_addrecord" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
		 aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Add New Maintenance</h5>
					<button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<?php if (isset($error)) { ?>
						<!-- <div class="alert alert-danger alert-error">
							<h4>Error!</h4>
							<?php echo $error; ?>
						</div> -->
					<?php } ?>
					<input type="hidden" name="hidden-eq-id" id="hidden-eq-id">
					<div class="row">
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="eqm_asset" class="col-sm-3 text-right control-label col-form-label">Asset Name</label>
								<div class="col-sm-9">
									<select class="form-control select2 custom-select reset-input" name="eqm_asset" id="eqm_asset" data-live-search="true" autocomplete="off">
										<option value="">---Select---</option>
										<?php foreach($assets as $asset){?>
											<option value="<?php echo $asset->eq_id?>"><?php echo $asset->eqm_asset_name?></option>
										<?php }?>
									</select>
									<small class="invalid-feedback eqm_asset"><?php echo form_error('eqm_asset'); ?></small>
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="eqm_supplier" class="col-sm-3 text-right control-label col-form-label">Supplier</label>
								<div class="col-sm-9">
									<select class="form-control select2 custom-select reset-input" name="eqm_supplier" id="eqm_supplier" data-live-search="true" autocomplete="off">
										<option value="">---Select---</option>
										<?php foreach($suppliers as $supplier){?>
											<option value="<?php echo $supplier->sup_id?>"><?php echo $supplier->sup_name?></option>
										<?php }?>
									</select>
									<small class="invalid-feedback eqm_supplier"><?php echo form_error('eqm_supplier'); ?></small>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="eqm_service_date" class="col-sm-3 text-right control-label col-form-label">Service Date</label>
								<div class="col-sm-9">
									<input type="date" class="form-control reset-input" name="eqm_service_date" id="eqm_service_date" autocomplete="off"/>
									<small class="invalid-feedback eqm_service_date"><?php echo form_error('eqm_service_date'); ?></small>
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="eqm_service_type" class="col-sm-3 text-right control-label col-form-label">Service Type</label>
								<div class="col-sm-9">
									<select class="form-control select2 custom-select reset-input" name="eqm_service_type" id="eqm_service_type" autocomplete="off">
										<option value="">---Select---</option>
										<option value="1">Scheduled Maintenance</option>
										<option value="2">Adhoc Maintenance</option>
									</select>
									<small class="invalid-feedback eqm_service_type"><?php echo form_error('eqm_service_type'); ?></small>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="eqm_notes" class="col-sm-3 text-right control-label col-form-label">Notes</label>
								<div class="col-sm-9">
									<textarea class="form-control reset-input" name="eqm_notes" id="eqm_notes" autocomplete="off"></textarea>
									<small class="invalid-feedback eqm_notes"><?php echo form_error('eqm_notes'); ?></small>
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="eqm_attachment" class="col-sm-3 text-right control-label col-form-label">Attachment</label>
								<div class="col-sm-9">
									<input type="file" class="form-control reset-input" name="eqm_attachment" id="eqm_attachment" autocomplete="off">
									<small class="invalid-feedback eqm_attachment"><?php echo form_error('eqm_attachment'); ?></small>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="eqm_total" class="col-sm-3 text-right control-label col-form-label">Maintenance Total</label>
								<div class="col-sm-9">
									<input type="text" class="form-control reset-input" name="eqm_total" id="eqm_total" autocomplete="off" readonly placeholder="00.00"/>
									<small class="invalid-feedback eqm_total"><?php echo form_error('eqm_total'); ?></small>
								</div>
							</div>
						</div>
					</div>
					<div class="row mt-4">
						<div class="col-12">
							<a href="javascript:;" onclick="goto_add_item();" class="btn btn-primary mb-2"
								style="margin-right: 10px;">Add New Item</a>
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							<div class="table-responsive">
								<table id="eqm_details_table" class="table table-striped table-bordered">
									<thead>
										<tr style="font-weight: bold;">
											<th>Sl No.</th>
											<th>Description</th>
											<th>Notes</th>
											<th>Tax Code</th>
											<th>Qty</th>
											<th>Rate</th>
											<th>Pre Tax Amt</th>
											<th>Tax Amt</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<tr id="row1">
											<input type="hidden" class="hidden-component-id" id="hidden-component-id1">
											<td class="row-index">1</td>
											<td>
												<textarea class="form-control eqmd_description required-eqmd reset-input" id="eqmd_description1" name="eqmd_description" placeholder="Enter Description"></textarea>
												<small class="invalid-feedback eqmd_description1"><?php echo form_error('eqmd_description1'); ?></small>
											</td>
											<td>
												<textarea class="form-control eqmd_notes required-eqmd reset-input" id="eqmd_notes1" name="eqmd_notes" placeholder="Enter Notes"></textarea>
												<small class="invalid-feedback eqmd_notes1"><?php echo form_error('eqmd_notes1'); ?></small>
											</td>
											<td>
													<?php foreach($taxcodes as $taxcode){?>
														<input type="hidden" id="eqmd_taxpercentage1<?php echo $taxcode->name?>" value="<?php echo $taxcode->percentage;?>">
													<?php }?>
												<select class="form-control select2 custom-select eqmd_taxcode required-eqmd reset-input" name="eqmd_taxcode" id="eqmd_taxcode1" data-live-search="true" autocomplete="off" style="width:100%">
													<option value="">---Select---</option>
													<?php foreach($taxcodes as $taxcode){?>
														<option value="<?php echo $taxcode->name?>"><?php echo $taxcode->description;?></option>
													<?php }?>
												</select><br>
												<small class="invalid-feedback eqmd_taxcode1"><?php echo form_error('eqmd_taxcode1'); ?></small>
											</td>
											<td>
												<input type="number" class="form-control eqmd_qty required-eqmd reset-input" id="eqmd_qty1" name="eqmd_qty" step="0.01">
												<small class="invalid-feedback eqmd_qty1"><?php echo form_error('eqmd_qty1'); ?></small>
											</td>
											<td>
												<input type="number" class="form-control eqmd_rate required-eqmd reset-input" id="eqmd_rate1" name="eqmd_rate" step="0.01">
												<small class="invalid-feedback eqmd_rate1"><?php echo form_error('eqmd_rate1'); ?></small>
											</td>
											<td>
												<input type="number" class="form-control eqmd_pre_tax_amt required-eqmd reset-input" id="eqmd_pre_tax_amt1" name="eqmd_pre_tax_amt" step="0.01" readonly>
												<small class="invalid-feedback eqmd_pre_tax_amt1"><?php echo form_error('eqmd_pre_tax_amt1'); ?></small>
											</td>
											<td>
												<input type="number" class="form-control eqmd_tax_amt required-eqmd reset-input" id="eqmd_tax_amt1" name="eqmd_tax_amt" step="0.01" readonly>
												<small class="invalid-feedback eqmd_tax_amt1"><?php echo form_error('eqmd_tax_amt1'); ?></small>
											</td>
											<td>
												<!-- <a id="removeItem" href="javascript:void(0);" title="Delete Record"><i class="fa fa-trash text-danger"></i></a> -->
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="row mt-4">
						<div class="col-12 col-md-4 offset-md-8">
							<table class="table table-bordered">
								<tbody>
									<tr>
										<td>Sub Total</td>
										<td id="sub-total">0.00</td>
									</tr>
									<tr>
										<td>Total Tax</td>
										<td id="tax-total">0.00</td>
									</tr>
									<tr>
										<td>Total</td>
										<td id="total">0.00</td>
									</tr>
								</tbody>
							</table>
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
					<div class="border-top" style="padding:20px 0;text-align:right">
						<button type="button" onclick="gotoclclickbutton();" class="btn btn-primary">Submit</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Modal -->
	<!-- Add Maintenance Model End -->

	<!-- Edit Maintenance Model Start -->
	<!-- Modal -->
	<div class="modal fade" id="Modal_editrecord" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
		 aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Edit Maintenance</h5>
					<button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<?php if (isset($error)) { ?>
						<!-- <div class="alert alert-danger alert-error">
							<h4>Error!</h4>
							<?php echo $error; ?>
						</div> -->
					<?php } ?>
					<input type="hidden" name="hidden-eqm-id" id="hidden-eqm-id">
					<div class="row">
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="update_eqm_asset" class="col-sm-3 text-right control-label col-form-label">Asset Name</label>
								<div class="col-sm-9">
									<select class="form-control select2 custom-select" name="update_eqm_asset" id="update_eqm_asset" data-live-search="true" autocomplete="off">
										<option value="">---Select---</option>
										<?php foreach($assets as $asset){?>
											<option value="<?php echo $asset->eq_id?>"><?php echo $asset->eqm_asset_name?></option>
										<?php }?>
									</select>
									<small class="invalid-feedback update_eqm_asset"><?php echo form_error('update_eqm_asset'); ?></small>
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="update_eqm_supplier" class="col-sm-3 text-right control-label col-form-label">Supplier</label>
								<div class="col-sm-9">
									<select class="form-control select2 custom-select" name="update_eqm_supplier" id="update_eqm_supplier" data-live-search="true" autocomplete="off">
										<option value="">---Select---</option>
										<?php foreach($suppliers as $supplier){?>
											<option value="<?php echo $supplier->sup_id?>"><?php echo $supplier->sup_name?></option>
										<?php }?>
									</select>
									<small class="invalid-feedback update_eqm_supplier"><?php echo form_error('update_eqm_supplier'); ?></small>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="update_eqm_service_date" class="col-sm-3 text-right control-label col-form-label">Service Date</label>
								<div class="col-sm-9">
									<input type="date" class="form-control" name="update_eqm_service_date" id="update_eqm_service_date" autocomplete="off"/>
									<small class="invalid-feedback update_eqm_service_date"><?php echo form_error('update_eqm_service_date'); ?></small>
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="update_eqm_service_type" class="col-sm-3 text-right control-label col-form-label">Service Type</label>
								<div class="col-sm-9">
									<select class="form-control select2 custom-select" name="update_eqm_service_type" id="update_eqm_service_type" autocomplete="off">
										<option value="">---Select---</option>
										<option value="1">Scheduled Maintenance</option>
										<option value="2">Adhoc Maintenance</option>
									</select>
									<small class="invalid-feedback update_eqm_service_type"><?php echo form_error('update_eqm_service_type'); ?></small>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="update_eqm_notes" class="col-sm-3 text-right control-label col-form-label">Notes</label>
								<div class="col-sm-9">
									<textarea class="form-control" name="update_eqm_notes" id="update_eqm_notes" autocomplete="off"></textarea>
									<small class="invalid-feedback update_eqm_notes"><?php echo form_error('update_eqm_notes'); ?></small>
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="update_eqm_attachment" class="col-sm-3 text-right control-label col-form-label">Attachment</label>
								<div class="col-sm-9">
									<input type="file" class="form-control" name="update_eqm_attachment" id="update_eqm_attachment" autocomplete="off">
									<small class="invalid-feedback update_eqm_attachment"><?php echo form_error('update_eqm_attachment'); ?></small>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="update_eqm_total" class="col-sm-3 text-right control-label col-form-label">Maintenance Total</label>
								<div class="col-sm-9">
									<input type="text" class="form-control" name="update_eqm_total" id="update_eqm_total" autocomplete="off" readonly placeholder="00.00"/>
									<small class="invalid-feedback update_eqm_total"><?php echo form_error('update_eqm_total'); ?></small>
								</div>
							</div>
						</div>
					</div>
					<div class="row mt-4">
						<div class="col-12">
							<a href="javascript:;" onclick="goto_update_add_item();" class="btn btn-primary mb-2"
								style="margin-right: 10px;">Add New Item</a>
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							<div class="table-responsive">
								<table id="update_eqm_details_table" class="table table-striped table-bordered">
									<thead>
										<tr style="font-weight: bold;">
											<th>Sl No.</th>
											<th>Description</th>
											<th>Notes</th>
											<th>Tax Code</th>
											<th>Qty</th>
											<th>Rate</th>
											<th>Pre Tax Amt</th>
											<th>Tax Amt</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<tr id="row1">
											<input type="hidden" class="update-hidden-eqmd-id" id="update-hidden-eqmd-id1">
											<td class="row-index">1</td>
											<td>
												<textarea class="form-control update_eqmd_description update-required-eqmd" id="update_eqmd_description1" name="update_eqmd_description" placeholder="Enter Description"></textarea>
												<small class="invalid-feedback update_eqmd_description1"><?php echo form_error('eqmd_description1'); ?></small>
											</td>
											<td>
												<textarea class="form-control update_eqmd_notes update-required-eqmd" id="update_eqmd_notes1" name="update_eqmd_notes" placeholder="Enter Notes"></textarea>
												<small class="invalid-feedback update_eqmd_notes1"><?php echo form_error('update_eqmd_notes1'); ?></small>
											</td>
											<td>
													<?php foreach($taxcodes as $taxcode){?>
														<input type="hidden" id="update_eqmd_taxpercentage1<?php echo $taxcode->name?>" value="<?php echo $taxcode->percentage;?>">
													<?php }?>
												<select class="form-control select2 custom-select update_eqmd_taxcode update-required-eqmd" name="update_eqmd_taxcode" id="update_eqmd_taxcode1" data-live-search="true" autocomplete="off" style="width:100%">
													<option value="">---Select---</option>
													<?php foreach($taxcodes as $taxcode){?>
														<option value="<?php echo $taxcode->name?>"><?php echo $taxcode->description;?></option>
													<?php }?>
												</select><br>
												<small class="invalid-feedback update_eqmd_taxcode1"><?php echo form_error('update_eqmd_taxcode1'); ?></small>
											</td>
											<td>
												<input type="number" class="form-control update_eqmd_qty update-required-eqmd" id="update_eqmd_qty1" name="update_eqmd_qty" step="0.01">
												<small class="invalid-feedback update_eqmd_qty1"><?php echo form_error('update_eqmd_qty1'); ?></small>
											</td>
											<td>
												<input type="number" class="form-control update_eqmd_rate update-required-eqmd" id="update_eqmd_rate1" name="update_eqmd_rate" step="0.01">
												<small class="invalid-feedback update_eqmd_rate1"><?php echo form_error('update_eqmd_rate1'); ?></small>
											</td>
											<td>
												<input type="number" class="form-control update_eqmd_pre_tax_amt update-required-eqmd" id="update_eqmd_pre_tax_amt1" name="update_eqmd_pre_tax_amt" step="0.01" readonly>
												<small class="invalid-feedback update_eqmd_pre_tax_amt1"><?php echo form_error('update_eqmd_pre_tax_amt1'); ?></small>
											</td>
											<td>
												<input type="number" class="form-control update_eqmd_tax_amt update-required-eqmd" id="update_eqmd_tax_amt1" name="update_eqmd_tax_amt" step="0.01" readonly>
												<small class="invalid-feedback update_eqmd_tax_amt1"><?php echo form_error('update_eqmd_tax_amt1'); ?></small>
											</td>
											<td>
												<!-- <a id="removeUItem" href="javascript:void(0);" title="Delete Record"><i class="fa fa-trash text-danger"></i></a> -->
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="row mt-4">
						<div class="col-12 col-md-4 offset-md-8">
							<table class="table table-bordered">
								<tbody>
									<tr>
										<td>Sub Total</td>
										<td id="update_sub-total">0.00</td>
									</tr>
									<tr>
										<td>Total Tax</td>
										<td id="update_tax-total">0.00</td>
									</tr>
									<tr>
										<td>Total</td>
										<td id="update_total">0.00</td>
									</tr>
								</tbody>
							</table>
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
					<div class="border-top" style="padding:20px 0;text-align:right">
						<button type="button" onclick="gotoupdateclickbutton();" class="btn btn-primary">Submit</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Modal -->
	<!-- Edit Maintenance Model End -->

	<!-- footer loder -->
	<?php $this->load->view('admin/component/footer') ?>

	<!-- javascript start -->
	<script type="text/javascript">

		$(function () {
			$('.alert-error, .invalid-feedback').delay(8000).fadeOut();
			$(".select2").selectpicker();
		});

		// Add Maintenance function
		function gotoclclickbutton() {
			// alert('skdfjkns');
			$('.div_roller_total').fadeIn();
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

			var eqm_asset = $('#eqm_asset').val();
			var eqm_supplier = $('#eqm_supplier').val();
			var eqm_service_date = $('#eqm_service_date').val();
			var eqm_service_type = $('#eqm_service_type').val();
			var eqm_notes = $('#eqm_notes').val();
			var eqm_total = $('#eqm_total').val();
			var eqm_attachment = $('#eqm_attachment')[0].files;

			if (eqm_asset == "") {
				e_error = 1;
				$('.eqm_asset').html('Name is Required.');
			} else {
				$('.eqm_asset').html('');
			}

			if (eqm_supplier == "") {
				e_error = 1;
				$('.eqm_supplier').html('Supplier is Required.');
			} else {
				$('.eqm_supplier').html('');
			}

			if (eqm_service_date == "") {
				e_error = 1;
				$('.eqm_service_date').html('Service Date is Required.');
			} else {
				$('.eqm_service_date').html('');
			}

			if (eqm_service_type == "") {
				e_error = 1;
				$('.eqm_service_type').html('Service Type is Required.');
			} else {
				$('.eqm_service_type').html('');
			}

			if (eqm_notes == "") {
				e_error = 1;
				$('.eqm_notes').html('Notes is Required.');
			} else {
				$('.eqm_notes').html('');
			}

			if ($('#eqm_attachment').val() == "") {
				e_error = 1;
				$('.eqm_attachment').html('Attachment is Required.');
			} else {
				$('.eqm_attachment').html('');
			}

			$('.required-eqmd').each(function(){
				var id=$(this).attr('id');
				if ($('#'+id).val() == "") {
					e_error = 1;
					$('.'+id).html('Required');
				} else {
					$('.'+id).html('');
				}
			});

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
				form_data.append("eqm_asset", eqm_asset);
				form_data.append("eqm_supplier", eqm_supplier);
				form_data.append("eqm_service_date", eqm_service_date);
				form_data.append("eqm_service_type", eqm_service_type);
				form_data.append("eqm_notes", eqm_notes);
				form_data.append("eqm_total", eqm_total);
				form_data.append("eqm_attachment", eqm_attachment[0]);
				
				var tableRow=0;
				for(i=1;i<=$('#eqm_details_table tbody tr').length;i++){
					form_data.append("eqmd_description"+i, $('#eqmd_description'+i).val());
					form_data.append("eqmd_notes"+i, $('#eqmd_notes'+i).val());
					form_data.append("eqmd_taxcode"+i, $('#eqmd_taxcode'+i).val());
					form_data.append("eqmd_qty"+i, $('#eqmd_qty'+i).val());
					form_data.append("eqmd_rate"+i, $('#eqmd_rate'+i).val());
					form_data.append("eqmd_pre_tax_amt"+i, $('#eqmd_pre_tax_amt'+i).val());
					form_data.append("eqmd_tax_amt"+i, $('#eqmd_tax_amt'+i).val());
					tableRow++;
				}

				form_data.append("row_count", tableRow);

				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/maintenance/new_maintenance_submission') ?>",
					dataType: 'json',
					data: form_data,
					contentType: false,
					cache: true,
					processData: false,
					success: function (data) {
						// alert(JSON.stringify(data.s_msg));
						if (data.msg == 1) {
							//console.log(data);
							//alert(data.msg[0].space_rate);
							$('.div_roller_total').fadeOut();
							toastr.success('Record is Inserted Successfully!', 'Success');
							setTimeout(function () {
								window.location.replace("<?php echo site_url('admincontrol/maintenance/all_maintenance_list') ?>");
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
					error: function (request, error) {
						// alert(request.s_msg);
						console.log(arguments);
						alert(" Can't do because: " + error);
					}
				});
			}
			//$("#myForm").submit();


		}

		// Update Maintenance function
		function gotoupdateclickbutton() {
			// alert('skdfjkns');
			$('.div_roller_total').fadeIn();
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

			var eqm_id = $('#hidden-eqm-id').val();
			var eqm_asset = $('#update_eqm_asset').val();
			var eqm_supplier = $('#update_eqm_supplier').val();
			var eqm_service_date = $('#update_eqm_service_date').val();
			var eqm_service_type = $('#update_eqm_service_type').val();
			var eqm_notes = $('#update_eqm_notes').val();
			var eqm_total = $('#update_eqm_total').val();
			var eqm_attachment = $('#update_eqm_attachment')[0].files;

			if (eqm_asset == "") {
				e_error = 1;
				$('.update_eqm_asset').html('Name is Required.');
			} else {
				$('.update_eqm_asset').html('');
			}

			if (eqm_supplier == "") {
				e_error = 1;
				$('.update_eqm_supplier').html('Supplier is Required.');
			} else {
				$('.update_eqm_supplier').html('');
			}

			if (eqm_service_date == "") {
				e_error = 1;
				$('.update_eqm_service_date').html('Service Date is Required.');
			} else {
				$('.update_eqm_service_date').html('');
			}

			if (eqm_service_type == "") {
				e_error = 1;
				$('.update_eqm_service_type').html('Service Type is Required.');
			} else {
				$('.update_eqm_service_type').html('');
			}

			if (eqm_notes == "") {
				e_error = 1;
				$('.update_eqm_notes').html('Notes is Required.');
			} else {
				$('.update_eqm_notes').html('');
			}

			// if ($('#eqm_attachment').val() == "") {
			// 	e_error = 1;
			// 	$('.eqm_attachment').html('Attachment is Required.');
			// } else {
			// 	$('.eqm_attachment').html('');
			// }

			$('.update-required-eqmd').each(function(){
				var id=$(this).attr('id');
				if ($('#'+id).val() == "") {
					e_error = 1;
					$('.'+id).html('Required');
				} else {
					$('.'+id).html('');
				}
			});

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
				form_data.append("eqm_id", eqm_id);
				form_data.append("eqm_asset", eqm_asset);
				form_data.append("eqm_supplier", eqm_supplier);
				form_data.append("eqm_service_date", eqm_service_date);
				form_data.append("eqm_service_type", eqm_service_type);
				form_data.append("eqm_notes", eqm_notes);
				form_data.append("eqm_total", eqm_total);
				form_data.append("eqmd_delete_ids", JSON.stringify(rmComponentArray1));
				form_data.append("eqm_attachment", eqm_attachment[0]);
				
				var tableRow=0;
				for(i=1;i<=$('#update_eqm_details_table tbody tr').length;i++){
					form_data.append("eqmd_id"+i, $('#update-hidden-eqmd-id'+i).val());
					form_data.append("eqmd_description"+i, $('#update_eqmd_description'+i).val());
					form_data.append("eqmd_notes"+i, $('#update_eqmd_notes'+i).val());
					form_data.append("eqmd_taxcode"+i, $('#update_eqmd_taxcode'+i).val());
					form_data.append("eqmd_qty"+i, $('#update_eqmd_qty'+i).val());
					form_data.append("eqmd_rate"+i, $('#update_eqmd_rate'+i).val());
					form_data.append("eqmd_pre_tax_amt"+i, $('#update_eqmd_pre_tax_amt'+i).val());
					form_data.append("eqmd_tax_amt"+i, $('#update_eqmd_tax_amt'+i).val());
					tableRow++;
				}

				form_data.append("row_count", tableRow);

				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/maintenance/update_maintenance_submission') ?>",
					dataType: 'json',
					data: form_data,
					contentType: false,
					cache: true,
					processData: false,
					success: function (data) {
						// alert(JSON.stringify(data.s_msg));
						if (data.msg == 1) {
							//console.log(data);
							//alert(data.msg[0].space_rate);
							$('.div_roller_total').fadeOut();
							toastr.success('Record is Inserted Successfully!', 'Success');
							setTimeout(function () {
								window.location.replace("<?php echo site_url('admincontrol/maintenance/all_maintenance_list') ?>");
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
					error: function (request, error) {
						// alert(request.s_msg);
						console.log(arguments);
						alert(" Can't do because: " + error);
					}
				});
			}
			//$("#myForm").submit();


		}

		function goto_add_record() {
			$('.reset-input').val('');
			$('#eqm_asset,#eqm_supplier,#eqm_service_type').change();
			$('select[name="eqmd_taxcode"]').selectpicker('refresh');
			$('#sub-total,#tax-total,#total').html('0.00');
			$('#Modal_addrecord').modal('show');
		}

		var rowCount=2;
		function goto_add_item(){
			$('#eqm_details_table tbody').append('\
			<tr id="row'+rowCount+'">\
				<input type="hidden" class="hidden-component-id" id="hidden-component-id'+rowCount+'">\
				<td class="row-index">'+rowCount+'</td>\
				<td>\
					<textarea class="form-control eqmd_description required-eqmd" id="eqmd_description'+rowCount+'" name="eqmd_description" placeholder="Enter Description"></textarea>\
					<small class="invalid-feedback eqmd_description'+rowCount+'"></small>\
				</td>\
				<td>\
					<textarea class="form-control eqmd_notes required-eqmd" id="eqmd_notes'+rowCount+'" name="eqmd_notes" placeholder="Enter Notes"></textarea>\
					<small class="invalid-feedback eqmd_notes'+rowCount+'"></small>\
				</td>\
				<td>\
					<?php foreach($taxcodes as $taxcode){?>
						<input type="hidden" id="eqmd_taxpercentage'+rowCount+'<?php echo $taxcode->name?>" value="<?php echo $taxcode->percentage;?>">\
					<?php }?>
					<select class="form-control select2 custom-select eqmd_taxcode required-eqmd" name="eqmd_taxcode" id="eqmd_taxcode'+rowCount+'" autocomplete="off" style="width:100%">\
						<option value="">---Select---</option>\
						<?php foreach($taxcodes as $taxcode){?>
							<option value="<?php echo $taxcode->name?>"><?php echo $taxcode->description;?></option>\
						<?php }?>
					</select><br>\
					<small class="invalid-feedback eqmd_taxcode'+rowCount+'"></small>\
				</td>\
				<td>\
					<input type="number" class="form-control eqmd_qty required-eqmd" id="eqmd_qty'+rowCount+'" name="eqmd_qty" step="0.01">\
					<small class="invalid-feedback eqmd_qty'+rowCount+'"></small>\
				</td>\
				<td>\
					<input type="number" class="form-control eqmd_rate required-eqmd" id="eqmd_rate'+rowCount+'" name="eqmd_rate" step="0.01">\
					<small class="invalid-feedback eqmd_tax_amt'+rowCount+'"></small>\
				</td>\
				<td>\
					<input type="number" class="form-control eqmd_pre_tax_amt required-eqmd" id="eqmd_pre_tax_amt'+rowCount+'" name="eqmd_pre_tax_amt" step="0.01" readonly>\
					<small class="invalid-feedback eqmd_pre_tax_amt'+rowCount+'"></small>\
				</td>\
				<td>\
					<input type="number" class="form-control eqmd_tax_amt required-eqmd" id="eqmd_tax_amt'+rowCount+'" name="eqmd_tax_amt" step="0.01" readonly>\
					<small class="invalid-feedback eqmd_tax_amt'+rowCount+'"></small>\
				</td>\
				<td>\
					<a id="removeItem" href="javascript:void(0);" title="Delete Record"><i class="fa fa-trash text-danger"></i></a>\
				</td>\
			</tr>\
			');
			rowCount++;
		}

		var rowCountUpdate=2;
		function goto_update_add_item(){
			rowCountUpdate=($('#update_eqm_details_table tbody tr').length+1);
			$('#update_eqm_details_table tbody').append('\
			<tr id="row'+rowCountUpdate+'">\
				<input type="hidden" class="update-hidden-eqmd-id" id="update-hidden-eqmd-id'+rowCountUpdate+'">\
				<td class="row-index">'+rowCountUpdate+'</td>\
				<td>\
					<textarea class="form-control update_eqmd_description update-required-eqmd" id="update_eqmd_description'+rowCountUpdate+'" name="update_eqmd_description" placeholder="Enter Description"></textarea>\
					<small class="invalid-feedback update_eqmd_description'+rowCountUpdate+'"></small>\
				</td>\
				<td>\
					<textarea class="form-control update_eqmd_notes update-required-eqmd" id="update_eqmd_notes'+rowCountUpdate+'" name="update_eqmd_notes" placeholder="Enter Notes"></textarea>\
					<small class="invalid-feedback update_eqmd_notes'+rowCountUpdate+'"></small>\
				</td>\
				<td>\
					<?php foreach($taxcodes as $taxcode){?>
						<input type="hidden" id="update_eqmd_taxpercentage'+rowCountUpdate+'<?php echo $taxcode->name?>" value="<?php echo $taxcode->percentage;?>">\
					<?php }?>
					<select class="form-control select2 custom-select update_eqmd_taxcode update-required-eqmd" name="update_eqmd_taxcode" id="update_eqmd_taxcode'+rowCountUpdate+'" autocomplete="off" style="width:100%">\
						<option value="">---Select---</option>\
						<?php foreach($taxcodes as $taxcode){?>
							<option value="<?php echo $taxcode->name?>"><?php echo $taxcode->description;?></option>\
						<?php }?>
					</select><br>\
					<small class="invalid-feedback update_eqmd_taxcode'+rowCountUpdate+'"></small>\
				</td>\
				<td>\
					<input type="number" class="form-control update_eqmd_qty update-required-eqmd" id="update_eqmd_qty'+rowCountUpdate+'" name="update_eqmd_qty" step="0.01">\
					<small class="invalid-feedback eqmd_qty'+rowCountUpdate+'"></small>\
				</td>\
				<td>\
					<input type="number" class="form-control update_eqmd_rate update-required-eqmd" id="update_eqmd_rate'+rowCountUpdate+'" name="update_eqmd_rate" step="0.01">\
					<small class="invalid-feedback update_eqmd_tax_amt'+rowCountUpdate+'"></small>\
				</td>\
				<td>\
					<input type="number" class="form-control update_eqmd_pre_tax_amt update-required-eqmd" id="update_eqmd_pre_tax_amt'+rowCountUpdate+'" name="update_eqmd_pre_tax_amt" step="0.01" readonly>\
					<small class="invalid-feedback update_eqmd_pre_tax_amt'+rowCountUpdate+'"></small>\
				</td>\
				<td>\
					<input type="number" class="form-control update_eqmd_tax_amt update-required-eqmd" id="update_eqmd_tax_amt'+rowCountUpdate+'" name="update_eqmd_tax_amt" step="0.01" readonly>\
					<small class="invalid-feedback update_eqmd_tax_amt'+rowCountUpdate+'"></small>\
				</td>\
				<td>\
					<a id="removeUItem" href="javascript:void(0);" title="Delete Record"><i class="fa fa-trash text-danger"></i></a>\
				</td>\
			</tr>\
			');
			rowCountUpdate++;
		}

		var rmComponentArray= [];

		$('#eqm_details_table tbody').on('click', '#removeItem', function(){
			if (confirm('Are you sure you want to delete ?')) {
				var rowId=$(this).closest('tr').attr('id');
				var index=rowId.match(/\d+/);
				if($(this).closest('tr').children('#hidden-component-id'+index).val()!=""){
					rmComponentArray.push($(this).closest('tr').children('#hidden-component-id'+index).val());
				}
				var child=$(this).closest('tr').nextAll();
				child.each(function(){
					var id=$(this).attr('id');
					var idx=$(this).children('.row-index');
					var dig=id.match(/\d+/);
					idx.html(`${dig-1}`);
					$(this).attr('id',`row${dig-1}`);
					$(this).children('.hidden-component-id').attr('id',`hidden-component-id${dig-1}`);
					$(this).children('td').children('.eqmd_description').attr('id',`eqmd_description${dig-1}`);
					$(this).find('select').attr('id',`eqmd_taxcode${dig-1}`);
					$(this).children('td').children('.eqmd_notes').attr('id',`eqmd_notes${dig-1}`);
					$(this).children('td').children('.eqmd_qty').attr('id',`eqmd_qty${dig-1}`);
					$(this).children('td').children('.eqmd_rate').attr('id',`eqmd_rate${dig-1}`);
					$(this).children('td').children('.eqmd_pre_tax_amt').attr('id',`eqmd_pre_tax_amt${dig-1}`);
					$(this).children('td').children('.eqmd_tax_amt').attr('id',`eqmd_tax_amt${dig-1}`);
				});
				$(this).parent().parent().remove();
				rowCount--;
			} else {
			}

			calculateTotal();
			// alert(rmComponentArray);
    	});

		var rmComponentArray1= [];

		$('#update_eqm_details_table tbody').on('click', '#removeUItem', function(){
			if (confirm('Are you sure you want to delete ?')) {
				var rowId=$(this).closest('tr').attr('id');
				var index=rowId.match(/\d+/);
				if($(this).closest('tr').children('#update-hidden-eqmd-id'+index).val()!=""){
					rmComponentArray1.push($(this).closest('tr').children('#update-hidden-eqmd-id'+index).val());
				}
				var child=$(this).closest('tr').nextAll();
				child.each(function(){
					var id=$(this).attr('id');
					var idx=$(this).children('.row-index');
					var dig=id.match(/\d+/);
					idx.html(`${dig-1}`);
					$(this).attr('id',`row${dig-1}`);
					$(this).children('.update-hidden-eqmd-id').attr('id',`update-hidden-eqmd-id${dig-1}`);
					$(this).children('td').children('.update_eqmd_description').attr('id',`update_eqmd_description${dig-1}`);
					$(this).find('select').attr('id',`update_eqmd_taxcode${dig-1}`);
					$(this).children('td').children('.update_eqmd_notes').attr('id',`update_eqmd_notes${dig-1}`);
					$(this).children('td').children('.update_eqmd_qty').attr('id',`update_eqmd_qty${dig-1}`);
					$(this).children('td').children('.update_eqmd_rate').attr('id',`update_eqmd_rate${dig-1}`);
					$(this).children('td').children('.update_eqmd_pre_tax_amt').attr('id',`update_eqmd_pre_tax_amt${dig-1}`);
					$(this).children('td').children('.update_eqmd_tax_amt').attr('id',`update_eqmd_tax_amt${dig-1}`);
				});
				$(this).parent().parent().remove();
				rowCountUpdate--;
			} else {
			}

			calculateTotal1();
			// alert(rmComponentArray);
    	});

		$(function () {
			$('#alert_msg').delay(6000).fadeOut();
			//$('.select22, .select33').selectpicker();
			$('.alert-error, .invalid-feedback').delay(6000).fadeOut();
		});


		/****************************************
		 *       Create data tables             *
		 ****************************************/
		$('#zero_config').DataTable();
		$('#eqm_details_table').DataTable({
			"autoWidth":false,
			"columns": [
				{ "width": "5%" },
				{ "width": "20%" },
				{ "width": "20%" },
				{ "width": "5%" },
				{ "width": "10%" },
				{ "width": "10%" },
				{ "width": "10%" },
				{ "width": "10%" },
				{ "width": "10%" }
			],
		});

		$(document).on('change','.eqmd_taxcode,.eqmd_qty,.eqmd_rate',function(){
			var id=$(this).attr('id');
			var idNo=id.match(/\d+/);
			var taxcode=$('#eqmd_taxcode'+idNo).val();
			var percentage=$('#eqmd_taxpercentage'+idNo+taxcode).val();
			var qty=$('#eqmd_qty'+idNo).val();
			var rate=$('#eqmd_rate'+idNo).val();
			var preTaxAmt=qty*rate;
			var tax=percentage/100*preTaxAmt;
			// alert(percentage);

			$('#eqmd_pre_tax_amt'+idNo).val(parseFloat(preTaxAmt).toFixed(2));
			$('#eqmd_tax_amt'+idNo).val(parseFloat(tax).toFixed(2));
			var subtotal=0;
			var taxtotal=0;
			$('.eqmd_pre_tax_amt').each(function(){
				var preTaxAmt=$(this).val();
				if(preTaxAmt==''){
					preTaxAmt=0;
				}
				subtotal=(parseFloat(subtotal)+parseFloat(preTaxAmt)).toFixed(2);
			});
			$('.eqmd_tax_amt').each(function(){
				var taxAmt=$(this).val();
				if(taxAmt==''){
					taxAmt=0;
				}
				taxtotal=(parseFloat(taxtotal)+parseFloat(taxAmt)).toFixed(2);
			});
			var total=(parseFloat(subtotal)+parseFloat(taxtotal)).toFixed(2);
			$('#sub-total').html(subtotal);
			$('#tax-total').html(taxtotal);
			$('#total').html(total);
			$('#eqm_total').val(total);
		});

		$(document).on('change','.update_eqmd_taxcode,.update_eqmd_qty,.update_eqmd_rate',function(){
			var id=$(this).attr('id');
			var idNo=id.match(/\d+/);
			var taxcode=$('#update_eqmd_taxcode'+idNo).val();
			var percentage=$('#update_eqmd_taxpercentage'+idNo+taxcode).val();
			var qty=$('#update_eqmd_qty'+idNo).val();
			var rate=$('#update_eqmd_rate'+idNo).val();
			var preTaxAmt=qty*rate;
			var tax=percentage/100*preTaxAmt;
			// alert(percentage);

			$('#update_eqmd_pre_tax_amt'+idNo).val(parseFloat(preTaxAmt).toFixed(2));
			$('#update_eqmd_tax_amt'+idNo).val(parseFloat(tax).toFixed(2));
			var subtotal=0;
			var taxtotal=0;
			$('.update_eqmd_pre_tax_amt').each(function(){
				var preTaxAmt=$(this).val();
				if(preTaxAmt==''){
					preTaxAmt=0;
				}
				subtotal=(parseFloat(subtotal)+parseFloat(preTaxAmt)).toFixed(2);
			});
			$('.update_eqmd_tax_amt').each(function(){
				var taxAmt=$(this).val();
				if(taxAmt==''){
					taxAmt=0;
				}
				taxtotal=(parseFloat(taxtotal)+parseFloat(taxAmt)).toFixed(2);
			});
			var total=(parseFloat(subtotal)+parseFloat(taxtotal)).toFixed(2);
			$('#update_sub-total').html(subtotal);
			$('#update_tax-total').html(taxtotal);
			$('#update_total').html(total);
			$('#update_eqm_total').val(total);
		});

		function calculateTotal(){
			var subtotal=0;
			var taxtotal=0;
			$('.eqmd_pre_tax_amt').each(function(){
				var preTaxAmt=$(this).val();
				if(preTaxAmt==''){
					preTaxAmt=0;
				}
				subtotal=(parseFloat(subtotal)+parseFloat(preTaxAmt)).toFixed(2);
			});
			$('.eqmd_tax_amt').each(function(){
				var taxAmt=$(this).val();
				if(taxAmt==''){
					taxAmt=0;
				}
				taxtotal=(parseFloat(taxtotal)+parseFloat(taxAmt)).toFixed(2);
			});
			var total=(parseFloat(subtotal)+parseFloat(taxtotal)).toFixed(2);
			$('#sub-total').html(subtotal);
			$('#tax-total').html(taxtotal);
			$('#total').html(total);
			$('#eqm_total').val(total);
		}

		function calculateTotal1(){
			var subtotal=0;
			var taxtotal=0;
			$('.update_eqmd_pre_tax_amt').each(function(){
				var preTaxAmt=$(this).val();
				if(preTaxAmt==''){
					preTaxAmt=0;
				}
				subtotal=(parseFloat(subtotal)+parseFloat(preTaxAmt)).toFixed(2);
			});
			$('.update_eqmd_tax_amt').each(function(){
				var taxAmt=$(this).val();
				if(taxAmt==''){
					taxAmt=0;
				}
				taxtotal=(parseFloat(taxtotal)+parseFloat(taxAmt)).toFixed(2);
			});
			var total=(parseFloat(subtotal)+parseFloat(taxtotal)).toFixed(2);
			$('#update_sub-total').html(subtotal);
			$('#update_tax-total').html(taxtotal);
			$('#update_total').html(total);
			$('#update_eqm_total').val(total);
		}

		// Trigers when edit is pressed for a particular Maintenance
		function modify_record(element) {
			// alert(element);
			if (element != "") {

				var form_data = new FormData();
				form_data.append("eqm_id", element);

				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/maintenance/get_details_of_maintenance') ?>",
					dataType: 'json',
					data: form_data,
					contentType: false,
					cache: true,
					processData: false,
					success: function (data) {
				// 		//alert(data.msg);
						if (data.msg == 1) {
				// 			//console.log(data);
							// alert(JSON.stringify(data));
							$('#hidden-eqm-id').val(data.s_msg[0].eqm_id);
							$('#update_eqm_asset').val(data.s_msg[0].asset_id);
							$('#update_eqm_supplier').val(data.s_msg[0].vendor_id);
							$('#update_eqm_service_date').val(data.s_msg[0].service_date);
							$('#update_eqm_service_type').val(data.s_msg[0].service_type);
							$('#update_eqm_notes').val(data.s_msg[0].maintenance_notes);
							$('#update_eqm_total').val(data.s_msg[0].maintenance_total);

							if(data.c_msg.length!=0){
								// alert(data.c_msg.length);
								$('#update_eqm_details_table tbody').html('');
								for(k=0;k<data.c_msg.length;k++){
									if(k==0){
									$('#update_eqm_details_table tbody').append('\
									<tr id="row'+(k+1)+'">\
										<input type="hidden" class="update-hidden-eqmd-id" id="update-hidden-eqmd-id'+(k+1)+'" value="'+data.c_msg[k].eqmd_id+'">\
										<td>'+(k+1)+'</td>\
										<td>\
											<textarea class="form-control update_eqmd_description update-required-eqmd" id="update_eqmd_description'+(k+1)+'" name="update_eqmd_description" placeholder="Enter Description">'+data.c_msg[k].description+'</textarea>\
											<small class="invalid-feedback update_eqmd_description'+(k+1)+'"></small>\
										</td>\
										<td>\
											<textarea class="form-control update_eqmd_notes update-required-eqmd" id="update_eqmd_notes'+(k+1)+'" name="update_eqmd_notes" placeholder="Enter Notes">'+data.c_msg[k].notes+'</textarea>\
											<small class="invalid-feedback update_eqmd_notes'+(k+1)+'"></small>\
										</td>\
										<td>\
												<?php foreach($taxcodes as $taxcode){?>
													<input type="hidden" id="eqmd_taxpercentage'+(k+1)+'<?php echo $taxcode->name?>" value="<?php echo $taxcode->percentage;?>">\
												<?php }?>
											<select class="form-control select2 custom-select update_eqmd_taxcode update-required-eqmd" name="update_eqmd_taxcode" id="update_eqmd_taxcode'+(k+1)+'" autocomplete="off" style="width:100%">\
												<option value="">---Select---</option>\
												<?php foreach($taxcodes as $taxcode){?>
													<option value="<?php echo $taxcode->name?>" '+(data.c_msg[k].tax_code== "<?php echo $taxcode->name?>" ? 'selected': '')+'><?php echo $taxcode->description;?></option>\
												<?php }?>
											</select><br>\
											<small class="invalid-feedback update_eqmd_taxcode'+(k+1)+'"></small>\
										</td>\
										<td>\
											<input type="number" class="form-control update_eqmd_qty update-required-eqmd" id="update_eqmd_qty'+(k+1)+'" name="update_eqmd_qty" value="'+data.c_msg[k].qty+'" step="0.01">\
											<small class="invalid-feedback update_eqmd_qty'+(k+1)+'"></small>\
										</td>\
										<td>\
											<input type="number" class="form-control update_eqmd_rate update-required-eqmd" id="update_eqmd_rate'+(k+1)+'" name="update_eqmd_rate" value="'+data.c_msg[k].rate+'" step="0.01">\
											<small class="invalid-feedback update_eqmd_rate'+(k+1)+'"></small>\
										</td>\
										<td>\
											<input type="number" class="form-control update_eqmd_pre_tax_amt update-required-eqmd" id="update_eqmd_pre_tax_amt'+(k+1)+'" name="update_eqmd_pre_tax_amt" value="'+data.c_msg[k].pre_tax_amount+'" step="0.01" readonly>\
											<small class="invalid-feedback update_eqmd_pre_tax_amt'+(k+1)+'"></small>\
										</td>\
										<td>\
											<input type="number" class="form-control update_eqmd_tax_amt update-required-eqmd" id="update_eqmd_tax_amt'+(k+1)+'" name="update_eqmd_tax_amt" step="0.01" value="'+data.c_msg[k].tax_amount+'" readonly>\
											<small class="invalid-feedback update_eqmd_tax_amt'+(k+1)+'"></small>\
										</td>\
										<td>\
										</td>\
									</tr>\
									');
										
									}else{
									$('#update_eqm_details_table tbody').append('\
									<tr id="row'+(k+1)+'">\
										<input type="hidden" class="update-hidden-eqmd-id" id="update-hidden-eqmd-id'+(k+1)+'" value="'+data.c_msg[k].eqmd_id+'">\
										<td>'+(k+1)+'</td>\
										<td>\
											<textarea class="form-control update_eqmd_description update-required-eqmd" id="update_eqmd_description'+(k+1)+'" name="update_eqmd_description" placeholder="Enter Description">'+data.c_msg[k].description+'</textarea>\
											<small class="invalid-feedback update_eqmd_description'+(k+1)+'"></small>\
										</td>\
										<td>\
											<textarea class="form-control update_eqmd_notes update-required-eqmd" id="update_eqmd_notes'+(k+1)+'" name="update_eqmd_notes" placeholder="Enter Notes">'+data.c_msg[k].notes+'</textarea>\
											<small class="invalid-feedback update_eqmd_notes'+(k+1)+'"></small>\
										</td>\
										<td>\
												<?php foreach($taxcodes as $taxcode){?>
													<input type="hidden" id="eqmd_taxpercentage'+(k+1)+'<?php echo $taxcode->name?>" value="<?php echo $taxcode->percentage;?>">\
												<?php }?>
											<select class="form-control select2 custom-select update_eqmd_taxcode update-required-eqmd" name="update_eqmd_taxcode" id="update_eqmd_taxcode'+(k+1)+'" autocomplete="off" style="width:100%">\
												<option value="">---Select---</option>\
												<?php foreach($taxcodes as $taxcode){?>
													<option value="<?php echo $taxcode->name?>" '+(data.c_msg[k].tax_code== "<?php echo $taxcode->name?>" ? 'selected': '')+'><?php echo $taxcode->description;?></option>\
												<?php }?>
											</select><br>\
											<small class="invalid-feedback update_eqmd_taxcode'+(k+1)+'"></small>\
										</td>\
										<td>\
											<input type="number" class="form-control update_eqmd_qty update-required-eqmd" id="update_eqmd_qty'+(k+1)+'" name="update_eqmd_qty" value="'+data.c_msg[k].qty+'" step="0.01">\
											<small class="invalid-feedback update_eqmd_qty'+(k+1)+'"></small>\
										</td>\
										<td>\
											<input type="number" class="form-control update_eqmd_rate update-required-eqmd" id="update_eqmd_rate'+(k+1)+'" name="update_eqmd_rate" value="'+data.c_msg[k].rate+'" step="0.01">\
											<small class="invalid-feedback update_eqmd_rate'+(k+1)+'"></small>\
										</td>\
										<td>\
											<input type="number" class="form-control update_eqmd_pre_tax_amt update-required-eqmd" id="update_eqmd_pre_tax_amt'+(k+1)+'" name="update_eqmd_pre_tax_amt" value="'+data.c_msg[k].pre_tax_amount+'" step="0.01" readonly>\
											<small class="invalid-feedback update_eqmd_pre_tax_amt'+(k+1)+'"></small>\
										</td>\
										<td>\
											<input type="number" class="form-control update_eqmd_tax_amt update-required-eqmd" id="update_eqmd_tax_amt'+(k+1)+'" name="update_eqmd_tax_amt" step="0.01" value="'+data.c_msg[k].tax_amount+'" readonly>\
											<small class="invalid-feedback update_eqmd_tax_amt'+(k+1)+'"></small>\
										</td>\
										<td>\
											<a id="removeUItem" href="javascript:void(0);" title="Delete Record"><i class="fa fa-trash text-danger"></i></a>\
										</td>\
									</tr>\
									');
									}
								}
							}else{
								$('#update_eqm_details_table tbody').html('');
								// $('#update_eqm_details_table tbody').append('\
								// 	<tr id="row1">\
								// 		<td colspan="9" style="text-align:center">\
								// 			NO MAINTENANCE AVAILABLE\
								// 		</td>\
								// 	</tr>\
								// ');
							}
							calculateTotal1();
							$(".select2").selectpicker('refresh');
							$('#Modal_editrecord').modal('show');

						} else {
							$('#update_pr_no').val('');
							$('#Modal_editrecord').modal('hide');
						}
					}
				});
			} else {
				$('#update_pr_no').val('');
				$('#Modal_editrecord').modal('hide');
			}
		}

		// on asset category change
		$('#eq_category').on('change',function(){
			if($(this).val()=='Vehicles'){
				$('.vehicle').css('display','flex');
			}else{
				$('.vehicle').css('display','none');
			}
		});

		// $('.eqmd_eqmd_tax_amt').on('change',function(){
		// });

	</script>	
	<!-- javascript ens -->
        
