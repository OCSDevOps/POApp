<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Uom extends Admin_Controller
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
		redirect('admincontrol/uom/unit_of_measures_list');
	}

	public function unit_of_measures_list()
	{
		$this->data['getrecord_list'] = $this->db->order_by('uom_name', 'ASC')->get_where('unit_of_measure_tab')->result();
		$this->load->view('admin/uom/uom_list_view', $this->data);
	}

	public function add_new_unit_of_measures()
	{
		if ($_POST) {
			$name_um = $this->input->post("name_um");

			$this->form_validation->set_rules('name_um', 'Unit Name', 'trim|required');

			if ($this->form_validation->run() == TRUE) {
				//echo "1st";
				if ($this->admin_m->check_uom_exist($name_um) == TRUE) {
					//date_default_timezone_set("Asia/Kolkata");

					$row = array(
						'uom_name' => trim($name_um),
						'uom_createdate' => date('Y-m-d H:i:s'),
						'uom_createby' => $this->session->userdata['uid']
					);

					if ($this->admin_m->common_Insertion_in_DB($row, 'unit_of_measure_tab') == TRUE) {
						echo json_encode(array('msg' => 1, 's_msg' => ''));
					} else {
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Insert Data, Try Again.'));
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

	public function lock_unitset($uid = NULL)
	{
		if ($this->session->userdata('utype') == 1 || $this->data['templateDetails']->pt_m_uom < 3) {
			if ($uid == NULL) {
				redirect('admincontrol/uom/unit_of_measures_list');
			}
			$row_arr = array(
				'uom_status' => 0
			);
			if ($this->admin_m->common_Updation_in_DB($row_arr, 'unit_of_measure_tab', 'uom_id', $uid) == TRUE) {
				$this->session->set_flashdata("success", "Record is Locked successfully");
				redirect('admincontrol/uom/unit_of_measures_list');
			} else {
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
				redirect('admincontrol/uom/unit_of_measures_list');
			}
		} else {
			redirect('default404');
		}
	}

	public function unlock_unitset($uid = NULL)
	{
		if ($this->session->userdata('utype') == 1 || $this->data['templateDetails']->pt_m_uom < 3) {
			if ($uid == NULL) {
				redirect('admincontrol/uom/unit_of_measures_list');
			}
			$row_arr = array(
				'uom_status' => 1
			);
			if ($this->admin_m->common_Updation_in_DB($row_arr, 'unit_of_measure_tab', 'uom_id', $uid) == TRUE) {
				$this->session->set_flashdata("success", "Record is Unlocked successfully");
				redirect('admincontrol/uom/unit_of_measures_list');
			} else {
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
				redirect('admincontrol/uom/unit_of_measures_list');
			}
		} else {
			redirect('default404');
		}
	}

	public function modify_unit_of_measures()
	{
		if ($_POST) {
			$update_id_um = $this->input->post("update_id_um");
			$update_name_um = $this->input->post("update_name_um");

			$this->form_validation->set_rules('update_id_um', 'Unit ID', 'trim|required|is_natural');
			$this->form_validation->set_rules('update_name_um', 'Unit Name', 'trim|required');

			if ($this->form_validation->run() == TRUE) {
				//echo "1st";
				if ($this->admin_m->check_uom_exist($update_name_um, $update_id_um) == TRUE) {
					//date_default_timezone_set("Asia/Kolkata");

					$row = array(
						'uom_name' => trim($update_name_um),
						'uom_modifydate' => date('Y-m-d H:i:s')
					);

					if ($this->admin_m->common_Updation_in_DB($row, 'unit_of_measure_tab', 'uom_id', $update_id_um) == TRUE) {
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

	public function get_details_of_uom()
	{
		if ($_POST) {
			$name_uomid = $this->input->post("name_uomid");

			$this->form_validation->set_rules('name_uomid', 'Unit ID', 'trim|required|is_natural');

			if ($this->form_validation->run() == TRUE) {

				$getrecord_detail = $this->db->where('uom_id', $name_uomid)->get('unit_of_measure_tab')->row();
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

	public function delete_itemset($id)
	{
		if ($this->session->userdata('utype') == 1 || $this->data['templateDetails']->pt_m_uom < 2) {
			$resultrow = $this->db->get_where('unit_of_measure_tab', array('uom_id' => $id))->row();
			if ($resultrow) {
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");

				$res = $this->admin_m->check_usage('item_master', 'item_unit_ms', $id);
				$res2 = $this->admin_m->check_usage('purchase_order_details', 'porder_detail_uom', $id);

				if (!$res && !$res2) {
					if ($this->db->delete('unit_of_measure_tab', array('uom_id' => $resultrow->uom_id))) {
						$this->session->set_flashdata("success", "Record Deleted successfully");
						return redirect('admincontrol/uom/unit_of_measures_list');
					}
				} else {
					$this->session->set_flashdata("e_error", "This unit is linked with items or purchase order, please unlink before delete.");
				}
				return redirect('admincontrol/uom/unit_of_measures_list');
			} else {
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
				return redirect('admincontrol/uom/unit_of_measures_list');
			}
		} else {
			redirect('default404');
		}
	}
}
