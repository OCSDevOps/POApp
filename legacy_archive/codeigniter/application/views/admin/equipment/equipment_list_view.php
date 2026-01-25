<?php $this->load->view('admin/component/header') ?>


<?php $this->load->view('admin/component/menu') ?>

<!-- style to increase width of models -->

<style>
	#Modal_addrecord .modal-dialog,#Modal_editrecord .modal-dialog {
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
				<h4 class="page-title">Equipment List</h4>
				<div class="ml-auto text-right">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a
										href="<?php echo base_url('admincontrol/dashboard'); ?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Equipment List</li>
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
									<label class="col-sm-1 text-right control-label col-form-label">Location</label>
									<div class="col-sm-1" style="padding:0px">
										<select name="project" class="form-control">
											<option value="">Select Option</option>

											<?php foreach ($projects as $project) {?>
											<option value="<?php echo $project->proj_id ?>" <?php echo isset($filters['eqm_location']) ? ($filters['eqm_location'] == $project->proj_id ? 'selected' : '') : '' ?>><?php echo $project->proj_name?></option>
											<?php } ?>
										</select>
									</div>
									<label class="col-sm-1 text-right control-label col-form-label">Category</label>
									<div class="col-sm-1" style="padding:0px">
										<select name="category" class="form-control">
											<option value="">Select Option</option>
											<option value="Small Tool"  <?php echo isset($filters['eqm_category']) ? ($filters['eqm_category'] == "Small Tool" ? 'selected' : '') : '' ?>>Small Tool</option>
											<option value="Equipments" <?php echo isset($filters['eqm_category']) ? ($filters['eqm_category'] == "Equipments" ? 'selected' : '') : '' ?>>Equipments</option>
											<option value="Vehicles" <?php echo isset($filters['eqm_category']) ? ($filters['eqm_category'] == "Vehicles" ? 'selected' : '') : '' ?>>Vehicles</option>
										</select>
									</div>
									<label class="col-sm-1 text-right control-label col-form-label">Type</label>
									<div class="col-sm-1" style="padding:0px">
										<select name="type" class="form-control">
											<option value="">Select Option</option>
											<option value="Owned"  <?php echo isset($filters['eqm_asset_type']) ? ($filters['eqm_asset_type'] == "Owned" ? 'selected' : '') : '' ?>>Owned</option>
											<option value="Rental" <?php echo isset($filters['eqm_asset_type']) ? ($filters['eqm_asset_type'] == "Rental" ? 'selected' : '') : '' ?>>Rental</option>
										</select>
									</div>
									<label class="col-sm-1 text-right control-label col-form-label">Status</label>
									<div class="col-sm-1" style="padding:0px">
										<select name="status" class="form-control">
											<option value="">Select Option</option>
											<option value="Available"  <?php echo isset($filters['eqm_status']) ? ($filters['eqm_status'] == "Available" ? 'selected' : '') : '' ?>>Available</option>
											<option value="In Use" <?php echo isset($filters['eqm_status']) ? ($filters['eqm_status'] == "In Use" ? 'selected' : '') : '' ?>>In Use</option>
											<option value="In Maintenance" <?php echo isset($filters['eqm_status']) ? ($filters['eqm_status'] == "In Maintenance" ? 'selected' : '') : '' ?>>In Maintenance</option>
											<option value="Retired" <?php echo isset($filters['eqm_status']) ? ($filters['eqm_status'] == "Retired" ? 'selected' : '') : '' ?>>Retired</option>
										</select>
									</div>
									<label class="col-sm-1 text-right control-label col-form-label">User</label>
									<div class="col-sm-1" style="padding:0px">
										<select name="user" class="form-control">
											<option value="">Select Option</option>
											<?php foreach($users as $user){?>
												<option value="<?php echo $user->u_id;?>" <?php echo isset($filters['eqm_current_operator']) ? ($filters['eqm_current_operator'] == $user->u_id ? 'selected' : '') : '' ?>><?php echo $user->firstname.' '.$user->lastname;?></option>
											<?php }?>
										</select>
									</div>
									<div class="col-sm-2">
										<button type="submit" class="btn btn-primary">Search</button>
										<button type="button" onclick="window.location.href='<?php echo base_url("admincontrol/equipments/all_equipment_list"); ?>'" class="btn btn-success">Reset</button>
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
						if($this->session->userdata('utype')==1 || $templateDetails->pt_e_eq<3){?>
							<a href="javascript:;" onclick="goto_add_record();" class="btn btn-primary float-right mb-2"
							style="margin-right: 10px;">Add New Equipment</a>
							<a href="javascript:;" onclick="checkin_asset('');" class="btn btn-primary float-right mb-2"
							style="margin-right: 10px;">Check In</a>
							<a href="javascript:;" onclick="checkout_asset('');" class="btn btn-primary float-right mb-2"
							style="margin-right: 10px;">Check Out</a>
						<?php }?>
						<div class="table-responsive">
							<table id="equipments-table" class="table table-striped table-bordered ">
								<thead>
								<tr style="font-weight: bold;">
									<th>Sl No.</th>
									<th>Picture</th>
									<th>Name</th>
									<th style="max-width:140px!important;overflow:hidden;word-wrap:break-word;white-space: normal;">Description</th>
									<th>Type</th>
									<th>Tag</th>
									<th>Condition</th>
									<th>Category</th>
									<th>Remaining Life</th>
									<th>Status</th>
									<?php 
									if($this->session->userdata('utype')==1 || $templateDetails->pt_e_eq<3){?>
										<th>Action</th>
									<?php }?>
								</tr>
								</thead>
								<tbody>
								<?php foreach ($getrecord_list as $keys => $recorditem) { ?>
									<tr>
										<td><?php echo $keys + 1; ?></td>
										<td><?php if($recorditem->eqm_asset_picture!=''){ ?><img class="img-responsive" style="width:120px" src="<?=base_url('/upload_file/equipment/'.$recorditem->eqm_asset_picture)?>"><?php }else{echo '';}?></td>
										<td style="max-width:120px!important;overflow:hidden;word-wrap:break-word;white-space: normal;"><?php echo $recorditem->eqm_asset_name; ?></td>
										<td style="max-width:200px!important;overflow:hidden;word-wrap:break-word;white-space: normal;"><?php echo $recorditem->eqm_asset_description; ?></td>
										<td><?php echo $recorditem->eqm_asset_type; ?></td>
										<td><?php echo $recorditem->eqm_asset_tag; ?></td>
										<td><?php echo $recorditem->eqm_asset_condition; ?></td>
										<td><?php echo $recorditem->eqm_category; ?></td>
										<td>
											<?php echo $recorditem->eqm_remaining_life; ?><br>
											<a href="javascript:;" onclick="update_reading(<?php echo $recorditem->eq_id; ?>);" class="btn btn-primary float-right mb-2"
						   						style="margin-right: 10px;">Update Reading</a>
										</td>
										<!--<td><?php //echo date('d-m-Y h:i A',strtotime($recorditem->cc_createdate));
										?></td>-->
										<td>
											<?php echo $recorditem->eqm_status;?><br>
											<?php if($recorditem->eqm_status == 'Available'){?>
												<a href="javascript:;" onclick="checkout_asset(<?php echo $recorditem->eq_id; ?>);" class="btn btn-primary float-right mb-2"
						   						style="margin-right: 10px;">Check Out</a>
											<?php }else if($recorditem->eqm_status == 'In Use'){?>
												<a href="javascript:;" onclick="checkin_asset(<?php echo $recorditem->eq_id; ?>);" class="btn btn-primary float-right mb-2"
						   						style="margin-right: 10px;">Check In</a>
											<?php }else{?>
											<?php }?>
										</td>
										<?php 
										if($this->session->userdata('utype')==1 || $templateDetails->pt_e_eq<3){?>
											<td>
												<a class="btn btn-outline-warning"
												onclick="modify_record(<?php echo $recorditem->eq_id; ?>);"
												href="javascript:;" title="Edit Record"><i
															class="fa fa-edit text-primary"></i></a>
												<?php if ($recorditem->eqm_status == 1) { ?>
													<!-- <a class="btn btn-outline-warning"
													href="<?php echo base_url() . 'admincontrol/projects/lock_project_set/' . $recorditem->proj_id; ?>"
													title="Lock Record"><i class="fa fa-unlock text-dark"></i></a> -->
												<?php } else { ?>
													<!-- <a class="btn btn-outline-warning"
													href="<?php echo base_url() . 'admincontrol/projects/unlock_project_set/' . $recorditem->proj_id; ?>"
													title="Unock Record"><i class="fa fa-lock text-dark"></i></a> -->
												<?php } ?>
												<?php 
												if($this->session->userdata('utype')==1 || $templateDetails->pt_e_eq<2){?>
													<a onclick="return confirm('You are about to delete a record. This cannot be undone. Are you sure?');" href="<?php echo base_url().'admincontrol/equipments/delete_equipment/'.$recorditem->eq_id;
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

	<!-- Model to add new Equipment / Asset start -->


	<!-- Modal -->
	<div class="modal fade" id="Modal_addrecord" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
		 aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Add New Equipment</h5>
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
					<nav>
						<div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
							<a class="nav-item nav-link active" id="nav-details-tab" data-toggle="tab" href="#nav-details"
							role="tab" aria-controls="nav-details" aria-selected="true">Details</a>
							<a class="nav-item nav-link disabled" id="nav-components-tab" data-toggle="tab" href="#nav-components"
							role="tab" aria-controls="nav-components" aria-selected="false">Components</a>
							<a class="nav-item nav-link disabled" id="nav-maintenance-tab" data-toggle="tab" href="#nav-maintenance"
							role="tab" aria-controls="nav-maintenance" aria-selected="false">Maintenance</a>
						</div>
					</nav>
					<div class="tab-content" id="nav-tabContent" style="padding:10px">
						<div class="tab-pane fade show active" id="nav-details" role="tabpanel"
							aria-labelledby="nav-details-tab">
							<div class="row">
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="eq_name" class="col-sm-3 text-right control-label col-form-label">Asset Name</label>
										<div class="col-sm-9">
											<input type="text" class="form-control reset-input" name="eq_name" id="eq_name"
												placeholder="Enter Asset Name" autocomplete="off"/>
											<small class="invalid-feedback eq_name"><?php echo form_error('eq_name'); ?></small>
										</div>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="eq_desc" class="col-sm-3 text-right control-label col-form-label">Description</label>
										<div class="col-sm-9">
											<textarea class="form-control reset-input" name="eq_desc" id="eq_desc"
													placeholder="Enter Description" autocomplete="off"></textarea>
											<small class="invalid-feedback eq_desc"><?php echo form_error('eq_desc'); ?></small>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="eq_type" class="col-sm-3 text-right control-label col-form-label">Type</label>
										<div class="col-sm-9">
											<select class="form-control select2 custom-select reset-input" name="eq_type" id="eq_type" autocomplete="off">
												<option value="">---Select---</option>
												<option value="Owned">Owned</option>
												<option value="Rental">Rental</option>
											</select>
											<small class="invalid-feedback eq_type"><?php echo form_error('eq_type'); ?></small>
										</div>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="eq_tag" class="col-sm-3 text-right control-label col-form-label">Asset Tag</label>
										<div class="col-sm-9">
											<input type="text" class="form-control reset-input" name="eq_tag" id="eq_tag"
												placeholder="Enter Asset Tag" autocomplete="off"/>
											<small class="invalid-feedback eq_tag"><?php echo form_error('eq_tag'); ?></small>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="eq_pic" class="col-sm-3 text-right control-label col-form-label">Picture</label>
										<div class="col-sm-9">
											<input type="file" class="form-control reset-input" name="eq_pic" id="eq_pic" autocomplete="off"/>
											<small class="invalid-feedback eq_pic"><?php echo form_error('eq_pic'); ?></small>
										</div>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="eq_condition" class="col-sm-3 text-right control-label col-form-label">Condition</label>
										<div class="col-sm-9">
											<select class="form-control select2 custom-select reset-input" name="eq_condition" id="eq_condition" autocomplete="off">
												<option value="">---Select---</option>
												<option value="New">New</option>
												<option value="Used">Used</option>
												<option value="To Be Retired">To Be Retired</option>
											</select>
											<small class="invalid-feedback eq_condition"><?php echo form_error('eq_condition'); ?></small>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="eq_category" class="col-sm-3 text-right control-label col-form-label">Asset Category</label>
										<div class="col-sm-9">
											<select class="form-control select2 custom-select reset-input" name="eq_category" id="eq_category" autocomplete="off">
												<option value="">---Select---</option>
												<option value="Small Tool">Small Tool</option>
												<option value="Equipment">Equipment</option>
												<option value="Vehicles">Vehicles</option>
											</select>
											<small class="invalid-feedback eq_category"><?php echo form_error('eq_category'); ?></small>
										</div>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="eq_status" class="col-sm-3 text-right control-label col-form-label">Status</label>
										<div class="col-sm-9">
											<select class="form-control select2 custom-select reset-input" name="eq_status" id="eq_status" autocomplete="off">
												<option value="">---Select---</option>
												<option value="Available">Available</option>
												<option value="In Use">In Use</option>
												<option value="In Maintenance">In Maintenance</option>
												<option value="Retired">Retired</option>
											</select>
											<small class="invalid-feedback eq_status"><?php echo form_error('eq_status'); ?></small>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="eq_existing_reading" class="col-sm-3 text-right control-label col-form-label">Existing Reading</label>
										<div class="col-sm-9">
											<input type="number" class="form-control reset-input" name="eq_existing_reading" id="eq_existing_reading"
												autocomplete="off" step=".01"/>
											<small class="invalid-feedback eq_existing_reading"><?php echo form_error('eq_existing_reading'); ?></small>
										</div>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="eq_estimate_usage" class="col-sm-3 text-right control-label col-form-label">Esitimate Usage</label>
										<div class="col-sm-9">
											<input type="number" class="form-control reset-input" name="eq_estimate_usage" id="eq_estimate_usage"
												autocomplete="off" step=".01"/>
											<small class="invalid-feedback eq_estimate_usage"><?php echo form_error('eq_estimate_usage'); ?></small>
										</div>
									</div>
								</div>
							</div>
							<div class="row vehicle" style="display:none">
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="eq_current_operator" class="col-sm-3 text-right control-label col-form-label">Current Operator</label>
										<div class="col-sm-9">
											<select class="form-control select2 custom-select reset-input" name="eq_current_operator" id="eq_current_operator" data-live-search="true" autocomplete="off">
												<option value="">---Select---</option>
												<?php foreach($users as $user){?>
													<option value="<?php echo $user->u_id?>"><?php echo $user->firstname.' '.$user->lastname;?></option>
												<?php }?>
											</select>
											<small class="invalid-feedback eq_current_operator"><?php echo form_error('eq_current_operator'); ?></small>
										</div>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="eq_license_plate" class="col-sm-3 text-right control-label col-form-label">License Plate</label>
										<div class="col-sm-9">
											<input type="text" class="form-control reset-input" name="eq_license_plate" id="eq_license_plate"
												placeholder="Enter License Plate No" autocomplete="off"/>
											<small class="invalid-feedback eq_license_plate"><?php echo form_error('eq_license_plate'); ?></small>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="eq_location" class="col-sm-3 text-right control-label col-form-label">Location</label>
										<div class="col-sm-9">
											<select class="form-control select2 custom-select reset-input" name="eq_location" id="eq_location" data-live-search="true" autocomplete="off">
												<option value="">---Select---</option>
												<?php foreach($projects as $project){?>
													<option value="<?php echo $project->proj_id?>"><?php echo $project->proj_name?></option>
												<?php }?>
											</select>
											<small class="invalid-feedback eq_location"><?php echo form_error('eq_location'); ?></small>
										</div>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="eq_supplier" class="col-sm-3 text-right control-label col-form-label">Supplier</label>
										<div class="col-sm-9">
											<select class="form-control select2 custom-select reset-input" name="eq_supplier" id="eq_supplier" data-live-search="true" autocomplete="off">
												<option value="">---Select---</option>
												<?php foreach ($suppliers as $supplier) { ?>
													<option value="<?php echo $supplier->sup_id; ?>"><?php echo $supplier->sup_name; ?></option>
												<?php } ?>
											</select>
											<small class="invalid-feedback eq_supplier"><?php echo form_error('eq_supplier'); ?></small>
										</div>
									</div>
								</div>
							</div>
							<div class="row not-rental">
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="eq_purchase_price" class="col-sm-3 text-right control-label col-form-label">Purchase Price</label>
										<div class="col-sm-9">
											<input type="number" class="form-control reset-input" name="eq_purchase_price" id="eq_purchase_price"
												autocomplete="off" step=".01"/>
											<small class="invalid-feedback eq_purchase_price"><?php echo form_error('eq_purchase_price'); ?></small>
										</div>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="eq_purchase_date" class="col-sm-3 text-right control-label col-form-label">Purchase Date</label>
										<div class="col-sm-9">
											<input type="date" class="form-control reset-input" name="eq_purchase_date" id="eq_purchase_date"
												autocomplete="off"/>
											<small class="invalid-feedback eq_purchase_date"><?php echo form_error('eq_purchase_date'); ?></small>
										</div>
									</div>
								</div>
							</div>
							<div class="row not-rental">
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="eq_current_value" class="col-sm-3 text-right control-label col-form-label">Current Value</label>
										<div class="col-sm-9">
											<input type="number" class="form-control reset-input" name="eq_current_value" id="eq_current_value"
												autocomplete="off" step=".01"/>
											<small class="invalid-feedback eq_current_value"><?php echo form_error('eq_current_value'); ?></small>
										</div>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="eq_brand" class="col-sm-3 text-right control-label col-form-label">Brand</label>
										<div class="col-sm-9">
											<input type="text" class="form-control reset-input" name="eq_brand" id="eq_brand"
												placeholder="Enter Brand" autocomplete="off"/>
											<small class="invalid-feedback eq_brand"><?php echo form_error('eq_brand'); ?></small>
										</div>
									</div>
								</div>
							</div>
							<div class="row not-rental">
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="eq_model" class="col-sm-3 text-right control-label col-form-label">Model</label>
										<div class="col-sm-9">
											<input type="text" class="form-control reset-input" name="eq_model" id="eq_model"
												placeholder="Enter Model" autocomplete="off"/>
											<small class="invalid-feedback eq_model"><?php echo form_error('eq_model'); ?></small>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="eq_serial" class="col-sm-3 text-right control-label col-form-label">Serial No</label>
										<div class="col-sm-9">
											<input type="text" class="form-control reset-input" name="eq_serial" id="eq_serial"
												placeholder="Enter Serial No" autocomplete="off"/>
											<small class="invalid-feedback eq_serial"><?php echo form_error('eq_serial'); ?></small>
										</div>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="eq_year" class="col-sm-3 text-right control-label col-form-label">Year</label>
										<div class="col-sm-9">
											<select class="form-control select2 custom-select reset-input" name="eq_year" id="eq_year" data-live-search="true" autocomplete="off">
												<option value="">---Select---</option>
												<?php for($i=1960;$i<=date("Y");$i++){?>
													<option value="<?php echo $i;?>"><?php echo $i;?></option>
												<?php }?>
											</select>
											<small class="invalid-feedback eq_year"><?php echo form_error('eq_year'); ?></small>
										</div>
									</div>
								</div>
							</div>
							<div class="row not-rental">
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="eq_war_expiry_date" class="col-sm-3 text-right control-label col-form-label">Warranty Expiry Date</label>
										<div class="col-sm-9">
											<input type="date" class="form-control reset-input" name="eq_war_expiry_date" id="eq_war_expiry_date"
												autocomplete="off"/>
											<small class="invalid-feedback eq_war_expiry_date"><?php echo form_error('eq_war_expiry_date'); ?></small>
										</div>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="eq_dep_method" class="col-sm-3 text-right control-label col-form-label">Depreciation Method</label>
										<div class="col-sm-9">
											<select class="form-control select2 custom-select reset-input" name="eq_dep_method" id="eq_dep_method" autocomplete="off">
												<option value="">---Select---</option>
												<option value="Straight line">Straight line</option>
												<option value="Double Decline">Double Decline</option>
												<option value="CRA">CRA</option>
											</select>
											<small class="invalid-feedback eq_dep_method"><?php echo form_error('eq_dep_method'); ?></small>
										</div>
									</div>
								</div>
							</div>
							<div class="row rental" style="display:none">
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="eq_rental_total_value" class="col-sm-3 text-right control-label col-form-label">Total Value</label>
										<div class="col-sm-9">
											<input type="number" class="form-control reset-input" name="eq_rental_total_value" id="eq_rental_total_value"
												autocomplete="off" step="0.01"/>
											<small class="invalid-feedback eq_rental_total_value"><?php echo form_error('eq_rental_total_value'); ?></small>
										</div>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="eq_rental_insurance" class="col-sm-3 text-right control-label col-form-label">Vendor Provided Insurance</label>
										<div class="col-sm-9">
											<select class="form-control select2 custom-select reset-input" name="eq_rental_insurance" id="eq_rental_insurance" autocomplete="off">
												<option value="">---Select---</option>
												<option value="YES">YES</option>
												<option value="NO">NO</option>
											</select>
											<small class="invalid-feedback eq_rental_insurance"><?php echo form_error('eq_rental_insurance'); ?></small>
										</div>
									</div>
								</div>
							</div>
							<div class="row rental-insurance" style="display:none">
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="eq_rental_insurance_amt" class="col-sm-3 text-right control-label col-form-label">Insured Amount</label>
										<div class="col-sm-9">
											<input type="number" class="form-control reset-input" name="eq_rental_insurance_amt" id="eq_rental_insurance_amt"
												autocomplete="off" step="0.01"/>
											<small class="invalid-feedback eq_rental_insurance_amt"><?php echo form_error('eq_rental_insurance_amt'); ?></small>
										</div>
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
							<div class="border-top" style="padding:20px 0;text-align:right">
								<button type="button" onclick="gotoclclickbutton();" class="btn btn-primary">Submit</button>
							</div>
							
						</div>
						<div class="tab-pane fade" id="nav-components" role="tabpanel"
							aria-labelledby="nav-components-tab">
							
						</div>
						<div class="tab-pane fade" id="nav-maintenance" role="tabpanel"
							aria-labelledby="nav-maintenance-tab">
							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Model to add new Equipment / Asset start end-->

	<!-- Model to edit Equipment / Asset details, add or edit components & maintenance details start -->

	<!-- Modal -->
	<div class="modal fade" id="Modal_editrecord" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2"
		 aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Update Equipment Details</h5>
					<button type="button" class="close close_modal2" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
				
				<!-- Navigation tabs -->
				<nav>
					<div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
						<a class="nav-item nav-link active" id="update-nav-details-tab" data-toggle="tab" href="#update-nav-details"
						role="tab" aria-controls="update-nav-details" aria-selected="true">Details</a>
						<a class="nav-item nav-link" id="update-nav-components-tab" data-toggle="tab" href="#update-nav-components"
						role="tab" aria-controls="update-nav-components" aria-selected="false">Components</a>
						<a class="nav-item nav-link" id="update-nav-maintenance-tab" data-toggle="tab" href="#update-nav-maintenance"
						role="tab" aria-controls="update-nav-maintenance" aria-selected="false">Maintenance</a>
						<a class="nav-item nav-link" id="update-nav-history-tab" data-toggle="tab" href="#update-nav-history"
						role="tab" aria-controls="update-nav-history" aria-selected="false">History</a>
					</div>
				</nav>
				<div class="tab-content" id="nav-tabContent" style="padding:10px">
					
					<!-- tab content for details update -->
					<div class="tab-pane fade show active" id="update-nav-details" role="tabpanel"
						aria-labelledby="update-nav-details-tab">
						<div class="row">
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="update_eq_name" class="col-sm-3 text-right control-label col-form-label">Asset Name</label>
										<div class="col-sm-9">
											<input type="text" class="form-control" name="update_eq_name" id="update_eq_name"
												placeholder="Enter Asset Name" autocomplete="off"/>
											<small class="invalid-feedback update_eq_name"><?php echo form_error('update_eq_name'); ?></small>
										</div>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="update_eq_desc" class="col-sm-3 text-right control-label col-form-label">Description</label>
										<div class="col-sm-9">
											<textarea class="form-control" name="update_eq_desc" id="update_eq_desc"
													placeholder="Enter Description" autocomplete="off"></textarea>
											<small class="invalid-feedback update_eq_desc"><?php echo form_error('update_eq_desc'); ?></small>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="update_eq_type" class="col-sm-3 text-right control-label col-form-label">Type</label>
										<div class="col-sm-9">
											<select class="form-control select2 custom-select" name="update_eq_type" id="update_eq_type" autocomplete="off">
												<option value="">---Select---</option>
												<option value="Owned">Owned</option>
												<option value="Rental">Rental</option>
											</select>
											<small class="invalid-feedback update_eq_type"><?php echo form_error('update_eq_type'); ?></small>
										</div>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="update_eq_tag" class="col-sm-3 text-right control-label col-form-label">Asset Tag</label>
										<div class="col-sm-9">
											<input type="text" class="form-control" name="update_eq_tag" id="update_eq_tag"
												placeholder="Enter Asset Tag" autocomplete="off" readonly/>
											<small class="invalid-feedback update_eq_tag"><?php echo form_error('update_eq_tag'); ?></small>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="update_eq_pic" class="col-sm-3 text-right control-label col-form-label">Picture</label>
										<div class="col-sm-9">
											<input type="file" class="form-control" name="update_eq_pic" id="update_eq_pic" autocomplete="off"/>
											<small class="invalid-feedback update_eq_pic"><?php echo form_error('update_eq_pic'); ?></small>
										</div>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="update_eq_condition" class="col-sm-3 text-right control-label col-form-label">Condition</label>
										<div class="col-sm-9">
											<select class="form-control select2 custom-select" name="update_eq_condition" id="update_eq_condition" autocomplete="off">
												<option value="">---Select---</option>
												<option value="New">New</option>
												<option value="Used">Used</option>
												<option value="To Be Retired">To Be Retired</option>
											</select>
											<small class="invalid-feedback update_eq_condition"><?php echo form_error('update_eq_condition'); ?></small>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="update_eq_category" class="col-sm-3 text-right control-label col-form-label">Asset Category</label>
										<div class="col-sm-9">
											<select class="form-control select2 custom-select" name="update_eq_category" id="update_eq_category" autocomplete="off">
												<option value="">---Select---</option>
												<option value="Small Tool">Small Tool</option>
												<option value="Equipment">Equipment</option>
												<option value="Vehicles">Vehicles</option>
											</select>
											<small class="invalid-feedback update_eq_category"><?php echo form_error('update_eq_category'); ?></small>
										</div>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="update_eq_status" class="col-sm-3 text-right control-label col-form-label">Status</label>
										<div class="col-sm-9">
											<select class="form-control select2 custom-select" name="update_eq_status" id="update_eq_status" autocomplete="off">
												<option value="">---Select---</option>
												<option value="Available">Available</option>
												<option value="In Use">In Use</option>
												<option value="In Maintenance">In Maintenance</option>
												<option value="Retired">Retired</option>
											</select>
											<small class="invalid-feedback update_eq_status"><?php echo form_error('update_eq_status'); ?></small>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="update_eq_existing_reading" class="col-sm-3 text-right control-label col-form-label">Existing Reading</label>
										<div class="col-sm-9">
											<input type="number" class="form-control" name="update_eq_existing_reading" id="update_eq_existing_reading"
												autocomplete="off" step=".01" readonly/>
											<small class="invalid-feedback update_eq_existing_reading"><?php echo form_error('update_eq_existing_reading'); ?></small>
										</div>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="update_eq_estimate_usage" class="col-sm-3 text-right control-label col-form-label">Esitimate Usage</label>
										<div class="col-sm-9">
											<input type="number" class="form-control" name="update_eq_estimate_usage" id="update_eq_estimate_usage"
												autocomplete="off" step=".01"/>
											<small class="invalid-feedback update_eq_estimate_usage"><?php echo form_error('update_eq_estimate_usage'); ?></small>
										</div>
									</div>
								</div>
							</div>
							<div class="row update_vehicle" style="display:none">
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="update_eq_current_operator" class="col-sm-3 text-right control-label col-form-label">Current Operator</label>
										<div class="col-sm-9">
											<select class="form-control select2 custom-select" name="update_eq_current_operator" id="update_eq_current_operator" data-live-search="true" autocomplete="off">
												<option value="">---Select---</option>
												<?php foreach($users as $user){?>
													<option value="<?php echo $user->u_id?>"><?php echo $user->firstname.' '.$user->lastname;?></option>
												<?php }?>
											</select>
											<small class="invalid-feedback update_eq_current_operator"><?php echo form_error('update_eq_current_operator'); ?></small>
										</div>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="update_eq_license_plate" class="col-sm-3 text-right control-label col-form-label">License Plate</label>
										<div class="col-sm-9">
											<input type="text" class="form-control" name="update_eq_license_plate" id="update_eq_license_plate"
												placeholder="Enter License Plate No" autocomplete="off"/>
											<small class="invalid-feedback update_eq_license_plate"><?php echo form_error('update_eq_license_plate'); ?></small>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="update_eq_location" class="col-sm-3 text-right control-label col-form-label">Location</label>
										<div class="col-sm-9">
											<select class="form-control select2 custom-select" name="update_eq_location" id="update_eq_location" data-live-search="true" autocomplete="off">
												<option value="">---Select---</option>
												<?php foreach($projects as $project){?>
													<option value="<?php echo $project->proj_id?>"><?php echo $project->proj_name?></option>
												<?php }?>
											</select>
											<small class="invalid-feedback update_eq_location"><?php echo form_error('update_eq_location'); ?></small>
										</div>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="update_eq_supplier" class="col-sm-3 text-right control-label col-form-label">Supplier</label>
										<div class="col-sm-9">
											<select class="form-control select2 custom-select" name="update_eq_supplier" id="update_eq_supplier" data-live-search="true" autocomplete="off">
												<option value="">---Select---</option>
												<?php foreach ($suppliers as $supplier) { ?>
													<option value="<?php echo $supplier->sup_id; ?>"><?php echo $supplier->sup_name; ?></option>
												<?php } ?>
											</select>
											<small class="invalid-feedback update_eq_supplier"><?php echo form_error('update_eq_supplier'); ?></small>
										</div>
									</div>
								</div>
							</div>
							<div class="row update_not-rental">
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="update_eq_purchase_price" class="col-sm-3 text-right control-label col-form-label">Purchase Price</label>
										<div class="col-sm-9">
											<input type="number" class="form-control" name="update_eq_purchase_price" id="update_eq_purchase_price"
												autocomplete="off" step=".01"/>
											<small class="invalid-feedback update_eq_purchase_price"><?php echo form_error('update_eq_purchase_price'); ?></small>
										</div>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="update_eq_purchase_date" class="col-sm-3 text-right control-label col-form-label">Purchase Date</label>
										<div class="col-sm-9">
											<input type="date" class="form-control" name="update_eq_purchase_date" id="update_eq_purchase_date"
												autocomplete="off"/>
											<small class="invalid-feedback update_eq_purchase_date"><?php echo form_error('update_eq_purchase_date'); ?></small>
										</div>
									</div>
								</div>
							</div>
							<div class="row update_not-rental">
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="update_eq_current_value" class="col-sm-3 text-right control-label col-form-label">Current Value</label>
										<div class="col-sm-9">
											<input type="number" class="form-control" name="update_eq_current_value" id="update_eq_current_value"
												autocomplete="off" step=".01"/>
											<small class="invalid-feedback update_eq_current_value"><?php echo form_error('update_eq_current_value'); ?></small>
										</div>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="update_eq_brand" class="col-sm-3 text-right control-label col-form-label">Brand</label>
										<div class="col-sm-9">
											<input type="text" class="form-control" name="update_eq_brand" id="update_eq_brand"
												placeholder="Enter Brand" autocomplete="off"/>
											<small class="invalid-feedback update_eq_brand"><?php echo form_error('update_eq_brand'); ?></small>
										</div>
									</div>
								</div>
							</div>
							<div class="row update_not-rental">
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="update_eq_model" class="col-sm-3 text-right control-label col-form-label">Model</label>
										<div class="col-sm-9">
											<input type="text" class="form-control" name="update_eq_model" id="update_eq_model"
												placeholder="Enter Model" autocomplete="off"/>
											<small class="invalid-feedback update_eq_model"><?php echo form_error('update_eq_model'); ?></small>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="update_eq_serial" class="col-sm-3 text-right control-label col-form-label">Serial No</label>
										<div class="col-sm-9">
											<input type="text" class="form-control" name="update_eq_serial" id="update_eq_serial"
												placeholder="Enter Serial No" autocomplete="off"/>
											<small class="invalid-feedback update_eq_serial"><?php echo form_error('update_eq_serial'); ?></small>
										</div>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="update_eq_year" class="col-sm-3 text-right control-label col-form-label">Year</label>
										<div class="col-sm-9">
											<select class="form-control select2 custom-select" name="update_eq_year" id="update_eq_year" data-live-search="true" autocomplete="off">
												<option value="">---Select---</option>
												<?php for($i=1960;$i<=date("Y");$i++){?>
													<option value="<?php echo $i;?>"><?php echo $i;?></option>
												<?php }?>
											</select>
											<small class="invalid-feedback update_eq_year"><?php echo form_error('update_eq_year'); ?></small>
										</div>
									</div>
								</div>
							</div>
							<div class="row update_not-rental">
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="update_eq_war_expiry_date" class="col-sm-3 text-right control-label col-form-label">Warranty Expiry Date</label>
										<div class="col-sm-9">
											<input type="date" class="form-control" name="update_eq_war_expiry_date" id="update_eq_war_expiry_date"
												autocomplete="off"/>
											<small class="invalid-feedback update_eq_war_expiry_date"><?php echo form_error('update_eq_war_expiry_date'); ?></small>
										</div>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="update_eq_dep_method" class="col-sm-3 text-right control-label col-form-label">Depreciation Method</label>
										<div class="col-sm-9">
											<select class="form-control select2 custom-select" name="update_eq_dep_method" id="update_eq_dep_method" autocomplete="off">
												<option value="">---Select---</option>
												<option value="Straight line">Straight line</option>
												<option value="Double Decline">Double Decline</option>
												<option value="CRA">CRA</option>
											</select>
											<small class="invalid-feedback update_eq_dep_method"><?php echo form_error('update_eq_dep_method'); ?></small>
										</div>
									</div>
								</div>
							</div>
							<div class="row update_rental" style="display:none">
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="update_eq_rental_total_value" class="col-sm-3 text-right control-label col-form-label">Total Value</label>
										<div class="col-sm-9">
											<input type="number" class="form-control" name="update_eq_rental_total_value" id="update_eq_rental_total_value"
												autocomplete="off" step="0.01"/>
											<small class="invalid-feedback update_eq_rental_total_value"><?php echo form_error('update_eq_rental_total_value'); ?></small>
										</div>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="update_eq_rental_insurance" class="col-sm-3 text-right control-label col-form-label">Vendor Provided Insurance</label>
										<div class="col-sm-9">
											<select class="form-control select2 custom-select" name="update_eq_rental_insurance" id="update_eq_rental_insurance" autocomplete="off">
												<option value="">---Select---</option>
												<option value="YES">YES</option>
												<option value="NO">NO</option>
											</select>
											<small class="invalid-feedback update_eq_rental_insurance"><?php echo form_error('update_eq_rental_insurance'); ?></small>
										</div>
									</div>
								</div>
							</div>
							<div class="row update_rental-insurance" style="display:none">
								<div class="col-12 col-md-6">
									<div class="form-group row">
										<label for="update_eq_rental_insurance_amt" class="col-sm-3 text-right control-label col-form-label">Insured Amount</label>
										<div class="col-sm-9">
											<input type="number" class="form-control" name="update_eq_rental_insurance_amt" id="update_eq_rental_insurance_amt"
												autocomplete="off" step="0.01"/>
											<small class="invalid-feedback update_eq_rental_insurance_amt"><?php echo form_error('update_eq_rental_insurance_amt'); ?></small>
										</div>
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
						<div class="border-top" style="padding:20px 0;text-align:right">
							<button type="button" onclick="gotoclupdateclickbutton();" class="btn btn-primary">Update Details</button>
						</div>

				</div>

				<!-- Conponents add and update  -->
				<div class="tab-pane fade" id="update-nav-components" role="tabpanel"
					aria-labelledby="update-nav-components-tab">
					<a href="javascript:;" onclick="goto_add_component();" class="btn btn-primary float-right mb-2"
						   style="margin-right: 10px;">Add New Component</a>
					<div class="table-responsive">
						<table id="component_table" class="table table-striped table-bordered">
							<thead>
								<tr style="font-weight: bold;">
									<th>Sl No.</th>
									<th>Name</th>
									<th>Value</th>
									<th>Expected Life</th>
									<th>Condition</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								<tr id="row1">
									<input type="hidden" class="hidden-component-id" id="hidden-component-id1">
									<td class="row-index">1</td>
									<td>
										<input type="text" class="form-control eq_component_name required-component" id="eq_component_name1" name="eq_component_name" placeholder="Enter Name">
										<small class="invalid-feedback eq_component_name1"><?php echo form_error('eq_component_name1'); ?></small>
									</td>
									<td>
										<input type="number" class="form-control eq_component_value required-component" id="eq_component_value1" name="eq_component_value" placeholder="Enter Value">
										<small class="invalid-feedback eq_component_value1"><?php echo form_error('eq_component_value1'); ?></small>
									</td>
									<td>
										<input type="date" class="form-control eq_component_expected_life required-component" id="eq_component_expected_life1" name="eq_component_expected_life">
										<small class="invalid-feedback eq_component_expected_life1"><?php echo form_error('eq_component_expected_life1'); ?></small>
									</td>
									<td>
									<select class="form-control select2 custom-select eq_component_condition required-component" name="eq_component_condition" id="eq_component_condition1" autocomplete="off" style="width:100%">
										<option value="">---Select---</option>
										<option value="New">New</option>
										<option value="Used">Used</option>
										<option value="To Be Retired">To Be Retired</option>
									</select>
									<div>
										<small class="invalid-feedback eq_component_condition1"><?php echo form_error('eq_component_condition1'); ?></small>
									</div>
									</td>
									<td>
									<!-- <a id="removeComponent" href="javascript:void(0);" title="Delete Record"><i class="fa fa-trash text-danger"></i></a> -->
									</td>
								</tr>
							</tbody>
						</table>
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
						<button type="button" onclick="updateComponents();" class="btn btn-primary">Update Components</button>
					</div>
					
				</div>

				<!-- Maintenance add and update for equipment -->
				<div class="tab-pane fade" id="update-nav-maintenance" role="tabpanel"
					aria-labelledby="update-nav-maintenance-tab">
					<div class="col-12 maintenance-list">
						<button type="button" class="btn btn-primary float-right mb-2 add-new-maintenance-btn"
							style="margin-right: 10px;">Add New Miantenance</button>
						<div class="table-responsive">
							<table id="maintenance_table" class="table table-striped table-bordered">
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
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
					<div class="col-12 add-new-maintenance" style="display:none">
						<div class="row">
							<div class="col-12 col-md-6">
								<div class="form-group row">
									<label for="eqm_supplier" class="col-sm-3 text-right control-label col-form-label">Supplier</label>
									<div class="col-sm-9">
										<select class="form-control select2 custom-select" name="eqm_supplier" id="eqm_supplier" data-live-search="true" autocomplete="off">
											<option value="">---Select---</option>
											<?php foreach($suppliers as $supplier){?>
												<option value="<?php echo $supplier->sup_id?>"><?php echo $supplier->sup_name?></option>
											<?php }?>
										</select>
										<small class="invalid-feedback eqm_supplier"><?php echo form_error('eqm_supplier'); ?></small>
									</div>
								</div>
							</div>
							<div class="col-12 col-md-6">
								<div class="form-group row">
									<label for="eqm_service_date" class="col-sm-3 text-right control-label col-form-label">Service Date</label>
									<div class="col-sm-9">
										<input type="date" class="form-control" name="eqm_service_date" id="eqm_service_date" autocomplete="off"/>
										<small class="invalid-feedback eqm_service_date"><?php echo form_error('eqm_service_date'); ?></small>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-12 col-md-6">
								<div class="form-group row">
									<label for="eqm_service_type" class="col-sm-3 text-right control-label col-form-label">Service Type</label>
									<div class="col-sm-9">
										<select class="form-control select2 custom-select" name="eqm_service_type" id="eqm_service_type" autocomplete="off">
											<option value="">---Select---</option>
											<option value="1">Scheduled Maintenance</option>
											<option value="2">Adhoc Maintenance</option>
										</select>
										<small class="invalid-feedback eqm_service_type"><?php echo form_error('eqm_service_type'); ?></small>
									</div>
								</div>
							</div>
							<div class="col-12 col-md-6">
								<div class="form-group row">
									<label for="eqm_notes" class="col-sm-3 text-right control-label col-form-label">Notes</label>
									<div class="col-sm-9">
										<textarea class="form-control" name="eqm_notes" id="eqm_notes" autocomplete="off"></textarea>
										<small class="invalid-feedback eqm_notes"><?php echo form_error('eqm_notes'); ?></small>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-12 col-md-6">
								<div class="form-group row">
									<label for="eqm_attachment" class="col-sm-3 text-right control-label col-form-label">Attachment</label>
									<div class="col-sm-9">
										<input type="file" class="form-control" name="eqm_attachment" id="eqm_attachment" autocomplete="off">
										<small class="invalid-feedback eqm_attachment"><?php echo form_error('eqm_attachment'); ?></small>
									</div>
								</div>
							</div>
							<div class="col-12 col-md-6">
								<div class="form-group row">
									<label for="eqm_total" class="col-sm-3 text-right control-label col-form-label">Maintenance Total</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" name="eqm_total" id="eqm_total" autocomplete="off" readonly placeholder="00.00"/>
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
												<input type="hidden" class="hidden-details-id" id="hidden-details-id1">
												<td class="row-index">1</td>
												<td>
													<textarea class="form-control eqmd_description required-eqmd" id="eqmd_description1" name="eqmd_description" placeholder="Enter Description"></textarea>
													<small class="invalid-feedback eqmd_description1"><?php echo form_error('eqmd_description1'); ?></small>
												</td>
												<td>
													<textarea class="form-control eqmd_notes required-eqmd" id="eqmd_notes1" name="eqmd_notes" placeholder="Enter Notes"></textarea>
													<small class="invalid-feedback eqmd_notes1"><?php echo form_error('eqmd_notes1'); ?></small>
												</td>
												<td>
														<?php foreach($taxcodes as $taxcode){?>
															<input type="hidden" id="eqmd_taxpercentage1<?php echo $taxcode->name?>" value="<?php echo $taxcode->percentage;?>">
														<?php }?>
													<select class="form-control select2 custom-select eqmd_taxcode required-eqmd" name="eqmd_taxcode" id="eqmd_taxcode1" data-live-search="true" autocomplete="off" style="width:100%">
														<option value="">---Select---</option>
														<?php foreach($taxcodes as $taxcode){?>
															<option value="<?php echo $taxcode->name?>"><?php echo $taxcode->description;?></option>
														<?php }?>
													</select><br>
													<small class="invalid-feedback eqmd_taxcode1"><?php echo form_error('eqmd_taxcode1'); ?></small>
												</td>
												<td>
													<input type="number" class="form-control eqmd_qty required-eqmd" id="eqmd_qty1" name="eqmd_qty" step="0.01">
													<small class="invalid-feedback eqmd_qty1"><?php echo form_error('eqmd_qty1'); ?></small>
												</td>
												<td>
													<input type="number" class="form-control eqmd_rate required-eqmd" id="eqmd_rate1" name="eqmd_rate" step="0.01">
													<small class="invalid-feedback eqmd_rate1"><?php echo form_error('eqmd_rate1'); ?></small>
												</td>
												<td>
													<input type="number" class="form-control eqmd_pre_tax_amt required-eqmd" id="eqmd_pre_tax_amt1" name="eqmd_pre_tax_amt" step="0.01" readonly>
													<small class="invalid-feedback eqmd_pre_tax_amt1"><?php echo form_error('eqmd_pre_tax_amt1'); ?></small>
												</td>
												<td>
													<input type="number" class="form-control eqmd_tax_amt required-eqmd" id="eqmd_tax_amt1" name="eqmd_tax_amt" step="0.01" readonly>
													<small class="invalid-feedback eqmd_tax_amt1"><?php echo form_error('eqmd_tax_amt1'); ?></small>
												</td>
												<td>
													<!-- <a id="removeMItem" class="removeMItem" href="javascript:void(0);" title="Delete Record"><i class="fa fa-trash text-danger"></i></a> -->
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
							<button type="button" onclick="gotomaintenanceclickbutton();" class="btn btn-primary">Submit</button>
							<button type="button" class="btn btn-danger cancel-new-maintenance">Cancel</button>
						</div>
					</div>
					<div class="col-12 edit-maintenance" style="display:none">
						<input type="hidden" name="hidden-eqm-id" id="hidden-eqm-id">
						<div class="row">
							<div class="col-12 col-md-6">
								<div class="form-group row">
									<label for="update_eqm_supplier" class="col-sm-3 text-right control-label col-form-label">Supplier</label>
									<div class="col-sm-9">
										<select class="form-control select2 custom-select" name="update_eqm_supplier" id="update_eqm_supplier" autocomplete="off">
											<option value="">---Select---</option>
											<?php foreach($suppliers as $supplier){?>
												<option value="<?php echo $supplier->sup_id?>"><?php echo $supplier->sup_name?></option>
											<?php }?>
										</select>
										<small class="invalid-feedback update_eqm_supplier"><?php echo form_error('update_eqm_supplier'); ?></small>
									</div>
								</div>
							</div>
							<div class="col-12 col-md-6">
								<div class="form-group row">
									<label for="update_eqm_service_date" class="col-sm-3 text-right control-label col-form-label">Service Date</label>
									<div class="col-sm-9">
										<input type="date" class="form-control" name="update_eqm_service_date" id="update_eqm_service_date" autocomplete="off"/>
										<small class="invalid-feedback update_eqm_service_date"><?php echo form_error('update_eqm_service_date'); ?></small>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
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
							<div class="col-12 col-md-6">
								<div class="form-group row">
									<label for="update_eqm_notes" class="col-sm-3 text-right control-label col-form-label">Notes</label>
									<div class="col-sm-9">
										<textarea class="form-control" name="update_eqm_notes" id="update_eqm_notes" autocomplete="off"></textarea>
										<small class="invalid-feedback update_eqm_notes"><?php echo form_error('update_eqm_notes'); ?></small>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-12 col-md-6">
								<div class="form-group row">
									<label for="update_eqm_attachment" class="col-sm-3 text-right control-label col-form-label">Attachment</label>
									<div class="col-sm-9">
										<input type="file" class="form-control" name="update_eqm_attachment" id="update_eqm_attachment" autocomplete="off">
										<small class="invalid-feedback update_eqm_attachment"><?php echo form_error('update_eqm_attachment'); ?></small>
									</div>
								</div>
							</div>
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
												<input type="hidden" class="hidden-eqmd-id" id="hidden-eqmd-id1">
												<td class="row-index">1</td>
												<td>
													<textarea class="form-control update_eqmd_description update-required-eqmd" id="update_eqmd_description1" name="update_eqmd_description" placeholder="Enter Description"></textarea>
													<small class="invalid-feedback update_eqmd_description1"><?php echo form_error('eqmdeqmd_description1'); ?></small>
												</td>
												<td>
													<textarea class="form-control update_eqmd_notes update-required-eqmd" id="update_eqmd_notes1" name="update_eqmd_notes" placeholder="Enter Notes"></textarea>
													<small class="invalid-feedback update_eqmd_notes1"><?php echo form_error('update_eqmd_notes1'); ?></small>
												</td>
												<td>
														<?php foreach($taxcodes as $taxcode){?>
															<input type="hidden" id="update_eqmd_taxpercentage1<?php echo $taxcode->name?>" value="<?php echo $taxcode->percentage;?>">
														<?php }?>
													<select class="form-control select2 update_eqmd_taxcode update-required-eqmd" name="update_eqmd_taxcode" id="update_eqmd_taxcode1" data-container="body" data-live-search="true" autocomplete="off" style="width:100%">
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
													<a id="removeUItem" class="removeUItem" href="javascript:void(0);" title="Delete Record"><i class="fa fa-trash text-danger"></i></a>
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
							<button type="button" onclick="gotoupdatemaintenanceclickbutton();" class="btn btn-primary">Submit</button>
							<button type="button" class="btn btn-danger cancel-new-maintenance">Cancel</button>
						</div>
					</div>
				</div>

				<!-- Equipments history -->
				<div class="tab-pane fade" id="update-nav-history" role="tabpanel"
					aria-labelledby="update-nav-hsitory-tab">
					<div class="table-responsive">
						<table id="history_table" class="table table-striped table-bordered">
							<thead>
								<tr style="font-weight: bold;">
									<th>Sl No.</th>
									<th>Date</th>
									<th>Description</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Model to edit Equipment / Asset details, add or edit components & maintenance details end -->

<!-- footer load -->
<?php $this->load->view('admin/component/footer') ?>	

	<!-- Model to checkout start -->
	<!-- Modal -->
	<div class="modal fade" id="Modal_checkout" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
		 aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Check-Out Equipment</h5>
					<button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
				<div class="row">
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="checkout_asset" class="col-sm-3 text-right control-label col-form-label">Asset Name</label>
								<div class="col-sm-9">
									<select class="form-control select2 custom-select" name="checkout_asset" id="checkout_asset" data-live-search="true" autocomplete="off">
										<option value="">---Select---</option>
										<?php foreach($availableEquipments as $asset){?>
											<option value="<?php echo $asset->eq_id?>"><?php echo $asset->eqm_asset_name?></option>
										<?php }?>
									</select>
									<small class="invalid-feedback checkout_asset"><?php echo form_error('checkout_asset'); ?></small>
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="checkout_location" class="col-sm-3 text-right control-label col-form-label">Location</label>
								<div class="col-sm-9">
									<select class="form-control select2 custom-select" name="checkout_location" id="checkout_location" data-live-search="true" autocomplete="off">
										<option value="">---Select---</option>
										<?php foreach($projects as $project){?>
											<option value="<?php echo $project->proj_id?>"><?php echo $project->proj_name?></option>
										<?php }?>
									</select>
									<small class="invalid-feedback checkout_location"><?php echo form_error('checkout_location'); ?></small>
								</div>
							</div>
						</div>
					</div><div class="row">
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="checkout_operator" class="col-sm-3 text-right control-label col-form-label">Operator</label>
								<div class="col-sm-9">
									<select class="form-control select2 custom-select" name="checkout_operator" id="checkout_operator" data-live-search="true" autocomplete="off">
										<option value="">---Select---</option>
										<?php foreach($users as $user){?>
											<option value="<?php echo $user->u_id?>"><?php echo $user->firstname.' '.$user->lastname;?></option>
										<?php }?>
									</select>
									<small class="invalid-feedback checkout_operator"><?php echo form_error('checkout_operator'); ?></small>
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="checkout_date" class="col-sm-3 text-right control-label col-form-label">Checkout Date</label>
								<div class="col-sm-9">
									<input type="date" class="form-control" name="checkout_date" id="checkout_date" autocomplete="off"/>
									<small class="invalid-feedback checkout_date"><?php echo form_error('checkout_date'); ?></small>
								</div>
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
					<div class="border-top" style="padding:20px 0;text-align:right">
						<button type="button" onclick="gotoupdatecheckoutclickbutton();" class="btn btn-primary">Update</button>
						<!-- <button type="button" class="btn btn-danger cancel-new-maintenance">Cancel</button> -->
					</div>
				</div>
			</div>
		</div>
	</div>	

	<!-- Model to checkout start -->
	<!-- Modal -->
	<div class="modal fade" id="Modal_checkin" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
		aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Check-In Equipment</h5>
					<button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-12">
							<div class="form-group row">
								<label for="checkin_asset" class="col-sm-3 text-right control-label col-form-label">Asset Name</label>
								<div class="col-sm-9">
									<select class="form-control select2 custom-select" name="checkin_asset" id="checkin_asset" data-live-search="true" autocomplete="off">
										<option value="">---Select---</option>
										<?php foreach($inUseEquipments as $asset){?>
											<option value="<?php echo $asset->eq_id?>"><?php echo $asset->eqm_asset_name?></option>
										<?php }?>
									</select>
									<small class="invalid-feedback checkin_asset"><?php echo form_error('checkin_asset'); ?></small>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							<div class="form-group row">
								<label for="checkin_date" class="col-sm-3 text-right control-label col-form-label">Checkin Date</label>
								<div class="col-sm-9">
									<input type="date" class="form-control" name="checkin_date" id="checkin_date" autocomplete="off"/>
									<small class="invalid-feedback checkin_date"><?php echo form_error('checkin_date'); ?></small>
								</div>
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
					<div class="border-top" style="padding:20px 0;text-align:right">
						<button type="button" onclick="gotoupdatecheckinclickbutton();" class="btn btn-primary">Update</button>
						<!-- <button type="button" class="btn btn-danger cancel-new-maintenance">Cancel</button> -->
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Model to update reading start -->
	<!-- Modal -->
	<div class="modal fade" id="Modal_updatereading" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
		aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Update Current Reading</h5>
					<button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<input type="hidden" id="hidden-update-reading-eq-id">
					<div class="row">
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="update_reading_estimate_usage" class="col-sm-3 text-right control-label col-form-label">Estimate Usage</label>
								<div class="col-sm-9">
									<input type="number" class="form-control" name="update_reading_estimate_usage" id="update_reading_estimate_usage" autocomplete="off" step="0.01" readonly/>
									<small class="invalid-feedback update_reading_estimate_usage"><?php echo form_error('update_reading_estimate_usage'); ?></small>
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="update_reading_current_reading" class="col-sm-3 text-right control-label col-form-label">Current Reading</label>
								<div class="col-sm-9">
									<input type="number" class="form-control" name="update_reading_current_reading" id="update_reading_current_reading" autocomplete="off" step="0.01"/>
									<small class="invalid-feedback update_reading_current_reading"><?php echo form_error('update_reading_current_reading'); ?></small>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="update_reading_remaining_life" class="col-sm-3 text-right control-label col-form-label">Remaining Life</label>
								<div class="col-sm-9">
									<input type="number" class="form-control" name="update_reading_remaining_life" id="update_reading_remaining_life" autocomplete="off" readonly step="0.01"/>
									<small class="invalid-feedback update_reading_remaining_life"><?php echo form_error('update_reading_remaining_life'); ?></small>
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="update_reading_date" class="col-sm-3 text-right control-label col-form-label">Current Date</label>
								<div class="col-sm-9">
									<input type="date" class="form-control" name="update_reading_date" id="update_reading_date" autocomplete="off"/>
									<small class="invalid-feedback update_reading_date"><?php echo form_error('update_reading_date'); ?></small>
								</div>
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
					<div class="border-top" style="padding:20px 0;text-align:right">
						<button type="button" onclick="gotoupdatereadingclickbutton();" class="btn btn-primary">Update</button>
						<!-- <button type="button" class="btn btn-danger cancel-new-maintenance">Cancel</button> -->
					</div>
				</div>
			</div>
		</div>
	</div>

<!-- javascript start -->

<script type="text/javascript">

	$(function () {
		$('.alert-error, .invalid-feedback').delay(8000).fadeOut();
		$(".select2").selectpicker();
	});

	// function when page loads
	$(document).ready(function() {

		$('#same_as_company_info').on('change',function () {
			$('#bill_name_div').toggleClass('d-none');
			$('#bill_address_div').toggleClass('d-none');
		});

		$('#update_same_as_company_info').on('change',function () {
			$('#update_bill_name_div').toggleClass('d-none');
			$('#update_bill_address_div').toggleClass('d-none');
		});
	});

	// function to add equipment / asset
	function gotoclclickbutton() {
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

		var eq_name = $('#eq_name').val();
		var eq_desc = $('#eq_desc').val();
		var eq_type = $('#eq_type').val();
		var eq_tag = $('#eq_tag').val();
		var eq_picture = $('#eq_pic')[0].files;
		var eq_condition = $('#eq_condition').val();
		var eq_category = $('#eq_category').val();
		var eq_current_operator = $('#eq_current_operator').val();
		var eq_license_plate = $('#eq_license_plate').val();
		var eq_category = $('#eq_category').val();
		var eq_status = $('#eq_status').val();
		var eq_existing_reading = $('#eq_existing_reading').val();
		var eq_estimate_usage = $('#eq_estimate_usage').val();
		var eq_location = $('#eq_location').val();
		var eq_supplier = $('#eq_supplier').val();
		var eq_purchase_price = $('#eq_purchase_price').val();
		var eq_purchase_date = $('#eq_purchase_date').val();
		var eq_current_value = $('#eq_current_value').val();
		var eq_brand = $('#eq_brand').val();
		var eq_model = $('#eq_model').val();
		var eq_serial = $('#eq_serial').val();
		var eq_year = $('#eq_year').val();
		var eq_war_expiry_date = $('#eq_war_expiry_date').val();
		var eq_dep_method = $('#eq_dep_method').val();
		var eq_rental_total_value = $('#eq_rental_total_value').val();
		var eq_rental_insurance = $('#eq_rental_insurance').val();
		var eq_rental_insurance_amt = $('#eq_rental_insurance_amt').val();

		if (eq_name == "") {
			e_error = 1;
			$('.eq_name').html('Name is Required.');
		} else {
			$('.eq_name').html('');
		}

		if (eq_desc == "") {
			e_error = 1;
			$('.eq_desc').html('Description is Required.');
		} else {
			$('.eq_desc').html('');
		}

		if (eq_type == "") {
			e_error = 1;
			$('.eq_type').html('Type is Required.');
		} else {
			$('.eq_type').html('');
		}

		if (eq_tag == "") {
			e_error = 1;
			$('.eq_tag').html('Tag is Required.');
		} else {
			$('.eq_tag').html('');
		}

		// if ($('#eq_pic').val() == "") {
		// 	e_error = 1;
		// 	$('.eq_pic').html('Picture is Required.');
		// } else {
		// 	$('.eq_pic').html('');
		// }

		// if (eq_condition == "") {
		// 	e_error = 1;
		// 	$('.eq_condition').html('Condition is Required.');
		// } else {
		// 	$('.eq_condition').html('');
		// }

		// if (eq_category == "") {
		// 	e_error = 1;
		// 	$('.eq_category').html('Category is Required.');
		// } else {
		// 	$('.eq_category').html('');
		// }

		// if(eq_category=='Vehicles'){
		// 	if (eq_license_plate == "") {
		// 		e_error = 1;
		// 		$('.eq_license_plate').html('License Plate is Required.');
		// 	} else {
		// 		$('.eq_license_plate').html('');
		// 	}

		// 	if (eq_current_operator == "") {
		// 		e_error = 1;
		// 		$('.eq_current_operator').html('Cusrrent Operator is Required.');
		// 	} else {
		// 		$('.eq_current_operator').html('');
		// 	}
		// }

		// if (eq_status == "") {
		// 	e_error = 1;
		// 	$('.eq_status').html('Status is Required.');
		// } else {
		// 	$('.eq_status').html('');
		// }

		// if (eq_existing_reading == "") {
		// 	e_error = 1;
		// 	$('.eq_existing_reading').html('Existing Reading is Required.');
		// } else {
		// 	$('.eq_existing_reading').html('');
		// }

		// if (eq_estimate_usage == "") {
		// 	e_error = 1;
		// 	$('.eq_estimate_usage').html('Estimate Usage is Required.');
		// } else {
		// 	$('.eq_estimate_usage').html('');
		// }
		
		if(eq_existing_reading!='' && eq_estimate_usage!=''){
			if(parseInt(eq_existing_reading) > parseInt(eq_estimate_usage)){
				e_error = 1;
				$('.eq_existing_reading').html('Cannot be greater than Estimate Usage.');
			}else{
				$('.eq_existing_reading').html('');
			}
		}

		// if (eq_location == "") {
		// 	e_error = 1;
		// 	$('.eq_location').html('Location is Required.');
		// } else {
		// 	$('.eq_location').html('');
		// }

		// if (eq_supplier == "") {
		// 	e_error = 1;
		// 	$('.eq_supplier').html('Supplier is Required.');
		// } else {
		// 	$('.eq_supplier').html('');
		// }

		// if(eq_type!='Rental'){
		// 	if (eq_purchase_price == "") {
		// 		e_error = 1;
		// 		$('.eq_purchase_price').html('Purchase Price is Required.');
		// 	} else {
		// 		$('.eq_purchase_price').html('');
		// 	}

		// 	if (eq_purchase_date == "") {
		// 		e_error = 1;
		// 		$('.eq_purchase_date').html('Purchase Date is Required.');
		// 	} else {
		// 		$('.eq_purchase_date').html('');
		// 	}

		// 	if (eq_current_value == "") {
		// 		e_error = 1;
		// 		$('.eq_current_value').html('Current Value is Required.');
		// 	} else {
		// 		$('.eq_current_value').html('');
		// 	}

		// 	if (eq_brand == "") {
		// 		e_error = 1;
		// 		$('.eq_brand').html('Brand is Required.');
		// 	} else {
		// 		$('.eq_brand').html('');
		// 	}

		// 	if (eq_model == "") {
		// 		e_error = 1;
		// 		$('.eq_model').html('Model is Required.');
		// 	} else {
		// 		$('.eq_model').html('');
		// 	}

		// 	if (eq_war_expiry_date == "") {
		// 		e_error = 1;
		// 		$('.eq_war_expiry_date').html('Warrenty Expory Date is Required.');
		// 	} else {
		// 		$('.eq_war_expiry_date').html('');
		// 	}

		// 	if (eq_dep_method == "") {
		// 		e_error = 1;
		// 		$('.eq_dep_method').html('Depreciation Method is Required.');
		// 	} else {
		// 		$('.eq_dep_method').html('');
		// 	}
		// }else{
		// 	if (eq_rental_total_value == "") {
		// 		e_error = 1;
		// 		$('.eq_rental_total_value').html('Total Value is Required.');
		// 	} else {
		// 		$('.eq_rental_total_value').html('');
		// 	}

		// 	if (eq_rental_insurance == "") {
		// 		e_error = 1;
		// 		$('.eq_rental_insurance').html('Required.');
		// 	} else {
		// 		$('.eq_rental_insurance').html('');
		// 	}
		// }

		// if(eq_rental_insurance=='YES'){
		// 	if (eq_rental_insurance_amt == "") {
		// 		e_error = 1;
		// 		$('.eq_rental_insurance_amt').html('Insurance Amount is Required.');
		// 	} else {
		// 		$('.eq_rental_insurance_amt').html('');
		// 	}
		// }

		// if (eq_serial == "") {
		// 	e_error = 1;
		// 	$('.eq_serial').html('Serial No is Required.');
		// } else {
		// 	$('.eq_serial').html('');
		// }

		// if (eq_year == "") {
		// 	e_error = 1;
		// 	$('.eq_year').html('Year is Required.');
		// } else {
		// 	$('.eq_year').html('');
		// }

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
			form_data.append("eq_name", eq_name);
			form_data.append("eq_desc", eq_desc);
			form_data.append("eq_type", eq_type);
			form_data.append("eq_tag", eq_tag);
			form_data.append("eq_picture", eq_picture[0]);
			form_data.append("eq_condition", eq_condition);
			form_data.append("eq_category", eq_category);
			form_data.append("eq_license_plate", eq_license_plate);
			form_data.append("eq_current_operator", eq_current_operator);
			form_data.append("eq_status", eq_status);
			form_data.append("eq_existing_reading", eq_existing_reading);
			form_data.append("eq_estimate_usage", eq_estimate_usage);
			form_data.append("eq_location", eq_location);
			form_data.append("eq_supplier", eq_supplier);
			form_data.append("eq_purchase_price", eq_purchase_price);
			form_data.append("eq_purchase_date", eq_purchase_date);
			form_data.append("eq_current_value", eq_current_value);
			form_data.append("eq_brand", eq_brand);
			form_data.append("eq_model", eq_model);
			form_data.append("eq_serial", eq_serial);
			form_data.append("eq_year", eq_year);
			form_data.append("eq_war_expiry_date", eq_war_expiry_date);
			form_data.append("eq_dep_method", eq_dep_method);
			form_data.append("eq_rental_total_value", eq_rental_total_value);
			form_data.append("eq_rental_insurance", eq_rental_insurance);
			form_data.append("eq_rental_insurance_amt", eq_rental_insurance_amt);

			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/equipments/new_equipment_submission') ?>",
				dataType: 'json',
				data: form_data,
				contentType: false,
				cache: true,
				processData: false,
				success: function (data) {
					// alert(data.msg);
					if (data.msg == 1) {
						//console.log(data);
						//alert(data.msg[0].space_rate);
						$('.div_roller_total').fadeOut();
						toastr.success('Record is Inserted Successfully!', 'Success');
						setTimeout(function () {
							window.location.replace("<?php echo site_url('admincontrol/equipments/all_equipment_list') ?>");
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
					console.log(arguments);
					alert(" Can't do because: " + error);
				}
			});
		}
		//$("#myForm").submit();


	}

	// function update Equipment / Asset details
	function gotoclupdateclickbutton() {
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

		var eq_id=$('#hidden-eq-id').val();
		var eq_name = $('#update_eq_name').val();
		var eq_desc = $('#update_eq_desc').val();
		var eq_type = $('#update_eq_type').val();
		var eq_tag = $('#update_eq_tag').val();
		var eq_picture = $('#update_eq_pic')[0].files;
		var eq_condition = $('#update_eq_condition').val();
		var eq_category = $('#update_eq_category').val();
		var eq_license_plate = $('#update_eq_license_plate').val();
		var eq_current_operator = $('#update_eq_current_operator').val();
		var eq_status = $('#update_eq_status').val();
		var eq_existing_reading = $('#update_eq_existing_reading').val();
		var eq_estimate_usage = $('#update_eq_estimate_usage').val();
		var eq_location = $('#update_eq_location').val();
		var eq_supplier = $('#update_eq_supplier').val();
		var eq_purchase_price = $('#update_eq_purchase_price').val();
		var eq_purchase_date = $('#update_eq_purchase_date').val();
		var eq_current_value = $('#update_eq_current_value').val();
		var eq_brand = $('#update_eq_brand').val();
		var eq_model = $('#update_eq_model').val();
		var eq_serial = $('#update_eq_serial').val();
		var eq_year = $('#update_eq_year').val();
		var eq_war_expiry_date = $('#update_eq_war_expiry_date').val();
		var eq_dep_method = $('#update_eq_dep_method').val();
		var eq_rental_total_value = $('#update_eq_rental_total_value ').val();
		var eq_rental_insurance = $('#update_eq_rental_insurance').val();
		var eq_rental_insurance_amt = $('#update_eq_rental_insurance_amt').val();

		if (eq_name == "") {
			e_error = 1;
			$('.update_eq_name').html('Name is Required.');
		} else {
			$('.update_eq_name').html('');
		}

		if (eq_desc == "") {
			e_error = 1;
			$('.update_eq_desc').html('Description is Required.');
		} else {
			$('.update_eq_desc').html('');
		}

		if (eq_type == "") {
			e_error = 1;
			$('.update_eq_type').html('Type is Required.');
		} else {
			$('.update_eq_type').html('');
		}

		if (eq_tag == "") {
			e_error = 1;
			$('.update_eq_tag').html('Tag is Required.');
		} else {
			$('.update_eq_tag').html('');
		}

		// if (eq_picture == "") {
		// 	e_error = 1;
		// 	$('.eq_pic').html('Picture is Required.');
		// } else {
		// 	$('.eq_pic').html('');
		// }

		// if (eq_condition == "") {
		// 	e_error = 1;
		// 	$('.update_eq_condition').html('Condition is Required.');
		// } else {
		// 	$('.update_eq_condition').html('');
		// }

		// if (eq_category == "") {
		// 	e_error = 1;
		// 	$('.update_eq_category').html('Category is Required.');
		// } else {
		// 	$('.update_eq_category').html('');
		// }

		// if(eq_category=='Vehicles'){
		// 	if (eq_license_plate == "") {
		// 		e_error = 1;
		// 		$('.update_eq_license_plate').html('License Plate is Required.');
		// 	} else {
		// 		$('.update_eq_license_plate').html('');
		// 	}

		// 	if (eq_current_operator == "") {
		// 		e_error = 1;
		// 		$('.update_eq_current_operator').html('Cusrrent Operator is Required.');
		// 	} else {
		// 		$('.update_eq_current_operator').html('');
		// 	}
		// }

		// if (eq_status == "") {
		// 	e_error = 1;
		// 	$('.update_eq_status').html('Status is Required.');
		// } else {
		// 	$('.update_eq_status').html('');
		// }

		// if (eq_existing_reading == "") {
		// 	e_error = 1;
		// 	$('.update_eq_existing_reading').html('Existing Reading is Required.');
		// } else {
		// 	$('.update_eq_existing_reading').html('');
		// }

		// if (eq_estimate_usage == "") {
		// 	e_error = 1;
		// 	$('.update_eq_estimate_usage').html('Estimate Usage is Required.');
		// } else {
		// 	$('.update_eq_estimate_usage').html('');
		// }

		if(eq_existing_reading!='' && eq_estimate_usage!=''){
			if(parseInt(eq_existing_reading) > parseInt(eq_estimate_usage)){
				e_error = 1;
				$('.update_eq_existing_reading').html('Cannot be greater than Estimate Usage.');
			}else{
				$('.update_eq_existing_reading').html('');
			}
		}

		// if (eq_location == "") {
		// 	e_error = 1;
		// 	$('.update_eq_location').html('Location is Required.');
		// } else {
		// 	$('.update_eq_location').html('');
		// }

		// if (eq_supplier == "") {
		// 	e_error = 1;
		// 	$('.update_eq_supplier').html('Supplier is Required.');
		// } else {
		// 	$('.update_eq_supplier').html('');
		// }

		// if(eq_type!='Rental'){
		// 	if (eq_purchase_price == "") {
		// 		e_error = 1;
		// 		$('.update_eq_purchase_price').html('Purchase Price is Required.');
		// 	} else {
		// 		$('.update_eq_purchase_price').html('');
		// 	}

		// 	if (eq_purchase_date == "") {
		// 		e_error = 1;
		// 		$('.update_eq_purchase_date').html('Purchase Date is Required.');
		// 	} else {
		// 		$('.update_eq_purchase_date').html('');
		// 	}

		// 	if (eq_current_value == "") {
		// 		e_error = 1;
		// 		$('.update_eq_current_value').html('Current Value is Required.');
		// 	} else {
		// 		$('.update_eq_current_value').html('');
		// 	}

		// 	if (eq_brand == "") {
		// 		e_error = 1;
		// 		$('.update_eq_brand').html('Brand is Required.');
		// 	} else {
		// 		$('.update_eq_brand').html('');
		// 	}

		// 	if (eq_model == "") {
		// 		e_error = 1;
		// 		$('.update_eq_model').html('Model is Required.');
		// 	} else {
		// 		$('.update_eq_model').html('');
		// 	}

		// 	if (eq_war_expiry_date == "") {
		// 		e_error = 1;
		// 		$('.update_eq_war_expiry_date').html('Warrenty Expory Date is Required.');
		// 	} else {
		// 		$('.update_eq_war_expiry_date').html('');
		// 	}

		// 	if (eq_dep_method == "") {
		// 		e_error = 1;
		// 		$('.update_eq_dep_method').html('Depreciation Method is Required.');
		// 	} else {
		// 		$('.update_eq_dep_method').html('');
		// 	}
		// }else{
		// 	if (eq_rental_total_value == "") {
		// 		e_error = 1;
		// 		$('.update_eq_rental_total_value').html('Total Value is Required.');
		// 	} else {
		// 		$('.update_eq_rental_total_value').html('');
		// 	}

		// 	if (eq_rental_insurance == "") {
		// 		e_error = 1;
		// 		$('.update_eq_rental_insurance').html('Required.');
		// 	} else {
		// 		$('.update_eq_rental_insurance').html('');
		// 	}
		// }

		// if(eq_rental_insurance=='YES'){
		// 	if (eq_rental_insurance_amt == "") {
		// 		e_error = 1;
		// 		$('.update_eq_rental_insurance_amt').html('Insurance Amount is Required.');
		// 	} else {
		// 		$('.update_eq_rental_insurance_amt').html('');
		// 	}
		// }

		// if (eq_serial == "") {
		// 	e_error = 1;
		// 	$('.update_eq_serial').html('Serial No is Required.');
		// } else {
		// 	$('.update_eq_serial').html('');
		// }

		// if (eq_year == "") {
		// 	e_error = 1;
		// 	$('.update_eq_year').html('Year is Required.');
		// } else {
		// 	$('.update_eq_year').html('');
		// }

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
			form_data.append("eq_id", eq_id);
			form_data.append("eq_name", eq_name);
			form_data.append("eq_desc", eq_desc);
			form_data.append("eq_type", eq_type);
			form_data.append("eq_tag", eq_tag);
			// if ($('#update_eq_pic').val() == "") {
			// 	form_data.append("eq_picture", '');
			// } else {
				form_data.append("eq_picture", eq_picture[0]);
			// }
			form_data.append("eq_condition", eq_condition);
			form_data.append("eq_category", eq_category);
			form_data.append("eq_license_plate", eq_license_plate);
			form_data.append("eq_current_operator", eq_current_operator);
			form_data.append("eq_status", eq_status);
			form_data.append("eq_existing_reading", eq_existing_reading);
			form_data.append("eq_estimate_usage", eq_estimate_usage);
			form_data.append("eq_location", eq_location);
			form_data.append("eq_supplier", eq_supplier);
			form_data.append("eq_purchase_price", eq_purchase_price);
			form_data.append("eq_purchase_date", eq_purchase_date);
			form_data.append("eq_current_value", eq_current_value);
			form_data.append("eq_brand", eq_brand);
			form_data.append("eq_model", eq_model);
			form_data.append("eq_serial", eq_serial);
			form_data.append("eq_year", eq_year);
			form_data.append("eq_war_expiry_date", eq_war_expiry_date);
			form_data.append("eq_dep_method", eq_dep_method);
			form_data.append("eq_rental_total_value", eq_rental_total_value);
			form_data.append("eq_rental_insurance", eq_rental_insurance);
			form_data.append("eq_rental_insurance_amt", eq_rental_insurance_amt);

			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/equipments/update_equipment_submission') ?>",
				dataType: 'json',
				data: form_data,
				contentType: false,
				cache: true,
				processData: false,
				success: function (data) {
					// alert(data.msg);
					if (data.msg == 1) {
						//console.log(data);
						//alert(data.msg[0].space_rate);
						$('.div_roller_total').fadeOut();
						toastr.success('Record is Inserted Successfully!', 'Success');
						setTimeout(function () {
							window.location.replace("<?php echo site_url('admincontrol/equipments/all_equipment_list') ?>");
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
					console.log(arguments);
					alert(" Can't do because: " + error);
				}
			});
		}
		//$("#myForm").submit();


	}

	// Add new Equipment / Asset popup show
	function goto_add_record() {
		$('.reset-input').val('').change();
		$('#Modal_addrecord').modal('show');
	}

	var rowCount=2;

	// function to add component line items
	function goto_add_component(){
		$('#component_table tbody').append('\
		<tr id="row'+rowCount+'">\
			<input type="hidden" class="hidden-component-id" id="hidden-component-id'+rowCount+'">\
			<td class="row-index">'+rowCount+'</td>\
			<td>\
				<input type="text" class="form-control eq_component_name required-component" id="eq_component_name'+rowCount+'" name="eq_component_name" placeholder="Enter Name">\
				<small class="invalid-feedback eq_component_name'+rowCount+'"></small>\
			</td>\
			<td>\
				<input type="number" class="form-control eq_component_value required-component" id="eq_component_value'+rowCount+'" name="eq_component_value" placeholder="Enter Value">\
				<small class="invalid-feedback eq_component_value'+rowCount+'"></small>\
			</td>\
			<td>\
				<input type="date" class="form-control eq_component_expected_life required-component" id="eq_component_expected_life'+rowCount+'" name="eq_component_expected_life">\
				<small class="invalid-feedback eq_component_expected_life'+rowCount+'"></small>\
			</td>\
			<td>\
				<select class="form-control select2 custom-select eq_component_condition required-component" name="eq_component_condition" id="eq_component_condition'+rowCount+'" autocomplete="off" style="width:100%">\
					<option value="">---Select---</option>\
					<option value="New">New</option>\
					<option value="Used">Used</option>\
					<option value="To Be Retired">To Be Retired</option>\
				</select><br>\
				<small class="invalid-feedback eq_component_condition'+rowCount+'"></small>\
			</td>\
			<td>\
			<a id="removeComponent" href="javascript:void(0);" title="Delete Record"><i class="fa fa-trash text-danger"></i></a>\
			</td>\
		</tr>\
		');
		rowCount++;
	}

	var rmComponentArray= [];

	// To remove component line item
	$("#component_table tbody").on('click', '#removeComponent', function(){
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
				$(this).children('td').children('.eq_component_name').attr('id',`eq_component_name${dig-1}`);
				$(this).children('td').children('.eq_component_value').attr('id',`eq_component_value${dig-1}`);
				$(this).children('td').children('.eq_component_expected_life').attr('id',`eq_component_expected_life${dig-1}`);
				$(this).find('select').attr('id',`eq_component_condition${dig-1}`);
			});
			$(this).parent().parent().remove();
			rowCount--;
		} else {
		}
		// alert(rmComponentArray);
	});
		 
	rowCount1=2;

	// To add maintenance line items
	function goto_add_item(){
		$('#eqm_details_table tbody').append('\
		<tr id="row'+rowCount1+'">\
			<input type="hidden" class="hidden-component-id" id="hidden-component-id'+rowCount1+'">\
			<td class="row-index">'+rowCount1+'</td>\
			<td>\
				<textarea class="form-control eqmd_description required-eqmd" id="eqmd_description'+rowCount1+'" name="eqmd_description" placeholder="Enter Description"></textarea>\
				<small class="invalid-feedback eqmd_description'+rowCount1+'"></small>\
			</td>\
			<td>\
				<select class="form-control select2 custom-select eqmd_taxcode required-eqmd" name="eqmd_taxcode" id="eqmd_taxcode'+rowCount1+'" autocomplete="off" style="width:100%">\
					<option value="">---Select---</option>\
					<?php foreach($taxcodes as $taxcode){?>
						<option value="<?php echo $taxcode->name?>"><?php echo $taxcode->description;?></option>\
					<?php }?>
				</select>\
				<small class="invalid-feedback eqmd_taxcode'+rowCount1+'"></small>\
			</td>\
			<td>\
				<textarea class="form-control eqmd_notes required-eqmd" id="eqmd_notes'+rowCount1+'" name="eqmd_notes" placeholder="Enter Notes"></textarea>\
				<small class="invalid-feedback eqmd_notes'+rowCount1+'"></small>\
			</td>\
			<td>\
				<input type="number" class="form-control eqmd_pre_tax_amt required-eqmd" id="eqmd_pre_tax_amt'+rowCount1+'" name="eqmd_pre_tax_amt" placeholder="Enter Pre Tax Amt" step="0.01">\
				<small class="invalid-feedback eqmd_pre_tax_amt'+rowCount1+'"></small>\
			</td>\
			<td>\
				<input type="number" class="form-control eqmd_tax_amt required-eqmd" id="eqmd_tax_amt'+rowCount1+'" name="eqmd_tax_amt" step="0.01" readonly>\
				<small class="invalid-feedback eqmd_tax_amt'+rowCount1+'"></small>\
			</td>\
			<td>\
				<a id="removeItem" href="javascript:void(0);" title="Delete Record"><i class="fa fa-trash text-danger"></i></a>\
			</td>\
		</tr>\
		');
		rowCount1++;
	}

	var rmItemArray= [];

	// To remove maintenance line items
	$('#eqm_details_table tbody').on('click', '#removeItem', function(){
		if (confirm('Are you sure you want to delete ?')) {
			var rowId=$(this).closest('tr').attr('id');
			var index=rowId.match(/\d+/);
			if($(this).closest('tr').children('#hidden-component-id'+index).val()!=""){
				rmItemArray.push($(this).closest('tr').children('#hidden-component-id'+index).val());
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
				$(this).children('td').children('.eqmd_pre_tax_amt').attr('id',`eqmd_pre_tax_amt${dig-1}`);
				$(this).children('td').children('.eqmd_tax_amt').attr('id',`eqmd_tax_amt${dig-1}`);
			});
			$(this).parent().parent().remove();
			rowCount--;
		} else {
		}
		// alert(rmComponentArray);
	});

	// To update components
	function updateComponents(){
		var delay = 8000;
		var e_error = 0;
		var error_message = 'There have some errors plese check above, Try again.';
		var count=1;

		$('.required-component').each(function(){
			var id=$(this).attr('id');
			var val=$('#'+id).val();
			if (val == "") {
				e_error = 1;
				$('.'+id).html('Required.');
			} else {
				$('.'+id).html('');
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
			if($('#component_table tbody tr').length==0){
				alert('Please add atleast one component');
			}else{
				var count=1;
			$('#component_table tbody tr').each(function(){
			var eq_id=$('#hidden-eq-id').val();
			var eqc_id=$('#hidden-component-id'+count).val();
			var eqc_component_name=$('#eq_component_name'+count+'').val();
			var eqc_value=$('#eq_component_value'+count+'').val();
			var eqc_expected_life=$('#eq_component_expected_life'+count+'').val();
			var eqc_condition=$('#eq_component_condition'+count+'').val();

			var form_data = new FormData();
			form_data.append("row_id", count);
			form_data.append("eq_id", eq_id);
			form_data.append("eqc_id", eqc_id);
			form_data.append("eqc_delete_ids", JSON.stringify(rmComponentArray));
			form_data.append("eqc_component_name", eqc_component_name);
			form_data.append("eqc_value", eqc_value);
			form_data.append("eqc_expected_life", eqc_expected_life);
			form_data.append("eqc_condition", eqc_condition);

			// alert(eqc_id);

			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/equipments/new_component_submission') ?>",
				dataType: 'json',
				data: form_data,
				contentType: false,
				cache: true,
				processData: false,
				success: function (data) {
					// alert(JSON.stringify(data.e_msg[0]));
					if (data.msg == 1) {
						//console.log(data);
						//alert(data.msg[0].space_rate);
						$('.div_roller_total').fadeOut();
						toastr.success('Record is Inserted Successfully!', 'Success');
						setTimeout(function () {
							window.location.replace("<?php echo site_url('admincontrol/equipments/all_equipment_list') ?>");
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
					console.log(arguments);
					alert(" Can't do because: " + error);
				}
			});
				count++;
			});
		x}

		}
		
	}

	// Trigers when edit is pressed for a particular Equipment / Asset
	function modify_record(element) {
		// alert(element);
		if (element != "") {

			var form_data = new FormData();
			form_data.append("eq_id", element);

			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/equipments/get_details_of_equipments') ?>",
				dataType: 'json',
				data: form_data,
				contentType: false,
				cache: true,
				processData: false,
				success: function (data) {
					//alert(data.msg);
					if (data.msg == 1) {
			// 			//console.log(data);
						// alert(JSON.stringify(data));
						$('#hidden-eq-id').val(data.s_msg[0].eq_id);
						$('#update_eq_name').val(data.s_msg[0].eqm_asset_name);
						$('#update_eq_desc').val(data.s_msg[0].eqm_asset_description);
						$('#update_eq_type option[value='+data.s_msg[0].eqm_asset_type+']').attr('selected','selected');
						$('#update_eq_tag').val(data.s_msg[0].eqm_asset_tag);
						// $('#update_eq_condition option[value='+data.s_msg[0].eqm_asset_condition+']').attr('selected','selected');
						$('#update_eq_condition').val(data.s_msg[0].eqm_asset_condition);
						$('#update_eq_category').val(data.s_msg[0].eqm_category);
						$('#update_eq_license_plate').val(data.s_msg[0].eqm_license_plate);
						$('#update_eq_current_operator').val(data.s_msg[0].eqm_current_operator);
						$('#update_eq_status').val(data.s_msg[0].eqm_status);
						$('#update_eq_existing_reading').val(data.s_msg[0].eqm_existing_reading);
						$('#update_eq_estimate_usage').val(data.s_msg[0].eqm_estimate_usage);
						$('#update_eq_location').val(data.s_msg[0].eqm_location);
						$('#update_eq_supplier').val(data.s_msg[0].eqm_supplier);
						$('#update_eq_purchase_price').val(data.s_msg[0].eqm_purchase_price);
						$('#update_eq_purchase_date').val(data.s_msg[0].eqm_purchase_date);
						$('#update_eq_current_value').val(data.s_msg[0].eqm_current_value);
						$('#update_eq_brand').val(data.s_msg[0].eqm_brand);
						$('#update_eq_model').val(data.s_msg[0].eqm_model);
						$('#update_eq_serial').val(data.s_msg[0].eqm_serial);
						$('#update_eq_year').val(data.s_msg[0].eqm_year);
						$('#update_eq_war_expiry_date').val(data.s_msg[0].eqm_warranty_expiry_date);
						$('#update_eq_dep_method').val(data.s_msg[0].eqm_depreciation_method);
						$('#update_eq_rental_total_value').val(data.s_msg[0].eqm_rental_total_value);
						$('#update_eq_rental_insurance').val(data.s_msg[0].eqm_rental_insurance);
						$('#update_eq_rental_insurance_amt').val(data.s_msg[0].eqm_rental_insurance_amt);
						// alert(data.c_msg.length);

						if($('#update_eq_category').val()=='Vehicles'){
							$('.update_vehicle').css('display','flex');
						}

						if($('#update_eq_type').val()=='Rental'){
							$('.update_not-rental').css('display','none');
							$('.update_rental').css('display','flex');
						}

						if($('#update_eq_rental_insurance').val()=='YES'){
							$('.update_rental-insurance').css('display','flex');
						}

						if(data.m_msg.length!=0){
								$('#maintenance_table tbody').html('');
							for(k=0;k<data.m_msg.length;k++){
								$('#maintenance_table tbody').append('\
								<tr id="row'+(k+1)+'">\
									<td>'+(k+1)+'</td>\
									<td>'+data.m_msg[k].eqm_asset_name+'</td>\
									<td>'+data.m_msg[k].sup_name+'</td>\
									<td>'+data.m_msg[k].service_date+'</td>\
									<td>'+data.m_msg[k].service_type+'</td>\
									<td>'+data.m_msg[k].maintenance_notes+'</td>\
									<td>'+data.m_msg[k].attachment+'</td>\
									<td>'+data.m_msg[k].maintenance_total+'</td>\
									<td>\
									<a class="btn btn-outline-warning" onclick="modify_maintenance('+data.m_msg[k].eqm_id+');" href="javascript:;" title="Edit Record"><i class="fa fa-edit text-primary"></i></a>\
									<a onclick="return confirm("You are about to delete a record. This cannot be undone. Are you sure?");" href="<?php echo base_url()."admincontrol/equipments/delete_maintenance/"?>'+data.m_msg[k].eqm_id+'" title="Delete Record"><i class="fa fa-trash text-danger"></i></a>\
									</td>\
								</tr>\
								');
							}
						}else{
								$('#maintenance_table tbody').html('');
							$('#maintenance_table tbody').append('\
								<tr id="row1">\
									<td colspan="9" style="text-align:center">\
										NO MAINTENANCE AVAILABLE\
									</td>\
								</tr>\
							');
						}

						if(data.h_msg.length==0){
							$('#history_table tbody').html('');
							$('#history_table tbody').append('\
								<tr id="row1">\
									<td colspan="3" style="text-align:center">\
										NO HISTORY AVAILABLE\
									</td>\
								</tr>\
							');
						}else{
							$('#history_table tbody').html('');
							for(j=0;j<data.h_msg.length;j++){
								$('#history_table tbody').append('\
								<tr id="row'+(j+1)+'">\
									<td class="row-index">'+(j+1)+'</td>\
									<td>'+data.h_msg[j].eqh_created_date+'</td>\
									<td>'+data.h_msg[j].eqh_description+'</td>\
								</tr>\
								');
							}
						}
						for(i=0;i<data.c_msg.length;i++){

							if(i==0){
								$('#eq_component_name1').val(data.c_msg[i].eqc_component_name);
								$('#eq_component_value1').val(data.c_msg[i].eqc_value);
								$('#eq_component_expected_life1').val(data.c_msg[i].eqc_expected_life);
								$('#eq_component_condition1').val(data.c_msg[i].eqc_condition);
								$('#hidden-component-id1').val(data.c_msg[i].eqc_id);
							}else{
								$('#component_table tbody').append('\
								<tr id="row'+(i+1)+'">\
									<input type="hidden" class="hidden-component-id" id="hidden-component-id'+(i+1)+'" value="'+data.c_msg[i].eqc_id+'">\
									<td class="row-index">'+(i+1)+'</td>\
									<td><input type="text" class="form-control eq_component_name" id="eq_component_name'+(i+1)+'" name="eq_component_name" placeholder="Enter Name" value="'+data.c_msg[i].eqc_component_name+'"></td>\
									<td><input type="number" class="form-control eq_component_value" id="eq_component_value'+(i+1)+'" name="eq_component_value" placeholder="Enter Value" value="'+data.c_msg[i].eqc_value+'"></td>\
									<td><input type="date" class="form-control eq_component_expected_life" id="eq_component_expected_life'+(i+1)+'" name="eq_component_expected_life" value="'+data.c_msg[i].eqc_expected_life+'"></td>\
									<td>\
										<select class="form-control select2 custom-select eq_component_condition" name="eq_component_condition" id="eq_component_condition'+(i+1)+'" autocomplete="off">\
											<option value="">---Select---</option>\
											<option value="New" '+(data.c_msg[i].eqc_condition == "New" ? 'selected': '')+'>New</option>\
											<option value="Used" '+(data.c_msg[i].eqc_condition == "Used" ? 'selected': '')+'>Used</option>\
											<option value="To Be Retired" '+(data.c_msg[i].eqc_condition == "To Be Retired" ? 'selected': '')+'>To Be Retired</option>\
										</select>\
									</td>\
									<td>\
									<a id="removeComponent" href="javascript:void(0);" title="Delete Record"><i class="fa fa-trash text-danger"></i></a>\
									</td>\
								</tr>\
								');

							}
						}
						rowCount=data.c_msg.length+1;
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

	// on asset type change
	$('#eq_type').on('change',function(){
		if($(this).val()=='Rental'){
			$('.not-rental').css('display','none');
			$('.rental').css('display','flex');
		}else{
			$('.not-rental').css('display','flex');
			$('.rental').css('display','none');
		}
	});

	// on rental insurance change
	$('#eq_rental_insurance').on('change',function(){
		if($(this).val()=='Rental'){
			$('.rental-insurance').css('display','flex');
		}else{
			$('.rental-insurance').css('display','none');
		}
	});

	// on update asset category change
	$('#update_eq_category').on('change',function(){
		if($(this).val()=='Vehicles'){
			$('.update_vehicle').css('display','flex');
		}else{
			$('.update_vehicle').css('display','none');
		}
	});

	// on update asset type change
	$('#update_eq_type').on('change',function(){
		if($(this).val()=='Rental'){
			$('.update_not-rental').css('display','none');
			$('.update_rental').css('display','flex');
		}else{
			$('.update_not-rental').css('display','flex');
			$('.update_rental').css('display','none');
		}
	});

	// on update rental insurance change
	$('#update_eq_rental_insurance').on('change',function(){
		if($(this).val()=='YES'){
			$('.update_rental-insurance').css('display','flex');
		}else{
			$('.update_rental-insurance').css('display','none');
		}
	});

	// on asset category change
	$('#eq_category').on('change',function(){
		if($(this).val()=='Vehicles'){
			$('.vehicle').css('display','flex');
		}else{
			$('.vehicle').css('display','none');
		}
	});

	// on asset type change
	$('#eq_type').on('change',function(){
		if($(this).val()=='Rental'){
			$('.not-rental').css('display','none');
			$('.rental').css('display','flex');
		}else{
			$('.not-rental').css('display','flex');
			$('.rental').css('display','none');
		}
	});

	// on rental insurance change
	$('#eq_rental_insurance').on('change',function(){
		if($(this).val()=='Rental'){
			$('.rental-insurance').css('display','flex');
		}else{
			$('.insurance').css('display','none');
		}
	});

	$(function () {
		$('#alert_msg').delay(6000).fadeOut();
		//$('.select22, .select33').selectpicker();
		$('.alert-error, .invalid-feedback').delay(6000).fadeOut();
	});

	/****************************************
	 *       Create data tables             *
	 ****************************************/
	$('#zero_config,#component_table').DataTable();
	$('#equipments-table').DataTable({});

	// add new maintenance btn click
	$('.add-new-maintenance-btn').click(function(){
		$('.maintenance-list').css('display','none');
		$('.add-new-maintenance').css('display','block');
	});

	// Cancel btn click maintenance
	$('.cancel-new-maintenance').click(function(){
		$('.maintenance-list').css('display','block');
		$('.add-new-maintenance').css('display','none');
	});

	var rowCount2=2;

	// add new maintenance details item
	function goto_add_item(){
		$('#eqm_details_table tbody').append('\
		<tr id="row'+rowCount2+'">\
			<input type="hidden" class="hidden-component-id" id="hidden-component-id'+rowCount2+'">\
			<td class="row-index">'+rowCount2+'</td>\
			<td>\
				<textarea class="form-control eqmd_description required-eqmd" id="eqmd_description'+rowCount2+'" name="eqmd_description" placeholder="Enter Description"></textarea>\
				<small class="invalid-feedback eqmd_description'+rowCount2+'"></small>\
			</td>\
			<td>\
				<textarea class="form-control eqmd_notes required-eqmd" id="eqmd_notes'+rowCount2+'" name="eqmd_notes" placeholder="Enter Notes"></textarea>\
				<small class="invalid-feedback eqmd_notes'+rowCount2+'"></small>\
			</td>\
			<td>\
				<?php foreach($taxcodes as $taxcode){?>
					<input type="hidden" id="eqmd_taxpercentage'+rowCount2+'<?php echo $taxcode->name?>" value="<?php echo $taxcode->percentage;?>">\
				<?php }?>
				<select class="form-control select2 custom-select eqmd_taxcode required-eqmd" name="eqmd_taxcode" id="eqmd_taxcode'+rowCount2+'" autocomplete="off" style="width:100%">\
					<option value="">---Select---</option>\
					<?php foreach($taxcodes as $taxcode){?>
						<option value="<?php echo $taxcode->name?>"><?php echo $taxcode->description;?></option>\
					<?php }?>
				</select><br>\
				<small class="invalid-feedback eqmd_taxcode'+rowCount2+'"></small>\
			</td>\
			<td>\
				<input type="number" class="form-control eqmd_qty required-eqmd" id="eqmd_qty'+rowCount2+'" name="eqmd_qty" step="0.01">\
				<small class="invalid-feedback eqmd_qty'+rowCount2+'"></small>\
			</td>\
			<td>\
				<input type="number" class="form-control eqmd_rate required-eqmd" id="eqmd_rate'+rowCount2+'" name="eqmd_rate" step="0.01">\
				<small class="invalid-feedback eqmd_tax_amt'+rowCount2+'"></small>\
			</td>\
			<td>\
				<input type="number" class="form-control eqmd_pre_tax_amt required-eqmd" id="eqmd_pre_tax_amt'+rowCount2+'" name="eqmd_pre_tax_amt" step="0.01" readonly>\
				<small class="invalid-feedback eqmd_pre_tax_amt'+rowCount2+'"></small>\
			</td>\
			<td>\
				<input type="number" class="form-control eqmd_tax_amt required-eqmd" id="eqmd_tax_amt'+rowCount2+'" name="eqmd_tax_amt" step="0.01" readonly>\
				<small class="invalid-feedback eqmd_tax_amt'+rowCount2+'"></small>\
			</td>\
			<td>\
				<a id="removeMItem" class="removeMItem" href="javascript:void(0);" title="Delete Record"><i class="fa fa-trash text-danger"></i></a>\
			</td>\
		</tr>\
		');
		rowCount2++;
	}

		var rmDetailsArray= [];
	// remove maintenance detail line item
	$('#eqm_details_table tbody').on('click', '#removeMItem', function(){
		if (confirm('Are you sure you want to delete ?')) {
			var rowId=$(this).closest('tr').attr('id');
			var index=rowId.match(/\d+/);
			if($(this).closest('tr').children('#hidden-details-id'+index).val()!=""){
				rmDetailsArray.push($(this).closest('tr').children('#hidden-details-id'+index).val());
			}
			var child=$(this).closest('tr').nextAll();
			child.each(function(){
				var id=$(this).attr('id');
				var idx=$(this).children('.row-index');
				var dig=id.match(/\d+/);
				idx.html(`${dig-1}`);
				$(this).attr('id',`row${dig-1}`);
				$(this).children('.hidden-details-id').attr('id',`hidden-details-id${dig-1}`);
				$(this).children('td').children('.eqmd_description').attr('id',`eqmd_description${dig-1}`);
				$(this).find('select').attr('id',`eqmd_taxcode${dig-1}`);
				$(this).children('td').children('.eqmd_notes').attr('id',`eqmd_notes${dig-1}`);
				$(this).children('td').children('.eqmd_qty').attr('id',`eqmd_qty${dig-1}`);
				$(this).children('td').children('.eqmd_rate').attr('id',`eqmd_qty${dig-1}`);
				$(this).children('td').children('.eqmd_pre_tax_amt').attr('id',`eqmd_pre_tax_amt${dig-1}`);
				$(this).children('td').children('.eqmd_tax_amt').attr('id',`eqmd_tax_amt${dig-1}`);
			});
			$(this).parent().parent().remove();
			rowCount--;
		} else {
		}
		calculateTotal();
	});


	// calculation for maintenance details line items - add
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

	// calculation for maintenance details line items - update
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

	// subtotal, total tax and total calulation - add
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

	// subtotal, total tax and total calulation - update
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

	// Add Maintenance function
	function gotomaintenanceclickbutton() {
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

		var eqm_asset = $('#hidden-eq-id').val();
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
			// alert(tableRow);

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
							window.location.replace("<?php echo site_url('admincontrol/equipments/all_equipment_list') ?>");
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
		
	// Trigers when edit is pressed for a particular Maintenance
	function modify_maintenance(element) {
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
						// alert(data.s_msg[0].vendor_id);
						$('#hidden-eqm-id').val(data.s_msg[0].eqm_id);
						// $('#update_eqm_asset').val(data.s_msg[0].asset_id);
						$('#update_eqm_supplier').val(data.s_msg[0].vendor_id).change();
						$('#update_eqm_service_date').val(data.s_msg[0].service_date);
						$('#update_eqm_service_type').val(data.s_msg[0].service_type).change();
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
						// $(".select2").selectpicker('refresh');
						$('.maintenance-list').css('display','none');
						$('.edit-maintenance').css('display','block');

					} else {
						// $('#update_pr_no').val('');
						// $('#Modal_editrecord').modal('hide');
					}
				}
			});
		} else {
			// $('#update_pr_no').val('');
			// $('#Modal_editrecord').modal('hide');
		}
	}
		
	var rowCountUpdate=2;

	// add maintenance detail line item - update
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
				<small class="invalid-feedback update_eqmd_qty'+rowCountUpdate+'"></small>\
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

	var rmComponentArray1= [];

	// remove maintenance detail line item - update
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
	});

	// Update Maintenance function
	function gotoupdatemaintenanceclickbutton() {
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
		var eqm_asset = $('#hidden-eq-id').val();
		var eqm_supplier = $('#update_eqm_supplier').val();
		var eqm_service_date = $('#update_eqm_service_date').val();
		var eqm_service_type = $('#update_eqm_service_type').val();
		var eqm_notes = $('#update_eqm_notes').val();
		var eqm_total = $('#update_eqm_total').val();
		var eqm_attachment = $('#update_eqm_attachment')[0].files;

		// if (eqm_asset == "") {
		// 	e_error = 1;
		// 	$('.update_eqm_asset').html('Name is Required.');
		// } else {
		// 	$('.update_eqm_asset').html('');
		// }

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
							window.location.replace("<?php echo site_url('admincontrol/equipments/all_equipment_list') ?>");
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
	}

	// Equipment / Asset Checkout popup show
	function checkout_asset($eq_id) {
		if($eq_id!=''){
			$('#checkout_asset').val($eq_id).change();
		}
		$('#Modal_checkout').modal('show');
	}

	// add checkout 
	function gotoupdatecheckoutclickbutton(){
		// alert('skdfjkns');
		$('.div_roller_total').fadeIn();
		var delay = 8000;
		var e_error = 0;
		var error_message = 'There have some errors plese check above, Try again.';

		var checkout_eq_id = $('#checkout_asset').val();
		var checkout_location_id = $('#checkout_location').val();
		var checkout_user_id = $('#checkout_operator').val();
		var checkout_date = $('#checkout_date').val();

		if (checkout_eq_id == "") {
			e_error = 1;
			$('.checkout_asset').html('Equipment is Required.');
		} else {
			$('.checkout_asset').html('');
		}

		if (checkout_location_id == "") {
			e_error = 1;
			$('.checkout_location').html('Location is Required.');
		} else {
			$('.checkout_location').html('');
		}

		if (checkout_user_id == "") {
			e_error = 1;
			$('.checkout_operator').html('Operator is Required.');
		} else {
			$('.checkout_operator').html('');
		}

		if (checkout_date == "") {
			e_error = 1;
			$('.checkout_date').html('Date is Required.');
		} else {
			$('.checkout_date').html('');
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
			form_data.append("checkout_eq_id", checkout_eq_id);
			form_data.append("checkout_location_id", checkout_location_id);
			form_data.append("checkout_user_id", checkout_user_id);
			form_data.append("checkout_date", checkout_date);
			
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/equipments/new_checkout_submission') ?>",
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
						toastr.success('Checkout Successfully!', 'Success');
						setTimeout(function () {
							window.location.replace("<?php echo site_url('admincontrol/equipments/all_equipment_list') ?>");
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

	}

	// Equipment / Asset Checkin popup show
	function checkin_asset($eq_id) {
		if($eq_id!=''){
			$('#checkin_asset').val($eq_id).change();
		}
		$('#Modal_checkin').modal('show');
	}

	// add checkout 
	function gotoupdatecheckinclickbutton(){
		// alert('skdfjkns');
		$('.div_roller_total').fadeIn();
		var delay = 8000;
		var e_error = 0;
		var error_message = 'There have some errors plese check above, Try again.';

		var checkin_eq_id = $('#checkin_asset').val();
		var checkin_date = $('#checkin_date').val();

		if (checkin_eq_id == "") {
			e_error = 1;
			$('.checkin_asset').html('Equipment is Required.');
		} else {
			$('.checkin_asset').html('');
		}

		if (checkin_date == "") {
			e_error = 1;
			$('.checkin_date').html('Date is Required.');
		} else {
			$('.checkin_date').html('');
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
			form_data.append("checkin_eq_id", checkin_eq_id);
			form_data.append("checkin_date", checkin_date);
			
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/equipments/new_checkin_submission') ?>",
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
						toastr.success('Checkout Successfully!', 'Success');
						setTimeout(function () {
							window.location.replace("<?php echo site_url('admincontrol/equipments/all_equipment_list') ?>");
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

	}

	// update reading popup show
	function update_reading(element) {
		// alert(element);
		if (element != "") {

			var form_data = new FormData();
			form_data.append("eq_id", element);

			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/equipments/get_equipment_reading_details') ?>",
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
						$('#hidden-update-reading-eq-id').val(data.s_msg[0].eq_id);
						$('#update_reading_estimate_usage').val(data.s_msg[0].eqm_estimate_usage);
						$('#update_reading_remaining_life').val(data.s_msg[0].eqm_remaining_life);

						$('#Modal_updatereading').modal('show');

					} else {
						// $('#update_pr_no').val('');
						$('#Modal_updatereading').modal('hide');
					}
				}
			});
		} else {
			// $('#update_pr_no').val('');
			$('#Modal_updatereading').modal('hide');
		}
	}

	// update current reading
	function gotoupdatereadingclickbutton(){
		// alert('skdfjkns');
		$('.div_roller_total').fadeIn();
		var delay = 8000;
		var e_error = 0;
		var error_message = 'There have some errors plese check above, Try again.';

		var eq_id = $('#hidden-update-reading-eq-id').val();
		var current_reading = parseInt($('#update_reading_current_reading').val());
		var current_reading_date = $('#update_reading_date').val();
		var remaining_life = parseInt($('#update_reading_remaining_life').val());

		if (current_reading == "") {
			e_error = 1;
			$('.update_reading_current_reading').html('Current Reading is Required.');
		} else {
			if (current_reading > remaining_life) {
				e_error = 1;
				$('.update_reading_current_reading').html('Can\'t be greater than remaining life.');
			} else {
				$('.update_reading_current_reading').html('');
			}
		}

		if (current_reading_date == "") {
			e_error = 1;
			$('.update_reading_date').html('Date is Required.');
		} else {
			$('.update_reading_date').html('');
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
			form_data.append("eq_id", eq_id);
			form_data.append("current_reading", current_reading);
			form_data.append("current_reading_date", current_reading_date);
			
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/equipments/update_reading_submission') ?>",
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
						toastr.success('Checkout Successfully!', 'Success');
						setTimeout(function () {
							window.location.replace("<?php echo site_url('admincontrol/equipments/all_equipment_list') ?>");
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

	}

</script>

<!-- javascript end -->
        
