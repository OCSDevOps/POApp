<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Projects extends Admin_Controller {
	
	 public function __construct() {
        parent::__construct();
        //date_default_timezone_set("Asia/Kolkata");
        $this->data["u_details"] = $this->admin_m->GetDetailsofUsers($this->session->userdata['uid']);
		$this->data['templateDetails'] = $this->db->get_where('permission_master',['pt_id'=>$this->session->userdata('pt_id'),'status'=>1])->row();
    
	}
	
    public function index() {
		redirect('admincontrol/projects/all_project_list');
    }
    
    public function all_project_list(){
		$this->data['getrecord_list'] = $this->db->order_by('proj_id','DESC')->get('project_master')->result();
		$this->data['usr_list'] = $this->db->join('master_user_type','master_user_type.mu_id=user_info.u_type')->order_by('firstname','ASC')->where(['status'=>'1','u_type !='=>4])->get('user_info')->result();
		$this->load->view('admin/project/project_list_view', $this->data);
	}
	
	public function add_new_project(){
		if($this->session->userdata('utype')==1 || $this->data['templateDetails']->pt_m_projects<3){
			$this->data['usr_list'] = $this->db->order_by('firstname','ASC')->where('u_type != 1')->where('status','1')->get('user_views')->result();
			$this->load->view('admin/project/add_project', $this->data);
		}else{
			redirect('default404');
		}
	}
	
	public function new_project_submission(){
		if($_POST){
			
			$pr_no = $this->input->post("pr_no");
			$pr_name = $this->input->post("pr_name");
			$pr_address = $this->input->post("pr_address");
			$pr_desc = $this->input->post("pr_desc");
			$pr_accountant = $this->input->post("pr_accountant");
			$pr_manager = $this->input->post("pr_manager");
			$pr_coordinator = $this->input->post("pr_coordinator");
			$pr_supervisor = $this->input->post("pr_supervisor");
			$pr_site_coordinator = $this->input->post("pr_site_coordinator");
			$billing_name = $this->input->post("billing_name");
			$billing_address = $this->input->post("billing_address");

            $this->form_validation->set_rules('pr_no', 'Project No.', 'trim|required');
            $this->form_validation->set_rules('pr_name', 'Project Name', 'trim|required');
            $this->form_validation->set_rules('pr_address', 'Project Address', 'trim|required');
            $this->form_validation->set_rules('pr_desc', 'Project Description', 'trim|required');

			if ($this->form_validation->run() == TRUE) {
                	//echo "1st";
				if($this->admin_m->check_project_nos_exist($pr_no) == TRUE)
				{			
					//date_default_timezone_set("Asia/Kolkata");
					$manager_arr = explode(",", $pr_manager);
					$accountant_arr = explode(",", $pr_accountant);
					$coordinator_arr = explode(",", $pr_coordinator);
					$supervisor_arr = explode(",", $pr_supervisor);
					$site_coordinator_arr = explode(",", $pr_site_coordinator);

					$row = array(
							'proj_number' => trim($pr_no),
							'proj_name' => trim($pr_name),
							'proj_address' => trim($pr_address),
							'proj_description' => trim($pr_desc),
							'proj_contact' => count($manager_arr) + count($accountant_arr) + count($coordinator_arr) + count($supervisor_arr) + count($site_coordinator_arr),
							'proj_createdate' => date('Y-m-d H:i:s'),
							'proj_createby' => $this->session->userdata['uid'],
							'billing_name' => $billing_name,
							'billing_address' => $billing_address
						);
					
					$rowids = $this->admin_m->common_Insertion_in_DB_with_ID($row, 'project_master');	
					if ($rowids != FALSE)
					{
						$detail_counter = 0;
						for($ii = 0; $ii < count($manager_arr); $ii++){
							$row2 = array(
								'pdetail_proj_ms' => $rowids,
								'pdetail_manager' => $manager_arr[$ii],
								'pdetail_createdate' => date('Y-m-d H:i:s')
							);
							if ($this->admin_m->common_Insertion_in_DB($row2, 'project_details') == FALSE) {
								$detail_counter++;
							}
						}

						for($ii = 0; $ii < count($accountant_arr); $ii++){
							$row2 = array(
								'pdetail_proj_ms' => $rowids,
								'pdetail_accountant' => $accountant_arr[$ii],
								'pdetail_createdate' => date('Y-m-d H:i:s')
							);
							if ($this->admin_m->common_Insertion_in_DB($row2, 'project_details') == FALSE) {
								$detail_counter++;
							}
						}

						for($ii = 0; $ii < count($coordinator_arr); $ii++){
							$row2 = array(
								'pdetail_proj_ms' => $rowids,
								'pdetail_coordinator' => $coordinator_arr[$ii],
								'pdetail_createdate' => date('Y-m-d H:i:s')
							);
							if ($this->admin_m->common_Insertion_in_DB($row2, 'project_details') == FALSE) {
								$detail_counter++;
							}
						}

						for($ii = 0; $ii < count($supervisor_arr); $ii++){
							$row2 = array(
								'pdetail_proj_ms' => $rowids,
								'pdetail_supervisor' => $supervisor_arr[$ii],
								'pdetail_createdate' => date('Y-m-d H:i:s')
							);
							if ($this->admin_m->common_Insertion_in_DB($row2, 'project_details') == FALSE) {
								$detail_counter++;
							}
						}

						for($ii = 0; $ii < count($site_coordinator_arr); $ii++){
							$row2 = array(
								'pdetail_proj_ms' => $rowids,
								'pdetail_site_coordinator' => $site_coordinator_arr[$ii],
								'pdetail_createdate' => date('Y-m-d H:i:s')
							);
							if ($this->admin_m->common_Insertion_in_DB($row2, 'project_details') == FALSE) {
								$detail_counter++;
							}
						}

						if($detail_counter == 0){
							echo json_encode(array('msg' => 1, 's_msg' => ''));
						}else{
							$this->db->delete('project_master', array('proj_id' => $rowids));
							$this->db->delete('project_details', array('pdetail_proj_ms' => $rowids));
							echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Insert Details Table Data, Try Again.'));
						}
					}
					else{
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Insert Data, Try Again.'));
					}
				}
				else
				{
					echo json_encode(array('msg' => 0, 'e_msg' => 'Project Number already Exist, please check it.'));
				}
            }else{
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		}else{
			redirect('default404');
		}
	}
	
	public function lock_project_set($uid = NULL){
		if($this->session->userdata('utype')==1 || $this->data['templateDetails']->pt_m_projects<3){
			if($uid == NULL){
				redirect('admincontrol/projects/all_project_list');
			}
			$row_arr = array(
				'proj_status' => 0
			);
			if($this->admin_m->common_Updation_in_DB($row_arr,'project_master', 'proj_id', $uid) == TRUE)
			{
				$this->session->set_flashdata("success","Record is Locked successfully");
				redirect('admincontrol/projects/all_project_list');
			}
			else
			{
				$this->session->set_flashdata("e_error","There is some Problem. Please try again.");
				redirect('admincontrol/projects/all_project_list');
			}
		}else{
			redirect('default404');
		}
	}
	
	public function unlock_project_set($uid = NULL){
		if($this->session->userdata('utype')==1 || $this->data['templateDetails']->pt_m_projects<3){
			if($uid == NULL){
				redirect('admincontrol/projects/all_project_list');
			}
			$row_arr = array(
				'proj_status' => 1
			);
			if($this->admin_m->common_Updation_in_DB($row_arr,'project_master', 'proj_id', $uid) == TRUE)
			{
				$this->session->set_flashdata("success","Record is Unlocked successfully");
				redirect('admincontrol/projects/all_project_list');
			}
			else
			{
				$this->session->set_flashdata("e_error","There is some Problem. Please try again.");
				redirect('admincontrol/projects/all_project_list');
			}
		}else{
			redirect('default404');
		}
	}

	public function modify_project_submission(){
		if($_POST){
			$pr_id = $this->input->post("pr_id");
			$pr_no = $this->input->post("pr_no");
			$pr_name = $this->input->post("pr_name");
			$pr_address = $this->input->post("pr_address");
			$pr_desc = $this->input->post("pr_desc");
			$pr_accountant = $this->input->post("pr_accountant");
			$pr_manager = $this->input->post("pr_manager");
			$pr_coordinator = $this->input->post("pr_coordinator");
			$pr_supervisor = $this->input->post("pr_supervisor");
			$pr_site_coordinator = $this->input->post("pr_site_coordinator");
			$update_billing_name = $this->input->post("update_billing_name");
			$update_billing_address = $this->input->post("update_billing_address");

            $this->form_validation->set_rules('pr_id', 'Project ID', 'trim|required|is_natural_no_zero');
            $this->form_validation->set_rules('pr_no', 'Project No.', 'trim|required');
            $this->form_validation->set_rules('pr_name', 'Project Name', 'trim|required');
            $this->form_validation->set_rules('pr_address', 'Project Address', 'trim|required');
            $this->form_validation->set_rules('pr_desc', 'Project Description', 'trim|required');

			if ($this->form_validation->run() == TRUE) {
                	//echo "1st";
				if($this->admin_m->check_project_nos_exist($pr_no, $pr_id) == TRUE)
				{			
					//date_default_timezone_set("Asia/Kolkata");
					$pdtl_sets = $this->db->where('proj_id',$pr_id)->get('project_master')->row();
					$manager_arr = !empty($pr_manager) ? explode(",", $pr_manager) : [];
					$accountant_arr =!empty($pr_accountant) ? explode(",", $pr_accountant) : [];
					$coordinator_arr = !empty($pr_coordinator) ? explode(",", $pr_coordinator) : [];
					$supervisor_arr = !empty($pr_supervisor) ? explode(",", $pr_supervisor) : [];
					$site_coordinator_arr = !empty($pr_site_coordinator) ? explode(",", $pr_site_coordinator) : [];

					$row = array(
							'proj_number' => trim($pr_no),
							'proj_name' => trim($pr_name),
							'proj_address' => trim($pr_address),
							'proj_description' => trim($pr_desc),
							'proj_contact' => count($manager_arr) + count($accountant_arr) + count($coordinator_arr) + count($supervisor_arr) + count($site_coordinator_arr),
							'proj_modifydate' => date('Y-m-d H:i:s'),
							'proj_modifyby' => $this->session->userdata['uid'],
							'billing_name' => $update_billing_name,
							'billing_address' => $update_billing_address
						);
						
					if ($this->admin_m->common_Updation_in_DB($row, 'project_master', 'proj_id', $pr_id) == TRUE)
					{
						//$prev_detailsets = $this->db->where('pdetail_proj_ms',$prid)->get('project_details')->result();
						$this->db->delete('project_details', array('pdetail_proj_ms' => $pr_id));
						$detail_counter = 0;

						for($ii = 0; $ii < count($manager_arr); $ii++){
							$row2 = array(
								'pdetail_proj_ms' => $pr_id,
								'pdetail_manager' => $manager_arr[$ii],
								'pdetail_createdate' => date('Y-m-d H:i:s')
							);
							if ($this->admin_m->common_Insertion_in_DB($row2, 'project_details') == FALSE) {
								$detail_counter++;
							}
						}

						for($ii = 0; $ii < count($accountant_arr); $ii++){
							$row2 = array(
								'pdetail_proj_ms' => $pr_id,
								'pdetail_accountant' => $accountant_arr[$ii],
								'pdetail_createdate' => date('Y-m-d H:i:s')
							);
							if ($this->admin_m->common_Insertion_in_DB($row2, 'project_details') == FALSE) {
								$detail_counter++;
							}
						}

						for($ii = 0; $ii < count($coordinator_arr); $ii++){
							$row2 = array(
								'pdetail_proj_ms' => $pr_id,
								'pdetail_coordinator' => $coordinator_arr[$ii],
								'pdetail_createdate' => date('Y-m-d H:i:s')
							);
							if ($this->admin_m->common_Insertion_in_DB($row2, 'project_details') == FALSE) {
								$detail_counter++;
							}
						}

						for($ii = 0; $ii < count($supervisor_arr); $ii++){
							$row2 = array(
								'pdetail_proj_ms' => $pr_id,
								'pdetail_supervisor' => $supervisor_arr[$ii],
								'pdetail_createdate' => date('Y-m-d H:i:s')
							);
							if ($this->admin_m->common_Insertion_in_DB($row2, 'project_details') == FALSE) {
								$detail_counter++;
							}
						}

						for($ii = 0; $ii < count($site_coordinator_arr); $ii++){
							$row2 = array(
								'pdetail_proj_ms' => $pr_id,
								'pdetail_site_coordinator' => $site_coordinator_arr[$ii],
								'pdetail_createdate' => date('Y-m-d H:i:s')
							);
							if ($this->admin_m->common_Insertion_in_DB($row2, 'project_details') == FALSE) {
								$detail_counter++;
							}
						}

						if($detail_counter == 0){
							echo json_encode(array('msg' => 1, 's_msg' => ''));
						}else{
							echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Update Details Data, Try Again.'));
						}
					}
					else{
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Update Data, Try Again.'));
					}
				}
				else
				{
					echo json_encode(array('msg' => 0, 'e_msg' => 'Item Code already Exist, please check it.'));
				}
            }else{
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		}else{
			redirect('default404');
		}
	}
	
	public function modify_project_sets($prid){
		
		$this->data['pdtl_list'] = $this->db->where('proj_id',$prid)->get('project_master')->row();
		$pdetailsets = $this->db->where('pdetail_proj_ms',$prid)->get('project_details')->result();
		$parr = array();
		foreach($pdetailsets as $pditems){
			$parr[] = $pditems->pdetail_user;
		}
		$this->data['pdetail_list'] = $parr;
		$this->data['usr_list'] = $this->db->order_by('firstname','ASC')->where('u_type != 1')->where('status','1')->get('user_views')->result();
		$this->load->view('admin/project/edit_project', $this->data);
	}

	public function get_details_of_projects(){
		if($_POST){
			$name_projid = $this->input->post("name_projid");

			$this->form_validation->set_rules('name_projid', 'Project ID', 'trim|required|is_natural');

			if ($this->form_validation->run() == TRUE) {

				$pdetailsets = $this->db->where('pdetail_proj_ms',$name_projid)->get('project_details')->result();
				$manager = array();
				$accountant = array();
				$coordinator = array();
				$supervisor = array();
				$site_coordinator = array();
				foreach($pdetailsets as $pditems){
					if($pditems->pdetail_manager != null)
					{
						$manager[] = $pditems->pdetail_manager;
					}
					if($pditems->pdetail_accountant != null)
					{
						$accountant[] = $pditems->pdetail_accountant;
					}
					if($pditems->pdetail_coordinator != null)
					{
						$coordinator[] = $pditems->pdetail_coordinator;
					}
					if($pditems->pdetail_supervisor != null)
					{
						$supervisor[] = $pditems->pdetail_supervisor;
					}
					if($pditems->pdetail_site_coordinator != null)
					{
						$site_coordinator[] = $pditems->pdetail_site_coordinator;
					}
				}

				$details = [
					"managers"=>$manager,
					"accountant"=>$accountant,
					"coordinator"=>$coordinator,
					"supervisor"=>$supervisor,
					"site_coordinator"=>$site_coordinator
				];

				$getrecord_detail = $this->db->where('proj_id',$name_projid)->get('project_master')->row();
				if (count((array)$getrecord_detail) > 0)
				{
					echo json_encode(array('msg' => 1, 's_msg' => $getrecord_detail,'users'=>$details));
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

	public function delete_itemset($id)
	{

		if($this->session->userdata('utype')==1 || $this->data['templateDetails']->pt_m_projects<2){
			$resultrow = $this->db->get_where('project_master', array('proj_id' => $id))->row();
			if ($resultrow) {
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
				$res = $this->admin_m->check_usage('purchase_order_master', 'porder_project_ms', $id);

				if (!$res) {
					if($this->db->delete('project_master', array('proj_id' => $resultrow->proj_id))) {
						$this->session->set_flashdata("success", "Record Deleted successfully");
						return redirect('admincontrol/projects/all_project_list');
					}
				} else {
					$this->session->set_flashdata("e_error", "This project is linked with purchase order, please unlink before delete.");
				}

				return redirect('admincontrol/projects/all_project_list');
			} else {
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
				return redirect('admincontrol/projects/all_project_list');
			}
		}else{
			redirect('default404');
		}
	}
}
