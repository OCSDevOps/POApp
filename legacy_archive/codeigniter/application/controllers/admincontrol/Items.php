<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Items extends Admin_Controller
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
		redirect('admincontrol/items/item_list');
	}

	public function item_list()
	{
		$category = $this->input->get('category');
		$cost_code = $this->input->get('cost_code');
		$rentable = $this->input->get('rentable');

		$filterClauses = [];

		if ($category != null) {
			$filterClauses = $filterClauses + array('item_cat_ms' => $category);
		}
		if ($cost_code != null) {
			$filterClauses = $filterClauses + array('item_ccode_ms' => $cost_code);
		}
		if ($rentable != null) {
			$filterClauses = $filterClauses + array('item_is_rentable' => $rentable);
		}

		$this->data['getrecord_list'] = $this->admin_m->get_All_ItemBundle_Set(null, $filterClauses);
		$this->data['category_list'] = $this->db->order_by('icat_name', 'ASC')->where('icat_status', 1)->get('item_category_tab')->result();
		$this->data['ccode_list'] = $this->db->order_by('cc_no', 'ASC')->where('cc_status', 1)->get('cost_code_master')->result();
		$this->data['uom_list'] = $this->db->order_by('uom_name', 'ASC')->where('uom_status', 1)->get('unit_of_measure_tab')->result();
		$this->data['filters'] = $filterClauses;
		$this->load->view('admin/item/item_list_view', $this->data);
	}

	public function add_new_item_sets()
	{
		if ($_POST) {
			$itm_code = $this->input->post("itm_code");
			$itm_name = $this->input->post("itm_name");
			$itm_category = $this->input->post("itm_category");
			$itm_costcode = $this->input->post("itm_costcode");
			$itm_desc = $this->input->post("itm_desc");
			$sc_uom = $this->input->post("sc_uom");
			$item_is_rentable = $this->input->post("item_is_rentable");

			$this->form_validation->set_rules('itm_code', 'Item Code', 'trim|required');
			$this->form_validation->set_rules('itm_name', 'Item Name', 'trim|required');
			$this->form_validation->set_rules('itm_category', 'Item Category', 'trim|required|is_natural_no_zero');
			$this->form_validation->set_rules('itm_costcode', 'Item Cost Code', 'trim|required|is_natural_no_zero');
			$this->form_validation->set_rules('itm_desc', 'Item Description', 'trim|required');
			$this->form_validation->set_rules('sc_uom', 'Unit of Measure', 'trim|required|is_natural_no_zero');

			if ($this->form_validation->run() == TRUE) {
				//echo "1st";
				if ($this->admin_m->check_itemsets_exist($itm_code) == TRUE) {
					//date_default_timezone_set("Asia/Kolkata");

					$row = array(
						'item_code' => trim($itm_code),
						'item_name' => trim($itm_name),
						'item_description' => trim($itm_desc),
						'item_ccode_ms' => $itm_costcode,
						'item_cat_ms' => $itm_category,
						'item_is_rentable' => $item_is_rentable == 1 ? 1 : 0,
						'item_unit_ms' => $sc_uom,
						'item_createdate' => date('Y-m-d H:i:s'),
						'item_createby' => $this->session->userdata['uid']
					);

					if ($this->admin_m->common_Insertion_in_DB($row, 'item_master') == TRUE) {
						echo json_encode(array('msg' => 1, 's_msg' => ''));
					} else {
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Insert Data, Try Again.'));
					}
				} else {
					echo json_encode(array('msg' => 0, 'e_msg' => 'Item Code already Exist, please check it.'));
				}
			} else {
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		} else {
			redirect('default404');
		}
	}

	public function lock_itemset($uid = NULL)
	{
		if($this->session->userdata('utype')==1 || $this->data['templateDetails']->pt_i_item<3){
			if ($uid == NULL) {
				redirect('admincontrol/items/item_list');
			}
			$row_arr = array(
				'item_status' => 0
			);
			if ($this->admin_m->common_Updation_in_DB($row_arr, 'item_master', 'item_id', $uid) == TRUE) {
				$this->session->set_flashdata("success", "Record is Locked successfully");
				redirect('admincontrol/items/item_list');
			} else {
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
				redirect('admincontrol/items/item_list');
			}
		} else {
			redirect('default404');
		}
	}

	public function unlock_itemset($uid = NULL)
	{
		if($this->session->userdata('utype')==1 || $this->data['templateDetails']->pt_i_item<3){
			if ($uid == NULL) {
				redirect('admincontrol/items/item_list');
			}
			$row_arr = array(
				'item_status' => 1
			);
			if ($this->admin_m->common_Updation_in_DB($row_arr, 'item_master', 'item_id', $uid) == TRUE) {
				$this->session->set_flashdata("success", "Record is Unlocked successfully");
				redirect('admincontrol/items/item_list');
			} else {
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
				redirect('admincontrol/items/item_list');
			}
		} else {
			redirect('default404');
		}
	}

	public function modify_item_sets()
	{
		if ($_POST) {
			$update_id_item = $this->input->post("update_id_item");
			$update_itm_code = $this->input->post("update_itm_code");
			$update_itm_name = $this->input->post("update_itm_name");
			$update_itm_category = $this->input->post("update_itm_category");
			$update_itm_costcode = $this->input->post("update_itm_costcode");
			$update_itm_desc = $this->input->post("update_itm_desc");
			$update_sc_uom = $this->input->post("update_sc_uom");
			$update_itm_is_rentable = $this->input->post("item_is_rentable");

			$this->form_validation->set_rules('update_id_item', 'Item ID', 'trim|required|is_natural_no_zero');
			$this->form_validation->set_rules('update_itm_code', 'Item Code', 'trim|required');
			$this->form_validation->set_rules('update_itm_name', 'Item Name', 'trim|required');
			$this->form_validation->set_rules('update_itm_category', 'Item Category', 'trim|required|is_natural_no_zero');
			$this->form_validation->set_rules('update_itm_costcode', 'Item CostCode', 'trim|required|is_natural_no_zero');
			$this->form_validation->set_rules('update_itm_desc', 'Item Description', 'trim|required');
			$this->form_validation->set_rules('update_sc_uom', 'Unit of Measure', 'trim|required|is_natural_no_zero');
			if ($this->form_validation->run() == TRUE) {
				//echo "1st";
				if ($this->admin_m->check_itemsets_exist($update_itm_code, $update_id_item) == TRUE) {
					//date_default_timezone_set("Asia/Kolkata");
					$row = array(
						'item_code' => trim($update_itm_code),
						'item_name' => trim($update_itm_name),
						'item_description' => trim($update_itm_desc),
						'item_cat_ms' => $update_itm_category,
						'item_unit_ms' => $update_sc_uom,
						'item_ccode_ms' => $update_itm_costcode,
						'item_is_rentable' => $update_itm_is_rentable == 1 ? 1 : 0,
						'item_modifydate' => date('Y-m-d H:i:s'),
						'item_modifyby' => $this->session->userdata['uid']
					);

					if ($this->admin_m->common_Updation_in_DB($row, 'item_master', 'item_id', $update_id_item) == TRUE) {
						echo json_encode(array('msg' => 1, 's_msg' => ''));
					} else {
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Update Data, Try Again.'));
					}
				} else {
					echo json_encode(array('msg' => 0, 'e_msg' => 'Item Code already Exist, please check it.'));
				}
			} else {
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		} else {
			redirect('default404');
		}
	}

	public function get_details_of_itemsets()
	{
		if ($_POST) {
			$name_itemid = $this->input->post("name_itemid");

			$this->form_validation->set_rules('name_itemid', 'Item ID', 'trim|required|is_natural_no_zero');

			if ($this->form_validation->run() == TRUE) {

				$getrecord_detail = $this->admin_m->get_All_ItemBundle_Set($name_itemid);
				if (count((array)$getrecord_detail) > 0) {
					echo json_encode(array('msg' => 1, 's_msg' => $getrecord_detail));
				} else {
					echo json_encode(array('msg' => $getrecord_detail, 'e_msg' => 'There have some Problem to Retrieve Data, Try Again.'));
				}

			} else {
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		} else {
			redirect('default404');
		}
	}

	public function bulkitem_upload_section_sets()
	{
		if ($_POST) {
			$filename = $_FILES['files']['name'];
			if (!empty($filename)) {

				//print_r($data_upload);
				$this->load->helper(array('form', 'url'));
				$this->load->library('upload');

				$config['upload_path'] = realpath('upload_file/bulk_file/');
				$config['allowed_types'] = 'csv';
				$config['overwrite'] = FALSE;
				$config['remove_spaces'] = TRUE;
				$config['max_size'] = '9000';
				$config['file_name'] = date('dmYHis') . $filename;

				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				if ($this->upload->do_upload('files')) {

					$upload_data = $this->upload->data();
					$filename = $upload_data['file_name'];
					$file_extension = $upload_data['file_ext'];

					$this->load->library('CSVReader');

					$csvData = $this->csvreader->parse_csv($_FILES['files']['tmp_name']);
					$errorStatus=0;
					if (!empty($csvData)) {
						foreach ($csvData as $row) {
							$cost_code = $this->admin_m->get_CostCodeByNo(ltrim(str_replace('/','-',$row['Item Cost Code']),"0"));
							if(!empty($cost_code)){
								// $cost_code = ltrim(str_replace('/','-',$row['cost_code']),"0");
								$category = $this->admin_m->get_CategoryByName($row['Item Category']);
								if(!empty($category)){
									$unit = $this->admin_m->get_UnitByName($row['UOM']);
									if(!empty($unit)){
										$memData = array(
											'item_code' => isset($row['Item Code']) ? $row['Item Code'] : null,
											'item_name' => isset($row['Item Name']) ? $row['Item Name'] : null,
											'item_description' => isset($row['Description']) ? $row['Description'] : null,
											'item_ccode_ms' => isset($cost_code) ? $cost_code->cc_id : null,
											'item_cat_ms' => isset($category) ? $category->icat_id : null,
											'item_unit_ms' => isset($unit) ? $unit->uom_id : null,
											'item_is_rentable' => isset($row['Rentable']) ? (($row['Rentable']=="YES") ? 1 : 0) : null
										);
										$this->admin_m->common_Insertion_in_DB_Items($memData, 'item_master');
									}else{
										$errorStatus=3;
										break;
									}
								}else{
									$errorStatus=2;
									break;
								}
							}else{
								$errorStatus=1;
								break;
							}
						}
					}

					if($errorStatus==1){
						echo json_encode(array('msg' => 0, 'e_msg' => 'Cost Code '.ltrim(str_replace('/','-',$row['Item Cost Code']),"0").' not setup in app, please check and add cost code first and then continue.'));
					}else if($errorStatus==2){
						echo json_encode(array('msg' => 0, 'e_msg' => 'Category '.$row['Item Category'].' not setup in app, please check and add category first and then continue.'));
					}else if($errorStatus==3){
						echo json_encode(array('msg' => 0, 'e_msg' => 'UOM '.$row['UOM'].' not setup in app, please check and add UOM first and then continue.'));
					}else{
						echo json_encode(array('msg' => 1, 's_msg' => ''));
					}

				} else {
					echo json_encode(array('msg' => 0, 'e_msg' => $this->upload->display_errors()));
				}

			} else {
				echo json_encode(array('msg' => 0, 'e_msg' => 'Please Select a File to Upload, Chcek Agian.'));
			}
			exit;
		} else {
			redirect('default404');
		}
	}

	public function export_csv(){
		/* file name */
		$getrecord_list =$this->admin_m->get_All_ItemBundle_Set();
		$filename = 'items_'.date('Ymd').'.csv';
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=$filename");
		header("Content-Type: application/csv; ");
		/* get data */

		/* file creation */
		$file = fopen('php://output', 'w');
		$header = array("Item Code","Item Name","Item Cost Code","Item Category","UOM","Status","Rentable");
		fputcsv($file, $header);
		foreach ($getrecord_list as $key=>$line){
			$data = [
				$line->item_code,
				$line->item_name,
				$line->cc_no,
				$line->icat_name,
				$line->uom_name,
				($line->item_status == 1 ? "Active" : "Inactive"),
				($line->item_is_rentable == 1 ? "Yes" : "No")
			];
			fputcsv($file,$data);
		}
		fclose($file);
		exit;
	}

	public function delete_itemset($id)
	{
		if($this->session->userdata('utype')==1 || $this->data['templateDetails']->pt_i_item<2){
			$resultrow = $this->db->get_where('item_master', array('item_id' => $id))->row();
			if ($resultrow) {
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
				$res = $this->admin_m->check_usage('supplier_catalog_tab', 'supcat_item_code', $resultrow->item_code);
				$res2 = $this->admin_m->check_usage('item_package_details', 'ipdetail_item_ms', $resultrow->item_code);
				$res3 = $this->admin_m->check_usage('receive_order_details', 'ro_detail_item', $resultrow->item_code);

				if (!$res && !$res2 && !$res3) {
				if($this->db->delete('item_master', array('item_id' => $resultrow->item_id))) {
					$this->session->set_flashdata("success", "Record Deleted successfully");
					return redirect('admincontrol/items/item_list');
				}
				} else {
					$this->session->set_flashdata("e_error", "This item is linked with supplier catalog or item package, please unlink before delete.");
				}
				return redirect('admincontrol/items/item_list');
			} else {
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
				return redirect('admincontrol/items/item_list');
			}
		} else {
			redirect('default404');
		}
	}
}
