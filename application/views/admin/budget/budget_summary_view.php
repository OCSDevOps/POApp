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
                        <h4 class="page-title">Budget List</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url('admincontrol/dashboard'); ?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Budget List</li>
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
                <!-- <div class="row">
					<div class="col-12">
						<div class="card">
							<div class="card-body">
								<form action="<?=base_url('admincontrol/budget/budget_list')?>" method="post" enctype="multipart/form-data">
									<div class="row">
									<label class="col-sm-1 text-right control-label col-form-label">Project</label>
									<div class="col-sm-2">
										<select name="project" class="form-control">
											<option value="">Select Option</option>

											<?php foreach ($getproject_list as $project) {?>
											<option value="<?php echo $project->project_id ?>" ><?php echo $project->proj_name?></option>
											<?php } ?>
										</select>
									</div>
									<label class="col-sm-1 text-right control-label col-form-label">Division</label>
									<div class="col-sm-2">
										<select name="division" class="form-control">
											<option value="">Select Option</option>
											<?php foreach ($getdivision_list as $division) {?>
												<option value="<?php echo $division->division_code ?>" ><?php echo $division->division_name?></option>
											<?php } ?>
										</select>
									</div>
									<div class="col-sm-2">
										<button type="submit" class="btn btn-primary">Search</button>
										<button type="button" onclick="window.location.href='<?php echo base_url("admincontrol/procore/procore_view"); ?>'" class="btn btn-success">Reset</button>
									</div>
								</div>
								</form>

							</div>
						</div>
					</div>
				</div> -->

				<div class="row">
                    <div class="col-12">
						<div class="card">
                            <div class="card-body">
								<!-- <?php if($this->session->flashdata('success')) { ?>
								<div id="alert_msg" class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
								<?php $this->session->unset_userdata('success'); }
								elseif($this->session->flashdata('e_error')) { ?>                
								<div id="alert_msg" class="alert alert-danger"><?php echo $this->session->flashdata('e_error'); ?></div>
								<?php $this->session->unset_userdata('e_error'); } ?>
                                <a href="javascript:;" onclick="goto_add_record();" class="btn btn-primary float-right mb-2" style="margin-right: 10px;">Add New Unit of Measures</a> -->
                                <div class="table-responsive">
                                    <table id="zero_config" class="table table-striped table-bordered">
                                        <thead>
                                            <tr style="font-weight: bold;">
                                                <th>Sl No.</th>
                                                <th>Project</th>
												<th>Original budget</th>
												<th>Revised budget</th>
												<th>Committed Cost</th>
												<th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
										<?php foreach($getsummary_list as $keys=>$recorditem)
										{ ?>
										<tr>
											<td><?php echo $keys+1; ?></td>
											<td><?php if($recorditem->procore_integration_status=='YES'){echo $recorditem->proj_name.'<br><span style="background-color:#E64900;color:#fff;font-size:10px;font-weight:bold;padding:5px">PROCORE</span>';}else{echo $recorditem->proj_name;} ?></td>
											<td>
												<?php 
													$formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
													echo $formatter->formatCurrency($recorditem->original_budget, 'USD'); 
												?>
											</td>
											<td><?php echo $formatter->formatCurrency($recorditem->revised_budget, 'USD'); ?></td>
											<td><?php echo $formatter->formatCurrency($recorditem->commited_cost, 'USD'); ?></td>
											<td style="text-align:center">
												<form action="<?=base_url('admincontrol/budget/budget_list')?>" method="post">
													<input type="hidden" name="project" id="project" value="<?php echo $recorditem->project_id;?>">
													<input type="hidden" name="division" id="division" value="">
													<button type="submit" class="btn btn-sm btn-danger">View Details</a>
												</form>
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
            
	<!-- Modal -->
	<div class="modal fade" id="Modal_addrecord" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Add New Unit of Measures</h5>
					<button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Name of Unit of Measures:</label>
							<input type="text" class="form-control" placeholder="Enter Name" name="name_um" id="name_um" autocomplete="off" />
							<small class="invalid-feedback name_um"><?php //echo form_error('name_um'); ?></small>
						</div>
						<!--<div class="form-group">
							<label for="message-text" class="col-form-label">Message:</label>
							<textarea class="form-control" id="message-text"></textarea>
						</div>-->
						<div class="col-sm-12 text-center">
							<div align="center">
								<div class="get_error_total" align="center" style="background-color: #bf0000;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
								<div class="get_success_total" align="center" style="background-color: #174b10;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
								<div class="div_roller_total" align="center" style="display: none;"><img src="<?php echo base_url(); ?>style/images/ajax_loader.gif" style="max-width: 60px;" /></div>
							</div>
						</div>
				</div>
				<div class="modal-footer">
					<!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>-->
					<button type="button" id="submit_record_btn" class="btn btn-primary" onclick="goto_submit_record();">Submit</button>
				</div>
			</div>
		</div>
	</div>
	<!-- Modal -->
	<div class="modal fade" id="Modal_editrecord" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Modify Unit of Measures</h5>
					<button type="button" class="close close_modal2" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Name of Unit of Measures:</label>
							<input type="hidden" name="update_id_um" id="update_id_um" value="" autocomplete="off" />
							<input type="text" class="form-control" placeholder="Enter Name" name="update_name_um" id="update_name_um" autocomplete="off" />
							<small class="invalid-feedback update_name_um"><?php //echo form_error('update_name_um'); ?></small>
						</div>
						<!--<div class="form-group">
							<label for="message-text" class="col-form-label">Message:</label>
							<textarea class="form-control" id="message-text"></textarea>
						</div>-->
						<div class="col-sm-12 text-center">
							<div align="center">
								<div class="get_error_total2" align="center" style="background-color: #bf0000;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
								<div class="get_success_total2" align="center" style="background-color: #174b10;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
								<div class="div_roller_total2" align="center" style="display: none;"><img src="<?php echo base_url(); ?>style/images/ajax_loader.gif" style="max-width: 60px;" /></div>
							</div>
						</div>
				</div>
				<div class="modal-footer">
					<!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>-->
					<button type="button" id="update_record_btn" class="btn btn-primary" onclick="goto_update_record();">Update</button>
				</div>
			</div>
		</div>
	</div>
	


<?php $this->load->view('admin/component/footer') ?>



<script type="text/javascript">
	$(function(){
	      $('#alert_msg').delay(6000).fadeOut();
		  $('.alert-error, .invalid-feedback').delay(6000).fadeOut();
	});
		/****************************************
         *       Basic Table                   *
         ****************************************/
        $('#zero_config').DataTable();
		
		
	function goto_add_record(){
		$('#Modal_addrecord').modal('show');
	}
	
	function goto_submit_record(){
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
		var specials_char = /[~`!#$%\^&*+=\[\]\\';./{}()|\\":<>\?]/g;
		var emailpattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;
		
		var name_um = $('#name_um').val();
		
		if(name_um == ""){
			e_error = 1;
			$('.name_um').html('Unit Name is Required.');
		}else{
			if(!name_um.match(alphanumerics_spaces)){
				e_error = 1;
				$('.name_um').html('Unit Name not use special carecters [without _ . , -], Check again.');
			}else{
				$('.name_um').html('');
			}	
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
			setTimeout(function() {
				$('.invalid-feedback, .get_error_total').fadeOut();
			}, delay);
		} else {
			
			var form_data = new FormData();
			form_data.append("name_um", name_um);
			
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/uom/add_new_unit_of_measures') ?>",
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
						toastr.success('Record is Inserted Successfully!', 'Success');
						setTimeout(function(){ 
							window.location.replace("<?php echo site_url('admincontrol/uom/unit_of_measures_list') ?>");
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
		
	}

	function modify_record(element){
		//alert(element);
		if(element != ""){
			var form_data = new FormData();
			form_data.append("name_uomid", element);
			
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/uom/get_details_of_uom') ?>",
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
						$('#update_id_um').val(element);
						$('#update_name_um').val(data.s_msg.uom_name);
						$('#Modal_editrecord').modal('show');
						
					}else{
						$('#update_id_um').val('');
						$('#Modal_editrecord').modal('hide');
					}
					
				}
			});
		}else{
			$('#update_id_um').val('');
			$('#Modal_editrecord').modal('hide');
		}
	}

	function goto_update_record(){
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
		
		var update_id_um = $('#update_id_um').val();
		var update_name_um = $('#update_name_um').val();
		
		if(update_id_um == ""){
			error_message = error_message + "<br/>ID not Found, Refresh the Page and Try again.";
		}
		if(update_name_um == ""){
			e_error = 1;
			$('.update_name_um').html('Unit Name is Required.');
		}else{
			if(!update_name_um.match(alphanumerics_spaces)){
				e_error = 1;
				$('.update_name_um').html('Unit Name not use special carecters [without _ . , -], Check again.');
			}else{
				$('.update_name_um').html('');
			}	
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
			setTimeout(function() {
				$('.invalid-feedback, .get_error_total2').fadeOut();
			}, delay);
		} else {
			
			var form_data = new FormData();
			form_data.append("update_id_um", update_id_um);
			form_data.append("update_name_um", update_name_um);
			
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/uom/modify_unit_of_measures') ?>",
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
						$('.div_roller_total2').fadeOut();
						toastr.success('Record is Updated Successfully!', 'Success');
						setTimeout(function(){ 
							window.location.replace("<?php echo site_url('admincontrol/uom/unit_of_measures_list') ?>");
						}, 2000);
						
					}else{
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
        
