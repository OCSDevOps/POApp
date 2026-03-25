<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Budget extends Admin_Controller {
	
	 public function __construct() {
        parent::__construct();
        //date_default_timezone_set("Asia/Kolkata");
        $this->load->model('Procore_Model');
        $this->data["u_details"] = $this->admin_m->GetDetailsofUsers($this->session->userdata['uid']);
    
	}
	
    public function index() {
		redirect('admincontrol/budget/budget_summary');
    }
    
    public function budget_list(){
		$data=[];
		if(isset($_POST['project']) && $_POST['project']!=''){
			$project_id=$_POST['project'];
			$data+=[
				'project_id' => $project_id
			];
		}
		if(isset($_POST['division']) && $_POST['division']!=''){
			$division_code=$_POST['division'];
			$data+=[
				'division_code' => $division_code
			];
		}
		$this->data['getbudget_list'] = $this->db->order_by('bli_id','ASC')->join('project_master','budget_line_item_master.project_id=project_master.procore_project_id')->get_where('budget_line_item_master',$data)->result();
		$this->data['getproject_list'] = $this->db->select('distinct(project_id),project_master.proj_name')->join('project_master','budget_line_item_master.project_id=project_master.procore_project_id')->get_where('budget_line_item_master')->result();
		$this->data['getdivision_list'] = $this->db->select('distinct(division_code),division_name')->get_where('budget_line_item_master')->result();
		$this->load->view('admin/budget/budget_list_view', $this->data);
	}

    public function budget_summary(){
		$this->data['getsummary_list'] = $this->db->order_by('bs_id','ASC')->join('project_master','budget_summary_master.project_id=project_master.procore_project_id')->get_where('budget_summary_master',['bs_status'=>1])->result();
        $this->load->view('admin/budget/budget_summary_view', $this->data);
    }
	
	// public function add_new_unit_of_measures(){
	// 	if($_POST){
	// 		$name_um = $this->input->post("name_um");
            
    //         $this->form_validation->set_rules('name_um', 'Unit Name', 'trim|required');
			
	// 		if ($this->form_validation->run() == TRUE) {
    //             	//echo "1st";
	// 			if($this->admin_m->check_uom_exist($name_um) == TRUE)
	// 			{			
	// 				//date_default_timezone_set("Asia/Kolkata");
					
	// 				$row = array(
	// 						'uom_name' => trim($name_um),
	// 						'uom_createdate' => date('Y-m-d H:i:s'),
	// 						'uom_createby' => $this->session->userdata['uid']
	// 					);
						
	// 				if ($this->admin_m->common_Insertion_in_DB($row, 'unit_of_measure_tab') == TRUE)
	// 				{
	// 					echo json_encode(array('msg' => 1, 's_msg' => ''));
	// 				}
	// 				else{
	// 					echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Insert Data, Try Again.'));
	// 				}
	// 			}
	// 			else
	// 			{
	// 				echo json_encode(array('msg' => 0, 'e_msg' => 'Unit Name already Exist, please check it.'));
	// 			}
    //         }else{
	// 			echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
	// 		}
	// 		exit;
	// 	}else{
	// 		redirect('default404');
	// 	}
	// }
	
	// public function lock_unitset($uid = NULL){
	// 	if($uid == NULL){
	// 		redirect('admincontrol/uom/unit_of_measures_list');
	// 	}
	// 	$row_arr = array(
	// 		'uom_status' => 0
	// 	);
	// 	if($this->admin_m->common_Updation_in_DB($row_arr,'unit_of_measure_tab', 'uom_id', $uid) == TRUE)
	// 	{
	// 		$this->session->set_flashdata("success","Record is Locked successfully");
	// 	    redirect('admincontrol/uom/unit_of_measures_list');
	// 	}
	// 	else
	// 	{
	// 		$this->session->set_flashdata("e_error","There is some Problem. Please try again.");
	// 	    redirect('admincontrol/uom/unit_of_measures_list');
	// 	}
	// }
	
	// public function unlock_unitset($uid = NULL){
	// 	if($uid == NULL){
	// 		redirect('admincontrol/uom/unit_of_measures_list');
	// 	}
	// 	$row_arr = array(
	// 		'uom_status' => 1
	// 	);
	// 	if($this->admin_m->common_Updation_in_DB($row_arr,'unit_of_measure_tab', 'uom_id', $uid) == TRUE)
	// 	{
	// 		$this->session->set_flashdata("success","Record is Unlocked successfully");
	// 	    redirect('admincontrol/uom/unit_of_measures_list');
	// 	}
	// 	else
	// 	{
	// 		$this->session->set_flashdata("e_error","There is some Problem. Please try again.");
	// 	    redirect('admincontrol/uom/unit_of_measures_list');
	// 	}
	// }

	// public function modify_unit_of_measures(){
	// 	if($_POST){
	// 		$update_id_um = $this->input->post("update_id_um");
	// 		$update_name_um = $this->input->post("update_name_um");
            
    //         $this->form_validation->set_rules('update_id_um', 'Unit ID', 'trim|required|is_natural');
    //         $this->form_validation->set_rules('update_name_um', 'Unit Name', 'trim|required');
			
	// 		if ($this->form_validation->run() == TRUE) {
    //             	//echo "1st";
	// 			if($this->admin_m->check_uom_exist($update_name_um, $update_id_um) == TRUE)
	// 			{			
	// 				//date_default_timezone_set("Asia/Kolkata");
					
	// 				$row = array(
	// 						'uom_name' => trim($update_name_um),
	// 						'uom_modifydate' => date('Y-m-d H:i:s')
	// 					);
						
	// 				if ($this->admin_m->common_Updation_in_DB($row, 'unit_of_measure_tab', 'uom_id', $update_id_um) == TRUE)
	// 				{
	// 					echo json_encode(array('msg' => 1, 's_msg' => ''));
	// 				}
	// 				else{
	// 					echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Update Data, Try Again.'));
	// 				}
	// 			}
	// 			else
	// 			{
	// 				echo json_encode(array('msg' => 0, 'e_msg' => 'Unit Name already Exist, please check it.'));
	// 			}
    //         }else{
	// 			echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
	// 		}
	// 		exit;
	// 	}else{
	// 		redirect('default404');
	// 	}
	// }
	
	// public function get_details_of_uom(){
	// 	if($_POST){
	// 		$name_uomid = $this->input->post("name_uomid");
            
    //         $this->form_validation->set_rules('name_uomid', 'Unit ID', 'trim|required|is_natural');
			
	// 		if ($this->form_validation->run() == TRUE) {
                		
	// 				$getrecord_detail = $this->db->where('uom_id',$name_uomid)->get('unit_of_measure_tab')->row();
	// 				if (count((array)$getrecord_detail) > 0)
	// 				{
	// 					echo json_encode(array('msg' => 1, 's_msg' => $getrecord_detail));
	// 				}
	// 				else{
	// 					echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Retrieve Data, Try Again.'));
	// 				}

    //         }else{
	// 			echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
	// 		}
	// 		exit;
	// 	}else{
	// 		redirect('default404');
	// 	}	
	// }
}
