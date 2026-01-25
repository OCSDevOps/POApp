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
                        <h4 class="page-title">Purchase Order - <?php echo $porder_list->porder_no; ?></h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url('admincontrol/dashboard'); ?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Purchase Order</li>
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
                                <div class="card-body">
                                    <div class="row">
										<div class="col-md-12">
											<a href="<?php echo base_url().'admincontrol/porder/print_porder_setpdf/'.$porder_list->porder_id; ?>" target="_blank" class="btn btn-primary float-right mb-2" style="margin-right: 10px;">PRINT</a>
											<table class="table">
												<tbody>
													<tr>
														<td colspan="2" width="50%"><?php echo $company_assets->company_name; ?><br/>
														<?php echo $company_assets->company_address; ?></td>
														<td colspan="2" rowspan="2" align="center"><img src="<?php echo base_url().'upload_file/company/'.$company_assets->company_logo; ?>" style="max-width:400px;" /></td>
													</tr>
													<tr>
														<td colspan="2">
															<strong>Project - </strong><?php echo $porder_list->proj_name; ?><br/>
															<strong>Project Address - </strong><?php echo $porder_list->porder_address; ?><br/>
															<strong>Supplier - </strong><?php echo $porder_list->sup_name; ?><br/>
														</td>
													</tr>
													<tr>
														<td width="25%"><strong>P.O. - </strong><?php echo $porder_list->porder_no; ?></td>
														<td width="50%" colspan="2"></td>
														<td align="right"><strong>Date - </strong><?php echo date("d-M-Y",strtotime($porder_list->porder_createdate)); ?></td>
													</tr>
													<tr>
														<td colspan="4">
															<table class="table table-bordered">
																<thead>
																	<tr>
																		<th>SKU</th>
																		<th>Item</th>
																		<th width="20px">Quantity</th>
																		<th width="150px">Unit price</th>
																		<th width="150px">Sub Total</th>
																		<th width="120px">Tax Amount</th>
																		<th>Total</th>
																	</tr>
																</thead>
																<tbody>
																	<?php $ss_total = $ttax = $finaltotal = 0.00;
																	foreach($item_detailsets as $keys=>$idetails){

																		$ss_total = $ss_total + $idetails->po_detail_subtotal;
																		$ttax = $ttax + $idetails->po_detail_taxamount;
																		$finaltotal = $finaltotal + $idetails->po_detail_total; ?>
																	<tr>
																		<td><?php echo $idetails->po_detail_sku; ?></td>
																		<td><?php echo isset($idetails->item_code) ? $idetails->item_code.' - '.$idetails->item_name : $idetails->po_detail_item; ?></td>
																		<td><?php echo $idetails->po_detail_quantity; ?></td>
																		<td><?php echo $idetails->po_detail_unitprice; ?></td>
																		<td><?php echo $idetails->po_detail_subtotal; ?></td>
																		<td><?php echo $idetails->po_detail_taxamount; ?></td>
																		<td><?php echo $idetails->po_detail_total; ?></td>
																	</tr>
																	<?php } ?>
																	<tr>
																		<td></td>
																		<td></td>
																		<td></td>
																		<td><strong>Total - </strong></td>
																		<td><strong><?php echo number_format((float)$ss_total, 2, '.', ''); ?></strong></td>
																		<td><strong><?php echo number_format((float)$ttax, 2, '.', ''); ?></strong></td>
																		<td><strong><?php echo number_format((float)$finaltotal, 2, '.', ''); ?></strong></td>
																	</tr>
																</tbody>
															</table>
														</td>
													</tr>
													<tr>
													<td colspan="4"><strong>Delivery Note - </strong><?php echo $porder_list->porder_delivery_note; ?></td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
                                </div>
                                <!--<div class="border-top">
                                    <div class="card-body">
                                        <button type="button" onclick="gotoclclickbutton();" class="btn btn-primary">Submit</button>
										&nbsp;<a href="<?= site_url('admincontrol/porder/all_purchase_order_list') ?>" class="btn btn-danger">Back</a>
                                    </div>
                                </div>-->
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
		  //$(".select2").selectpicker();
	});
	
</script>
