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
				<h4 class="page-title">Template List</h4>
				<div class="ml-auto text-right">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a
										href="<?php echo base_url('admincontrol/dashboard'); ?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Template List</li>
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
						if($this->session->userdata('utype')==1 || $templateDetails->pt_m_email<3){?>
							<a href="javascript:;" onclick="goto_add_record();" class="btn btn-primary float-right mb-2"
							style="margin-right: 10px;">Add New Template</a>
						<?php }?>
						<div class="table-responsive">
							<table id="zero_config" class="table table-striped table-bordered">
								<thead>
								<tr style="font-weight: bold;">
									<th>Sl No.</th>
									<th>Name</th>
									<th>Key</th>
									<th>Created At</th>
									<?php 
									if($this->session->userdata('utype')==1 || $templateDetails->pt_m_email<3){?>
										<th>Action</th>
									<?php }?>
								</tr>
								</thead>
								<tbody>
								<?php foreach ($getrecord_list as $keys => $recorditem) { ?>
									<tr>
										<td><?php echo $keys + 1; ?></td>
										<td><?php echo $recorditem->email_name; ?></td>
										<td><?php echo $recorditem->email_key; ?></td>
										<td><?php echo date('d-m-Y', strtotime($recorditem->created_at)); ?></td>
										<?php 
										if($this->session->userdata('utype')==1 || $templateDetails->pt_m_email<3){?>
											<td>
												<a class="btn btn-outline-warning"
												onclick="modify_record(<?php echo $recorditem->id; ?>);"
												href="javascript:;" title="Edit Record"><i
															class="fa fa-edit text-primary"></i></a>
												<?php 
												if($this->session->userdata('utype')==1 || $templateDetails->pt_m_email<2){?>
													<a class="btn btn-outline-warning"
													onclick="return confirm('You are about to delete a record. This cannot be undone. Are you sure?');"
													href="<?php echo base_url() . 'admincontrol/template/delete_record/' . $recorditem->id; ?>"
													title="Delete Record"><i class="fa fa-trash text-danger"></i></a>
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
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Add New Template</h5>
					<button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="recipient-name" class="col-form-label">Template Name:</label>
						<input type="text" class="form-control reset-input" placeholder="Enter Name" name="email_name"
							   id="email_name" autocomplete="off"/>
						<small class="invalid-feedback email_name"><?php //echo form_error('name_supp'); ?></small>
					</div>
					<div class="form-group">
						<label for="recipient-name" class="col-form-label">Variables</label><br>
						<select class="form-control" name="variables" onchange="javascript:insertAtCaret('email_body','variables')" id="variables">
							<option value="#SupName#">#SupName#</option>
							<option value="#PorderNo#">#PorderNo#</option>
							<option value="#SupEmail#">#SupEmail#</option>
							<option value="#ProjNumber#">#ProjNumber#</option>
							<option value="#ProjName#">#ProjName#</option>
							<option value="#ProjAddress#">#ProjAddress#</option>
							<option value="#FirstName#">#FirstName#</option>
							<option value="#LastName#">#LastName#</option>
							<option value="#UserName#">#UserName#</option>
							<option value="#Password#">#Password#</option>
							<option value="#ReceiptNo#">#ReceiptNo#</option>
							<option value="#PackingSlipNo#">#PackingSlipNo#</option>
							<option value="#Status#">#Status#</option>
							<option value="#SummaryTable#">#SummaryTable#</option>
							<option value="#PorderAddress#">#PorderAddress#</option>
							<option value="#PorderDeliveryDate#">#PorderDeliveryDate#</option>
							<option value="#PorderDeliveryNote#">#PorderDeliveryNote#</option>
							<option value="#PorderTotalAmount#">#PorderTotalAmount#</option>
							<option value="#Link#">#Link#</option>

						</select>
					</div>
					<div class="form-group">
						<label for="recipient-name" class="col-form-label">Body:</label>
						<textarea class="form-control summernote reset-input" placeholder="Enter Email Body" name="email_body"
								  id="email_body" autocomplete="off"></textarea>
						<small class="invalid-feedback email_body"><?php //echo form_error('name_supp'); ?></small>
					</div>

					<div class="form-group">
						<label for="recipient-name" class="col-form-label">Key: </label>
						<input type="text" class="form-control reset-input" placeholder="Enter Email Key" name="email_key"
							   id="email_key" autocomplete="off"/>
						<small class="invalid-feedback email_key"><?php //echo form_error('name_supp_cp'); ?></small>
					</div>
					<div class="form-group">
						<label for="recipient-name" class="col-form-label">Email Subject: </label>
						<input type="text" class="form-control reset-input" placeholder="Enter Email Subject" name="email_subject"
							   id="email_subject" autocomplete="off"/>
						<small class="invalid-feedback update_email_subject"><?php //echo form_error('name_supp_cp'); ?></small>
					</div>
					<div class="form-group">
						<label for="recipient-name" class="col-form-label">Email CC: </label>
						<input type="text" class="form-control reset-input" placeholder="Enter Email CC" name="email_cc"
							   id="email_cc" autocomplete="off"/>
						<small class="invalid-feedback email_cc"><?php //echo form_error('name_supp_cp'); ?></small>
					</div>
					<div class="form-group">
						<label for="recipient-name" class="col-form-label">Email BCC: </label>
						<input type="text" class="form-control reset-input" placeholder="Enter Email BCC" name="email_bcc"
							   id="email_bcc" autocomplete="off"/>
						<small class="invalid-feedback email_bcc"><?php //echo form_error('name_supp_cp'); ?></small>
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
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Modify Template</h5>
					<button type="button" class="close close_modal2" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="recipient-name" class="col-form-label">Email Name:</label>
						<input type="hidden" name="update_id_template" id="update_id_template" value=""
							   autocomplete="off"/>
						<input type="text" class="form-control" placeholder="Enter Email Name" name="update_email_name"
							   id="update_email_name" autocomplete="off"/>
						<small class="invalid-feedback update_email_name"><?php //echo form_error('update_name_supp'); ?></small>
					</div>
					<div class="form-group">
						<label for="recipient-name" class="col-form-label">Variables</label><br>
						<select class="form-control" name="update_variables" onchange="javascript:insertAtCaret('update_email_body','update_variables')" id="update_variables">
							<option value="#SupName#">#SupName#</option>
							<option value="#PorderNo#">#PorderNo#</option>
							<option value="#SupEmail#">#SupEmail#</option>
							<option value="#ProjNumber#">#ProjNumber#</option>
							<option value="#ProjName#">#ProjName#</option>
							<option value="#ProjAddress#">#ProjAddress#</option>
							<option value="#FirstName#">#FirstName#</option>
							<option value="#LastName#">#LastName#</option>
							<option value="#UserName#">#UserName#</option>
							<option value="#Password#">#Password#</option>
							<option value="#ReceiptNo#">#ReceiptNo#</option>
							<option value="#PackingSlipNo#">#PackingSlipNo#</option>
							<option value="#Status#">#Status#</option>
							<option value="#SummaryTable#">#SummaryTable#</option>
							<option value="#PorderAddress#">#PorderAddress#</option>
							<option value="#PorderDeliveryDate#">#PorderDeliveryDate#</option>
							<option value="#PorderDeliveryNote#">#PorderDeliveryNote#</option>
							<option value="#PorderTotalAmount#">#PorderTotalAmount#</option>
							<option value="#Link#">#Link#</option>
						</select>
					</div>
					<div class="form-group">
						<label for="recipient-name" class="col-form-label">Email Body:</label>
						<textarea class="form-control summernote" placeholder="Enter Email Body"
								  name="update_email_body" id="update_email_body" autocomplete="off"></textarea>
						<small class="invalid-feedback update_email_body"><?php //echo form_error('name_supp'); ?></small>
					</div>
					<div class="form-group">
						<label for="recipient-name" class="col-form-label">Email Key</label>
						<input type="text" class="form-control" placeholder="Enter Email Key" name="update_email_key"
							   readonly id="update_email_key" autocomplete="off"/>
						<small class="invalid-feedback update_email_key"><?php //echo form_error('update_name_supp_cp'); ?></small>
					</div>
					<div class="form-group">
						<label for="recipient-name" class="col-form-label">Email Subject</label>
						<input type="text" class="form-control" placeholder="Enter Email Subject" name="update_email_subject"
							   id="update_email_subject" autocomplete="off"/>
						<small class="invalid-feedback update_email_subject"><?php //echo form_error('update_name_supp_cp'); ?></small>
					</div>
					<div class="form-group">
						<label for="recipient-name" class="col-form-label">Email CC</label>
						<input type="text" class="form-control" placeholder="Enter Email CC" name="update_email_cc"
							    id="update_email_cc" autocomplete="off"/>
						<small class="invalid-feedback update_email_cc"><?php //echo form_error('update_name_supp_cp'); ?></small>
					</div>
					<div class="form-group">
						<label for="recipient-name" class="col-form-label">Email BCC</label>
						<input type="text" class="form-control" placeholder="Enter Email BCC" name="update_email_bcc"
							    id="update_email_bcc" autocomplete="off"/>
						<small class="invalid-feedback update_email_bcc"><?php //echo form_error('update_name_supp_cp'); ?></small>
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


	<?php $this->load->view('admin/component/footer') ?>


	<script type="text/javascript">
		$(function () {
			$('#alert_msg').delay(6000).fadeOut();
			$('.alert-error, .invalid-feedback').delay(6000).fadeOut();
		});

		$(document).ready(function () {
			$('.summernote').summernote();
		});

		function insertAtCaret(areaId, elementID) {

			var text = $("#"+elementID + " option:selected").val();
			$('#'+areaId).summernote('editor.saveRange');
			$('#'+areaId).summernote('editor.restoreRange');
			$('#'+areaId).summernote('editor.focus');
			$('#'+areaId).summernote('editor.insertText', text);
		}

		/****************************************
		 *       Basic Table                   *
		 ****************************************/
		$('#zero_config').DataTable();


		function goto_add_record() {
			$('.reset-input').val('');
			$('#variables').val('#SupName#');
			$('#email_body').summernote('reset');
			$('#Modal_addrecord').modal('show');
		}

		function goto_submit_record() {
			$('.div_roller_total').fadeIn();
			$('.close_modal').hide();
			$('#submit_record_btn').prop('disabled', true);

			var delay = 8000;
			var e_error = 0;
			var error_message = 'There have some errors please check above, Try again.';
			var alphaletters_spaces = /^[A-Za-z ]+$/;
			var alphaletters = /^[A-Za-z]+$/;
			var alphanumerics = /^[A-Za-z0-9/() ]+$/;
			var alphanumerics_spaces = /^[A-Za-z0-9_.,\- ]+$/;
			var alphanumerics_no = /^[A-Za-z0-9_/&(@):.,\- ]+$/;
			var onlynumerics = /^[0-9]+$/;
			var specials_char = /[~`!#$%\^&*+=\[\]\\';./{}()|\\":<>\?]/g;
			var emailpattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;

			var email_name = $('#email_name').val();
			var email_key = $('#email_key').val();
			var email_subject = $('#email_subject').val();
			var email_body = $('#email_body').val();
			var email_cc = $('#email_cc').val();
			var email_bcc = $('#email_bcc').val();

			if (email_name == "") {
				e_error = 1;
				$('.email_name').html('Email Name is Required.');
			} else {

				$('.email_name').html('');
			}
			if (email_key == "") {
				e_error = 1;
				$('.email_key').html('Email Key is Required.');
			} else {
				$('.email_key').html('');
			}

			if (email_body == "") {
				e_error = 1;
				$('.email_key').html('Email body is Required.');
			} else {
				$('.email_body').html('');
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
				form_data.append("email_name", email_name);
				form_data.append("email_key", email_key);
				form_data.append("email_body", email_body);
				form_data.append("email_cc", email_cc);
				form_data.append("email_subject", email_subject);
				form_data.append("email_bcc", email_bcc);

				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/template/add_new_template') ?>",
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
								window.location.replace("<?php echo site_url('admincontrol/template/template_list') ?>");
							}, 2000);

						} else {
							$('.div_roller_total').fadeOut();
							$('#submit_record_btn').prop('disabled', false);
							$('.close_modal').show();
							error_message = data.e_msg;
							toastr.error(error_message, 'Error!');

						}

					}
				});
			}

		}

		function modify_record(element) {
			//alert(element);
			if (element != "") {
				var form_data = new FormData();
				form_data.append("id", element);

				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/template/get_details_of_template') ?>",
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
							$('#update_id_template').val(element);
							$('#update_email_name').val(data.s_msg.email_name);
							$('#update_email_key').val(data.s_msg.email_key);
							$('#update_email_bcc').val(data.s_msg.email_bcc);
							$('#update_email_cc').val(data.s_msg.email_cc);
							$('#update_email_subject').val(data.s_msg.email_subject);
							$('#update_email_body').summernote('code', data.s_msg.email_body);
							$('#Modal_editrecord').modal('show');

						} else {
							$('#update_id_template').val('');
							$('#Modal_editrecord').modal('hide');
						}

					}
				});
			} else {
				$('#update_id_template').val('');
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
			var specials_char = /[~`!#$%\^&*+=\[\]\\';./{}()|\\":<>\?]/g;
			var emailpattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;

			var update_id_template = $('#update_id_template').val();
			var update_email_name = $('#update_email_name').val();
			var update_email_key = $('#update_email_key').val();
			var update_email_body = $('#update_email_body').val();
			var update_email_cc = $('#update_email_cc').val();
			var update_email_subject = $('#update_email_subject').val();
			var update_email_bcc = $('#update_email_bcc').val();

			if (update_id_template == "") {
				error_message = error_message + "<br/>ID not Found, Refresh the Page and Try again.";
			}

			if (update_email_name == "") {
				e_error = 1;
				$('.update_email_name').html('Email Name is Required.');
			} else {

				$('.update_email_name').html('');

			}

			if (update_email_key == "") {
				e_error = 1;
				$('.update_email_key').html('Email Key is Required.');
			} else {
				$('.update_email_key').html('');
			}

			if (update_email_body == "") {
				e_error = 1;
				$('.update_email_body').html('Email Body is Required.');
			} else {
				$('.update_email_body').html('');
			}

			if (e_error == 1) {
				$('.div_roller_total2').fadeOut();
				$('#update_record_btn').prop('disabled', false);
				$('.close_modal2').show();
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
				form_data.append("update_id_template", update_id_template);
				form_data.append("update_email_name", update_email_name);
				form_data.append("update_email_key", update_email_key);
				form_data.append("update_email_body", update_email_body);
				form_data.append("update_email_subject", update_email_subject);
				form_data.append("update_email_cc", update_email_cc);
				form_data.append("update_email_bcc", update_email_bcc);

				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/template/modify_email') ?>",
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
								window.location.replace("<?php echo site_url('admincontrol/template/template_list') ?>");
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


	</script>
        
