<?php $this->load->view('admin/component/header') ?>


<?php $this->load->view('admin/component/menu') ?>

<style>

/* To chnage model width */
.modal-dialog {
	max-width: 60%!important;
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
				<h4 class="page-title">Check Lists</h4>
				<div class="ml-auto text-right">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a
										href="<?php echo base_url('admincontrol/dashboard'); ?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Check Lists</li>
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
						<a href="javascript:;" onclick="goto_add_record();" class="btn btn-primary float-right mb-2"
						   style="margin-right: 10px;">Add New CheckList</a>
						<div class="table-responsive">
							<table id="zero_config" class="table table-striped table-bordered">
								<thead>
								<tr style="font-weight: bold;">
									<th>Sl No.</th>
									<th>Checklist Name</th>
									<th>Checklist Frequency</th>
									<th>Action</th>
								</tr>
								</thead>
								<tbody>
								<?php foreach ($getrecord_list as $keys => $recorditem) { ?>
									<tr>
										<td><?php echo $keys + 1; ?></td>
										<td><?php echo $recorditem->cl_name; ?></td>
										<td><?php if($recorditem->cl_frequency==1){echo 'Daily';}else if($recorditem->cl_frequency==2){echo 'Weekly';}else if($recorditem->cl_frequency==3){echo 'Monthly';}else if($recorditem->cl_frequency==4){echo 'Half Yearly';}else if($recorditem->cl_frequency==5){ echo 'Yearly';} ?></td>
										<td>
											<a class="btn btn-outline-warning" 
											   	onclick="modify_record_view(<?php echo $recorditem->cl_id; ?>);"
												href="javascript:;" title="View Record"><i class="fa fa-eye text-primary"></i></a>
											<a class="btn btn-outline-warning"
											   onclick="modify_record(<?php echo $recorditem->cl_id; ?>);"
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
											<a onclick="return confirm('You are about to delete a record. This cannot be undone. Are you sure?');" href="<?php echo base_url().'admincontrol/checklist/delete_checklist/'.$recorditem->cl_id;?>" title="Delete Record"><i class="fa fa-trash text-danger"></i></a>

										</td>
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

	<!-- Add Checklist Model Start -->
	<!-- Modal -->
	<div class="modal fade" id="Modal_addrecord" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
		 aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Add New Checklist</h5>
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
								<label for="cl_name" class="col-sm-3 text-right control-label col-form-label">Checklist Name</label>
								<div class="col-sm-9">
								<input type="text" class="form-control reset-input" name="cl_name" id="cl_name" autocomplete="off"/>
									<small class="invalid-feedback cl_name"><?php echo form_error('cl_name'); ?></small>
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="cl_frequency" class="col-sm-3 text-right control-label col-form-label">Frequency</label>
								<div class="col-sm-9">
									<select class="form-control select2 custom-select reset-input" name="cl_frequency" id="cl_frequency" autocomplete="off">
										<option value="">---Select---</option>
										<option value="1">Daily</option>
										<option value="2">Weekly</option>
										<option value="3">Monthly</option>
										<option value="4">Half Yearly</option>
										<option value="5">Yearly</option>
									</select>
									<small class="invalid-feedback cl_frequency"><?php echo form_error('cl_frequency'); ?></small>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="cl_equipments" class="col-sm-3 text-right control-label col-form-label">Equipments</label>
								<div class="col-sm-9">
									<select class="form-control selectpicker reset-input" multiple
											name="cl_equipments[]" id="cl_equipments"
											autocomplete="off" data-live-search="true"
											onchange="">
										<!-- <option value="">---Select---</option> -->
										<?php foreach($assets as $asset){?>
											<option value="<?php echo $asset->eq_id;?>"><?php echo $asset->eqm_asset_name;?></option>
										<?php }?>
									</select>
									<small class="invalid-feedback cl_equipments"><?php echo form_error('cl_equipments'); ?></small>
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="cl_users" class="col-sm-3 text-right control-label col-form-label">Users</label>
								<div class="col-sm-9">
									<select class="form-control selectpicker reset-input" multiple
											name="cl_users[]" id="cl_users"
											autocomplete="off" data-live-search="true"
											onchange="">
										<!-- <option value="">---Select---</option> -->
										<?php foreach($users as $user){?>
											<option value="<?php echo $user->u_id;?>"><?php echo $user->firstname.' '.$user->lastname;?></option>
										<?php } ?>
									</select>
									<small class="invalid-feedback cl_users"><?php echo form_error('cl_users'); ?></small>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="cl_start_date" class="col-sm-3 text-right control-label col-form-label">Start Date</label>
								<div class="col-sm-9">
									<input type="date" class="form-control reset-input" name="cl_start_date" id="cl_start_date" autocomplete="off"/>
									<small class="invalid-feedback cl_start_date"><?php echo form_error('cl_start_date'); ?></small>
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
								<table id="checklist_details_table" class="table table-striped table-bordered">
									<thead>
										<tr style="font-weight: bold;">
											<th>Sl No.</th>
											<th>Checklist Item</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<tr id="row1">
											<input type="hidden" class="hidden-clitem-id" id="hidden-clitem-id1">
											<td class="row-index">1</td>
											<td>
												<textarea class="form-control clitem_description required-cli reset-input" id="clitem_description1" name="clitem_description" placeholder="Enter Description"></textarea>
												<small class="invalid-feedback clitem_description1"><?php echo form_error('clitem_description1'); ?></small>
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

	<!-- Model to edit checklist start -->
	<!-- Modal -->
	<div class="modal fade" id="Modal_editrecord" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2"
		 aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Edit Checklist</h5>
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
					
					<input type="hidden" name="hidden-cl-id" id="hidden-cl-id">
					<div class="row">
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="update_cl_name" class="col-sm-3 text-right control-label col-form-label">Checklist Name</label>
								<div class="col-sm-9">
								<input type="text" class="form-control" name="update_cl_name" id="update_cl_name" autocomplete="off"/>
									<small class="invalid-feedback update_cl_name"><?php echo form_error('cl_name'); ?></small>
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="update_cl_frequency" class="col-sm-3 text-right control-label col-form-label">Frequency</label>
								<div class="col-sm-9">
									<select class="form-control select2 custom-select" name="update_cl_frequency" id="update_cl_frequency" autocomplete="off">
										<option value="">---Select---</option>
										<option value="1">Weekly</option>
										<option value="2">Monthly</option>
										<option value="3">Half Yearly</option>
										<option value="4">Yearly</option>
									</select>
									<small class="invalid-feedback update_cl_frequency"><?php echo form_error('update_cl_frequency'); ?></small>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="update_cl_equipments" class="col-sm-3 text-right control-label col-form-label">Equipments</label>
								<div class="col-sm-9">
									<select class="form-control selectpicker" multiple
											name="update_cl_equipments[]" id="update_cl_equipments"
											autocomplete="off" data-live-search="true"
											onchange="">
										<option value="">---Select---</option>
										<?php foreach($assets as $asset){?>
											<option value="<?php echo $asset->eq_id;?>"><?php echo $asset->eqm_asset_name;?></option>
										<?php }?>
									</select>
									<small class="invalid-feedback update_cl_equipments"><?php echo form_error('update_cl_equipments'); ?></small>
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="update_cl_users" class="col-sm-3 text-right control-label col-form-label">Users</label>
								<div class="col-sm-9">
									<select class="form-control selectpicker" multiple
											name="update_cl_users[]" id="update_cl_users"
											autocomplete="off" data-live-search="true"
											onchange="">
										<option value="">---Select---</option>
										<?php foreach($users as $user){?>
											<option value="<?php echo $user->u_id;?>"><?php echo $user->firstname.' '.$user->lastname;?></option>
										<?php } ?>
									</select>
									<small class="invalid-feedback update_cl_users"><?php echo form_error('update_cl_users'); ?></small>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="update_cl_start_date" class="col-sm-3 text-right control-label col-form-label">Start Date</label>
								<div class="col-sm-9">
									<input type="date" class="form-control" name="update_cl_start_date" id="update_cl_start_date" autocomplete="off"/>
									<small class="invalid-feedback update_cl_start_date"><?php echo form_error('update_cl_start_date'); ?></small>
								</div>
							</div>
						</div>
					</div>
					<div class="row mt-4">
						<div class="col-12">
							<a href="javascript:;" onclick="update_goto_add_item();" class="btn btn-primary mb-2"
								style="margin-right: 10px;">Add New Item</a>
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							<div class="table-responsive">
								<table id="update_checklist_details_table" class="table table-striped table-bordered">
									<thead>
										<tr style="font-weight: bold;">
											<th>Sl No.</th>
											<th>Checklist Item</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<tr id="row1">
											<input type="hidden" class="update-hidden-clitem-id" id="update-hidden-clitem-id1">
											<td class="row-index">1</td>
											<td>
												<textarea class="form-control update_clitem_description update-required-cli" id="update_clitem_description1" name="update_clitem_description" placeholder="Enter Description"></textarea>
												<small class="invalid-feedback update_clitem_description1"><?php echo form_error('update_clitem_description1'); ?></small>
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
						<button type="button" onclick="gotoupdatechecklistclickbutton();" class="btn btn-primary">Submit</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Model to add new Equipment / Asset End -->
	<!-- Model to edit checklist start -->
	<!-- Modal -->
	<!-- Modal -->
	<div class="modal fade" id="Modal_viewrecord" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2"
		 aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">View Checklist</h5>
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
					
					<input type="hidden" name="hidden-cl-id" id="hidden-cl-id">
					<div class="row">
						<div class="col-12" style="margin-bottom:10px">
						<a id="print_checklist" target="_blank" class="btn btn-primary float-right mb-2" style="margin-right: 10px;">PRINT</a>
						</div>
						<div class="col-12 col-md-6">
							
							<div class="form-group row">
								<label for="view_cl_name" class="col-sm-3 text-right control-label col-form-label">Checklist Name</label>
								<div class="col-sm-9">
								<input type="text" class="form-control" name="view_cl_name" id="view_cl_name" autocomplete="off" disabled/>
									<small class="invalid-feedback view_cl_name"><?php echo form_error('cl_name'); ?></small>
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="view__cl_frequency" class="col-sm-3 text-right control-label col-form-label">Frequency</label>
								<div class="col-sm-9">
									<select class="form-control select2 custom-select" name="view_cl_frequency" id="view_cl_frequency" autocomplete="off" disbaled>
										<option value="">---Select---</option>
										<option value="1">Weekly</option>
										<option value="2">Monthly</option>
										<option value="3">Half Yearly</option>
										<option value="4">Yearly</option>
									</select>
									<small class="invalid-feedback view__cl_frequency"><?php echo form_error('view_cl_frequency'); ?></small>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="view_cl_equipments" class="col-sm-3 text-right control-label col-form-label">Equipments</label>
								<div class="col-sm-9">
									<select class="form-control selectpicker" multiple
											name="view_cl_equipments[]" id="view_cl_equipments"
											autocomplete="off" data-live-search="true"
											onchange="" disabled>
										<option value="">---Select---</option>
										<?php foreach($assets as $asset){?>
											<option value="<?php echo $asset->eq_id;?>"><?php echo $asset->eqm_asset_name;?></option>
										<?php }?>
									</select>
									<small class="invalid-feedback view_cl_equipments"><?php echo form_error('view_cl_equipments'); ?></small>
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="view_cl_users" class="col-sm-3 text-right control-label col-form-label">Users</label>
								<div class="col-sm-9">
									<select class="form-control selectpicker" multiple
											name="view_cl_users[]" id="view_cl_users"
											autocomplete="off" data-live-search="true"
											onchange="" disabled>
										<option value="">---Select---</option>
										<?php foreach($users as $user){?>
											<option value="<?php echo $user->u_id;?>"><?php echo $user->firstname.' '.$user->lastname;?></option>
										<?php } ?>
									</select>
									<small class="invalid-feedback view_cl_users"><?php echo form_error('view_cl_users'); ?></small>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="view_cl_start_date" class="col-sm-3 text-right control-label col-form-label">Start Date</label>
								<div class="col-sm-9">
									<input type="date" class="form-control" name="view_cl_start_date" id="view_cl_start_date" autocomplete="off" disabled/>
									<small class="invalid-feedback update_cl_start_date"><?php echo form_error('update_cl_start_date'); ?></small>
								</div>
							</div>
						</div>
					</div>
					<!-- <div class="row mt-4">
						<div class="col-12">
							<a href="javascript:;" onclick="update_goto_add_item();" class="btn btn-primary mb-2"
								style="margin-right: 10px;">Add New Item</a>
						</div>
					</div> -->
					<div class="row">
						<div class="col-12">
							<div class="table-responsive">
								<table id="update_checklist_details_table" class="table table-striped table-bordered">
									<thead>
										<tr style="font-weight: bold;">
											<th>Sl No.</th>
											<th>Checklist Item</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<tr id="row1">
											<input type="hidden" class="view-hidden-clitem-id" id="view-hidden-clitem-id1">
											<td class="row-index">1</td>
											<td>
												<textarea class="form-control view_clitem_description view-required-cli" id="view_clitem_description1" name="view_clitem_description" placeholder="Enter Description" disabled></textarea>
												<small class="invalid-feedback view_clitem_description1"><?php echo form_error('view_clitem_description1'); ?></small>
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
					<!-- <div class="border-top" style="padding:20px 0;text-align:right">
						<button type="button" onclick="gotoupdatechecklistclickbutton();" class="btn btn-primary">Submit</button>
					</div> -->
				</div>
			</div>
		</div>
	</div>
	<!-- Model to add new Equipment / Asset End -->
	<!-- Model to edit checklist start -->
	<!-- Modal -->
	<div class="modal fade" id="Modal_performchecklist" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2"
		 aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Perform Checklist</h5>
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
					
					<input type="hidden" name="hidden-cl-id" id="hidden-cl-id">
					<div class="row">
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="cl_eq_id" class="col-sm-3 text-right control-label col-form-label">Equipment</label>
								<div class="col-sm-9">
									<select class="form-control select2 custom-select" name="cl_eq_id" id="cl_eq_id" autocomplete="off">
										<option value="">---Select---</option>
										<?php foreach($assets as $asset){?>
											<option value="<?php echo $asset->eq_id;?>"><?php echo $asset->eqm_asset_name;?></option>
										<?php }?>
									</select>
									<small class="invalid-feedback cl_eq_id"><?php echo form_error('cl_eq_id'); ?></small>
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="cl_p_date" class="col-sm-3 text-right control-label col-form-label">Date</label>
								<div class="col-sm-9">
									<input type="date" class="form-control" name="cl_p_date" id="cl_p_date" autocomplete="off"/>
									<small class="invalid-feedback cl_p_date"><?php echo form_error('cl_p_date'); ?></small>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							<div class="table-responsive">
								<table id="perform_checklist_details_table" class="table table-striped table-bordered">
									<thead>
										<tr style="font-weight: bold;">
											<th>Sl No.</th>
											<th>Checklist Item</th>
											<th>Action</th>
											<th>Notes</th>
										</tr>
									</thead>
									<tbody>
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
					<div class="border-top" style="padding:20px 0;text-align:right">
						<button type="button" onclick="gotoperformchecklistclickbutton();" class="btn btn-primary">Submit</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Model to perform checklist End -->

	<!-- footer loder -->
	<?php $this->load->view('admin/component/footer') ?>

	<!-- javascript start -->
	<script type="text/javascript">

		$(function () {
			$('.alert-error, .invalid-feedback').delay(8000).fadeOut();
			$(".select2").selectpicker();
		});

		function goto_add_record() {
			$('.reset-input').val('').change();
			$('#Modal_addrecord').modal('show');
		}

		function goto_perform_checklist(element) {
			// alert(element);
			if (element != "") {

				var form_data = new FormData();
				form_data.append("cl_id", element);

				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/checklist/get_checklist_items') ?>",
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
							$('#hidden-cl-id').val(element);
							// $('#update_cl_name').val(data.s_msg[0].cl_name);
							// $('#update_cl_frequency').val(data.s_msg[0].cl_frequency).change();

							if(data.s_msg.length!=0){
								// alert(data.s_msg.length);
								$('#perform_checklist_details_table tbody').html('');
								for(k=0;k<data.s_msg.length;k++){
									$('#perform_checklist_details_table tbody').append('\
									<tr id="row'+(k+1)+'">\
										<input type="hidden" class="perform-hidden-clitem-id" id="perform-hidden-clitem-id'+(k+1)+'" value="'+data.s_msg[k].cli_id+'">\
										<td>'+(k+1)+'</td>\
										<td>'+data.s_msg[k].cli_item+'</td>\
										<td>\
											<select class="form-control perform_clitem_value required-perform" id="perform_clitem_value'+(k+1)+'">\
												<option value="">--select--</option>\
												<option value="P">Pass</option>\
												<option value="F">Fail</option>\
												<option value="N">NA</option>\
											</select>\
											<small class="invalid-feedback perform_clitem_value'+(k+1)+'"></small>\
										</td>\
										<td>\
											<textarea class="form-control perform_clitem_notes required-perform" id="perform_clitem_notes'+(k+1)+'"></textarea>\
											<small class="invalid-feedback perform_clitem_notes'+(k+1)+'"></small>\
										</td>\
									</tr>\
									');
								}
							}else{
							}
							// calculateTotal1();
							// $(".select2").selectpicker('refresh');
							$('#Modal_performchecklist').modal('show');

						} else {
							// $('#update_pr_no').val('');
							$('#Modal_performchecklist').modal('hide');
						}
					}
				});
			} else {
				// $('#update_pr_no').val('');
				$('#Modal_performchecklist').modal('hide');
			}
		}

		// add checklist items
		var rowCount=2;
		function goto_add_item(){
			$('#checklist_details_table tbody').append('\
			<tr id="row'+rowCount+'">\
				<input type="hidden" class="hidden-clitem-id" id="hidden-clitem-id'+rowCount+'">\
				<td class="row-index">'+rowCount+'</td>\
				<td>\
					<textarea class="form-control clitem_description required-cli reset-input" id="clitem_description'+rowCount+'" name="clitem_description" placeholder="Enter Description"></textarea>\
					<small class="invalid-feedback clitem_description'+rowCount+'"></small>\
				</td>\
				<td>\
					<a id="removeItem" href="javascript:void(0);" title="Delete Record"><i class="fa fa-trash text-danger"></i></a>\
				</td>\
			</tr>\
			');
			rowCount++;
		}

		// add update checklist items
		var rowCountUpdate=2;
		function update_goto_add_item(){
			rowCountUpdate=($('#update_checklist_details_table tbody tr').length+1);
			$('#update_checklist_details_table tbody').append('\
			<tr id="row'+rowCountUpdate+'">\
				<input type="hidden" class="update-hidden-clitem-id" id="update-hidden-clitem-id'+rowCountUpdate+'">\
				<td class="row-index">'+rowCountUpdate+'</td>\
				<td>\
					<textarea class="form-control update_clitem_description update-required-cli" id="update_clitem_description'+rowCountUpdate+'" name="update_clitem_description" placeholder="Enter Description"></textarea>\
					<small class="invalid-feedback update_clitem_description'+rowCountUpdate+'"></small>\
				</td>\
				<td>\
					<a id="removeUItem" href="javascript:void(0);" title="Delete Record"><i class="fa fa-trash text-danger"></i></a>\
				</td>\
			</tr>\
			');
			rowCountUpdate++;
		}

		var rmComponentArray= [];

		$('#checklist_details_table tbody').on('click', '#removeItem', function(){
			if (confirm('Are you sure you want to delete ?')) {
				var rowId=$(this).closest('tr').attr('id');
				var index=rowId.match(/\d+/);
				if($(this).closest('tr').children('#hidden-clitem-id'+index).val()!=""){
					rmComponentArray.push($(this).closest('tr').children('#hidden-clitem-id'+index).val());
				}
				var child=$(this).closest('tr').nextAll();
				child.each(function(){
					var id=$(this).attr('id');
					var idx=$(this).children('.row-index');
					var dig=id.match(/\d+/);
					idx.html(`${dig-1}`);
					$(this).attr('id',`row${dig-1}`);
					$(this).children('.hidden-clitem-id').attr('id',`hidden-clitem-id${dig-1}`);
					$(this).children('td').children('.clitem_description').attr('id',`clitem_description${dig-1}`);
				});
				$(this).parent().parent().remove();
				rowCount--;
			} else {
			}

			// calculateTotal();
			// alert(rmComponentArray);
    	});

		var rmComponentArray1= [];

		$('#update_checklist_details_table tbody').on('click', '#removeUItem', function(){
			if (confirm('Are you sure you want to delete ?')) {
				var rowId=$(this).closest('tr').attr('id');
				var index=rowId.match(/\d+/);
				if($(this).closest('tr').children('#update-hidden-clitem-id'+index).val()!=""){
					rmComponentArray1.push($(this).closest('tr').children('#update-hidden-clitem-id'+index).val());
				}
				var child=$(this).closest('tr').nextAll();
				child.each(function(){
					var id=$(this).attr('id');
					var idx=$(this).children('.row-index');
					var dig=id.match(/\d+/);
					idx.html(`${dig-1}`);
					$(this).attr('id',`row${dig-1}`);
					$(this).children('.update-hidden-clitem-id').attr('id',`update-hidden-clitem-id${dig-1}`);
					$(this).children('td').children('.update_clitem_description').attr('id',`update_clitem_description${dig-1}`);
				});
				$(this).parent().parent().remove();
				rowCountUpdate--;
			} else {
			}

			// calculateTotal();
			// alert(rmComponentArray);
    	});

		// Add checklist function
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

			var cl_name = $('#cl_name').val();
			var cl_frequency = $('#cl_frequency').val();
			var cl_eq_ids = $('#cl_equipments').val();
			var cl_user_ids = $('#cl_users').val();
			var cl_start_date= $('#cl_start_date').val();

			if (cl_name == "") {
				e_error = 1;
				$('.cl_name').html('Name is Required.');
			} else {
				$('.cl_name').html('');
			}

			if (cl_frequency == "") {
				e_error = 1;
				$('.cl_frequency').html('Frequency is Required.');
			} else {
				$('.cl_frequency').html('');
			}

			if (cl_eq_ids == "") {
				e_error = 1;
				$('.cl_equipments').html('Equipments are Required.');
			} else {
				$('.cl_equipments').html('');
			}

			if (cl_user_ids == "") {
				e_error = 1;
				$('.cl_users').html('Users are Required.');
			} else {
				$('.cl_users').html('');
			}

			if (cl_start_date == "") {
				e_error = 1;
				$('.cl_start_date').html('Start Date is Required.');
			} else {
				$('.cl_start_date').html('');
			}

			$('.required-cli').each(function(){
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
				form_data.append("cl_name", cl_name);
				form_data.append("cl_frequency", cl_frequency);
				form_data.append("cl_eq_ids", JSON.stringify(cl_eq_ids));
				form_data.append("cl_user_ids", JSON.stringify(cl_user_ids));
				form_data.append("cl_start_date", cl_start_date);
				
				var tableRow=0;
				for(i=1;i<=$('#checklist_details_table tbody tr').length;i++){
					form_data.append("clitem_description"+i, $('#clitem_description'+i).val());
					tableRow++;
				}

				form_data.append("row_count", tableRow);

				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/checklist/new_checklist_submission') ?>",
					dataType: 'json',
					data: form_data,
					contentType: false,
					cache: true,
					processData: false,
					success: function (data) {
						// alert(JSON.stringify(data.s_msg));
						if (data.msg == 1) {
							//console.log(data);
							// alert(JSON.stringify(data.s_msg));
							// alert(data.msg[0].space_rate);
							$('.div_roller_total').fadeOut();
							toastr.success('Record is Inserted Successfully!', 'Success');
							setTimeout(function () {
								window.location.replace("<?php echo site_url('admincontrol/checklist/all_checklist_list') ?>");
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

		// Trigers when edit is pressed for a particular Checklist
		function modify_record(element) {
			// alert(element);
			if (element != "") {

				var form_data = new FormData();
				form_data.append("cl_id", element);

				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/checklist/get_details_of_checklist') ?>",
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
							var assetArray=JSON.parse(data.s_msg[0].cl_eq_ids);
							var usersArray=JSON.parse(data.s_msg[0].cl_user_ids);
							$('#hidden-cl-id').val(data.s_msg[0].cl_id);
							$('#update_cl_name').val(data.s_msg[0].cl_name);
							$('#update_cl_frequency').val(data.s_msg[0].cl_frequency).change();
							$('#update_cl_start_date').val(data.s_msg[0].cl_start_date);

							for(i=0;i<assetArray.length;i++){
								// alert(usersArray[i]);
								$('#update_cl_equipments option[value="'+assetArray[i]+'"]').attr('selected','selected');
							}

							for(j=0;j<usersArray.length;j++){
								// alert(usersArray[i]);
								$('#update_cl_users option[value="'+usersArray[j]+'"]').attr('selected','selected');
							}

							$('#update_cl_equipments, #update_cl_users').change();
							if(data.c_msg.length!=0){
								// alert(data.c_msg.length);
								$('#update_checklist_details_table tbody').html('');
								for(k=0;k<data.c_msg.length;k++){
									if(k==0){
									$('#update_checklist_details_table tbody').append('\
									<tr id="row'+(k+1)+'">\
										<input type="hidden" class="update-hidden-clitem-id" id="update-hidden-clitem-id'+(k+1)+'" value="'+data.c_msg[k].cli_id+'">\
										<td>'+(k+1)+'</td>\
										<td>\
											<textarea class="form-control update_clitem_description update-required-clitem" id="update_clitem_description'+(k+1)+'" name="update_clitem_description" placeholder="Enter Description">'+data.c_msg[k].cli_item+'</textarea>\
											<small class="invalid-feedback update_clitem_description'+(k+1)+'"></small>\
										</td>\
										<td>\
										</td>\
									</tr>\
									');
										
									}else{
									$('#update_checklist_details_table tbody').append('\
									<tr id="row'+(k+1)+'">\
										<input type="hidden" class="update-hidden-clitem-id" id="update-hidden-clitem-id'+(k+1)+'" value="'+data.c_msg[k].cli_id+'">\
										<td>'+(k+1)+'</td>\
										<td>\
											<textarea class="form-control update_clitem_description update-required-clitem" id="update_clitem_description'+(k+1)+'" name="update_clitem_description" placeholder="Enter Description">'+data.c_msg[k].cli_item+'</textarea>\
											<small class="invalid-feedback update_clitem_description'+(k+1)+'"></small>\
										</td>\
										<td>\
											<a id="removeUItem" href="javascript:void(0);" title="Delete Record"><i class="fa fa-trash text-danger"></i></a>\
										</td>\
									</tr>\
									');
									}
								}
							}else{
								$('#update_checklist_details_table tbody').html('');
								// $('#update_eqm_details_table tbody').append('\
								// 	<tr id="row1">\
								// 		<td colspan="9" style="text-align:center">\
								// 			NO MAINTENANCE AVAILABLE\
								// 		</td>\
								// 	</tr>\
								// ');
							}
							// calculateTotal1();
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

		// Trigers when view is pressed for a particular Checklist
		function modify_record_view(element) {
			// alert(element);
			if (element != "") {

				var form_data = new FormData();
				form_data.append("cl_id", element);

				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/checklist/get_details_of_checklist') ?>",
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
							var assetArray=JSON.parse(data.s_msg[0].cl_eq_ids);
							var usersArray=JSON.parse(data.s_msg[0].cl_user_ids);
							$('#hidden-cl-id').val(data.s_msg[0].cl_id);
							$('#view_cl_name').val(data.s_msg[0].cl_name);
							$('#view_cl_frequency').val(data.s_msg[0].cl_frequency).change();
							$('#view_cl_start_date').val(data.s_msg[0].cl_start_date);
							$('#print_checklist').attr('href','print_checklist_setpdf/'+data.s_msg[0].cl_id);

							for(i=0;i<assetArray.length;i++){
								// alert(usersArray[i]);
								$('#view_cl_equipments option[value="'+assetArray[i]+'"]').attr('selected','selected');
							}

							for(j=0;j<usersArray.length;j++){
								// alert(usersArray[i]);
								$('#view_cl_users option[value="'+usersArray[j]+'"]').attr('selected','selected');
							}

							$('#view_cl_equipments, #view_cl_users').change();
							if(data.c_msg.length!=0){
								// alert(data.c_msg.length);
								$('#view_checklist_details_table tbody').html('');
								for(k=0;k<data.c_msg.length;k++){
									if(k==0){
									$('#view_checklist_details_table tbody').append('\
									<tr id="row'+(k+1)+'">\
										<input type="hidden" class="view-hidden-clitem-id" id="view-hidden-clitem-id'+(k+1)+'" value="'+data.c_msg[k].cli_id+'">\
										<td>'+(k+1)+'</td>\
										<td>\
											<textarea class="form-control update_clitem_description view-required-clitem" id="view_clitem_description'+(k+1)+'" name="view_clitem_description" placeholder="Enter Description" disabled>'+data.c_msg[k].cli_item+'</textarea> \
											<small class="invalid-feedback update_clitem_description'+(k+1)+'"></small>\
										</td>\
										<td>\
										</td>\
									</tr>\
									');
										
									}else{
									$('#update_checklist_details_table tbody').append('\
									<tr id="row'+(k+1)+'">\
										<input type="hidden" class="view-hidden-clitem-id" id="view-hidden-clitem-id'+(k+1)+'" value="'+data.c_msg[k].cli_id+'">\
										<td>'+(k+1)+'</td>\
										<td>\
											<textarea class="form-control view_clitem_description view-required-clitem" id="view_clitem_description'+(k+1)+'" name="view_clitem_description" placeholder="Enter Description" disabled>'+data.c_msg[k].cli_item+'</textarea> \
											<small class="invalid-feedback view_clitem_description'+(k+1)+'"></small>\
										</td>\
										<td>\
										</td>\
									</tr>\
									');
									}
								}
							}else{
								$('#update_checklist_details_table tbody').html('');
								// $('#update_eqm_details_table tbody').append('\
								// 	<tr id="row1">\
								// 		<td colspan="9" style="text-align:center">\
								// 			NO MAINTENANCE AVAILABLE\
								// 		</td>\
								// 	</tr>\
								// ');
							}
							// calculateTotal1();
							$(".select2").selectpicker('refresh');
							$('#Modal_viewrecord').modal('show');

						} else {
							$('#update_pr_no').val('');
							$('#Modal_viewrecord').modal('hide');
						}
					}
				});
			} else {
				$('#update_pr_no').val('');
				$('#Modal_editrecord').modal('hide');
			}
		}

		// Update Checklist function
		function gotoupdatechecklistclickbutton() {
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

			var cl_id = $('#hidden-cl-id').val();
			var cl_name = $('#update_cl_name').val();
			var cl_frequency = $('#update_cl_frequency').val();
			var cl_eq_ids = $('#update_cl_equipments').val();
			var cl_user_ids = $('#update_cl_users').val();
			var cl_start_date= $('#update_cl_start_date').val();

			if (cl_name == "") {
				e_error = 1;
				$('.update_cl_name').html('Name is Required.');
			} else {
				$('.update_cl_name').html('');
			}

			if (cl_frequency == "") {
				e_error = 1;
				$('.update_cl_frequency').html('Frequency is Required.');
			} else {
				$('.update_cl_frequency').html('');
			}

			if (cl_eq_ids == "") {
				e_error = 1;
				$('.update_cl_equipments').html('Equipments are Required.');
			} else {
				$('.update_cl_equipments').html('');
			}

			if (cl_user_ids == "") {
				e_error = 1;
				$('.update_cl_users').html('Users are Required.');
			} else {
				$('.update_cl_users').html('');
			}

			if (cl_start_date == "") {
				e_error = 1;
				$('.update_cl_start_date').html('Start Date is Required.');
			} else {
				$('.update_cl_start_date').html('');
			}

			$('.update-required-clitem').each(function(){
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
				// alert(JSON.stringify(rmComponentArray1));
				var form_data = new FormData();
				form_data.append("cl_id", cl_id);
				form_data.append("cl_name", cl_name);
				form_data.append("cl_frequency", cl_frequency);
				form_data.append("cl_eq_ids", JSON.stringify(cl_eq_ids));
				form_data.append("cl_user_ids", JSON.stringify(cl_user_ids));
				form_data.append("cl_start_date", cl_start_date);
				form_data.append("cli_delete_ids", JSON.stringify(rmComponentArray1));
				
				var tableRow=0;
				for(i=1;i<=$('#update_checklist_details_table tbody tr').length;i++){
					form_data.append("cli_id"+i, $('#update-hidden-clitem-id'+i).val());
					form_data.append("clitem_description"+i, $('#update_clitem_description'+i).val());
					tableRow++;
				}

				form_data.append("row_count", tableRow);

				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/checklist/update_checklist_submission') ?>",
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
								window.location.replace("<?php echo site_url('admincontrol/checklist/all_checklist_list') ?>");
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

		// perform checklist function
		function gotoperformchecklistclickbutton() {
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

			var cl_id = $('#hidden-cl-id').val();
			var cl_eq_id = $('#cl_eq_id').val();
			var cl_p_date = $('#cl_p_date').val();

			if (cl_eq_id == "") {
				e_error = 1;
				$('.cl_eq_id').html('Equipment is Required.');
			} else {
				$('.cl_ncl_eq_idame').html('');
			}

			if (cl_p_date == "") {
				e_error = 1;
				$('.cl_p_date').html('Date is Required.');
			} else {
				$('.cl_p_date').html('');
			}

			$('.required-perform').each(function(){
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
				form_data.append("cl_id", cl_id);
				form_data.append("cl_eq_id", cl_eq_id);
				form_data.append("cl_p_date", cl_p_date);
				
				// var tableRow=0;
				var performArray=[];
				for(i=1;i<=$('#perform_checklist_details_table tbody tr').length;i++){
					// form_data.append("perform_clitem_value"+i, $('#perform_clitem_value'+i).val());
					// form_data.append("perform_clitem_notes"+i, $('#perform_clitem_notes'+i).val());
					// tableRow++;
					performArray.push({
						id: $('#perform-hidden-clitem-id'+i).val(), 
						value:  $('#perform_clitem_value'+i).val(),
						notes:  $('#perform_clitem_notes'+i).val()
					});
				}

				form_data.append("cl_item_values", JSON.stringify(performArray));

				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/checklist/perform_checklist_submission') ?>",
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
								window.location.replace("<?php echo site_url('admincontrol/checklist/all_checklist_list') ?>");
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



		/****************************************
		 *       Create data tables             *
		 ****************************************/
		$('#zero_config').DataTable();

	</script>	
	<!-- javascript ens -->
        
