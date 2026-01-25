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
                                        <label for="fname" class="col-sm-3 text-right control-label col-form-label">Package Name</label>
                                        <div class="col-sm-4">
											<!-- <input type="hidden" name="ipack_itm_no" id="ipack_itm_no" value="<?php echo $at_no; ?>" autocomplete="off" />
											<input type="hidden" name="itemdtl_counter" id="itemdtl_counter" value="<?php echo $pkg_list->ipack_totalitem; ?>" autocomplete="off" />
											<input type="hidden" name="itemdtl_qty" id="itemdtl_qty" value="<?php echo $pkg_list->ipack_total_qty; ?>" autocomplete="off" /> -->
											<input type="hidden" name="hidden_ipack_id" id="hidden_ipack_id" value="<?php echo $pkg_list->ipack_id; ?>" autocomplete="off" />
                                            <input type="text" class="form-control" name="pkg_name" id="pkg_name" placeholder="Enter Package Name" value="<?php echo $pkg_list->ipack_name; ?>" autocomplete="off" />
											<small class="invalid-feedback pkg_name"><?php echo form_error('pkg_name'); ?></small>
                                        </div>
										<div class="col-sm-4">
											<select class="form-control select2" name="pk_project" id="pk_project">
												<option value="">--select project--</option>
												<?php foreach($project_list as $project){?>
													<option value="<?php echo $project->proj_id?>" <?php if($pkg_list->ipack_project == $project->proj_id){echo 'selected';}?>><?php echo $project->proj_name?></option>
												<?php }?>
											</select>
											<small class="invalid-feedback pk_project"><?php echo form_error('pk_project'); ?></small>
										</div>
										
                                    </div>
                                    <div class="form-group row">
                                        <label for="cono1" class="col-sm-3 text-right control-label col-form-label">Package Details</label>
                                        <div class="col-sm-8">
                                            <textarea class="form-control" name="pkg_detail" id="pkg_detail" placeholder="Enter Package Details" autocomplete="off"><?php echo $pkg_list->ipack_details; ?></textarea>
											<small class="invalid-feedback pkg_detail"><?php echo form_error('pkg_detail'); ?></small>
                                        </div>
                                    </div>
									<div class="form-group row">
										<div class="col-sm-9 offset-1">
											<a href="javascript:;" onclick="goto_advance_item_lookup();" class="btn btn-primary mb-2"
											style="margin-right: 10px;">Advance Item Lookup</a>
											<!--								<a href="javascript:;" class="btn btn-primary mb-2" id="itemtbutton"-->
											<!--								   onclick="gotosubmit_itemset();" disabled>Add Item</a>-->
											<a href="javascript:;" onclick="goto_update_add_item()" class="btn btn-primary mb-2 add_row" id="itemtbutton">Add
												Item</a>
										</div>
									</div>
									<div class="form-group row justify-content-md-center">
										<div class="col-sm-10">
											<div class="table-responsive1">
											<table width="100%" class="table table-bordered" id="update-package-items-table">
												<thead>
													<tr>
														<th>Item</th>
														<th>CostCode</th>
														<th>Quantity</th>
														<th>Action</th>
													</tr>
												</thead>
												<tbody class="setall_experiences">
												<?php 
												$updateItemCount=1;
												foreach($item_detailsets as $d_items){ ?>
												<tr id="<?php echo $updateItemCount;?>">
													<input type="hidden" class="hidden_ipdetail_id" id="hidden_ipdetail_id<?php echo $updateItemCount;?>" value="<?php echo $d_items->ipdetail_id;?>">
													<td>
													<select class="form-control select2 custom-select pk_item update-package-item-required" name="pk_item" id="pk_item<?php echo $updateItemCount;?>" data-live-search="true" autocomplete="off" onchange="goto_check_item(<?php echo $updateItemCount;?>);">
													<option value="">---Select---</option>
													<?php foreach($itm_list as $items){ ?>
													<option value="<?php echo $items->item_code; ?>" <?php if($d_items->item_name == $items->item_name){echo 'selected';}?>><?php echo htmlspecialchars($items->item_code . ' - ' . $items->item_name, ENT_QUOTES); ?></option>
													<?php } ?>
													</select>
													<small class="invalid-feedback pk_item<?php echo $updateItemCount;?>"><?php echo form_error('pk_item'); ?></small>
													</td>
													<td>
														<input type="text" class="form-control pk_ccode update-package-item-required" name="pk_ccode" id="pk_ccode<?php echo $updateItemCount;?>" placeholder="Item Cost Code" autocomplete="off" readonly value="<?php echo $d_items->cc_no;?>"/>
														<small class="invalid-feedback pk_ccode<?php echo $updateItemCount;?>"><?php echo form_error('pk_ccode'); ?></small>
													</td>
													<td>
														<input type="text" class="form-control pk_itm_qnty update-package-item-required" name="pk_itm_qnty" id="pk_itm_qnty<?php echo $updateItemCount;?>" placeholder="Enter Quantity" autocomplete="off"  value="<?php echo $d_items->ipdetail_quantity;?>"/>
														<small class="invalid-feedback pk_itm_qnty<?php echo $updateItemCount;?>"><?php echo form_error('pk_itm_qnty'); ?></small>
													</td>
													<?php if($updateItemCount!=1){?>
													<td>
														<a id="removeItem" href="javascript:void(0);" title="Delete Record"><i class="fa fa-trash text-danger"></i></a>
													</td>
													<?php }?>
												</tr>
												<?php $updateItemCount++;} ?>
												<!-- <tr>
													<td colspan="4">
														<div align="center">
															<div class="get_error_total9" align="center" style="background-color: #bf0000;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
															<div class="get_success_total9" align="center" style="background-color: #174b10;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
															<div class="div_roller_total9" align="center" style="display: none;"><img src="<?php echo base_url(); ?>style/images/ajax_loader.gif" style="max-width: 60px;" /></div>
														</div>
													</td>
												</tr> -->
												<!-- </tbody>
												<tbody class="expr_setvalue">
												<?php foreach($item_detailsets as $d_items){ ?>
												<tr class="expset_<?php echo $d_items->ipdetail_id; ?>">
												<td><?php echo $d_items->item_name; ?></td>
												<td><?php echo $d_items->cc_no; ?></td>
												<td><?php echo $d_items->ipdetail_quantity; ?></td>
												<td><a href="javascript:;" onclick="gotodelete_items(<?php echo $d_items->ipdetail_id; ?>);"><i class="fa fa-trash text-danger"></i></a></td>
												</tr>
												<?php } ?>
												</tbody> -->
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
										&nbsp;<a href="<?= site_url('admincontrol/packages/all_package_list') ?>" class="btn btn-danger">Back</a>
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
            
			<!-- Modal -->
			<div class="modal fade" id="Modal_advanceitemlookup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
				aria-hidden="true">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Advance Item lookup</h5>
							<button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<input type="hidden" name="" id="hidden-item-id">
							<div class="row">
								<div class="col-12 text-center">
									<div class="form-check-inline">
										<label class="form-check-label">
											<input type="radio" class="form-check-input" name="itemfrom" value="1">Item Master
										</label>
									</div>
									<div class="form-check-inline">
										<label class="form-check-label">
											<input type="radio" class="form-check-input" name="itemfrom" checked value="2">Supplier Catalog
										</label>
									</div>
								</div>
							</div>
							<div class="row">
								<?php foreach($icat_list as $icat){?>
									<input type="hidden" id="catName<?php echo $icat->icat_id?>" value="<?php echo $icat->icat_name?>">
									<!-- <option value="<?php echo $icat->icat_id?>"><?php echo $icat->icat_name?></option> -->
								<?php }?>
								<div class="col-12 col-md-6">
									<label for="search_category"></label>
									<select class="form-control" name="search_category" id="search_category">
										<option value="">--Select Category--</option>
										<?php foreach($icat_list as $icat){?>
											<option value="<?php echo $icat->icat_id?>"><?php echo $icat->icat_name?></option>
										<?php }?>
									</select>
								</div>
								<div class="col-12 col-md-6">
									<label for="search_cc"></label>
									<select class="form-control" name="search_cc" id="search_cc">
										<option value="">--Select Cost Code--</option>
										<?php foreach($ccode_list as $ccode){?>
											<option value="<?php echo $ccode->cc_id?>"><?php echo $ccode->cc_no?></option>
										<?php }?>
									</select>
								</div>
							</div>
							<div class="item_row row mt-4" style="display:none;">
								<div class="col-12 table-responsive">
									<table class="table table-bordered" id="itemmaster_search_table" style="width:100%!important">
										<thead>
											<tr>
												<th>S No</th>
												<th>Item Code</th>
												<th>Item Name</th>
												<th>Category</th>
												<th>Cost Code</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
							</div>
							<div class="supc_row row mt-4">
								<div class="col-12 table-responsive">
									<table class="table table-bordered" id="supplierc_search_table">
										<thead>
											<tr>
												<th>S No</th>
												<th>Item Code</th>
												<th>Item Name</th>
												<th>Category</th>
												<th>Cost Code</th>
												<th>SKU No</th>
												<th>Price</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>-->
							<button type="button" id="select_multiple_items_btn" class="btn btn-primary">Submit
							</button>
						</div>
					</div>
				</div>
			</div>
