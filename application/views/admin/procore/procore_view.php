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
                        <h4 class="page-title">Company Information</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url('admincontrol/dashboard'); ?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Company Information</li>
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
                                        <label for="fname" class="col-sm-3 text-left control-label col-form-label">Company Name:</label>
                                        <div class="col-sm-6">
											<input type="text" class="form-control" name="company_name" id="company_name" autocomplete="off" />
											<small class="invalid-feedback company_name"><?php echo form_error('company_name'); ?></small>
                                        </div>
									</div>
									<div class="form-group row">
                                        <label for="fname" class="col-sm-3 text-left control-label col-form-label">Company Address:</label>
                                        <div class="col-sm-6">
											<textarea class="form-control" placeholder="Enter Address" id="company_address" name="company_address" autocomplete="off"></textarea>
										<small class="invalid-feedback company_address"><?php //echo form_error('update_supp_address'); ?></small>
                                        </div>
									</div>
									
									
									
                                </div>
                                <div class="border-top">
                                    <div class="card-body">
                                        <button type="button" onclick="gotoclclickbutton();" class="btn btn-primary">Submit</button>
										
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
		
    	var ipack_itm_no = $('#ipack_itm_no').val();
    	var itemdtl_counter = $('#itemdtl_counter').val();
    	var itemdtl_qty = $('#itemdtl_qty').val();
    	var pkg_name = $('#pkg_name').val();
    	var pkg_detail = $('#pkg_detail').val();
    	//var ap_quaran = $("input[name='ap_quaran']:checked").val();
		
		if (ipack_itm_no == "") {
			e_error = 1;
			error_message = error_message + "<br/>ID missing, Refresh the page";
		}
		if (itemdtl_counter == "" || itemdtl_counter <= 0 || itemdtl_qty == "" || itemdtl_qty <= 0) {
			e_error = 1;
			error_message = error_message + "<br/>Item not found in the Package, Add some Item.";
		}
		
		if(pkg_name == ""){
			e_error = 1;
			$('.pkg_name').html('Project Name is Required.');
		}else{
			if(!pkg_name.match(alphanumerics_no)){
				e_error = 1;
				$('.pkg_name').html('Project Name not use special carecters [without _ / : ( @ . & ) , -], Check again.');
			}else{
				$('.pkg_name').html('');
			}	
		}
		if(pkg_detail != ""){
			if(!pkg_detail.match(alphanumerics_no)){
				e_error = 1;
				$('.pkg_detail').html('Project Address not use special carecters [without _ / : ( @ . & ) , -], Check again.');
			}else{
				$('.pkg_detail').html('');
			}	
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
			form_data.append("ipack_itm_no", ipack_itm_no);
			form_data.append("itemdtl_counter", itemdtl_counter);
			form_data.append("itemdtl_qty", itemdtl_qty);
			form_data.append("pkg_name", pkg_name);
			form_data.append("pkg_detail", pkg_detail);
			
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/porder/new_p_order_Set_submission') ?>",
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
							window.location.replace("<?php echo site_url('admincontrol/porder/all_package_list') ?>");
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
