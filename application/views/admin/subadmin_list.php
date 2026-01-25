<?php $this->load->view('admin/component/header') ?>


<?php $this->load->view('admin/component/menu') ?>


		<!-- Page wrapper  -->
        <!-- ============================================================== -->
	<div class="page-wrapper">

	<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-12 d-flex no-block align-items-center">
                        <h4 class="page-title">User List</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url('admincontrol/dashboard'); ?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">User List</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

	<div class="container-fluid">

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
                                if($this->session->userdata('utype')==1 || $templateDetails->pt_a_user<3){?>
                                    <a href="#" onclick="goto_add_record()" class="btn btn-primary float-right mb-2" style="margin-right: 10px;">Add New User</a>
						        <?php }?>
                                <div class="table-responsive">
                                    <table id="zero_config" class="table table-striped table-bordered">
                                        <thead>
                                            <tr style="font-weight: bold;">
                                                <th>Name</th>
												<th>Mail-ID</th>
												<th>User Name</th>
												<th>User Type</th>
												<th>Access IP</th>
												<th>Status</th>
                                                <?php 
                                                if($this->session->userdata('utype')==1 || $templateDetails->pt_a_user<3){?>
												    <th>Action</th>
						                        <?php }?>
                                            </tr>
                                        </thead>
                                        <tbody>
										<?php foreach($userlist as $users)
										{ ?>
										<tr>
											<td><?php if($users->procore_integration_status=='YES'){echo $users->firstname.' '.$users->lastname.'<br><span style="background-color:#E64900;color:#fff;font-size:10px;font-weight:bold;padding:5px">PROCORE</span>';}else{echo $users->firstname.' '.$users->lastname;} ?></td>
											<td><?php echo $users->email; ?></td>
											<td><?php echo $users->username; ?></td>
											<td><?php echo $users->parent_type; ?></td>
											<!--<td><?php //echo date('d-m-Y h:i A',strtotime($users->modify_date)); ?></td>-->
											<td><?php echo $users->access_ip; ?></td>
											<td><?php if($users->status == 1){ ?>
												  <span style="color:green;">Active</span>
											  <?php }elseif($users->status == 0){ ?>
												<span style="color:red;">InActive</span>
											  <?php } ?></td>
                                            <?php 
                                            if($this->session->userdata('utype')==1 || $templateDetails->pt_a_user<3){?>
                                                <td>
                                                    <a href="javascript:;" onclick="modify_record(<?php echo $users->u_id; ?>)" title="Edit User"><i class="fa fa-edit text-primary"></i></a>
                                                    <?php if($users->status == 1){ ?>	
                                                    <a href="<?php echo base_url().'admincontrol/dashboard/lock_user/'.$users->u_id; ?>" title="Lock User"><i class="fa fa-unlock text-dark"></i></a>
                                                    <?php } else { ?>
                                                    <a href="<?php echo base_url().'admincontrol/dashboard/unlock_user/'.$users->u_id; ?>" title="Unock User"><i class="fa fa-lock text-dark"></i></a>
                                                    <?php } ?>
                                                    <?php 
                                                    if($users->procore_integration_status!='YES' && ($this->session->userdata('utype')==1 || $templateDetails->pt_a_user<2)){?>
                                                        <a onclick="return confirm('You are about to delete a record. This cannot be undone. Are you sure?');" href="<?php echo base_url().'admincontrol/dashboard/delete_user/'.$users->u_id; ?>" title="Delete User"><i class="fa fa-trash text-danger"></i></a>
										            <?php } ?>
                                                    
                                                </td>
										    <?php } ?>
										</tr>	
										<?php } ?>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>

	<div class="modal fade" id="Modal_addrecord" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<form class="form-horizontal" id="myForm-add" method="post" action="<?=base_url('admincontrol/dashboard/administrator');?>">
				<div class="modal-header">
					<h5 class="modal-title">Add New User</h5>
					<button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<?php 
					// echo form_open(base_url('admincontrol/dashboard/administrator'),'class="form-horizontal" id="myForm"'); 
					?>
					<div class="form-group row">
						<label for="fname" class="col-sm-3 text-right control-label col-form-label">First Name</label>
						<div class="col-sm-9">
							<input type="text" class="form-control reset-input" name="fname" id="fname" placeholder="Enter First Name" value="<?php echo set_value('fname'); ?>" autocomplete="off" />
							<small class="invalid-feedback fname"><?php echo form_error('fname'); ?></small>
						</div>

					</div>
					<div class="form-group row">
						<label for="lname" class="col-sm-3 text-right control-label col-form-label">Last Name</label>
						<div class="col-sm-9">
							<input type="text" class="form-control reset-input" name="lname" id="lname" placeholder="Enter Last Name" value="<?php echo set_value('lname'); ?>" autocomplete="off" />
							<small class="invalid-feedback lname"><?php echo form_error('lname'); ?></small>
						</div>
					</div>
					<div class="form-group row">
						<label for="lname" class="col-sm-3 text-right control-label col-form-label">User Type</label>
						<div class="col-sm-9">
							<select class="form-control select2 custom-select reset-input" name="u_type" id="u_type">
								<option value="">---Select---</option>
								<?php foreach($utype_list as $usertypes){ ?>
									<option value="<?php echo $usertypes->mu_id; ?>"><?php echo $usertypes->mu_name; ?></option>
								<?php } ?>
							</select>
							<small class="invalid-feedback u_type"><?php echo form_error('u_type'); ?></small>
						</div>
					</div>
					<div class="form-group row ">
						<label for="lname" style="display:none" class="col-sm-3 user-supplier text-right control-label col-form-label">Supplier</label>
						<div class="col-sm-9 user-supplier" style="display:none">
							<select class="form-control select2 custom-select reset-input" name="supplier" id="supplier">
								<option value="">---Select---</option>
								<?php foreach($supplier_list as $supplier){ ?>
									<option value="<?php echo $supplier->sup_id; ?>"><?php echo $supplier->sup_name; ?></option>
								<?php } ?>
							</select>
							<small class="invalid-feedback u_type"><?php echo form_error('u_type'); ?></small>
						</div>
					</div>

					<div class="form-group row">
						<label for="lname" class="col-sm-3 text-right control-label col-form-label">Username</label>
						<div class="col-sm-9">
							<input type="text" class="form-control reset-input" name="username" id="username" placeholder="Enter UserName" value="<?php echo set_value('username'); ?>" autocomplete="off" />
							<small class="invalid-feedback username"><?php echo form_error('username'); ?></small>
						</div>
					</div>
					<div class="form-group row">
						<label for="lname" class="col-sm-3 text-right control-label col-form-label">Password</label>
						<div class="col-sm-9">
							<input type="password" class="form-control reset-input" name="password" id="password" placeholder="Enter Password" autocomplete="off" />
							<small class="invalid-feedback password"><?php echo form_error('password'); ?></small>
						</div>
					</div>
					<div class="form-group row">
						<label for="lname" class="col-sm-3 text-right control-label col-form-label">Re-Password</label>
						<div class="col-sm-9">
							<input type="password" class="form-control reset-input" name="re_password" id="re_password" placeholder="Enter Password Again" autocomplete="off" />
							<small class="invalid-feedback re_password"><?php echo form_error('re_password'); ?></small>
						</div>
					</div>
					<div class="form-group row">
						<label for="email1" class="col-sm-3 text-right control-label col-form-label">E-Mail</label>
						<div class="col-sm-9">
							<input type="email" class="form-control reset-input" name="emailid" id="emailid" placeholder="Enter Email" value="<?php echo set_value('emailid'); ?>" autocomplete="off" />
							<small class="invalid-feedback emailid"><?php echo form_error('emailid'); ?></small>
						</div>
					</div>
					<div class="form-group row">
						<label for="cono1" class="col-sm-3 text-right control-label col-form-label">Mobile No</label>
						<div class="col-sm-9">
							<input type="text" class="form-control reset-input" name="u_mobile" id="u_mobile" placeholder="Enter Mobile" value="<?php echo set_value('u_mobile'); ?>" autocomplete="off" />
							<small class="invalid-feedback u_mobile"><?php echo form_error('u_mobile'); ?></small>
						</div>
					</div>
					<div class="form-group row">
						<label for="cono1" class="col-sm-3 text-right control-label col-form-label">Address</label>
						<div class="col-sm-9">
							<textarea class="form-control reset-input" name="u_address" id="u_address" placeholder="Enter Full Address"><?php echo set_value('u_address'); ?></textarea>
							<small class="invalid-feedback u_address"><?php echo form_error('u_address'); ?></small>
						</div>
					</div>
					<div class="form-group row">
						<div  class="col-sm-12 text-center">
							<div align="center">
								<div class="get_error_total" align="center" style="background-color: #bf0000;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
								<div class="get_success_total" align="center" style="background-color: #174b10;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
								<div class="div_roller_total" align="center" style="display: none;"><img src="<?php echo base_url(); ?>style/images/ajax_loader.gif" style="max-width: 60px;" /></div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>-->
					<button type="button" id="submit_record_btn" class="btn btn-primary" onclick="gotoclclickbutton();">Submit</button>
				</div>
			</div>
			</form>
		</div>
	</div>

		<div class="modal fade" id="Modal_editrecord" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Update User</h5>
					<button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<?php echo form_open_multipart(base_url().'admincontrol/dashboard/update_user','class="form-horizontal" id="myForm"'); ?>
					<input type="hidden" name="update_id_user" id="update_id_user">
					<div class="form-group row">
						<label for="fname" class="col-sm-3 text-right control-label col-form-label">First Name</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" name="fname" id="update_fname" placeholder="Enter First Name" value="<?php echo set_value('fname'); ?>" autocomplete="off" />
							<small class="invalid-feedback fname"><?php echo form_error('fname'); ?></small>
						</div>

					</div>
					<div class="form-group row">
						<label for="lname" class="col-sm-3 text-right control-label col-form-label">Last Name</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" name="lname" id="update_lname" placeholder="Enter Last Name" value="<?php echo set_value('lname'); ?>" autocomplete="off" />
							<small class="invalid-feedback lname"><?php echo form_error('lname'); ?></small>
						</div>
					</div>
					<div class="form-group row">
						<label for="lname" class="col-sm-3 text-right control-label col-form-label">User Type</label>
						<div class="col-sm-9">
							<select class="form-control select2 custom-select" name="u_type" id="update_u_type">
								<option value="">---Select---</option>
								<?php foreach($utype_list as $usertypes){ ?>
									<option value="<?php echo $usertypes->mu_id; ?>"><?php echo $usertypes->mu_name; ?></option>
								<?php } ?>
							</select>
							<small class="invalid-feedback u_type"><?php echo form_error('u_type'); ?></small>
						</div>
					</div>
					<div class="form-group row">
						<label for="lname" style="display:none" class="col-sm-3 update-user-supplier text-right control-label col-form-label">Supplier</label>
						<div class="col-sm-9 update-user-supplier" style="display:none">
							<select class="form-control select2 custom-select" name="supplier" id="update_supplier">
								<option value="">---Select---</option>
								<?php foreach($supplier_list as $supplier){ ?>
									<option value="<?php echo $supplier->sup_id; ?>"><?php echo $supplier->sup_name; ?></option>
								<?php } ?>
							</select>
							<small class="invalid-feedback u_type"><?php echo form_error('u_type'); ?></small>
						</div>
					</div>
					<div class="form-group row">
						<label for="lname" class="col-sm-3 text-right control-label col-form-label">Username</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" name="username" id="update_username" placeholder="Enter UserName" value="<?php echo set_value('username'); ?>" autocomplete="off" />
							<small class="invalid-feedback username"><?php echo form_error('username'); ?></small>
						</div>
					</div>
					<div class="form-group row">
						<label for="lname" class="col-sm-3 text-right control-label col-form-label">Password</label>
						<div class="col-sm-9">
							<input type="password" class="form-control" name="password" id="update_password" placeholder="Enter Password" autocomplete="off" />
							<small class="invalid-feedback password"><?php echo form_error('password'); ?></small>
						</div>
					</div>
					<div class="form-group row">
						<label for="lname" class="col-sm-3 text-right control-label col-form-label">Re-Password</label>
						<div class="col-sm-9">
							<input type="password" class="form-control" name="re_password" id="update_re_password" placeholder="Enter Password Again" autocomplete="off" />
							<small class="invalid-feedback re_password"><?php echo form_error('re_password'); ?></small>
						</div>
					</div>
					<div class="form-group row">
						<label for="email1" class="col-sm-3 text-right control-label col-form-label">E-Mail</label>
						<div class="col-sm-9">
							<input type="email" class="form-control" name="emailid" id="update_emailid" placeholder="Enter Email" value="<?php echo set_value('emailid'); ?>" autocomplete="off" />
							<small class="invalid-feedback emailid"><?php echo form_error('emailid'); ?></small>
						</div>
					</div>
					<div class="form-group row">
						<label for="cono1" class="col-sm-3 text-right control-label col-form-label">Mobile No</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" name="u_mobile" id="update_u_mobile" placeholder="Enter Mobile" value="<?php echo set_value('u_mobile'); ?>" autocomplete="off" />
							<small class="invalid-feedback u_mobile"><?php echo form_error('u_mobile'); ?></small>
						</div>
					</div>
					<div class="form-group row">
						<label for="cono1" class="col-sm-3 text-right control-label col-form-label">Address</label>
						<div class="col-sm-9">
							<textarea class="form-control" name="u_address" id="update_u_address" placeholder="Enter Full Address"><?php echo set_value('u_address'); ?></textarea>
							<small class="invalid-feedback u_address"><?php echo form_error('u_address'); ?></small>
						</div>
					</div>
					<div class="form-group row">
						<div  class="col-sm-12 text-center">
							<div align="center">
								<div class="get_error_total" align="center" style="background-color: #bf0000;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
								<div class="get_success_total" align="center" style="background-color: #174b10;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
								<div class="div_roller_total" align="center" style="display: none;"><img src="<?php echo base_url(); ?>style/images/ajax_loader.gif" style="max-width: 60px;" /></div>
							</div>
						</div>
					</div>
						<?php form_close(); ?>
				</div>
				<div class="modal-footer">
					<!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>-->
					<button type="button" id="submit_record_btn" class="btn btn-primary" onclick="updateUser();">Submit</button>
				</div>
			</div>
		</div>
	</div>


