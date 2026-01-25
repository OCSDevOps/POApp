
<?php $this->load->view('admin/component/header') ?>


<?php $this->load->view('admin/component/menu') ?>

<style>
.box-body textarea,input,select {max-width: 500px;}
.box-body textarea { resize: vertical; }
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
                        <h4 class="page-title">Edit User Details</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url('admincontrol/dashboard'); ?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Edit User</li>
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
                                <h5 class="card-title">Edit User Details</h5>
								<?php if (isset($error)) { ?>
            <div class="alert alert-error alert-danger">                
                <h4>Error!</h4>
                <?php echo $error; ?>
            </div>
        	<?php } ?>
                                <?php echo form_open_multipart(base_url().'admincontrol/dashboard/edit_user/'.$data_list->u_id,'class="form-horizontal" id="myForm"'); ?>
                
                 <div class="form-group row">
				    <label class="col-sm-3 control-label text-right">First Name<font style="color: red;">*</font></label>
				    <div class="col-sm-3">
				      <input type="text" class="form-control" name="fname" id="fname" placeholder="Enter First Name" value="<?php echo $data_list->firstname; ?>" autocomplete="off" />
				      <small class="invalid-feedback fname"><?php echo form_error('fname'); ?></small>
				    </div>
				    <label class="col-sm-2 control-label text-right">Last Name<font style="color: red;">*</font></label>
				    <div class="col-sm-3">
				      <input type="text" class="form-control" name="lname" id="lname" placeholder="Enter Last Name" value="<?php echo $data_list->lastname; ?>" autocomplete="off" />
				      <small class="invalid-feedback lname"><?php echo form_error('lname'); ?></small>
				    </div>
				 </div>
                 <div class="form-group row">
				  	<label class="col-sm-3 control-label text-right">User Type<font style="color: red;">*</font></label>
				    <div class="col-sm-3">
				      <select class="form-control select2" name="u_type" id="u_type">
				      	<option value="">---Select---</option>
				      	<?php foreach($utype_list as $usertypes){ ?>
						<option value="<?php echo $usertypes->mu_id; ?>" <?php if($usertypes->mu_id == $data_list->u_type){echo "selected";} ?>><?php echo $usertypes->mu_name; ?></option>
						<?php } ?>
				      </select>
				      <small class="invalid-feedback u_type"><?php echo form_error('supplier'); ?></small>
				    </div>
					 <label class="col-sm-2 control-label text-right">Supplier</label>
					 <div class="col-sm-3">
						 <select class="form-control select2" name="supplier" id="supplier">
							 <option value="">---Select---</option>
							 <?php foreach($supplier_list as $supplier){ ?>
								 <option value="<?php echo $supplier->sup_id; ?>" <?php if($supplier->sup_id==$data_list->supplier_id){ echo "selected";}?>><?php echo $supplier->sup_name; ?></option>
							 <?php } ?>
						 </select>
						 <small class="invalid-feedback u_type"><?php echo form_error('supplier'); ?></small>
					 </div>
				    <!--<label class="col-sm-2 control-label text-right">Email Address<font style="color: red;">*</font></label>
				    <div class="col-sm-3">
				      <input type="email" class="form-control" name="emailid" id="emailid" placeholder="Enter Email" value="<?php //echo $data_list->email; ?>" autocomplete="off" />
				      <small class="invalid-feedback emailid"><?php //echo form_error('emailid'); ?></small>
				    </div>-->
				  </div>
                  <div class="form-group row">
				    <label class="col-sm-3 control-label text-right">User Name<font style="color: red;">*</font></label>
				    <div class="col-sm-3">
				      <input type="text" class="form-control" name="username" id="username" placeholder="Enter UserName" value="<?php echo $data_list->username; ?>" autocomplete="off" />
				      <small class="invalid-feedback username"><?php echo form_error('username'); ?></small>
				    </div>
				  </div>
                  <div class="form-group row">
				  	<label class="col-sm-3 control-label text-right">Email ID<font style="color: red;">*</font></label>
				    <div class="col-sm-3">
				      <input type="email" class="form-control" name="emailid" id="emailid" placeholder="Enter Email" value="<?php echo $data_list->email; ?>" autocomplete="off" />
				      <small class="invalid-feedback emailid"><?php echo form_error('emailid'); ?></small>
				    </div>
				    <label class="col-sm-2 control-label text-right">Mobile</label>
				    <div class="col-sm-3">
				      <input type="text" class="form-control" name="u_mobile" id="u_mobile" placeholder="Enter Mobile" value="<?php echo $data_list->phone; ?>" autocomplete="off" />
				      <small class="invalid-feedback u_mobile"><?php echo form_error('u_mobile'); ?></small>
				    </div>
				  </div>
                  <div class="form-group row">
				    <label class="col-sm-3 control-label text-right">Address</label>
				    <div class="col-sm-6">
				      <textarea class="form-control" name="u_address" id="u_address" placeholder="Enter Full Address"><?php echo $data_list->address; ?></textarea>
				      <small class="invalid-feedback u_address"><?php echo form_error('u_address'); ?></small>
				    </div>
				  </div>
				  <!--<div class="form-group">
				  	<label class="col-sm-3 control-label text-right">Phone/Mobile</label>
				    <div class="col-sm-3">
				      <input type="text" class="form-control" name="u_mobile" id="u_mobile" placeholder="Enter phone/mobile" value="<?php //echo $data_list->phone; ?>" autocomplete="off" />
				      <small class="invalid-feedback u_mobile"><?php //echo form_error('u_mobile'); ?></small>
				    </div>
				    <label class="col-sm-2 control-label text-right">State</label>
				    <div class="col-sm-3">
				      <input type="text" class="form-control" name="u_state" id="u_state" placeholder="Enter State" value="<?php //echo $data_list->state; ?>" autocomplete="off" />
				      <small class="invalid-feedback u_state"><?php //echo form_error('u_state'); ?></small>
				    </div>
				  </div>
				  <div class="form-group">
				    <label class="col-sm-3 control-label text-right">City</label>
				    <div class="col-sm-3">
				      <input type="text" class="form-control" name="u_city" id="u_city" placeholder="Enter City" value="<?php //echo $data_list->city; ?>" autocomplete="off" />
				      <small class="invalid-feedback u_city"><?php //echo form_error('u_city'); ?></small>
				    </div>
				    <label class="col-sm-2 control-label text-right">Pincode</label>
				    <div class="col-sm-3">
				      <input type="text" class="form-control" name="u_pincode" id="u_pincode" placeholder="Enter Pincode" value="<?php //echo $data_list->pincode; ?>" autocomplete="off" />
				      <small class="invalid-feedback u_pincode"><?php //echo form_error('u_pincode'); ?></small>
				    </div>
				  </div>-->
                  <br/><br/>
                  <div class="col-sm-12"><strong>If you want to change the password then Type New Password, Oterwise Leave password Blank</strong></div>
                  <br/><br/><br/>
                  <div class="form-group row">
				    <label class="col-sm-3 control-label text-right">Password<font style="color: red;">*</font></label>
				    <div class="col-sm-3">
				      <input type="password" class="form-control" name="password" id="password" placeholder="Enter Password" autocomplete="off" />
				      <small class="invalid-feedback password"><?php echo form_error('password'); ?></small>
				    </div>
				    <label class="col-sm-2 control-label text-right">Re-Password<font style="color: red;">*</font></label>
				    <div class="col-sm-3">
				      <input type="password" class="form-control" name="re_password" id="re_password" placeholder="Enter Password Again" autocomplete="off" />
				      <small class="invalid-feedback re_password"><?php echo form_error('re_password'); ?></small>
				    </div>
				  </div>
                  	<div class="form-group row">
						<div  class="col-sm-12 text-center">
							<div align="center">
								<div class="get_error_total" align="center" style="background-color: #bf0000;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
								<div class="get_success_total" align="center" style="background-color: #174b10;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
								<div class="div_roller_total" align="center" style="display: none;"><img src="<?php echo base_url(); ?>images/ajax_loader.gif" style="max-width: 60px;" /></div>
							</div>
						</div>
					</div>
                  <div class="form-group row">
				    <div class="col-sm-offset-3 col-sm-9">
					  <button type="button" onclick="gotoclclickbutton();" class="btn btn-primary">Submit</button>
                      &nbsp;<a href="<?= site_url('admincontrol/dashboard/administrator') ?>" class="btn btn-danger">Back</a>
				    </div>
				  </div>
                <?php form_close(); ?>
                
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- End PAge Content -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Right sidebar -->
                <!-- ============================================================== -->
                <!-- .right-sidebar -->
                <!-- ============================================================== -->
                <!-- End Right sidebar -->
                <!-- ============================================================== -->
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            


