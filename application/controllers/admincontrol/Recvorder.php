<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Recvorder extends Admin_Controller
{

	public function __construct()
	{
		parent::__construct();
		//date_default_timezone_set("Asia/Kolkata");
		$this->data["u_details"] = $this->admin_m->GetDetailsofUsers($this->session->userdata['uid']);
		$this->data['templateDetails'] = $this->db->get_where('permission_master',['pt_id'=>$this->session->userdata('pt_id'),'status'=>1])->row();
	}

	public function index()
	{
		redirect('admincontrol/recvorder/all_receive_order_list');
	}

	public function all_receive_order_list()
	{
		$project = $this->input->get('project');
		$supplier = $this->input->get('supplier');
		$purchase_order = $this->input->get('purchase_order');

		$filterClauses = [];

		if($project != null) {
			$filterClauses = $filterClauses + array('porder_project_ms'=>$project);
		}
		if($supplier != null) {
			$filterClauses = $filterClauses + array('porder_supplier_ms'=>$supplier);
		}
		if($purchase_order != null) {
			$filterClauses = $filterClauses + array('rorder_porder_ms'=>$purchase_order);
		}
		$this->data['filters'] = $filterClauses;
		$this->data['projects'] = $this->admin_m->get_All_Project();
		$this->data['suppliers'] = $this->admin_m->get_All_Suppliers();
		$this->data['purchase_order'] = $this->admin_m->get_All_PurchaseOrder();
		$this->data['getrecord_list'] = $this->admin_m->get_All_Receive_order_list_fromDB(null, $filterClauses);
		$this->load->view('admin/recv_order/recv_order_list_view', $this->data);
	}

	public function add_new_receive_order()
	{

		if($this->session->userdata('utype')==1 || $this->data['templateDetails']->pt_t_rcorder<3){
			$this->data['po_list'] = $this->admin_m->getPurchaseOrderForReceiveOrder();
			$this->load->view('admin/recv_order/add_recv_order', $this->data);
		} else {
			redirect('default404');
		}
	}

	public function get_all_items_find_for_receive()
	{
		if ($_POST) {
			$po_id = $this->input->post("po_id");

			$this->form_validation->set_rules('po_id', 'Purchase Order', 'trim|required');

			if ($this->form_validation->run() == TRUE) {
				$getresult = $this->admin_m->getDetails_ItemList_POrder_from_DB($po_id);

				if (count((array)$getresult) > 0) {
					$mixstring = '';
					$keyset = 0;
					foreach ($getresult as $key=>$po_items) {
						$item_name = ($po_items->po_detail_item != null) ? $po_items->po_detail_item : $po_items->po_detail_description;
						$get_receive_items = $this->admin_m->getDetails_ItemList_for_Receive_Order_from_DB($po_id, $item_name);

						// $get_receive_items = $this->admin_m->getDetails_ItemList_forRecv_Order_from_DB($po_id);
						// $previousReceivedQty=$this->admin_m->getSUMOfReceiveOrderItem($po_id, $po_items->po_detail_item, $po_items->po_detail_id)->previous_purchase;
						// if(empty($previousReceivedQty)){
							$previousReceivedQty=0;
						// }
						if (count((array)$get_receive_items) > 0) {
							$exact_balance = 0;

//								$get_receive_latest_item = $this->admin_m->getLatestDetails_ItemList_for_Receive_Order_from_DB($get_receive_items->rorder_id, $item_name);
								$get_receive_items = $this->admin_m->getDetailsOfReceiveOrder($get_receive_items->rorder_id);
								$get_latest_receive_items = $this->admin_m->getSingleRecordOfReceiveOrder($get_receive_items[$key]->ro_detail_id);
								$previousReceivedQty = $this->admin_m->getSUMOfReceiveOrderItemAdd($po_id, $get_receive_items[$key]->ro_detail_item, $get_receive_items[$key]->ro_detail_id)->previous_purchase;
								if($po_items->po_detail_item == $get_latest_receive_items->ro_detail_item) {
									$mixstring = $mixstring . '
									<tr>
									<td>' . $po_items->item_name . '</td>
									<td>' . $po_items->po_detail_description . '</td>
									<td><input type="hidden" name="itemset_code[]" id="itemset_code_' . $keyset . '" value="' . (isset($po_items->po_detail_item) && $po_items->po_detail_item != null ? $po_items->po_detail_item : $po_items->po_detail_description) . '" /><input class="form-control" type="text" name="po_totalqty[]" id="po_totalqty_' . $keyset . '" value="' . $po_items->po_detail_quantity . '" readonly /></td>
									<td><input class="form-control" data-id="' . $keyset . '" type="number" name="po_previousrecqty[]" id="po_previousrecqty_' . $keyset . '" value="'.$previousReceivedQty. '" readonly /></td>
									<td><input class="form-control" data-id="' . $keyset . '" type="number" name="po_balanceqty[]" id="po_balanceqty_' . $keyset . '" value="' . $get_latest_receive_items->ro_detail_remaining . '" readonly /></td>
									<td><input class="form-control po_recv_qty_' . $keyset . '" type="number" max="' . $get_latest_receive_items->ro_detail_remaining . '" min="0" data-id="' . $keyset . '" onchange="javascript:validateReceiveQty(this)" data-po-id="'.$po_items->po_detail_id.'" name="po_recv_qty[]" id="po_recv_qty_' . $keyset . '" placeholder="Enter Receive Quantity" value="0" /><small class="invalid-feedback po_recv_qty_' . $keyset . '"></small></td>
									</tr>';
									$keyset++;
								}
						} else {
							$mixstring = $mixstring . '
							<tr>
							<td>' . $po_items->item_name . '</td>
							<td>' . $po_items->po_detail_description . '</td>
							<td><input type="hidden" name="itemset_code[]" id="itemset_code_' . $keyset . '" value="' . (isset($po_items->po_detail_item) && $po_items->po_detail_item != null ? $po_items->po_detail_item : $po_items->po_detail_description) . '" /><input class="form-control" type="text" name="po_totalqty[]" id="po_totalqty_' . $keyset . '" value="' . $po_items->po_detail_quantity . '" readonly /></td>
							<td><input class="form-control" data-id="' . $keyset . '" type="number" name="po_previousrecqty[]" id="po_previousrecqty_' . $keyset . '" value="'.$previousReceivedQty. '" readonly /></td>
							<td><input class="form-control" data-id="'.$keyset.'" type="number" name="po_balanceqty[]" id="po_balanceqty_' . $keyset . '" value="' . $po_items->po_detail_quantity . '" readonly /></td>
							<td><input class="form-control po_recv_qty_' . $keyset . '" type="number" name="po_recv_qty[]" id="po_recv_qty_' . $keyset . '" max="'.$po_items->po_detail_quantity.'" min="0" data-id="'.$keyset.'" onchange="javascript:validateReceiveQty(this)" data-po-id="'.$po_items->po_detail_id.'" placeholder="Enter Receive Quantity" value="0" /><small class="invalid-feedback po_recv_qty_' . $keyset . '"></small></td>
							</tr>
							';
							$keyset++;
						}
					}
					if ($keyset == 0) {
						$mixstring = '<div class="col-sm-10 table-responsive">
							<table class="table table-bordered">
								<thead>	
									<tr>
										<th>Item Name</th>
										<th>Item Description</th>
										<th>Purchase Quantity</th>
										<th>Previously Reveived Quantity</th>
										<th>Balance Quantity</th>
										<th>Receive Quantity</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td colspan="5">No Item Found to Receive in this Purchase Order.</td>
									</tr>
								</tbody>
							</table>
						</div>';
					} else {
						$mixstring = '
						<div class="col-sm-10 table-responsive">
							<input type="hidden" name="totalitem_po[]" id="totalitem_po" value="' . $keyset . '" />
							<table class="table table-bordered">
								<thead>
									<tr>
										<th>Item Name</th>
										<th>Item Description</th>
										<th>Purchase Quantity</th>
										<th>Previously Reveived Quantity</th>
										<th>Balance Quantity</th>
										<th>Receive Quantity</th>
									</tr>
								</thead>
								<tbody>
									' . $mixstring . '
								</tbody>
							</table>
						</div>';
					}

					$this->db->select('*');
					$this->db->from('purchase_order_master');
					$this->db->where('porder_id', $po_id);
					$po_record = $this->db->get()->row();

					$this->db->select('*');
					$this->db->from('receive_order_master');
					$this->db->order_by('rorder_id', 'DESC')->limit(1);
					$this->db->where('SUBSTRING(rorder_receipt_no, 1,9) = ',$po_record->porder_no)->limit(1);
					$ro_record = $this->db->get()->row();
					if($ro_record) {
						$rnumberArray = explode('-',$ro_record->rorder_receipt_no);
						$receipt_no = $po_record->porder_no . "-" . (isset($ro_record) && !is_null($ro_record) ? ($rnumberArray[2] + 1) : "1");
					} else {
						$receipt_no = $po_record->porder_no . "-" . (isset($ro_record) && !is_null($ro_record) ? ( $ro_record->rorder_id + 1) : "1");

					}


					echo json_encode(array('msg' => 1, 's_msg' => $mixstring, 'receipt_no' => $receipt_no));
				} else {
					echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to get the proper Data, Try Again.'));
				}

			} else {
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		} else {
			redirect('default404');
		}

	}

	public function new_recv_order_Set_submission()
	{
		if ($_POST) {

			$po_id = $this->input->post("po_id");
			$ro_no = $this->input->post("ro_no");
			$ro_date = $this->input->post("ro_date");
			$ro_receipt_no = $this->input->post("ro_receipt_no");
			$ro_file = $this->input->post("ro_file");
			$totalitem_po = $this->input->post("totalitem_po");

			$itmcode = $this->input->post("itmcode");
			$balance_qty = $this->input->post("balance_qty");
			$recv_qty = $this->input->post("recv_qty");
			$total_po_qty = $this->admin_m->get_TotalQtyofPOByPoID($po_id);
			$this->form_validation->set_rules('po_id', 'Purchase Order ID', 'trim|required|is_natural_no_zero');

//			$this->form_validation->set_rules('ro_no', 'Receive Order No.', 'trim|required');
//			$this->form_validation->set_rules('ro_date', 'Receive Order Date', 'trim|required');
//			$this->form_validation->set_rules('totalitem_po', 'Total PO Item', 'trim|required|is_natural_no_zero');


			if ($this->form_validation->run() == TRUE) {
				//echo "1st";
				if ($this->admin_m->check_recv_order_set_nos_exist(trim($ro_no)) == TRUE) {
					//date_default_timezone_set("Asia/Kolkata");
					$config['upload_path'] = './upload_file/';
					$config['allowed_types'] = 'gif|jpg|png|jpeg|pdf|doc|docx';
					$config['max_size'] = 1000;
					$config['max_width'] = 1024;
					$config['max_height'] = 768;

					$this->load->library('upload', $config);

					if (!$this->upload->do_upload('ro_file')) {
						$error = array('error' => $this->upload->display_errors());
					} else {
						$file_data = array('image_metadata' => $this->upload->data());
					}

					$row = array(
						'rorder_porder_ms' => $po_id,
						'rorder_date' => date('Y-m-d', strtotime($ro_date)),
						'rorder_totalitem' => $totalitem_po,
						'rorder_receipt_no' => $ro_receipt_no,
						'rorder_slip_no' => $ro_no,
						'rorder_file' => isset($file_data['image_metadata']['file_name']) ? $file_data['image_metadata']['file_name'] : "",
						'rorder_createdate' => date('Y-m-d H:i:s'),
						'rorder_createby' => $this->session->userdata['uid']
					);

					$rowids = $this->admin_m->common_Insertion_in_DB_with_ID($row, 'receive_order_master');

					if ($rowids != FALSE) {
						$detail_counter = 0;
						for ($jk = 0; $jk < $totalitem_po; $jk++) {
							if (json_decode($itmcode[0])[$jk] == "" || json_decode($itmcode[0])[$jk] == NULL || json_decode($balance_qty[0])[$jk] == NULL || json_decode($balance_qty[0])[$jk] == "" || json_decode($recv_qty[0])[$jk] == NULL || json_decode($recv_qty[0])[$jk] == "") {
								$detail_counter++;
							}
						}
						$completed = false;
						$receive_qty = 0;
						$counter = 0;
						if ($detail_counter == 0) {

							for ($jk = 0; $jk < $totalitem_po; $jk++) {

								$get_po_items = $this->admin_m->getDetailItemsofPOForReceiveOrder($po_id);
								$get_po_single_items = $this->admin_m->getSingleItemDetailPOForReceiveOrder($get_po_items[$jk]->po_detail_id);

								$get_latest_receive_items = $this->admin_m->getMINOfReceiveOrderItem($po_id, $get_po_single_items->po_detail_item);

								if(isset($get_latest_receive_items->remaining) && $get_latest_receive_items->remaining > 0) {
									// if(json_decode($recv_qty[0])[$jk]==0){
									// 	$remaining=$get_latest_receive_items->remaining;
									// }else{
										$remaining = $get_latest_receive_items->remaining - json_decode($recv_qty[0])[$jk];
									// }
								}else if(isset($get_latest_receive_items->remaining) && $get_latest_receive_items->remaining == 0) {
									$remaining = 0;
								}else {
									// if(json_decode($recv_qty[0])[$jk]==0){
									// 	$remaining=$get_po_single_items->po_detail_quantity;
									// }else{
										$remaining = $get_po_single_items->po_detail_quantity - json_decode($recv_qty[0])[$jk];
									// }
								}

								$row2 = array(
									'ro_detail_rorder_ms' => $rowids,
									'ro_detail_item' => json_decode($itmcode[0])[$jk],
									'ro_detail_quantity' => json_decode($recv_qty[0])[$jk],
									'ro_detail_total' => $get_po_single_items->po_detail_quantity,
									'ro_detail_remaining' => $remaining,
									'ro_detail_createdate' => date('Y-m-d H:i:s')
								);

								$receive_qty += json_decode($recv_qty[0])[$jk];

								if ($this->admin_m->common_Insertion_in_DB($row2, 'receive_order_details') == FALSE) {
									$detail_counter++;
								}

								if($remaining == 0) {
									$completed = true;
								} else {
									$counter++;
									$completed = false;

								}
							}
						}

						if($counter==0) {
							$row = array(
								'porder_delivery_status' => 1,
							);
							$this->admin_m->common_Updation_in_DB($row,'purchase_order_master','porder_id',$po_id);

						} else {
							$row = array(
								'porder_delivery_status' => 2,
							);
							$this->admin_m->common_Updation_in_DB($row,'purchase_order_master','porder_id',$po_id);
						}

						if ($detail_counter == 0) {

							if($this->admin_m->get_Notification_SettingByKey('is_receive_order')) {

								$template = $this->admin_m->GetEmailTemplateWithID($this->admin_m->get_Notification_SettingByKey('receive_order_template'));
								$setting = $this->admin_m->get_CompanySMTP_Setting();
								$attData['receive_order'] = $receive_order = $this->admin_m->get_All_Receive_order_list_fromDB($rowids);
								$attData['item_detailsets'] = $this->admin_m->getDetails_ItemList_forRecv_Order_from_DB($rowids);
								$attData['company'] = $this->admin_m->getCompanySetting();
								$supplier = $this->admin_m->GetDetailsofSupplier($receive_order->porder_supplier_ms);
								$project_details = $this->admin_m->getPurchaseOrderByID($po_id);
								$project_details = $this->admin_m->getProjectDetails($project_details->porder_project_ms);
								if ($supplier->sup_email != null) {

									$toEmail = [
										[
											'email' => $supplier->sup_email,
											'name' => $supplier->sup_name,
										]
									];

									if(count($project_details) > 0) {
										foreach($project_details as $key => $value) {
											$accountant = $this->admin_m->GetDetailsofUsers($value->pdetail_accountant);
											$coordinator = $this->admin_m->GetDetailsofUsers($value->pdetail_coordinator);
											$manager = $this->admin_m->GetDetailsofUsers($value->pdetail_manager);
											$site_coordinator = $this->admin_m->GetDetailsofUsers($value->pdetail_site_coordinator);
											$supervisor = $this->admin_m->GetDetailsofUsers($value->pdetail_supervisor);

											if(isset($accountant)) {
												$toEmail[] = [
													'name' => $accountant->username,
													'email' => $accountant->email
												];
											}

											if($coordinator) {
												$toEmail[] = [
													'name' => $coordinator->username,
													'email' => $coordinator->email
												];
											}

											if(isset($manager)) {
												$toEmail[] = [
													'name' => $manager->username,
													'email' => $manager->email
												];
											}

											if(isset($site_coordinator)) {
												$toEmail[] = [
													'name' => $site_coordinator->username,
													'email' => $site_coordinator->email
												];
											}

											if(isset($supervisor)) {
												$toEmail[] = [
													'name' => $supervisor->username,
													'email' => $supervisor->email
												];
											}
										}
									}

									if($template->email_cc != null) {
										$cc = [
											$template->email_cc
										];
									} else {
										$cc = [];
									}

									$params = [
										"#PorderNo#" => $receive_order->porder_no,
										"#ReceiptNo#" => $receive_order->rorder_receipt_no,
										"#PackingSlipNo#" => $receive_order->rorder_slip_no,
										"#Status#" => $receive_order->rorder_status
									];

									$subjectParams = [
										"#PackingSlipNo#" => $receive_order->rorder_slip_no
									];

									$data = $this->admin_m->prepareEmailBody($template->email_key, $params, $subjectParams);

									$attData['supplier'] = $supplier;

									$attachment = $this->admin_m->makePOPdf('receive_order', $attData,$receive_order->rorder_receipt_no);
									$custom_attachment = isset($file_data['image_metadata']['full_path']) ? $file_data['image_metadata']['full_path'] : null;

									$this->sendSMTPEmail($toEmail, $data['subject'], $data['content'], $cc, $setting, $attachment, null, $custom_attachment);
								}
							}
							echo json_encode(array('msg' => 1, 's_msg' => ''));

						} else {
							$this->db->delete('receive_order_master', array('rorder_id' => $rowids));
							$this->db->delete('receive_order_details', array('ro_detail_rorder_ms' => $rowids));
							echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Insert Details Table Data, Try Again.'));
						}
					} else {
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Insert Data, Try Again.'));
					}
				} else {
					echo json_encode(array('msg' => 0, 'e_msg' => 'Receive Order No. already Exist, please check it.'));
				}
			} else {
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		} else {
			redirect('default404');
		}
	}

	public function delete_itemset($id)
	{

		if($this->session->userdata('utype')==1 || $this->data['templateDetails']->pt_t_rcorder<3){
			$resultrow = $this->db->get_where('receive_order_master', array('rorder_id' => $id))->row();
			if ($resultrow) {
				if($this->db->delete('receive_order_details', array('ro_detail_rorder_ms' => $id))) {
					$this->db->delete('receive_order_master', array('rorder_id' => $id));
					$hasReceiveOrders = $this->db->get_where('receive_order_master',['rorder_porder_ms'=>$resultrow->rorder_porder_ms])->num_rows();
					if($hasReceiveOrders == 0){
						$this->db->where('porder_id',$resultrow->rorder_porder_ms);
						$this->db->update('purchase_order_master',['porder_delivery_status'=>0]);
					}else{
						$this->db->where('porder_id',$resultrow->rorder_porder_ms);
						$this->db->update('purchase_order_master',['porder_delivery_status'=>2]);
					}
					$this->session->set_flashdata("success", "Record Deleted successfully");
					return redirect('admincontrol/recvorder/all_receive_order_list');
				}
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
				return redirect('admincontrol/recvorder/all_receive_order_list');
			} else {
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
				return redirect('admincontrol/recvorder/all_receive_order_list');
			}
		} else {
			redirect('default404');
		}
	}

	public function get_address_from_porject_find111111111()
	{
		if ($_POST) {
			$po_project = $this->input->post("po_project");

			$this->form_validation->set_rules('po_project', 'Project', 'trim|required');

			if ($this->form_validation->run() == TRUE) {

				$getresult = $this->db->get_where('project_master', array('proj_id' => $po_project, 'proj_status' => 1))->row();
				if (count((array)$getresult) > 0) {
					echo json_encode(array('msg' => 1, 's_msg' => $getresult));
				} else {
					echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Update Details Data, Try Again.'));
				}

			} else {
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		} else {
			redirect('default404');
		}
	}

	public function get_packageitems_from_package_find111111111()
	{
		if ($_POST) {
			$itmpk_id = $this->input->post("itmpk_id");

			$this->form_validation->set_rules('itmpk_id', 'Package ID', 'trim|required');

			if ($this->form_validation->run() == TRUE) {

				$getresult = $this->admin_m->getDetails_Detail_ItemList_from_DB($itmpk_id);
				if (count((array)$getresult) > 0) {
					echo json_encode(array('msg' => 1, 's_msg' => $getresult));
				} else {
					echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Retrieve Data, Try Again.'));
				}

			} else {
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		} else {
			redirect('default404');
		}
	}

	public function add_multiple_items_from_package_sets111111111()
	{
		if ($_POST) {
			$ipack_itm_no = $this->input->post("ipack_itm_no");
			$totalitem_pkg = $this->input->post("totalitem_pkg");
			$supp_set = $this->input->post("supp_set");
			$itmcode = $this->input->post("itmcode");
			$itmqty = $this->input->post("itmqty");
			$itmprice = $this->input->post("itmprice");

			$this->form_validation->set_rules('totalitem_pkg', 'Item Count', 'trim|required|is_natural_no_zero');
			$this->form_validation->set_rules('supp_set', 'Supplier', 'trim|required');
			$this->form_validation->set_rules('ipack_itm_no', 'AUTOGEN ID', 'trim|required');

			if ($this->form_validation->run() == TRUE) {

				$pk_item_set = 0;
				$mixset_string = '';
				for ($jk = 0; $jk < $totalitem_pkg; $jk++) {

					if ($itmcode[$jk] == "" || $itmcode[$jk] == NULL || $itmqty[$jk] == NULL || $itmqty[$jk] == "" || $itmprice[$jk] == NULL || $itmprice[$jk] == "") {
						$pk_item_set++;
					}
					if ($pk_item_set == 0) {

						if ($this->admin_m->check_Existing_Item_asper_POrder_inDB($itmcode[$jk], $ipack_itm_no) == TRUE) {

							$getresult = $this->admin_m->GetSupplier_Item_cataLog_search($supp_set, $itmcode[$jk]);
							if (count((array)$getresult) > 0) {
								$getresult1 = $this->admin_m->GetCCode_from_ItemCode_serch($itmcode[$jk]);
								if (count((array)$getresult1) > 0) {
									$pk_subtotal = $itmqty[$jk] * $itmprice[$jk];
									$pk_total_amt = $pk_subtotal;
									$row_arr = array(
										'po_detail_autogen' => $ipack_itm_no,
										'po_detail_item' => $itmcode[$jk],
										'po_detail_sku' => $getresult->supcat_sku_no,
										'po_detail_quantity' => $itmqty[$jk],
										'po_detail_unitprice' => $itmprice[$jk],
										'po_detail_subtotal' => $pk_subtotal,
										'po_detail_total' => $pk_total_amt,
										'po_detail_createdate' => date('Y-m-d H:i:s')
									);

									$resultset = $this->admin_m->addupdate_temp_Porder_Item_inDB($row_arr);
									if ($resultset != FALSE) {
										$resultbunch = $this->admin_m->getDetails_Porder_Item_from_DB($resultset);
										//echo json_encode(array('msg' => 1, 'cat_set' => $resultbunch));
										$mixset_string = $mixset_string . '<tr class="expset_' . $resultbunch->po_detail_id . '"><td>' . $resultbunch->po_detail_item . '</td><td>' . $resultbunch->item_name . '</td><td>' . $resultbunch->cc_no . '</td><td>' . $resultbunch->po_detail_sku . '</td><td>' . $resultbunch->uom_name . '</td><td>' . $resultbunch->po_detail_quantity . '</td><td>' . $resultbunch->po_detail_unitprice . '</td><td>' . $resultbunch->po_detail_subtotal . '</td><td>' . $resultbunch->po_detail_taxamount . '</td><td>' . $resultbunch->po_detail_total . '</td><td><a href="javascript:;" onclick="gotodelete_items(' . $resultbunch->po_detail_id . ');"><i class="fa fa-trash text-danger"></i></a></td></tr>';
									}
								}
							}

						}
					}
				}
				if ($pk_item_set == 0) {
					$selectitems = $this->admin_m->get_All_POrder_Items($ipack_itm_no);
					$total_itemamt = $this->admin_m->get_All_POrder_Items_TotalAmount($ipack_itm_no);
					echo json_encode(array('msg' => 1, 's_msg' => $mixset_string, 'titem' => $selectitems, 'tamount' => $total_itemamt->pdtotal));
				} else {
					echo json_encode(array('msg' => 0, 'e_msg' => 'Qualification section Problem Occured, Check Again.'));
				}


			} else {
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		} else {
			redirect('default404');
		}
	}

	public function get_alldetails_from_item_find111111111()
	{
		if ($_POST) {
			$pk_item = $this->input->post("pk_item");
			$supp_set = $this->input->post("supp_set");

			$this->form_validation->set_rules('pk_item', 'Item', 'trim|required');
			$this->form_validation->set_rules('supp_set', 'Supplier', 'trim|required');

			if ($this->form_validation->run() == TRUE) {

				$getresult = $this->admin_m->GetSupplier_Item_cataLog_search($supp_set, $pk_item);
				if (count((array)$getresult) > 0) {
					$getresult1 = $this->admin_m->GetCCode_from_ItemCode_serch($pk_item);
					if (count((array)$getresult1) > 0) {
						echo json_encode(array('msg' => 1, 's_msg' => $getresult1, 'supp_set' => $getresult));
					} else {
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Update Details Data, Try Again.'));
					}
				} else {
					echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to collect Supplier Catalog Data, Try Again.'));
				}

			} else {
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		} else {
			redirect('default404');
		}
	}

	public function new_porder_item_submission111111111()
	{
		if ($_POST) {
			$ipack_itm_no = $this->input->post("ipack_itm_no");
			$pk_code = $this->input->post("pk_code");
			$pk_item = $this->input->post("pk_item");
			$pk_ccode = $this->input->post("pk_ccode");
			$pk_sku = $this->input->post("pk_sku");
			$pk_uom = $this->input->post("pk_uom");
			$pk_itm_qnty = $this->input->post("pk_itm_qnty");
			$pk_itm_price = $this->input->post("pk_itm_price");
			$pk_subtotal = $this->input->post("pk_subtotal");
			$pk_tax_amt = $this->input->post("pk_tax_amt");
			$pk_total_amt = $this->input->post("pk_total_amt");

			$this->form_validation->set_rules('pk_code', 'Item Code', 'trim|required|matches[pk_item]');
			$this->form_validation->set_rules('pk_item', 'Item Name', 'trim|required');
			$this->form_validation->set_rules('pk_ccode', 'CostCode', 'trim|required');
			$this->form_validation->set_rules('pk_sku', 'SKU', 'trim|required');
			$this->form_validation->set_rules('pk_uom', 'UOM', 'trim|required');
			$this->form_validation->set_rules('pk_itm_qnty', 'Quantity', 'trim|required|is_natural_no_zero');
			$this->form_validation->set_rules('pk_itm_price', 'Price', 'trim|required');
			$this->form_validation->set_rules('pk_subtotal', 'Sub Total', 'trim|required');
			$this->form_validation->set_rules('pk_tax_amt', 'Tax Amount', 'trim|required');
			$this->form_validation->set_rules('pk_total_amt', 'Total Amount', 'trim|required');
			$this->form_validation->set_rules('ipack_itm_no', 'AUTOGEN ID', 'trim|required');

			if ($this->form_validation->run()) {

				if ($this->admin_m->check_Existing_Item_asper_POrder_inDB($pk_code, $ipack_itm_no) == TRUE) {

					$row_arr = array(
						'po_detail_autogen' => $ipack_itm_no,
						'po_detail_item' => $pk_code,
						'po_detail_sku' => $pk_sku,
						'po_detail_quantity' => $pk_itm_qnty,
						'po_detail_unitprice' => $pk_itm_price,
						'po_detail_subtotal' => $pk_subtotal,
						'po_detail_taxamount' => $pk_tax_amt,
						'po_detail_total' => $pk_total_amt,
						'po_detail_createdate' => date('Y-m-d H:i:s')
					);

					$resultset = $this->admin_m->addupdate_temp_Porder_Item_inDB($row_arr);
					if ($resultset != FALSE) {
						$resultbunch = $this->admin_m->getDetails_Porder_Item_from_DB($resultset);
						echo json_encode(array('msg' => 1, 'cat_set' => $resultbunch));
					} else {
						echo json_encode(array('msg' => 0, 'e_msg' => 'DB insertion Problem, check again.'));
					}

				} else {
					echo json_encode(array('msg' => 0, 'e_msg' => 'Item already inserted, check again.'));
				}
			} else {
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		} else {
			redirect('dafault404');
		}
	}

	public function modify_porder_set_submission111111111()
	{
		if ($_POST) {
			$po_id = $this->input->post("po_id");
			$ipack_itm_no = $this->input->post("ipack_itm_no");
			$itemdtl_counter = $this->input->post("itemdtl_counter");
			$itemdtl_tamount = $this->input->post("itemdtl_tamount");
			$po_project = $this->input->post("po_project");
			$po_numner = $this->input->post("po_numner");
			$po_supp = $this->input->post("po_supp");
			$po_address = $this->input->post("po_address");
			$po_dl_note = $this->input->post("po_dl_note");

			$this->form_validation->set_rules('ipack_itm_no', 'Autogen ID', 'trim|required');
			$this->form_validation->set_rules('itemdtl_counter', 'Item', 'trim|required|is_natural_no_zero');
			$this->form_validation->set_rules('itemdtl_tamount', 'Total Amount', 'trim|required');
			$this->form_validation->set_rules('po_project', 'Project', 'trim|required|is_natural_no_zero');
			$this->form_validation->set_rules('po_numner', 'Purchase Order No.', 'trim|required');
			$this->form_validation->set_rules('po_supp', 'Supplier', 'trim|required|is_natural_no_zero');
			$this->form_validation->set_rules('po_address', 'Delivery Address', 'trim|required');
			$this->form_validation->set_rules('po_dl_note', 'Delivery Note', 'trim');
			$this->form_validation->set_rules('po_id', 'Package ID', 'trim|required|is_natural_no_zero');

			if ($this->form_validation->run() == TRUE) {
				//echo "1st";
				if ($this->admin_m->check_porder_set_nos_exist(trim($po_numner), $po_id) == TRUE) {
					//date_default_timezone_set("Asia/Kolkata");
					if ($po_dl_note != "") {
						$po_dl_note = trim($po_dl_note);
					} else {
						$po_dl_note = NULL;
					}
					$row = array(
						'porder_project_ms' => $po_project,
						'porder_no' => trim($po_numner),
						'porder_supplier_ms' => $po_supp,
						'porder_address' => trim($po_address),
						'porder_delivery_note' => trim($po_dl_note),
						'porder_total_item' => $itemdtl_counter,
						'porder_total_amount' => $itemdtl_tamount,
						'porder_modifydate' => date('Y-m-d H:i:s'),
						'porder_modifyby' => $this->session->userdata['uid']
					);

					if ($this->admin_m->common_Updation_in_DB($row, 'purchase_order_master', 'porder_id', $po_id) == TRUE) {
						$detail_counter = 0;
						$row2 = array(
							'po_detail_porder_ms' => $po_id
						);
						if ($this->admin_m->common_Updation_in_DB($row2, 'purchase_order_details', 'po_detail_autogen', $ipack_itm_no) == FALSE) {
							$detail_counter++;
						}
						if ($detail_counter == 0) {
							echo json_encode(array('msg' => 1, 's_msg' => ''));
						} else {
							echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Update Details Table Data, Try Again.'));
						}
					} else {
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Update Data, Try Again.'));
					}
				} else {
					echo json_encode(array('msg' => 0, 'e_msg' => 'Purchase Order No. already Exist, please check it.'));
				}
			} else {
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		} else {
			redirect('default404');
		}
	}

	public function view_recv_order_sets($prid, $view = 0)
	{
		$this->data['po_list'] = $this->db->order_by('porder_no', 'ASC')->where('porder_status', 1)->get('purchase_order_master')->result();
		$this->data['rorder_list'] = $this->admin_m->get_All_Receive_order_list_fromDB($prid);
		$this->data['item_detailsets'] = $this->admin_m->getDetails_ItemList_forRecv_Order_from_DB($prid);
		$this->data['view'] = $view;
		$this->data['receiveID'] = $prid;
		$this->load->view('admin/recv_order/view_recv_order', $this->data);
	}

	public function print_recv_order_setpdf($prid)
	{
		$receive_order = $this->admin_m->getReceiveOrder($prid);
		$attData['receive_order'] = $this->admin_m->get_All_Receive_order_list_fromDB($prid);
		$attData['item_detailsets'] = $this->admin_m->getDetails_ItemList_forRecv_Order_from_DB($prid);
		$attData['company'] = $this->admin_m->getCompanySetting();
		$title = $receive_order->rorder_porder_ms;
		return $this->admin_m->showPOPDF('receive_order', $attData,$title);

	}

}
