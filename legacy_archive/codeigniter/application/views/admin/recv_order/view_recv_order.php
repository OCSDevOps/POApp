<?php $this->load->view('admin/component/header') ?>

<?php $this->load->view('admin/component/menu') ?>


<style>
	.box-body textarea, input, select {
		max-width: 500px;
	}

	.box-body textarea {
		resize: vertical;
	}
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
				<h4 class="page-title">View Receive Order - <?php echo $rorder_list->rorder_slip_no; ?></h4>
				<div class="ml-auto text-right">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a
										href="<?php echo base_url('admincontrol/dashboard'); ?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">View Receive Order</li>
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
					<?php echo form_open_multipart('', 'class="form-horizontal" id="myForm" enctype="multipart/form-data"'); ?>

					<div class="border-bottom">
						<div class="card-body">
							<div class="row">
								<div class="col-md-12">
										<a href="<?php echo base_url() . 'admincontrol/recvorder/print_recv_order_setpdf/' . $receiveID; ?>"
										   target="_blank" class="btn btn-primary float-right mb-2" style="margin-right: 10px;">PRINT</a>
								</div>
							</div>
						</div>
					</div>
					<div class="card-body">
						<?php if (isset($error)) { ?>
							<div class="alert alert-danger alert-error">
								<h4>Error!</h4>
								<?php echo $error; ?>
							</div>
						<?php } ?>

						<!--<h4 class="card-title">Personal Info</h4>-->
						<div class="form-group row">
							<label for="fname" class="col-sm-2 text-right control-label col-form-label">Purchase Order
								No.</label>
							<div class="col-sm-6">
								<select class="form-control select2 custom-select" disabled name="po_id" id="po_id"
										data-live-search="true" autocomplete="off" onchange="goto_check_po_items();">
									<option value="">---Select---</option>
									<?php foreach ($po_list as $p_items) { ?>
										<option value="<?php echo $p_items->porder_id; ?>" <?php echo $p_items->porder_id == $rorder_list->porder_id ? "selected" : ""?>><?php echo $p_items->porder_no; ?></option>
									<?php } ?>
								</select>
								<small class="invalid-feedback po_id"><?php echo form_error('po_id'); ?></small>
							</div>
						</div>
						<div class="form-group row">
							<label for="fname" class="col-sm-2 text-right control-label col-form-label">Packing Slip
								Number</label>
							<div class="col-sm-3">
								<input class="form-control" name="ro_no" disabled value="<?php echo $rorder_list->rorder_slip_no?>" id="ro_no" placeholder="Enter Receive No."
									   autocomplete="off"/>
								<small class="invalid-feedback ro_no"><?php echo form_error('ro_no'); ?></small>
							</div>
							<label for="cono1" class="col-sm-3 text-right control-label col-form-label">Receive Order
								Date</label>
							<div class="col-sm-3">
								<input class="form-control" type="date" disabled name="ro_date" id="ro_date" value="<?php echo date('Y-m-d',strtotime($rorder_list->rorder_date)) ?>" placeholder="dd-mm-yyyy"
									   autocomplete="off"/>
								<small class="invalid-feedback ro_date"><?php echo form_error('ro_date'); ?></small>
							</div>
						</div>
						<div class="form-group row">
							<label for="cono1" class="col-sm-2 text-right control-label col-form-label">Receipt
								No</label>
							<div class="col-sm-3">
								<input class="form-control" disabled name="ro_receipt_no" value="<?php echo $rorder_list->rorder_receipt_no?>" type="text" id="ro_receipt_no"
									   placeholder="Receipt No" autocomplete="off"/>
								<small class="invalid-feedback ro_receipt_no"><?php echo form_error('ro_receipt_no'); ?></small>
							</div>
							<?php if($rorder_list->rorder_file) { ?>
							<label for="fname"
								   class="col-sm-3 text-right control-label col-form-label">Attachment</label>
							<div class="col-sm-3">
<!--								<input class="form-control" type="file" name="ro_file" id="ro_file" autocomplete="off"/>-->
							<label><a href="<?php echo base_url().'upload_file/'.$rorder_list->rorder_file?>" target="_blank"><?php echo $rorder_list->rorder_file ?></a></label>
							</div>
						<?php } ?>
						</div>

						<!--<div class="form-group row">
                                        <label for="cono1" class="col-sm-2 text-right control-label col-form-label">Delivery Note</label>
                                        <div class="col-sm-6">
                                            <textarea class="form-control" name="po_dl_note" id="po_dl_note" placeholder="Enter Delivery Note" autocomplete="off"></textarea>
											<small class="invalid-feedback po_dl_note"><?php //echo form_error('po_dl_note'); ?></small>
                                        </div>
                                    </div>-->
						<div class="form-group row justify-content-md-center collectitems">
							<div class="col-sm-10 table-responsive"><input type="hidden" name="totalitem_po" id="totalitem_po" value="1">
								<table class="table table-bordered">
									<thead>
									<tr>
										<th>Item Name</th>
										<th>Item Description</th>
										<th>Purchased Quantity</th>
										<th>Previously Received Quantity</th>
										<th>Balance Quantity</th>
										<th>Receive Quantity</th>
									</tr>
									</thead>
									<tbody>
									<?php
									$slipNoArray = explode('-', $rorder_list->rorder_receipt_no);
									foreach ($item_detailsets as $keys => $idetails) { ?>
										<tr>
											<td><?php echo $idetails->item_name; ?></td>
											<td><?php echo $idetails->ro_detail_item; ?></td>
											<td><?php echo $idetails->ro_detail_total; ?></td>
											<td><?php if($slipNoArray[2]==1){echo '0';}else{echo $this->admin_m->getSUMOfReceiveOrderItem($rorder_list->rorder_porder_ms, $idetails->ro_detail_item, $idetails->ro_detail_id)->previous_purchase; }?></td>
											<td><?php echo $idetails->ro_detail_remaining; ?></td>
											<td><?php echo $idetails->ro_detail_quantity; ?></td>
										</tr>
									<?php } ?>
									</tbody>
								</table>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-sm-12 text-center">
								<div align="center">
									<div class="get_error_total" align="center"
										 style="background-color: #bf0000;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
									<div class="get_success_total" align="center"
										 style="background-color: #174b10;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
									<div class="div_roller_total" align="center" style="display: none;"><img
												src="<?php echo base_url(); ?>style/images/ajax_loader.gif"
												style="max-width: 60px;"/></div>
								</div>
							</div>
						</div>
					</div>
					<div class="border-top">
						<div class="card-body">

							&nbsp;<a href="<?= site_url('admincontrol/recvorder/all_receive_order_list') ?>"
									 class="btn btn-danger">Back</a>
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
		$(function () {
			$('.alert-error, .invalid-feedback').delay(8000).fadeOut();
			//$(".select2").selectpicker();
		});

	</script>