<?php $this->load->view('admin/component/footer') ?>

<script type="text/javascript">
	$(function(){
	      $('.alert-error, .invalid-feedback').delay(8000).fadeOut();
		  $(".select2").selectpicker();
	});
	
	function goto_check_item(element){
		var pk_item = $('#pk_item'+element+' option:selected').val();
		
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
							$('#pk_ccode'+element).val(data.s_msg.cc_no);

						} else {
							$('#pk_ccode'+element).val('');
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
		
    	// var ipack_itm_no = $('#ipack_itm_no').val();
    	// var itemdtl_counter = $('#itemdtl_counter').val();
    	// var itemdtl_qty = $('#itemdtl_qty').val();
    	var pk_project = $('#pk_project').val();
    	var pkg_name = $('#pkg_name').val();
    	var pkg_detail = $('#pkg_detail').val();
    	var pkg_id = $('#hidden_ipack_id').val();;
    	//var ap_quaran = $("input[name='ap_quaran']:checked").val();
		
		// if (ipack_itm_no == "" || pkg_id == "") {
		// 	e_error = 1;
		// 	error_message = error_message + "<br/>ID missing, Refresh the page";
		// }
		// if (itemdtl_counter == "" || itemdtl_counter <= 0 || itemdtl_qty == "" || itemdtl_qty <= 0) {
		// 	e_error = 1;
		// 	error_message = error_message + "<br/>Item not found in the Package, Add some Item.";
		// }
		
		if(pk_project == ""){
			e_error = 1;
			$('.pk_project').html('Project Required.');
		}else{	
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

		$('.update-package-item-required').each(function(){
			var id=$(this).attr('id');
			if ($('#'+id).val() == "") {
				e_error = 1;
				$('.'+id).html('Required');
			} else {
				$('.'+id).html('');
			}
		});
		
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
			// form_data.append("ipack_itm_no", ipack_itm_no);
			// form_data.append("itemdtl_counter", itemdtl_counter);
			// form_data.append("itemdtl_qty", itemdtl_qty);
			form_data.append("pk_project", pk_project);
			form_data.append("pkg_name", pkg_name);
			form_data.append("pkg_detail", pkg_detail);
			form_data.append("pkg_delete_ids", JSON.stringify(rmComponentArray1));
			// alert(JSON.stringify(rmComponentArray1));
			var tableRow=0;
			for(i=1;i<=$('#update-package-items-table tbody tr').length;i++){
				form_data.append("ipdetail_id"+i, $('#hidden_ipdetail_id'+i).val());
				form_data.append("pk_item"+i, $('#pk_item'+i).val());
				form_data.append("pk_ccode"+i, $('#pk_ccode'+i).val());
				form_data.append("pk_itm_qnty"+i, $('#pk_itm_qnty'+i).val());
				tableRow++;
			}

			form_data.append("row_count", tableRow);
			
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
					// alert(JSON.stringify(data.s_msg));
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

function goto_advance_item_lookup(){
	// $('#hidden-item-id').val(element);
	itemIds=[];
	$('#Modal_advanceitemlookup').modal('show');
}

function getItemMasterItemsdata(){
		$('#itemmaster_search_table').DataTable({
			ajax:{
				url: '<?php echo base_url("admincontrol/porder/get_itemmaster_list")?>',
				type : "post",
				data :{
					category : $('#search_category').val(),
					cc : ''
				}
			},
			bDestroy : true
		});
	}

	function getSupplierCatelogItemsdata(){
		$('#supplierc_search_table').DataTable({
			ajax:{
				url: '<?php echo base_url("admincontrol/porder/get_suppliercatalog_list")?>',
				type : "post",
				data :{
					category : $('#search_category').val(),
					cc : $('#search_cc').val(),
					supplier : ''
				}
			},
			bDestroy : true
		});
	}

	getItemMasterItemsdata();
	getSupplierCatelogItemsdata();

	$('#search_category').change(function(){
		getItemMasterItemsdata();
		getSupplierCatelogItemsdata();
	});

	$('#search_cc').change(function(){
		getItemMasterItemsdata();
		getSupplierCatelogItemsdata();
	});

	// $('#po_supp').change(function(){
	// 	getSupplierCatelogItemsdata();
	// });
	
	$('input:radio[name="itemfrom"]').change(function(){
		var value=$(this).val();
		if(value==1){
			$('.item_row').css('display','block');
			$('.supc_row').css('display','none');
		}else{
			$('.item_row').css('display','none');
			$('.supc_row').css('display','block');
		}
	});

	var rowCountUpdate=2;
	function goto_update_add_item(){
		rowCountUpdate=($('#update-package-items-table tbody tr').length+1);
		$('#update-package-items-table tbody').append('\
		<tr id="row'+rowCountUpdate+'">\
		<input type="hidden" class="hidden_ipdetail_id" id="hidden_ipdetail_id'+rowCountUpdate+'">\
			<td>\
				<select class="form-control select2 custom-select update-package-item-required pk_item" name="pk_item" id="pk_item'+rowCountUpdate+'" data-live-search="true" autocomplete="off" onchange="goto_check_item('+rowCountUpdate+');">\
					<option value="">---Select---</option>\
					<?php foreach($itm_list as $items){ ?>
						<option value="<?php echo $items->item_code; ?>"><?php echo htmlspecialchars($items->item_code . ' - ' . $items->item_name, ENT_QUOTES); ?></option>\
					<?php } ?>
				</select><br>\
				<small class="invalid-feedback pk_item'+rowCountUpdate+'"></small>\
			</td>\
			<td>\
				<input type="text" class="form-control update-package-item-required pk_ccode" name="pk_ccode" id="pk_ccode'+rowCountUpdate+'" placeholder="Item Cost Code" autocomplete="off" readonly />\
				<small class="invalid-feedback pk_ccode'+rowCountUpdate+'"></small>\
			</td>\
			<td>\
				<input type="text" class="form-control update-package-item-required pk_itm_qnty" name="pk_itm_qnty" id="pk_itm_qnty'+rowCountUpdate+'" placeholder="Enter Quantity" autocomplete="off" />\
				<small class="invalid-feedback pk_itm_qnty'+rowCountUpdate+'"></small>\
			</td>\
			<td>\
				<a id="removeItem" href="javascript:void(0);" title="Delete Record"><i class="fa fa-trash text-danger"></i></a>\
			</td>\
		</tr>\
		');
		  $(".select2").selectpicker();
		rowCountUpdate++;
	}

	var rmComponentArray1= [];

	$('#update-package-items-table tbody').on('click', '#removeItem', function(){
		if (confirm('Are you sure you want to delete ?')) {
			var rowId=$(this).closest('tr').attr('id');
			var index=rowId.match(/\d+/);
			if($(this).closest('tr').children('#hidden_ipdetail_id'+index).val()!=""){
				rmComponentArray1.push($(this).closest('tr').children('#hidden_ipdetail_id'+index).val());
			}
			var child=$(this).closest('tr').nextAll();
			child.each(function(){
				var id=$(this).attr('id');
				var idx=$(this).children('.row-index');
				var dig=id.match(/\d+/);
				idx.html(`${dig-1}`);
				$(this).attr('id',`row${dig-1}`);
				$(this).children('.hidden_ipdetail_id').attr('id',`hidden_ipdetail_id${dig-1}`);
				$(this).find('select').attr('id',`pk_item${dig-1}`);
				$(this).children('td').children('.pk_ccode').attr('id',`pk_ccode${dig-1}`);
				$(this).children('td').children('.pk_itm_qnty').attr('id',`pk_itm_qnty${dig-1}`);
			});
			$(this).parent().parent().remove();
			rowCountUpdate--;
		} else {
		}
	});

	var itemIds=new Array();
	var itemValues=new Array();
	jQuery(document).delegate('.search-item','click',function(){
		if($(this).is(':checked')){
			itemIds.push($(this).attr('id'));
			itemValues.push($(this).val());
		}else{
			itemIds.splice( $.inArray($(this).attr('id'), itemIds), 1 );
			itemvalues.splice( $.inArray($(this).val(), itemValues), 1 );
		}
	});

	$('#select_multiple_items_btn').click(function(){
		// var itemCount=1;
		var itemFrom=$('input:radio[name="itemfrom"]:checked').val();
		// $('.search-item').each(function(){
		// 	if($(this).is(':checked')){
			for(itemCount=0;itemCount<itemIds.length;itemCount++){
				if($('#pk_item'+itemCount).val()==''){
					// var itemId=$('#hidden-item-id').val();
					var id=itemIds[itemCount];
					var index=id.match(/\d+/);
					var itemCode=itemValues[itemCount];
					$('#pk_item'+itemCount).val(itemCode).change();
				}else{
					var adItemCount=($('#update-package-items-table tbody tr').length+1);
					$('#update-package-items-table tbody').append('\
					<tr id="row'+adItemCount+'">\
					<input type="hidden" class="hidden_ipdetail_id" id="hidden_ipdetail_id'+adItemCount+'">\
						<td>\
							<select class="form-control select2 custom-select update-package-item-required pk_item" name="pk_item" id="pk_item'+adItemCount+'" data-live-search="true" autocomplete="off" onchange="goto_check_item('+adItemCount+');">\
								<option value="">---Select---</option>\
								<?php foreach($itm_list as $items){ ?>
									<option value="<?php echo $items->item_code; ?>"><?php echo htmlspecialchars($items->item_code . ' - ' . $items->item_name, ENT_QUOTES); ?></option>\
								<?php } ?>
							</select><br>\
							<small class="invalid-feedback pk_item'+adItemCount+'"></small>\
						</td>\
						<td>\
							<input type="text" class="form-control update-package-item-required pk_ccode" name="pk_ccode" id="pk_ccode'+adItemCount+'" placeholder="Item Cost Code" autocomplete="off" readonly />\
							<small class="invalid-feedback pk_ccode'+adItemCount+'"></small>\
						</td>\
						<td>\
							<input type="text" class="form-control update-package-item-required pk_itm_qnty" name="pk_itm_qnty" id="pk_itm_qnty'+adItemCount+'" placeholder="Enter Quantity" autocomplete="off" />\
							<small class="invalid-feedback pk_itm_qnty'+adItemCount+'"></small>\
						</td>\
						<td>\
							<a id="removeItem" href="javascript:void(0);" title="Delete Record"><i class="fa fa-trash text-danger"></i></a>\
						</td>\
					</tr>\
					');
					$(".select2").selectpicker();
					var id=itemIds[itemCount];
					var index=id.match(/\d+/);
					var itemCode=itemValues[itemCount];
					$('#pk_item'+adItemCount).val(itemCode).change();
					adItemCount++;
				}
				// itemCount++;
			}
		// });
	// 	var itemFrom=$('input:radio[name="itemfrom"]:checked').val();
	// 	var id=$(this).attr('id');
	// 	var index=id.match(/\d+/);
	// 	var itemId=$('#hidden-item-id').val();
	// 	if(itemFrom==1){
	// 		var itemCode=$('#search-item-code1'+index).val();
	// 	}else{
	// 		var itemCode=$('#search-item-code2'+index).val();
	// 	}
	// 	$('.pk_item'+itemId).val(itemCode).change();
		$('#Modal_advanceitemlookup').modal('hide');
		$('.search-item').prop('checked', false); 
	});

	// $('input:radio[name="itemfrom"]').change(function(){
	// 	$('.search-item').prop('checked', false); 
	// });
</script>
