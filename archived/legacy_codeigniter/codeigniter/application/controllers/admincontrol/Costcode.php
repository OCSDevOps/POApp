<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Costcode extends Admin_Controller
{

	public function __construct()
	{
		parent::__construct();
		//date_default_timezone_set("Asia/Kolkata");
		$this->data["u_details"] = $this->admin_m->GetDetailsofUsers($this->session->userdata['uid']);
		$this->data['templateDetails'] = $this->db->get_where('permission_master', ['pt_id' => $this->session->userdata('pt_id'), 'status' => 1])->row();

	}

	public function index()
	{
		redirect('admincontrol/costcode/cost_code_list');
	}

	public function cost_code_list()
	{
		$this->data['getrecord_list'] = $this->db->order_by('cc_no', 'ASC')->get_where('cost_code_master')->result();
		$this->load->view('admin/costcode/costcode_list_view', $this->data);
	}

	public function add_new_costcode_set()
	{
		if ($_POST) {
			$name_cc = $this->input->post("name_cc");
			$desc_cc = $this->input->post("desc_cc");

			$this->form_validation->set_rules('name_cc', 'Cost Code', 'trim|required');
			$this->form_validation->set_rules('desc_cc', 'Description', 'trim|required');

			if ($this->form_validation->run() == TRUE) {
				//echo "1st";
				if ($this->admin_m->check_ccode_exist($name_cc) == TRUE) {
					//date_default_timezone_set("Asia/Kolkata");

					$row = array(
						'cc_no' => trim($name_cc),
						'cc_description' => trim($desc_cc),
						'cc_createdate' => date('Y-m-d H:i:s'),
						'cc_createby' => $this->session->userdata['uid']
					);

					if ($this->admin_m->common_Insertion_in_DB($row, 'cost_code_master') == TRUE) {
						echo json_encode(array('msg' => 1, 's_msg' => ''));
					} else {
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Insert Data, Try Again.'));
					}
				} else {
					echo json_encode(array('msg' => 0, 'e_msg' => 'Cost Code already Exist, please check it.'));
				}
			} else {
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		} else {
			redirect('default404');
		}
	}

	public function lock_costcodeset($uid = NULL)
	{
		if ($this->session->userdata('utype') == 1 || $this->data['templateDetails']->pt_m_costcode < 3) {
			if ($uid == NULL) {
				redirect('admincontrol/costcode/cost_code_list');
			}
			$row_arr = array(
				'cc_status' => 0
			);
			if ($this->admin_m->common_Updation_in_DB($row_arr, 'cost_code_master', 'cc_id', $uid) == TRUE) {
				$this->session->set_flashdata("success", "Record is Locked successfully");
				redirect('admincontrol/costcode/cost_code_list');
			} else {
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
				redirect('admincontrol/costcode/cost_code_list');
			}
		} else {
			redirect('default404');
		}
	}

	public function unlock_costcodeset($uid = NULL)
	{
		if ($this->session->userdata('utype') == 1 || $this->data['templateDetails']->pt_m_costcode < 3) {
			if ($uid == NULL) {
				redirect('admincontrol/costcode/cost_code_list');
			}
			$row_arr = array(
				'cc_status' => 1
			);
			if ($this->admin_m->common_Updation_in_DB($row_arr, 'cost_code_master', 'cc_id', $uid) == TRUE) {
				$this->session->set_flashdata("success", "Record is Unlocked successfully");
				redirect('admincontrol/costcode/cost_code_list');
			} else {
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
				redirect('admincontrol/costcode/cost_code_list');
			}
		} else {
			redirect('default404');
		}
	}

	public function modify_costcode_sets()
	{
		if ($_POST) {
			$update_id_cc = $this->input->post("update_id_cc");
			$update_name_cc = $this->input->post("update_name_cc");
			$update_desc_cc = $this->input->post("update_desc_cc");

			$this->form_validation->set_rules('update_name_cc', 'Cost Code', 'trim|required');
			$this->form_validation->set_rules('update_desc_cc', 'Description', 'trim|required');
			$this->form_validation->set_rules('update_id_cc', 'Costcode ID', 'trim|required|is_natural');

			if ($this->form_validation->run() == TRUE) {
				//echo "1st";
				if ($this->admin_m->check_ccode_exist($update_name_cc, $update_id_cc) == TRUE) {
					//date_default_timezone_set("Asia/Kolkata");

					$row = array(
						'cc_no' => trim($update_name_cc),
						'cc_description' => trim($update_desc_cc),
						'cc_modifydate' => date('Y-m-d H:i:s'),
						'cc_modifyby' => $this->session->userdata['uid']
					);

					if ($this->admin_m->common_Updation_in_DB($row, 'cost_code_master', 'cc_id', $update_id_cc) == TRUE) {
						echo json_encode(array('msg' => 1, 's_msg' => ''));
					} else {
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Update Data, Try Again.'));
					}
				} else {
					echo json_encode(array('msg' => 0, 'e_msg' => 'Unit Name already Exist, please check it.'));
				}
			} else {
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		} else {
			redirect('default404');
		}
	}

	public function get_details_of_ccode()
	{
		if ($_POST) {
			$name_ccid = $this->input->post("name_ccid");

			$this->form_validation->set_rules('name_ccid', 'Costcode ID', 'trim|required|is_natural');

			if ($this->form_validation->run() == TRUE) {

				$getrecord_detail = $this->db->where('cc_id', $name_ccid)->get('cost_code_master')->row();
				if (count((array)$getrecord_detail) > 0) {
					echo json_encode(array('msg' => 1, 's_msg' => $getrecord_detail));
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

	public function bulkitem_upload_section_sets()
	{
		if ($_POST) {
			$filename = $_FILES['files']['name'];
			if (!empty($filename)) {

				//print_r($data_upload);
				$this->load->helper(array('form', 'url'));
				$this->load->library('upload');

				$config['upload_path'] = realpath('upload_file/bulk_file/');
				$config['allowed_types'] = 'xls|xlsx';
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

					$this->load->library('excel');

					if ($file_extension == ".xls") {
						$objReader = PHPExcel_IOFactory::createReader('Excel5');     //For excel 2003
					} elseif ($file_extension == ".xlsx") {
						$objReader = PHPExcel_IOFactory::createReader('Excel2007');    // For excel 2007
					}
					//$objReader =PHPExcel_IOFactory::createReader('Excel5');     //For excel 2003
					//$objReader= PHPExcel_IOFactory::createReader('Excel2007');	// For excel 2007 	  
					//Set to read only
					$objReader->setReadDataOnly(true);
					//Load excel file
					$objPHPExcel = $objReader->load(FCPATH . 'upload_file/bulk_file/' . $filename);
					$totalrows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();   //Count Numbe of rows avalable in excel
					$objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
					//loop from first data untill last data
					//$get_err = array();
					$get_err = '';
					$get_err_cnt = 0;
					$get_success_cnt = 0;
					$sms_main_array = array();
					//$sms_string_set = "";
					//$sms_counter = 1;
					for ($i = 2; $i <= $totalrows; $i++) {
						$name_cc = $objWorksheet->getCellByColumnAndRow(1, $i)->getValue(); //Excel Column 2
						$desc_cc = $objWorksheet->getCellByColumnAndRow(2, $i)->getValue(); //Excel Column 2
						//$patient_time = $objWorksheet->getCellByColumnAndRow(9,$i)->getValue(); //Excel Column 10

						//$patient_time_set = PHPExcel_Style_NumberFormat::toFormattedString($patient_time, 'hh:mm:ss');
						//$mobile_number = str_replace(' ', '', $mobile_number);
						if ($this->admin_m->check_ccode_exist(trim($name_cc)) == TRUE) {
							$rows_array = array(
								'cc_no' => trim($name_cc),
								'cc_description' => trim($desc_cc),
								'cc_createdate' => date('Y-m-d H:i:s'),
								'cc_createby' => $this->session->userdata['uid']
							);
							$this->admin_m->common_Insertion_in_DB($rows_array, 'cost_code_master');
						}

					}
					echo json_encode(array('msg' => 1, 's_msg' => ''));

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

	public function delete_itemset($id)
	{
		if ($this->session->userdata('utype') == 1 || $this->data['templateDetails']->pt_m_costcode < 2) {
			$resultrow = $this->db->get_where('cost_code_master', array('cc_id' => $id))->row();
			if ($resultrow) {
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
				$res = $this->admin_m->check_usage('item_master', 'item_ccode_ms', $id);
				$res2 = $this->admin_m->check_usage('purchase_order_details', 'po_detail_cc', $id);

				if (!$res && !$res2) {
					if ($this->db->delete('cost_code_master', array('cc_id' => $resultrow->cc_id))) {
						$this->session->set_flashdata("success", "Record Deleted successfully");
						return redirect('admincontrol/costcode/cost_code_list');
					}
				} else {
					$this->session->set_flashdata("e_error", "This cost code is linked with items or purchase order, please unlink before delete.");
				}
				return redirect('admincontrol/costcode/cost_code_list');
			} else {
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
				return redirect('admincontrol/costcode/cost_code_list');
			}
		} else {
			redirect('default404');
		}
	}


}
