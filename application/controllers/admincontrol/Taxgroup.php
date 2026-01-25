<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Taxgroup extends Admin_Controller {
	
	 public function __construct() {
        parent::__construct();
        //date_default_timezone_set("Asia/Kolkata");
		$this->data['templateDetails'] = $this->db->get_where('permission_master',['pt_id'=>$this->session->userdata('pt_id'),'status'=>1])->row();
	}
	
    public function index() {
		redirect('admincontrol/taxgroup/tax_group_list');
    }
    
    public function tax_group_list() {
		$this->data['getrecord_list'] = $this->db->order_by('id','DESC')->get_where('taxgroup_master')->result();
		$this->load->view('admin/taxgroup/taxgroup_list_view', $this->data);
	}

	public function add_new_taxgroup_sets(){
		if($_POST){
			$name = $this->input->post("name");
			$percentage = $this->input->post("percentage");
			$description = $this->input->post("description");

			$this->form_validation->set_rules('name', 'Tax Group Name', 'trim|required');
			$this->form_validation->set_rules('percentage', 'Tax Group Percentage', 'trim|required');

			if ($this->form_validation->run() == TRUE) {
				//echo "1st";
				if($this->admin_m->check_taxgroup_exist($name) == TRUE)
				{
					//date_default_timezone_set("Asia/Kolkata");

					$row = array(
						'name' => trim($name),
						'percentage' => trim($percentage),
						'description' => trim($description),
						'created_at' => date('Y-m-d H:i:s'),
					);

					if ($this->admin_m->common_Insertion_in_DB($row, 'taxgroup_master') == TRUE)
					{
						echo json_encode(array('msg' => 1, 's_msg' => ''));
					}
					else{
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Insert Data, Try Again.'));
					}
				}
				else
				{
					echo json_encode(array('msg' => 0, 'e_msg' => 'Tax Group Name already Exist, please check it.'));
				}
			}else{
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		}else{
			redirect('default404');
		}
	}

	public function get_details_of_taxgroup(){
		if($_POST){
			$id = $this->input->post("id");

			$this->form_validation->set_rules('id', 'Tax Group ID', 'trim|required|is_natural');

			if ($this->form_validation->run() == TRUE) {

				$getrecord_detail = $this->db->where('id',$id)->get('taxgroup_master')->row();
				if (count((array)$getrecord_detail) > 0)
				{
					echo json_encode(array('msg' => 1, 's_msg' => $getrecord_detail));
				}
				else{
					echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Retrieve Data, Try Again.'));
				}

			}else{
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		}else{
			redirect('default404');
		}
	}

	public function modify_taxgroup_sets(){
		if($_POST){
			$update_id_taxgroup = $this->input->post("update_id_taxgroup");
			$update_name = $this->input->post("update_name");
			$update_description = $this->input->post("update_description");
			$update_percentage = $this->input->post("update_percentage");

			$this->form_validation->set_rules('update_id_taxgroup', 'Tax Group ID', 'trim|required');
			$this->form_validation->set_rules('update_name', 'Tax Group Name', 'trim|required');
			$this->form_validation->set_rules('update_percentage', 'Tax Group Percentage', 'trim|required');

			if ($this->form_validation->run() == TRUE) {
				//echo "1st";
				if($this->admin_m->check_taxgroup_exist($update_name, $update_id_taxgroup) == TRUE)
				{
					//date_default_timezone_set("Asia/Kolkata");

					$row = array(
						'name' => trim($update_name),
						'percentage' => trim($update_percentage),
						'description' => trim($update_description),
					);

					if ($this->admin_m->common_Updation_in_DB($row, 'taxgroup_master', 'id', $update_id_taxgroup) == TRUE)
					{
						echo json_encode(array('msg' => 1, 's_msg' => ''));
					}
					else{
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Update Data, Try Again.'));
					}
				}
				else
				{
					echo json_encode(array('msg' => 0, 'e_msg' => 'Tax Group Name already Exist, please check it.'));
				}
			}else{
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		}else{
			redirect('default404');
		}
	}

	public function delete_record($id) {

		if($this->session->userdata('utype')==1 || $this->data['templateDetails']->pt_m_suppliers<2){
			if($this->db->delete('taxgroup_master', array('id' => $id)))
			{
				$this->session->set_flashdata("success","Tax Group is Removed successfully");
				redirect('admincontrol/taxgroup/tax_group_list','refresh');
			}
			else
			{
				$this->session->set_flashdata("e_error","There is some Problem. Please try again.");
				redirect('admincontrol/taxgroup/tax_group_list','refresh');
			}
		}else{
			redirect('default404');
		}
	}
}
