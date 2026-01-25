<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Porder extends Admin_Controller
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
		redirect('admincontrol/porder/all_purchase_order_list');
	}

	// list view of purchase order
	public function all_purchase_order_list()
	{
		$project = $this->input->get('project');
		$supplier = $this->input->get('supplier');
		$status = $this->input->get('status');

		$filterClauses = [];

		if ($project != null) {
			$filterClauses = $filterClauses + array('porder_project_ms' => $project);
		}
		if ($supplier != null) {
			$filterClauses = $filterClauses + array('porder_supplier_ms' => $supplier);
		}
		if ($status != null) {
			$filterClauses = $filterClauses + array('porder_general_status' => $status);
		}

		$this->data['getrecord_list'] = $this->admin_m->get_All_purchaseorder_list_fromDB(null, $filterClauses);

		$this->data['projects'] = $this->admin_m->get_All_Project();
		$this->data['suppliers'] = $this->admin_m->get_All_Suppliers();
		$this->data['filters'] = $filterClauses;
		$this->load->view('admin/porder/porder_list_view', $this->data);
	}

	// add new purchase order
	public function add_new_purchase_order()
	{
		if($this->session->userdata('utype')==1 || $this->data['templateDetails']->pt_t_porder<3){
			$this->data['itm_list'] = $this->db->order_by('item_name', 'ASC')->where('item_status', 1)->where('item_is_rentable', 0)->get('item_master')->result();
			$this->data['proj_list'] = $this->db->order_by('proj_name', 'ASC')->where('proj_status', 1)->get('project_master')->result();
			$this->data['supp_list'] = $this->db->order_by('sup_name', 'ASC')->where('sup_status', 1)->get('supplier_master')->result();
			$this->data['pak_list'] = $this->db->order_by('ipack_name', 'ASC')->get('item_package_master')->result();
			$this->data['taxgroup_list'] = $this->db->order_by('id', 'ASC')->get('taxgroup_master')->result();
			$this->data['ccode_list'] = $this->db->order_by('cc_id', 'ASC')->get('cost_code_master')->result();
			$this->data['icat_list'] = $this->db->order_by('icat_id', 'ASC')->get('item_category_tab')->result();
			$this->data['uom_list'] = $this->db->order_by('uom_id', 'ASC')->get('unit_of_measure_tab')->result();
			$this->load->view('admin/porder/add_pur_order', $this->data);
		}else{
			redirect('default404');
		}
	}

	// get item master item list
	function get_itemmaster_list(){

		$this->db->where('item_status', 1);
		$this->db->where('item_is_rentable', 0);
		if(!empty($_POST['category'])){
			$this->db->where('im.item_cat_ms', $_POST['category']);
		}
		if(!empty($_POST['cc'])){
			$this->db->where('im.item_ccode_ms', $_POST['cc']);
		}
		$this->db->join('item_category_tab ic','ic.icat_id=im.item_cat_ms');
		$this->db->join('cost_code_master cc','cc.cc_id=im.item_ccode_ms');
		$this->db->order_by('item_name', 'ASC');
		$result = $this->db->get('item_master im')->result();
		$data=array();
		$count=1;
		foreach($result as $item){
			$data[]=array(
				$count,
				$item->item_code,
				$item->item_name,
				$item->icat_name,
				$item->cc_no,
				// '<input type="hidden" id="search-item-code1'.$count.'" value="'.$item->item_code.'"><button type="button" class="btn btn-sm btn-danger select-search-item" id="select-search-item'.$count.'">Slelect</button>'
				'<input type="checkbox" class="form-control search-item" id="search-item1'.$count.'" value="'.$item->item_code.'">'
			);
			$count++;
		}

		$output=array("data"=>$data);
		echo json_encode($output);
	}

	// get supplier catalog item list
	function get_suppliercatalog_list(){
		$this->db->where('item_status', 1);
		$this->db->where('item_is_rentable', 0);
		if(!empty($_POST['category'])){
			$this->db->where('im.item_cat_ms', $_POST['category']);
		}
		if(!empty($_POST['cc'])){
			$this->db->where('im.item_ccode_ms', $_POST['cc']);
		}
		if(!empty($_POST['supplier'])){
			$this->db->where('sc.supcat_supplier', $_POST['supplier']);
		}
		$this->db->join('item_master im','im.item_code=sc.supcat_item_code');
		$this->db->join('item_category_tab ic','ic.icat_id=im.item_cat_ms');
		$this->db->join('cost_code_master cc','cc.cc_id=im.item_ccode_ms');
		$this->db->order_by('item_name', 'ASC');
		$result = $this->db->get('supplier_catalog_tab sc')->result();
		$data=array();
		$count=1;
		foreach($result as $item){
			$data[]=array(
				$count,
				$item->item_code,
				$item->item_name,
				$item->icat_name,
				$item->cc_no,
				$item->supcat_sku_no,
				$item->supcat_price,
				// '<input type="hidden" id="search-item-code2'.$count.'" value="'.$item->item_code.'"><button type="button" class="btn btn-sm btn-danger select-search-item" id="select-search-item'.$count.'">Slelect</button>'
				'<input type="checkbox" class="form-control search-item" id="search-item2'.$count.'" value="'.$item->item_code.'">'
			);
			$count++;
		}

		$output=array("data"=>$data);
		echo json_encode($output);
	}

	// get supplier catalog item list
	function get_supplierc_list(){

	}

	// get address from projects
	public function get_address_from_porject_find()
	{
		if ($_POST) {
			$po_project = $this->input->post("po_project");

			$this->form_validation->set_rules('po_project', 'Project', 'trim|required');

			if ($this->form_validation->run() == TRUE) {

				$getresult = $this->db->get_where('project_master', array('proj_id' => $po_project, 'proj_status' => 1))->row();

				$this->db->select('porder_id, porder_no');
				$this->db->from('purchase_order_master');
				$this->db->where('porder_project_ms',$po_project);
				$this->db->order_by('porder_id', 'DESC')->limit(1);
				$po_record = $this->db->get()->row();

				$proj_id = str_replace('-', '', $getresult->proj_number);
				$number = isset($po_record) ? explode('-', $po_record->porder_no) : [];

				$company = $this->admin_m->getCompanySetting();

				if($getresult->billing_address == null) {
					$getresult->billing_name = $company->company_name;
					$getresult->billing_address = $company->company_address;
				}

				if (isset($number[1])) {
					$number_ = $number[1];
				} else {
					$number_ = 0;
				}
				$porder_id = str_pad(isset($po_record->porder_id) ? $number_ + 1 : 1, 4, '0', STR_PAD_LEFT);
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

	// add items from package
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

						$getresult = $this->admin_m->GetSupplier_Item_cataLog_search($supp_set, $itmcode[$jk]);

						if (count((array)$getresult) > 0) {
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
								'po_detail_unitprice' => $getresult->supcat_price,
								'po_detail_subtotal' => $pk_subtotal,
								'po_detail_taxamount' => $pk_tax_amt,
								'po_detail_total' => $pk_total_amt,
								'item_description' => $getresult1->item_description,
								'po_detail_tax_group' => $pk_tax_group,
								'po_detail_cost_code' => $getresult1->cc_id,
								'porder_detail_uom' => $getresult1->uom_id,
								'po_detail_createdate' => date('Y-m-d H:i:s')
							);
							$response[] = $row_arr;
						}
						} else {
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
									'po_detail_unitprice' => 0,
									'po_detail_subtotal' => $pk_subtotal,
									'po_detail_taxamount' => $pk_tax_amt,
									'po_detail_total' => $pk_total_amt,
									'item_description' => $getresult1->item_description,
									'po_detail_tax_group' => $pk_tax_group,
									'po_detail_cost_code' => $getresult1->cc_id,
									'porder_detail_uom' => $getresult1->uom_id,
									'po_detail_createdate' => date('Y-m-d H:i:s')
								);
								$response[] = $row_arr;
							}
						}
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

	// storing new purchase order in DB
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

	public function delete_itemset($id)
	{

		if($this->session->userdata('utype')==1 || $this->data['templateDetails']->pt_t_porder<3){
			$resultrow = $this->db->get_where('purchase_order_master', array('porder_id' => $id))->row();
			$hasReceiveOrder = $this->db->get_where('receive_order_master',['rorder_porder_ms'=>$id])->num_rows();
			if($hasReceiveOrder == 0){
				if ($resultrow) {
					if($this->db->delete('purchase_order_details', array('po_detail_porder_ms' => $resultrow->porder_id))) {
						$this->db->delete('purchase_order_master', array('porder_id' => $id));
						$this->session->set_flashdata("success", "Record Deleted successfully");
						return redirect('admincontrol/porder/all_purchase_order_list');
					}
					$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
					return redirect('admincontrol/porder/all_purchase_order_list');
				} else {
					$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
					return redirect('admincontrol/porder/all_purchase_order_list');
				}
			}else{
				$this->session->set_flashdata("e_error", "Cannot delete purchase order, it has receive orders associated with it. please remove receive orders first to delete purchase order.");
				return redirect('admincontrol/porder/all_purchase_order_list');
			}
		} else {
			redirect('default404');
		}
	}


	// Adding new Record of PO
	public function new_p_order_Set_submission()
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
			$pk_sku = $this->input->post("pk_sku");
			$pk_uom = $this->input->post("pk_uom");

			$pk_taxcode = $this->input->post("pk_taxcode");
			$pk_itm_qnty = $this->input->post("pk_itm_qnty");
			$pk_itm_price = $this->input->post("pk_itm_price");
			$pk_desc = $this->input->post("pk_desc");
			$pk_subtotal = $this->input->post("pk_subtotal");
			$pk_tax_amt = $this->input->post("pk_tax_amt");
			$pk_total_amt = $this->input->post("pk_total_amt");
			$pk_tax_group = $this->input->post("pk_tax_group");
			$status = $this->input->post("po_status");

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

				for ($i = 0; $i < count(json_decode($pk_total_amt[0])); $i++) {

					if ($this->admin_m->check_Existing_Item_asper_POrder_inDB(json_decode($pk_item[0])[$i], $ipack_itm_no) == FALSE) {

						$row_arr = array(
							'po_detail_autogen' => $ipack_itm_no,
							'po_detail_item' => json_decode($pk_item[0])[$i] != "" ? json_decode($pk_item[0])[$i] : null,
							'po_detail_sku' => isset($pk_sku[0]) ? json_decode($pk_sku[0])[$i] : null,
							'po_detail_taxcode' => isset($pk_taxcode[0]) ? json_decode($pk_taxcode[0])[$i] : null,
							'po_detail_quantity' => isset($pk_itm_qnty[0]) ? json_decode($pk_itm_qnty[0])[$i] : null,
							'po_detail_description' => isset($pk_desc[0]) ? json_decode($pk_desc[0])[$i] : null,
							'po_detail_unitprice' => isset($pk_itm_price[0]) ? json_decode($pk_itm_price[0])[$i] : null,
							'po_detail_subtotal' => isset($pk_subtotal[0]) ? json_decode($pk_subtotal[0])[$i] : null,
							'po_detail_taxamount' => isset($pk_tax_amt[0]) ? json_decode($pk_tax_amt[0])[$i] : null,
							'po_detail_total' => isset($pk_total_amt[0]) ? json_decode($pk_total_amt[0])[$i] : null,
							'po_detail_cc' => isset($pk_ccode[0]) ? json_decode($pk_ccode[0])[$i] : null,
							'po_detail_tax_group' => isset($pk_tax_group[0]) ? json_decode($pk_tax_group[0])[$i] : null,
							'porder_detail_uom' => isset($pk_uom[0]) ? json_decode($pk_uom[0])[$i] : null,
							'po_detail_createdate' => date('Y-m-d H:i:s')
						);
						$resultset = $this->admin_m->addupdate_temp_Porder_Item_inDB($row_arr);
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

				if ($this->admin_m->check_porder_set_nos_exist(trim($po_number)) == TRUE) {
					//date_default_timezone_set("Asia/Kolkata");
					if ($po_dl_note != "") {
						$po_dl_note = trim($po_dl_note);
					} else {
						$po_dl_note = NULL;
					}
					$row = array(
						'porder_project_ms' => $po_project,
						'porder_no' => ($po_number),
						'porder_supplier_ms' => $po_supp,
						'porder_address' => trim($po_address),
						'porder_delivery_date' => trim($po_delivery_date),
						'porder_delivery_note' => trim($po_dl_note),
						'porder_total_item' => $itemdtl_counter,
						'porder_total_amount' => $itemdtl_tamount,
						'porder_type' => $po_type,
						'porder_description' => $po_desc,
						'porder_general_status' => $status,
						'integration_status' => $status == 'submitted' ? "rte" : "pending",
						'porder_createdate' => date('Y-m-d H:i:s'),
						'porder_createby' => $this->session->userdata['uid']
					);

					$rowids = $this->admin_m->common_Insertion_in_DB_with_ID($row, 'purchase_order_master');
					if ($rowids != FALSE) {
						$detail_counter = 0;
						$row2 = array(
							'po_detail_porder_ms' => $rowids
						);
						if ($this->admin_m->common_Updation_in_DB($row2, 'purchase_order_details', 'po_detail_autogen', $ipack_itm_no) == FALSE) {
							$detail_counter++;
						}
						if ($detail_counter == 0) {

							$response['msg'] = 1;
							$response['e_msg'] = '';

							if($status == "submitted") {

								if($this->admin_m->get_Notification_SettingByKey('is_purchase_order')) {

									$template = $this->admin_m->GetEmailTemplateWithID($this->admin_m->get_Notification_SettingByKey('purchase_order_template'));
									$template2 = $this->admin_m->GetEmailTemplateWithID($this->admin_m->get_Notification_SettingByKey('procore_template'));
									$procoreUser = $this->admin_m->get_Notification_SettingByKey('procore_notify_user');

									$setting = $this->admin_m->get_CompanySMTP_Setting();
									$supplier = $this->admin_m->GetDetailsofSupplier($po_supp);
									$company = $this->admin_m->getCompanySetting();
									$attData['company'] = $company;
									$attData['supplier'] = $supplier;
									$attData['purchase_order'] = $purchase_order = $this->admin_m->get_All_purchaseorder_list_fromDB($rowids);
									$attData['project'] = $project = $this->admin_m->get_Project_By_ID($po_project);
									$attData['item_detailsets'] = $this->admin_m->getDetails_ItemList_POrder_from_DB($rowids);
									$project_details = $this->admin_m->getProjectDetails($po_project);
									$todaysPO = $this->admin_m->getTodayPurchaseOrder();

									if($supplier->sup_email != null) {
										$toEmail = [
											[
												'email' => $supplier->sup_email,
												'name' => $supplier->sup_name,
											]
										];
									}	

									$toEmail2=[];

									foreach(json_decode($procoreUser) as $user){
										$pUser = $this->admin_m->GetDetailsofUsers($user);
										$toEmail2[] = [
											'name' => $pUser->username,
											'email' => $pUser->email
										];
									}


									// if(count($project_details) > 0) {
									// 	foreach($project_details as $key => $value) {
									// 		$accountant = $this->admin_m->GetDetailsofUsers($value->pdetail_accountant);
									// 		$coordinator = $this->admin_m->GetDetailsofUsers($value->pdetail_coordinator);
									// 		$manager = $this->admin_m->GetDetailsofUsers($value->pdetail_manager);
									// 		$site_coordinator = $this->admin_m->GetDetailsofUsers($value->pdetail_site_coordinator);
									// 		$supervisor = $this->admin_m->GetDetailsofUsers($value->pdetail_supervisor);


									// 		if(isset($accountant)) {
									// 			$toEmail[] = [
									// 				'name' => $accountant->username,
									// 				'email' => $accountant->email
									// 			];
									// 		}

									// 		if($coordinator) {
									// 			$toEmail[] = [
									// 				'name' => $coordinator->username,
									// 				'email' => $coordinator->email
									// 			];
									// 		}

									// 		if(isset($manager)) {
									// 			$toEmail[] = [
									// 				'name' => $manager->username,
									// 				'email' => $manager->email
									// 			];
									// 		}

									// 		if(isset($site_coordinator)) {
									// 			$toEmail[] = [
									// 				'name' => $site_coordinator->username,
									// 				'email' => $site_coordinator->email
									// 			];
									// 		}

									// 		if(isset($supervisor)) {
									// 			$toEmail[] = [
									// 				'name' => $supervisor->username,
									// 				'email' => $supervisor->email
									// 			];
									// 		}
									// 	}
									// }


									$summaryTable = '<table class="MsoNormalTable" border="1" cellpadding="0" width="550" style="width:412.5pt;
mso-cellspacing:1.5pt;margin-left:7.5pt;border-top:solid #DDDDDD 1.0pt;
border-left:none;border-bottom:solid #DDDDDD 1.0pt;border-right:none;
mso-border-top-alt:solid #DDDDDD .75pt;mso-border-bottom-alt:solid #DDDDDD .75pt;
mso-yfti-tbllook:1184;box-sizing:inherit">
									 <tbody>';

										$summaryTable .= '<tr style="mso-yfti-irow:0;mso-yfti-firstrow:yes;height:22.5pt;box-sizing:inherit">
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;background:#DDDDDD;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">Project Name<o:p></o:p></span></b></p>
									  </td>
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;background:#DDDDDD;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">PO #<o:p></o:p></span></b></p>
									  </td>
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;background:#DDDDDD;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">PO Title<o:p></o:p></span></b></p>
									  </td>
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;background:#DDDDDD;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">Contract Co.<o:p></o:p></span></b></p>
									  </td>
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;background:#DDDDDD;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">Value<o:p></o:p></span></b></p>
									  </td>
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;background:#DDDDDD;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">Export Status<o:p></o:p></span></b></p>
									  </td>
									 </tr>';
										foreach($todaysPO as $po) {
											$summaryTable .= '<tr style="mso-yfti-irow:0;mso-yfti-firstrow:yes;height:22.5pt;box-sizing:inherit">
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">'.$po->proj_name.'<o:p></o:p></span></b></p>
									  </td>
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">'.$po->porder_no.'<o:p></o:p></span></b></p>
									  </td>
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">'.$po->porder_description.'<o:p></o:p></span></b></p>
									  </td>
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;background:#DDDDDD;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">'.$po->sup_name.'<o:p></o:p></span></b></p>
									  </td>
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">'.$po->porder_total_amount.'<o:p></o:p></span></b></p>
									  </td>
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">Ready To Export<o:p></o:p></span></b></p>
									  </td>
									 </tr>';
										}

										$summaryTable .= '</tbody>
									</table>';

									$cc = [
										$template->email_cc,
									];

									$params = [
										"#SupName#" => $supplier->sup_name,
										"#ProjName#" => $project->proj_name,
										"#PorderNo#" => $purchase_order->porder_no,
										"#PorderAddress#" => $purchase_order->porder_address,
										"#PorderDeliveryDate#" => date('d/m/Y', strtotime($purchase_order->porder_delivery_date)),
										"#PorderDeliveryNote#" => $purchase_order->porder_delivery_note,
										"#PorderTotalAmount#" => $purchase_order->porder_total_amount
									];



									$params2 = [
										"#SupName#" => $supplier->sup_name,
										"#ProjName#" => $project->proj_name,
										"#PorderNo#" => $purchase_order->porder_no,
										"#PorderAddress#" => $purchase_order->porder_address,
										"#PorderDeliveryDate#" => date('d/m/Y', strtotime($purchase_order->porder_delivery_date)),
										"#PorderDeliveryNote#" => $purchase_order->porder_delivery_note,
										"#PorderTotalAmount#" => $purchase_order->porder_total_amount,
										"#SummaryTable#" => $summaryTable
										];

									$subjectParams = [
										"#PorderNo#" => $po_number
									];

									$data = $this->admin_m->prepareEmailBody($template->email_key, $params, $subjectParams);
									$data1 = $this->admin_m->prepareEmailBody($template2->email_key, $params2, $subjectParams);


									$attachment = $this->admin_m->makePOPdf('purchase_order', $attData, $attData['purchase_order']->porder_no);
									$response['e_msg'] = $attData['purchase_order']->porder_no;

									$this->sendSMTPEmail($toEmail, $data['subject'], $data['content'], $cc, $setting, $attachment);
									$this->sendSMTPEmail($toEmail2, $data1['subject'], $data1['content'], $cc, $setting, $attachment);

									$row3 = [
										'is_email_sent'=>1
									];
									$this->admin_m->common_Updation_in_DB($row3, 'purchase_order_master', 'porder_id', $rowids);
								}
							}
							// echo json_encode(array('msg' => 1, 's_msg' => $template));

						} else {
							$this->db->delete('purchase_order_master', array('porder_id' => $rowids));
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
					$response['e_msg'] = "Purchase Order No. already Exist, please check it.";
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

	// get details of tax
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

	// Update record of po order
	public function modify_porder_set_submission()
	{
		if ($_POST) {
			$po_id = $this->input->post("po_id");
			$ipack_itm_no = $this->input->post("ipack_itm_no");
			$itemdtl_counter = $this->input->post("itemdtl_counter");

			$itemdtl_tamount = $this->input->post("itemdtl_tamount");
			$po_number = $this->input->post("po_number_prefix") . '-' . $this->input->post("po_number");
			$po_project = $this->input->post("po_project");
			$po_desc = $this->input->post("po_desc");
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
			$status = $this->input->post("status");
			$email_include = $this->input->post("email_include");

			$po_detail_autogen = $this->input->post("po_detail_autogen");
			$this->form_validation->set_rules('ipack_itm_no', 'Autogen ID', 'trim|required');
			$this->form_validation->set_rules('itemdtl_counter', 'Item', 'trim|required|is_natural_no_zero');
			$this->form_validation->set_rules('itemdtl_tamount', 'Total Amount', 'trim|required');
			$this->form_validation->set_rules('po_project', 'Project', 'trim|required|is_natural_no_zero');
			$this->form_validation->set_rules('po_number', 'Purchase Order No.', 'trim');
			$this->form_validation->set_rules('po_supp', 'Supplier', 'trim|required|is_natural_no_zero');
			$this->form_validation->set_rules('po_address', 'Delivery Address', 'trim|required');
			$this->form_validation->set_rules('po_delivery_date', 'Delivery Date', 'trim|required');
			$this->form_validation->set_rules('po_dl_note', 'Delivery Note', 'trim');
			$this->form_validation->set_rules('po_id', 'Package ID', 'trim|required|is_natural_no_zero');

			if ($this->form_validation->run() == TRUE) {
				$this->db->delete('purchase_order_details', array('po_detail_porder_ms' => $po_id));

				for ($i = 0; $i < count(json_decode($pk_item[0])); $i++) {

					if ($this->admin_m->check_Existing_Item_asper_POrder_inDB(isset(json_decode($pk_item[0])[$i]) ? json_decode($pk_item[0])[$i] : json_decode($pk_desc[0])[$i], $ipack_itm_no) == False) {
						$row_arr = array(
							'po_detail_autogen' => $ipack_itm_no,
							'po_detail_item' => isset(json_decode($pk_item[0])[$i]) ? json_decode($pk_item[0])[$i] : json_decode($pk_desc[0])[$i],
							'po_detail_sku' => isset($pk_sku[0]) ? json_decode($pk_sku[0])[$i] : null,
							'po_detail_description' => isset($pk_desc[0]) ? json_decode($pk_desc[0])[$i] : null,
							'po_detail_taxcode' => isset($pk_taxcode[0]) ? json_decode($pk_taxcode[0])[$i] : null,
							'po_detail_quantity' => isset($pk_itm_qnty[0]) ? json_decode($pk_itm_qnty[0])[$i] : null,
							'po_detail_unitprice' => isset($pk_itm_price[0]) ? json_decode($pk_itm_price[0])[$i] : null,
							'po_detail_subtotal' => isset($pk_subtotal[0]) ? json_decode($pk_subtotal[0])[$i] : null,
							'po_detail_taxamount' => isset($pk_tax_amt[0]) ? json_decode($pk_tax_amt[0])[$i] : null,
							'po_detail_total' => isset($pk_total_amt[0]) ? json_decode($pk_total_amt[0])[$i] : null,
							'po_detail_porder_ms' => $po_id,
							'po_detail_cc' => isset($pk_ccode[0]) ? json_decode($pk_ccode[0])[$i] : null,
							'po_detail_tax_group' => isset($pk_tax_group[0]) ? json_decode($pk_tax_group[0])[$i] : null,
							'porder_detail_uom' => isset($pk_uom[0]) ? json_decode($pk_uom[0])[$i] : null,
							'po_detail_createdate' => date('Y-m-d H:i:s')
						);

						$resultset = $this->admin_m->addupdate_temp_Porder_Item_inDB($row_arr, isset(json_decode($po_detail_autogen[0])[$i]) ? json_decode($po_detail_autogen[0])[$i] : null);
						if ($resultset != FALSE) {
							$response['msg'] = 1;
							$response['cat_set'] = $resultset;
						} else {
							$response['msg'] = 0;
							$response['e_msg'] = 'DB insertion Problem, check again.';
						}
					} else {
						$row_arr = array(
							'po_detail_autogen' => $ipack_itm_no,
							'po_detail_item' => json_decode($pk_item[0])[$i] != "" ? json_decode($pk_item[0])[$i] : (isset($pk_desc[0]) ? json_decode($pk_desc[0])[$i] : null),
							'po_detail_sku' => isset($pk_sku[0]) ? json_decode($pk_sku[0])[$i] : null,
							'po_detail_taxcode' => isset($pk_taxcode[0]) ? json_decode($pk_taxcode[0])[$i] : null,
							'po_detail_description' => isset($pk_desc[0]) ? json_decode($pk_desc[0])[$i] : null,
							'po_detail_quantity' => isset($pk_itm_qnty[0]) ? json_decode($pk_itm_qnty[0])[$i] : null,
							'po_detail_unitprice' => isset($pk_itm_price[0]) ? json_decode($pk_itm_price[0])[$i] : null,
							'po_detail_subtotal' => isset($pk_subtotal[0]) ? json_decode($pk_subtotal[0])[$i] : null,
							'po_detail_taxamount' => isset($pk_tax_amt[0]) ? json_decode($pk_tax_amt[0])[$i] : null,
							'po_detail_total' => isset($pk_total_amt[0]) ? json_decode($pk_total_amt[0])[$i] : null,
							'po_detail_porder_ms' => $po_id,
							'po_detail_cc' => isset($pk_ccode[0]) ? json_decode($pk_ccode[0])[$i] : null,
							'po_detail_tax_group' => isset($pk_tax_group[0]) ? json_decode($pk_tax_group[0])[$i] : null,
							'porder_detail_uom' => isset($pk_uom[0]) ? json_decode($pk_uom[0])[$i] : null,
							'po_detail_createdate' => date('Y-m-d H:i:s')
						);

						$resultset = $this->admin_m->addupdate_temp_Porder_Item_inDB($row_arr, isset(json_decode($po_detail_autogen[0])[$i]) ? json_decode($po_detail_autogen[0])[$i] : $ipack_itm_no);
						if ($resultset != FALSE) {
							$response['msg'] = 1;
							$response['cat_set'] = $resultset;
						} else {
							$response['msg'] = 0;
							$response['e_msg'] = 'DB insertion Problem, check again.';
						}
					}
				}

				if ($this->admin_m->check_porder_set_nos_exist(trim($po_number), $po_id) == TRUE) {
					//date_default_timezone_set("Asia/Kolkata");
					if ($po_dl_note != "") {
						$po_dl_note = trim($po_dl_note);
					} else {
						$po_dl_note = NULL;
					}
					$row = array(
						'porder_project_ms' => $po_project,
						'porder_no' => trim($po_number),
						'porder_supplier_ms' => $po_supp,
						'porder_address' => trim($po_address),
						'porder_delivery_date' => trim($po_delivery_date),
						'porder_delivery_note' => trim($po_dl_note),
						'porder_description' => trim($po_desc),
						'porder_total_item' => $itemdtl_counter,
						'porder_total_amount' => $itemdtl_tamount,
						'porder_general_status' => $status,
						'integration_status' => $status == 'submitted' ? "rte" : "pending",
						'porder_modifydate' => date('Y-m-d H:i:s'),
						'porder_modifyby' => $this->session->userdata['uid']
					);

					if ($this->admin_m->common_Updation_in_DB($row, 'purchase_order_master', 'porder_id', $po_id) == TRUE) {
						$detail_counter = 0;
						$row2 = array(
							'po_detail_porder_ms' => $po_id
						);
						if ($this->admin_m->common_Updation_in_DB($row2, 'purchase_order_details', 'po_detail_autogen', isset(json_decode($po_detail_autogen[0])[$i]) ? json_decode($po_detail_autogen[0])[$i] : $ipack_itm_no) == FALSE) {
							$detail_counter++;
						}
						if ($detail_counter == 0) {
							$response['msg'] = 1;
							$response['e_msg'] = '';

							if($status == "submitted") {

								if($this->admin_m->get_Notification_SettingByKey('is_purchase_order')) {

										$template = $this->admin_m->GetEmailTemplateWithID($this->admin_m->get_Notification_SettingByKey('purchase_order_template'));
										$template2 = $this->admin_m->GetEmailTemplateWithID($this->admin_m->get_Notification_SettingByKey('procore_template'));
										$procoreUser = $this->admin_m->get_Notification_SettingByKey('procore_notify_user');
										$setting = $this->admin_m->get_CompanySMTP_Setting();
										$supplier = $this->admin_m->GetDetailsofSupplier($po_supp);
										$company = $this->admin_m->getCompanySetting();
										$todaysPO = $this->admin_m->getTodayPurchaseOrder();

										$attData['company'] = $company;
										$attData['supplier'] = $supplier;
										$attData['purchase_order'] = $purchase_order = $this->admin_m->get_All_purchaseorder_list_fromDB($po_id);
										$attData['project'] = $project = $this->admin_m->get_Project_By_ID($po_project);
										$attData['item_detailsets'] = $this->admin_m->getDetails_ItemList_POrder_from_DB($po_id);
										$project_details = $this->admin_m->getProjectDetails($po_project);


										if($supplier->sup_email != null) {
											$toEmail = [
												[
													'email' => $supplier->sup_email,
													'name' => $supplier->sup_name,
												]
											];
										}	

										$toEmail2=[];

										foreach(json_decode($procoreUser) as $user){
											$pUser = $this->admin_m->GetDetailsofUsers($user);
											$toEmail2[] = [
												'name' => $pUser->username,
												'email' => $pUser->email
											];
										}

										// if(count($project_details) > 0) {
										// 	foreach($project_details as $key => $value) {
										// 		$accountant = $this->admin_m->GetDetailsofUsers($value->pdetail_accountant);
										// 		$coordinator = $this->admin_m->GetDetailsofUsers($value->pdetail_coordinator);
										// 		$manager = $this->admin_m->GetDetailsofUsers($value->pdetail_manager);
										// 		$site_coordinator = $this->admin_m->GetDetailsofUsers($value->pdetail_site_coordinator);
										// 		$supervisor = $this->admin_m->GetDetailsofUsers($value->pdetail_supervisor);

										// 		if(isset($accountant)) {
										// 			$toEmail[] = [
										// 				'name' => $accountant->username,
										// 				'email' => $accountant->email
										// 			];
										// 		}

										// 		if($coordinator) {
										// 			$toEmail[] = [
										// 				'name' => $coordinator->username,
										// 				'email' => $coordinator->email
										// 			];
										// 		}

										// 		if(isset($manager)) {
										// 			$toEmail[] = [
										// 				'name' => $manager->username,
										// 				'email' => $manager->email
										// 			];
										// 		}

										// 		if(isset($site_coordinator)) {
										// 			$toEmail[] = [
										// 				'name' => $site_coordinator->username,
										// 				'email' => $site_coordinator->email
										// 			];
										// 		}

										// 		if(isset($supervisor)) {
										// 			$toEmail[] = [
										// 				'name' => $supervisor->username,
										// 				'email' => $supervisor->email
										// 			];
										// 		}
										// 	}
										// }

										$cc = [
											$template->email_cc
										];

										

										$summaryTable = '<table class="MsoNormalTable" border="1" cellpadding="0" width="550" style="width:412.5pt;
mso-cellspacing:1.5pt;margin-left:7.5pt;border-top:solid #DDDDDD 1.0pt;
border-left:none;border-bottom:solid #DDDDDD 1.0pt;border-right:none;
mso-border-top-alt:solid #DDDDDD .75pt;mso-border-bottom-alt:solid #DDDDDD .75pt;
mso-yfti-tbllook:1184;box-sizing:inherit">
									 <tbody>';

										$summaryTable .= '<tr style="mso-yfti-irow:0;mso-yfti-firstrow:yes;height:22.5pt;box-sizing:inherit">
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;background:#DDDDDD;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">Project Name<o:p></o:p></span></b></p>
									  </td>
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;background:#DDDDDD;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">PO #<o:p></o:p></span></b></p>
									  </td>
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;background:#DDDDDD;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">PO Title<o:p></o:p></span></b></p>
									  </td>
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;background:#DDDDDD;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">Contract Co.<o:p></o:p></span></b></p>
									  </td>
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;background:#DDDDDD;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">Value<o:p></o:p></span></b></p>
									  </td>
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;background:#DDDDDD;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">Export Status<o:p></o:p></span></b></p>
									  </td>
									 </tr>';
										foreach($todaysPO as $po) {
											$summaryTable .= '<tr style="mso-yfti-irow:0;mso-yfti-firstrow:yes;height:22.5pt;box-sizing:inherit">
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">'.$po->proj_name.'<o:p></o:p></span></b></p>
									  </td>
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">'.$po->porder_no.'<o:p></o:p></span></b></p>
									  </td>
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">'.$po->porder_description.'<o:p></o:p></span></b></p>
									  </td>
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;background:#DDDDDD;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">'.$po->sup_name.'<o:p></o:p></span></b></p>
									  </td>
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">'.$po->porder_total_amount.'<o:p></o:p></span></b></p>
									  </td>
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">Ready To Export<o:p></o:p></span></b></p>
									  </td>
									 </tr>';
										}

										$summaryTable .= '</tbody>
									</table>';

										$params = [
											"#SupName#" => $supplier->sup_name,
											"#ProjName#" => $project->proj_name,
											"#PorderNo#" => $purchase_order->porder_no,
											"#PorderAddress#" => $purchase_order->porder_address,
											"#PorderDeliveryDate#" => date('d/m/Y', strtotime($purchase_order->porder_delivery_date)),
											"#PorderDeliveryNote#" => $purchase_order->porder_delivery_note,
											"#PorderTotalAmount#" => $purchase_order->porder_total_amount
											// "#SummaryTable#" => $summaryTable
											];

										$params2 = [
											"#SupName#" => $supplier->sup_name,
											"#ProjName#" => $project->proj_name,
											"#PorderNo#" => $purchase_order->porder_no,
											"#PorderAddress#" => $purchase_order->porder_address,
											"#PorderDeliveryDate#" => date('d/m/Y', strtotime($purchase_order->porder_delivery_date)),
											"#PorderDeliveryNote#" => $purchase_order->porder_delivery_note,
											"#PorderTotalAmount#" => $purchase_order->porder_total_amount,
											"#SummaryTable#" => $summaryTable
											];

										$subjectParams = [
											"#PorderNo#" => $po_number
										];


										$data = $this->admin_m->prepareEmailBody($template->email_key, $params, $subjectParams);
										$data1 = $this->admin_m->prepareEmailBody($template2->email_key, $params2, $subjectParams);

										$attachment = $this->admin_m->makePOPdf('purchase_order', $attData, $attData['purchase_order']->porder_no);
										
										// $response['e_msg'] = $email_include;
										if ($supplier->sup_email != null && $email_include == 'true') {
											$this->sendSMTPEmail($toEmail, $data['subject'], $data['content'], $cc, $setting, $attachment);
										}
										$this->sendSMTPEmail($toEmail2, $data1['subject'], $data1['content'], $cc, $setting, $attachment);

										$row3 = [
											'is_email_sent'=>1
										];
										$this->admin_m->common_Updation_in_DB($row3, 'purchase_order_master', 'porder_id', $po_id);
									}
								}

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
					$response['e_msg'] = 'Purchase Order Number. already Exist, please check it.';
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

	public function modify_porder_sets($prid, $view = 0)
	{
		$this->data['porder_list'] = $this->db->where('porder_id', $prid)->get('purchase_order_master')->row();
		$this->data['item_detailsets'] = $detailset = $this->admin_m->getDetails_ItemList_POrder_from_DB($prid);
		foreach ($detailset as $ditems) {
			$autonumber = $ditems->po_detail_autogen;
			break;
		}
		$this->data['at_no'] = $autonumber;

		$this->data['itm_list'] = $this->db->order_by('item_name', 'ASC')->where('item_status', 1)->where('item_is_rentable', 0)->get('item_master')->result();
		$this->data['proj_list'] = $this->db->order_by('proj_name', 'ASC')->where('proj_status', 1)->get('project_master')->result();
		$this->data['ccode_list'] = $this->db->order_by('cc_id', 'ASC')->get('cost_code_master')->result();
		$this->data['pak_list'] = $this->db->order_by('ipack_name', 'ASC')->get('item_package_master')->result();
		$this->data['icat_list'] = $this->db->order_by('icat_id', 'ASC')->get('item_category_tab')->result();
		$this->data['uom_list'] = $this->db->order_by('uom_id', 'ASC')->get('unit_of_measure_tab')->result();
		$this->data['taxgroup_list'] = $this->db->order_by('id', 'ASC')->get('taxgroup_master')->result();
		$this->data['supp_list'] = $this->db->order_by('sup_name', 'ASC')->where('sup_status', 1)->get('supplier_master')->result();
		$this->data['view'] = $view;
		$this->load->view('admin/porder/edit_pur_order', $this->data);
	}

	// view of PO order
	public function view_porder_sets($prid)
	{

		$this->data['porder_list'] = $this->admin_m->get_All_purchaseorder_list_fromDB($prid);
		$this->data['item_detailsets'] = $this->admin_m->getDetails_ItemList_POrder_from_DB($prid);
		$this->load->view('admin/porder/view_pur_order', $this->data);

	}

	// print view of porder
	public function print_porder_setpdf($prid)
	{
		$attData['purchase_order'] = $purchase_order = $this->admin_m->get_All_purchaseorder_list_fromDB($prid);
		$supplier = $this->admin_m->GetDetailsofSupplier($purchase_order->porder_supplier_ms);
		$attData['supplier'] = $supplier;
		$attData['project'] = $this->admin_m->get_Project_By_ID($purchase_order->porder_project_ms);
		$attData['item_detailsets'] = $this->admin_m->getDetails_ItemList_POrder_from_DB($prid);
		$attData['company'] = $this->admin_m->getCompanySetting();
		$title = $purchase_order->porder_id;
		return $this->admin_m->showPOPDF('purchase_order', $attData,$title);

	}

	public function get_project_budget()
	{
		if ($_POST) {
			$project_id = $this->input->post("project_id");

			$this->form_validation->set_rules('project_id', 'Project Id', 'trim|required');

			if ($this->form_validation->run() == TRUE) {
				$po_ids=array();
				$projectDetails = $this->db->get_where('project_master',['proj_id'=>$project_id,'proj_status'=>1])->row();
				$pos = $this->db->get_where('purchase_order_master',['porder_project_ms'=>$project_id,'porder_status'=>1,'integration_status!=' => 'synced'])->result();
				if(!empty($pos)){
					foreach($pos as $po){
						array_push($po_ids,$po->porder_id);
					}
					$this->db->select('cc.cc_no,sum(pod.po_detail_subtotal) as committed_cost');
					$this->db->where_in('pod.po_detail_porder_ms',$po_ids);
					$this->db->from('purchase_order_details pod');
					$this->db->join('cost_code_master cc','cc.cc_id=pod.po_detail_cc');
					$this->db->group_by('pod.po_detail_cc');
					$query=$this->db->get();
					$poCommittedCost=$query->result();
					foreach($poCommittedCost as $poc){
						${'cc'.$poc->cc_no}=$poc->committed_cost;
					}
				}

				$projectBliDetails = $this->db->get_where('budget_line_item_master',['project_id'=>$projectDetails->procore_project_id,'bli_status'=>1])->result();
				$revisedBudget=array();
				$commitedCost=array();
				foreach($projectBliDetails as $bl){
					$revisedBudget+=[
						trim($bl->cost_code) => trim($bl->revised_budget)
					];
				}
				foreach($projectBliDetails as $ct){
					$ccost=0;
					if(isset(${'cc'.trim($ct->cost_code)})){
						$ccost=($ct->committed_cost+${'cc'.trim($ct->cost_code)});
					}else{
						$ccost=$ct->committed_cost;
					}
					$commitedCost+=[
						trim($ct->cost_code) => trim($ccost)
					];
				}
				if (count((array)$projectBliDetails) > 0) {
					echo json_encode(array('msg' => 1, 'rv_msg' => $revisedBudget, 'cc_msg' => $commitedCost));
				}else {
					echo json_encode(array('msg' => 0, 'e_msg' => $projectDetails));
				}
			} else {
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		} else {
			redirect('default404');
		}
	}

	public function get_supplier_email()
	{
		if ($_POST) {
			$supp_id = $this->input->post("sup_id");

			$this->form_validation->set_rules('sup_id', 'Supplier Id', 'trim|required');

			if ($this->form_validation->run() == TRUE) {
				$po_ids=array();
				$supplierDetails = $this->db->get_where('supplier_master',['sup_id'=>$supp_id,'sup_status'=>1])->row();
				// $pos = $this->db->get_where('purchase_order_master',['porder_project_ms'=>$project_id,'porder_status'=>1,'integration_status!=' => 'synced'])->result();
				// if(!empty($pos)){
				// 	foreach($pos as $po){
				// 		array_push($po_ids,$po->porder_id);
				// 	}
				// 	$this->db->select('cc.cc_no,sum(pod.po_detail_subtotal) as committed_cost');
				// 	$this->db->where_in('pod.po_detail_porder_ms',$po_ids);
				// 	$this->db->from('purchase_order_details pod');
				// 	$this->db->join('cost_code_master cc','cc.cc_id=pod.po_detail_cc');
				// 	$this->db->group_by('pod.po_detail_cc');
				// 	$query=$this->db->get();
				// 	$poCommittedCost=$query->result();
				// 	foreach($poCommittedCost as $poc){
				// 		${'cc'.$poc->cc_no}=$poc->committed_cost;
				// 	}
				// }

				// $projectBliDetails = $this->db->get_where('budget_line_item_master',['project_id'=>$projectDetails->procore_project_id,'bli_status'=>1])->result();
				// $revisedBudget=array();
				// $commitedCost=array();
				// foreach($projectBliDetails as $bl){
				// 	$revisedBudget+=[
				// 		trim($bl->cost_code) => trim($bl->revised_budget)
				// 	];
				// }
				// foreach($projectBliDetails as $ct){
				// 	$ccost=0;
				// 	if(isset(${'cc'.trim($ct->cost_code)})){
				// 		$ccost=($ct->committed_cost+${'cc'.trim($ct->cost_code)});
				// 	}else{
				// 		$ccost=$ct->committed_cost;
				// 	}
				// 	$commitedCost+=[
				// 		trim($ct->cost_code) => trim($ccost)
				// 	];
				// }
				if (count((array)$supplierDetails) > 0) {
					echo json_encode(array('msg' => 1, 'se_msg' => $supplierDetails));
				}else {
					echo json_encode(array('msg' => 0, 'e_msg' => $supplierDetails));
				}
			} else {
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		} else {
			redirect('default404');
		}
	}

	public function get_project_packages()
	{
		if ($_POST) {
			$project_id = $this->input->post("project_id");

			$this->form_validation->set_rules('project_id', 'Project Id', 'trim|required');

			if ($this->form_validation->run() == TRUE) {
				$projectPackages = $this->db->get_where('item_package_master',['ipack_project'=>$project_id,'ipack_status'=>1])->result();

				if (count((array)$projectPackages) > 0) {
					echo json_encode(array('msg' => 1, 'pk_msg' => $projectPackages));
				}else {
					echo json_encode(array('msg' => 0, 'e_msg' => $projectDetails));
				}
			} else {
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		} else {
			redirect('default404');
		}
	}

}
