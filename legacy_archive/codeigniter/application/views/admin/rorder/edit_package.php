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
                        <h4 class="page-title">Update Package</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url('admincontrol/dashboard'); ?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Update Package</li>
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
                                        <label for="fname" class="col-sm-3 text-right control-label col-form-label">Package Number</label>
                                        <div class="col-sm-9">
											<input type="hidden" name="ipack_itm_no" id="ipack_itm_no" value="<?php echo $at_no; ?>" autocomplete="off" />
											<input type="hidden" name="itemdtl_counter" id="itemdtl_counter" value="<?php echo $pkg_list->ipack_totalitem; ?>" autocomplete="off" />
											<input type="hidden" name="itemdtl_qty" id="itemdtl_qty" value="<?php echo $pkg_list->ipack_total_qty; ?>" autocomplete="off" />
                                            <input type="text" class="form-control" name="pkg_name" id="pkg_name" placeholder="Enter Package Name" value="<?php echo $pkg_list->ipack_name; ?>" autocomplete="off" />
											<small class="invalid-feedback pkg_name"><?php echo form_error('pkg_name'); ?></small>
                                        </div>
										
                                    </div>
                                    <div class="form-group row">
                                        <label for="cono1" class="col-sm-3 text-right control-label col-form-label">Package Details</label>
                                        <div class="col-sm-6">
                                            <textarea class="form-control" name="pkg_detail" id="pkg_detail" placeholder="Enter Package Details" autocomplete="off"><?php echo $pkg_list->ipack_details; ?></textarea>
											<small class="invalid-feedback pkg_detail"><?php echo form_error('pkg_detail'); ?></small>
                                        </div>
                                    </div>
									<div class="form-group row justify-content-md-center">
										<div class="col-sm-10">
											<div class="table-responsive">
											<table width="100%" class="table table-bordered">
												<thead>
													<tr>
														<th>Item</th>
														<th>CostCode</th>
														<th>Quantity</th>
														<th>Action</th>
													</tr>
												</thead>
												<tbody class="setall_experiences">
												<tr>
													<td>
													<select class="form-control select2 custom-select" name="pk_item" id="pk_item" data-live-search="true" autocomplete="off" onchange="goto_check_item();">
													<option value="">---Select---</option>
													<?php foreach($itm_list as $items){ ?>
													<option value="<?php echo $items->item_code; ?>"><?php echo $items->item_name; ?></option>
													<?php } ?>
													</select>
													<small class="invalid-feedback pk_item"><?php echo form_error('pk_item'); ?></small>
													</td>
													<td>
														<input type="text" class="form-control" name="pk_ccode" id="pk_ccode" placeholder="Item Cost Code" autocomplete="off" readonly />
														<small class="invalid-feedback pk_ccode"><?php echo form_error('pk_ccode'); ?></small>
													</td>
													<td>
														<input type="text" class="form-control" name="pk_itm_qnty" id="pk_itm_qnty" placeholder="Enter Quantity" autocomplete="off" />
														<small class="invalid-feedback pk_itm_qnty"><?php echo form_error('pk_itm_qnty'); ?></small>
													</td>
													<td>
														<a href="javascript:;" class="btn btn-primary" id="itemtbutton" onclick="gotosubmit_itemset();" disabled>ADD ITEM</a>
													</td>
												</tr>
												<tr>
													<td colspan="4">
														<div align="center">
															<div class="get_error_total9" align="center" style="background-color: #bf0000;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
															<div class="get_success_total9" align="center" style="background-color: #174b10;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
															<div class="div_roller_total9" align="center" style="display: none;"><img src="<?php echo base_url(); ?>style/images/ajax_loader.gif" style="max-width: 60px;" /></div>
														</div>
													</td>
												</tr>
												</tbody>
												<tbody class="expr_setvalue">
												<?php foreach($item_detailsets as $d_items){ ?>
												<tr class="expset_<?php echo $d_items->ipdetail_id; ?>">
												<td><?php echo $d_items->item_name; ?></td>
												<td><?php echo $d_items->cc_no; ?></td>
												<td><?php echo $d_items->ipdetail_quantity; ?></td>
												<td><a href="javascript:;" onclick="gotodelete_items(<?php echo $d_items->ipdetail_id; ?>);"><i class="fa fa-trash text-danger"></i></a></td>
												</tr>
												<?php } ?>
												</tbody>
											</table>
											</div>
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
										<!--&nbsp;<a href="<?= site_url('admincontrol/projects/all_project_list') ?>" class="btn btn-danger">Cancel</a>-->
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
	
	function goto_check_item(){
		var pk_item = $('#pk_item option:selected').val();
		
		if(pk_item != ""){
			var form_data = new FormData();
			form_data.append('pk_item', pk_item);
				
			$.ajax({
					method: 'POST',
					url: '<?php echo base_url() . "admincontrol/packages/get_code_from_itemfind"; ?>',
					data: form_data,
					dataType: 'JSON',
					contentType: false,
					processData: false,
					success: function(data) {
						//alert(data.msg);
						if (data.msg == 1) {
							//console.log(data);
							//alert(data.msg[0].space_rate);
							$('.div_roller_total9').fadeOut();
							$('#pk_ccode').val(data.s_msg.cc_no);

						} else {
							$('#pk_ccode').val('');
						}
					}
				});
		
		}else{
			$('#pk_ccode').val('');
		}
	}
	
	function gotosubmit_itemset() {
		$('.div_roller_total9').fadeIn();
		var delay = 5000;
		var e_error = 0;
		var error_message = 'There have some errors plese check above, Try again.';
		var onlynumerics = /^[0-9]+$/;
		var onlynumerics_withdot = /^[0-9.]+$/;
		var alphaletters = /^[A-Za-z]+$/;
		
		var pk_item = $('#pk_item option:selected').val();
		var pk_ccode = $('#pk_ccode').val();
		var pk_itm_qnty = $('#pk_itm_qnty').val();
		var ipack_itm_no = $('#ipack_itm_no').val();
		var tempo_candidate = 0;
		
		if (ipack_itm_no == "") {
			e_error = 1;
			error_message = error_message + "<br/>ID missing, Refresh the page";
		}

		if(pk_item == ""){
			e_error = 1;
			$('.pk_item').html('Item is Required.');
		}else{
			$('.pk_item').html('');
		}
		if(pk_ccode == ""){
			e_error = 1;
			$('.pk_ccode').html('Item CostCode is Required.');
		}else{
			$('.pk_ccode').html('');
		}
		if (pk_itm_qnty == "") {
			e_error = 1;
			$('.pk_itm_qnty').html('Item Quantity is Required.');
		} else {
			if (!pk_itm_qnty.match(onlynumerics)) {
				e_error = 1;
				$('.pk_itm_qnty').html('Item Quantity only use numeric values, Check again.');
			} else if (parseInt(pk_itm_qnty) <= 0) {
				e_error = 1;
				$('.pk_itm_qnty').html('Item Quantity always greater than 0, Check again.');
			} else {
				$('.pk_itm_qnty').html('');
			}
		}

		if (e_error == 1) {
			$('.div_roller_total9').fadeOut();
			$('.get_error_total9').html(error_message);
			$(".get_error_total9").fadeIn();
			$(".invalid-feedback").fadeIn();
			//$(".text-error").fadeIn();
			setTimeout(function() {
				$('.invalid-feedback, .get_error_total9').fadeOut();
			}, delay);
		} else {
			//alert("Reached");exit();
			var form_data = new FormData();
			form_data.append('ipack_itm_no', ipack_itm_no);
			form_data.append('pk_item', pk_item);
			form_data.append('pk_ccode', pk_ccode);
			form_data.append('pk_itm_qnty', pk_itm_qnty);
			//form_data.append("files", files[0]);
			$.ajax({
				method: 'POST',
				url: '<?php echo base_url() . "admincontrol/packages/new_package_item_submission"; ?>',
				data: form_data,
				dataType: 'JSON',
				contentType: false,
				processData: false,
				success: function(data) {
					//alert(data.msg);
					if (data.msg == 1) {
						//console.log(data);
						//alert(data.msg[0].space_rate);
						$('.div_roller_total9').fadeOut();
						$('.get_success_total9').html('Item is Added in the List Successfully.');
						$(".get_success_total9").fadeIn();
						var expr_string = '<tr class="expset_' + data.cat_set.ipdetail_id + '"><td>' + data.cat_set.item_name + '</td><td>' + data.cat_set.cc_no + '</td><td>' + data.cat_set.ipdetail_quantity + '</td><td><a href="javascript:;" onclick="gotodelete_items(' + data.cat_set.ipdetail_id + ');"><i class="fa fa-trash text-danger"></i></a></td></tr>';
						$('.expr_setvalue').append(expr_string);
						
						var item_counter = $('#itemdtl_counter').val();
						var item_qty = $('#itemdtl_qty').val();
						item_counter = Number(item_counter) + 1;
						item_qty = Number(item_qty) + Number(data.cat_set.ipdetail_quantity);
						$('#itemdtl_counter').val(item_counter);
						$('#itemdtl_qty').val(item_qty);
						$('#pk_ccode, #pk_itm_qnty').val('');
						setTimeout(function() {
							$('.get_success_total9').fadeOut();
						}, 3000);

					} else {
						$('.div_roller_total9').fadeOut();
						//error_message = "There have some problem to Store Data, Try after some time.";
						error_message = data.e_msg;
						$('.get_error_total9').html(error_message);
						$(".get_error_total9").fadeIn();
						setTimeout(function() {
							$('.get_error_total9').fadeOut();
						}, delay);
					}
				}
			});
		}

		
	}
	
	function gotodelete_items(exid) {
		if (exid != "") {
			var conf_answer = confirm("You are about to Delete a record. Are you sure?")
			if (conf_answer) {
				$('.div_roller_total9').fadeIn();
				$.ajax({
					method: 'POST',
					url: '<?php echo base_url() . "admincontrol/packages/delete_itemset_update"; ?>',
					data: {
						qid: exid
					},
					dataType: 'JSON',
					success: function(data) {
						//alert(data.msg);
						if (data.msg == 1) {
							//console.log(data);
							//alert(data.msg[0].option_set);
							$('.div_roller_total9').fadeOut();
							$('.get_success_total9').html('Item is Deleted Successfully.');
							$(".get_success_total9").fadeIn();
							
							var item_counter = $('#itemdtl_counter').val();
							item_counter = Number(item_counter) - 1;
							$('#itemdtl_counter').val(item_counter);
							var item_qty = $('#itemdtl_qty').val();
							item_qty = Number(item_qty) - Number(data.expmarks.ipdetail_quantity);
							$('#itemdtl_qty').val(item_qty);
							
							$(".expset_" + exid).remove();
							setTimeout(function() {
								$('.get_success_total9').fadeOut();
							}, 3000);
						} else {
							$('.div_roller_total9').fadeOut();
							error_message = "There have some problem to Update Data, Try again.";
							error_message = error_message + "<br/>" + data.e_msg;
							$('.get_error_total9').html(error_message);
							$(".get_error_total9").fadeIn();
							setTimeout(function() {
								$('.get_error_total9').fadeOut();
							}, 3000);
						}

					}
				});
			}
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
    	var pkg_id = '<?php echo $pkg_list->ipack_id; ?>';
    	//var ap_quaran = $("input[name='ap_quaran']:checked").val();
		
		if (ipack_itm_no == "" || pkg_id == "") {
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
			form_data.append("pkg_id", pkg_id);
			form_data.append("ipack_itm_no", ipack_itm_no);
			form_data.append("itemdtl_counter", itemdtl_counter);
			form_data.append("itemdtl_qty", itemdtl_qty);
			form_data.append("pkg_name", pkg_name);
			form_data.append("pkg_detail", pkg_detail);
			
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/packages/modify_package_submission') ?>",
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
							window.location.replace("<?php echo site_url('admincontrol/packages/all_package_list') ?>");
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
