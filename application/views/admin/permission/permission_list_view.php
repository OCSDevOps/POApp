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
				<h4 class="page-title">Permission Template List</h4>
				<div class="ml-auto text-right">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a
										href="<?php echo base_url('admincontrol/dashboard'); ?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Permission Template List</li>
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
						   style="margin-right: 10px;">Add New Template</a>
						<div class="table-responsive">
							<table id="zero_config" class="table table-striped table-bordered">
								<thead>
								<tr style="font-weight: bold;">
									<th>Sl No.</th>
									<th>Template Name</th>
									<th>Action</th>
								</tr>
								</thead>
								<tbody>
								<?php foreach ($getrecord_list as $keys => $recorditem) { ?>
									<tr>
										<td><?php echo $keys + 1; ?></td>
										<td><?php echo $recorditem->pt_template_name; ?></td>
										</td>
										<td>
											<a class="btn btn-outline-warning"
											   onclick="modify_record(<?php echo $recorditem->pt_id; ?>);"
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
											<a onclick="return confirm('You are about to delete a record. This cannot be undone. Are you sure?');" href="<?php echo base_url().'admincontrol/permissions/delete_permission_template/'.$recorditem->pt_id;
											?>" title="Delete Record"><i class="fa fa-trash text-danger"></i></a>

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

	<!-- Add Maintenance Model Start -->
	<!-- Modal -->
	<div class="modal fade" id="Modal_addrecord" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
		 aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Add New Permission Template</h5>
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
						<div class="col-12 col-md-7">
							<div class="form-group row">
								<label for="pt_template_name" class="col-sm-5 text-right control-label col-form-label">Permission Template Name</label>
								<div class="col-sm-7">
									<input type="text" class="form-control reset-input" name="pt_template_name" id="pt_template_name"
										placeholder="Enter Template Name" autocomplete="off"/>
									<small class="invalid-feedback pt_template_name"><?php echo form_error('pt_template_name'); ?></small>
								</div>
							</div>
						</div>
						<div class="col-12 col-md-5">
							<div class="form-group row">
								<label for="pt_template_users" class="col-sm-5 text-right control-label col-form-label">Users</label>
								<div class="col-sm-7">
									<select class="form-control selectpicker reset-input" multiple
											name="pt_template_users[]" id="pt_template_users"
											autocomplete="off" data-live-search="true"
											onchange="">
										<?php
										// $users = json_decode($notification_record->notify_users_list);
										foreach ($users as $user) { ?>
											<option value="<?php echo $user->u_id ?>"><?php echo $user->firstname . ' ' . $user->lastname ?></option>
										<?php } ?>
									</select>
									<small class="invalid-feedback pt_template_users"><?php echo form_error('pt_template_users'); ?></small>
								</div>
							</div>
						</div>
					</div>
					<div class="row" style="padding:20px">
						<div class="table-responsive">
							<table class="table table-bordered" id="permissions-table">
								<thead>
									<tr class="bg-primary text-light font-weight-bold">
										<td>Permissions</td>
										<td class="text-center">
											NONE<br>
											<input class="reset-radio" type="radio" name="pt_template" id="" value="none">
										</td>
										<td class="text-center">
											READ ONLY<br>
											<input class="reset-radio" type="radio" name="pt_template" id="" value="readonly">
										</td>
										<td class="text-center">
											STANDARD<br>
											<input class="reset-radio" type="radio" name="pt_template" id="" value="standard">
										</td>
										<td class="text-center">
											ADMIN<br>
											<input class="reset-radio" type="radio" name="pt_template" id="" value="admin">
										</td>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td colspan="5" class="text-center bg-light font-weight-bold">Tasks</td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Purchase Order<br>
											<small class="invalid-feedback pt_t_porder"><?php echo form_error('pt_t_porder'); ?></small>
										</td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_t_porder" id="" value="4"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_t_porder" id="" value="3"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_t_porder" id="" value="2"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_t_porder" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Rental Order<br>
											<small class="invalid-feedback pt_t_rorder"><?php echo form_error('pt_t_rorder'); ?></small>
										</td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_t_rorder" id="" value="4"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_t_rorder" id="" value="3"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_t_rorder" id="" value="2"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_t_rorder" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Receive Order<br>
											<small class="invalid-feedback pt_t_rcorder"><?php echo form_error('pt_t_rcorder'); ?></small>
										</td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_t_rcorder" id="" value="4"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_t_rcorder" id="" value="3"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_t_rcorder" id="" value="2"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_t_rcorder" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Request Form Quote<br>
											<small class="invalid-feedback pt_t_rfq"><?php echo form_error('pt_t_rfq'); ?></small>
										</td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_t_rfq" id="" value="4"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_t_rfq" id="" value="3"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_t_rfq" id="" value="2"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_t_rfq" id="" value="1"></td>
									</tr>
									<tr>
										<td colspan="5" class="text-center bg-light font-weight-bold">Master Setup</td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Item Categories<br>
											<small class="invalid-feedback pt_m_item"><?php echo form_error('pt_m_item'); ?></small>
										</td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_m_item" id="" value="4"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_m_item" id="" value="3"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_m_item" id="" value="2"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_m_item" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Unit of Measures<br>
											<small class="invalid-feedback pt_m_uom"><?php echo form_error('pt_m_uom'); ?></small>
										</td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_m_uom" id="" value="4"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_m_uom" id="" value="3"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_m_uom" id="" value="2"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_m_uom" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Cost Code<br>
											<small class="invalid-feedback pt_m_costcode"><?php echo form_error('pt_m_costcode'); ?></small>
										</td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_m_costcode" id="" value="4"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_m_costcode" id="" value="3"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_m_costcode" id="" value="2"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_m_costcode" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Projects<br>
											<small class="invalid-feedback pt_m_projects"><?php echo form_error('pt_m_projects'); ?></small>
										</td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_m_projects" id="" value="4"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_m_projects" id="" value="3"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_m_projects" id="" value="2"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_m_projects" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Suppliers<br>
											<small class="invalid-feedback pt_m_suppliers"><?php echo form_error('pt_m_suppliers'); ?></small>
										</td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_m_suppliers" id="" value="4"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_m_suppliers" id="" value="3"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_m_suppliers" id="" value="2"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_m_suppliers" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Tax Group<br>
											<small class="invalid-feedback pt_m_taxgroup"><?php echo form_error('pt_m_taxgroup'); ?></small>
										</td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_m_taxgroup" id="" value="4"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_m_taxgroup" id="" value="3"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_m_taxgroup" id="" value="2"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_m_taxgroup" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Budget<br>
											<small class="invalid-feedback pt_m_budget"><?php echo form_error('pt_m_budget'); ?></small>
										</td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_m_budget" id="" value="4"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_m_budget" id="" value="3"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_m_budget" id="" value="2"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_m_budget" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Email Template<br>
											<small class="invalid-feedback pt_m_email"><?php echo form_error('pt_m_email'); ?></small>
										</td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_m_email" id="" value="4"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_m_email" id="" value="3"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_m_email" id="" value="2"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_m_email" id="" value="1"></td>
									</tr>
									<tr>
										<td colspan="5" class="text-center bg-light font-weight-bold">Item Setup</td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Items<br>
											<small class="invalid-feedback pt_i_item"><?php echo form_error('pt_i_item'); ?></small>
										</td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_i_item" id="" value="4"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_i_item" id="" value="3"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_i_item" id="" value="2"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_i_item" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Item Packages<br>
											<small class="invalid-feedback pt_i_itemp"><?php echo form_error('pt_i_itemp'); ?></small>
										</td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_i_itemp" id="" value="4"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_i_itemp" id="" value="3"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_i_itemp" id="" value="2"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_i_itemp" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Supplier catalogs<br>
											<small class="invalid-feedback pt_i_supplierc"><?php echo form_error('pt_i_suplierc'); ?></small>
										</td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_i_supplierc" id="" value="4"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_i_supplierc" id="" value="3"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_i_supplierc" id="" value="2"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_i_supplierc" id="" value="1"></td>
									</tr>
									<tr>
										<td colspan="5" class="text-center bg-light font-weight-bold">Equipment Setup</td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Equipment Master<br>
											<small class="invalid-feedback pt_e_eq"><?php echo form_error('pt_e_eq'); ?></small>
										</td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_e_eq" id="" value="4"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_e_eq" id="" value="3"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_e_eq" id="" value="2"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_e_eq" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Equipment Maintenance<br>
											<small class="invalid-feedback pt_e_eqm"><?php echo form_error('pt_e_eqm'); ?></small>
										</td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_e_eqm" id="" value="4"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_e_eqm" id="" value="3"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_e_eqm" id="" value="2"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_e_eqm" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Equipment Checklist<br>
											<small class="invalid-feedback pt_e_checklist"><?php echo form_error('pt_e_checklist'); ?></small>
										</td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_e_checklist" id="" value="4"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_e_checklist" id="" value="3"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_e_checklist" id="" value="2"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_e_checklist" id="" value="1"></td>
									</tr>
									<tr>
										<td colspan="5" class="text-center bg-light font-weight-bold">App Settings</td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											User Setup<br>
											<small class="invalid-feedback pt_a_user"><?php echo form_error('pt_a_user'); ?></small>
										</td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_a_user" id="" value="4"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_a_user" id="" value="3"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_a_user" id="" value="2"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_a_user" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Permissions<br>
											<small class="invalid-feedback pt_a_permissions"><?php echo form_error('pt_a_permissions'); ?></small>
										</td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_a_permissions" id="" value="4"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_a_permissions" id="" value="3"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_a_permissions" id="" value="2"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_a_permissions" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Company Information<br>
											<small class="invalid-feedback pt_a_cinfo"><?php echo form_error('pt_a_cinfo'); ?></small>
										</td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_a_cinfo" id="" value="4"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_a_cinfo" id="" value="3"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_a_cinfo" id="" value="2"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_a_cinfo" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Procore Integaration<br>
											<small class="invalid-feedback pt_a_procore"><?php echo form_error('pt_a_procore'); ?></small>
										</td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_a_procore" id="" value="4"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_a_procore" id="" value="3"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_a_procore" id="" value="2"></td>
										<td class="text-center"><input class="reset-radio" type="radio" name="pt_a_procore" id="" value="1"></td>
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

	<!-- Model to edit permission template start -->
	<!-- Modal -->
	<div class="modal fade" id="Modal_editrecord" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2"
		 aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Edit Permission Template</h5>
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
					<input type="hidden" name="hidden-pt-id" id="hidden-pt-id">
					<div class="row">
						<div class="col-12 col-md-7">
							<div class="form-group row">
								<label for="update_pt_template_name" class="col-sm-5 text-right control-label col-form-label">Permission Template Name</label>
								<div class="col-sm-7">
									<input type="text" class="form-control" name="update_pt_template_name" id="update_pt_template_name"
										placeholder="Enter Template Name" autocomplete="off"/>
									<small class="invalid-feedback update_pt_template_name"><?php echo form_error('update_pt_template_name'); ?></small>
								</div>
							</div>
						</div>
						<div class="col-12 col-md-5">
							<div class="form-group row">
								<label for="update_pt_template_users" class="col-sm-5 text-right control-label col-form-label">Users</label>
								<div class="col-sm-7">
									<select class="form-control selectpicker" multiple
											name="update_pt_template_users[]" id="update_pt_template_users"
											autocomplete="off" data-live-search="true"
											onchange="">
										<?php
										// $users = json_decode($notification_record->notify_users_list);
										foreach ($users as $user) { ?>
											<option value="<?php echo $user->u_id ?>"><?php echo $user->firstname . ' ' . $user->lastname ?></option>
										<?php } ?>
									</select>
									<small class="invalid-feedback update_pt_template_users"><?php echo form_error('update_pt_template_users'); ?></small>
								</div>
							</div>
						</div>
					</div>
					<div class="row" style="padding:20px">
						<div class="table-responsive">
							<table class="table table-bordered" id="permissions-table">
								<thead>
									<tr class="bg-primary text-light font-weight-bold">
										<td>Permissions</td>
										<td class="text-center">NONE</td>
										<td class="text-center">READ ONLY</td>
										<td class="text-center">STANDARD</td>
										<td class="text-center">ADMIN</td>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td colspan="5" class="text-center bg-light font-weight-bold">Tasks</td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Purchase Order<br>
											<small class="invalid-feedback update_pt_t_porder"><?php echo form_error('update_pt_t_porder'); ?></small>
										</td>
										<td class="text-center"><input type="radio" name="update_pt_t_porder" id="" value="4"></td>
										<td class="text-center"><input type="radio" name="update_pt_t_porder" id="" value="3"></td>
										<td class="text-center"><input type="radio" name="update_pt_t_porder" id="" value="2"></td>
										<td class="text-center"><input type="radio" name="update_pt_t_porder" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Rental Order<br>
											<small class="invalid-feedback update_pt_t_rorder"><?php echo form_error('update_pt_t_rorder'); ?></small>
										</td>
										<td class="text-center"><input type="radio" name="update_pt_t_rorder" id="" value="4"></td>
										<td class="text-center"><input type="radio" name="update_pt_t_rorder" id="" value="3"></td>
										<td class="text-center"><input type="radio" name="update_pt_t_rorder" id="" value="2"></td>
										<td class="text-center"><input type="radio" name="update_pt_t_rorder" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Receive Order<br>
											<small class="invalid-feedback update_pt_t_rcorder"><?php echo form_error('update_pt_t_rcorder'); ?></small>
										</td>
										<td class="text-center"><input type="radio" name="update_pt_t_rcorder" id="" value="4"></td>
										<td class="text-center"><input type="radio" name="update_pt_t_rcorder" id="" value="3"></td>
										<td class="text-center"><input type="radio" name="update_pt_t_rcorder" id="" value="2"></td>
										<td class="text-center"><input type="radio" name="update_pt_t_rcorder" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Request Form Quote<br>
											<small class="invalid-feedback update_pt_t_rfq"><?php echo form_error('update_pt_t_rfq'); ?></small>
										</td>
										<td class="text-center"><input type="radio" name="update_pt_t_rfq" id="" value="4"></td>
										<td class="text-center"><input type="radio" name="update_pt_t_rfq" id="" value="3"></td>
										<td class="text-center"><input type="radio" name="update_pt_t_rfq" id="" value="2"></td>
										<td class="text-center"><input type="radio" name="update_pt_t_rfq" id="" value="1"></td>
									</tr>
									<tr>
										<td colspan="5" class="text-center bg-light font-weight-bold">Master Setup</td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Item Categories<br>
											<small class="invalid-feedback update_pt_m_item"><?php echo form_error('update_pt_m_item'); ?></small>
										</td>
										<td class="text-center"><input type="radio" name="update_pt_m_item" id="" value="4"></td>
										<td class="text-center"><input type="radio" name="update_pt_m_item" id="" value="3"></td>
										<td class="text-center"><input type="radio" name="update_pt_m_item" id="" value="2"></td>
										<td class="text-center"><input type="radio" name="update_pt_m_item" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Unit of Measures<br>
											<small class="invalid-feedback update_pt_m_uom"><?php echo form_error('update_pt_m_uom'); ?></small>
										</td>
										<td class="text-center"><input type="radio" name="update_pt_m_uom" id="" value="4"></td>
										<td class="text-center"><input type="radio" name="update_pt_m_uom" id="" value="3"></td>
										<td class="text-center"><input type="radio" name="update_pt_m_uom" id="" value="2"></td>
										<td class="text-center"><input type="radio" name="update_pt_m_uom" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Cost Code<br>
											<small class="invalid-feedback update_pt_m_costcode"><?php echo form_error('update_pt_m_costcode'); ?></small>
										</td>
										<td class="text-center"><input type="radio" name="update_pt_m_costcode" id="" value="4"></td>
										<td class="text-center"><input type="radio" name="update_pt_m_costcode" id="" value="3"></td>
										<td class="text-center"><input type="radio" name="update_pt_m_costcode" id="" value="2"></td>
										<td class="text-center"><input type="radio" name="update_pt_m_costcode" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Projects<br>
											<small class="invalid-feedback update_pt_m_projects"><?php echo form_error('update_pt_m_projects'); ?></small>
										</td>
										<td class="text-center"><input type="radio" name="update_pt_m_projects" id="" value="4"></td>
										<td class="text-center"><input type="radio" name="update_pt_m_projects" id="" value="3"></td>
										<td class="text-center"><input type="radio" name="update_pt_m_projects" id="" value="2"></td>
										<td class="text-center"><input type="radio" name="update_pt_m_projects" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Suppliers<br>
											<small class="invalid-feedback update_pt_m_suppliers"><?php echo form_error('update_pt_m_suppliers'); ?></small>
										</td>
										<td class="text-center"><input type="radio" name="update_pt_m_suppliers" id="" value="4"></td>
										<td class="text-center"><input type="radio" name="update_pt_m_suppliers" id="" value="3"></td>
										<td class="text-center"><input type="radio" name="update_pt_m_suppliers" id="" value="2"></td>
										<td class="text-center"><input type="radio" name="update_pt_m_suppliers" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Tax Group<br>
											<small class="invalid-feedback update_pt_m_taxgroup"><?php echo form_error('update_pt_m_taxgroup'); ?></small>
										</td>
										<td class="text-center"><input type="radio" name="update_pt_m_taxgroup" id="" value="4"></td>
										<td class="text-center"><input type="radio" name="update_pt_m_taxgroup" id="" value="3"></td>
										<td class="text-center"><input type="radio" name="update_pt_m_taxgroup" id="" value="2"></td>
										<td class="text-center"><input type="radio" name="update_pt_m_taxgroup" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Budget<br>
											<small class="invalid-feedback update_pt_m_budget"><?php echo form_error('update_pt_m_budget'); ?></small>
										</td>
										<td class="text-center"><input type="radio" name="update_pt_m_budget" id="" value="4"></td>
										<td class="text-center"><input type="radio" name="update_pt_m_budget" id="" value="3"></td>
										<td class="text-center"><input type="radio" name="update_pt_m_budget" id="" value="2"></td>
										<td class="text-center"><input type="radio" name="update_pt_m_budget" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Email Template<br>
											<small class="invalid-feedback update_pt_m_email"><?php echo form_error('update_pt_m_email'); ?></small>
										</td>
										<td class="text-center"><input type="radio" name="update_pt_m_email" id="" value="4"></td>
										<td class="text-center"><input type="radio" name="update_pt_m_email" id="" value="3"></td>
										<td class="text-center"><input type="radio" name="update_pt_m_email" id="" value="2"></td>
										<td class="text-center"><input type="radio" name="update_pt_m_email" id="" value="1"></td>
									</tr>
									<tr>
										<td colspan="5" class="text-center bg-light font-weight-bold">Item Setup</td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Items<br>
											<small class="invalid-feedback update_pt_i_item"><?php echo form_error('update_pt_i_item'); ?></small>
										</td>
										<td class="text-center"><input type="radio" name="update_pt_i_item" id="" value="4"></td>
										<td class="text-center"><input type="radio" name="update_pt_i_item" id="" value="3"></td>
										<td class="text-center"><input type="radio" name="update_pt_i_item" id="" value="2"></td>
										<td class="text-center"><input type="radio" name="update_pt_i_item" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Item Packages<br>
											<small class="invalid-feedback update_pt_i_itemp"><?php echo form_error('update_pt_i_itemp'); ?></small>
										</td>
										<td class="text-center"><input type="radio" name="update_pt_i_itemp" id="" value="4"></td>
										<td class="text-center"><input type="radio" name="update_pt_i_itemp" id="" value="3"></td>
										<td class="text-center"><input type="radio" name="update_pt_i_itemp" id="" value="2"></td>
										<td class="text-center"><input type="radio" name="update_pt_i_itemp" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Supplier catalogs<br>
											<small class="invalid-feedback update_pt_i_supplierc"><?php echo form_error('update_pt_i_suplierc'); ?></small>
										</td>
										<td class="text-center"><input type="radio" name="update_pt_i_supplierc" id="" value="4"></td>
										<td class="text-center"><input type="radio" name="update_pt_i_supplierc" id="" value="3"></td>
										<td class="text-center"><input type="radio" name="update_pt_i_supplierc" id="" value="2"></td>
										<td class="text-center"><input type="radio" name="update_pt_i_supplierc" id="" value="1"></td>
									</tr>
									<tr>
										<td colspan="5" class="text-center bg-light font-weight-bold">Equipment Setup</td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Equipment Master<br>
											<small class="invalid-feedback update_pt_e_eq"><?php echo form_error('update_pt_e_eq'); ?></small>
										</td>
										<td class="text-center"><input type="radio" name="update_pt_e_eq" id="" value="4"></td>
										<td class="text-center"><input type="radio" name="update_pt_e_eq" id="" value="3"></td>
										<td class="text-center"><input type="radio" name="update_pt_e_eq" id="" value="2"></td>
										<td class="text-center"><input type="radio" name="update_pt_e_eq" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Equipment Maintenance<br>
											<small class="invalid-feedback update_pt_e_eqm"><?php echo form_error('update_pt_e_eqm'); ?></small>
										</td>
										<td class="text-center"><input type="radio" name="update_pt_e_eqm" id="" value="4"></td>
										<td class="text-center"><input type="radio" name="update_pt_e_eqm" id="" value="3"></td>
										<td class="text-center"><input type="radio" name="update_pt_e_eqm" id="" value="2"></td>
										<td class="text-center"><input type="radio" name="update_pt_e_eqm" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Equipment Checklist<br>
											<small class="invalid-feedback update_pt_e_checklist"><?php echo form_error('update_pt_e_checklist'); ?></small>
										</td>
										<td class="text-center"><input type="radio" name="update_pt_e_checklist" id="" value="4"></td>
										<td class="text-center"><input type="radio" name="update_pt_e_checklist" id="" value="3"></td>
										<td class="text-center"><input type="radio" name="update_pt_e_checklist" id="" value="2"></td>
										<td class="text-center"><input type="radio" name="update_pt_e_checklist" id="" value="1"></td>
									</tr>
									<tr>
										<td colspan="5" class="text-center bg-light font-weight-bold">App Settings</td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											User Setup<br>
											<small class="invalid-feedback update_pt_a_user"><?php echo form_error('update_pt_a_user'); ?></small>
										</td>
										<td class="text-center"><input type="radio" name="update_pt_a_user" id="" value="4"></td>
										<td class="text-center"><input type="radio" name="update_pt_a_user" id="" value="3"></td>
										<td class="text-center"><input type="radio" name="update_pt_a_user" id="" value="2"></td>
										<td class="text-center"><input type="radio" name="update_pt_a_user" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Permissions<br>
											<small class="invalid-feedback update_pt_a_permissions"><?php echo form_error('update_pt_a_permissions'); ?></small>
										</td>
										<td class="text-center"><input type="radio" name="update_pt_a_permissions" id="" value="4"></td>
										<td class="text-center"><input type="radio" name="update_pt_a_permissions" id="" value="3"></td>
										<td class="text-center"><input type="radio" name="update_pt_a_permissions" id="" value="2"></td>
										<td class="text-center"><input type="radio" name="update_pt_a_permissions" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Company Information<br>
											<small class="invalid-feedback update_pt_a_cinfo"><?php echo form_error('update_pt_a_cinfo'); ?></small>
										</td>
										<td class="text-center"><input type="radio" name="update_pt_a_cinfo" id="" value="4"></td>
										<td class="text-center"><input type="radio" name="update_pt_a_cinfo" id="" value="3"></td>
										<td class="text-center"><input type="radio" name="update_pt_a_cinfo" id="" value="2"></td>
										<td class="text-center"><input type="radio" name="update_pt_a_cinfo" id="" value="1"></td>
									</tr>
									<tr>
										<td class="font-weight-bold">
											Procore Integaration<br>
											<small class="invalid-feedback update_pt_a_procore"><?php echo form_error('update_pt_a_procore'); ?></small>
										</td>
										<td class="text-center"><input type="radio" name="update_pt_a_procore" id="" value="4"></td>
										<td class="text-center"><input type="radio" name="update_pt_a_procore" id="" value="3"></td>
										<td class="text-center"><input type="radio" name="update_pt_a_procore" id="" value="2"></td>
										<td class="text-center"><input type="radio" name="update_pt_a_procore" id="" value="1"></td>
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
						<button type="button" onclick="gotoupdatepermissionsclickbutton();" class="btn btn-primary">Submit</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Model to add new Equipment / Asset End -->

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
			$('.reset-radio').prop('checked',false);
			$('#Modal_addrecord').modal('show');
		}

		// Add New Permission Template function
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

			var pt_template_name = $('#pt_template_name').val();
			var pt_template_users = $('#pt_template_users').val();

			var pt_t_porder = $('input[name="pt_t_porder"]:checked').val();
			var pt_t_rorder = $('input[name="pt_t_rorder"]:checked').val();
			var pt_t_rcorder = $('input[name="pt_t_rcorder"]:checked').val();
			var pt_t_rfq = $('input[name="pt_t_rfq"]:checked').val();

			var pt_m_item = $('input[name="pt_m_item"]:checked').val();
			var pt_m_uom = $('input[name="pt_m_uom"]:checked').val();
			var pt_m_costcode = $('input[name="pt_m_costcode"]:checked').val();
			var pt_m_projects = $('input[name="pt_m_projects"]:checked').val();
			var pt_m_suppliers = $('input[name="pt_m_suppliers"]:checked').val();
			var pt_m_taxgroup = $('input[name="pt_m_taxgroup"]:checked').val();
			var pt_m_budget = $('input[name="pt_m_budget"]:checked').val();
			var pt_m_email = $('input[name="pt_m_email"]:checked').val();

			var pt_i_item = $('input[name="pt_i_item"]:checked').val();
			var pt_i_itemp = $('input[name="pt_i_itemp"]:checked').val();
			var pt_i_supplierc = $('input[name="pt_i_supplierc"]:checked').val();

			var pt_e_eq = $('input[name="pt_e_eq"]:checked').val();
			var pt_e_eqm = $('input[name="pt_e_eqm"]:checked').val();
			var pt_e_checklist = $('input[name="pt_e_checklist"]:checked').val();

			var pt_a_user = $('input[name="pt_a_user"]:checked').val();
			var pt_a_permissions = $('input[name="pt_a_permissions"]:checked').val();
			var pt_a_cinfo = $('input[name="pt_a_cinfo"]:checked').val();
			var pt_a_procore = $('input[name="pt_a_procore"]:checked').val();

			if (pt_template_name == "") {
				e_error = 1;
				$('.pt_template_name').html('Template Name is Required.');
			} else {
				$('.pt_template_name').html('');
			}

			if ($('input:radio[name="pt_t_porder"]').is(':checked')) {
				$('.pt_t_porder').html('');
			} else {
				e_error = 1;
				$('.pt_t_porder').html('Required.');
			}

			if ($('input:radio[name="pt_t_rorder"]').is(':checked')) {
				$('.pt_t_rorder').html('');
			} else {
				e_error = 1;
				$('.pt_t_rorder').html('Required.');
			}

			if ($('input:radio[name="pt_t_rcorder"]').is(':checked')) {
				$('.pt_t_rcorder').html('');
			} else {
				e_error = 1;
				$('.pt_t_rcorder').html('Required.');
			}

			if ($('input:radio[name="pt_t_rfq"]').is(':checked')) {
				$('.pt_t_rfq').html('');
			} else {
				e_error = 1;
				$('.pt_t_rfq').html('Required.');
			}

			if ($('input:radio[name="pt_m_item"]').is(':checked')) {
				$('.pt_m_item').html('');
			} else {
				e_error = 1;
				$('.pt_m_item').html('Required.');
			}

			if ($('input:radio[name="pt_m_uom"]').is(':checked')) {
				$('.pt_m_uom').html('');
			} else {
				e_error = 1;
				$('.pt_m_uom').html('Required.');
			}

			if ($('input:radio[name="pt_m_costcode"]').is(':checked')) {
				$('.pt_m_costcode').html('');
			} else {
				e_error = 1;
				$('.pt_m_costcode').html('Required.');
			}

			if ($('input:radio[name="pt_m_projects"]').is(':checked')) {
				$('.pt_m_projects').html('');
			} else {
				e_error = 1;
				$('.pt_m_projects').html('Required.');
			}

			if ($('input:radio[name="pt_m_suppliers"]').is(':checked')) {
				$('.pt_m_suppliers').html('');
			} else {
				e_error = 1;
				$('.pt_m_suppliers').html('Required.');
			}

			if ($('input:radio[name="pt_m_taxgroup"]').is(':checked')) {
				$('.pt_m_taxgroup').html('');
			} else {
				e_error = 1;
				$('.pt_m_taxgroup').html('Required.');
			}

			if ($('input:radio[name="pt_m_budget"]').is(':checked')) {
				$('.pt_m_budget').html('');
			} else {
				e_error = 1;
				$('.pt_m_budget').html('Required.');
			}

			if ($('input:radio[name="pt_m_email"]').is(':checked')) {
				$('.pt_m_email').html('');
			} else {
				e_error = 1;
				$('.pt_m_email').html('Required.');
			}

			if ($('input:radio[name="pt_i_item"]').is(':checked')) {
				$('.pt_i_item').html('');
			} else {
				e_error = 1;
				$('.pt_i_item').html('Required.');
			}

			if ($('input:radio[name="pt_i_itemp"]').is(':checked')) {
				$('.pt_i_itemp').html('');
			} else {
				e_error = 1;
				$('.pt_i_itemp').html('Required.');
			}

			if ($('input:radio[name="pt_i_supplierc"]').is(':checked')) {
				$('.pt_i_supplierc').html('');
			} else {
				e_error = 1;
				$('.pt_i_supplierc').html('Required.');
			}

			if ($('input:radio[name="pt_e_eq"]').is(':checked')) {
				$('.pt_e_eq').html('');
			} else {
				e_error = 1;
				$('.pt_e_eq').html('Required.');
			}

			if ($('input:radio[name="pt_e_eqm"]').is(':checked')) {
				$('.pt_e_eqm').html('');
			} else {
				e_error = 1;
				$('.pt_e_eqm').html('Required.');
			}

			if ($('input:radio[name="pt_e_checklist"]').is(':checked')) {
				$('.pt_e_checklist').html('');
			} else {
				e_error = 1;
				$('.pt_e_checklist').html('Required.');
			}

			if ($('input:radio[name="pt_a_user"]').is(':checked')) {
				$('.pt_a_user').html('');
			} else {
				e_error = 1;
				$('.pt_a_user').html('Required.');
			}

			if ($('input:radio[name="pt_a_permissions"]').is(':checked')) {
				$('.pt_a_permissions').html('');
			} else {
				e_error = 1;
				$('.pt_a_permissions').html('Required.');
			}

			if ($('input:radio[name="pt_a_cinfo"]').is(':checked')) {
				$('.pt_a_cinfo').html('');
			} else {
				e_error = 1;
				$('.pt_a_cinfo').html('Required.');
			}

			if ($('input:radio[name="pt_a_procore"]').is(':checked')) {
				$('.pt_a_procore').html('');
			} else {
				e_error = 1;
				$('.pt_a_procore').html('Required.');
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
				form_data.append("pt_template_name", pt_template_name);
				form_data.append("pt_template_users", JSON.stringify(pt_template_users));

				form_data.append("pt_t_porder", pt_t_porder);
				form_data.append("pt_t_rorder", pt_t_rorder);
				form_data.append("pt_t_rcorder", pt_t_rcorder);
				form_data.append("pt_t_rfq", pt_t_rfq);

				form_data.append("pt_m_item", pt_m_item);
				form_data.append("pt_m_uom", pt_m_uom);
				form_data.append("pt_m_costcode", pt_m_costcode);
				form_data.append("pt_m_projects", pt_m_projects);
				form_data.append("pt_m_suppliers", pt_m_suppliers);
				form_data.append("pt_m_taxgroup", pt_m_taxgroup);
				form_data.append("pt_m_budget", pt_m_budget);
				form_data.append("pt_m_email", pt_m_email);

				form_data.append("pt_i_item", pt_i_item);
				form_data.append("pt_i_itemp", pt_i_itemp);
				form_data.append("pt_i_supplierc", pt_i_supplierc);

				form_data.append("pt_e_eq", pt_e_eq);
				form_data.append("pt_e_eqm", pt_e_eqm);
				form_data.append("pt_e_checklist", pt_e_checklist);

				form_data.append("pt_a_user", pt_a_user);
				form_data.append("pt_a_permissions", pt_a_permissions);
				form_data.append("pt_a_cinfo", pt_a_cinfo);
				form_data.append("pt_a_procore", pt_a_procore);

				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/permissions/new_permission_submission') ?>",
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
								window.location.replace("<?php echo site_url('admincontrol/permissions/all_permissions_list') ?>");
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

		// update Permission Template function
		function gotoupdatepermissionsclickbutton() {
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

			var pt_id = $('#hidden-pt-id').val();
			var pt_template_name = $('#update_pt_template_name').val();
			var pt_template_users = $('#update_pt_template_users').val();
			var pt_t_porder = $('input[name="update_pt_t_porder"]:checked').val();
			var pt_t_rorder = $('input[name="update_pt_t_rorder"]:checked').val();
			var pt_t_rcorder = $('input[name="update_pt_t_rcorder"]:checked').val();
			var pt_t_rfq = $('input[name="update_pt_t_rfq"]:checked').val();

			var pt_m_item = $('input[name="update_pt_m_item"]:checked').val();
			var pt_m_uom = $('input[name="update_pt_m_uom"]:checked').val();
			var pt_m_costcode = $('input[name="update_pt_m_costcode"]:checked').val();
			var pt_m_projects = $('input[name="update_pt_m_projects"]:checked').val();
			var pt_m_suppliers = $('input[name="update_pt_m_suppliers"]:checked').val();
			var pt_m_taxgroup = $('input[name="update_pt_m_taxgroup"]:checked').val();
			var pt_m_budget = $('input[name="update_pt_m_budget"]:checked').val();
			var pt_m_email = $('input[name="update_pt_m_email"]:checked').val();

			var pt_i_item = $('input[name="update_pt_i_item"]:checked').val();
			var pt_i_itemp = $('input[name="update_pt_i_itemp"]:checked').val();
			var pt_i_supplierc = $('input[name="update_pt_i_supplierc"]:checked').val();

			var pt_e_eq = $('input[name="update_pt_e_eq"]:checked').val();
			var pt_e_eqm = $('input[name="update_pt_e_eqm"]:checked').val();
			var pt_e_checklist= $('input[name="update_pt_e_checklist"]:checked').val();

			var pt_a_user = $('input[name="update_pt_a_user"]:checked').val();
			var pt_a_permissions = $('input[name="update_pt_a_permissions"]:checked').val();
			var pt_a_cinfo = $('input[name="update_pt_a_cinfo"]:checked').val();
			var pt_a_procore = $('input[name="update_pt_a_procore"]:checked').val();

			if (pt_template_name == "") {
				e_error = 1;
				$('.update_pt_template_name').html('Template Name is Required.');
			} else {
				$('.update_pt_template_name').html('');
			}

			if (pt_template_users == "") {
				e_error = 1;
				$('.update_pt_template_users').html('Atleast 1 user is Required.');
			} else {
				$('.update_pt_template_users').html('');
			}

			if ($('input:radio[name="update_pt_t_porder"]').is(':checked')) {
				$('.update_pt_t_porder').html('');
			} else {
				e_error = 1;
				$('.update_pt_t_porder').html('Required.');
			}

			if ($('input:radio[name="update_pt_t_rorder"]').is(':checked')) {
				$('.update_pt_t_rorder').html('');
			} else {
				e_error = 1;
				$('.update_pt_t_rorder').html('Required.');
			}

			if ($('input:radio[name="update_pt_t_rcorder"]').is(':checked')) {
				$('.update_pt_t_rcorder').html('');
			} else {
				e_error = 1;
				$('.update_pt_t_rcorder').html('Required.');
			}

			if ($('input:radio[name="update_pt_t_rfq"]').is(':checked')) {
				$('.update_pt_t_rfq').html('');
			} else {
				e_error = 1;
				$('.update_pt_t_rfq').html('Required.');
			}

			if ($('input:radio[name="update_pt_m_item"]').is(':checked')) {
				$('.update_pt_m_item').html('');
			} else {
				e_error = 1;
				$('.update_pt_m_item').html('Required.');
			}

			if ($('input:radio[name="update_pt_m_uom"]').is(':checked')) {
				$('.update_pt_m_uom').html('');
			} else {
				e_error = 1;
				$('.update_pt_m_uom').html('Required.');
			}

			if ($('input:radio[name="update_pt_m_costcode"]').is(':checked')) {
				$('.update_pt_m_costcode').html('');
			} else {
				e_error = 1;
				$('.update_pt_m_costcode').html('Required.');
			}

			if ($('input:radio[name="update_pt_m_projects"]').is(':checked')) {
				$('.update_pt_m_projects').html('');
			} else {
				e_error = 1;
				$('.update_pt_m_projects').html('Required.');
			}

			if ($('input:radio[name="update_pt_m_suppliers"]').is(':checked')) {
				$('.update_pt_m_suppliers').html('');
			} else {
				e_error = 1;
				$('.update_pt_m_suppliers').html('Required.');
			}

			if ($('input:radio[name="update_pt_m_taxgroup"]').is(':checked')) {
				$('.update_pt_m_taxgroup').html('');
			} else {
				e_error = 1;
				$('.update_pt_m_taxgroup').html('Required.');
			}

			if ($('input:radio[name="update_pt_m_budget"]').is(':checked')) {
				$('.update_pt_m_budget').html('');
			} else {
				e_error = 1;
				$('.update_pt_m_budget').html('Required.');
			}

			if ($('input:radio[name="update_pt_m_email"]').is(':checked')) {
				$('.update_pt_m_email').html('');
			} else {
				e_error = 1;
				$('.update_pt_m_email').html('Required.');
			}

			if ($('input:radio[name="update_pt_i_item"]').is(':checked')) {
				$('.update_pt_i_item').html('');
			} else {
				e_error = 1;
				$('.update_pt_i_item').html('Required.');
			}

			if ($('input:radio[name="update_pt_i_itemp"]').is(':checked')) {
				$('.update_pt_i_itemp').html('');
			} else {
				e_error = 1;
				$('.update_pt_i_itemp').html('Required.');
			}

			if ($('input:radio[name="update_pt_i_supplierc"]').is(':checked')) {
				$('.update_pt_i_supplierc').html('');
			} else {
				e_error = 1;
				$('.update_pt_i_supplierc').html('Required.');
			}

			if ($('input:radio[name="update_pt_e_eq"]').is(':checked')) {
				$('.update_pt_e_eq').html('');
			} else {
				e_error = 1;
				$('.update_pt_e_eq').html('Required.');
			}

			if ($('input:radio[name="update_pt_e_eqm"]').is(':checked')) {
				$('.update_pt_e_eqm').html('');
			} else {
				e_error = 1;
				$('.update_pt_e_eqm').html('Required.');
			}

			if ($('input:radio[name="update_pt_e_checklist"]').is(':checked')) {
				$('.update_pt_e_checklist').html('');
			} else {
				e_error = 1;
				$('.update_pt_e_checklist').html('Required.');
			}

			if ($('input:radio[name="update_pt_a_user"]').is(':checked')) {
				$('.update_pt_a_user').html('');
			} else {
				e_error = 1;
				$('.update_pt_a_user').html('Required.');
			}

			if ($('input:radio[name="update_pt_a_permissions"]').is(':checked')) {
				$('.update_pt_a_permissions').html('');
			} else {
				e_error = 1;
				$('.update_pt_a_permissions').html('Required.');
			}

			if ($('input:radio[name="update_pt_a_cinfo"]').is(':checked')) {
				$('.update_pt_a_cinfo').html('');
			} else {
				e_error = 1;
				$('.update_pt_a_cinfo').html('Required.');
			}

			if ($('input:radio[name="update_pt_a_procore"]').is(':checked')) {
				$('.update_pt_a_procore').html('');
			} else {
				e_error = 1;
				$('.update_pt_a_procore').html('Required.');
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
				var form_data1 = new FormData();
				form_data1.append("pt_id", pt_id);
				form_data1.append("pt_template_name", pt_template_name);
				form_data1.append("pt_template_users", JSON.stringify(pt_template_users));
				form_data1.append("pt_t_porder", pt_t_porder);
				form_data1.append("pt_t_rorder", pt_t_rorder);
				form_data1.append("pt_t_rcorder", pt_t_rcorder);
				form_data1.append("pt_t_rfq", pt_t_rfq);

				form_data1.append("pt_m_item", pt_m_item);
				form_data1.append("pt_m_uom", pt_m_uom);
				form_data1.append("pt_m_costcode", pt_m_costcode);
				form_data1.append("pt_m_projects", pt_m_projects);
				form_data1.append("pt_m_suppliers", pt_m_suppliers);
				form_data1.append("pt_m_taxgroup", pt_m_taxgroup);
				form_data1.append("pt_m_budget", pt_m_budget);
				form_data1.append("pt_m_email", pt_m_email);

				form_data1.append("pt_i_item", pt_i_item);
				form_data1.append("pt_i_itemp", pt_i_itemp);
				form_data1.append("pt_i_supplierc", pt_i_supplierc);

				form_data1.append("pt_e_eq", pt_e_eq);
				form_data1.append("pt_e_eqm", pt_e_eqm);
				form_data1.append("pt_e_checklist", pt_e_checklist);

				form_data1.append("pt_a_user", pt_a_user);
				form_data1.append("pt_a_permissions", pt_a_permissions);
				form_data1.append("pt_a_cinfo", pt_a_cinfo);
				form_data1.append("pt_a_procore", pt_a_procore);
				// alert(JSON.stringify(form_data1));

				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/permissions/update_permission_submission') ?>",
					dataType: 'json',
					data: form_data1,
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
								window.location.replace("<?php echo site_url('admincontrol/permissions/all_permissions_list') ?>");
							}, 2000);

						} else {
							$('.div_roller_total').fadeOut();
							$('#submit_record_btn').prop('disabled', false);
							$('.close_modal').show();
							error_message = data.e_msg;
							alert(data.msg);
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

		// Trigers when edit is pressed for a particular Permission Template
		function modify_record(element) {
			// alert(element);
			if (element != "") {

				var form_data = new FormData();
				form_data.append("pt_id", element);

				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/permissions/get_details_of_permissions') ?>",
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
							var usersArray=JSON.parse(data.s_msg[0].pt_template_users);
							$('#hidden-pt-id').val(data.s_msg[0].pt_id);
							$('#update_pt_template_name').val(data.s_msg[0].pt_template_name);
							$('input[name="update_pt_t_porder"][value="'+data.s_msg[0].pt_t_porder+'"]').prop('checked',true);
							$('input[name="update_pt_t_rorder"][value="'+data.s_msg[0].pt_t_rorder+'"]').prop('checked',true);
							$('input[name="update_pt_t_rcorder"][value="'+data.s_msg[0].pt_t_rcorder+'"]').prop('checked',true);
							$('input[name="update_pt_t_rfq"][value="'+data.s_msg[0].pt_t_rfq+'"]').prop('checked',true);
							$('input[name="update_pt_m_item"][value="'+data.s_msg[0].pt_m_item+'"]').prop('checked',true);
							$('input[name="update_pt_m_uom"][value="'+data.s_msg[0].pt_m_uom+'"]').prop('checked',true);
							$('input[name="update_pt_m_costcode"][value="'+data.s_msg[0].pt_m_costcode+'"]').prop('checked',true);
							$('input[name="update_pt_m_projects"][value="'+data.s_msg[0].pt_m_projects+'"]').prop('checked',true);
							$('input[name="update_pt_m_suppliers"][value="'+data.s_msg[0].pt_m_suppliers+'"]').prop('checked',true);
							$('input[name="update_pt_m_taxgroup"][value="'+data.s_msg[0].pt_m_taxgroup+'"]').prop('checked',true);
							$('input[name="update_pt_m_budget"][value="'+data.s_msg[0].pt_m_budget+'"]').prop('checked',true);
							$('input[name="update_pt_m_email"][value="'+data.s_msg[0].pt_m_email+'"]').prop('checked',true);
							$('input[name="update_pt_i_item"][value="'+data.s_msg[0].pt_i_item+'"]').prop('checked',true);
							$('input[name="update_pt_i_itemp"][value="'+data.s_msg[0].pt_i_itemp+'"]').prop('checked',true);
							$('input[name="update_pt_i_supplierc"][value="'+data.s_msg[0].pt_i_supplierc+'"]').prop('checked',true);
							$('input[name="update_pt_e_eq"][value="'+data.s_msg[0].pt_e_eq+'"]').prop('checked',true);
							$('input[name="update_pt_e_eqm"][value="'+data.s_msg[0].pt_e_eqm+'"]').prop('checked',true);
							$('input[name="update_pt_e_checklist"][value="'+data.s_msg[0].pt_e_checklist+'"]').prop('checked',true);
							$('input[name="update_pt_a_user"][value="'+data.s_msg[0].pt_a_user+'"]').prop('checked',true);
							$('input[name="update_pt_a_permissions"][value="'+data.s_msg[0].pt_a_permissions+'"]').prop('checked',true);
							$('input[name="update_pt_a_cinfo"][value="'+data.s_msg[0].pt_a_cinfo+'"]').prop('checked',true);
							$('input[name="update_pt_a_procore"][value="'+data.s_msg[0].pt_a_procore+'"]').prop('checked',true);

							for(i=0;i<usersArray.length;i++){
								// alert(usersArray[i]);
								$('#update_pt_template_users option[value="'+usersArray[i]+'"]').attr('selected','selected');
							}
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

		$('input:radio[name="pt_template"]').on('change',function(){
			var templateValue=$(this).val();
			$('input:radio[name="pt_t_porder"]').prop('checked',false);
				$('input:radio[name="pt_t_rorder"]').prop('checked',false);
				$('input:radio[name="pt_t_rcorder"]').prop('checked',false);
				$('input:radio[name="pt_t_rfq"]').prop('checked',false);

				$('input:radio[name="pt_m_item"]').prop('checked',false);
				$('input:radio[name="pt_m_uom"]').prop('checked',false);
				$('input:radio[name="pt_m_costcode"]').prop('checked',false);
				$('input:radio[name="pt_m_projects"]').prop('checked',false);
				$('input:radio[name="pt_m_suppliers"]').prop('checked',false);
				$('input:radio[name="pt_m_taxgroup"]').prop('checked',false);
				$('input:radio[name="pt_m_budget"]').prop('checked',false);
				$('input:radio[name="pt_m_email"]').prop('checked',false);

				$('input:radio[name="pt_i_item"]').prop('checked',false);
				$('input:radio[name="pt_i_itemp"]').prop('checked',false);
				$('input:radio[name="pt_i_supplierc"]').prop('checked',false);

				$('input:radio[name="pt_e_eq"]').prop('checked',false);
				$('input:radio[name="pt_e_eqm"]').prop('checked',false);

				$('input:radio[name="pt_a_user"]').prop('checked',false);
				$('input:radio[name="pt_a_permissions"]').prop('checked',false);
				$('input:radio[name="pt_a_cinfo"]').prop('checked',false);
				$('input:radio[name="pt_a_procore"]').prop('checked',false);
			if(templateValue == 'none'){
				$('input:radio[name="pt_t_porder"][value="4"]').prop('checked',true);
				$('input:radio[name="pt_t_rorder"][value="4"]').prop('checked',true);
				$('input:radio[name="pt_t_rcorder"][value="4"]').prop('checked',true);
				$('input:radio[name="pt_t_rfq"][value="4"]').prop('checked',true);

				$('input:radio[name="pt_m_item"][value="4"]').prop('checked',true);
				$('input:radio[name="pt_m_uom"][value="4"]').prop('checked',true);
				$('input:radio[name="pt_m_costcode"][value="4"]').prop('checked',true);
				$('input:radio[name="pt_m_projects"][value="4"]').prop('checked',true);
				$('input:radio[name="pt_m_suppliers"][value="4"]').prop('checked',true);
				$('input:radio[name="pt_m_taxgroup"][value="4"]').prop('checked',true);
				$('input:radio[name="pt_m_budget"][value="4"]').prop('checked',true);
				$('input:radio[name="pt_m_email"][value="4"]').prop('checked',true);

				$('input:radio[name="pt_i_item"][value="4"]').prop('checked',true);
				$('input:radio[name="pt_i_itemp"][value="4"]').prop('checked',true);
				$('input:radio[name="pt_i_supplierc"][value="4"]').prop('checked',true);

				$('input:radio[name="pt_e_eq"][value="4"]').prop('checked',true);
				$('input:radio[name="pt_e_eqm"][value="4"]').prop('checked',true);

				$('input:radio[name="pt_a_user"][value="4"]').prop('checked',true);
				$('input:radio[name="pt_a_permissions"][value="4"]').prop('checked',true);
				$('input:radio[name="pt_a_cinfo"][value="4"]').prop('checked',true);
				$('input:radio[name="pt_a_procore"][value="4"]').prop('checked',true);
			}else if(templateValue == 'readonly'){
				$('input:radio[name="pt_t_porder"][value="3"]').prop('checked',true);
				$('input:radio[name="pt_t_rorder"][value="3"]').prop('checked',true);
				$('input:radio[name="pt_t_rcorder"][value="3"]').prop('checked',true);
				$('input:radio[name="pt_t_rfq"][value="3"]').prop('checked',true);

				$('input:radio[name="pt_m_item"][value="3"]').prop('checked',true);
				$('input:radio[name="pt_m_uom"][value="3"]').prop('checked',true);
				$('input:radio[name="pt_m_costcode"][value="3"]').prop('checked',true);
				$('input:radio[name="pt_m_projects"][value="3"]').prop('checked',true);
				$('input:radio[name="pt_m_suppliers"][value="3"]').attr('checked',true);
				$('input:radio[name="pt_m_taxgroup"][value="3"]').prop('checked',true);
				$('input:radio[name="pt_m_budget"][value="3"]').prop('checked',true);
				$('input:radio[name="pt_m_email"][value="3"]').prop('checked',true);

				$('input:radio[name="pt_i_item"][value="3"]').prop('checked',true);
				$('input:radio[name="pt_i_itemp"][value="3"]').prop('checked',true);
				$('input:radio[name="pt_i_supplierc"][value="3"]').prop('checked',true);

				$('input:radio[name="pt_e_eq"][value="3"]').prop('checked',true);
				$('input:radio[name="pt_e_eqm"][value="3"]').prop('checked',true);

				$('input:radio[name="pt_a_user"][value="3"]').prop('checked',true);
				$('input:radio[name="pt_a_permissions"][value="3"]').prop('checked',true);
				$('input:radio[name="pt_a_cinfo"][value="3"]').prop('checked',true);
				$('input:radio[name="pt_a_procore"][value="3"]').prop('checked',true);
			}else if(templateValue == 'standard'){
				$('input:radio[name="pt_t_porder"][value="2"]').prop('checked',true);
				$('input:radio[name="pt_t_rorder"][value="2"]').prop('checked',true);
				$('input:radio[name="pt_t_rcorder"][value="2"]').prop('checked',true);
				$('input:radio[name="pt_t_rfq"][value="2"]').prop('checked',true);

				$('input:radio[name="pt_m_item"][value="2"]').prop('checked',true);
				$('input:radio[name="pt_m_uom"][value="2"]').prop('checked',true);
				$('input:radio[name="pt_m_costcode"][value="2"]').prop('checked',true);
				$('input:radio[name="pt_m_projects"][value="2"]').prop('checked',true);
				$('input:radio[name="pt_m_suppliers"][value="2"]').prop('checked',true);
				$('input:radio[name="pt_m_taxgroup"][value="2"]').prop('checked',true);
				$('input:radio[name="pt_m_budget"][value="2"]').prop('checked',true);
				$('input:radio[name="pt_m_email"][value="2"]').prop('checked',true);

				$('input:radio[name="pt_i_item"][value="2"]').prop('checked',true);
				$('input:radio[name="pt_i_itemp"][value="2"]').prop('checked',true);
				$('input:radio[name="pt_i_supplierc"][value="2"]').prop('checked',true);

				$('input:radio[name="pt_e_eq"][value="2"]').prop('checked',true);
				$('input:radio[name="pt_e_eqm"][value="2"]').prop('checked',true);

				$('input:radio[name="pt_a_user"][value="2"]').prop('checked',true);
				$('input:radio[name="pt_a_permissions"][value="2"]').prop('checked',true);
				$('input:radio[name="pt_a_cinfo"][value="2"]').prop('checked',true);
				$('input:radio[name="pt_a_procore"][value="2"]').prop('checked',true);
			}else if(templateValue == 'admin'){
				$('input:radio[name="pt_t_porder"][value="1"]').prop('checked',true);
				$('input:radio[name="pt_t_rorder"][value="1"]').prop('checked',true);
				$('input:radio[name="pt_t_rcorder"][value="1"]').prop('checked',true);
				$('input:radio[name="pt_t_rfq"][value="1"]').prop('checked',true);

				$('input:radio[name="pt_m_item"][value="1"]').prop('checked',true);
				$('input:radio[name="pt_m_uom"][value="1"]').prop('checked',true);
				$('input:radio[name="pt_m_costcode"][value="1"]').prop('checked',true);
				$('input:radio[name="pt_m_projects"][value="1"]').prop('checked',true);
				$('input:radio[name="pt_m_suppliers"][value="1"]').prop('checked',true);
				$('input:radio[name="pt_m_taxgroup"][value="1"]').prop('checked',true);
				$('input:radio[name="pt_m_budget"][value="1"]').prop('checked',true);
				$('input:radio[name="pt_m_email"][value="1"]').prop('checked',true);

				$('input:radio[name="pt_i_item"][value="1"]').prop('checked',true);
				$('input:radio[name="pt_i_itemp"][value="1"]').prop('checked',true);
				$('input:radio[name="pt_i_supplierc"][value="1"]').prop('checked',true);

				$('input:radio[name="pt_e_eq"][value="1"]').prop('checked',true);
				$('input:radio[name="pt_e_eqm"][value="1"]').prop('checked',true);

				$('input:radio[name="pt_a_user"][value="1"]').prop('checked',true);
				$('input:radio[name="pt_a_permissions"][value="1"]').prop('checked',true);
				$('input:radio[name="pt_a_cinfo"][value="1"]').prop('checked',true);
				$('input:radio[name="pt_a_procore"][value="1"]').prop('checked',true);
			}
		});


		/****************************************
		 *       Create data tables             *
		 ****************************************/
		$('#zero_config').DataTable();

	</script>	
	<!-- javascript ens -->
        
