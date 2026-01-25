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
				<h4 class="page-title">Company Details</h4>
				<div class="ml-auto text-right">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a
										href="<?php echo base_url('admincontrol/dashboard'); ?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Company Details</li>
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
					<div class="card-body">

						<nav>
							<div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
								<a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home"
								   role="tab" aria-controls="nav-home" aria-selected="true">Company Details</a>
								<?php 
								if($this->session->userdata('utype')==1 || $templateDetails->pt_a_cinfo<3){?>
									<a class="nav-item nav-link" id="nav-insurance-tab" data-toggle="tab" href="#nav-insurance"
									role="tab" aria-controls="nav-insurance" aria-selected="false">Insurance
										Settings</a>
									<a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile"
									role="tab" aria-controls="nav-profile" aria-selected="false">Notification
										Settings</a>
									<a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact"
									role="tab" aria-controls="nav-contact" aria-selected="false">Email Setup</a>
								<?php }?>
							</div>
						</nav>

						<div class="tab-content" id="nav-tabContent">
							<div class="tab-pane fade show active" id="nav-home" role="tabpanel"
								 aria-labelledby="nav-home-tab">
								<div class="card-body">
									<form action="" method="post" enctype="multipart/form-data" id="myForm">
										<?php if ($this->session->flashdata('success')) { ?>
											<div id="alert_msg"
												 class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
											<?php $this->session->unset_userdata('success');
										} elseif ($this->session->flashdata('e_error')) { ?>
											<div id="alert_msg"
												 class="alert alert-danger"><?php echo $this->session->flashdata('e_error'); ?></div>
											<?php $this->session->unset_userdata('e_error');
										} ?>

										<?php if (isset($error)) { ?>
											<div class="alert alert-danger alert-error">
												<h4>Error!</h4>
												<?php echo $error; ?>
											</div>
										<?php } ?>

										<div class="form-group row">
											<label for="fname" class="col-sm-3 text-right control-label col-form-label">Company
												Name</label>
											<div class="col-sm-9">
												<input type="text" class="form-control" name="cname" id="cname"
													   placeholder="Enter Company Name"
													   value="<?php echo $getrecord_list->company_name; ?>"
													   autocomplete="off" 
								<?php if(!empty($templateDetails) && $templateDetails->pt_a_cinfo>=3){ echo 'readonly';}?>/>
												<small class="invalid-feedback cname"><?php echo form_error('cname'); ?></small>
											</div>

										</div>
										<div class="form-group row">
											<label for="cono1" class="col-sm-3 text-right control-label col-form-label">Address</label>
											<div class="col-sm-6">
											<textarea class="form-control" name="c_address" id="c_address"
													  placeholder="Enter Company Address" <?php if(!empty($templateDetails) && $templateDetails->pt_a_cinfo>=3){ echo 'readonly';}?>><?php echo set_value('c_address'); ?><?php echo $getrecord_list->company_address; ?></textarea>
												<small class="invalid-feedback c_address"><?php echo form_error('c_address'); ?></small>
											</div>
										</div>
										<div class="form-group row">
											<label for="fname" class="col-sm-3 text-right control-label col-form-label">Company
												Logo</label>
											<div class="col-sm-9">
												<input type="file" class="form-control" name="c_logo" id="c_logo"
													   autocomplete="off" <?php if(!empty($templateDetails) && $templateDetails->pt_a_cinfo>=3){ echo 'disabled';}?>/>
												<small class="invalid-feedback c_logo"><?php echo form_error('c_logo'); ?></small>
											</div>
											<label for="fname" class="col-sm-3 text-right control-label col-form-label"></label>
											<div class="col-sm-9 text-left">
												<?php if (!empty($getrecord_list->company_logo)) { ?>
													<img src="<?php echo base_url('upload_file/company/' . $getrecord_list->company_logo); ?>"
														 alt="Company Logo loading..." class="img-fluid" width="100" height="100"
														 style="max-width:500px;"/>
													<?php //}else{ ?>
													<!--<img src="<?php //echo base_url(); ?>style/assets/images/logo-text.png" alt="homepage" class="img-fluid" />-->
												<?php } ?>
											</div>
										</div>
										<div class="form-group row">
											<label for="fname" class="col-sm-3 text-right control-label col-form-label">App Logo 1</label>
											<div class="col-sm-9">
												<input type="file" class="form-control" name="app_logo_one" id="app_logo_one"
													   autocomplete="off" <?php if(!empty($templateDetails) && $templateDetails->pt_a_cinfo>=3){ echo 'disabled';}?>/>
												<small class="invalid-feedback app_logo_one"><?php echo form_error('app_logo_one'); ?></small>
											</div>
											<label for="fname" class="col-sm-3 text-right control-label col-form-label"></label>
											<div class="col-sm-9 text-left">
												<?php if (!empty($getrecord_list->app_logo_one)) { ?>
													<img src="<?php echo base_url('upload_file/company/' . $getrecord_list->app_logo_one); ?>"
														 alt="Company Logo loading..." class="img-fluid" width="100" height="100"
														 style="max-width:500px;"/>
														<?php } ?>
											</div>
										</div>
										<div class="form-group row">
											<label for="fname" class="col-sm-3 text-right control-label col-form-label">App Logo 2</label>
											<div class="col-sm-9">
												<input type="file" class="form-control" name="app_logo_two" id="app_logo_two"
													   autocomplete="off" <?php if(!empty($templateDetails) && $templateDetails->pt_a_cinfo>=3){ echo 'disabled';}?>/>
												<small class="invalid-feedback app_logo_two"><?php echo form_error('app_logo_two'); ?></small>
											</div>
											<label for="fname" class="col-sm-3 text-right control-label col-form-label"></label>
											<div class="col-sm-9 text-left">
												<?php if (!empty($getrecord_list->app_logo_two)) { ?>
													<img src="<?php echo base_url('upload_file/company/' . $getrecord_list->app_logo_two); ?>"
														 alt="Company Logo loading..." class="img-fluid" width="100" height="100"
														 style="max-width:500px;"/>
													<?php } ?>
											</div>
										</div>
										<div class="form-group row">
											<div class="col-sm-12 text-center">
												<div align="center">
													<div class="get_error_total" align="center"
														 style="background-color: #bf0000;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
													<div class="get_success_total" align="center"
														 style="background-color: #174b10;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
													<div class="div_roller_total" align="center" style="display: none;">
														<img
																src="<?php echo base_url(); ?>style/images/ajax_loader.gif"
																style="max-width: 60px;"/></div>
												</div>
											</div>
										</div>
										<?php if($this->session->userdata('utype')==1 || $templateDetails->pt_a_cinfo<3){?>
										<div class="border-top">
											<div class="card-body">
												<button type="button" onclick="gotoclclickbutton();"
														class="btn btn-primary">Update
												</button>
											</div>
										</div>
										<?php }?>
									</form>

								</div>
							</div>

							<div class="tab-pane fade" id="nav-insurance" role="tabpanel"
								 aria-labelledby="nav-insurance-tab">

								
								<div class="card-body">
								<form action="<?php echo base_url() . 'admincontrol/company/updateinsurance_settings' ?>" method="post" enctype="multipart/form-data" id="myForm1">
										<?php if ($this->session->flashdata('success')) { ?>
											<div id="alert_msg"
												 class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
											<?php $this->session->unset_userdata('success');
										} elseif ($this->session->flashdata('e_error')) { ?>
											<div id="alert_msg"
												 class="alert alert-danger"><?php echo $this->session->flashdata('e_error'); ?></div>
											<?php $this->session->unset_userdata('e_error');
										} ?>

										<?php if (isset($error)) { ?>
											<div class="alert alert-danger alert-error">
												<h4>Error!</h4>
												<?php echo $error; ?>
											</div>
										<?php } ?>
										<div id="accordion">
											<div class="card">
												<div class="card-header" id="headingOne">
													<h5 class="mb-0">
														<button type="button" class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
															Insurance Details
														</button>
													</h5>
												</div>

												<div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion" style="padding-top:20px">
												<div class="form-group row">
													<label for="ins_vendor" class="col-sm-3 text-right control-label col-form-label">Vendor</label>
													<div class="col-sm-6">
														<select class="form-control selectpicker" name="ins_vendor" id="ins_vendor">
															<option value="">-- select vendor --</option>
															<?php foreach($suppliers as $sup){?>
															<option value="<?php echo $sup->sup_id;?>" <?php if(!empty($insurance_info) && $insurance_info->insurance_vendor==$sup->sup_id){echo 'selected';}?>><?php echo $sup->sup_name;?></option>
															<?php }?>
														</select>
														<small class="invalid-feedback ins_vendor"><?php echo form_error('ins_vendor'); ?></small>
													</div>

												</div>
												<div class="form-group row">
													<label for="ins_policy_no" class="col-sm-3 text-right control-label col-form-label">Policy No</label>
													<div class="col-sm-6">
														<input class="form-control" type="text" name="ins_policy_no" id="ins_policy_no" value="<?php if(!empty($insurance_info)){echo $insurance_info->policy_no;}?>">
														<small class="invalid-feedback ins_policy_no"><?php echo form_error('ins_vendor'); ?></small>
													</div>

												</div>
												<div class="form-group row">
													<label for="ins_policy_start_date" class="col-sm-3 text-right control-label col-form-label">Policy Start Date</label>
													<div class="col-sm-6">
														<input class="form-control" type="date" name="ins_policy_start_date" id="ins_policy_start_date" value="<?php if(!empty($insurance_info)){echo $insurance_info->policy_start_date;}?>">
														<small class="invalid-feedback ins_policy_start_date"><?php echo form_error('ins_policy_start_date'); ?></small>
													</div>

												</div>
												<div class="form-group row">
													<label for="ins_policy_end_date" class="col-sm-3 text-right control-label col-form-label">Policy End Date</label>
													<div class="col-sm-6">
														<input class="form-control" type="date" name="ins_policy_end_date" id="ins_policy_end_date" value="<?php if(!empty($insurance_info)){echo $insurance_info->policy_end_date;}?>">
														<small class="invalid-feedback ins_policy_end_date"><?php echo form_error('ins_policy_end_date'); ?></small>
													</div>

												</div>
												<div class="form-group row" style="margin-top:20px">
													<label for="ins_basic_valuation" class="col-sm-3 text-right control-label col-form-label">Basic Valuation</label>
													<div class="col-sm-6">
														<textarea class="form-control" name="ins_basic_valuation" id="ins_basic_valuation"><?php if(!empty($insurance_info)){echo $insurance_info->basic_valuation;}?></textarea>
														<small class="invalid-feedback ins_basic_valuation"><?php echo form_error('ins_basic_valuation'); ?></small>
													</div>

												</div>
												<?php
												$insuranceEquipmentsArray=array();
												if(!empty($insurance_equipments)){
												foreach($insurance_equipments as $equip){
													array_push($insuranceEquipmentsArray,$equip->equip_id);
												}}
												?>
												<div class="form-group row" style="margin-top:20px">
													<label for="ins_equipments" class="col-sm-3 text-right control-label col-form-label">Equipments</label>
													<div class="col-sm-6">
														<select class="form-control selectpicker" multiple
																name="ins_equipments[]" id="ins_equipments"
																autocomplete="off" data-live-search="true">
															<!-- <option value="">-- select equipments --</option> -->
															<?php foreach($equip_list as $equip){?>
																<option value="<?php echo $equip->eq_id;?>" <?php if(!empty($insuranceEquipmentsArray) && in_array($equip->eq_id,$insuranceEquipmentsArray)){echo 'selected';}?>><?php echo $equip->eqm_asset_name;?></option> 
															<?php }?>
														</select>
														<small class="invalid-feedback ins_equipments"><?php echo form_error('ins_equipments'); ?></small>
													</div>

												</div>
												<div class="form-group row" style="margin-top:20px">
													<label for="ins_attachment" class="col-sm-3 text-right control-label col-form-label">Attachment</label>
													<div class="col-sm-6">
														<input type="file" name="ins_attachment" id="ins_attachment">
														<small class="invalid-feedback ins_attachment"><?php echo form_error('ins_attachment'); ?></small>
													</div>

												</div>
												</div>
											</div>
											<div class="card">
												<div class="card-header" id="headingTwo">
													<h5 class="mb-0">
														<button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
															Insurance Coverage ( Constructors Equipment Floater )
														</button>
													</h5>
												</div>
												<div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion" style="padding-top:20px">
												<div class="table-responsive">
													<table class="table table-condensed table-bordered">
														<thead>
															<!-- <tr class="bg-primary text-light"  style="font-weight:bold!important;text-align:center">
																<th colspan="2">Insurance Coverage</th>
															</tr>
															<tr class="bg-primary text-light"  style="font-weight:bold!important;text-align:center">
																<th colspan="2">Constructors Equipment Floater</th>
															</tr> -->
															<tr style="font-weight:bold!important;text-align:center">
																<th>Amount</th>
																<th>Description</th>
															</tr>
														</thead>
														<tbody>
															<tr>
																<td>
																	<input class="form-control" type="number" name="ins_coverage_amt_1" id="ins_coverage_amt_1" value="<?php if(!empty($insurance_info)){echo $insurance_info->coverage_amt_1;}?>">
																	<small class="invalid-feedback ins_coverage_amt_1"><?php echo form_error('ins_coverage_amt_1'); ?></small>
																</td>
																<td>
																	<textarea class="form-control" name="ins_coverage_desc_1" rows="1" id="ins_coverage_desc_1"><?php if(!empty($insurance_info)){echo $insurance_info->coverage_desc_1;}?></textarea>
																	<small class="invalid-feedback ins_coverage_desc_1"><?php echo form_error('ins_coverage_desc_1'); ?></small>
																</td>
															</tr>
															<tr>
																<td>
																	<input class="form-control" type="number" name="ins_coverage_amt_2" id="ins_coverage_amt_2" value="<?php if(!empty($insurance_info)){echo $insurance_info->coverage_amt_2;}?>">
																	<small class="invalid-feedback ins_coverage_amt_2"><?php echo form_error('ins_coverage_amt_2'); ?></small>
																</td>
																<td>
																	<textarea class="form-control" name="ins_coverage_desc_2" rows="1" id="ins_coverage_desc_2"><?php if(!empty($insurance_info)){echo $insurance_info->coverage_desc_2;}?></textarea>
																	<small class="invalid-feedback ins_coverage_desc_2"><?php echo form_error('ins_coverage_desc_2'); ?></small>
																</td>
															</tr>
															<tr>
																<td>
																	<input class="form-control" type="number" name="ins_coverage_amt_3" id="ins_coverage_amt_3" value="<?php if(!empty($insurance_info)){echo $insurance_info->coverage_amt_3;}?>">
																	<small class="invalid-feedback ins_coverage_amt_3"><?php echo form_error('ins_coverage_amt_3'); ?></small>
																</td>
																<td>
																	<textarea class="form-control" name="ins_coverage_desc_3" rows="1" id="ins_coverage_desc_3"><?php if(!empty($insurance_info)){echo $insurance_info->coverage_desc_3;}?></textarea>
																	<small class="invalid-feedback ins_coverage_desc_3"><?php echo form_error('ins_coverage_desc_3'); ?></small>
																</td>
															</tr>
														</tbody>
													</table>
												</div>
												</div>
											</div>
											<div class="card">
												<div class="card-header" id="headingThree">
													<h5 class="mb-0">
														<button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
															Deductibles
														</button>
													</h5>
												</div>
												<div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion" style="padding-top:20px">
												<div class="table-responsive">
													<table class="table table-condensed table-bordered">
														<thead>
															<!-- <tr class="bg-primary text-light" style="font-weight:bold;text-align:center">
																<th colspan="2">Deductibles</th>
															</tr> -->
															<tr style="font-weight:bold;text-align:center">
																<th>Amount</th>
																<th>Description</th>
															</tr>
														</thead>
														<tbody>
															<tr>
																<td>
																	<input class="form-control" type="number" name="ins_deductible_amt_1" id="ins_deductible_amt_1" value="<?php if(!empty($insurance_info)){echo $insurance_info->deductible_amt_1;}?>">
																	<small class="invalid-feedback ins_deductible_amt_1"><?php echo form_error('ins_deductible_amt_1'); ?></small>
																</td>
																<td>
																	<textarea class="form-control" name="ins_deductible_desc_1" rows="1" id="ins_deductible_desc_1"><?php if(!empty($insurance_info)){echo $insurance_info->deductible_desc_1;}?></textarea>
																	<small class="invalid-feedback ins_deductible_desc_1"><?php echo form_error('ins_deductible_desc_1'); ?></small>
																</td>
															</tr>
															<tr>
																<td>
																	<input class="form-control" type="number" name="ins_deductible_amt_2" id="ins_deductible_amt_2" value="<?php if(!empty($insurance_info)){echo $insurance_info->deductible_amt_2;}?>">
																	<small class="invalid-feedback ins_deductible_amt_2"><?php echo form_error('ins_deductible_amt_2'); ?></small>
																</td>
																<td>
																	<textarea class="form-control" name="ins_deductible_desc_2" rows="1" id="ins_deductible_desc_2"><?php if(!empty($insurance_info)){echo $insurance_info->deductible_desc_2;}?></textarea>
																	<small class="invalid-feedback ins_deductible_desc_2"><?php echo form_error('ins_deductible_desc_2'); ?></small>
																</td>
															</tr>
															<tr>
																<td>
																	<input class="form-control" type="number" name="ins_deductible_amt_3" id="ins_deductible_amt_3" value="<?php if(!empty($insurance_info)){echo $insurance_info->deductible_amt_3;}?>">
																	<small class="invalid-feedback ins_deductible_amt_3"><?php echo form_error('ins_deductible_amt_3'); ?></small>
																</td>
																<td>
																	<textarea class="form-control" name="ins_deductible_desc_3" rows="1" id="ins_deductible_desc_3"><?php if(!empty($insurance_info)){echo $insurance_info->deductible_desc_3;}?></textarea>
																	<small class="invalid-feedback ins_deductible_desc_3"><?php echo form_error('ins_deductible_desc_3'); ?></small>
																</td>
															</tr>
														</tbody>
													</table>
												</div>
												</div>
											</div>
											<!-- <div class="card">
												<div class="card-header" id="headingFour">
													<h5 class="mb-0">
														<button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
															App Notifications
														</button>
													</h5>
												</div>
												<div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordion">
													
												</div>
											</div> -->
										</div>

										
										
										
										<!-- <div class="form-group row" style="margin-top:20px">
											<label for="" class="col-sm-3 text-right control-label col-form-label">Insurance Coverage</label>
											<label for="" class="col-sm-3 text-center control-label col-form-label">Amount</label>
											<label for="" class="col-sm-6 text-center control-label col-form-label">Description</label>
										</div> -->
										<!-- <div class="form-group row">
											<label for="" class="col-sm-3 text-right control-label col-form-label">Constructors Equipment Floater</label>
											<div class="col-sm-3">
												<input class="form-control" type="number" name="ins_coverage_amt_1" id="ins_coverage_amt_1" value="<?php if(!empty($insurance_info)){echo $insurance_info->coverage_amt_1;}?>">
												<small class="invalid-feedback ins_coverage_amt_1"><?php echo form_error('ins_coverage_amt_1'); ?></small>
											</div>
											<div class="col-sm-6">
												<textarea class="form-control" name="ins_coverage_desc_1" id="ins_coverage_desc_1"><?php if(!empty($insurance_info)){echo $insurance_info->coverage_desc_1;}?></textarea>
												<small class="invalid-feedback ins_coverage_desc_1"><?php echo form_error('ins_coverage_desc_1'); ?></small>
											</div>

										</div> -->
										<!-- <div class="form-group row">
											<label for="" class="col-sm-3 text-right control-label col-form-label"></label>
											<div class="col-sm-3">
												<input class="form-control" type="number" name="ins_coverage_amt_2" id="ins_coverage_amt_2" value="<?php if(!empty($insurance_info)){echo $insurance_info->coverage_amt_2;}?>">
												<small class="invalid-feedback ins_coverage_amt_2"><?php echo form_error('ins_coverage_amt_2'); ?></small>
											</div>
											<div class="col-sm-6">
												<textarea class="form-control" name="ins_coverage_desc_2" id="ins_coverage_desc_2"><?php if(!empty($insurance_info)){echo $insurance_info->coverage_desc_2;}?></textarea>
												<small class="invalid-feedback ins_coverage_desc_2"><?php echo form_error('ins_coverage_desc_2'); ?></small>
											</div>

										</div> -->
										<!-- <div class="form-group row">
											<label for="" class="col-sm-3 text-right control-label col-form-label"></label>
											<div class="col-sm-3">
												<input class="form-control" type="number" name="ins_coverage_amt_3" id="ins_coverage_amt_3" value="<?php if(!empty($insurance_info)){echo $insurance_info->coverage_amt_3;}?>">
												<small class="invalid-feedback ins_coverage_amt_3"><?php echo form_error('ins_coverage_amt_3'); ?></small>
											</div>
											<div class="col-sm-6">
												<textarea class="form-control" name="ins_coverage_desc_3" id="ins_coverage_desc_3"><?php if(!empty($insurance_info)){echo $insurance_info->coverage_desc_3;}?></textarea>
												<small class="invalid-feedback ins_coverage_desc_3"><?php echo form_error('ins_coverage_desc_3'); ?></small>
											</div>

										</div> -->
										<!-- <div class="form-group row" style="margin-top:20px">
											<label for="" class="col-sm-3 text-right control-label col-form-label">Deductibles</label>
											<label for="" class="col-sm-3 text-center control-label col-form-label">Amount</label>
											<label for="" class="col-sm-6 text-center control-label col-form-label">Description</label>
										</div> -->
										<!-- <div class="form-group row">
											<label for="" class="col-sm-3 text-right control-label col-form-label"></label>
											<div class="col-sm-3">
												<input class="form-control" type="number" name="ins_deductible_amt_1" id="ins_deductible_amt_1" value="<?php if(!empty($insurance_info)){echo $insurance_info->deductible_amt_1;}?>">
												<small class="invalid-feedback ins_deductible_amt_1"><?php echo form_error('ins_deductible_amt_1'); ?></small>
											</div>
											<div class="col-sm-6">
												<textarea class="form-control" name="ins_deductible_desc_1" id="ins_deductible_desc_1"><?php if(!empty($insurance_info)){echo $insurance_info->deductible_desc_1;}?></textarea>
												<small class="invalid-feedback ins_deductible_desc_1"><?php echo form_error('ins_deductible_desc_1'); ?></small>
											</div>

										</div> -->
										<!-- <div class="form-group row">
											<label for="" class="col-sm-3 text-right control-label col-form-label"></label>
											<div class="col-sm-3">
												<input class="form-control" type="number" name="ins_deductible_amt_2" id="ins_deductible_amt_2" value="<?php if(!empty($insurance_info)){echo $insurance_info->deductible_amt_2;}?>">
												<small class="invalid-feedback ins_deductible_amt_2"><?php echo form_error('ins_deductible_amt_2'); ?></small>
											</div>
											<div class="col-sm-6">
												<textarea class="form-control" name="ins_deductible_desc_2" id="ins_deductible_desc_2"><?php if(!empty($insurance_info)){echo $insurance_info->deductible_desc_2;}?></textarea>
												<small class="invalid-feedback ins_deductible_desc_2"><?php echo form_error('ins_deductible_desc_2'); ?></small>
											</div>
										</div> -->
										<!-- <div class="form-group row">
											<label for="" class="col-sm-3 text-right control-label col-form-label"></label>
											<div class="col-sm-3">
												<input class="form-control" type="number" name="ins_deductible_amt_3" id="ins_deductible_amt_3" value="<?php if(!empty($insurance_info)){echo $insurance_info->deductible_amt_3;}?>">
												<small class="invalid-feedback ins_deductible_amt_3"><?php echo form_error('ins_deductible_amt_3'); ?></small>
											</div>
											<div class="col-sm-6">
												<textarea class="form-control" name="ins_deductible_desc_3" id="ins_deductible_desc_3"><?php if(!empty($insurance_info)){echo $insurance_info->deductible_desc_3;}?></textarea>
												<small class="invalid-feedback ins_deductible_desc_3"><?php echo form_error('ins_deductible_desc_3'); ?></small>
											</div>

										</div> -->
										
										<div class="form-group row">
											<div class="col-sm-12 text-center">
												<div align="center">
													<div class="get_error_total" align="center"
														 style="background-color: #bf0000;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
													<div class="get_success_total" align="center"
														 style="background-color: #174b10;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
													<div class="div_roller_total" align="center" style="display: none;">
														<img
																src="<?php echo base_url(); ?>style/images/ajax_loader.gif"
																style="max-width: 60px;"/></div>
												</div>
											</div>
										</div>
										<?php if($this->session->userdata('utype')==1 || $templateDetails->pt_a_cinfo<3){?>
										<div class="border-top">
											<div class="card-body">
												<button type="button" onclick="gotoinsuranceclickbutton();"
														class="btn btn-primary">Update
												</button>
											</div>
										</div>
										<?php }?>
									</form>
								</div>
							</div>

							<div class="tab-pane fade" id="nav-profile" role="tabpanel"
								 aria-labelledby="nav-profile-tab">
								<div class="card-body">
									<div class="row">
										<?php if (validation_errors()) { ?>
											<div id="alert_msg"
												 class="col-md-12 alert alert-danger"><?php echo validation_errors(); ?></div>
										<?php } ?>
										<?php if ($this->session->flashdata('success')) { ?>
											<div id="alert_msg"
												 class="col-md-12 alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
											<?php $this->session->unset_userdata('success');
										} elseif ($this->session->flashdata('e_error')) { ?>
											<div id="alert_msg"
												 class="col-md-12 alert alert-danger"><?php echo $this->session->flashdata('e_error'); ?></div>
											<?php $this->session->unset_userdata('e_error');
										} ?>

										<div class="col-md-12">
											<form action="<?php echo base_url() . 'admincontrol/company/update_notification_setting' ?>"
												  method="post" enctype="multipart/form-data">

												<div id="accordion">
													<div class="card">
														<div class="card-header" id="headingOne">
															<h5 class="mb-0">
																<button type="button" class="btn btn-link" data-toggle="collapse" data-target="#collapseOne1" aria-expanded="true" aria-controls="collapseOne">
																	Equipment Notifications
																</button>
															</h5>
														</div>

														<div id="collapseOne1" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
															<div class="card-body">
																<div class="form-group row">
																	<label for="is_checkin_email"
																		   class="col-sm-2 text-right control-label col-form-label">Enable
																		Checkin Email</label>
																	<div class="col-sm-0">
																		<input type="checkbox"
																			   name="is_checkin_email" <?php echo ($this->admin_m->get_Notification_SettingByKey('is_checkin_email') ? "checked" : "") ?>
																			   value="1" id="is_checkin_email">
																		<input type="hidden" name="key[]" value="is_checkin_email">
																		<small class="invalid-feedback is_checkin_email"><?php echo form_error('is_checkin_email'); ?></small>
																	</div>
																	<label for="is_checkin_email"
																		   class="col-sm-1 text-right po_template_div control-label col-form-label">Use
																		template</label>
																	<div class="col-sm-2">
																		<input type="hidden" name="key[]" value="checkin_template">
																		<select class="form-control po_template_div selectpicker" name="checkin_template"
																				id="po_template"
																				autocomplete="off" data-live-search="true"
																				onchange="">
																			<?php foreach ($gettemplate_list as $template) { ?>
																				<option value="<?php echo $template->id ?>" <?php echo $template->id == $this->admin_m->get_Notification_SettingByKey('checkin_template') ? "selected" : "" ?>><?php echo $template->email_name ?></option>
																			<?php } ?>
																		</select>
																		<small class="invalid-feedback checkin_template"><?php echo form_error('checkin_template'); ?></small>
																	</div>

																	<label for="fname"
																		   class="col-sm-1 text-right  control-label col-form-label">User
																		selection</label>
																	<div class="col-sm-1">
																		<input type="hidden" name="key[]" value="notify_checkin_users">

																		<select class="form-control selectpicker" multiple
																				name="notify_checkin_users[]" id="notify_checkin_users"
																				autocomplete="off" data-live-search="true"
																				onchange="">
																			<?php
																			$users = json_decode($this->admin_m->get_Notification_SettingByKey('notify_checkin_users'));
																			$users = $users == null ? [] : $users;
																			foreach ($users_list as $user) { ?>
																				<option value="<?php echo $user->u_id ?>" <?php echo in_array($user->u_id, $users) ? "selected" : "" ?>><?php echo $user->firstname . ' ' . $user->lastname ?></option>
																			<?php } ?>
																		</select>
																		<small class="invalid-feedback notify_checkin_users"><?php echo form_error('notify_checkin_users'); ?></small>
																	</div>

																</div>
																<hr>
																<div class="form-group row">
																	<label for="is_po_template"
																		   class="col-sm-2 text-right control-label col-form-label">Enable
																		Checkout Email</label>
																	<div class="col-sm-0">
																		<input type="hidden" name="key[]" value="is_checkout_email">
																		<input type="checkbox"
																			   name="is_checkout_email" <?php echo $this->admin_m->get_Notification_SettingByKey('is_checkout_email') ?  "checked" : "" ?>
																			   value="1" id="is_checkout_email">
																		<small class="invalid-feedback is_checkout_email"><?php echo form_error('is_checkout_email'); ?></small>
																	</div>
																	<label for="po_template"
																		   class="col-sm-1 text-right po_template_div control-label col-form-label">Use
																		template</label>
																	<div class="col-sm-2">
																		<input type="hidden" name="key[]" value="checkout_template">
																		<select class="form-control po_template_div selectpicker" name="checkout_template"
																				id="po_template"
																				autocomplete="off" data-live-search="true"
																				onchange="">
																			<?php foreach ($gettemplate_list as $template) { ?>
																				<option value="<?php echo $template->id ?>" <?php echo $template->id == $this->admin_m->get_Notification_SettingByKey('checkout_template') ? "selected" : "" ?>><?php echo $template->email_name ?></option>
																			<?php } ?>
																		</select>
																		<small class="invalid-feedback checkout_template"><?php echo form_error('checkout_template'); ?></small>
																	</div>

																	<label for="fname"
																		   class="col-sm-1 text-right  control-label col-form-label">User
																		selection</label>
																	<div class="col-sm-1 ">
																		<input type="hidden" name="key[]" value="notify_checkout_users">
																		<select class="form-control selectpicker" multiple
																				name="notify_checkout_users[]" id="notify_checkout_users"
																				autocomplete="off" data-live-search="true"
																				onchange="">
																			<?php
																			$users = json_decode($this->admin_m->get_Notification_SettingByKey('notify_checkout_users'));
																			$users = $users == null ? [] : $users;
																			foreach ($users_list as $user) { ?>
																				<option value="<?php echo $user->u_id ?>" <?php echo in_array($user->u_id, $users) ? "selected" : "" ?>><?php echo $user->firstname . ' ' . $user->lastname ?></option>
																			<?php } ?>
																		</select>
																		<small class="invalid-feedback notify_checkout_users"><?php echo form_error('notify_checkout_users'); ?></small>
																	</div>

																</div>
																<hr>
																<div class="form-group row">
																	<label for="is_po_template"
																		   class="col-sm-2 text-right control-label col-form-label">Enable
																		Checklist Assigned Email</label>
																	<div class="col-sm-0">
																		<input type="hidden" name="key[]" value="is_checklist_assigned">
																		<input type="checkbox"
																			   name="is_checklist_assigned" <?php echo ($this->admin_m->get_Notification_SettingByKey('is_checklist_assigned')) ? "checked" : "" ?>
																			   value="1" id="is_checklist_assigned">
																		<small class="invalid-feedback is_checklist_assigned"><?php echo form_error('is_checklist_assigned'); ?></small>
																	</div>
																	<label for="po_template"
																		   class="col-sm-1 text-right po_template_div control-label col-form-label">Use
																		template</label>
																	<div class="col-sm-2">
																		<input type="hidden" name="key[]" value="checklist_assigned_template">
																		<select class="form-control po_template_div selectpicker" name="checklist_assigned_template"
																				id="checklist_assigned_template"
																				autocomplete="off" data-live-search="true"
																				onchange="">
																			<?php foreach ($gettemplate_list as $template) { ?>
																				<option value="<?php echo $template->id ?>" <?php echo $template->id == $this->admin_m->get_Notification_SettingByKey('checklist_assigned_template') ? "selected" : "" ?>><?php echo $template->email_name ?></option>
																			<?php } ?>
																		</select>
																		<small class="invalid-feedback checklist_assigned_template"><?php echo form_error('checklist_assigned_template'); ?></small>
																	</div>
																	<label for="fname"
																		   class="col-sm-3 text-right  control-label col-form-label"></label>
																	<div class="col-sm-1">
																	</div>
																	<label for="fname"
																		   class="col-sm-2 text-right  control-label col-form-label">Email to checklist assigned user</label>

																</div>
																<hr>
																<div class="form-group row">
																	<label for="is_po_template"
																		   class="col-sm-2 text-right control-label col-form-label">Enable
																		Checklist Performed Email</label>
																	<div class="col-sm-0">
																		<input type="hidden" name="key[]" value="is_checklist_performed">
																		<input type="checkbox"
																			   name="is_checklist_performed" <?php echo ($this->admin_m->get_Notification_SettingByKey('is_checklist_performed')) ? "checked" : "" ?>
																			   value="1" id="is_checklist_performed">
																		<small class="invalid-feedback is_checklist_performed"><?php echo form_error('is_checklist_performed'); ?></small>
																	</div>
																	<label for="po_template"
																		   class="col-sm-1 text-right po_template_div control-label col-form-label">Use
																		template</label>
																	<div class="col-sm-2">
																		<input type="hidden" name="key[]" value="checklist_performed_template">
																		<select class="form-control po_template_div selectpicker" name="checklist_performed_template"
																				id="checklist_performed_template"
																				autocomplete="off" data-live-search="true"
																				onchange="">
																			<?php foreach ($gettemplate_list as $template) { ?>
																				<option value="<?php echo $template->id ?>" <?php echo $template->id == $this->admin_m->get_Notification_SettingByKey('checklist_performed_template') ? "selected" : "" ?>><?php echo $template->email_name ?></option>
																			<?php } ?>
																		</select>
																		<small class="invalid-feedback checklist_performed_template"><?php echo form_error('checklist_performed_template'); ?></small>
																	</div>
																	<label for="fname"
																		   class="col-sm-1 text-right  control-label col-form-label">User
																		selection</label>
																	<div class="col-sm-1 ">
																		<input type="hidden" name="key[]" value="notify_checklist_performed_users">
																		<select class="form-control selectpicker" multiple
																				name="notify_checklist_performed_users[]" id="notify_checklist_performed_users"
																				autocomplete="off" data-live-search="true"
																				onchange="">
																			<?php
																			$users = json_decode($this->admin_m->get_Notification_SettingByKey('notify_checklist_performed_users'));
																			$users = $users == null ? [] : $users;
																			foreach ($users_list as $user) { ?>
																				<option value="<?php echo $user->u_id ?>" <?php echo in_array($user->u_id, $users) ? "selected" : "" ?>><?php echo $user->firstname . ' ' . $user->lastname ?></option>
																			<?php } ?>
																		</select>
																		<small class="invalid-feedback users"><?php echo form_error('notify_users_list'); ?></small>
																	</div>
																</div>
																<hr>
																<div class="form-group row">
																	<label for="is_po_template"
																		   class="col-sm-2 text-right control-label col-form-label">Enable
																		Maintenance Updated Email</label>
																	<div class="col-sm-0">
																		<input type="hidden" name="key[]" value="is_maintenance">
																		<input type="checkbox"
																			   name="is_maintenance" <?php echo $this->admin_m->get_Notification_SettingByKey('is_maintenance') ? "checked" : "" ?>
																			   value="1" id="is_maintenance">
																		<small class="invalid-feedback is_maintenance"><?php echo form_error('is_maintenance'); ?></small>
																	</div>
																	<label for="po_template"
																		   class="col-sm-1 text-right po_template_div control-label col-form-label">Use
																		template</label>
																	<div class="col-sm-2">
																		<input type="hidden" name="key[]" value="maintenance_template">
																		<select class="form-control po_template_div selectpicker" name="maintenance_template"
																				id="maintenance_template"
																				autocomplete="off" data-live-search="true"
																				onchange="">
																			<?php foreach ($gettemplate_list as $template) { ?>
																				<option value="<?php echo $template->id ?>" <?php echo $template->id == $this->admin_m->get_Notification_SettingByKey('maintenance_template')? "selected" : "" ?>><?php echo $template->email_name ?></option>
																			<?php } ?>
																		</select>
																		<small class="invalid-feedback maintenance_template"><?php echo form_error('maintenance_template'); ?></small>
																	</div>
																	<label for="fname"
																		   class="col-sm-1 text-right  control-label col-form-label">User
																		selection</label>
																	<div class="col-sm-1 ">
																		<input type="hidden" name="key[]" value="notify_maintenance_user">
																		<select class="form-control selectpicker" multiple
																				name="notify_maintenance_user[]" id="notify_maintenance_user"
																				autocomplete="off" data-live-search="true"
																				onchange="">
																			<?php
																			$users = json_decode($this->admin_m->get_Notification_SettingByKey('notify_maintenance_user'));
																			$users = $users == null ? [] : $users;
																			foreach ($users_list as $user) { ?>
																				<option value="<?php echo $user->u_id ?>" <?php echo in_array($user->u_id, $users) ? "selected" : "" ?>><?php echo $user->firstname . ' ' . $user->lastname ?></option>
																			<?php } ?>
																		</select>
																		<small class="invalid-feedback users"><?php echo form_error('notify_maintenance_user'); ?></small>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<div class="card">
														<div class="card-header" id="headingTwo">
															<h5 class="mb-0">
																<button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo1" aria-expanded="false" aria-controls="collapseTwo">
																	Item Notifications
																</button>
															</h5>
														</div>
														<div id="collapseTwo1" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
															<div class="card-body">
																<div class="form-group row">
																	<label for="fname"
																		   class="col-sm-2 text-right control-label col-form-label">Enable
																		notification for price Expiry</label>
																	<div class="col-sm-0">
																		<input type="hidden" name="key[]" value="is_price_expiry">
																		<input type="checkbox" name="is_price_expiry"
																			   value="1" <?php echo $this->admin_m->get_Notification_SettingByKey('is_price_expiry') ? "checked" : "" ?>
																			   id="is_price_expiry">
																		<small class="invalid-feedback is_price_expiry"><?php echo form_error('is_price_expiry'); ?></small>
																	</div>
																	<label for="fname"
																		   class="col-sm-1 text-right control-label col-form-label">Use
																		template</label>
																	<div class="col-sm-2 ">
																		<input type="hidden" name="key[]" value="price_expiry_template">
																		<select class="form-control selectpicker "
																				name="price_expiry_template" id="price_expiry_template"
																				autocomplete="off" data-live-search="true"
																				onchange="">
																			<?php foreach ($gettemplate_list as $template) { ?>
																				<option value="<?php echo $template->id ?>" <?php echo $template->id == $this->admin_m->get_Notification_SettingByKey('price_expiry_template') ? "selected" : "" ?>><?php echo $template->email_name ?></option>
																			<?php } ?>
																		</select>
																		<small class="invalid-feedback price_expiry_template"><?php echo form_error('price_expiry_template'); ?></small>
																	</div>
																	<label for="fname"
																		   class="col-sm-1 text-right control-label col-form-label">User
																		selection</label>
																	<div class="col-sm-1">
																		<input type="hidden" name="key[]" value="notify_price_expiry_users">
																		<select class="form-control selectpicker" multiple
																				name="notify_price_expiry_users[]" id="notify_price_expiry_users"
																				autocomplete="off" data-live-search="true"
																				onchange="">
																			<?php

																			$users = json_decode($this->admin_m->get_Notification_SettingByKey('notify_price_expiry_users'));
																			$users = $users == null ? [] : $users;
																			foreach ($users_list as $user) { ?>
																				<option value="<?php echo $user->u_id ?>" <?php echo in_array($user->u_id, $users) ? "selected" : "" ?>><?php echo $user->firstname . ' ' . $user->lastname ?></option>
																			<?php } ?>
																		</select>
																		<small class="invalid-feedback notify_price_expiry_users"><?php echo form_error('notify_price_expiry_users'); ?></small>
																	</div>

																	<label for="fname"
																		   class="col-sm-3 text-right control-label col-form-label">Notify
																		users no of days before price expiry</label>
																	<div class="col-sm-1 no_of_days_div">
																		<input type="hidden" name="key[]" value="price_expiry_no_of_days">
																		<input type="number" id="price_expiry_no_of_days" name="price_expiry_no_of_days"
																			   value="<?php echo $this->admin_m->get_Notification_SettingByKey('price_expiry_no_of_days') ?>"
																			   class="form-control">
																		<small class="invalid-feedback price_expiry_no_of_days"><?php echo form_error('price_expiry_no_of_days'); ?></small>
																	</div>


																</div>
																<hr>
																<div class="form-group row">
																	<label for="fname"
																		   class="col-sm-2 text-right control-label col-form-label">Enable
																		Item Approval Email</label>
																	<div class="col-sm-0">
																		<input type="hidden" name="key[]" value="is_item_approval">
																		<input type="checkbox" name="is_item_approval"
																			   value="1" <?php echo $this->admin_m->get_Notification_SettingByKey('is_item_approval') ? "checked" : "" ?>
																			   id="is_item_approval">
																		<small class="invalid-feedback is_item_approval"><?php echo form_error('is_item_approval'); ?></small>
																	</div>
																	<label for="fname"
																		   class="col-sm-1 text-right control-label col-form-label">Use
																		template</label>
																	<div class="col-sm-2">
																		<input type="hidden" name="key[]" value="item_approval_template">
																		<select class="form-control selectpicker "
																				name="item_approval_template" id="item_approval_template"
																				autocomplete="off" data-live-search="true"
																				onchange="">
																			<?php foreach ($gettemplate_list as $template) { ?>
																				<option value="<?php echo $template->id ?>" <?php echo $template->id == $this->admin_m->get_Notification_SettingByKey('item_approval_template') ? "selected" : "" ?>><?php echo $template->email_name ?></option>
																			<?php } ?>
																		</select>
																		<small class="invalid-feedback item_approval_template"><?php echo form_error('item_approval_template'); ?></small>
																	</div>


																</div>

															</div>
														</div>
													</div>
													<div class="card">
														<div class="card-header" id="headingThree">
															<h5 class="mb-0">
																<button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseThree1" aria-expanded="false" aria-controls="collapseThree">
																	Purchase Order Notifications
																</button>
															</h5>
														</div>
														<div id="collapseThree1" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
															<div class="card-body">
																<div class="form-group row">
																	<label for="is_po_template"
																		   class="col-sm-2 text-right control-label col-form-label">Enable
																		PO form email</label>
																	<div class="col-sm-0">
																		<input type="hidden" name="key[]" value="is_purchase_order">
																		<input type="checkbox"
																			   name="is_purchase_order" <?php echo $this->admin_m->get_Notification_SettingByKey('is_purchase_order') ? "checked" : "" ?>
																			   value="1" id="is_purchase_order">
																		<small class="invalid-feedback is_purchase_order"><?php echo form_error('is_purchase_order'); ?></small>
																	</div>
																	<label for="purchase_order_template"
																		   class="col-sm-1 text-right control-label col-form-label">Use
																		template</label>
																	<div class="col-sm-2">
																		<input type="hidden" name="key[]" value="purchase_order_template">
																		<select class="form-control selectpicker" name="purchase_order_template"
																				id="purchase_order_template"
																				autocomplete="off" data-live-search="true"
																				onchange="">
																			<?php foreach ($gettemplate_list as $template) { ?>
																				<option value="<?php echo $template->id ?>" <?php echo $template->id == $this->admin_m->get_Notification_SettingByKey('purchase_order_template')? "selected" : "" ?>><?php echo $template->email_name ?></option>
																			<?php } ?>
																		</select>
																		<small class="invalid-feedback purchase_order_template"><?php echo form_error('purchase_order_template'); ?></small>
																	</div>
																	<label for="fname"
																		   class="col-sm-3 text-right control-label col-form-label"></label>
																	<div class="col-sm-1">
																	</div>
																	<label for="fname"
																		   class="col-sm-2 text-right control-label col-form-label">Suppliers Primary Contact</label>

																</div>
																<hr>
																<div class="form-group row">
																	<label for="is_receive_order"
																		   class="col-sm-2 text-right control-label col-form-label">Enable
																		RFQ order notification</label>
																	<div class="col-sm-0">
																		<input type="hidden" name="key[]" value="is_rfq_order">
																		<input type="checkbox"
																			   name="is_rfq_order" <?php echo ($this->admin_m->get_Notification_SettingByKey('is_rfq_order')) ? "checked" : "" ?>
																			   value="1" id="is_rfq_order">
																		<small class="invalid-feedback is_rfq_order"><?php echo form_error('is_rfq_order'); ?></small>
																	</div>
																	<label for="is_receive_order"
																		   class="col-sm-1 text-right control-label col-form-label">Use
																		template</label>
																	<div class="col-sm-2">
																		<input type="hidden" name="key[]" value="rfq_order_template">
																		<select class="form-control selectpicker" name="rfq_order_template"
																				id="rfq_order_template"
																				autocomplete="off" data-live-search="true"
																				onchange="">
																			<?php foreach ($gettemplate_list as $template) { ?>
																				<option value="<?php echo $template->id ?>" <?php echo $template->id == $this->admin_m->get_Notification_SettingByKey('rfq_order_template') ? "selected" : "" ?>><?php echo $template->email_name ?></option>
																			<?php } ?>
																		</select>
																		<small class="invalid-feedback rfq_order_template"><?php echo form_error('rfq_order_template'); ?></small>
																	</div>
																	<label for="fname"
																		   class="col-sm-3 text-right control-label col-form-label"></label>
																	<div class="col-sm-1">
																	</div>
																	<label for="fname"
																		   class="col-sm-2 text-right control-label col-form-label">Suppliers Primary Contact</label>
																</div>
																<hr>
																<div class="form-group row">
																	<label for="is_receive_order"
																		   class="col-sm-2 text-right control-label col-form-label">Enable
																		RFQ Response notification</label>
																	<div class="col-sm-0">
																		<input type="hidden" name="key[]" value="is_rfq_response">
																		<input type="checkbox"
																			   name="is_rfq_response" <?php echo ($this->admin_m->get_Notification_SettingByKey('is_rfq_response')) ? "checked" : "" ?>
																			   value="1" id="is_rfq_order">
																		<small class="invalid-feedback is_rfq_response"><?php echo form_error('is_rfq_response'); ?></small>
																	</div>
																	<label for="is_receive_order"
																		   class="col-sm-1 text-right control-label col-form-label">Use
																		template</label>
																	<div class="col-sm-2">
																		<input type="hidden" name="key[]" value="rfq_response_template">
																		<select class="form-control selectpicker" name="rfq_response_template"
																				id="rfq_response_template"
																				autocomplete="off" data-live-search="true"
																				onchange="">
																			<?php foreach ($gettemplate_list as $template) { ?>
																				<option value="<?php echo $template->id ?>" <?php echo $template->id == $this->admin_m->get_Notification_SettingByKey('rfq_response_template') ? "selected" : "" ?>><?php echo $template->email_name ?></option>
																			<?php } ?>
																		</select>
																		<small class="invalid-feedback rfq_order_template"><?php echo form_error('rfq_response_template'); ?></small>
																	</div>
																	<label for="fname"
																		   class="col-sm-3 text-right control-label col-form-label">Project Contacts
																	</label>
																	<div class="col-sm-3 " style="text-align:right">
																		<input type="hidden" name="key[]" value="notify_project_contacts_rfq">
																		<select class="form-control selectpicker" multiple
																				name="notify_project_contacts_rfq[]" id="notify_project_contacts_rfq"
																				autocomplete="off" data-live-search="true"
																				onchange="">
																			<?php
																			$users = json_decode($this->admin_m->get_Notification_SettingByKey('notify_project_contacts_rfq'));
																			$users = $users == null ? [] : $users;
																			// foreach ($users_list as $user) { 
																			?>
																				<!-- <option value="<?php echo $user->u_id ?>" <?php echo in_array($user->u_id, $users) ? "selected" : "" ?>><?php echo $user->firstname . ' ' . $user->lastname ?></option> -->
																			<?php 
																				// }
																			 ?>
																			 <option value="1"<?php echo in_array(1, $users) ? "selected" : "" ?>>Manager</option>
																			 <option value="2"<?php echo in_array(2, $users) ? "selected" : "" ?>>Accountant</option>
																			 <option value="3"<?php echo in_array(3, $users) ? "selected" : "" ?>>Coordinator</option>
																			 <option value="4"<?php echo in_array(4, $users) ? "selected" : "" ?>>Supervisior</option>
																			 <option value="5"<?php echo in_array(5, $users) ? "selected" : "" ?>>Site Coordinator</option>
																		</select>
																		<small class="invalid-feedback users"><?php echo form_error('notify_project_contacts_rfq'); ?></small>
																	</div>
																</div>
																<hr>
																<div class="form-group row">
																	<label for="is_receive_order"
																		   class="col-sm-2 text-right control-label col-form-label">Enable
																		receive order notification</label>
																	<div class="col-sm-0">
																		<input type="hidden" name="key[]" value="is_receive_order">
																		<input type="checkbox"
																			   name="is_receive_order" <?php echo $this->admin_m->get_Notification_SettingByKey('is_receive_order') ? "checked" : "" ?>
																			   value="1" id="is_receive_order">
																		<small class="invalid-feedback is_receive_order"><?php echo form_error('is_receive_order'); ?></small>
																	</div>
																	<label for="is_receive_order"
																		   class="col-sm-1 text-right control-label col-form-label">Use
																		template</label>
																	<div class="col-sm-2">
																		<input type="hidden" name="key[]" value="receive_order_template">
																		<select class="form-control selectpicker" name="receive_order_template"
																				id="receive_order_template"
																				autocomplete="off" data-live-search="true"
																				onchange="">
																			<?php foreach ($gettemplate_list as $template) { ?>
																				<option value="<?php echo $template->id ?>" <?php echo $template->id == $this->admin_m->get_Notification_SettingByKey('receive_order_template') ? "selected" : "" ?>><?php echo $template->email_name ?></option>
																			<?php } ?>
																		</select>
																		<small class="invalid-feedback receive_order_template"><?php echo form_error('receive_order_template'); ?></small>
																	</div>
																	<label for="fname"
																		   class="col-sm-3 text-right  control-label col-form-label"></label>
																	<div class="col-sm-1">
																	</div>
																	<label for="fname"
																		   class="col-sm-2 text-right  control-label col-form-label">Projects contact's, Upon receiving all PO orders</label>
																</div>
															</div>
														</div>
													</div>
													<div class="card">
														<div class="card-header" id="headingFour">
															<h5 class="mb-0">
																<button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseFour1" aria-expanded="false" aria-controls="collapseFour">
																	App Notifications
																</button>
															</h5>
														</div>
														<div id="collapseFour1" class="collapse" aria-labelledby="headingFour" data-parent="#accordion">
															<div class="card-body">
																<div class="form-group row">
																	<label for="is_procore"
																		   class="col-sm-2 text-right control-label col-form-label">Enable
																		procore integration notification</label>
																	<div class="col-sm-0">
																		<input type="hidden" name="key[]" value="is_procore">
																		<input type="checkbox"
																			   name="is_procore" <?php echo ($this->admin_m->get_Notification_SettingByKey('is_procore')) ? "checked" : "" ?>
																			   value="1" id="is_procore">
																		<small class="invalid-feedback is_procore"><?php echo form_error('is_procore'); ?></small>
																	</div>
																	<label for="po_template"
																		   class="col-sm-1 text-right control-label col-form-label">Use
																		template</label>
																	<div class="col-sm-2">
																		<input type="hidden" name="key[]" value="procore_template">
																		<select class="form-control selectpicker" name="procore_template"
																				id="procore_template"
																				autocomplete="off" data-live-search="true"
																				onchange="">
																			<?php foreach ($gettemplate_list as $template) { ?>
																				<option value="<?php echo $template->id ?>" <?php echo $template->id == $this->admin_m->get_Notification_SettingByKey('procore_template') ? "selected" : "" ?>><?php echo $template->email_name ?></option>
																			<?php } ?>
																		</select>
																		<small class="invalid-feedback procore_template"><?php echo form_error('procore_template'); ?></small>
																	</div>
																	<label for="fname"
																		   class="col-sm-1 text-right control-label col-form-label">User
																		selection</label>
																	<div class="col-sm-1 ">
																		<input type="hidden" name="key[]" value="procore_notify_user">
																		<select class="form-control selectpicker" multiple
																				name="procore_notify_user[]" id="procore_notify_user"
																				autocomplete="off" data-live-search="true"
																				onchange="">
																			<?php
																			$users = json_decode($this->admin_m->get_Notification_SettingByKey('procore_notify_user'));
																			$users = $users == null ? [] : $users;
																			foreach ($users_list as $user) { ?>
																				<option value="<?php echo $user->u_id ?>" <?php echo in_array($user->u_id, $users) ? "selected" : "" ?>><?php echo $user->firstname . ' ' . $user->lastname ?></option>
																			<?php } ?>
																		</select>
																		<small class="invalid-feedback procore_notify_user"><?php echo form_error('procore_notify_user'); ?></small>
																	</div>
																	<label for="fname"
																		   class="col-sm-3 text-right control-label col-form-label"></label>
																	<div class="col-sm-1">
																	</div>

																</div>
																<hr>
																<div class="form-group row">
																	<label for="is_new_user"
																		   class="col-sm-2 text-right control-label col-form-label">Enable
																		new user notification</label>
																	<div class="col-sm-0">
																		<input type="hidden" name="key[]" value="is_new_user">
																		<input type="checkbox"
																			   name="is_new_user" <?php echo ($this->admin_m->get_Notification_SettingByKey('is_new_user')) ? "checked" : "" ?>
																			   value="1" id="is_new_user">
																		<small class="invalid-feedback is_new_user"><?php echo form_error('is_new_user'); ?></small>
																	</div>
																	<label for="new_user_template"
																		   class="col-sm-1 text-right control-label col-form-label">Use
																		template</label>
																	<div class="col-sm-2">
																		<input type="hidden" name="key[]" value="new_user_template">
																		<select class="form-control selectpicker" name="new_user_template"
																				id="new_user_template"
																				autocomplete="off" data-live-search="true"
																				onchange="">
																			<?php foreach ($gettemplate_list as $template) { ?>
																				<option value="<?php echo $template->id ?>" <?php echo $template->id == $this->admin_m->get_Notification_SettingByKey('new_user_template') ? "selected" : "" ?>><?php echo $template->email_name ?></option>
																			<?php } ?>
																		</select>
																		<small class="invalid-feedback new_user_template"><?php echo form_error('new_user_template'); ?></small>
																	</div>
																	<label for="fname"
																		   class="col-sm-3 text-right control-label col-form-label"></label>
																	<div class="col-sm-1">
																	</div>
																	<label for="fname"
																		   class="col-sm-2 text-right control-label col-form-label">To the new user when user is added</label>
																</div>
																<hr>
																<div class="form-group row">
																	<label for="is_new_user"
																		   class="col-sm-2 text-right control-label col-form-label">Enable
																		forgot password notification</label>
																	<div class="col-sm-0">
																		<input type="hidden" name="key[]" value="is_forgot_password">
																		<input type="checkbox"
																			   name="is_forgot_password" <?php echo $this->admin_m->get_Notification_SettingByKey('is_forgot_password') ?  "checked" : "" ?>
																			   value="1" id="is_forgot_password">
																		<small class="invalid-feedback is_forgot_password"><?php echo form_error('is_forgot_password'); ?></small>
																	</div>
																	<label for="new_user_template"
																		   class="col-sm-1 text-right control-label col-form-label">Use
																		template</label>
																	<div class="col-sm-2">
																		<input type="hidden" name="key[]" value="forgot_password_template">
																		<select class="form-control selectpicker" name="forgot_password_template"
																				id="forgot_password_template"
																				autocomplete="off" data-live-search="true"
																				onchange="">
																			<?php foreach ($gettemplate_list as $template) { ?>
																				<option value="<?php echo $template->id ?>" <?php echo $template->id == $this->admin_m->get_Notification_SettingByKey('forgot_password_template') ? "selected" : "" ?>><?php echo $template->email_name ?></option>
																			<?php } ?>
																		</select>
																		<small class="invalid-feedback forgot_password_template"><?php echo form_error('forgot_password_template'); ?></small>
																	</div>
																	<label for="fname"
																		   class="col-sm-3 text-right control-label col-form-label"></label>
																	<div class="col-sm-1">
																	</div>
																	<label for="fname"
																		   class="col-sm-2 text-right control-label col-form-label">To the new user when user is added</label>
																</div>
															</div>
														</div>
													</div>
												</div>

												<div class="border-top">
													<div class="card-body">
														<button type="submit"
																class="btn btn-primary">Update
														</button>
													</div>
												</div>
											</form>
										</div>
									</div>

								</div>

							</div>

							<div class="tab-pane fade" id="nav-contact" role="tabpanel"
								 aria-labelledby="nav-contact-tab">
								<div class="card-body">
									<div class="row">
										<?php if (validation_errors()) { ?>
											<div id="alert_msg"
												 class="col-md-12 alert alert-danger"><?php echo validation_errors(); ?></div>
										<?php } ?>
										<?php if ($this->session->flashdata('success')) { ?>
											<div id="alert_msg"
												 class="col-md-12 alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
											<?php $this->session->unset_userdata('success');
										} elseif ($this->session->flashdata('e_error')) { ?>
											<div id="alert_msg"
												 class="col-md-12 alert alert-danger"><?php echo $this->session->flashdata('e_error'); ?></div>
											<?php $this->session->unset_userdata('e_error');
										} ?>

										<div class="col-md-6">
											<form action="<?php echo base_url() . 'admincontrol/company/updatemail_setting' ?>"
												  method="post" enctype="multipart/form-data">
												<div class="form-group row">
													<label for="fname"
														   class="col-sm-3 text-right control-label col-form-label">SMTP
														Host</label>
													<div class="col-sm-6">
														<input type="text" class="form-control" name="smtp_host"
															   id="smtp_host" placeholder="Enter SMTP Host"
															   value="<?php echo isset($getsmtp_record->smtp_host) ? $getsmtp_record->smtp_host : null; ?>"
															   autocomplete="off"/>
														<small class="invalid-feedback smtp_host"><?php echo form_error('smtp_host'); ?></small>
													</div>

												</div>
												<div class="form-group row">
													<label for="cono1"
														   class="col-sm-3 text-right control-label col-form-label">SMTP
														Username</label>
													<div class="col-sm-6">
														<input type="text" class="form-control" name="smtp_username"
															   id="smtp_username" placeholder="Enter SMTP Username"
															   value="<?php echo isset($getsmtp_record->smtp_username) ? $getsmtp_record->smtp_username : null; ?>"
															   autocomplete="off"/>
														<small class="invalid-feedback smtp_username"><?php echo form_error('smtp_username'); ?></small>
													</div>
												</div>
												<div class="form-group row">
													<label for="cono1"
														   class="col-sm-3 text-right control-label col-form-label">SMTP
														Password</label>
													<div class="col-sm-6">
														<input class="form-control" name="smtp_password"
															   id="smtp_password"
															   placeholder="Enter SMTP Password"
															   value="<?php echo isset($getsmtp_record->smtp_password) ? $getsmtp_record->smtp_password : null; ?>">
														<small class="invalid-feedback smtp_password"><?php echo form_error('smtp_password'); ?></small>
													</div>
												</div>
												<div class="form-group row">
													<label for="cono1"
														   class="col-sm-3 text-right control-label col-form-label">SMTP
														Port</label>
													<div class="col-sm-6">
														<input class="form-control" name="smtp_port" id="smtp_port"
															   placeholder="Enter SMTP Port"
															   value="<?php echo isset($getsmtp_record->smtp_port) ? $getsmtp_record->smtp_port : null; ?>">
														<small class="invalid-feedback smtp_port"><?php echo form_error('smtp_port'); ?></small>
													</div>
												</div>
												<div class="form-group row">
													<label for="cono1"
														   class="col-sm-3 text-right control-label col-form-label">SMTP
														Encryption</label>
													<div class="col-sm-6">
														<input class="form-control" name="smtp_encryption"
															   id="smtp_encryption" placeholder="Enter SMTP Encryption"
															   value="<?php echo isset($getsmtp_record->smtp_encryption) ? $getsmtp_record->smtp_encryption : null; ?>">
														<small class="invalid-feedback smtp_encryption"><?php echo form_error('smtp_encryption'); ?></small>
													</div>
												</div>
												<div class="form-group row">
													<label for="cono1"
														   class="col-sm-3 text-right control-label col-form-label">SMTP
														From Address</label>
													<div class="col-sm-6">
														<input class="form-control" name="smtp_from_address"
															   id="smtp_from_address"
															   placeholder="Enter SMTP From Address"
															   value="<?php echo isset($getsmtp_record->smtp_from_address) ? $getsmtp_record->smtp_from_address : null; ?>">
														<small class="invalid-feedback smtp_from_address"><?php echo form_error('smtp_from_address'); ?></small>
													</div>
												</div>
												<div class="form-group row">
													<label for="cono1"
														   class="col-sm-3 text-right control-label col-form-label">SMTP
														From Name</label>
													<div class="col-sm-6">
														<input class="form-control" name="smtp_from_name"
															   id="smtp_from_name" placeholder="Enter SMTP From Name"
															   value="<?php echo isset($getsmtp_record->smtp_from_name) ? $getsmtp_record->smtp_from_name : null; ?>">
														<small class="invalid-feedback smtp_from_name"><?php echo form_error('smtp_from_name'); ?></small>
													</div>
												</div>
												<div class="border-top">
													<div class="card-body">
														<button type="submit"
																class="btn btn-primary">Update
														</button>
													</div>
												</div>
											</form>
										</div>
										<div class="col-md-6">
											<form action="<?php echo base_url() . 'admincontrol/company/test_email' ?>"
												  method="post"
												  enctype="multipart/form-data">
												<div class="form-group row">
													<label for="fname"
														   class="col-sm-3 text-right control-label col-form-label">Test
														SMTP configuration</label>
													<div class="col-sm-6">
														<input type="text" class="form-control" name="email"
															    placeholder="Enter Test Email Address"
															   autocomplete="off"/>
													</div>
												</div>
												<div class="border-top">
													<div class="card-body">
														<button type="submit" class="btn btn-primary">Test</button>
													</div>
												</div>
											</form>
										</div>
									</div>

								</div>

							</div>
						</div>
					</div>

				</div>
			</div>

		</div>
		<!-- ============================================================== -->
		<!-- End Container fluid  -->
		<!-- ============================================================== -->
		<!-- ============================================================== -->


		<?php $this->load->view('admin/component/footer') ?>

		<script type="text/javascript">
			$(function () {
				$('.alert, .alert-error, .invalid-feedback').delay(8000).fadeOut();
				$(".select2").select2();
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
				var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.JPG|\.JPEG|\.PNG)$/i;

				var cname = $('#cname').val();
				var c_address = $('#c_address').val();
				var u_mobile = $('#u_mobile').val();
				/*var u_state = $('#u_state').val();
				var u_city = $('#u_city').val();
				var u_pincode = $('#u_pincode').val();*/

				//var ap_symptom = $("input[name='ap_symptom']:checked").val();
				//var ap_quaran = $("input[name='ap_quaran']:checked").val();

				if (cname == "") {
					e_error = 1;
					$('.cname').html('Company Name is Required.');
				} else {
					if (!cname.match(alphanumerics_spaces)) {
						e_error = 1;
						$('.cname').html('Company Name not use special carecters [without _ , . -], Check again.');
					} else {
						$('.cname').html('');
					}
				}
				if (c_address == "") {
					e_error = 1;
					$('.c_address').html('Company Address is Required.');
				} else {
					$('.c_address').html('');
				}


				if (document.getElementById("c_logo").files.length == 0) {
					// e_error = 1;
					// $('.c_logo').html('Company Logo is Required.');
				} else {
					var fileInput = document.getElementById('c_logo');
					var filePath = fileInput.value;
					if (!allowedExtensions.exec(filePath)) {
						e_error = 1;
						$('.c_logo').html('Company Logo type Invalid.(Use PNG/JPG)');
					} else {
						$('.c_logo').html('');
					}
				}

				if (document.getElementById("app_logo_one").files.length == 0) {
					// e_error = 1;
					// $('.c_logo_two').html('Company Logo is Required.');
				} else {
					var fileInput = document.getElementById('app_logo_one');
					var filePath = fileInput.value;
					if (!allowedExtensions.exec(filePath)) {
						e_error = 1;
						$('.app_logo_one').html('App Logo One type Invalid.(Use PNG/JPG)');
					} else {
						$('.app_logo_one').html('');
					}

				}

				if (document.getElementById("app_logo_two").files.length == 0) {
					// e_error = 1;
					// $('.c_logo_two').html('Company Logo is Required.');
				} else {
					var fileInput = document.getElementById('app_logo_two');
					var filePath = fileInput.value;
					if (!allowedExtensions.exec(filePath)) {
						e_error = 1;
						$('.app_logo_two').html('App Logo Two type Invalid.(Use PNG/JPG)');
					} else {
						$('.app_logo_two').html('');
					}

				}


				//alert(salts);
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
					//alert(newhash);
					//alert(rehash);
					$("#myForm").submit();
				}

			}

			function gotoinsuranceclickbutton() {
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
				var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.JPG|\.JPEG|\.PNG)$/i;

				var ins_vendor = $('#ins_vendor').val();
				var ins_policy_no = $('#ins_policy_no ').val();
				var ins_policy_start_date = $('#ins_policy_start_date').val();
				var ins_policy_end_date = $('#ins_policy_end_date').val();
				var ins_coverage_amt_1 = $('#ins_coverage_amt_1').val();
				var ins_coverage_amt_2 = $('#ins_coverage_amt_2').val();
				var ins_coverage_amt_3 = $('#ins_coverage_amt_3').val();
				var ins_coverage_desc_1 = $('#ins_coverage_desc_1').val();
				var ins_coverage_desc_2 = $('#ins_coverage_desc_2').val();
				var ins_coverage_desc_3 = $('#ins_coverage_desc_3').val();
				var ins_basic_valuation = $('#ins_basic_valuation').val();
				var ins_deductible_amt_1 = $('#ins_deductible_amt_1').val();
				var ins_deductible_amt_2 = $('#ins_deductible_amt_2').val();
				var ins_deductible_amt_3 = $('#ins_deductible_amt_3').val();
				var ins_deductible_desc_1 = $('#ins_deductible_desc_1').val();
				var ins_deductible_desc_2 = $('#ins_deductible_desc_2').val();
				var ins_deductible_desc_3 = $('#ins_deductible_desc_3').val();
				var ins_equipments = $('#ins_equipments').val();
				/*var u_state = $('#u_state').val();
				var u_city = $('#u_city').val();
				var u_pincode = $('#u_pincode').val();*/

				//var ap_symptom = $("input[name='ap_symptom']:checked").val();
				//var ap_quaran = $("input[name='ap_quaran']:checked").val();

				if (ins_vendor == "") {
					e_error = 1;
					$('.ins_vendor').html('Vendor is Required.');
				} else {
					$('.ins_vendor').html('');
				}

				if (ins_policy_no == "") {
					e_error = 1;
					$('.ins_policy_no').html('Policy No is Required.');
				} else {
					$('.ins_policy_no').html('');
				}

				if (ins_policy_start_date == "") {
					e_error = 1;
					$('.ins_policy_start_date').html('Policy Start Date is Required.');
				} else {
					$('.ins_policy_start_date').html('');
				}

				if (ins_policy_end_date == "") {
					e_error = 1;
					$('.ins_policy_end_date').html('Policy end Date is Required.');
				} else {
					$('.ins_policy_senddate').html('');
				}

				if (ins_coverage_amt_1 == "") {
					e_error = 1;
					$('.ins_coverage_amt_1').html('Coverage Amount is Required.');
				} else {
					$('.ins_coverage_amt_1').html('');
				}

				if (ins_coverage_amt_2 == "") {
					e_error = 1;
					$('.ins_coverage_amt_2').html('Coverage Amount is Required.');
				} else {
					$('.ins_coverage_amt_2').html('');
				}

				if (ins_coverage_amt_3 == "") {
					e_error = 1;
					$('.ins_coverage_amt_3').html('Coverage Amount is Required.');
				} else {
					$('.ins_coverage_amt_3').html('');
				}

				if (ins_coverage_desc_1 == "") {
					e_error = 1;
					$('.ins_coverage_desc_1').html('Coverage Description is Required.');
				} else {
					$('.ins_coverage_desc_1').html('');
				}

				if (ins_coverage_amt_2 == "") {
					e_error = 1;
					$('.ins_coverage_desc_2').html('Coverage Description is Required.');
				} else {
					$('.ins_coverage_desc_2').html('');
				}

				if (ins_coverage_amt_3 == "") {
					e_error = 1;
					$('.ins_coverage_desc_3').html('Coverage Description is Required.');
				} else {
					$('.ins_coverage_desc_3').html('');
				}

				if (ins_basic_valuation == "") {
					e_error = 1;
					$('.ins_basic_valuation').html('Basic Valuation is Required.');
				} else {
					$('.ins_basic_valuation').html('');
				}

				if (ins_deductible_amt_1 == "") {
					e_error = 1;
					$('.ins_deductible_amt_1').html('Coverage Deductible is Required.');
				} else {
					$('.ins_deductible_amt_1').html('');
				}

				if (ins_deductible_amt_2 == "") {
					e_error = 1;
					$('.ins_deductible_amt_2').html('Coverage Deductible is Required.');
				} else {
					$('.ins_deductible_amt_2').html('');
				}

				if (ins_deductible_amt_3 == "") {
					e_error = 1;
					$('.ins_deductible_amt_3').html('Coverage Deductible is Required.');
				} else {
					$('.ins_deductible_amt_3').html('');
				}

				if (ins_deductible_desc_1 == "") {
					e_error = 1;
					$('.ins_deductible_desc_1').html('Deductible Description is Required.');
				} else {
					$('.ins_deductible_desc_1').html('');
				}

				if (ins_deductible_amt_2 == "") {
					e_error = 1;
					$('.ins_deductible_desc_2').html('Deductible Description is Required.');
				} else {
					$('.ins_deductible_desc_2').html('');
				}

				if (ins_deductible_amt_3 == "") {
					e_error = 1;
					$('.ins_deductible_desc_3').html('Deductible Description is Required.');
				} else {
					$('.ins_deductible_desc_3').html('');
				}

				if (ins_equipments == "") {
					e_error = 1;
					$('.ins_equipments').html('Equipments Required.');
				} else {
					$('.ins_equipments').html('');
				}


				// alert(e_error);
				//alert(salts);
				if (e_error == 1) {
					// alert('in');
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
					// alert('out');
					//alert(newhash);
					//alert(rehash);
					$("#myForm1").submit();
				}

			}
		</script>
