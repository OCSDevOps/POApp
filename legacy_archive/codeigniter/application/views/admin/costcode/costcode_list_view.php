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
                        <h4 class="page-title">Cost Code List</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url('admincontrol/dashboard'); ?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Cost Code List</li>
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
								if($this->session->userdata('utype')==1 || $templateDetails->pt_m_costcode<3){?>
									<a href="javascript:;" onclick="goto_add_record();" class="btn btn-primary float-right mb-2" style="margin-right: 10px;">Add New Cost Code</a>
									<a href="javascript:;" onclick="goto_bulkupload_record();" class="btn btn-primary float-right mb-2" style="margin-right: 10px;">Bulk upload - CostCode</a>
								<?php }?>
                                <div class="table-responsive">
                                    <table id="zero_config" class="table table-striped table-bordered">
                                        <thead>
                                            <tr style="font-weight: bold;">
                                                <th>Sl No.</th>
                                                <th>Code</th>
												<th>Description</th>
												<th>Status</th>
												<?php 
												if($this->session->userdata('utype')==1 || $templateDetails->pt_m_costcode<3){?>
													<th>Action</th>
												<?php }?>
                                            </tr>
                                        </thead>
                                        <tbody>
										<?php foreach($getrecord_list as $keys=>$recorditem)
										{ ?>
										<tr>
											<td><?php echo $keys+1; ?></td>
											<td><?php echo $recorditem->cc_no; ?></td>
											<td><?php if($recorditem->procore_integration_status=='YES'){echo $recorditem->cc_description.'<br><span style="background-color:#E64900;color:#fff;font-size:10px;font-weight:bold;padding:5px">PROCORE</span>';}else{echo $recorditem->cc_description;} ?></td>
											<!--<td><?php //echo date('d-m-Y h:i A',strtotime($recorditem->cc_createdate)); ?></td>-->
											<td><?php if($recorditem->cc_status == 1){ ?>
												  <span style="color:green;">Active</span>
											  <?php }elseif($recorditem->cc_status == 0){ ?>
												<span style="color:red;">InActive</span>
											  <?php } ?></td>
											<?php 
											if($this->session->userdata('utype')==1 || $templateDetails->pt_m_costcode<3){?>
												<td>
													<a class="btn btn-outline-warning" onclick="modify_record(<?php echo $recorditem->cc_id; ?>);" href="javascript:;<?php //echo base_url().'admincontrol/dashboard/edit_user/'.$recorditem->cc_id; ?>" title="Edit Record"><i class="fa fa-edit text-primary"></i></a>
													<?php if($recorditem->cc_status == 1){ ?>	
													<a class="btn btn-outline-warning" href="<?php echo base_url().'admincontrol/costcode/lock_costcodeset/'.$recorditem->cc_id; ?>" title="Lock Record"><i class="fa fa-unlock text-dark"></i></a>
													<?php } else { ?>
													<a class="btn btn-outline-warning" href="<?php echo base_url().'admincontrol/costcode/unlock_costcodeset/'.$recorditem->cc_id; ?>" title="Unock Record"><i class="fa fa-lock text-dark"></i></a>
													<?php } ?>
													<?php 
													if($recorditem->procore_integration_status!='YES' && ($this->session->userdata('utype')==1 || $templateDetails->pt_m_costcode<2)){?>
														<a class="btn btn-outline-warning" onclick="return confirm('You are about to delete a record. This cannot be undone. Are you sure?');" href="<?php echo base_url().'admincontrol/costcode/delete_itemset/'.$recorditem->cc_id; ?>" title="Delete Record"><i class="fa fa-trash text-danger"></i></a>
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
					<h5 class="modal-title">Add New Cost Code</h5>
					<button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Cost Code:</label>
							<input type="text" class="form-control reset-input" placeholder="Enter Code" name="name_cc" id="name_cc" autocomplete="off" />
							<small class="invalid-feedback name_cc"><?php //echo form_error('name_cc'); ?></small>
						</div>
						<div class="form-group">
							<label for="message-text" class="col-form-label">Description:</label>
							<textarea class="form-control reset-input" placeholder="Enter Description" id="desc_cc" name="desc_cc" autocomplete="off"></textarea>
							<small class="invalid-feedback desc_cc"><?php //echo form_error('desc_cc'); ?></small>
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
					<h5 class="modal-title">Update Cost Code</h5>
					<button type="button" class="close close_modal2" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Cost Code:</label>
							<input type="hidden" name="update_id_cc" id="update_id_cc" value="" autocomplete="off" />
							<input type="text" class="form-control" placeholder="Enter Name" name="update_name_cc" id="update_name_cc" autocomplete="off" />
							<small class="invalid-feedback update_name_cc"><?php //echo form_error('update_name_cc'); ?></small>
						</div>
						<div class="form-group">
							<label for="message-text" class="col-form-label">Description:</label>
							<textarea class="form-control" placeholder="Enter Description" id="update_desc_cc" name="update_desc_cc" autocomplete="off"></textarea>
							<small class="invalid-feedback update_desc_cc"><?php //echo form_error('update_desc_cc'); ?></small>
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
	
	<!-- Modal -->
	<div class="modal fade" id="Modal_bulkupload_record" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel3" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Bulk upload - Cost Code</h5>
					<button type="button" class="close close_modal3" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Upload Excel :</label>
							<input type="hidden" name="update_cat" id="update_cat" value="BULK" />
							<input type="file" class="form-control" name="upload_item_cat" id="upload_item_cat" autocomplete="off" />
							<small class="invalid-feedback upload_item_cat"></small>
						</div>
						<div class="col-sm-12 text-center">
							<div align="center">
								<div class="get_error_total3" align="center" style="background-color: #bf0000;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
								<div class="get_success_total3" align="center" style="background-color: #174b10;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
								<div class="div_roller_total3" align="center" style="display: none;"><img src="<?php echo base_url(); ?>style/images/ajax_loader.gif" style="max-width: 60px;" /></div>
							</div>
						</div>
				</div>
				<div class="modal-footer">
					<!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>-->
					<button type="button" id="upload_bulk_btn" class="btn btn-primary" onclick="goto_upload_setof_record();">Upload</button>
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
		var error_message = 'There have some errors plese check above, Try again.';
		var alphaletters_spaces = /^[A-Za-z ]+$/;
		var alphaletters = /^[A-Za-z]+$/;
		var alphanumerics = /^[A-Za-z0-9/() ]+$/;
		var alphanumerics_spaces = /^[A-Za-z0-9_.,\- ]+$/;
		var alphanumerics_no = /^[A-Za-z0-9_/&(@):.,\- ]+$/;
		var onlynumerics = /^[0-9]+$/;
		var specials_char = /[~`!#$%\^&*+=\[\]\\';./{}()|\\":<>\?]/g;
		var emailpattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;
		
		var name_cc = $('#name_cc').val();
		var desc_cc = $('#desc_cc').val();
		
		if(name_cc == ""){
			e_error = 1;
			$('.name_cc').html('Cost Code is Required.');
		}else{
			if(!name_cc.match(alphanumerics_spaces)){
				e_error = 1;
				$('.name_cc').html('Cost Code not use special carecters [without _ . , -], Check again.');
			}else{
				$('.name_cc').html('');
			}	
		}
		
		if(desc_cc == ""){
			e_error = 1;
			$('.desc_cc').html('Description is Required.');
		}else{
			if(!desc_cc.match(alphanumerics_no)){
				e_error = 1;
				$('.desc_cc').html('Description not use special carecters [without _ & ( @ ) : . , -], Check again.');
			}else{
				$('.desc_cc').html('');
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
			form_data.append("name_cc", name_cc);
			form_data.append("desc_cc", desc_cc);
			
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/costcode/add_new_costcode_set') ?>",
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
							window.location.replace("<?php echo site_url('admincontrol/costcode/cost_code_list') ?>");
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
			form_data.append("name_ccid", element);
			
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/costcode/get_details_of_ccode') ?>",
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
						$('#update_id_cc').val(element);
						$('#update_name_cc').val(data.s_msg.cc_no);
						$('#update_desc_cc').val(data.s_msg.cc_description);
						$('#Modal_editrecord').modal('show');
						
					}else{
						$('#update_id_cc').val('');
						$('#Modal_editrecord').modal('hide');
					}
					
				}
			});
		}else{
			$('#update_id_cc').val('');
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
		
		var update_id_cc = $('#update_id_cc').val();
		var update_name_cc = $('#update_name_cc').val();
		var update_desc_cc = $('#update_desc_cc').val();
		
		if(update_id_cc == ""){
			error_message = error_message + "<br/>ID not Found, Refresh the Page and Try again.";
		}
		
		if(update_name_cc == ""){
			e_error = 1;
			$('.update_name_cc').html('Cost Code is Required.');
		}else{
			if(!update_name_cc.match(alphanumerics_spaces)){
				e_error = 1;
				$('.update_name_cc').html('Cost Code not use special carecters [without _ . , -], Check again.');
			}else{
				$('.update_name_cc').html('');
			}	
		}
		
		if(update_desc_cc == ""){
			e_error = 1;
			$('.update_desc_cc').html('Description is Required.');
		}else{
			if(!update_desc_cc.match(alphanumerics_no)){
				e_error = 1;
				$('.update_desc_cc').html('Description not use special carecters [without _ & ( @ ) : . , -], Check again.');
			}else{
				$('.update_desc_cc').html('');
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
			form_data.append("update_id_cc", update_id_cc);
			form_data.append("update_name_cc", update_name_cc);
			form_data.append("update_desc_cc", update_desc_cc);
			
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/costcode/modify_costcode_sets') ?>",
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
							window.location.replace("<?php echo site_url('admincontrol/costcode/cost_code_list') ?>");
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

	
	
	function goto_bulkupload_record(){
		$('#Modal_bulkupload_record').modal('show');
	}

	function goto_upload_setof_record(){
		$('.div_roller_total3').fadeIn();
		$('.close_modal3').hide();
		$('#upload_bulk_btn').prop('disabled', true);
		
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
		var allowedExtensions = /(\.xls|\.XLS|\.xlsx|\.XLSX)$/i;

		var update_cat = $('#update_cat').val();
		var files = $('#upload_item_cat')[0].files;

		if (document.getElementById("upload_item_cat").files.length == 0) {
			e_error = 1;
			$('.upload_item_cat').html('Upload File is Required.');
		} else {
			var fileInput = document.getElementById('upload_item_cat');
			var filePath = fileInput.value;
			if (!allowedExtensions.exec(filePath)) {
				e_error = 1;
				$('.upload_item_cat').html('Upload File type Invalid.(Use Excel File Only)');
			} else {
				$('.upload_item_cat').html('');
			}
		}
		
		if (e_error == 1) {
			$('.div_roller_total3').fadeOut();
			$('#upload_bulk_btn').prop('disabled', false);
			$('.close_modal3').show();
			//$('.get_error_total').html(error_message);
			//$(".get_error_total").fadeIn();
			toastr.error(error_message, 'Error!');
			$(".invalid-feedback").fadeIn();
			/*e_error = 0;
			error_message = '';*/
			setTimeout(function() {
				$('.invalid-feedback, .get_error_total3').fadeOut();
			}, delay);
		} else {
			
			var form_data = new FormData();
			form_data.append("update_cat", update_cat);
			form_data.append("files", files[0]);
			
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/costcode/bulkitem_upload_section_sets') ?>",
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
						$('.div_roller_total3').fadeOut();
						toastr.success('Record is Uploaded Successfully!', 'Success');
						setTimeout(function(){ 
							window.location.replace("<?php echo site_url('admincontrol/costcode/cost_code_list') ?>");
						}, 2000);
						
					}else{
						$('.div_roller_total3').fadeOut();
						$('#upload_bulk_btn').prop('disabled', false);
						$('.close_modal3').show();
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
        