<?php $this->load->view('admin/component/footer') ?>



<script type="text/javascript">
	$(function(){
	      $('#alert_msg').delay(6000).fadeOut();
	});

	$('#zero_config').DataTable();

	function goto_add_record(){
		$('.reset-input').val('').change();
		$('#Modal_addrecord').modal('show');
	}

	function gotoclclickbutton(){
		$('.div_roller_total').fadeIn();
		var delay = 8000;
		var e_error = 0;
		var error_message = 'There have some errors plese check above, Try again.';
		var alphaletters_spaces = /^[A-Za-z ]+$/;
		var alphaletters = /^[A-Za-z]+$/;
		var alphanumerics = /^[A-Za-z0-9/() ]+$/;
		var alphanumerics_spaces = /^[A-Za-z0-9_,\- ]+$/;
		var alphanumerics_no = /^[A-Za-z0-9_/&(@):.,\- ]+$/;
		var onlynumerics = /^[0-9]+$/;
		var specials_char = /[~`!#$%\^&*+=\[\]\\';./{}()|\\":<>\?]/g;
		var emailpattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;

		var fname = $('#fname').val();
		var lname = $('#lname').val();
		var u_type = $('#u_type option:selected').val();
		//var parent_utype = $('#parent_utype option:selected').val();
		//var u_dist = $('#u_dist option:selected').val();
		var emailid = $('#emailid').val();
		var username = $('#username').val();
		var password = $('#password').val();
		var re_password = $('#re_password').val();
		var u_address = $('#u_address').val();
		var u_mobile = $('#u_mobile').val();
		/*var u_state = $('#u_state').val();
		var u_city = $('#u_city').val();
		var u_pincode = $('#u_pincode').val();*/

		//var ap_symptom = $("input[name='ap_symptom']:checked").val();
		//var ap_quaran = $("input[name='ap_quaran']:checked").val();

		if(fname == ""){
			e_error = 1;
			$('.fname').html('First Name is Required.');
		}else{
			if(!fname.match(alphanumerics_spaces)){
				e_error = 1;
				$('.fname').html('First Name not use special carecters [without _ , -], Check again.');
			}else{
				$('.fname').html('');
			}
		}
		if(lname == ""){
			e_error = 1;
			$('.lname').html('Last Name is Required.');
		}else{
			if(!lname.match(alphanumerics_spaces)){
				e_error = 1;
				$('.lname').html('Last Name not use special carecters [without _ , -], Check again.');
			}else{
				$('.lname').html('');
			}
		}
		if(u_type == ""){
			e_error = 1;
			$('.u_type').html('User Type is Required.');
		}else{
			if(!u_type.match(onlynumerics)){
				e_error = 1;
				$('.u_type').html('User Type only use Numeric Values, Check again.');
			}else{
				$('.u_type').html('');
			}
		}
		if(emailid == ""){
			e_error = 1;
			$('.emailid').html('Email ID is Required.');
		}else{
			if(!emailid.match(emailpattern)){
				e_error = 1;
				$('.emailid').html('Email ID not valid Format, Check again.');
			}else{
				$('.emailid').html('');
			}
		}

		if(username == ""){
			e_error = 1;
			$('.username').html('UserName is Required.');
		}else{
			if(!username.match(alphanumerics_no)){
				e_error = 1;
				$('.username').html('UserName not use special carecters [without _ / : ( @ . & ) , -], Check again.');
			}else{
				$('.username').html('');
			}
		}
		if(password == ""){
			e_error = 1;
			$('.password').html('Password is Required.');
		}else{
			if(!password.match(alphanumerics_no)){
				e_error = 1;
				$('.password').html('Password not use special carecters [without _ / : ( @ . & ) , -], Check again.');
			}else{
				$('.password').html('');
			}
		}
		if(re_password == ""){
			e_error = 1;
			$('.re_password').html('Re-Password is Required.');
		}else{
			if(!re_password.match(alphanumerics_no)){
				e_error = 1;
				$('.re_password').html('Re-Password not use special carecters [without _ / : ( @ . & ) , -], Check again.');
			}else{
				$('.re_password').html('');
			}
		}
		// if(u_address != ""){
		// 	if(!u_address.match(alphanumerics_no)){
		// 		e_error = 1;
		// 		$('.u_address').html('Address not use special carecters [without _ / : ( @ . & ) , -], Check again.');
		// 	}else{
		// 		$('.u_address').html('');
		// 	}
		// }
		if(u_mobile == ""){
			e_error = 1;
			$('.u_mobile').html('Mobile No. is Required.');
		}else{
			// if(!u_mobile.match(onlynumerics)){
			// 	e_error = 1;
			// 	$('.u_mobile').html('Mobile No. needs only Numbers.');
				/*}else if(u_mobile.length != 10){
					e_error = 1;
					$('.u_mobile').html('Mobile No. needs only 10 digit.');*/
			// }else{
				$('.u_mobile').html('');
			// }
		}
		/*if(u_state != ""){
			if(!u_state.match(alphanumerics_no)){
				e_error = 1;
				$('.u_state').html('State not use special carecters [without _ / : ( @ . & ) , -], Check again.');
			}else{
				$('.u_state').html('');
			}
		}
		if(u_city != ""){
			if(!u_city.match(alphanumerics_no)){
				e_error = 1;
				$('.u_city').html('City not use special carecters [without _ / : ( @ . & ) , -], Check again.');
			}else{
				$('.u_city').html('');
			}
		}
		if(u_pincode != ""){
			if(!u_pincode.match(onlynumerics)){
				e_error = 1;
				$('.u_pincode').html('Pincode only use Numeric Values, Check again.');
			}else{
				$('.u_pincode').html('');
			}
		}*/

		if(password != "" && re_password != ""){
			if(password != re_password){
				e_error = 1;
				error_message = error_message + '<br/>Password and Re-Password Not Matched, Check again.';
			}
		}

		/*if(document.getElementById("userworkorder").files.length == 0){
			e_error = 1;
			$('.userworkorder').html('Work-Order File is Required.');
		}else{
			var fileInput = document.getElementById('userworkorder');
			var filePath = fileInput.value;
			if(!allowedExtensions.exec(filePath)){
				e_error = 1;
				$('.userworkorder').html('Work-Order File type Invalid.(Use PDF/JPG)');
			}else{
				$('.userworkorder').html('');
			}

		}
		if(document.getElementById("userworker").files.length == 0){
			e_error = 1;
			$('.userworker').html('Worker Details File is Required.');
		}else{
			var fileInput = document.getElementById('userworker');
			var filePath = fileInput.value;
			if(!allowedExtensions.exec(filePath)){
				e_error = 1;
				$('.userworker').html('Worker Details File type Invalid.(Use PDF/JPG)');
			}else{
				$('.userworker').html('');
			}
		}*/

		//alert(salts);
		if(e_error == 1){
			$('.div_roller_total').fadeOut();
			$('.get_error_total').html(error_message);
			$(".get_error_total").fadeIn();
			$(".invalid-feedback").fadeIn();
			/*e_error = 0;
			error_message = '';*/
			setTimeout(function(){ $('.invalid-feedback, .get_error_total').fadeOut(); }, delay);
		}else{
			//alert(newhash);
			//alert(rehash);
			// $("#myForm-add").submit();
			var form_data = new FormData();
			form_data.append("fname", fname);
			form_data.append("lname", lname);
			form_data.append("u_type", u_type);
			form_data.append("emailid", emailid);
			form_data.append("username", username);
			form_data.append("password", password);
			form_data.append("re_password", re_password);
			form_data.append("u_address", u_address);
			form_data.append("u_mobile", u_mobile);
			form_data.append("u_mobile", u_mobile);
			form_data.append("user_id", update_id_user);
			form_data.append("supplier", update_supplier);


			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/dashboard/administrator') ?>",
				dataType: 'json',
				data: form_data,
				contentType: false,
				cache: true,
				processData: false,
				success: function (data) {
					if (data.msg == 1) {
						console.log(data);
						//alert(data.msg[0].space_rate);
						$('.div_roller_total').fadeOut();
						toastr.success('Record is Updated Successfully!', 'Success');
						setTimeout(function () {
							window.location.replace("<?php echo site_url('admincontrol/dashboard/administrator') ?>");
						}, 2000);

					} else {
						$('.div_roller_total').fadeOut();
						$('#submit_record_btn').prop('disabled', false);
						$('.close_modal').show();
						error_message = data.e_msg;
						toastr.error(error_message, 'Error!');
						$('.get_error_total').html(error_message);
						$(".get_error_total").fadeIn();
						setTimeout(function(){ $('.get_error_total').fadeOut(); }, delay);
					}

				},
				error: function (error) {
					console.log(error);
				},
				//complete: function (error) {
				//	toastr.success('Record is Inserted Successfully!', 'Success');
				//	setTimeout(function () {
				//		window.location.replace("<?php //echo site_url('admincontrol/porder/all_purchase_order_list') ?>//");
				//	}, 2000);
				//}
			});
		}

	}

	function modify_record(element){
		//alert(element);
		if(element != ""){
			var form_data = new FormData();
			form_data.append("user_id", element);

			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/dashboard/edit_user') ?>",
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
						// $('#update_supplier').removeAttr('selected');

						$('#update_id_user').val(element);
						$('#update_fname').val(data.data_list.firstname);
						$('#update_lname').val(data.data_list.lastname);
						$('#update_username').val(data.data_list.username);
						$('#update_emailid').val(data.data_list.email);
						$('#update_u_mobile').val(data.data_list.phone);
						$('#update_u_address').val(data.data_list.address);

						$('#update_u_type').find('option[value="'+data.data_list.u_type+'"]').attr("selected",true).trigger('change');
						$('#update_supplier').val(data.data_list.supplier_id).change();
						var uType=$('#update_u_type').val();
						if(uType==4){
							$('.update-user-supplier').css('display','block');
						}else{
							$('.update-user-supplier').css('display','none');
						}

						$('.select2').selectpicker('refresh');
						$('#Modal_editrecord').modal('show');

					}else{
						$('#update_id_user').val('');
						$('#Modal_editrecord').modal('hide');
					}

				}
			});
		}else{
			$('#update_id_user').val('');
			$('#Modal_editrecord').modal('hide');
		}
	}

	function updateUser()
	{
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
		var onlynumerics_withdot = /^[0-9.]+$/;
		var fname = $('#update_fname').val();
		var lname = $('#update_lname').val();
		var u_type = $('#update_u_type option:selected').val();
		var emailid = $('#update_emailid').val();
		var username = $('#update_username').val();
		var password = $('#update_password').val();
		var re_password = $('#update_re_password').val();
		var u_address = $('#update_u_address').val();
		var u_mobile = $('#update_u_mobile').val();
		var update_id_user = $('#update_id_user').val();
		var update_supplier = $('#update_supplier').val();
//var ap_quaran = $("input[name='ap_quaran']:checked").val();

		if(fname == ""){
			e_error = 1;
			$('.update_fname').html('First Name is Required.');
		}else{
			if(!fname.match(alphanumerics_spaces)){
				e_error = 1;
				$('.update_fname').html('First Name not use special carecters [without _ , -], Check again.');
			}else{
				$('.update_fname').html('');
			}
		}
		if(lname == ""){
			e_error = 1;
			$('.update_lname').html('Last Name is Required.');
		}else{
			if(!lname.match(alphanumerics_spaces)){
				e_error = 1;
				$('.update_lname').html('Last Name not use special carecters [without _ , -], Check again.');
			}else{
				$('.update_lname').html('');
			}
		}
		if(u_type == ""){
			e_error = 1;
			$('.update_u_type').html('User Type is Required.');
		}else{
			if(!u_type.match(onlynumerics)){
				e_error = 1;
				$('.update_u_type').html('User Type only use Numeric Values, Check again.');
			}else{
				$('.update_u_type').html('');
			}
		}
		if(emailid == ""){
			e_error = 1;
			$('.update_emailid').html('Email ID is Required.');
		}else{
			if(!emailid.match(emailpattern)){
				e_error = 1;
				$('.update_emailid').html('Email ID not valid Format, Check again.');
			}else{
				$('.update_emailid').html('');
			}
		}

		if(username == ""){
			e_error = 1;
			$('.update_username').html('UserName is Required.');
		}else{
			if(!username.match(alphanumerics_no)){
				e_error = 1;
				$('.update_username').html('UserName not use special carecters [without _ / : ( @ . & ) , -], Check again.');
			}else{
				$('.update_username').html('');
			}
		}

		// if(u_address != ""){
		// 	if(!u_address.match(alphanumerics_no)){
		// 		e_error = 1;
		// 		$('.update_u_address').html('Address not use special carecters [without _ / : ( @ . & ) , -], Check again.');
		// 	}else{
		// 		$('.update_u_address').html('');
		// 	}
		// }
		if(u_mobile == ""){
			e_error = 1;
			$('.update_u_mobile').html('Mobile No. is Required.');
		}else{
			// if(!u_mobile.match(onlynumerics)){
			// 	e_error = 1;
			// 	$('.update_u_mobile').html('Mobile No. needs only Numbers.');
				/*}else if(u_mobile.length != 10){
					e_error = 1;
					$('.u_mobile').html('Mobile No. needs only 10 digit.');*/
			// }else{
				$('.update_u_mobile').html('');
			// }
		}
		if(password != "" && re_password != ""){
			if(password != re_password){
				e_error = 1;
				error_message = error_message + '<br/>Password and Re-Password Not Matched, Check again.';
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
			form_data.append("fname", fname);
			form_data.append("lname", lname);
			form_data.append("u_type", u_type);
			form_data.append("emailid", emailid);
			form_data.append("username", username);
			form_data.append("password", password);
			form_data.append("re_password", re_password);
			form_data.append("u_address", u_address);
			form_data.append("u_mobile", u_mobile);
			form_data.append("u_mobile", u_mobile);
			form_data.append("user_id", update_id_user);
			form_data.append("supplier", update_supplier);


			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/dashboard/update_user') ?>",
				dataType: 'json',
				data: form_data,
				contentType: false,
				cache: true,
				processData: false,
				success: function (data) {
					if (data.msg == 1) {
						console.log(data);
						//alert(data.msg[0].space_rate);
						$('.div_roller_total').fadeOut();
						toastr.success('Record is Updated Successfully!', 'Success');
						setTimeout(function () {
							window.location.replace("<?php echo site_url('admincontrol/dashboard/administrator') ?>");
						}, 2000);

					} else {
						$('.div_roller_total').fadeOut();
						$('#submit_record_btn').prop('disabled', false);
						$('.close_modal').show();
						error_message = data.e_msg;
						toastr.error(error_message, 'Error!');
						$('.get_error_total').html(error_message);
						$(".get_error_total").fadeIn();
						setTimeout(function(){ $('.get_error_total').fadeOut(); }, delay);
					}

				},
				error: function (error) {
					console.log(error);
				},
				//complete: function (error) {
				//	toastr.success('Record is Inserted Successfully!', 'Success');
				//	setTimeout(function () {
				//		window.location.replace("<?php //echo site_url('admincontrol/porder/all_purchase_order_list') ?>//");
				//	}, 2000);
				//}
			});
		}

	}

	$('#u_type').change(function(){
		var value=$(this).val();
		if(value==4){
			$('.user-supplier').css('display','block');
		}else{
			$('.user-supplier').css('display','none');
		}
	});

	$('#update_u_type').change(function(){
		var value=$(this).val();
		if(value==4){
			$('.update-user-supplier').css('display','block');
		}else{
			$('.update-user-supplier').css('display','none');
		}
	});

</script>
        
