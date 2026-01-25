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
                        <h4 class="page-title">Tax Group List</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url('admincontrol/dashboard'); ?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Supplier List</li>
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
								<?php if($this->session->flashdata('success')) { ?>
								<div id="alert_msg" class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
								<?php $this->session->unset_userdata('success'); }
								elseif($this->session->flashdata('e_error')) { ?>                
								<div id="alert_msg" class="alert alert-danger"><?php echo $this->session->flashdata('e_error'); ?></div>
								<?php $this->session->unset_userdata('e_error'); } ?>
								<?php 
								if($this->session->userdata('utype')==1 || $templateDetails->pt_m_taxgroup<3){?>
                                	<a href="javascript:;" onclick="goto_add_record();" class="btn btn-primary float-right mb-2" style="margin-right: 10px;">Add New Tax Group</a>
								<?php }?>
                                <div class="table-responsive">
                                    <table id="zero_config" class="table table-striped table-bordered">
                                        <thead>
                                            <tr style="font-weight: bold;">
                                                <th>Sl No.</th>
                                                <th>Name</th>
                                                <th>Percentage</th>
												<?php 
												if($this->session->userdata('utype')==1 || $templateDetails->pt_m_taxgroup<3){?>
													<th>Action</th>
												<?php }?>
                                            </tr>
                                        </thead>
                                        <tbody>
										<?php foreach($getrecord_list as $keys=>$recorditem)
										{ ?>
										<tr>
											<td><?php echo $keys+1; ?></td>
											<td><?php if($recorditem->procore_integration_status=='YES'){echo $recorditem->name.'<br><span style="background-color:#E64900;color:#fff;font-size:10px;font-weight:bold;padding:5px">PROCORE</span>';}else{echo $recorditem->name;} ?></td>
											<td><?php echo $recorditem->percentage; ?></td>
											<!--<td><?php //echo date('d-m-Y h:i A',strtotime($recorditem->cc_createdate)); ?></td>-->
											<?php 
											if($this->session->userdata('utype')==1 || $templateDetails->pt_m_taxgroup<3){?>
												<td>
													<a class="btn btn-outline-warning" onclick="modify_record(<?php echo $recorditem->id; ?>);" href="javascript:;" title="Edit Record"><i class="fa fa-edit text-primary"></i></a>
													<?php 
													if($recorditem->procore_integration_status!='YES' && ($this->session->userdata('utype')==1 || $templateDetails->pt_m_taxgroup<2)){?>
														<a class="btn btn-outline-warning" onclick="return confirm('You are about to delete a record. This cannot be undone. Are you sure?');" href="<?php echo base_url().'admincontrol/taxgroup/delete_record/'.$recorditem->id; ?>" title="Delete Record"><i class="fa fa-trash text-danger"></i></a>
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
	<div class="modal fade" id="Modal_addrecord" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Add New Tax Group</h5>
					<button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Group Name:</label>
							<input type="text" class="form-control reset-input" placeholder="Enter Name" name="name" id="name" autocomplete="off" />
							<small class="invalid-feedback name"><?php //echo form_error('name_supp'); ?></small>
						</div>

					<div class="form-group">
						<label for="recipient-name" class="col-form-label">Description:</label>
						<textarea class="form-control reset-input" placeholder="Enter Description" name="description" id="description" autocomplete="off"></textarea>
						<small class="invalid-feedback name"><?php //echo form_error('name_supp'); ?></small>
					</div>

						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Percentage: </label>
							<input type="text" class="form-control reset-input" placeholder="Enter Percentage" name="percentage" id="percentage" autocomplete="off" />
							<small class="invalid-feedback percentage"><?php //echo form_error('name_supp_cp'); ?></small>
						</div>
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
					<h5 class="modal-title">Update Tax Group Details</h5>
					<button type="button" class="close close_modal2" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Group Name:</label>
							<input type="hidden" name="update_id_taxgroup" id="update_id_taxgroup" value="" autocomplete="off" />
							<input type="text" class="form-control" placeholder="Enter Tax Group Name" name="update_name" id="update_name" autocomplete="off" />
							<small class="invalid-feedback update_name"><?php //echo form_error('update_name_supp'); ?></small>
						</div>
					<div class="form-group">
						<label for="recipient-name" class="col-form-label">Description:</label>
						<textarea class="form-control" placeholder="Enter Description" name="updated_description" id="updated_description" autocomplete="off"></textarea>
						<small class="invalid-feedback name"><?php //echo form_error('name_supp'); ?></small>
					</div>
					<div class="form-group">
							<label for="recipient-name" class="col-form-label">Percentage</label>
							<input type="text" class="form-control" placeholder="Enter Percentage" name="update_percentage" id="update_percentage" autocomplete="off" />
							<small class="invalid-feedback update_percentage"><?php //echo form_error('update_name_supp_cp'); ?></small>
						</div>

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
		$('.reset-input').val('');
		$('#Modal_addrecord').modal('show');
	}
	
	function goto_submit_record(){
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
		
		var name = $('#name').val();
		var percentage = $('#percentage').val();
		var description = $('#description').val();

		if(name == ""){
			e_error = 1;
			$('.name').html('Tax Group Name is Required.');
		}else{
			if(!name.match(alphanumerics_no)){
				e_error = 1;
				$('.name').html('Tax Group Name not use special characters [without _ . , -], Check again.');
			}else{
				$('.name').html('');
			}	
		}
		if(percentage == ""){
			e_error = 1;
			$('.percentage').html('Percentage is Required.');
		}else{
			if(!percentage.match(alphanumerics_spaces)){
				e_error = 1;
				$('.percentage').html('Percentage not use special characters [without _ . , -], Check again.');
			}else{
				$('.percentage').html('');
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
			form_data.append("name", name);
			form_data.append("percentage", percentage);
			form_data.append("description", description);

			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/taxgroup/add_new_taxgroup_sets') ?>",
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
							window.location.replace("<?php echo site_url('admincontrol/taxgroup/tax_group_list') ?>");
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
			form_data.append("id", element);
			
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/taxgroup/get_details_of_taxgroup') ?>",
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
						$('#update_id_taxgroup').val(element);
						$('#update_name').val(data.s_msg.name);
						$('#update_percentage').val(data.s_msg.percentage);
						$('#updated_description').val(data.s_msg.description);
						$('#Modal_editrecord').modal('show');
						
					}else{
						$('#update_id_taxgroup').val('');
						$('#Modal_editrecord').modal('hide');
					}
					
				}
			});
		}else{
			$('#update_id_taxgroup').val('');
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
		
		var update_id_taxgroup = $('#update_id_taxgroup').val();
		var update_name = $('#update_name').val();
		var update_percentage = $('#update_percentage').val();
		var updated_description = $('#updated_description').val();

		if(update_id_taxgroup == ""){
			error_message = error_message + "<br/>ID not Found, Refresh the Page and Try again.";
		}
		
		if(update_name == ""){
			e_error = 1;
			$('.update_name').html('Supplier Name is Required.');
		}else{
			if(!update_name.match(alphanumerics_no)){
				e_error = 1;
				$('.update_name').html('Supplier Name not use special characters [without _ . , -], Check again.');
			}else{
				$('.update_name').html('');
			}	
		}
		if(update_percentage == ""){
			e_error = 1;
			$('.update_percentage').html('Tax Group Percentage is Required.');
		}else{
			if(!update_percentage.match(alphanumerics_spaces)){
				e_error = 1;
				$('.update_percentage').html('Tax Group Percentage not use special characters [without _ . , -], Check again.');
			}else{
				$('.update_percentage').html('');
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
			form_data.append("update_id_taxgroup", update_id_taxgroup);
			form_data.append("update_name", update_name);
			form_data.append("update_percentage", update_percentage);
			form_data.append("updated_description", updated_description);

			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/taxgroup/modify_taxgroup_sets') ?>",
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
							window.location.replace("<?php echo site_url('admincontrol/taxgroup/tax_group_list') ?>");
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
        
