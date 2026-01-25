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
				<h4 class="page-title">Project List</h4>
				<div class="ml-auto text-right">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a
										href="<?php echo base_url('admincontrol/dashboard'); ?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Project List</li>
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
						if($this->session->userdata('utype')==1 || $templateDetails->pt_m_projects<3){?>
							<a href="javascript:;" onclick="goto_add_record();" class="btn btn-primary float-right mb-2"
							style="margin-right: 10px;">Add New Project</a>
						<?php }?>
						<div class="table-responsive">
							<table id="zero_config" class="table table-striped table-bordered">
								<thead>
								<tr style="font-weight: bold;">
									<th>Sl No.</th>
									<th>Project No.</th>
									<th>Name</th>
									<th>Total User</th>
									<th>Address</th>
									<th>Description</th>
									<th>Status</th>
									<?php 
									if($this->session->userdata('utype')==1 || $templateDetails->pt_m_projects<3){?>
										<th>Action</th>
									<?php }?>
								</tr>
								</thead>
								<tbody>
								<?php foreach ($getrecord_list as $keys => $recorditem) { ?>
									<tr>
										<td><?php echo $keys + 1; ?></td>
										<td><?php echo $recorditem->proj_number; ?></td>
										<td><?php if($recorditem->procore_integration_status=='YES'){echo $recorditem->proj_name.'<br><span style="background-color:#E64900;color:#fff;font-size:10px;font-weight:bold;padding:5px">PROCORE</span>';}else{echo $recorditem->proj_name;} ?></td>
										<td><?php echo $recorditem->proj_contact; ?></td>
										<td><?php echo $recorditem->proj_address; ?></td>
										<td><?php echo $recorditem->proj_description; ?></td>
										<!--<td><?php //echo date('d-m-Y h:i A',strtotime($recorditem->cc_createdate));
										?></td>-->
										<td><?php if ($recorditem->proj_status == 1) { ?>
												<span style="color:green;">Active</span>
											<?php } elseif ($recorditem->proj_status == 0) { ?>
												<span style="color:red;">InActive</span>
											<?php } ?></td>
										<?php 
										if($this->session->userdata('utype')==1 || $templateDetails->pt_m_projects<3){?>
											<td>
												<a class="btn btn-outline-warning"
												onclick="modify_record(<?php echo $recorditem->proj_id; ?>);"
												href="javascript:;" title="Edit Record"><i
															class="fa fa-edit text-primary"></i></a>
												<?php if ($recorditem->proj_status == 1) { ?>
													<a class="btn btn-outline-warning"
													href="<?php echo base_url() . 'admincontrol/projects/lock_project_set/' . $recorditem->proj_id; ?>"
													title="Lock Record"><i class="fa fa-unlock text-dark"></i></a>
												<?php } else { ?>
													<a class="btn btn-outline-warning"
													href="<?php echo base_url() . 'admincontrol/projects/unlock_project_set/' . $recorditem->proj_id; ?>"
													title="Unock Record"><i class="fa fa-lock text-dark"></i></a>
												<?php } ?>
													<?php 
													if($recorditem->procore_integration_status!='YES' && ($this->session->userdata('utype')==1 || $templateDetails->pt_m_projects<2)){?>
														<a class="btn btn-outline-warning" onclick="return confirm('You are about to delete a record. This cannot be undone. Are you sure?');" href="<?php echo base_url().'admincontrol/projects/delete_itemset/'.$recorditem->proj_id;
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
					<h5 class="modal-title">Add New Project</h5>
					<button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<?php if (isset($error)) { ?>
						<div class="alert alert-danger alert-error">
							<h4>Error!</h4>
							<?php echo $error; ?>
						</div>
					<?php } ?>

					<!--<h4 class="card-title">Personal Info</h4>-->
					<div class="form-group row">
						<label for="fname" class="col-sm-3 text-right control-label col-form-label">Project
							Number</label>
						<div class="col-sm-9">
							<input type="text" class="form-control reset-input" name="pr_no" id="pr_no"
								   placeholder="Enter Project Number" autocomplete="off"/>
							<input type="hidden" class="form-control" name="pr_id" id="pr_id"
								   placeholder="Enter Project ID" autocomplete="off"/>
							<small class="invalid-feedback pr_no"><?php echo form_error('pr_no'); ?></small>
						</div>

					</div>
					<div class="form-group row">
						<label for="lname" class="col-sm-3 text-right control-label col-form-label">Project Name</label>
						<div class="col-sm-9">
							<input type="text" class="form-control reset-input" name="pr_name" id="pr_name"
								   placeholder="Enter Project Name" autocomplete="off"/>
							<small class="invalid-feedback pr_name"><?php echo form_error('pr_name'); ?></small>
						</div>
					</div>
					<div class="form-group row">
						<label for="cono1" class="col-sm-3 text-right control-label col-form-label">Address</label>
						<div class="col-sm-6">
							<textarea class="form-control reset-input" name="pr_address" id="pr_address"
									  placeholder="Enter Full Address" autocomplete="off"></textarea>
							<small class="invalid-feedback pr_address"><?php echo form_error('pr_address'); ?></small>
						</div>
					</div>
					<div class="form-group row">
						<label for="cono1" class="col-sm-3 text-right control-label col-form-label">Description</label>
						<div class="col-sm-6">
							<textarea class="form-control reset-input" name="pr_desc" id="pr_desc" placeholder="Enter Description"
									  autocomplete="off"></textarea>
							<small class="invalid-feedback pr_desc"><?php echo form_error('pr_desc'); ?></small>
						</div>
					</div>
					<div class="form-group row">
						<label for="lname" class="col-sm-3 text-right control-label col-form-label">Project Manager</label>
						<div class="col-sm-6">
							<select class="form-control select2 custom-select reset-input" name="pr_manager" id="pr_manager" multiple
									data-live-search="true" autocomplete="off">
								<!--<option value="">---Select---</option>-->
								<?php foreach ($usr_list as $users) { ?>
									<option value="<?php echo $users->u_id; ?>"><?php echo $users->firstname . ' ' . $users->lastname . ' | ' . $users->phone . ' (' . $users->mu_name . ')'; ?></option>
								<?php } ?>
							</select>
							<small class="invalid-feedback pr_manager"><?php echo form_error('pr_manager'); ?></small>
						</div>
					</div>
					<div class="form-group row">
						<label for="lname" class="col-sm-3 text-right control-label col-form-label">Project Coordinator</label>
						<div class="col-sm-6">
							<select class="form-control select2 custom-select reset-input" name="pr_coordinator" id="pr_coordinator" multiple
									data-live-search="true" autocomplete="off">
								<!--<option value="">---Select---</option>-->
								<?php foreach ($usr_list as $users) { ?>
									<option value="<?php echo $users->u_id; ?>"><?php echo $users->firstname . ' ' . $users->lastname . ' | ' . $users->phone . ' (' . $users->mu_name . ')'; ?></option>
								<?php } ?>
							</select>
							<small class="invalid-feedback pr_coordinator"><?php echo form_error('pr_coordinator'); ?></small>
						</div>
					</div>
					<div class="form-group row">
						<label for="lname" class="col-sm-3 text-right control-label col-form-label">Site Supervisor</label>
						<div class="col-sm-6">
							<select class="form-control select2 custom-select reset-input" name="pr_supervisor" id="pr_supervisor" multiple
									data-live-search="true" autocomplete="off">
								<!--<option value="">---Select---</option>-->
								<?php foreach ($usr_list as $users) { ?>
									<option value="<?php echo $users->u_id; ?>"><?php echo $users->firstname . ' ' . $users->lastname . ' | ' . $users->phone . ' (' . $users->mu_name . ')'; ?></option>
								<?php } ?>
							</select>
							<small class="invalid-feedback pr_supervisor"><?php echo form_error('pr_supervisor'); ?></small>
						</div>
					</div>
					<div class="form-group row">
						<label for="lname" class="col-sm-3 text-right control-label col-form-label">Site Coordinator</label>
						<div class="col-sm-6">
							<select class="form-control select2 custom-select reset-input" name="pr_site_coordinator" id="pr_site_coordinator" multiple
									data-live-search="true" autocomplete="off">
								<!--<option value="">---Select---</option>-->
								<?php foreach ($usr_list as $users) { ?>
									<option value="<?php echo $users->u_id; ?>"><?php echo $users->firstname . ' ' . $users->lastname . ' | ' . $users->phone . ' (' . $users->mu_name . ')'; ?></option>
								<?php } ?>
							</select>
							<small class="invalid-feedback pr_site_coordinator"><?php echo form_error('pr_site_coordinator'); ?></small>
						</div>
					</div>
					<div class="form-group row">
						<label for="lname" class="col-sm-3 text-right control-label col-form-label">Project Accountant</label>
						<div class="col-sm-6">
							<select class="form-control select2 custom-select reset-input" name="pr_accountant" id="pr_accountant" multiple
									data-live-search="true" autocomplete="off">
								<!--<option value="">---Select---</option>-->
								<?php foreach ($usr_list as $users) { ?>
									<option value="<?php echo $users->u_id; ?>"><?php echo $users->firstname . ' ' . $users->lastname . ' | ' . $users->phone . ' (' . $users->mu_name . ')'; ?></option>
								<?php } ?>
							</select>
							<small class="invalid-feedback pr_accountant"><?php echo form_error('pr_accountant'); ?></small>
						</div>
					</div>
					<div class="form-group row">
						<label for="lname" class="col-sm-3 text-right control-label col-form-label">Bill to</label>
						<div class="col-sm-6">

							<label for="same_as_company_info">
								<input type="checkbox" class="same_as_company_info" checked id="same_as_company_info" name="same_as_company_info" value="1">
								<span class="tag">Same as company info</span>
							</label>

						</div>
					</div>

					<div class="form-group row d-none" id="bill_name_div">
						<label for="lname" class="col-sm-3 text-right control-label col-form-label">Billing Name</label>
						<div class="col-sm-6">
							<input type="text" class="form-control" name="billing_name" id="billing_name"
								   placeholder="Enter Billing Name" autocomplete="off"/>
							<small class="invalid-feedback billing_name"><?php echo form_error('billing_name'); ?></small>

						</div>
					</div>

					<div class="form-group row d-none" id="bill_address_div">
						<label for="lname" class="col-sm-3 text-right control-label col-form-label">Billing Address</label>
						<div class="col-sm-6">
							<textarea class="form-control" name="billing_address" id="billing_address"
									  placeholder="Enter Billing Address" autocomplete="off"></textarea>
							<small class="invalid-feedback billing_address"><?php echo form_error('billing_address'); ?></small>

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
				<div class="modal-footer">
					<!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>-->
					<button type="button" id="submit_record_btn" class="btn btn-primary" onclick="gotoclclickbutton();">
						Submit
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
					<h5 class="modal-title">Update Project Details</h5>
					<button type="button" class="close close_modal2" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<?php echo form_open_multipart('', 'class="form-horizontal" id="myForm"'); ?>
					<div class="card-body">
						<?php if (isset($error)) { ?>
							<div class="alert alert-danger alert-error">
								<h4>Error!</h4>
								<?php echo $error; ?>
							</div>
						<?php } ?>

						<!--<h4 class="card-title">Personal Info</h4>-->
						<div class="form-group row">
							<label for="fname" class="col-sm-3 text-right control-label col-form-label">Project
								Number</label>
							<div class="col-sm-8">
								<input type="text" class="form-control reset-input1" name="pr_no" id="update_pr_no"
									   placeholder="Enter Project Number" autocomplete="off"/>
								<small class="invalid-feedback pr_no"><?php echo form_error('pr_no'); ?></small>
							</div>

						</div>
						<div class="form-group row">
							<label for="lname" class="col-sm-3 text-right control-label col-form-label">Project
								Name</label>
							<div class="col-sm-8">
								<input type="text" class="form-control reset-input1" name="pr_name" id="update_pr_name"
									   placeholder="Enter Project Name" autocomplete="off"/>
								<small class="invalid-feedback pr_name"><?php echo form_error('pr_name'); ?></small>
							</div>
						</div>
						<div class="form-group row">
							<label for="cono1" class="col-sm-3 text-right control-label col-form-label">Address</label>
							<div class="col-sm-6">
								<textarea class="form-control reset-input1" name="pr_address" id="update_pr_address"
										  placeholder="Enter Full Address" autocomplete="off"></textarea>
								<small class="invalid-feedback pr_address"><?php echo form_error('pr_address'); ?></small>
							</div>
						</div>
						<div class="form-group row">
							<label for="cono1"
								   class="col-sm-3 text-right control-label col-form-label">Description</label>
							<div class="col-sm-6">
								<textarea class="form-control reset-input1" name="pr_desc" id="update_pr_desc"
										  placeholder="Enter Description" autocomplete="off"></textarea>
								<small class="invalid-feedback pr_desc"><?php echo form_error('pr_desc'); ?></small>
							</div>
						</div>
						<div class="form-group row">
							<label for="lname" class="col-sm-3 text-right control-label col-form-label">Project Manager</label>
							<div class="col-sm-6">
								<select class="form-control select2 custom-select reset-input1" name="update_pr_manager" id="update_pr_manager" multiple
										data-live-search="true" autocomplete="off">
									<!--<option value="">---Select---</option>-->
									<?php foreach ($usr_list as $users) { ?>
										<option value="<?php echo $users->u_id; ?>"><?php echo $users->firstname . ' ' . $users->lastname . ' | ' . $users->phone . ' (' . $users->mu_name . ')'; ?></option>
									<?php } ?>
								</select>
								<small class="invalid-feedback update_pr_manager"><?php echo form_error('update_pr_manager'); ?></small>
							</div>
						</div>
						<div class="form-group row">
							<label for="lname" class="col-sm-3 text-right control-label col-form-label">Project Coordinator</label>
							<div class="col-sm-6">
								<select class="form-control select2 custom-select reset-input1" name="update_pr_coordinator" id="update_pr_coordinator" multiple
										data-live-search="true" autocomplete="off">
									<!--<option value="">---Select---</option>-->
									<?php foreach ($usr_list as $users) { ?>
										<option value="<?php echo $users->u_id; ?>"><?php echo $users->firstname . ' ' . $users->lastname . ' | ' . $users->phone . ' (' . $users->mu_name . ')'; ?></option>
									<?php } ?>
								</select>
								<small class="invalid-feedback update_pr_coordinator"><?php echo form_error('update_pr_coordinator'); ?></small>
							</div>
						</div>
						<div class="form-group row">
							<label for="lname" class="col-sm-3 text-right control-label col-form-label">Site Supervisor</label>
							<div class="col-sm-6">
								<select class="form-control select2 custom-select reset-input1" name="update_pr_supervisor" id="update_pr_supervisor" multiple
										data-live-search="true" autocomplete="off">
									<!--<option value="">---Select---</option>-->
									<?php foreach ($usr_list as $users) { ?>
										<option value="<?php echo $users->u_id; ?>"><?php echo $users->firstname . ' ' . $users->lastname . ' | ' . $users->phone . ' (' . $users->mu_name . ')'; ?></option>
									<?php } ?>
								</select>
								<small class="invalid-feedback update_pr_supervisor"><?php echo form_error('update_pr_supervisor'); ?></small>
							</div>
						</div>
						<div class="form-group row">
							<label for="lname" class="col-sm-3 text-right control-label col-form-label">Site Coordinator</label>
							<div class="col-sm-6">
								<select class="form-control select2 custom-select reset-input1" name="update_pr_site_coordinator" id="update_pr_site_coordinator" multiple
										data-live-search="true" autocomplete="off">
									<!--<option value="">---Select---</option>-->
									<?php foreach ($usr_list as $users) { ?>
										<option value="<?php echo $users->u_id; ?>"><?php echo $users->firstname . ' ' . $users->lastname . ' | ' . $users->phone . ' (' . $users->mu_name . ')'; ?></option>
									<?php } ?>
								</select>
								<small class="invalid-feedback update_pr_site_coordinator"><?php echo form_error('update_pr_site_coordinator'); ?></small>
							</div>
						</div>
						<div class="form-group row">
							<label for="lname" class="col-sm-3 text-right control-label col-form-label">Project Accountant</label>
							<div class="col-sm-6">
								<select class="form-control select2 custom-select reset-input1" name="update_pr_accountant" id="update_pr_accountant" multiple
										data-live-search="true" autocomplete="off">
									<!--<option value="">---Select---</option>-->
									<?php foreach ($usr_list as $users) { ?>
										<option value="<?php echo $users->u_id; ?>"><?php echo $users->firstname . ' ' . $users->lastname . ' | ' . $users->phone . ' (' . $users->mu_name . ')'; ?></option>
									<?php } ?>
								</select>
								<small class="invalid-feedback update_pr_accountant"><?php echo form_error('update_pr_accountant'); ?></small>
							</div>
						</div>

						<div class="form-group row">
							<label for="lname" class="col-sm-3 text-right control-label col-form-label">Bill to</label>
							<div class="col-sm-6">

								<label for="update_same_as_company_info">
									<input type="checkbox" class="update_same_as_company_info" checked id="update_same_as_company_info" name="update_same_as_company_info" value="1">
									<span class="tag">Same as company info</span>
								</label>

							</div>
						</div>

						<div class="form-group row d-none" id="update_bill_name_div">
							<label for="lname" class="col-sm-3 text-right control-label col-form-label">Billing Name</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" name="update_billing_name" id="update_billing_name"
									   placeholder="Enter Billing Name" autocomplete="off"/>
								<small class="invalid-feedback update_billing_name"><?php echo form_error('update_billing_name'); ?></small>

							</div>
						</div>

						<div class="form-group row d-none" id="update_bill_address_div">
							<label for="lname" class="col-sm-3 text-right control-label col-form-label">Billing Address</label>
							<div class="col-sm-6">
							<textarea class="form-control" name="update_billing_address" id="update_billing_address"
									  placeholder="Enter Billing Address" autocomplete="off"></textarea>
								<small class="invalid-feedback update_billing_address"><?php echo form_error('update_billing_address'); ?></small>

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

					<?php form_close(); ?>

				</div>
				<div class="modal-footer">
					<!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>-->
					<button type="button" id="update_record_btn" class="btn btn-primary"
							onclick="gotoupdatebutton();">Update
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

			var pr_no = $('#pr_no').val();
			var pr_name = $('#pr_name').val();
			var pr_address = $('#pr_address').val();
			var pr_desc = $('#pr_desc').val();
			var pr_manager = $('#pr_manager').val();
			var pr_coordinator = $('#pr_coordinator').val();
			var pr_supervisor = $('#pr_supervisor').val();
			var pr_site_coordinator = $('#pr_site_coordinator').val();
			var pr_accountant = $('#pr_accountant').val();
			var billing_name = $('#billing_name').val();
			var billing_address = $('#billing_address').val();
			var same_as_company_info = $('#same_as_company_info').val();
			//var ap_quaran = $("input[name='ap_quaran']:checked").val();

			if (pr_no == "") {
				e_error = 1;
				$('.pr_no').html('Project Number is Required.');
			} else {
				if (!pr_no.match(alphanumerics_spaces)) {
					e_error = 1;
					$('.pr_no').html('Project Number not use special carecters [without _ , . -], Check again.');
				} else {
					$('.pr_no').html('');
				}
			}
			if (pr_name == "") {
				e_error = 1;
				$('.pr_name').html('Project Name is Required.');
			} else {
				if (!pr_name.match(alphanumerics_no)) {
					e_error = 1;
					$('.pr_name').html('Project Name not use special carecters [without _ / : ( @ . & ) , -], Check again.');
				} else {
					$('.pr_name').html('');
				}
			}

			if(same_as_company_info == 0){
				if(billing_name == "") {
					e_error = 1;
					$('.billing_name').html('Billing name is required');
				} else {
					$('.billing_name').html('');
				}

				if(billing_address == "") {
					e_error = 1;
					$('.billing_address').html('Billing address is required');
				} else {
					$('.billing_address').html('');
				}

			}else{
				billing_name="";
				billing_address="";
			}

			if (pr_address == "") {
				e_error = 1;
				$('.pr_address').html('Project Address is Required.');
			} else {
				if (!pr_address.match(alphanumerics_no)) {
					e_error = 1;
					$('.pr_address').html('Project Address not use special carecters [without _ / : ( @ . & ) , -], Check again.');
				} else {
					$('.pr_address').html('');
				}
			}
			if (pr_desc == "") {
				e_error = 1;
				$('.pr_desc').html('Project Description is Required.');
			} else {
				if (!pr_desc.match(alphanumerics_no)) {
					e_error = 1;
					$('.pr_desc').html('Project Description not use special carecters [without _ / : ( @ . & ) , -], Check again.');
				} else {
					$('.pr_desc').html('');
				}
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

				var form_data = new FormData();
				form_data.append("pr_no", pr_no);
				form_data.append("pr_name", pr_name);
				form_data.append("pr_address", pr_address);
				form_data.append("pr_desc", pr_desc);
				form_data.append("pr_accountant", pr_accountant);
				form_data.append("pr_manager", pr_manager);
				form_data.append("pr_coordinator", pr_coordinator);
				form_data.append("pr_supervisor", pr_supervisor);
				form_data.append("pr_site_coordinator", pr_site_coordinator);
				form_data.append("billing_name", billing_name);
				form_data.append("billing_address", billing_address);

				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/projects/new_project_submission') ?>",
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
								window.location.replace("<?php echo site_url('admincontrol/projects/all_project_list') ?>");
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
			//$("#myForm").submit();


		}


		function goto_add_record() {
			$('.reset-input').val('').change();
			$('.same_as_company_info').prop('checked',true);
			$('#bill_name_div').addClass('d-none');
			$('#bill_address_div').addClass('d-none');
			$('#Modal_addrecord').modal('show');
		}

		function modify_record(element) {
			//alert(element);
			if (element != "") {
				$('.reset-input1').trigger('change');
				// $(".select2").selectpicker('refresh');
				$('.update_same_as_company_info').prop('checked',true);
				$('#update_bill_name_div').addClass('d-none');
				$('#update_bill_address_div').addClass('d-none');
				var form_data = new FormData();
				form_data.append("name_projid", element);

				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/projects/get_details_of_projects') ?>",
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
							$('#update_pr_no').val(data.s_msg.proj_number);
							$('#pr_id').val(data.s_msg.proj_id);
							$('#update_pr_name').val(data.s_msg.proj_name);
							$('#update_pr_address').val(data.s_msg.proj_address);
							$('#update_pr_desc').val(data.s_msg.proj_description);
							// $('#update_pr_manager').val(data.s_msg.sup_email);
							// $('#update_pr_coordinator').val(data.s_msg.sup_email);
							// $('#update_pr_supervisor').val(data.s_msg.sup_email);
							// $('#update_pr_site_coordinator').val(data.s_msg.sup_email);
							// $('#update_pr_accountant').val(data.s_msg.sup_email);
							$('#update_billing_name').val(data.s_msg.billing_name);
							$('#update_billing_address').val(data.s_msg.billing_address);
							if(data.s_msg.billing_address != null && data.s_msg.billing_address != '') {
								if ($('#update_same_as_company_info').is(":checked")) {
									$('#update_same_as_company_info').prop('checked', false);
									$('#update_same_as_company_info').trigger('change');
								}
							} else {
								if ($('#update_same_as_company_info').is(":checked")) {

								}else {
									$('#update_same_as_company_info').prop('checked', true);
									$('#update_same_as_company_info').trigger('change');
								}
							}
							$('#update_pr_manager option').each(function (item,key) {
								if(jQuery.inArray(key.value, data.users.managers) >=0) {
									$('#update_pr_manager option[value='+key.value+']').attr('selected','selected')
								}else{
									this.selected=false;
								}
							});

							$('#update_pr_coordinator option').each(function (item,key) {
								if(jQuery.inArray(key.value, data.users.coordinator) >=0) {
									$('#update_pr_coordinator option[value='+key.value+']').attr('selected','selected')
								}else{
									this.selected=false;
								}
							});

							$('#update_pr_supervisor option').each(function (item,key) {
								if(jQuery.inArray(key.value, data.users.supervisor) >=0) {
									$('#update_pr_supervisor option[value='+key.value+']').attr('selected','selected')
								}else{
									this.selected=false;
								}
							});

							$('#update_pr_site_coordinator option').each(function (item,key) {
								if(jQuery.inArray(key.value, data.users.site_coordinator) >=0) {
									$('#update_pr_site_coordinator option[value='+key.value+']').attr('selected','selected')
								}else{
									this.selected=false;
								}
							});

							$('#update_pr_accountant option').each(function (item,key) {
								if(jQuery.inArray(key.value, data.users.accountant) >=0) {
									$('#update_pr_accountant option[value='+key.value+']').attr('selected','selected')
								}else{
									this.selected=false;
								}
							});

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

		$(function () {
			$('#alert_msg').delay(6000).fadeOut();
			//$('.select22, .select33').selectpicker();
			$('.alert-error, .invalid-feedback').delay(6000).fadeOut();
		});
		/****************************************
		 *       Basic Table                   *
		 ****************************************/
		$('#zero_config').DataTable();
		function gotoupdatebutton(){
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

			var pr_id = $('#pr_id').val()
			var pr_no = $('#update_pr_no').val();
			var pr_name = $('#update_pr_name').val();
			var pr_address = $('#update_pr_address').val();
			var pr_desc = $('#update_pr_desc').val();

			var pr_manager = $('#update_pr_manager').val();
			var pr_coordinator = $('#update_pr_coordinator').val();
			var pr_supervisor = $('#update_pr_supervisor').val();
			var pr_site_coordinator = $('#update_pr_site_coordinator').val();
			var pr_accountant = $('#update_pr_accountant').val();
			var update_billing_name = $('#update_billing_name').val();
			var update_billing_address = $('#update_billing_address').val();
			var update_same_as_company_info = $('#update_same_as_company_info').val();

			//var ap_quaran = $("input[name='ap_quaran']:checked").val();

			if(pr_id == ""){
				e_error = 1;
				error_message = error_message + "<br/>ID not Found, Refresh the Page and Try again.";
			}
			if(pr_no == ""){
				e_error = 1;
				$('.pr_no').html('Project Number is Required.');
			}else{
				if(!pr_no.match(alphanumerics_spaces)){
					e_error = 1;
					$('.pr_no').html('Project Number not use special carecters [without _ , . -], Check again.');
				}else{
					$('.pr_no').html('');
				}
			}
			if(pr_name == ""){
				e_error = 1;
				$('.pr_name').html('Project Name is Required.');
			}else{
				if(!pr_name.match(alphanumerics_no)){
					e_error = 1;
					$('.pr_name').html('Project Name not use special carecters [without _ / : ( @ . & ) , -], Check again.');
				}else{
					$('.pr_name').html('');
				}
			}
			if(pr_address == ""){
				e_error = 1;
				$('.pr_address').html('Project Address is Required.');
			}else{
				if(!pr_address.match(alphanumerics_no)){
					e_error = 1;
					$('.pr_address').html('Project Address not use special carecters [without _ / : ( @ . & ) , -], Check again.');
				}else{
					$('.pr_address').html('');
				}
			}

			// alert('sameasu='+update_same_as_company_info);
			if(update_same_as_company_info == 0){
				if(update_billing_name == "") {
					e_error = 1;
					$('.update_billing_name').html('Billing name is required');
				} else {
					$('.update_billing_name').html('');
				}

				if(update_billing_address == "") {
					e_error = 1;
					$('.update_billing_address').html('Billing address is required');
				} else {
					$('.update_billing_address').html('');
				}

			}else{
				update_billing_name="";
				update_billing_address="";
			}

			if(pr_desc == ""){
				e_error = 1;
				$('.pr_desc').html('Project Description is Required.');
			}else{
				if(!pr_desc.match(alphanumerics_no)){
					e_error = 1;
					$('.pr_desc').html('Project Description not use special carecters [without _ / : ( @ . & ) , -], Check again.');
				}else{
					$('.pr_desc').html('');
				}
			}

			//alert(pr_user);return;
			if(e_error == 1){
				$('.div_roller_total').fadeOut();
				$('.get_error_total').html(error_message);
				$(".get_error_total").fadeIn();
				$(".invalid-feedback").fadeIn();
				/*e_error = 0;
				error_message = '';*/
				setTimeout(function(){ $('.invalid-feedback, .get_error_total').fadeOut(); }, delay);
			}else {

				var form_data = new FormData();
				form_data.append("pr_id", pr_id);
				form_data.append("pr_no", pr_no);
				form_data.append("pr_name", pr_name);
				form_data.append("pr_address", pr_address);
				form_data.append("pr_desc", pr_desc);
				form_data.append("pr_manager", pr_manager);
				form_data.append("pr_coordinator", pr_coordinator);
				form_data.append("pr_supervisor", pr_supervisor);
				form_data.append("pr_site_coordinator", pr_site_coordinator);
				form_data.append("pr_accountant", pr_accountant);
				form_data.append("update_billing_name", update_billing_name);
				form_data.append("update_billing_address", update_billing_address);

				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/projects/modify_project_submission') ?>",
					dataType: 'json',
					data: form_data,
					contentType:false,
					cache:true,
					processData:false,
					success:function(data){
						//alert(data.msg);
						if(data.msg == 1)
						{
							//console.log(data);
							//alert(data.msg[0].space_rate);
							$('.div_roller_total').fadeOut();
							toastr.success('Record is Updated Successfully!', 'Success');
							setTimeout(function(){
								window.location.replace("<?php echo site_url('admincontrol/projects/all_project_list') ?>");
							}, 2000);

						}else{
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
			//$("#myForm").submit();
		}

	</script>
        
