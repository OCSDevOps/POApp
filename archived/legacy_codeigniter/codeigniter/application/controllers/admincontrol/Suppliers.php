<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Suppliers extends Admin_Controller
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
		redirect('admincontrol/suppliers/supplier_list');
	}

	public function supplier_list()
	{
		$this->data['getrecord_list'] = $this->db->order_by('sup_id', 'DESC')->get_where('supplier_master')->result();
		$this->load->view('admin/supplier/supplier_list_view', $this->data);
	}

	public function add_new_supplier_sets()
	{
		if ($_POST) {
			$name_supp = $this->input->post("name_supp");
			$name_supp_cp = $this->input->post("name_supp_cp");
			$supp_phone = $this->input->post("supp_phone");
			$supp_email = $this->input->post("supp_email");
			$supp_address = $this->input->post("supp_address");

			$this->form_validation->set_rules('name_supp', 'Supplier Name', 'trim|required');
			$this->form_validation->set_rules('name_supp_cp', 'Contact Person Name', 'trim|required');
			$this->form_validation->set_rules('supp_phone', 'Mobile No.', 'trim|required');
			$this->form_validation->set_rules('supp_email', 'Email ID', 'trim|required|valid_email');
			$this->form_validation->set_rules('supp_address', 'Address', 'trim|required');

			if ($this->form_validation->run() == TRUE) {
				//echo "1st";
				if ($this->admin_m->check_suppliers_exist($name_supp) == TRUE) {
					//date_default_timezone_set("Asia/Kolkata");

					$row = array(
						'sup_name' => trim($name_supp),
						'sup_contact_person' => trim($name_supp_cp),
						'sup_phone' => trim($supp_phone),
						'sup_email' => trim($supp_email),
						'sup_address' => trim($supp_address),
						'sup_createdate' => date('Y-m-d H:i:s'),
						'sup_createby' => $this->session->userdata['uid']
					);

					if ($this->admin_m->common_Insertion_in_DB($row, 'supplier_master') == TRUE) {
						echo json_encode(array('msg' => 1, 's_msg' => ''));
					} else {
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Insert Data, Try Again.'));
					}
				} else {
					echo json_encode(array('msg' => 0, 'e_msg' => 'Supplier Name already Exist, please check it.'));
				}
			} else {
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		} else {
			redirect('default404');
		}
	}

	public function lock_supplier($uid = NULL)
	{
		if ($this->session->userdata('utype') == 1 || $this->data['templateDetails']->pt_m_suppliers < 3) {
			if ($uid == NULL) {
				redirect('admincontrol/suppliers/supplier_list');
			}
			$row_arr = array(
				'sup_status' => 0
			);
			if ($this->admin_m->common_Updation_in_DB($row_arr, 'supplier_master', 'sup_id', $uid) == TRUE) {
				$this->session->set_flashdata("success", "Record is Locked successfully");
				redirect('admincontrol/suppliers/supplier_list');
			} else {
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
				redirect('admincontrol/suppliers/supplier_list');
			}
		} else {
			redirect('default404');
		}
	}

	public function unlock_supplier($uid = NULL)
	{
		if ($this->session->userdata('utype') == 1 || $this->data['templateDetails']->pt_m_suppliers < 3) {
			if ($uid == NULL) {
				redirect('admincontrol/suppliers/supplier_list');
			}
			$row_arr = array(
				'sup_status' => 1
			);
			if ($this->admin_m->common_Updation_in_DB($row_arr, 'supplier_master', 'sup_id', $uid) == TRUE) {
				$this->session->set_flashdata("success", "Record is Unlocked successfully");
				redirect('admincontrol/suppliers/supplier_list');
			} else {
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
				redirect('admincontrol/suppliers/supplier_list');
			}
		} else {
			redirect('default404');
		}
	}

	public function modify_suppliers_sets()
	{
		if ($_POST) {
			$update_id_sup = $this->input->post("update_id_sup");
			$update_name_supp = $this->input->post("update_name_supp");
			$update_name_supp_cp = $this->input->post("update_name_supp_cp");
			$update_supp_phone = $this->input->post("update_supp_phone");
			$update_supp_email = $this->input->post("update_supp_email");
			$update_supp_address = $this->input->post("update_supp_address");

			$this->form_validation->set_rules('update_id_sup', 'Supplier ID', 'trim|required');
			$this->form_validation->set_rules('update_name_supp', 'Supplier Name', 'trim|required');
			$this->form_validation->set_rules('update_name_supp_cp', 'Contact Person Name', 'trim|required');
			$this->form_validation->set_rules('update_supp_phone', 'Mobile No.', 'trim|required');
			$this->form_validation->set_rules('update_supp_email', 'Email ID', 'trim|required|valid_email');
			$this->form_validation->set_rules('update_supp_address', 'Address', 'trim|required');

			if ($this->form_validation->run() == TRUE) {
				//echo "1st";
				if ($this->admin_m->check_suppliers_exist($update_name_supp, $update_id_sup) == TRUE) {
					//date_default_timezone_set("Asia/Kolkata");

					$row = array(
						'sup_name' => trim($update_name_supp),
						'sup_contact_person' => trim($update_name_supp_cp),
						'sup_phone' => trim($update_supp_phone),
						'sup_email' => trim($update_supp_email),
						'sup_address' => trim($update_supp_address),
						'sup_modifydate' => date('Y-m-d H:i:s'),
						'sup_modifyby' => $this->session->userdata['uid']
					);

					if ($this->admin_m->common_Updation_in_DB($row, 'supplier_master', 'sup_id', $update_id_sup) == TRUE) {
						echo json_encode(array('msg' => 1, 's_msg' => ''));
					} else {
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Update Data, Try Again.'));
					}
				} else {
					echo json_encode(array('msg' => 0, 'e_msg' => 'Supplier Name already Exist, please check it.'));
				}
			} else {
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		} else {
			redirect('default404');
		}
	}

	public function get_details_of_suppliers()
	{
		if ($_POST) {
			$name_supid = $this->input->post("name_supid");

			$this->form_validation->set_rules('name_supid', 'Supplier ID', 'trim|required|is_natural');

			if ($this->form_validation->run() == TRUE) {

				$getrecord_detail = $this->db->where('sup_id', $name_supid)->get('supplier_master')->row();
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

		if ($this->session->userdata('utype') == 1 || $this->data['templateDetails']->pt_m_suppliers < 2) {
			$resultrow = $this->db->get_where('supplier_master', array('sup_id' => $id))->row();
			if ($resultrow) {
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");

				$res = $this->admin_m->check_usage('supplier_catalog_tab', 'supcat_supplier', $id);
				$res2 = $this->admin_m->check_usage('purchase_order_master', 'porder_supplier_ms', $id);

				if (!$res && !$res2) {
					if ($this->db->delete('supplier_master', array('sup_id' => $resultrow->sup_id))) {
						$this->session->set_flashdata("success", "Record Deleted successfully");
						return redirect('admincontrol/suppliers/supplier_list');
					}
				} else {
					$this->session->set_flashdata("e_error", "This supplier is linked with supplier catalog or purchase order, please unlink before delete.");
				}
				return redirect('admincontrol/suppliers/supplier_list');
			} else {
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
				return redirect('admincontrol/suppliers/supplier_list');
			}
		} else {
			redirect('default404');
		}
	}

}
