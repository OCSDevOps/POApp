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
                        <h4 class="page-title">Update Purchase Order</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url('admincontrol/dashboard'); ?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Update Purchase Order</li>
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
                                        <label for="fname" class="col-sm-2 text-right control-label col-form-label">Project</label>
                                        <div class="col-sm-3">
											<input type="hidden" name="ipack_itm_no" id="ipack_itm_no" value="<?php echo $at_no; ?>" autocomplete="off" />
											<input type="hidden" name="itemdtl_counter" id="itemdtl_counter" value="<?php echo $porder_list->porder_total_item; ?>" autocomplete="off" />
											<input type="hidden" name="itemdtl_tamount" id="itemdtl_tamount" value="<?php echo $porder_list->porder_total_amount; ?>" autocomplete="off" />
											<select class="form-control select2 custom-select" name="po_project" id="po_project" data-live-search="true" autocomplete="off" onchange="goto_check_project();">
											<option value="">---Select---</option>
											<?php foreach($proj_list as $p_items){ ?>
											<option value="<?php echo $p_items->proj_id; ?>" <?php if($p_items->proj_id == $porder_list->porder_project_ms){echo "selected";} ?>><?php echo $p_items->proj_name; ?></option>
											<?php } ?>
											</select>
											<small class="invalid-feedback po_project"><?php echo form_error('po_project'); ?></small>
                                        </div>
									    <label for="fname" class="col-sm-3 text-right control-label col-form-label">P.O. Number</label>
                                        <div class="col-sm-3">
											<input type="text" class="form-control" name="po_numner" id="po_numner" value="<?php echo $porder_list->porder_no; ?>" autocomplete="off" />
											<small class="invalid-feedback po_numner"><?php echo form_error('po_numner'); ?></small>
                                        </div>
									</div>
                                    <div class="form-group row">
                                        <label for="fname" class="col-sm-2 text-right control-label col-form-label">Supplier</label>
                                        <div class="col-sm-3">
											<select class="form-control select2 custom-select" name="po_supp" id="po_supp" data-live-search="true" autocomplete="off">
											<option value="">---Select---</option>
											<?php foreach($supp_list as $sup_items){ ?>
											<option value="<?php echo $sup_items->sup_id; ?>" <?php if($sup_items->sup_id == $porder_list->porder_supplier_ms){echo "selected";} ?>><?php echo $sup_items->sup_name; ?></option>
											<?php } ?>
											</select>
											<small class="invalid-feedback po_supp"><?php echo form_error('po_supp'); ?></small>
                                        </div>
									    <label for="cono1" class="col-sm-3 text-right control-label col-form-label">Delivery Address</label>
                                        <div class="col-sm-3">
                                            <textarea class="form-control" name="po_address" id="po_address" placeholder="Enter Delivery Address" autocomplete="off"><?php echo $porder_list->porder_address; ?></textarea>
											<small class="invalid-feedback po_address"><?php echo form_error('po_address'); ?></small>
                                        </div>
                                    </div>
									<div class="form-group row">
                                        <label for="cono1" class="col-sm-2 text-right control-label col-form-label">Delivery Note</label>
                                        <div class="col-sm-6">
                                            <textarea class="form-control" name="po_dl_note" id="po_dl_note" placeholder="Enter Delivery Note" autocomplete="off"><?php echo $porder_list->porder_delivery_note; ?></textarea>
											<small class="invalid-feedback po_dl_note"><?php echo form_error('po_dl_note'); ?></small>
                                        </div>
                                    </div>
									<div class="form-group row justify-content-md-center">
										<div class="col-sm-12">
											<div class="table-responsive1">
											<table width="100%" class="table table-bordered">
												<thead>
													<tr>
														<th>Item Code</th>
														<th>Item Name</th>
														<th>CostCode</th>
														<th>SKU</th>
														<th>UOM</th>
														<th>Tax Code</th>
														<th>Quantity</th>
														<th>Unit Price</th>
														<th>Sub Total</th>
														<th>Tax Amount</th>
														<th>Total</th>
														<th>Action</th>
													</tr>
												</thead>
												<tbody class="setall_experiences">
												<tr>
													<td>
													<select class="form-control select2 custom-select" name="pk_code" id="pk_code" data-live-search="true" autocomplete="off" onchange="goto_check_item('code');">
													<option value="">---Select---</option>
													<?php foreach($itm_list as $items){ ?>
													<option value="<?php echo $items->item_code; ?>"><?php echo $items->item_code; ?></option>
													<?php } ?>
													</select>
													<small class="invalid-feedback pk_code"><?php echo form_error('pk_code'); ?></small>
													</td>
													<td>
													<select class="form-control select2 custom-select" name="pk_item" id="pk_item" data-live-search="true" autocomplete="off" onchange="goto_check_item('name');">
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
														<input type="text" class="form-control" name="pk_sku" id="pk_sku" placeholder="Item SKU" autocomplete="off" readonly />
														<small class="invalid-feedback pk_sku"><?php echo form_error('pk_sku'); ?></small>
													</td>
													<td>
														<input type="text" class="form-control" name="pk_uom" id="pk_uom" placeholder="Item UOM" autocomplete="off" readonly />
														<small class="invalid-feedback pk_uom"><?php echo form_error('pk_uom'); ?></small>
													</td>

													<td>
														<input type="text" class="form-control" name="pk_itm_qnty" id="pk_itm_qnty" placeholder="Enter Quantity" autocomplete="off" onkeyup="goto_check_item_amounts();" />
														<small class="invalid-feedback pk_itm_qnty"><?php echo form_error('pk_itm_qnty'); ?></small>
													</td>
													<td>
														<input type="text" class="form-control" name="pk_itm_price" id="pk_itm_price" placeholder="Enter Price" autocomplete="off" onkeyup="goto_check_item_amounts();" />
														<small class="invalid-feedback pk_itm_price"><?php echo form_error('pk_itm_price'); ?></small>
													</td>
													<td>
														<input type="text" class="form-control" name="pk_subtotal" id="pk_subtotal" placeholder="Item Sub Total" autocomplete="off" readonly />
														<small class="invalid-feedback pk_subtotal"><?php echo form_error('pk_subtotal'); ?></small>
													</td>
													<td>
														<input type="text" class="form-control" name="pk_tax_amt" id="pk_tax_amt" placeholder="Item Tax Amount" autocomplete="off" readonly />
														<small class="invalid-feedback pk_tax_amt"><?php echo form_error('pk_tax_amt'); ?></small>
													</td>
													<td>
														<input type="text" class="form-control" name="pk_total_amt" id="pk_total_amt" placeholder="Item Total Amount" autocomplete="off" readonly />
														<small class="invalid-feedback pk_total_amt"><?php echo form_error('pk_total_amt'); ?></small>
													</td>

													<td>
														<a href="javascript:;" class="btn btn-sm btn-primary" id="itemtbutton" onclick="gotosubmit_itemset();" disabled>ADD ITEM</a>
													</td>
												</tr>
												<tr>
													<td colspan="12">
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
												<tr class="expset_<?php echo $d_items->po_detail_id; ?>">
												<td><?php echo $d_items->po_detail_item; ?></td>
												<td><?php echo $d_items->item_name; ?></td>
												<td><?php echo $d_items->cc_no; ?></td>
												<td><?php echo $d_items->po_detail_sku; ?></td>
												<td><?php echo $d_items->uom_name; ?></td>
												<td><?php echo $d_items->po_detail_quantity; ?></td>
												<td><?php echo $d_items->po_detail_unitprice; ?></td>
												<td><?php echo $d_items->po_detail_subtotal; ?></td>
												<td><?php echo $d_items->po_detail_taxamount; ?></td>
												<td><?php echo $d_items->po_detail_total; ?></td>
												<td><a href="javascript:;" onclick="gotodelete_items(<?php echo $d_items->po_detail_id; ?>);"><i class="fa fa-trash text-danger"></i></a></td>
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
										&nbsp;<a href="<?= site_url('admincontrol/porder/all_purchase_order_list') ?>" class="btn btn-danger">Cancel</a>
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
	
	function goto_check_item_amounts(){
		var pk_itm_qnty = $('#pk_itm_qnty').val();
		var pk_itm_price = $('#pk_itm_price').val();
		if(pk_itm_qnty != "" &&  pk_itm_price != ""){
			var subtotal_amount = parseFloat(pk_itm_qnty) * parseFloat(pk_itm_price);
			var tax_amount = (parseFloat(subtotal_amount)) / 100;
			var alltotal = parseFloat(subtotal_amount) + parseFloat(tax_amount);
			$('#pk_subtotal').val(subtotal_amount);
			$('#pk_tax_amt').val(tax_amount);
			$('#pk_total_amt').val(alltotal);
		}else{
			$('#pk_subtotal').val('');
			$('#pk_tax_amt').val('');
			$('#pk_total_amt').val('');
		}
	}
	
	function goto_check_project(){
		var po_project = $('#po_project option:selected').val();
		
		if(po_project != ""){
			var form_data = new FormData();
			form_data.append('po_project', po_project);
				
			$.ajax({
					method: 'POST',
					url: '<?php echo base_url() . "admincontrol/porder/get_address_from_porject_find"; ?>',
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
							$('#po_address').val(data.s_msg.proj_address);

						} else {
							$('#po_address').val('');
						}
					}
				});
		
		}else{
			$('#po_address').val('');
		}
	}
	
	function goto_check_item(recordid){
		var supp_set = $('#po_supp option:selected').val();
		//alert(supp_set);return;
		if(supp_set != ""){
			if(recordid != ""){
				if(recordid == "code"){
					var pk_code = $('#pk_code option:selected').val();
					if(pk_code != ""){
						$('#pk_item').val(pk_code);
						$('#pk_item').selectpicker('refresh');
					}
				}else if(recordid == "name"){
					var pk_item = $('#pk_item option:selected').val();
					if(pk_item != ""){
						$('#pk_code').val(pk_item);
						$('#pk_code').selectpicker('refresh');
					}
				}
			}
			var pk_code2 = $('#pk_code option:selected').val();
			
			if(pk_code2 != ""){
				var form_data = new FormData();
				form_data.append('pk_item', pk_code2);
				form_data.append('supp_set', supp_set);
					
				$.ajax({
						method: 'POST',
						url: '<?php echo base_url() . "admincontrol/porder/get_alldetails_from_item_find"; ?>',
						data: form_data,
						dataType: 'JSON',
						contentType: false,
						processData: false,
						success: function(data) {
							//alert(data.msg);
							if (data.msg == 1) {
								//console.log(data);
								//alert(data.msg[0].space_rate);
								//$('.div_roller_total9').fadeOut();
								$('#pk_ccode').val(data.s_msg.cc_no);
								$('#pk_sku').val(data.supp_set.supcat_sku_no);
								$('#pk_uom').val(data.s_msg.uom_name);
								$('#pk_itm_price').val(data.supp_set.supcat_price);

							} else {
								$('#pk_ccode, #pk_sku, #pk_uom, #pk_itm_price, #pk_subtotal, #pk_tax_amt, #pk_total_amt').val('');
								$('.get_error_total9').html(data.e_msg);
								$(".get_error_total9").fadeIn();
								setTimeout(function() {
									$('.invalid-feedback, .get_error_total9').fadeOut();
								}, 5000);
							}
						}
					});
			
			}else{
				$('#pk_ccode, #pk_sku, #pk_uom, #pk_taxcode, #pk_itm_price, #pk_subtotal, #pk_tax_amt, #pk_total_amt').val('');
			}
		}else{
			$('#pk_code, #pk_item').val('');
			$('#pk_code, #pk_item').selectpicker('refresh');
			$('#pk_ccode, #pk_sku, #pk_uom, #pk_taxcode, #pk_itm_price, #pk_subtotal, #pk_tax_amt, #pk_total_amt').val('');
			var error_message = "Supplier is needed. please select.";
			$('.get_error_total9').html(error_message);
			$(".get_error_total9").fadeIn();
			setTimeout(function() {
				$('.invalid-feedback, .get_error_total9').fadeOut();
			}, 5000);
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
		
		var pk_code = $('#pk_code option:selected').val();
		var pk_item = $('#pk_item option:selected').val();
		var pk_ccode = $('#pk_ccode').val();
		var pk_sku = $('#pk_sku').val();
		var pk_uom = $('#pk_uom').val();
		var pk_taxcode = $('#pk_taxcode').val();
		var pk_itm_qnty = $('#pk_itm_qnty').val();
		var pk_itm_price = $('#pk_itm_price').val();
		var pk_subtotal = $('#pk_subtotal').val();
		var pk_tax_amt = $('#pk_tax_amt').val();
		var pk_total_amt = $('#pk_total_amt').val();
		var ipack_itm_no = $('#ipack_itm_no').val();
		
		if (ipack_itm_no == "") {
			e_error = 1;
			error_message = error_message + "<br/>ID missing, Refresh the page";
		}

		if(pk_code == ""){
			e_error = 1;
			$('.pk_code').html('Item Code is Required.');
		}else{
			$('.pk_code').html('');
		}
		if(pk_item == ""){
			e_error = 1;
			$('.pk_item').html('Item Name is Required.');
		}else{
			$('.pk_item').html('');
		}
		if(pk_code != "" && pk_item != ""){
			if(pk_code != pk_item){
				e_error = 1;
				error_message = error_message + "<br/>Item Name and Item Code not Matched Properly and Try again.";
			}
		}
		if(pk_ccode == ""){
			e_error = 1;
			$('.pk_ccode').html('Item CostCode is Required.');
		}else{
			$('.pk_ccode').html('');
		}
		if(pk_sku == ""){
			e_error = 1;
			$('.pk_sku').html('SKU is Required.');
		}else{
			$('.pk_sku').html('');
		}
		if(pk_uom == ""){
			e_error = 1;
			$('.pk_uom').html('UOM is Required.');
		}else{
			$('.pk_uom').html('');
		}
		if(pk_taxcode == ""){
			e_error = 1;
			$('.pk_taxcode').html('Tax Code is Required.');
		}else{
			$('.pk_taxcode').html('');
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
		if (pk_itm_price == "") {
			e_error = 1;
			$('.pk_itm_price').html('Price is Required.');
		} else {
			if (!pk_itm_price.match(onlynumerics_withdot)) {
				e_error = 1;
				$('.pk_itm_price').html('Price only use numeric values, Check again.');
			} else if (parseInt(pk_itm_price) <= 0) {
				e_error = 1;
				$('.pk_itm_price').html('Price always greater than 0, Check again.');
			} else {
				$('.pk_itm_price').html('');
			}
		}

		if (pk_subtotal == "") {
			e_error = 1;
			$('.pk_subtotal').html('Sub Total is Required.');
		} else {
			if (!pk_subtotal.match(onlynumerics_withdot)) {
				e_error = 1;
				$('.pk_subtotal').html('Sub Total only use numeric values, Check again.');
			} else if (parseInt(pk_subtotal) <= 0) {
				e_error = 1;
				$('.pk_subtotal').html('Sub Total always greater than 0, Check again.');
			} else {
				$('.pk_subtotal').html('');
			}
		}
		if (pk_tax_amt == "") {
			e_error = 1;
			$('.pk_tax_amt').html('Tax Amount is Required.');
		} else {
			if (!pk_tax_amt.match(onlynumerics_withdot)) {
				e_error = 1;
				$('.pk_tax_amt').html('Tax Amount only use numeric values, Check again.');
			} else if (parseInt(pk_tax_amt) < 0) {
				e_error = 1;
				$('.pk_tax_amt').html('Tax Amount always greater than or Equal to 0, Check again.');
			} else {
				$('.pk_tax_amt').html('');
			}
		}
		if (pk_total_amt == "") {
			e_error = 1;
			$('.pk_total_amt').html('Total Amount is Required.');
		} else {
			if (!pk_total_amt.match(onlynumerics_withdot)) {
				e_error = 1;
				$('.pk_total_amt').html('Total Amount only use numeric values, Check again.');
			} else if (parseInt(pk_total_amt) <= 0) {
				e_error = 1;
				$('.pk_total_amt').html('Total Amount always greater than 0, Check again.');
			} else {
				$('.pk_total_amt').html('');
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
			form_data.append('pk_code', pk_code);
			form_data.append('pk_item', pk_item);
			form_data.append('pk_ccode', pk_ccode);
			form_data.append('pk_sku', pk_sku);
			form_data.append('pk_uom', pk_uom);
			form_data.append('pk_taxcode', pk_taxcode);
			form_data.append('pk_itm_qnty', pk_itm_qnty);
			form_data.append('pk_itm_price', pk_itm_price);
			form_data.append('pk_subtotal', pk_subtotal);
			form_data.append('pk_tax_amt', pk_tax_amt);
			form_data.append('pk_total_amt', pk_total_amt);
			//form_data.append("files", files[0]);
			$.ajax({
				method: 'POST',
				url: '<?php echo base_url() . "admincontrol/porder/new_porder_item_submission"; ?>',
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
						var expr_string = '<tr class="expset_' + data.cat_set.po_detail_id + '"><td>' + data.cat_set.po_detail_item + '</td><td>' + data.cat_set.item_name + '</td><td>' + data.cat_set.cc_no + '</td><td>' + data.cat_set.po_detail_sku + '</td><td>' + data.cat_set.uom_name + '</td><td>' + data.cat_set.po_detail_taxcode + '</td><td>' + data.cat_set.po_detail_quantity + '</td><td>' + data.cat_set.po_detail_unitprice + '</td><td>' + data.cat_set.po_detail_subtotal + '</td><td>' + data.cat_set.po_detail_taxamount + '</td><td>' + data.cat_set.po_detail_total + '</td><td><a href="javascript:;" onclick="gotodelete_items(' + data.cat_set.po_detail_id + ');"><i class="fa fa-trash text-danger"></i></a></td></tr>';
						$('.expr_setvalue').append(expr_string);
						
						var item_counter = $('#itemdtl_counter').val();
						var item_amount = $('#itemdtl_tamount').val();
						item_counter = Number(item_counter) + 1;
						item_amount = parseFloat(item_amount) + parseFloat(data.cat_set.po_detail_total);
						$('#itemdtl_counter').val(item_counter);
						$('#itemdtl_tamount').val(item_amount);
						$('#pk_ccode, #pk_sku, #pk_uom, #pk_taxcode, #pk_itm_price, #pk_itm_qnty, #pk_subtotal, #pk_tax_amt, #pk_total_amt').val('');
						$('#pk_code, #pk_item').val('');
						$('#pk_code, #pk_item').selectpicker('refresh');
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
					url: '<?php echo base_url() . "admincontrol/porder/delete_itemset_update"; ?>',
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
							
							var item_amount = $('#itemdtl_tamount').val();
							item_amount = parseFloat(item_amount) - parseFloat(data.expmarks.po_detail_total);
							$('#itemdtl_tamount').val(item_amount);
							
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
    	var itemdtl_tamount = $('#itemdtl_tamount').val();
    	var po_project = $('#po_project option:selected').val();
    	var po_numner = $('#po_numner').val();
    	var po_supp = $('#po_supp option:selected').val();
    	var po_address = $('#po_address').val();
    	var po_dl_note = $('#po_dl_note').val();
		var po_id = '<?php echo $porder_list->porder_id; ?>';
    	//var ap_quaran = $("input[name='ap_quaran']:checked").val();
		
		if (ipack_itm_no == "" || po_id == "") {
			e_error = 1;
			error_message = error_message + "<br/>ID missing, Refresh the page";
		}
		if (itemdtl_counter == "" || itemdtl_counter <= 0 || itemdtl_tamount == "" || itemdtl_tamount <= 0) {
			e_error = 1;
			error_message = error_message + "<br/>Item not found in the Purchase Order, Add some Item.";
		}
		
		if(po_project == ""){
			e_error = 1;
			$('.po_project').html('Project is Required.');
		}else{
			if(!po_project.match(onlynumerics)){
				e_error = 1;
				$('.po_project').html('Project only use Numeric value, Check again.');
			}else{
				$('.po_project').html('');
			}	
		}
		if(po_numner == ""){
			e_error = 1;
			$('.po_numner').html('Purchase Order No. is Required.');
		}else{
			if(!po_numner.match(alphanumerics_spaces)){
				e_error = 1;
				$('.po_numner').html('Purchase Order No. not use special carecters [without _ . , -], Check again.');
			}else{
				$('.po_numner').html('');
			}	
		}
		if(po_supp == ""){
			e_error = 1;
			$('.po_supp').html('Project is Required.');
		}else{
			if(!po_supp.match(onlynumerics)){
				e_error = 1;
				$('.po_supp').html('Project only use Numeric value, Check again.');
			}else{
				$('.po_supp').html('');
			}	
		}
		if(po_address == ""){
			e_error = 1;
			$('.po_address').html('Delivery Address is Required.');
		}else{
			$('.po_address').html('');	
		}
		if(po_dl_note != ""){
			if(!po_dl_note.match(alphanumerics_no)){
				e_error = 1;
				$('.po_dl_note').html('Delivery Note not use special carecters [without _ / : ( @ . & ) , -], Check again.');
			}else{
				$('.po_dl_note').html('');
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
			form_data.append("po_id", po_id);
			form_data.append("ipack_itm_no", ipack_itm_no);
			form_data.append("itemdtl_counter", itemdtl_counter);
			form_data.append("itemdtl_tamount", itemdtl_tamount);
			form_data.append("po_project", po_project);
			form_data.append("po_numner", po_numner);
			form_data.append("po_supp", po_supp);
			form_data.append("po_address", po_address);
			form_data.append("po_dl_note", po_dl_note);
			
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/porder/modify_porder_set_submission') ?>",
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
							window.location.replace("<?php echo site_url('admincontrol/porder/all_purchase_order_list') ?>");
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
