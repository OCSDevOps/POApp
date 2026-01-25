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
				<h4 class="page-title">Perform CheckLists</h4>
				<div class="ml-auto text-right">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a
										href="<?php echo base_url('admincontrol/dashboard'); ?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Perform CheckLists</li>
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
						<!-- <a href="javascript:;" onclick="goto_add_record();" class="btn btn-primary float-right mb-2"
						   style="margin-right: 10px;">Add New CheckList</a> -->
						<div class="table-responsive">
							<table id="zero_config" class="table table-striped table-bordered">
								<thead>
								<tr style="font-weight: bold;">
									<th>Sl No.</th>
									<th>Checklist Name</th>
									<th>Equipment Name</th>
									<th>Frequency</th>
									<th>Assigned Users</th>
									<th>Status</th>
									<th>Action</th>
								</tr>
								</thead>
								<tbody>
								<?php 
								$count=1;
								foreach($assets as $asset){
									${'asset'.$asset->eq_id}=$asset->eqm_asset_name;
								}
								foreach ($getrecord_list as $keys => $recorditem) {
									$eq_ids=json_decode($recorditem->cl_eq_ids, true);
									foreach($eq_ids as $eq_id){
								?>
									<tr>
										<td><?php echo $count; ?></td>
										<td><?php echo $recorditem->cl_name; ?></td>
										<td><?php echo ${'asset'.$eq_id}; ?></td>
										<td>
											<?php 
												if($recorditem->cl_frequency==1){
													echo 'Daily';
												}else if($recorditem->cl_frequency==2){
													echo 'Weekly';
												}else if($recorditem->cl_frequency==3){
													echo 'Monthly';
												}else if($recorditem->cl_frequency==4){
													echo 'Half Yearly';
												}else if($recorditem->cl_frequency==5){
													echo 'Yearly';
												} 
											?>
										</td>
										<td>
											<?php 
												foreach(json_decode($recorditem->cl_user_ids) as $u){
													echo $usersArray[$u].'<br>';
												} 
											?>
										</td>
										<td>Pending</td>
										<td>
											<a class="btn btn-outline-danger"
											   onclick="goto_perform_checklist(<?php echo $recorditem->cl_id; ?>,<?php echo $eq_id;?>);"
											   href="javascript:;" title="Edit Record">Perform Checklist</a>

										</td>
									</tr>
								<?php $count++;}} ?>
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

	
	<!-- Model to perform checklist start -->
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
					<input type="hidden" name="hidden-eq-id" id="hidden-eq-id">
					<div class="row">
						<div class="col-12 col-md-6">
							<div class="form-group row">
								<label for="cl_eq_id" class="col-sm-3 text-right control-label col-form-label">Equipment</label>
								<div class="col-sm-9">
									<select class="form-control select2 custom-select" name="cl_eq_id" id="cl_eq_id" autocomplete="off" disabled>
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
											<th>Attachment</th>
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

		function goto_perform_checklist(cl_id,eq_id) {
			if (cl_id != "") {

				var form_data = new FormData();
				form_data.append("cl_id", cl_id);

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
							$('#hidden-cl-id').val(cl_id);
							$('#hidden-eq-id').val(eq_id);
							$('#cl_eq_id').val(eq_id).change();
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
										<td>\
											<input type="file" class="form-control perform_clitem_attachment" id="perform_clitem_attachment'+(k+1)+'"/>\
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
				
				var tableRow=0;
				// var performArray=[];
				for(i=1;i<=$('#perform_checklist_details_table tbody tr').length;i++){
					form_data.append("perform_clitem_id"+i, $('#perform-hidden-clitem-id'+i).val());
					form_data.append("perform_clitem_value"+i, $('#perform_clitem_value'+i).val());
					form_data.append("perform_clitem_notes"+i, $('#perform_clitem_notes'+i).val());
					form_data.append("perform_clitem_attachment"+i, $('#perform_clitem_attachment'+i)[0].files[0]);
					tableRow++;
					// performArray.push({
					// 	id: $('#perform-hidden-clitem-id'+i).val(), 
					// 	value:  $('#perform_clitem_value'+i).val(),
					// 	notes:  $('#perform_clitem_notes'+i).val()
					// });
				}

				// form_data.append("cl_item_values", JSON.stringify(performArray));

				form_data.append("row_count", tableRow);

				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/PerformChecklist/perform_checklist_submission') ?>",
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
								window.location.replace("<?php echo site_url('admincontrol/PerformChecklist/all_checklist_list') ?>");
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
						// alert(JSON.stringify(request));
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
        
