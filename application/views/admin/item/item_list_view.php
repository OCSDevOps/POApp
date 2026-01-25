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
                        <h4 class="page-title">Item List</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url('admincontrol/dashboard'); ?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Item List</li>
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
								<form action="" method="get" enctype="multipart/form-data">
									<div class="row">
										<label class="col-sm-1 text-right control-label col-form-label">Category</label>
										<div class="col-sm-2">
											<select name="category" class="form-control selectpicker">
												<option value="">Select Option</option>

												<?php foreach ($category_list as $category) {?>
													<option value="<?php echo $category->icat_id ?>" <?php echo isset($filters['item_cat_ms']) ? ($filters['item_cat_ms'] == $category->icat_id ? 'selected' : '') : '' ?>><?php echo $category->icat_name?></option>
												<?php } ?>
											</select>
										</div>
										<label class="col-sm-1 text-right control-label col-form-label">CostCode</label>
										<div class="col-sm-2">
											<select name="cost_code" class="form-control selectpicker">
												<option value="">Select Option</option>
												<?php foreach ($ccode_list as $ccode) {?>
													<option value="<?php echo $ccode->cc_id ?>" <?php echo isset($filters['item_ccode_ms']) ? ($filters['item_ccode_ms'] == $ccode->cc_id ? 'selected' : '') : '' ?>><?php echo $ccode->cc_no.' - '.$ccode->cc_description?></option>
												<?php } ?>
											</select>
										</div>
										<label class="col-sm-1 text-right control-label col-form-label">Rentable</label>
										<div class="col-sm-2">
											<select name="rentable" class="form-control selectpicker">
												<option value="">Select Option</option>
												<option value="1" <?php echo isset($filters['item_is_rentable']) ? ($filters['item_is_rentable'] == "1" ? 'selected' : '') : '' ?>>Yes</option>
												<option value="0" <?php echo isset($filters['item_is_rentable']) ? ($filters['item_is_rentable'] == "0" ? 'selected' : '') : '' ?>>No</option>
											</select>
										</div>
										<div class="col-sm-2">
											<button type="submit" class="btn btn-primary">Search</button>
											<button type="button" onclick="window.location.href='<?php echo base_url("admincontrol/items/item_list"); ?>'" class="btn btn-success">Reset</button>
										</div>
									</div>
								</form>

							</div>
						</div>
					</div>
				</div>

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
								if($this->session->userdata('utype')==1 || $templateDetails->pt_i_item<3){?>
									<a href="javascript:;" onclick="goto_add_record();" class="btn btn-primary float-right mb-2" style="margin-right: 10px;">Add New Item</a>
									<a href="javascript:;" onclick="goto_bulkupload_record();" class="btn btn-primary float-right mb-2" style="margin-right: 10px;">Bulk upload - Item</a>
								<?php }?>
								<a href="<?php base_url() ?>export_csv" class="btn btn-primary float-right mb-2" style="margin-right: 10px;">Export CSV</a>
								<div class="table-responsive">
                                    <table id="zero_config" class="table table-striped table-bordered">
                                        <thead>
                                            <tr style="font-weight: bold;">
                                                <th>Sl No.</th>
                                                <th>Item Code</th>
                                                <th>Item Name</th>
                                                <th>Category</th>
                                                <th>Cost Code</th>
                                                <th>UOM</th>
                                                <th>Rentable</th>
												<th>Status</th>
												<?php 
												if($this->session->userdata('utype')==1 || $templateDetails->pt_i_item<3){?>
													<th>Action</th>
												<?php }?>
                                            </tr>
                                        </thead>
                                        <tbody>
										<?php foreach($getrecord_list as $keys=>$recorditem)
										{ ?>
										<tr>
											<td><?php echo $keys+1; ?></td>
											<td><?php echo $recorditem->item_code; ?></td>
											<td><?php echo $recorditem->item_name; ?></td>
											<td><?php echo $recorditem->icat_name; ?></td>
											<td><?php echo $recorditem->cc_no.' - '.$recorditem->cc_description; ?></td>
											<td><?php echo $recorditem->uom_name; ?></td>
											<td><?php echo $recorditem->item_is_rentable == 1 ? "Yes" : "No"; ?></td>
											<!--<td><?php //echo date('d-m-Y h:i A',strtotime($recorditem->cc_createdate)); ?></td>-->
											<td><?php if($recorditem->item_status == 1){ ?>
												  <span style="color:green;">Active</span>
											  <?php }elseif($recorditem->item_status == 0){ ?>
												<span style="color:red;">InActive</span>
											  <?php } ?></td>
											<?php 
											if($this->session->userdata('utype')==1 || $templateDetails->pt_i_item<3){?>
												<td>
													<a class="btn btn-outline-warning" onclick="modify_record(<?php echo $recorditem->item_id; ?>);" href="javascript:;<?php //echo base_url().'admincontrol/items/edit_user/'.$recorditem->item_id; ?>" title="Edit Record"><i class="fa fa-edit text-primary"></i></a>
													<?php if($recorditem->item_status == 1){ ?>	
													<a class="btn btn-outline-warning" href="<?php echo base_url().'admincontrol/items/lock_itemset/'.$recorditem->item_id; ?>" title="Lock Record"><i class="fa fa-unlock text-dark"></i></a>
													<?php } else { ?>
													<a class="btn btn-outline-warning" href="<?php echo base_url().'admincontrol/items/unlock_itemset/'.$recorditem->item_id; ?>" title="Unock Record"><i class="fa fa-lock text-dark"></i></a>
													<?php } ?>
													<?php 
													if($this->session->userdata('utype')==1 || $templateDetails->pt_i_item<2){?>
														<a class="btn btn-outline-warning" onclick="return confirm('You are about to delete a record. This cannot be undone. Are you sure?');" href="<?php echo base_url().'admincontrol/items/delete_itemset/'.$recorditem->item_id; ?>" title="Delete Record"><i class="fa fa-trash text-danger"></i></a>
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
					<h5 class="modal-title">Add New Item</h5>
					<button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Item Code:</label>
							<input type="text" class="form-control reset-input" placeholder="Enter Item Code" name="itm_code" id="itm_code" autocomplete="off" />
							<small class="invalid-feedback itm_code"><?php //echo form_error('itm_code'); ?></small>
						</div>
						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Item Name:</label>
					<textarea class="form-control reset-input" placeholder="Enter Item Name" name="itm_name" id="itm_name" autocomplete="off" ></textarea>
<!--							<input type="text" class="form-control" placeholder="Enter Item Name" name="itm_name" id="itm_name" autocomplete="off" />-->
							<small class="invalid-feedback itm_name"><?php //echo form_error('itm_name'); ?></small>
						</div>
<!--						<div class="form-group">-->
<!--							<label for="message-text" class="col-form-label">Item Description:</label>-->
<!--							<textarea class="form-control" placeholder="Enter Item Description" id="itm_desc" name="itm_desc" autocomplete="off"></textarea>-->
<!--							<small class="invalid-feedback itm_desc">--><?php ////echo form_error('itm_desc'); ?><!--</small>-->
<!--						</div>-->
						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Item Category:</label><br/>
							<select class="form-control select22 reset-input" name="itm_category" id="itm_category" autocomplete="off" data-live-search="true">
							<option value="">---Select---</option>
							<?php foreach($category_list as $catitem){ ?>
							<option value="<?php echo $catitem->icat_id; ?>"><?php echo $catitem->icat_name; ?></option>
							<?php } ?>
							</select>
							<small class="invalid-feedback itm_category"><?php //echo form_error('itm_category'); ?></small>
						</div>
						<div class="form-group row">
							<div class="col-sm-6">
								<label for="recipient-name" class="col-form-label">Item CostCode:</label><br/>
								<select class="form-control select33 reset-input" name="itm_costcode" id="itm_costcode" autocomplete="off" data-live-search="true">
								<option value="">---Select---</option>
								<?php foreach($ccode_list as $ccitem){ ?>
								<option value="<?php echo $ccitem->cc_id; ?>"><?php echo $ccitem->cc_no.' - '.$ccitem->cc_description; ?></option>
								<?php } ?>
								</select>
								<small class="invalid-feedback itm_costcode"><?php //echo form_error('itm_costcode'); ?></small>
							</div>
							<div class="col-sm-6">
								<label for="recipient-name" class="col-form-label">Unit of Measure:</label><br/>
								<select class="form-control select22 reset-input" name="sc_uom" id="sc_uom" autocomplete="off" data-live-search="true">
								<option value="">---Select---</option>
								<?php foreach($uom_list as $u_item){ ?>
								<option value="<?php echo $u_item->uom_id; ?>"><?php echo $u_item->uom_name; ?></option>
								<?php } ?>
								</select>
								<small class="invalid-feedback sc_uom"><?php //echo form_error('sc_uom'); ?></small>
							</div>
						</div>
					<div class="form-group">
						<label for="item_is_rentable">
							<span class="tag">Rentable?</span>
							<input type="checkbox" class="area_type" id="item_is_rentable" name="item_is_rentable" value="1">
						</label>
						<small class="invalid-feedback itm_category"><?php //echo form_error('itm_category'); ?></small>
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
					<h5 class="modal-title">Update Item Details</h5>
					<button type="button" class="close close_modal2" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Item Code:</label>
							<input type="hidden" name="update_id_item" id="update_id_item" value="" autocomplete="off" />
							<input type="text" class="form-control" placeholder="Enter Item Code" name="update_itm_code" id="update_itm_code" autocomplete="off" />
							<small class="invalid-feedback update_itm_code"><?php //echo form_error('update_itm_code'); ?></small>
						</div>
						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Item Name:</label>
							<textarea class="form-control" placeholder="Enter Item Name" name="update_itm_name" id="update_itm_name" autocomplete="off" ></textarea>
<!--							<input type="text" class="form-control" placeholder="Enter Item Name" name="update_itm_name" id="update_itm_name" autocomplete="off" />-->
							<small class="invalid-feedback update_itm_name"><?php //echo form_error('update_itm_name'); ?></small>
						</div>
<!--						<div class="form-group">-->
<!--							<label for="message-text" class="col-form-label">Item Description:</label>-->
<!--							<textarea class="form-control" placeholder="Enter Item Description" id="update_itm_desc" name="update_itm_desc" autocomplete="off"></textarea>-->
<!--							<small class="invalid-feedback update_itm_desc">--><?php ////echo form_error('update_itm_desc'); ?><!--</small>-->
<!--						</div>-->
						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Item Category:</label><br/>
							<select class="form-control select22" name="update_itm_category" id="update_itm_category" autocomplete="off" data-live-search="true">
							</select>
							<small class="invalid-feedback update_itm_category"><?php //echo form_error('update_itm_category'); ?></small>
						</div>
						<div class="form-group row">
							<div class="col-sm-6">
								<label for="recipient-name" class="col-form-label">Item CostCode:</label><br/>
								<select class="form-control select33" name="update_itm_costcode" id="update_itm_costcode" autocomplete="off" data-live-search="true">
								</select>
								<small class="invalid-feedback update_itm_costcode"><?php //echo form_error('update_itm_costcode'); ?></small>
							</div>
							<div class="col-sm-6">
								<label for="recipient-name" class="col-form-label">Unit of Measure:</label><br/>
								<select class="form-control select22" name="update_sc_uom" id="update_sc_uom" autocomplete="off" data-live-search="true">
								<option value="">---Select---</option>
								<?php foreach($uom_list as $u_item){ ?>
								<option value="<?php echo $u_item->uom_id; ?>"><?php echo $u_item->uom_name; ?></option>
								<?php } ?>
								</select>
								<small class="invalid-feedback update_sc_uom"><?php //echo form_error('update_sc_uom'); ?></small>
							</div>

						</div>
					<div class="form-group">
						<label for="item_is_rentable">
							<span class="tag">Rentable?</span>
							<input type="checkbox" class="area_type" id="update_itm_is_rentable" name="update_itm_is_rentable" value="1">
						</label>
						<small class="invalid-feedback itm_category"><?php //echo form_error('itm_category'); ?></small>
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

			<div class="modal fade" id="Modal_bulkupload_record" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel3" aria-hidden="true" data-backdrop="static" data-keyboard="false">
				<div class="modal-dialog modal-md" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Bulk upload - Item</h5>
							<button type="button" class="close close_modal3" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label for="recipient-name" class="col-form-label">Upload CSV :</label>
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
		  $('.select22, .select33').selectpicker();
		  $('.alert-error, .invalid-feedback').delay(6000).fadeOut();
	});
		/****************************************
         *       Basic Table                   *
         ****************************************/
        $('#zero_config').DataTable();
		
		
	function goto_add_record(){
		$('.reset-input').val('').change();
		$('#item_is_rentable').prop('checked',false);
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
		
		var itm_code = $('#itm_code').val();
		var itm_name = $('#itm_name').val();
		var itm_category = $('#itm_category option:selected').val();
		var itm_costcode = $('#itm_costcode option:selected').val();
		var sc_uom = $('#sc_uom option:selected').val();
		var itm_desc = $('#itm_name').val();
		var item_is_rentable = 0;
		if($('#item_is_rentable').is(':checked')) {
			item_is_rentable = 1;
		}

		if(itm_code == ""){
			e_error = 1;
			$('.itm_code').html('Item Code is Required.');
		}else{
			if(!itm_code.match(alphanumerics_spaces)){
				e_error = 1;
				$('.itm_code').html('Item Code not use special carecters [without _ . , -], Check again.');
			}else{
				$('.itm_code').html('');
			}	
		}
		if(itm_name == ""){
			e_error = 1;
			$('.itm_name').html('Item Name is Required.');
		}else{
			if(!itm_name.match(alphanumerics_spaces)){
				e_error = 1;
				$('.itm_name').html('Item Name not use special characters [without _ . , -], Check again.');
			}else{
				$('.itm_name').html('');
			}	
		}
		if(itm_category == ""){
			e_error = 1;
			$('.itm_category').html('Item Category is Required.');
		}else{
			if(!itm_category.match(onlynumerics)){
				e_error = 1;
				$('.itm_category').html('Item Category needs only Numeric Value.');
			}else{
				$('.itm_category').html('');
			}
		}
		if(itm_costcode == ""){
			e_error = 1;
			$('.itm_costcode').html('Item CostCode is Required.');
		}else{
			if(!itm_costcode.match(onlynumerics)){
				e_error = 1;
				$('.itm_costcode').html('Item CostCode needs only Numeric Value.');
			}else{
				$('.itm_costcode').html('');
			}
		}
		if(sc_uom == ""){
			e_error = 1;
			$('.sc_uom').html('Unit of Measure is Required.');
		}else{
			if(!sc_uom.match(onlynumerics)){
				e_error = 1;
				$('.sc_uom').html('Unit of Measure needs only Numeric Value.');
			}else{
				$('.sc_uom').html('');
			}
		}
		if(itm_desc == ""){
			e_error = 1;
			$('.itm_desc').html('Item Description is Required.');
		}else{
			if(!itm_desc.match(alphanumerics_no)){
				e_error = 1;
				$('.itm_desc').html('Item Description not use special carecters [without _ & ( @ ) : . , -], Check again.');
			}else{
				$('.itm_desc').html('');
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
			form_data.append("itm_code", itm_code);
			form_data.append("itm_name", itm_name);
			form_data.append("itm_category", itm_category);
			form_data.append("itm_costcode", itm_costcode);
			form_data.append("itm_desc", itm_desc);
			form_data.append("sc_uom", sc_uom);
			form_data.append("item_is_rentable", item_is_rentable);

			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/items/add_new_item_sets') ?>",
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
							window.location.replace("<?php echo site_url('admincontrol/items/item_list') ?>");
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
		// alert(element);
		if(element != ""){
			var form_data = new FormData();
			form_data.append("name_itemid", element);
			
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/items/get_details_of_itemsets') ?>",
				dataType: 'json',
				data: form_data,
				contentType:false,
				cache:true,
				processData:false,
				success:function(data){
					// alert(JSON.stringify(data.msg));
					if(data.msg == 1)
					{
						//console.log(data);
						//alert(data.msg[0].space_rate);
						$('#update_id_item').val(element);
						$('#update_itm_code').val(data.s_msg.item_code);
						$('#update_itm_name').val(data.s_msg.item_name);
						// $('#update_itm_desc').val(data.s_msg.item_description);
						if(data.s_msg.item_is_rentable == "1") {
							$('#update_itm_is_rentable').prop('checked',true);
						} else {
							$('#update_itm_is_rentable').prop('checked',false);
						}

						var catstring = '<option value="">---Select---</option>';
						var ccodestring = '<option value="">---Select---</option>';
						var s_uom = '<option value="">---Select---</option>';
						var cat_setlock = '';
						var cc_setlock = '';
						var uom_setlock = '';
						<?php foreach($category_list as $catitem){ ?>
							var citem = "<?php echo $catitem->icat_id; ?>";
							var citemname = "<?php echo $catitem->icat_name; ?>";
							
							if(parseInt(citem) == parseInt(data.s_msg.item_cat_ms)){
								cat_setlock = parseInt(citem);
								catstring = catstring + '<option value="'+citem+'" selected="selected">'+citemname+'</option>';
							}else{
								catstring = catstring + '<option value="'+citem+'">'+citemname+'</option>';
							}
						<?php } ?>
						$('#update_itm_category').html(catstring);
						<?php foreach($ccode_list as $ccitem){ ?>
							var ccitemset = "<?php echo $ccitem->cc_id; ?>";
							var ccitemnoset = "<?php echo $ccitem->cc_no.' - '.$ccitem->cc_description; ?>";
							if(parseInt(ccitemset) == parseInt(data.s_msg.item_ccode_ms)){
								cc_setlock = parseInt(ccitemset);
								ccodestring = ccodestring + '<option value="'+ccitemset+'" selected="selected">'+ccitemnoset+'</option>';
							}else{
								ccodestring = ccodestring + '<option value="'+ccitemset+'">'+ccitemnoset+'</option>';
							}
						<?php } ?>
						$('#update_itm_costcode').html(ccodestring);
						<?php foreach($uom_list as $u_item){ ?>
							var uuom3 = "<?php echo $u_item->uom_id; ?>";
							var uuomname3 = "<?php echo $u_item->uom_name; ?>";
							if(parseInt(uuom3) == parseInt(data.s_msg.item_unit_ms)){
								uom_setlock = parseInt(uuom3);
								s_uom = s_uom + '<option value="'+uuom3+'" selected="selected">'+uuomname3+'</option>';
							}else{
								s_uom = s_uom + '<option value="'+uuom3+'">'+uuomname3+'</option>';
							}
						<?php } ?>
						$('#update_sc_uom').html(s_uom);
						$('.select22, .select33').selectpicker('refresh');
						$('#update_itm_category').val(cat_setlock);
						$('#update_itm_costcode').val(cc_setlock);
						$('#update_sc_uom').val(uom_setlock);
						$('.select22, .select33').selectpicker('refresh');
						$('#Modal_editrecord').modal('show');
						
					}else{
						$('#update_id_item').val('');
						$('#Modal_editrecord').modal('hide');
					}
					
				}
			});
		}else{
			$('#update_id_item').val('');
			$('#Modal_editrecord').modal('hide');
		}
	}

	function goto_bulkupload_record(){
		$('#Modal_bulkupload_record').modal('show');
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
		
		var update_id_item = $('#update_id_item').val();
		var update_itm_code = $('#update_itm_code').val();
		var update_itm_name = $('#update_itm_name').val();
		var update_itm_category = $('#update_itm_category option:selected').val();
		var update_itm_costcode = $('#update_itm_costcode option:selected').val();
		var update_sc_uom = $('#update_sc_uom option:selected').val();
		var update_itm_desc = $('#update_itm_name').val();
		var update_itm_is_rentable = 0;
		if($('#update_itm_is_rentable').is(':checked')) {
			update_itm_is_rentable = 1;
		}

		if(update_id_item == ""){
			error_message = error_message + "<br/>ID not Found, Refresh the Page and Try again.";
		}
		if(update_itm_code == ""){
			e_error = 1;
			$('.update_itm_code').html('Item Code is Required.');
		}else{
			if(!update_itm_code.match(alphanumerics_spaces)){
				e_error = 1;
				$('.update_itm_code').html('Item Code not use special carecters [without _ . , -], Check again.');
			}else{
				$('.update_itm_code').html('');
			}	
		}
		if(update_itm_name == ""){
			e_error = 1;
			$('.update_itm_name').html('Item Name is Required.');
		}else{
			// if(!update_itm_name.match(alphanumerics_spaces)){
			// 	e_error = 1;
			// 	$('.update_itm_name').html('Item Name not use special carecters [without _ . , -], Check again.');
			// }else{
				$('.update_itm_name').html('');
			// }	
		}
		if(update_itm_category == ""){
			e_error = 1;
			$('.update_itm_category').html('Item Category is Required.');
		}else{
			if(!update_itm_category.match(onlynumerics)){
				e_error = 1;
				$('.update_itm_category').html('Item Category needs only Numeric Value.');
			}else{
				$('.update_itm_category').html('');
			}
		}
		if(update_itm_costcode == ""){
			e_error = 1;
			$('.update_itm_costcode').html('Item CostCode is Required.');
		}else{
			if(!update_itm_costcode.match(onlynumerics)){
				e_error = 1;
				$('.update_itm_costcode').html('Item CostCode needs only Numeric Value.');
			}else{
				$('.update_itm_costcode').html('');
			}
		}
		if(update_itm_desc == ""){
			e_error = 1;
			$('.update_itm_desc').html('Item Description is Required.');
		}else{
			// if(!update_itm_desc.match(alphanumerics_no)){
			// 	e_error = 1;
			// 	$('.update_itm_desc').html('Item Description not use special carecters [without _ & ( @ ) : . , -], Check again.');
			// }else{
				$('.update_itm_desc').html('');
			// }	
		}
		if(update_sc_uom == ""){
			e_error = 1;
			$('.update_sc_uom').html('Unit of Measure is Required.');
		}else{
			if(!update_sc_uom.match(onlynumerics)){
				e_error = 1;
				$('.update_sc_uom').html('Unit of Measure needs only Numeric Value.');
			}else{
				$('.update_sc_uom').html('');
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
			form_data.append("update_id_item", update_id_item);
			form_data.append("update_itm_code", update_itm_code);
			form_data.append("update_itm_name", update_itm_name);
			form_data.append("update_itm_category", update_itm_category);
			form_data.append("update_itm_costcode", update_itm_costcode);
			form_data.append("update_sc_uom", update_sc_uom);
			form_data.append("update_itm_desc", update_itm_desc);
			form_data.append("item_is_rentable", update_itm_is_rentable);

			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/items/modify_item_sets') ?>",
				dataType: 'json',
				data: form_data,
				contentType:false,
				cache:true,
				processData:false,
				success:function(data){
					// alert(data.msg);
					if(data.msg == 1)
					{
						//console.log(data);
						//alert(data.msg[0].space_rate);
						$('.div_roller_total2').fadeOut();
						toastr.success('Record is Updated Successfully!', 'Success');
						setTimeout(function(){ 
							window.location.replace("<?php echo site_url('admincontrol/items/item_list') ?>");
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

	function goto_upload_setof_record() {
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
		var allowedExtensions = /(\.csv|)$/i;

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
			setTimeout(function () {
				$('.invalid-feedback, .get_error_total3').fadeOut();
			}, delay);
		} else {

			var form_data = new FormData();
			form_data.append("update_cat", update_cat);
			form_data.append("files", files[0]);

			$.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/items/bulkitem_upload_section_sets') ?>",
				dataType: 'json',
				data: form_data,
				contentType: false,
				cache: true,
				processData: false,
				success: function (data) {
					//alert(data.msg);
					if (data.msg == 1) {
						//console.log(data);
						//alert(data.msg[0].space_rate);
						// alert(JSON.stringify(data.s_msg));
						$('.div_roller_total3').fadeOut();
						toastr.success('Record is Uploaded Successfully!', 'Success');
						setTimeout(function () {
							window.location.replace("<?php echo site_url('admincontrol/items/item_list') ?>");
						}, 2000);

					}else {
						$('.div_roller_total3').fadeOut();
						$('#upload_bulk_btn').prop('disabled', false);
						$('.close_modal3').show();
						error_message = data.e_msg;
						toastr.error(error_message, 'Error!');
						$('#Modal_bulkupload_record').modal('hide');
						setTimeout(function () {
							window.location.replace("<?php echo site_url('admincontrol/items/item_list') ?>");
						}, 2000);
						//$('.get_error_total').html(error_message);
						//$(".get_error_total").fadeIn();
						//setTimeout(function(){ $('.get_error_total').fadeOut(); }, delay);
					}

				}
			});
		}
	}


</script>
        
