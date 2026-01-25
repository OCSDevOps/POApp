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
                        <h4 class="page-title">Supplier List</h4>
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
                                <a href="javascript:;" onclick="goto_add_record();" class="btn btn-primary float-right mb-2" style="margin-right: 10px;">Add New Supplier</a>
                                <div class="table-responsive">
                                    <table id="zero_config" class="table table-striped table-bordered">
                                        <thead>
                                            <tr style="font-weight: bold;">
                                                <th>Sl No.</th>
                                                <th>Supplier</th>
                                                <th>Contact Person</th>
                                                <th>Mobile</th>
                                                <th>Email</th>
												<th>Address</th>
												<th>Status</th>
												<th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
										<?php foreach($getrecord_list as $keys=>$recorditem)
										{ ?>
										<tr>
											<td><?php echo $keys+1; ?></td>
											<td><?php if($recorditem->procore_integration_status=='YES'){echo $recorditem->sup_name.'<br><span style="background-color:#E64900;color:#fff;font-size:10px;font-weight:bold;padding:5px">PROCORE</span>';}else{echo $recorditem->sup_name;} ?></td>
											<td><?php echo $recorditem->sup_contact_person; ?></td>
											<td><?php echo $recorditem->sup_phone; ?></td>
											<td><?php echo $recorditem->sup_email; ?></td>
											<td><?php echo $recorditem->sup_address; ?></td>
											<!--<td><?php //echo date('d-m-Y h:i A',strtotime($recorditem->cc_createdate)); ?></td>-->
											<td><?php if($recorditem->sup_status == 1){ ?>
												  <span style="color:green;">Active</span>
											  <?php }elseif($recorditem->sup_status == 0){ ?>
												<span style="color:red;">InActive</span>
											  <?php } ?></td>
											<td>
												<a class="btn btn-outline-warning" onclick="modify_record(<?php echo $recorditem->sup_id; ?>);" href="javascript:;<?php //echo base_url().'admincontrol/suppliers/edit_user/'.$recorditem->sup_id; ?>" title="Edit Record"><i class="fa fa-edit text-primary"></i></a>
												<?php if($recorditem->sup_status == 1){ ?>	
												<a class="btn btn-outline-warning" href="<?php echo base_url().'admincontrol/suppliers/lock_supplier/'.$recorditem->sup_id; ?>" title="Lock Record"><i class="fa fa-unlock text-dark"></i></a>
												<?php } else { ?>
												<a class="btn btn-outline-warning" href="<?php echo base_url().'admincontrol/suppliers/unlock_supplier/'.$recorditem->sup_id; ?>" title="Unock Record"><i class="fa fa-lock text-dark"></i></a>
												<?php } ?>
												<a class="btn btn-outline-warning" onclick="return confirm('You are about to delete a record. This cannot be undone. Are you sure?');" href="<?php echo base_url().'admincontrol/suppliers/delete_itemset/'.$recorditem->sup_id; ?>" title="Delete Record"><i class="fa fa-trash text-danger"></i></a>
												
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
					<h5 class="modal-title">Add New Supplier</h5>
					<button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Supplier Name:</label>
							<input type="text" class="form-control reset-input" placeholder="Enter Supplier Name" name="name_supp" id="name_supp" autocomplete="off" />
							<small class="invalid-feedback name_supp"><?php //echo form_error('name_supp'); ?></small>
						</div>
						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Contact Person Name:</label>
							<input type="text" class="form-control reset-input" placeholder="Enter Contact Person Name" name="name_supp_cp" id="name_supp_cp" autocomplete="off" />
							<small class="invalid-feedback name_supp_cp"><?php //echo form_error('name_supp_cp'); ?></small>
						</div>
						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Contact Mobile:</label>
							<input type="text" class="form-control reset-input" placeholder="Enter Contact Mobile" name="supp_phone" id="supp_phone" autocomplete="off" />
							<small class="invalid-feedback supp_phone"><?php //echo form_error('supp_phone'); ?></small>
						</div>
						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Contact Email:</label>
							<input type="text" class="form-control reset-input" placeholder="Enter Contact Email" name="supp_email" id="supp_email" autocomplete="off" />
							<small class="invalid-feedback supp_email"><?php //echo form_error('supp_email'); ?></small>
						</div>
						<div class="form-group">
							<label for="message-text" class="col-form-label">Supplier Address:</label>
							<textarea class="form-control reset-input" placeholder="Enter Address" id="supp_address" name="supp_address" autocomplete="off"></textarea>
							<small class="invalid-feedback supp_address"><?php //echo form_error('supp_address'); ?></small>
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
					<h5 class="modal-title">Update Supplier Details</h5>
					<button type="button" class="close close_modal2" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Supplier Name:</label>
							<input type="hidden" name="update_id_sup" id="update_id_sup" value="" autocomplete="off" />
							<input type="text" class="form-control" placeholder="Enter Supplier Name" name="update_name_supp" id="update_name_supp" autocomplete="off" />
							<small class="invalid-feedback update_name_supp"><?php //echo form_error('update_name_supp'); ?></small>
						</div>
						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Contact Person Name:</label>
							<input type="text" class="form-control" placeholder="Enter Contact Person Name" name="update_name_supp_cp" id="update_name_supp_cp" autocomplete="off" />
							<small class="invalid-feedback update_name_supp_cp"><?php //echo form_error('update_name_supp_cp'); ?></small>
						</div>
						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Contact Mobile:</label>
							<input type="text" class="form-control" placeholder="Enter Contact Mobile" name="update_supp_phone" id="update_supp_phone" autocomplete="off" />
							<small class="invalid-feedback update_supp_phone"><?php //echo form_error('update_supp_phone'); ?></small>
						</div>
						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Contact Email:</label>
							<input type="text" class="form-control" placeholder="Enter Contact Email" name="update_supp_email" id="update_supp_email" autocomplete="off" />
							<small class="invalid-feedback update_supp_email"><?php //echo form_error('update_supp_email'); ?></small>
						</div>
						<div class="form-group">
							<label for="message-text" class="col-form-label">Supplier Address:</label>
							<textarea class="form-control" placeholder="Enter Address" id="update_supp_address" name="update_supp_address" autocomplete="off"></textarea>
							<small class="invalid-feedback update_supp_address"><?php //echo form_error('update_supp_address'); ?></small>
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
		var error_message = 'There have some errors plese check above, Try again.';
		var alphaletters_spaces = /^[A-Za-z ]+$/;
		var alphaletters = /^[A-Za-z]+$/;
		var alphanumerics = /^[A-Za-z0-9/() ]+$/;
		var alphanumerics_spaces = /^[A-Za-z0-9_.,\- ]+$/;
		var alphanumerics_no = /^[A-Za-z0-9_/&(@):.,\- ]+$/;
		var onlynumerics = /^[0-9]+$/;
		var specials_char = /[~`!#$%\^&*+=\[\]\\';./{}()|\\":<>\?]/g;
		var emailpattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;
		
		var name_supp = $('#name_supp').val();
		var name_supp_cp = $('#name_supp_cp').val();
		var supp_phone = $('#supp_phone').val();
		var supp_email = $('#supp_email').val();
		var supp_address = $('#supp_address').val();
		
		if(name_supp == ""){
			e_error = 1;
			$('.name_supp').html('Supplier Name is Required.');
		}else{
			if(!name_supp.match(alphanumerics_spaces)){
				e_error = 1;
				$('.name_supp').html('Supplier Name not use special carecters [without _ . , -], Check again.');
			}else{
				$('.name_supp').html('');
			}	
		}
		if(name_supp_cp == ""){
			e_error = 1;
			$('.name_supp_cp').html('Contact Person is Required.');
		}else{
			if(!name_supp_cp.match(alphanumerics_spaces)){
				e_error = 1;
				$('.name_supp_cp').html('Contact Person not use special carecters [without _ . , -], Check again.');
			}else{
				$('.name_supp_cp').html('');
			}	
		}
		if(supp_phone == ""){
			e_error = 1;
			$('.supp_phone').html('Mobile No. is Required.');
		}else{
			// if(!supp_phone.match(onlynumerics)){
			// 	e_error = 1;
			// 	$('.supp_phone').html('Mobile No. needs only digit.');
			/*}else if(supp_phone.length != 10){
				e_error = 1;
				$('.supp_phone').html('Mobile No. needs only 10 digit.');*/
			// }else{
				$('.supp_phone').html('');
			// }
		}
		if(supp_email == ""){
			e_error = 1;
			$('.supp_email').html('Email ID is Required.');
		}else{
			if(!supp_email.match(emailpattern)){
				e_error = 1;
				$('.supp_email').html('Email ID not valid Format, Check again.');
			}else{
				$('.supp_email').html('');
			}	
		}
		
		if(supp_address == ""){
			e_error = 1;
			$('.supp_address').html('Address is Required.');
		}else{
			if(!supp_address.match(alphanumerics_no)){
				e_error = 1;
				$('.supp_address').html('Address not use special carecters [without _ & ( @ ) : . , -], Check again.');
			}else{
				$('.supp_address').html('');
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
			form_data.append("name_supp", name_supp);
			form_data.append("name_supp_cp", name_supp_cp);
			form_data.append("supp_phone", supp_phone);
			form_data.append("supp_email", supp_email);
			form_data.append("supp_address", supp_address);
			
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/suppliers/add_new_supplier_sets') ?>",
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
							window.location.replace("<?php echo site_url('admincontrol/suppliers/supplier_list') ?>");
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
			form_data.append("name_supid", element);
			
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/suppliers/get_details_of_suppliers') ?>",
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
						$('#update_id_sup').val(element);
						$('#update_name_supp').val(data.s_msg.sup_name);
						$('#update_name_supp_cp').val(data.s_msg.sup_contact_person);
						$('#update_supp_phone').val(data.s_msg.sup_phone);
						$('#update_supp_email').val(data.s_msg.sup_email);
						$('#update_supp_address').val(data.s_msg.sup_address);
						$('#Modal_editrecord').modal('show');
						
					}else{
						$('#update_id_sup').val('');
						$('#Modal_editrecord').modal('hide');
					}
					
				}
			});
		}else{
			$('#update_id_sup').val('');
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
		
		var update_id_sup = $('#update_id_sup').val();
		var update_name_supp = $('#update_name_supp').val();
		var update_name_supp_cp = $('#update_name_supp_cp').val();
		var update_supp_phone = $('#update_supp_phone').val();
		var update_supp_email = $('#update_supp_email').val();
		var update_supp_address = $('#update_supp_address').val();
		
		if(update_id_sup == ""){
			error_message = error_message + "<br/>ID not Found, Refresh the Page and Try again.";
		}
		
		if(update_name_supp == ""){
			e_error = 1;
			$('.update_name_supp').html('Supplier Name is Required.');
		}else{
			if(!update_name_supp.match(alphanumerics_spaces)){
				e_error = 1;
				$('.update_name_supp').html('Supplier Name not use special carecters [without _ . , -], Check again.');
			}else{
				$('.update_name_supp').html('');
			}	
		}
		if(update_name_supp_cp == ""){
			e_error = 1;
			$('.update_name_supp_cp').html('Contact Person is Required.');
		}else{
			if(!update_name_supp_cp.match(alphanumerics_spaces)){
				e_error = 1;
				$('.update_name_supp_cp').html('Contact Person not use special carecters [without _ . , -], Check again.');
			}else{
				$('.update_name_supp_cp').html('');
			}	
		}
		if(update_supp_phone == ""){
			e_error = 1;
			$('.update_supp_phone').html('Mobile No. is Required.');
		}else{
			// if(!update_supp_phone.match(onlynumerics)){
			// 	e_error = 1;
			// 	$('.update_supp_phone').html('Mobile No. needs only digit.');
			/*}else if(update_supp_phone.length != 10){
				e_error = 1;
				$('.update_supp_phone').html('Mobile No. needs only 10 digit.');*/
			// }else{
				$('.update_supp_phone').html('');
			// }
		}
		if(update_supp_email == ""){
			e_error = 1;
			$('.update_supp_email').html('Email ID is Required.');
		}else{
			if(!update_supp_email.match(emailpattern)){
				e_error = 1;
				$('.update_supp_email').html('Email ID not valid Format, Check again.');
			}else{
				$('.update_supp_email').html('');
			}	
		}
		
		if(update_supp_address == ""){
			e_error = 1;
			$('.update_supp_address').html('Address is Required.');
		}else{
			if(!update_supp_address.match(alphanumerics_no)){
				e_error = 1;
				$('.update_supp_address').html('Address not use special carecters [without _ & ( @ ) : . , -], Check again.');
			}else{
				$('.update_supp_address').html('');
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
			form_data.append("update_id_sup", update_id_sup);
			form_data.append("update_name_supp", update_name_supp);
			form_data.append("update_name_supp_cp", update_name_supp_cp);
			form_data.append("update_supp_phone", update_supp_phone);
			form_data.append("update_supp_email", update_supp_email);
			form_data.append("update_supp_address", update_supp_address);
			
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/suppliers/modify_suppliers_sets') ?>",
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
							window.location.replace("<?php echo site_url('admincontrol/suppliers/supplier_list') ?>");
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
        