<?php $this->load->view('admin/component/footer') ?>

<script type="text/javascript">
	$(function(){
	      $('.alert-error, .invalid-feedback').delay(6000).fadeOut();
		  $(".select2").select2();
	});
	
	function check_user_district(){
		var parent_utype_val = $('#parent_utype option:selected').val();
		var parent_utype = $('#parent_utype option:selected').html();
    	var u_dist = $('#u_dist option:selected').val();
		var u_type = $('#u_type option:selected').val();
		if(parent_utype_val != "" && u_dist != "" && u_type != ""){
			
		}else{
			$('#username').val('');
		}
	}

	function check_theuser_type(){
		var u_type = $('#u_type option:selected').val();
		if(u_type != ""){
			if(u_type == 7 || u_type == 8){
				$('#parent_utype, #u_dist').val('');
				$('.usertype_choose').fadeIn();
			}else{
				$('#parent_utype, #u_dist').val('');
				$('.usertype_choose').fadeOut();
			}
		}else{
			$('#parent_utype, #u_dist').val('');
			$('.usertype_choose').fadeOut();
		}
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
			alert("1");
			$('.fname').html('First Name is Required.');
		}else{
			if(!fname.match(alphanumerics_spaces)){
				e_error = 1;
			alert("2");
				$('.fname').html('First Name not use special carecters [without _ , -], Check again.');
			}else{
				$('.fname').html('');
			}	
		}
		if(lname == ""){
			e_error = 1;
			alert("3");
			$('.lname').html('Last Name is Required.');
		}else{
			if(!lname.match(alphanumerics_spaces)){
				e_error = 1;
			alert("4");
				$('.lname').html('Last Name not use special carecters [without _ , -], Check again.');
			}else{
				$('.lname').html('');
			}	
		}
		if(u_type == ""){
			e_error = 1;
			alert("5");
			$('.u_type').html('User Type is Required.');
		}else{
			if(!u_type.match(onlynumerics)){
				e_error = 1;
			alert("6");
				$('.u_type').html('User Type only use Numeric Values, Check again.');
			}else{
				$('.u_type').html('');
			}	
		}
		if(emailid == ""){
			e_error = 1;
			alert("7");
			$('.emailid').html('Name is Required.');
		}else{
			if(!emailid.match(emailpattern)){
				e_error = 1;
			alert("8");
				$('.emailid').html('Email ID not valid Format, Check again.');
			}else{
				$('.emailid').html('');
			}	
		}

		if(username == ""){
			e_error = 1;
			alert("9");
			$('.username').html('UserName is Required.');
		}else{
			if(!username.match(alphanumerics_no)){
				e_error = 1;
			alert("10");
				$('.username').html('UserName not use special carecters [without _ / : ( @ . & ) , -], Check again.');
			}else{
				$('.username').html('');
			}	
		}
		if(password != ""){
			if(!password.match(alphanumerics_no)){
				e_error = 1;
			alert("11");
				$('.password').html('Password not use special carecters [without _ / : ( @ . & ) , -], Check again.');
			}else{
				$('.password').html('');
				if(re_password == ""){
					e_error = 1;
			alert("12");
					$('.re_password').html('Re-Password is Required.');
				}else{
					if(!re_password.match(alphanumerics_no)){
						e_error = 1;
						$('.re_password').html('Re-Password not use special carecters [without _ / : ( @ . & ) , -], Check again.');
					}else{
						$('.re_password').html('');
					}	
				}
			}	
		}
		
		if(u_address != ""){
			if(!u_address.match(alphanumerics_no)){
				e_error = 1;
			alert("13");
				$('.u_address').html('Address not use special carecters [without _ / : ( @ . & ) , -], Check again.');
			}else{
				$('.u_address').html('');
			}	
		}
		if(u_mobile == ""){
			e_error = 1;
			alert("14");
			$('.u_mobile').html('Mobile No. is Required');
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
			alert("15");
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
			alert(e_error);
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
			$("#myForm").submit();
		}

  	}
</script>
