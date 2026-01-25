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
                        <h4 class="page-title">Purchase Order List</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url('admincontrol/dashboard'); ?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Purchase Order List</li>
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
                                <a href="<?php echo base_url().'admincontrol/porder/add_new_purchase_order'; ?>" class="btn btn-primary float-right mb-2" style="margin-right: 10px;">Add New Purchase Order</a>
                                <div class="table-responsive">
                                    <table id="zero_config" class="table table-striped table-bordered">
                                        <thead>
                                            <tr style="font-weight: bold;">
                                                <th>Sl No.</th>
                                                <th>Purchase Order Number</th>
                                                <th>Project</th>
                                                <th>Supplier</th>
                                                <th>Total Item</th>
                                                <th>Status</th>
												<th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
										<?php foreach($getrecord_list as $keys=>$recorditem)
										{ ?>
										<tr>
											<td><?php echo $keys+1; ?></td>
											<td><?php echo $recorditem->porder_no; ?></td>
											<td><?php echo $recorditem->proj_name; ?></td>
											<td><?php echo $recorditem->sup_name; ?></td>
											<td><?php echo $recorditem->porder_total_item; ?></td>
											<!--<td><?php //echo date('d-m-Y h:i A',strtotime($recorditem->cc_createdate)); ?></td>-->
											<td><?php if($recorditem->porder_status == 1){ ?>
												  <span style="color:green;">Active</span>
											  <?php }elseif($recorditem->porder_status == 0){ ?>
												<span style="color:red;">InActive</span>
											  <?php } ?></td>
											<td>
												<a class="btn btn-outline-warning" href="<?php echo base_url().'admincontrol/porder/view_porder_sets/'.$recorditem->porder_id; ?>" title="View Record"><i class="fa fa-eye text-primary"></i></a>
												<a class="btn btn-outline-warning" href="<?php echo base_url().'admincontrol/porder/modify_porder_sets/'.$recorditem->porder_id; ?>" title="Edit Record"><i class="fa fa-edit text-primary"></i></a>
												<?php if($recorditem->porder_status == 1){ ?>	
												<a class="btn btn-outline-warning" href="<?php echo base_url().'admincontrol/porder/lock_porder_set/'.$recorditem->porder_id; ?>" title="Lock Record"><i class="fa fa-unlock text-dark"></i></a>
												<?php } else { ?>
												<a class="btn btn-outline-warning" href="<?php echo base_url().'admincontrol/porder/unlock_porder_set/'.$recorditem->porder_id; ?>" title="Unock Record"><i class="fa fa-lock text-dark"></i></a>
												<?php } ?>
												<!--<a onclick="return confirm('You are about to delete a record. This cannot be undone. Are you sure?');" href="<?php //echo base_url().'admincontrol/porder/delete_user/'.$recorditem->porder_id; ?>" title="Delete Record"><i class="fa fa-trash text-danger"></i></a>-->
												
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
					<h5 class="modal-title">Add New Supplier Catalog</h5>
					<button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
						<div class="form-group">
							<label for="recipient-name" class="col-form-label">SKU Code:</label>
							<input type="text" class="form-control" placeholder="Enter SKU Code" name="sc_sku_code" id="sc_sku_code" autocomplete="off" />
							<small class="invalid-feedback sc_sku_code"><?php //echo form_error('sc_sku_code'); ?></small>
						</div>
						<div class="form-group row">
							<div class="col-sm-6">
								<label for="recipient-name" class="col-form-label">Price:</label>
								<input type="text" class="form-control" placeholder="Enter Price" name="sc_price" id="sc_price" autocomplete="off" />
								<small class="invalid-feedback sc_price"><?php //echo form_error('sc_price'); ?></small>
							</div>
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
					<h5 class="modal-title">Modify Supplier Catalog Details</h5>
					<button type="button" class="close close_modal2" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
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
		  //$('.select22, .select33').selectpicker();
		  $('.alert-error, .invalid-feedback').delay(6000).fadeOut();
	});
		/****************************************
         *       Basic Table                   *
         ****************************************/
        $('#zero_config').DataTable();
	
</script>
        
