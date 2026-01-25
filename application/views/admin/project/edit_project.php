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
                        <h4 class="page-title">Update Project</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url('admincontrol/dashboard'); ?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Update Project</li>
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
                            <?php echo form_open_multipart('','class="form-horizontal" id="myForm"'); ?>
							    <div class="card-body">
                                    <?php if (isset($error)) { ?>
									<div class="alert alert-danger alert-error">                
										<h4>Error!</h4>
										<?php echo $error; ?>
									</div>
									<?php } ?>
			
									<!--<h4 class="card-title">Personal Info</h4>-->
                                    <div class="form-group row">
                                        <label for="fname" class="col-sm-3 text-right control-label col-form-label">Project Number</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="pr_no" id="pr_no" placeholder="Enter Project Number" value="<?php echo $pdtl_list->proj_number; ?>" autocomplete="off" />
											<small class="invalid-feedback pr_no"><?php echo form_error('pr_no'); ?></small>
                                        </div>
										
                                    </div>
                                    <div class="form-group row">
                                        <label for="lname" class="col-sm-3 text-right control-label col-form-label">Project Name</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="pr_name" id="pr_name" placeholder="Enter Project Name" value="<?php echo $pdtl_list->proj_name; ?>" autocomplete="off" />
											<small class="invalid-feedback pr_name"><?php echo form_error('pr_name'); ?></small>
                                        </div>
                                    </div>
									<div class="form-group row">
                                        <label for="cono1" class="col-sm-3 text-right control-label col-form-label">Address</label>
                                        <div class="col-sm-6">
                                            <textarea class="form-control" name="pr_address" id="pr_address" placeholder="Enter Full Address" autocomplete="off"><?php echo $pdtl_list->proj_address; ?></textarea>
											<small class="invalid-feedback pr_address"><?php echo form_error('pr_address'); ?></small>
                                        </div>
                                    </div>
									<div class="form-group row">
                                        <label for="cono1" class="col-sm-3 text-right control-label col-form-label">Description</label>
                                        <div class="col-sm-6">
                                            <textarea class="form-control" name="pr_desc" id="pr_desc" placeholder="Enter Description" autocomplete="off"><?php echo $pdtl_list->proj_description; ?></textarea>
											<small class="invalid-feedback pr_desc"><?php echo form_error('pr_desc'); ?></small>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="lname" class="col-sm-3 text-right control-label col-form-label">User</label>
                                        <div class="col-sm-6">
                                            <select class="form-control select2 custom-select" name="pr_user" id="pr_user" multiple data-live-search="true" autocomplete="off">
												<?php foreach($usr_list as $users){ ?>
												<?php if(in_array($users->u_id, $pdetail_list)){ ?>
												<option value="<?php echo $users->u_id; ?>" selected><?php echo $users->firstname.' '.$users->lastname.' | '.$users->phone.' ('.$users->mu_name.')'; ?></option>
												<?php }else{ ?>
												<option value="<?php echo $users->u_id; ?>"><?php echo $users->firstname.' '.$users->lastname.' | '.$users->phone.' ('.$users->mu_name.')'; ?></option>
												<?php }} ?>
											  </select>
											  <small class="invalid-feedback pr_user"><?php echo form_error('pr_user'); ?></small>
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
                                <div class="border-top">
                                    <div class="card-body">
                                        <button type="button" onclick="gotoclclickbutton();" class="btn btn-primary">Submit</button>
										&nbsp;<a href="<?= site_url('admincontrol/projects/all_project_list') ?>" class="btn btn-danger">Cancel</a>
                                    </div>
                                </div>
                            <?php form_close(); ?>
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
	$(function(){
	      $('.alert-error, .invalid-feedback').delay(8000).fadeOut();
		  $(".select2").selectpicker();
	});
	
	function gotoclclickbutton(){
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
		
		var pr_id = '<?php echo $pdtl_list->proj_id; ?>';
    	var pr_no = $('#pr_no').val();
    	var pr_name = $('#pr_name').val();
    	var pr_address = $('#pr_address').val();
    	var pr_desc = $('#pr_desc').val();
    	var pr_user = $('#pr_user').val();
    	//var ap_quaran = $("input[name='ap_quaran']:checked").val();
		
		if(pr_id == ""){
			e_error = 1;
			error_message = error_message + "<br/>ID not Found, Refresh the Page and Try again.";
		}
		if(pr_no == ""){
			e_error = 1;
			$('.pr_no').html('Project Number is Required.');
		}else{
			if(!pr_no.match(alphanumerics_spaces)){
				e_error = 1;
				$('.pr_no').html('Project Number not use special carecters [without _ , . -], Check again.');
			}else{
				$('.pr_no').html('');
			}	
		}
		if(pr_name == ""){
			e_error = 1;
			$('.pr_name').html('Project Name is Required.');
		}else{
			if(!pr_name.match(alphanumerics_no)){
				e_error = 1;
				$('.pr_name').html('Project Name not use special carecters [without _ / : ( @ . & ) , -], Check again.');
			}else{
				$('.pr_name').html('');
			}	
		}
		if(pr_address == ""){
			e_error = 1;
			$('.pr_address').html('Project Address is Required.');
		}else{
			if(!pr_address.match(alphanumerics_no)){
				e_error = 1;
				$('.pr_address').html('Project Address not use special carecters [without _ / : ( @ . & ) , -], Check again.');
			}else{
				$('.pr_address').html('');
			}	
		}
		if(pr_desc == ""){
			e_error = 1;
			$('.pr_desc').html('Project Description is Required.');
		}else{
			if(!pr_desc.match(alphanumerics_no)){
				e_error = 1;
				$('.pr_desc').html('Project Description not use special carecters [without _ / : ( @ . & ) , -], Check again.');
			}else{
				$('.pr_desc').html('');
			}	
		}

		if(pr_user == ""){
			e_error = 1;
			$('.pr_user').html('User is Required.');
		}else{
			/*if(!pr_user.match(onlynumerics)){
				e_error = 1;
				$('.pr_user').html('User needs only Numbers.');
			}else{*/
				$('.pr_user').html('');
			//}
		}
		
		//alert(pr_user);return;
		if(e_error == 1){
			$('.div_roller_total').fadeOut();
			$('.get_error_total').html(error_message);
			$(".get_error_total").fadeIn();
			$(".invalid-feedback").fadeIn();
			/*e_error = 0;
			error_message = '';*/
			setTimeout(function(){ $('.invalid-feedback, .get_error_total').fadeOut(); }, delay);
		}else {
			
			var form_data = new FormData();
			form_data.append("pr_id", pr_id);
			form_data.append("pr_no", pr_no);
			form_data.append("pr_name", pr_name);
			form_data.append("pr_address", pr_address);
			form_data.append("pr_desc", pr_desc);
			form_data.append("pr_user", pr_user);
			
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/projects/modify_project_submission') ?>",
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
						toastr.success('Record is Updated Successfully!', 'Success');
						setTimeout(function(){ 
							window.location.replace("<?php echo site_url('admincontrol/projects/all_project_list') ?>");
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
		//$("#myForm").submit();
		

  	}
</script>
