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
                        <h4 class="page-title">Add New Receive Order</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url('admincontrol/dashboard'); ?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Add Receive Order</li>
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
                            <?php echo form_open_multipart('','class="form-horizontal" id="myForm" enctype="multipart/form-data"'); ?>
							    <div class="card-body">
                                    <?php if (isset($error)) { ?>
									<div class="alert alert-danger alert-error">                
										<h4>Error!</h4>
										<?php echo $error; ?>
									</div>
									<?php } ?>
			
									<!--<h4 class="card-title">Personal Info</h4>-->
                                    <div class="form-group row">
                                        <label for="fname" class="col-sm-2 text-right control-label col-form-label">Purchase Order No.</label>
                                        <div class="col-sm-6">
											<input type="hidden" name="ipack_itm_no" id="ipack_itm_no" value="<?php echo date('dmyHis'); ?>" autocomplete="off" />
											<input type="hidden" name="itemdtl_counter" id="itemdtl_counter" value="0" autocomplete="off" />
											<input type="hidden" name="itemdtl_tamount" id="itemdtl_tamount" value="0" autocomplete="off" />
											<select class="form-control select2 custom-select" name="po_id" id="po_id" data-live-search="true" autocomplete="off" onchange="goto_check_po_items();">
											<option value="">---Select---</option>
											<?php foreach($po_list as $p_items){ ?>
											<option value="<?php echo $p_items->porder_id; ?>"><?php echo $p_items->porder_no; ?></option>
											<?php } ?>
											</select>
											<small class="invalid-feedback po_id"><?php echo form_error('po_id'); ?></small>
                                        </div>
									</div>
                                    <div class="form-group row">
                                        <label for="fname" class="col-sm-2 text-right control-label col-form-label">Packing Slip Number</label>
                                        <div class="col-sm-3">
											<input class="form-control" name="ro_no" id="ro_no" placeholder="Enter Receive No." autocomplete="off" />
											<small class="invalid-feedback ro_no"><?php echo form_error('ro_no'); ?></small>
                                        </div>
									    <label for="cono1" class="col-sm-3 text-right control-label col-form-label">Receive Order Date</label>
                                        <div class="col-sm-3">
                                            <input class="form-control" name="ro_date" id="ro_date" placeholder="dd-mm-yyyy" autocomplete="off" />
											<small class="invalid-feedback ro_date"><?php echo form_error('ro_date'); ?></small>
                                        </div>
                                    </div>
									<div class="form-group row">
										<label for="cono1" class="col-sm-2 text-right control-label col-form-label">Receipt No</label>
										<div class="col-sm-3">
											<input class="form-control" name="ro_receipt_no" type="text" id="ro_receipt_no" placeholder="Receipt No" autocomplete="off" />
											<small class="invalid-feedback ro_receipt_no"><?php echo form_error('ro_receipt_no'); ?></small>
										</div>
										<label for="fname" class="col-sm-3 text-right control-label col-form-label">Attachment</label>
										<div class="col-sm-3">
											<input class="form-control" type="file" name="ro_file" id="ro_file" autocomplete="off" />
											<small class="invalid-feedback ro_file"><?php echo form_error('ro_file'); ?></small>
										</div>
									</div>
									<!--<div class="form-group row">
                                        <label for="cono1" class="col-sm-2 text-right control-label col-form-label">Delivery Note</label>
                                        <div class="col-sm-6">
                                            <textarea class="form-control" name="po_dl_note" id="po_dl_note" placeholder="Enter Delivery Note" autocomplete="off"></textarea>
											<small class="invalid-feedback po_dl_note"><?php //echo form_error('po_dl_note'); ?></small>
                                        </div>
                                    </div>-->
									<div class="form-group row justify-content-md-center collectitems">
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
										&nbsp;<a href="<?= site_url('admincontrol/recvorder/all_receive_order_list') ?>" class="btn btn-danger">Cancel</a>
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
		  $('#ro_date').datepicker({
            autoclose: true,
            todayHighlight: true,
			format: 'dd-mm-yyyy'
        });
	});
	
	function goto_check_po_items(){
		var po_id = $('#po_id option:selected').val();
		
		if(po_id != ""){
			var form_data = new FormData();
			form_data.append('po_id', po_id);
				
			$.ajax({
					method: 'POST',
					url: '<?php echo base_url() . "admincontrol/recvorder/get_all_items_find_for_receive"; ?>',
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
							$('.collectitems').html(data.s_msg);
							$('#ro_receipt_no').val(data.receipt_no);

						} else {
							$('.collectitems').html('');
						}
					}
				});
		
		}else{
			$('.collectitems').html('');
		}
	}
	
	
	function goto_submit_record(){
		$('.div_roller_total2').fadeIn();
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
		var onlynumerics_withdot = /^[0-9.]+$/;
		var specials_char = /[~`!#$%\^&*+=\[\]\\';./{}()|\\":<>\?]/g;
		var emailpattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;
		
		var totalitem_pkg = $('#totalitem_pkg').val();
		var supp_set = $('#po_supp option:selected').val();
		var name_supp_cp = $('#name_supp_cp').val();
		var supp_phone = $('#supp_phone').val();
		var supp_email = $('#supp_email').val();
		var supp_address = $('#supp_address').val();
		var ipack_itm_no = $('#ipack_itm_no').val();
		
		if (ipack_itm_no == "") {
			e_error = 1;
			error_message = error_message + "<br/>ID missing, Refresh the page";
		}

		if(totalitem_pkg == "" || parseInt(totalitem_pkg) == 0){
			e_error = 1;
			error_message = error_message + '<br/>Item is Missing, Check Again.';
		}

		if(supp_set == ""){
			e_error = 1;
			error_message = error_message + '<br/>Supplier is Missing, Check Again.';
		}
		
		if(parseInt(totalitem_pkg) > 0){
			
			var ii = 0;
			for(ii = 0;ii<parseInt(totalitem_pkg);ii++){
				var itmcode = $("input[name='ipackitem_code_"+ii+"']").val();
				var itmqty = $("input[name='ipackitem_qty_"+ii+"']").val();
				var itmprice = $("input[name='ipackitem_price_"+ii+"']").val();
				var receiveqty = $("#po_recv_qty_"+ii+"").val();

				if(itmqty == ""){
					e_error = 1;
					$('.ipackitem_qty_'+ii).html('Quantity is Required');
				}else{
					if (!itmqty.match(onlynumerics)) {
						e_error = 1;
						$('.ipackitem_qty_'+ii).html('Quantity use only Numeric Value');
					}else if(parseInt(itmqty) <= 0){
						e_error = 1;
						$('.ipackitem_qty_'+ii).html('Quantity always greater than 0');
					}else{
						$('.ipackitem_qty_'+ii).html('');
					}
				}
				if(receiveqty == ""){
					e_error = 1;
					$('.po_recv_qty_'+ii).html('Quantity is Required');
				}else{
					if (!itmqty.match(onlynumerics)) {
						e_error = 1;
						$('.po_recv_qty_'+ii).html('Quantity use only Numeric Value');
					}else if(parseInt(itmqty) <= 0){
						e_error = 1;
						$('.po_recv_qty_'+ii).html('Quantity always greater than 0');
					}else{
						$('.po_recv_qty_'+ii).html('');
					}
				}
				if(itmprice == ""){
					e_error = 1;
					$('.ipackitem_price_'+ii).html('Price is Required');
				}else{
					if (!itmprice.match(onlynumerics_withdot)) {
						e_error = 1;
						$('.ipackitem_price_'+ii).html('Price use only Numeric Value');
					}else if(parseInt(itmprice) <= 0){
						e_error = 1;
						$('.ipackitem_price_'+ii).html('Price always greater than 0');
					}else{
						$('.ipackitem_price_'+ii).html('');
					}
				}
			}

		}

		if (e_error == 1) {
			$('.div_roller_total2').fadeOut();
			$('#submit_record_btn').prop('disabled', false);
			$('.close_modal').show();
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
			form_data.append("totalitem_pkg", totalitem_pkg);
			form_data.append("supp_set", supp_set);
			form_data.append("ipack_itm_no", ipack_itm_no);

			for(ii = 0;ii<parseInt(totalitem_pkg);ii++){
				var itmcode = $("input[name='ipackitem_code_"+ii+"']").val();
				var itmqty = $("input[name='ipackitem_qty_"+ii+"']").val();
				var itmprice = $("input[name='ipackitem_price_"+ii+"']").val();

				form_data.append('itmcode[]', itmcode);
				form_data.append('itmqty[]', itmqty);
				form_data.append('itmprice[]', itmprice);
			}
			
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/porder/add_multiple_items_from_package_sets') ?>",
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
						$('#packset_item').html('');
						$('#Modal_addrecord').modal('hide');
						//toastr.success('Item is Added Successfully!', 'Success');
						$('.get_success_total9').html('Item is Added in the List Successfully.');
						$(".get_success_total9").fadeIn();
						$('.expr_setvalue').append(data.s_msg);
						
						$('#itemdtl_counter').val(data.titem);
						$('#itemdtl_tamount').val(data.tamount);
						$('#pk_ccode, #pk_sku, #pk_uom, #pk_itm_price, #pk_itm_qnty, #pk_subtotal, #pk_tax_amt, #pk_total_amt').val('');
						$('#pk_code, #pk_item').val('');
						$('#pk_code, #pk_item').selectpicker('refresh');
						setTimeout(function() {
							$('.get_success_total9').fadeOut();
						}, 3000);
						
					}else{
						$('.div_roller_total2').fadeOut();
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

	function validateReceiveQty(element)
	{

		var max = parseInt($(element).attr('max'));
		var min = parseInt($(element).attr('min'));
		if ($(element).val() > max)
		{
			// var error_message = "Value cannot be more than balance qty.";
			// $('.div_roller_total').fadeOut();
			// $('.get_error_total').html(error_message);
			// $(".get_error_total").fadeIn();
			// $(".invalid-feedback").fadeIn();
			// setTimeout(function(){ $('.invalid-feedback, .get_error_total').fadeOut(); }, 8000);
			// $(element).val(0);
		}
		else if ($(element).val() < min)
		{
			// $(element).val(min);
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
		
    	var po_id = $('#po_id option:selected').val();
    	var ro_no = $('#ro_no').val();
    	var ro_date = $('#ro_date').val();
    	var ro_receipt_no = $('#ro_receipt_no').val();
		var file_data = $('#ro_file').prop('files')[0];
		var totalitem_po = $('#totalitem_po').val();
		//var ap_quaran = $("input[name='ap_quaran']:checked").val();

		if (totalitem_po == "" || totalitem_po <= 0) {
			e_error = 1;
			error_message = error_message + "<br/>Item not found in the Purchase Order, Check it Again.";
		}
		
		if(po_id == ""){
			e_error = 1;
			$('.po_id').html('Purchase Order is Required.');
		}else{
			if(!po_id.match(onlynumerics)){
				e_error = 1;
				$('.po_id').html('Purchase Order only use Numeric value, Check again.');
			}else{
				$('.po_id').html('');
			}	
		}
		if(ro_no == ""){
			e_error = 1;
			$('.ro_no').html('Receive Slip No. is Required.');
		}else{
			if(!ro_no.match(alphanumerics_spaces)){
				e_error = 1;
				$('.ro_no').html('Receive Slip No. not use special charecters [without _ . , -], Check again.');
			}else{
				$('.ro_no').html('');
			}	
		}

		if (ro_date == "") {
			e_error = 1;
			$('.ro_date').html('Receive Order Date is Required.');
		}else{
			$('.ro_date').html('');
		}

		if(parseInt(totalitem_po) > 0){

			var ii = 0;
			for(ii = 0;ii<parseInt(totalitem_po);ii++){

				var receiveqty = $("#po_recv_qty_"+ii+"").val();
				var max = $("#po_recv_qty_"+ii+"").attr("max");
				if(receiveqty == ""){
					e_error = 1;
					$('.po_recv_qty_'+ii).html('Quantity is Required');
				}else{
					if(parseInt(receiveqty) > max){
						e_error = 1;
						$('.po_recv_qty_'+ii).html('Quantity should be less than balance qty');
					}else{
						$('.po_recv_qty_'+ii).html('');
					}
				}

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
			form_data.append("ro_no", ro_no);
			form_data.append("ro_date", ro_date);
			form_data.append("ro_receipt_no", ro_receipt_no);
			form_data.append("totalitem_po", totalitem_po);

			form_data.append('ro_file', file_data);
			// for(ii = 0;ii<parseInt(totalitem_po);ii++){
			// 	var itmcode = $("input[name='itemset_code_"+ii+"']").val();
			// 	var balance_qty = $("input[name='po_balanceqty_"+ii+"']").val();
			// 	var recv_qty = $("input[name='po_recv_qty_"+ii+"']").val();
			//
			// 	form_data.append('itmcode[]', itmcode);
				// form_data.append('balance_qty[]', balance_qty);
				// form_data.append('recv_qty[]', recv_qty);
			// }

			var itmcode = $('input[name="itemset_code[]"]').map(function () {
				return this.value; // $(this).val()
			}).get();

			var total_qty = $('input[name="po_totalqty[]"]').map(function () {
				return this.value; // $(this).val()
			}).get();

			var balance_qty = $('input[name="po_balanceqty[]"]').map(function () {
				return this.value; // $(this).val()
			}).get();

			var recv_qty = $('input[name="po_recv_qty[]"]').map(function () {
				return this.value; // $(this).val()
			}).get();

			form_data.append('itmcode[]', JSON.stringify(itmcode));
			form_data.append('balance_qty[]', JSON.stringify(balance_qty));
			form_data.append('recv_qty[]', JSON.stringify(recv_qty));
			form_data.append('total_qty[]', JSON.stringify(total_qty));

			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/recvorder/new_recv_order_Set_submission') ?>",
				dataType: 'json',
				data: form_data,
				contentType:false,
				cache:false,
				processData:false,
				success:function(data){
					//alert(data.msg);
					if(data.msg == 1)
					{
						//console.log(data);
						//alert(data.msg[0].space_rate);
						// alert(JSON.stringify(data.s_msg));
						$('.div_roller_total').fadeOut();
						toastr.success('Record is Inserted Successfully!', 'Success');
						setTimeout(function(){
							window.location.replace("<?php echo site_url('admincontrol/recvorder/all_receive_order_list') ?>");
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
