<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class RFQOrder extends Admin_Controller
{

	public function __construct()
	{
		parent::__construct();
		//date_default_timezone_set("Asia/Kolkata");
		$this->data["u_details"] = $this->admin_m->GetDetailsofUsers($this->session->userdata['uid']);
//		$this->data["taxcode_set"] = $this->db->where('tc_id', 1)->get('taxcode_master')->row();
	}

	public function index()
	{
		redirect('admincontrol/rfqorder/all_rfq_list');
	}

	public function all_rfq_list()
	{

		$this->data['getrecord_list'] = $this->admin_m->get_All_rfq_list_fromDB();
		$this->data['proj_list'] = $this->db->order_by('proj_name', 'ASC')->where('proj_status', 1)->get('project_master')->result();
		$this->data['supp_list'] = $this->db->order_by('sup_name', 'ASC')->where('sup_status', 1)->get('supplier_master')->result();
		$this->load->view('admin/rporder/rporder_list_view', $this->data);
	}

	public function add_new_request_form_quote()
	{

		$this->data['itm_list'] = $this->db->order_by('item_name', 'ASC')->where('item_status', 1)->get('item_master')->result();
		$this->data['proj_list'] = $this->db->order_by('proj_name', 'ASC')->where('proj_status', 1)->get('project_master')->result();
		$this->data['supp_list'] = $this->db->order_by('sup_name', 'ASC')->where('sup_status', 1)->get('supplier_master')->result();
		$this->data['pak_list'] = $this->db->order_by('ipack_name', 'ASC')->get('item_package_master')->result();
		$this->data['taxgroup_list'] = $this->db->order_by('id', 'ASC')->get('taxgroup_master')->result();
		$this->data['ccode_list'] = $this->db->order_by('cc_id', 'ASC')->get('cost_code_master')->result();
		$this->data['uom_list'] = $this->db->order_by('uom_id', 'ASC')->get('unit_of_measure_tab')->result();
		$this->load->view('admin/rporder/add_rfq', $this->data);
	}

	public function get_address_from_porject_find()
	{
		if ($_POST) {
			$po_project = $this->input->post("po_project");

			$this->form_validation->set_rules('po_project', 'Project', 'trim|required');

			if ($this->form_validation->run() == TRUE) {

				$getresult = $this->db->get_where('project_master', array('proj_id' => $po_project, 'proj_status' => 1))->row();

				$this->db->select('purchase_order_master.porder_id, purchase_order_master.porder_no,project_master.proj_id');
				$this->db->from('purchase_order_master');
				$this->db->join('project_master', 'purchase_order_master.porder_project_ms = project_master.proj_id');
				$this->db->where('purchase_order_master.porder_project_ms', $po_project);
				$this->db->order_by('porder_id', 'DESC')->limit(1);
				$po_record = $this->db->get()->row();

				$proj_id = str_replace('-', '', $getresult->proj_number);
				$number = isset($po_record) ? explode('-',$po_record->porder_no) : [];
				$porder_id = str_pad(isset($po_record->porder_id) ? $number[1]+1 : 1, 4, '0', STR_PAD_LEFT);
				$po_number = $porder_id;
				$po_number_prefix = $proj_id;
				if (count((array)$getresult) > 0) {
					echo json_encode(array('msg' => 1, 's_msg' => $getresult, 'po_number' => $po_number, 'po_number_prefix' => $po_number_prefix));
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

	public function get_packageitems_from_package_find()
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

	public function add_multiple_items_from_package_sets()
	{
		if ($_POST) {
			$ipack_itm_no = $this->input->post("ipack_itm_no");
			$totalitem_pkg = $this->input->post("totalitem_pkg");
			$supp_set = $this->input->post("supp_set");
			$itmcode = $this->input->post("itmcode");
			$itmqty = $this->input->post("itmqty");
			$pk_tax_group = $this->input->post("pk_tax_group");

			$this->form_validation->set_rules('totalitem_pkg', 'Item Count', 'trim|required|is_natural_no_zero');
			$this->form_validation->set_rules('supp_set', 'Supplier', 'trim|required');
			$this->form_validation->set_rules('ipack_itm_no', 'AUTOGEN ID', 'trim|required');

			if ($this->form_validation->run() == TRUE) {
				$pk_item_set = 0;
				$response = [];

				for ($jk = 0; $jk < $totalitem_pkg; $jk++) {

					if ($itmcode[$jk] == "" || $itmcode[$jk] == NULL || $itmqty[$jk] == NULL || $itmqty[$jk] == "") {
						$pk_item_set++;
					}
					if ($pk_item_set == 0) {

//						$getresult = $this->admin_m->GetSupplier_Item_cataLog_search($supp_set, $itmcode[$jk]);

//						if (count((array)$getresult) > 0) {
							$getresult1 = $this->admin_m->GetCCode_from_ItemCode_serch($itmcode[$jk]);
							if (count((array)$getresult1) > 0) {

								$pk_subtotal = $itmqty[$jk] * 1;
//									$pk_tax_amt = ($pk_subtotal * $this->data["taxcode_set"]->tc_tax_value) / 100;
								$pk_tax_amt = 0;
								$pk_total_amt = $pk_subtotal + $pk_tax_amt;
								$row_arr = array(
									'po_detail_autogen' => $ipack_itm_no,
									'po_detail_item' => $itmcode[$jk],
									'po_detail_sku' => isset($getresult->supcat_sku_no) ? $getresult->supcat_sku_no : null,
									'po_detail_taxcode' => 0,
									'po_detail_quantity' => $itmqty[$jk],
									'po_detail_unitprice' => 1,
									'po_detail_subtotal' => $pk_subtotal,
									'po_detail_taxamount' => $pk_tax_amt,
									'po_detail_total' => $pk_total_amt,
									'po_detail_tax_group' => $pk_tax_group,
									'po_detail_cost_code' => $getresult1->cc_no,
									'po_detail_uom' => $getresult1->uom_name,
									'po_detail_createdate' => date('Y-m-d H:i:s')
								);
								$response[] = $row_arr;
							}
//						}
					}
				}
				if ($pk_item_set == 0) {
					$selectitems = $this->admin_m->get_All_POrder_Items($ipack_itm_no);
					$total_itemamt = $this->admin_m->get_All_POrder_Items_TotalAmount($ipack_itm_no);
					echo json_encode(array('msg' => 1, 's_msg' => $response, 'titem' => $selectitems, 'tamount' => $total_itemamt->pdtotal));
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


	public function get_alldetails_from_item_find()
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
					$getresult1 = $this->admin_m->GetCCode_from_ItemCode_serch($pk_item);
					if (count((array)$getresult1) > 0) {
						echo json_encode(array('msg' => 1, 's_msg' => $getresult1, 'supp_set' => $getresult, 'e_msg' => 'Item selected is not setup in Supplier Catalog. Therefore, the price information will not be auto-populate. User will need manually input the price.'));
					} else {
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Update Details Data, Try Again.'));
					}
//					echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to collect Supplier Catalog Data, Try Again.'));
				}

			} else {
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		} else {
			redirect('default404');
		}
	}

	public function new_porder_item_submission()
	{
		if ($_POST) {
			$ipack_itm_no = $this->input->post("ipack_itm_no");
			$pk_code = $this->input->post("pk_code");
			$pk_item = $this->input->post("pk_item");
			$pk_ccode = $this->input->post("pk_ccode");
			$pk_sku = $this->input->post("pk_sku");
			$pk_uom = $this->input->post("pk_uom");
			$pk_taxcode = $this->input->post("pk_taxcode");
			$pk_itm_qnty = $this->input->post("pk_itm_qnty");
			$pk_itm_price = $this->input->post("pk_itm_price");
			$pk_subtotal = $this->input->post("pk_subtotal");
			$pk_tax_amt = $this->input->post("pk_tax_amt");
			$pk_total_amt = $this->input->post("pk_total_amt");
			$pk_tax_group = $this->input->post("pk_tax_group");
			$this->form_validation->set_rules('pk_code', 'Item Code', 'trim|required|matches[pk_item]');
			$this->form_validation->set_rules('pk_ccode', 'CostCode', 'trim|required');
			$this->form_validation->set_rules('pk_sku', 'SKU', 'trim|required');
			$this->form_validation->set_rules('pk_uom', 'UOM', 'trim|required');
			$this->form_validation->set_rules('pk_taxcode', 'TaxCode', 'trim|required');
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
						'po_detail_taxcode' => $pk_taxcode,
						'po_detail_quantity' => $pk_itm_qnty,
						'po_detail_unitprice' => $pk_itm_price,
						'po_detail_subtotal' => $pk_subtotal,
						'po_detail_taxamount' => $pk_tax_amt,
						'po_detail_total' => $pk_total_amt,
						'po_detail_tax_group' => $pk_tax_group,
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

	public function delete_itemset_update()
	{
		if ($_POST) {
			$qid = $this->input->post("qid");
			$this->form_validation->set_rules('qid', 'ITEM ID', 'trim|required|is_natural_no_zero');

			if ($this->form_validation->run()) {

				$resultrow = $this->db->get_where('purchase_order_details', array('po_detail_id' => $qid))->row();
				if (count((array)$resultrow) > 0) {
					if ($this->db->delete('purchase_order_details', array('po_detail_id' => $qid))) {
						echo json_encode(array('msg' => 1, 'expmarks' => $resultrow));
					} else {
						echo json_encode(array('msg' => 0, 'e_msg' => 'Data not Deleted from DB, check again.'));
					}
				} else {
					echo json_encode(array('msg' => 0, 'e_msg' => 'DB Data not found, check again.'));
				}
			} else {
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		} else {
			redirect('dafault404');
		}
	}


	public function new_rfq_Set_submission()
	{
		if ($_POST) {

			$ipack_itm_no = $this->input->post("ipack_itm_no");
			$itemdtl_counter = $this->input->post("itemdtl_counter");
			$itemdtl_tamount = $this->input->post("itemdtl_tamount");
			$po_project = $this->input->post("po_project");
			$po_number = $this->input->post("po_number_prefix") . '-' . $this->input->post("po_number");
			$po_supp = $this->input->post("po_supp");
			$po_address = $this->input->post("po_address");
			$po_delivery_date = $this->input->post("po_delivery_date");
			$po_dl_note = $this->input->post("po_dl_note");
			$po_desc = $this->input->post("po_desc");


			$ipack_itm_no = $this->input->post("ipack_itm_no");
			$pk_item = $this->input->post("pk_item");
			$pk_ccode = $this->input->post("pk_ccode");
			$pk_uom = $this->input->post("pk_uom");
			$pk_itm_qnty = $this->input->post("pk_itm_qnty");
			$pk_desc = $this->input->post("pk_desc");
			$po_type = $this->input->post("po_type");

			$this->form_validation->set_rules('ipack_itm_no', 'Autogen ID', 'trim|required');
			$this->form_validation->set_rules('itemdtl_tamount', 'Total Amount', 'trim|required');
			$this->form_validation->set_rules('po_project', 'Project', 'trim|required|is_natural_no_zero');
			$this->form_validation->set_rules('po_number', 'Purchase Order No.', 'trim|required');
			$this->form_validation->set_rules('po_supp', 'Supplier', 'trim|required|is_natural_no_zero');
			$this->form_validation->set_rules('po_address', 'Delivery Address', 'trim|required');
			$this->form_validation->set_rules('po_delivery_date', 'Delivery Date', 'trim|required');
			$this->form_validation->set_rules('po_dl_note', 'Delivery Note', 'trim');
			$response = [];

			if ($this->form_validation->run() == TRUE) {
				//echo "1st";

				for ($i = 0; $i < count(json_decode($pk_item[0])); $i++) {

					if ($this->admin_m->check_Existing_Item_asper_RFQ_inDB(json_decode($pk_item[0])[$i], $ipack_itm_no) == TRUE) {
						$row_arr = array(
							'rfq_detail_autogen' => $ipack_itm_no,
							'rfq_detail_item' => json_decode($pk_item[0])[$i] != "" ? json_decode($pk_item[0])[$i] : (isset($pk_desc[0]) ? json_decode($pk_desc[0])[$i] : null) ,
							'rfq_detail_quantity' => isset($pk_itm_qnty[0]) ? json_decode($pk_itm_qnty[0])[$i] : null,
							'rfq_detail_unitprice' => isset($pk_itm_price[0]) ? json_decode($pk_itm_price[0])[$i] : null,
							'rfq_detail_cc' => isset($pk_ccode[0]) ? json_decode($pk_ccode[0])[$i] : null,
							'rfq_detail_uom' => isset($pk_uom[0]) ? json_decode($pk_uom[0])[$i] : null,
							'rfq_detail_createdate' => date('Y-m-d H:i:s')
						);

						$resultset = $this->admin_m->addupdate_temp_Rfq_Item_inDB($row_arr);
						if ($resultset != FALSE) {
							$response['msg'] = 1;
							$response['cat_set'] = $resultset;
						} else {
							$response['msg'] = 0;
							$response['e_msg'] = 'DB insertion Problem, check again.';
						}

					} else {
						$response['msg'] = 0;
						$response['e_msg'] = 'Item already inserted, check again.';

					}
				}

				if ($this->admin_m->check_rfq_set_nos_exist(trim($po_number)) == TRUE) {
					//date_default_timezone_set("Asia/Kolkata");
					if ($po_dl_note != "") {
						$po_dl_note = trim($po_dl_note);
					} else {
						$po_dl_note = NULL;
					}
					$row = array(
						'rporder_project_ms' => $po_project,
						'rporder_no' => ($po_number),
						'rporder_supplier_ms' => $po_supp,
						'rporder_address' => trim($po_address),
						'rporder_delivery_date' => trim($po_delivery_date),
						'rporder_delivery_note' => trim($po_dl_note),
						'rporder_total_item' => $itemdtl_counter,
						'rporder_total_amount' => $itemdtl_tamount,
						'rporder_type' => $po_type,
						'rporder_description' => $po_desc,
						'rporder_createdate' => date('Y-m-d H:i:s'),
						'rporder_createby' => $this->session->userdata['uid']
					);

					$rowids = $this->admin_m->common_Insertion_in_DB_with_ID($row, 'request_purchase_order');
					if ($rowids != FALSE) {
						$detail_counter = 0;
						$row2 = array(
							'rfq_detail_porder_ms' => $rowids
						);
						if ($this->admin_m->common_Updation_in_DB($row2, 'request_purchase_order_details', 'rfq_detail_autogen', $ipack_itm_no) == FALSE) {
							$detail_counter++;
						}
						if ($detail_counter == 0) {
							$response['msg'] = 1;
							$response['e_msg'] = "";
						} else {
							$this->db->delete('request_purchase_order', array('rporder_id' => $rowids));
							//$this->db->delete('project_details', array('pdetail_proj_ms' => $rowids));
							$response['msg'] = 0;
							$response['e_msg'] = "There have some Problem to Insert Details Table Data, Try Again.";
						}
					} else {
						$response['msg'] = 0;
						$response['e_msg'] = "There have some Problem to Insert Data, Try Again.";
					}
				} else {
					$response['msg'] = 0;
					$response['e_msg'] = "Request Form Quote No. already Exist, please check it.";
				}
			} else {
				$response['msg'] = 0;
				$response['e_msg'] = validation_errors();
			}

			echo json_encode($response);
			exit;
		} else {
			redirect('default404');
		}
	}

	public function get_tax_detail()
	{
		if ($_POST) {

			$tax_group = $this->input->post("tax_group");

			//echo "1st";

			$resultset = $this->admin_m->get_detail_tax_group($tax_group);
			if ($resultset != FALSE) {
				echo json_encode(array('msg' => 1, 'taxgroup' => $resultset));
			} else {
				echo json_encode(array('msg' => 0, 'e_msg' => 'Tax Group Fetching Problem, check again.'));
			}

			exit;
		} else {
			redirect('default404');
		}
	}

	public
	function lock_porder_set($uid = NULL)
	{
		if ($uid == NULL) {
			redirect('admincontrol/porder/all_purchase_order_list');
		}
		$row_arr = array(
			'porder_status' => 0
		);
		if ($this->admin_m->common_Updation_in_DB($row_arr, 'purchase_order_master', 'porder_id', $uid) == TRUE) {
			$this->session->set_flashdata("success", "Record is Locked successfully");
			redirect('admincontrol/porder/all_purchase_order_list');
		} else {
			$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
			redirect('admincontrol/porder/all_purchase_order_list');
		}
	}

	public
	function unlock_porder_set($uid = NULL)
	{
		if ($uid == NULL) {
			redirect('admincontrol/porder/all_purchase_order_list');
		}
		$row_arr = array(
			'porder_status' => 1
		);
		if ($this->admin_m->common_Updation_in_DB($row_arr, 'purchase_order_master', 'porder_id', $uid) == TRUE) {
			$this->session->set_flashdata("success", "Record is Unlocked successfully");
			redirect('admincontrol/porder/all_purchase_order_list');
		} else {
			$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
			redirect('admincontrol/porder/all_purchase_order_list');
		}
	}

	public
	function modify_rfq_set_submission()
	{
		if ($_POST) {
			$po_id = $this->input->post("po_id");
			$ipack_itm_no = $this->input->post("ipack_itm_no");
			$itemdtl_counter = $this->input->post("itemdtl_counter");
			$itemdtl_tamount = $this->input->post("itemdtl_tamount");
			$po_project = $this->input->post("po_project");
			$po_number = $this->input->post("po_number_prefix") . '-' . $this->input->post("po_number");
			$po_supp = $this->input->post("po_supp");
			$po_address = $this->input->post("po_address");
			$po_delivery_date = $this->input->post("po_delivery_date");
			$po_dl_note = $this->input->post("po_dl_note");

			$pk_taxcode = $this->input->post("pk_taxcode");
			$pk_itm_price = $this->input->post("pk_itm_price");
			$pk_subtotal = $this->input->post("pk_subtotal");
			$pk_tax_amt = $this->input->post("pk_tax_amt");
			$pk_total_amt = $this->input->post("pk_total_amt");
			$pk_tax_group = $this->input->post("pk_tax_group");

			$pk_item = $this->input->post("pk_item");
			$pk_ccode = $this->input->post("pk_ccode");
			$pk_uom = $this->input->post("pk_uom");
			$pk_itm_qnty = $this->input->post("pk_itm_qnty");
			$pk_desc = $this->input->post("pk_desc");
			$po_type = $this->input->post("po_type");

			$rfq_detail_autogen = $this->input->post("rfq_detail_autogen");
			$this->form_validation->set_rules('ipack_itm_no', 'Autogen ID', 'trim|required');
			$this->form_validation->set_rules('itemdtl_counter', 'Item', 'trim|required|is_natural_no_zero');
			$this->form_validation->set_rules('itemdtl_tamount', 'Total Amount', 'trim|required');
			$this->form_validation->set_rules('po_project', 'Project', 'trim|required|is_natural_no_zero');
			$this->form_validation->set_rules('po_number', 'Request Quote No.', 'trim|required');
			$this->form_validation->set_rules('po_supp', 'Supplier', 'trim|required|is_natural_no_zero');
			$this->form_validation->set_rules('po_address', 'Delivery Address', 'trim|required');
			$this->form_validation->set_rules('po_delivery_date', 'Delivery Date', 'trim|required');
			$this->form_validation->set_rules('po_dl_note', 'Delivery Note', 'trim');
			$this->form_validation->set_rules('po_id', 'Package ID', 'trim|required|is_natural_no_zero');

			if ($this->form_validation->run() == TRUE) {


				for ($i = 0; $i < count(json_decode($pk_item[0])); $i++) {

					if ($this->admin_m->check_Item_Exist_RFQ_inDB(json_decode($pk_item[0])[$i], json_decode($rfq_detail_autogen[0])[$i]) == TRUE) {
						$row_arr = array(
							'rfq_detail_autogen' => json_decode($rfq_detail_autogen[0])[$i],
							'rfq_detail_item' => json_decode($pk_item[0])[$i] != "" ? json_decode($pk_item[0])[$i] : (isset($pk_desc[0]) ? json_decode($pk_desc[0])[$i] : null),
							'rfq_detail_quantity' => isset($pk_itm_qnty[0]) ? json_decode($pk_itm_qnty[0])[$i] : null,
							'rfq_detail_unitprice' => isset($pk_itm_price[0]) ? json_decode($pk_itm_price[0])[$i] : null,
							'rfq_detail_cc' => isset($pk_ccode[0]) ? json_decode($pk_ccode[0])[$i] : null,
							'rfq_detail_createdate' => date('Y-m-d H:i:s'),
							'rfq_detail_taxcode' => isset($pk_taxcode[0]) ? json_decode($pk_taxcode[0])[$i] : null,
							'rfq_detail_subtotal' => isset($pk_subtotal[0]) ? json_decode($pk_subtotal[0])[$i] : null,
							'rfq_detail_taxamount' => isset($pk_tax_amt[0]) ? json_decode($pk_tax_amt[0])[$i] : null,
							'rfq_detail_total' => isset($pk_total_amt[0]) ? json_decode($pk_total_amt[0])[$i] : null,
							'rfq_detail_tax_group' => isset($pk_tax_group[0]) ? json_decode($pk_tax_group[0])[$i] : null,
							'rfq_detail_uom' => isset($pk_uom[0]) ? json_decode($pk_uom[0])[$i] : null,
						);

						$resultset = $this->admin_m->update_Rfq_Item_inDB($row_arr, json_decode($rfq_detail_autogen[0])[$i]);
						if ($resultset != FALSE) {
							$response['msg'] = 1;
							$response['cat_set'] = $resultset;
						} else {
							$response['msg'] = 0;
							$response['e_msg'] = 'DB insertion Problem, check again.';
						}

					} else {
						$row_arr = array(
							'rfq_detail_autogen' => json_decode($rfq_detail_autogen[0])[$i],
							'rfq_detail_item' => json_decode($pk_item[0])[$i] != "" ? json_decode($pk_item[0])[$i] : (isset($pk_desc[0]) ? json_decode($pk_desc[0])[$i] : null),
							'rfq_detail_quantity' => isset($pk_itm_qnty[0]) ? json_decode($pk_itm_qnty[0])[$i] : null,
							'rfq_detail_unitprice' => isset($pk_itm_price[0]) ? json_decode($pk_itm_price[0])[$i] : null,
							'rfq_detail_cc' => isset($pk_ccode[0]) ? json_decode($pk_ccode[0])[$i] : null,
							'rfq_detail_createdate' => date('Y-m-d H:i:s'),
							'rfq_detail_taxcode' => isset($pk_taxcode[0]) ? json_decode($pk_taxcode[0])[$i] : null,
							'rfq_detail_subtotal' => isset($pk_subtotal[0]) ? json_decode($pk_subtotal[0])[$i] : null,
							'rfq_detail_taxamount' => isset($pk_tax_amt[0]) ? json_decode($pk_tax_amt[0])[$i] : null,
							'rfq_detail_total' => isset($pk_total_amt[0]) ? json_decode($pk_total_amt[0])[$i] : null,
							'rfq_detail_tax_group' => isset($pk_tax_group[0]) ? json_decode($pk_tax_group[0])[$i] : null,
							'rfq_detail_uom' => isset($pk_uom[0]) ? json_decode($pk_uom[0])[$i] : null,
						);

						$resultset = $this->admin_m->update_Rfq_Item_inDB($row_arr);
						if ($resultset != FALSE) {
							$response['msg'] = 1;
							$response['cat_set'] = $resultset;
						} else {
							$response['msg'] = 0;
							$response['e_msg'] = 'DB insertion Problem, check again.';
						}

					}
					//echo "1st";
					if ($this->admin_m->check_rfq_set_nos_exist(trim($po_number), $po_id) == TRUE) {
						//date_default_timezone_set("Asia/Kolkata");
						if ($po_dl_note != "") {
							$po_dl_note = trim($po_dl_note);
						} else {
							$po_dl_note = NULL;
						}
						$row = array(
							'rporder_project_ms' => $po_project,
							'rporder_no' => trim($po_number),
							'rporder_supplier_ms' => $po_supp,
							'rporder_address' => trim($po_address),
							'rporder_delivery_date' => trim($po_delivery_date),
							'rporder_delivery_note' => trim($po_dl_note),
							'rporder_total_item' => $itemdtl_counter,
							'rporder_total_amount' => $itemdtl_tamount,
							'rporder_modifydate' => date('Y-m-d H:i:s'),
							'rporder_modifyby' => $this->session->userdata['uid']
						);

						if ($this->admin_m->common_Updation_in_DB($row, 'request_purchase_order', 'rporder_id', $po_id) == TRUE) {
							$detail_counter = 0;
							$row2 = array(
								'rfq_detail_porder_ms' => $po_id
							);
							if ($this->admin_m->common_Updation_in_DB($row2, 'request_purchase_order_details', 'rfq_detail_autogen', json_decode($rfq_detail_autogen[0])[$i]) == FALSE) {
								$detail_counter++;
							}
							if ($detail_counter == 0) {
								$response['msg'] = 1;
								$response['e_msg'] = '';
							} else {
								$response['msg'] = 0;
								$response['e_msg'] = 'There have some Problem to Update Details Table Data, Try Again.';
							}
						} else {
							$response['msg'] = 0;
							$response['e_msg'] = 'There have some Problem to Update Data, Try Again.';
						}
					} else {
						$response['msg'] = 0;
						$response['e_msg'] = 'Request Quote No. already Exist, please check it.';
					}
				}
			}
			 else {
				 $response['msg'] = 0;
				 $response['e_msg'] = validation_errors();
			}
		echo json_encode($response);
			exit;
		} else {
			redirect('default404');
		}
	}

	public
	function modify_rfq_sets($prid, $view = 0)
	{

		$this->data['rporder_list'] = $this->db->where('rporder_id', $prid)->get('request_purchase_order')->row();
		$this->data['item_detailsets'] = $detailset = $this->admin_m->getDetails_ItemList_RFQ_from_DB($prid);
		foreach ($detailset as $ditems) {
			$autonuber = $ditems->rfq_detail_autogen;
			break;
		}
		$this->data['at_no'] = $autonuber;
		$this->data['itm_list'] = $this->db->order_by('item_name', 'ASC')->where('item_status', 1)->get('item_master')->result();
		$this->data['proj_list'] = $this->db->order_by('proj_name', 'ASC')->where('proj_status', 1)->get('project_master')->result();
		$this->data['ccode_list'] = $this->db->order_by('cc_id', 'ASC')->get('cost_code_master')->result();
		$this->data['uom_list'] = $this->db->order_by('uom_id', 'ASC')->get('unit_of_measure_tab')->result();
		$this->data['taxgroup_list'] = $this->db->order_by('id', 'ASC')->get('taxgroup_master')->result();
		$this->data['supp_list'] = $this->db->order_by('sup_name', 'ASC')->where('sup_status', 1)->get('supplier_master')->result();
		$this->data['view'] = $view;
		$this->load->view('admin/rporder/edit_pur_order', $this->data);
	}

	public
	function view_porder_sets($prid)
	{

		$this->data['rporder_list'] = $this->admin_m->get_All_rfq_list_fromDB($prid);
		$this->data['item_detailsets'] = $this->admin_m->getDetails_ItemList_RFQ_from_DB($prid);
		$this->load->view('admin/rporder/view_pur_order', $this->data);

	}

	public function make_porder($prid)
	{

		$rporder_list = $this->admin_m->get_All_rfq_list_fromDB($prid);
		$item_detailsets = $this->admin_m->getDetails_ItemList_RFQ_from_DB($prid);

		$this->admin_m->quote_to_purchase_order($rporder_list, $item_detailsets);

		return redirect('admincontrol/rfqorder/all_rfq_list');

	}

	public
	function print_porder_setpdf($prid)
	{
		$company_assets = $this->data['company_assets'];
		$porder_list = $this->admin_m->get_All_purchaseorder_list_fromDB($prid);
		$item_detailsets = $this->admin_m->getDetails_ItemList_POrder_from_DB($prid);
		error_reporting(0);
		$this->load->helper("tcpdf_helper");
		tcpdf();
		$obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$obj_pdf->SetCreator(PDF_CREATOR);
		$title = $porder_list->porder_no;
		$obj_pdf->SetTitle($title);

		$obj_pdf->SetPrintHeader(false);
		$obj_pdf->SetPrintFooter(false);

		//$obj_pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $title, PDF_HEADER_STRING);
		//$obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		//$obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		//$obj_pdf->SetDefaultMonospacedFont('helvetica');
		$obj_pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		$obj_pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_RIGHT);
		$obj_pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		//$obj_pdf->SetFont('helvetica', '', 9);
		//$obj_pdf->setFontSubsetting(false);
		$obj_pdf->AddPage();

		//ob_start();
		// we can have any view part here like HTML, PHP etc


		$my_html = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
		<html xmlns=\"http://www.w3.org/1999/xhtml\">
		<head>
		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />
		<title>" . $title . "</title>
		</head>
		<body>
		<div class=\"header\">";
		$my_html = $my_html . '<table style="width: 100%" style="font-size: 20px;" cellpadding="10">
		<tbody>
			<tr>
				<td colspan="2" width="50%"><span style="font-size: 25px;"><strong>Purchase Order : </strong>' . $porder_list->porder_no . '</span>
				<br/><br/>' . $company_assets->company_name . '<br/>
				' . $company_assets->company_address . '<br/></td>
				<td colspan="2" rowspan="2" align="center"><img src="' . base_url() . 'upload_file/company/' . $company_assets->company_logo . '" style="max-width:400px;" /></td>
			</tr>
			<tr>
				<td colspan="2">
					<strong>Project - </strong>' . $porder_list->proj_name . '<br/>
					<strong>Project Address - </strong>' . $porder_list->porder_address . '<br/><br/>
					<strong>Supplier - </strong>' . $porder_list->sup_name . '<br/>
				</td>
			</tr>
			<tr>
				<td width="25%"><strong>P.O. - </strong>' . $porder_list->porder_no . '</td>
				<td width="50%" colspan="2"></td>
				<td align="right"><strong>Date - </strong>' . date("d-M-Y", strtotime($porder_list->porder_createdate)) . '</td>
			</tr>
			<tr>
				<td colspan="4">
				<table border="1" style="width: 100%" style="font-size: 20px;" cellpadding="5">
						<thead>
							<tr style="background-color:#cdcdcd;">
								<th><strong>Sl No.</strong></th>
								<th><strong>Item</strong></th>
								<th><strong>Quantity</strong></th>
								<th><strong>Unit price</strong></th>
								<th><strong>Sub Total</strong></th>
								<th><strong>Tax Amount</strong></th>
								<th><strong>Total</strong></th>
							</tr>
						</thead>
						<tbody>';
		$ss_total = $ttax = $finaltotal = 0.00;
		foreach ($item_detailsets as $keys => $idetails) {
			$ss_total = $ss_total + $idetails->po_detail_subtotal;
			$ttax = $ttax + $idetails->po_detail_taxamount;
			$finaltotal = $finaltotal + $idetails->po_detail_total;
			$my_html = $my_html . '<tr>
								<td>' . ($keys + 1) . '</td>
								<td>' . $idetails->item_name . '</td>
								<td>' . $idetails->po_detail_quantity . '</td>
								<td>' . $idetails->po_detail_unitprice . '</td>
								<td>' . $idetails->po_detail_subtotal . '</td>
								<td>' . $idetails->po_detail_taxamount . '</td>
								<td>' . $idetails->po_detail_total . '</td>
							</tr>';
		}
		$my_html = $my_html . '<tr>
								<td></td>
								<td></td>
								<td></td>
								<td><strong>Total - </strong></td>
								<td><strong>' . number_format((float)$ss_total, 2, ".", "") . '</strong></td>
								<td><strong>' . number_format((float)$ttax, 2, ".", "") . '</strong></td>
								<td><strong>' . number_format((float)$finaltotal, 2, ".", "") . '</strong></td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
			<td colspan="4"><br/><br/><strong>Delivery Note - </strong>' . $porder_list->porder_delivery_note . '</td>
			</tr>
		</tbody>
		</table>';
		$my_html = $my_html . "</div>
		</body>
		</html>";

		$content = $my_html; //ob_get_contents();
		//ob_end_clean();
		$obj_pdf->writeHTML($content, true, false, true, false, '');
		$obj_pdf->Output($title . '.pdf', 'I');
		//$obj_pdf->Output(FCPATH.'/pdf/'.$advice_detail->advice_id.'.pdf', 'D');

		//$this->session->set_flashdata("success","Report is Generated Successfully");


	}

}
