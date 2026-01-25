<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Checklist extends Admin_Controller {
	
	 public function __construct() {
        parent::__construct();
		$this->load->model('Equipment_Model');
        //date_default_timezone_set("Asia/Kolkata");
        $this->data["u_details"] = $this->admin_m->GetDetailsofUsers($this->session->userdata['uid']);
		$this->data['templateDetails'] = $this->db->get_where('permission_master',['pt_id'=>$this->session->userdata('pt_id'),'status'=>1])->row();
    
	}

    public function index() {
		redirect('admincontrol/checklist/checklist_list_view');
    }
    
	//Main view function
    public function all_checklist_list(){
		$this->data['getrecord_list'] = $this->db->where('status',1)->order_by('cl_id','DESC')->get('checklist_master')->result();
		$this->data['users'] = $this->db->get_where('user_info',['u_type !=' => '4'])->result();
		$this->data['assets'] = $this->db->get_where('eq_master',['status'=>1])->result();
		// $this->data['taxcodes'] = $this->db->get('taxgroup_master')->result();
		$this->load->view('admin/checklist/checklist_list_view', $this->data);
	}
	
	// Add New Checklist
	public function new_checklist_submission(){
		if($_POST){
			
			$cl_name = $this->input->post("cl_name");
			$cl_frequency = $this->input->post("cl_frequency");
			$cl_eq_ids = $this->input->post("cl_eq_ids");
			$cl_user_ids = $this->input->post("cl_user_ids");
			$cl_start_date = $this->input->post("cl_start_date");

			for($i=1;$i<=$this->input->post("row_count");$i++){
				${"clitem_description".$i}=$this->input->post("clitem_description".$i);
			}

            $this->form_validation->set_rules('cl_name', 'Checklist Name', 'trim|required');
            $this->form_validation->set_rules('cl_frequency', 'Checklist Frequency', 'trim|required');
		
			for($i=1;$i<=$this->input->post("row_count");$i++){
				$this->form_validation->set_rules("clitem_description".$i, 'Required', 'trim|required');
			}
			
			if ($this->form_validation->run() == TRUE) {
					
					$row = array(
						'cl_name' => trim($cl_name),
						'cl_frequency' => trim($cl_frequency),
						'cl_eq_ids' => $this->input->post("cl_eq_ids"),
						'cl_user_ids' => $this->input->post("cl_user_ids"),
						'cl_start_date' => trim($cl_start_date),
						'created_date' => date('Y-m-d H:i:s')
					);
					
					$result = $this->Equipment_Model->insertNewCheckList($row, 'checklist_master');	
					if ($result!='FALSE')
					{
						for($i=1;$i<=$this->input->post("row_count");$i++){
							$row1 = array(
								'cl_id' => trim($result),
								'cli_item' => trim(${"clitem_description".$i}),
								'created_date' => date('Y-m-d H:i:s')
							);
							$this->Equipment_Model->insertCheckListDetails($row1, 'checklist_details');
						}

						if ($this->admin_m->get_Notification_SettingByKey('is_checklist_assigned')) {

							$template = $this->admin_m->GetEmailTemplateWithID($this->admin_m->get_Notification_SettingByKey('checklist_assigned_template'));
							$setting = $this->admin_m->get_CompanySMTP_Setting();


							$attData['checklist'] = $checklist = $this->db->get_where('checklist_master',['cl_id'=>$result,'status'=>1])->row();
							$attData['checklist_detailsets'] = $this->db->get_where('checklist_details',['cl_id'=>$result,'status'=>1])->result();
							$attData['checklist_users'] = $this->db->select('*')->from('user_info')->where_in('u_id',json_decode($checklist->cl_user_ids))->get()->result();
							$attData['checklist_equipments'] = $this->db->select('*')->from('eq_master')->where_in('eq_id',json_decode($checklist->cl_eq_ids))->get()->result();
							$attData['company'] = $this->admin_m->getCompanySetting();

							$toEmail = [];

							$checkin_user = json_decode($this->input->post("cl_user_ids"));

							if(count($checkin_user) > 0) {
								foreach($checkin_user as $key => $value) {
									$user = $this->admin_m->GetDetailsofUsers($value);
									if(isset($user)) {
										$toEmail[] = [
											'name' => $user->username,
											'email' => $user->email
										];
									}
								}
							}

							$params = [];

							$cc = [
								$template->email_cc
							];

							$data = $this->admin_m->prepareEmailBody($template->email_key, $params);

							$attachment = $this->admin_m->makePOPdf('checklist', $attData,$checklist->cl_name);
							$this->sendSMTPEmail($toEmail, $data['subject'], $data['content'], $cc, $setting, $attachment);
						}

						// if ($this->Equipment_Model->insertMaintenanceDetails($row1, 'eqm_details',$eqm_asset))
						// {
							echo json_encode(array('msg' => 1, 's_msg' => ''));
						// }
						// else{
						// 	echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Insert Data, Try Again.'));
						// }
					}
					else{
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Insert Data, Try Again.'));
					}
            }else{
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
		// 	exit;
		}else{
			redirect('default404');
		}
	}

	// get checklist details
	public function get_details_of_checklist(){
		if($_POST){
			$cl_id = $this->input->post("cl_id");

			$this->form_validation->set_rules('cl_id', 'Checklist ID', 'trim|required|is_natural');

			if ($this->form_validation->run() == TRUE) {

				$eqdetailsets = $this->db->where(['cl_id'=>$cl_id,'status',1])->get('checklist_master')->result();
				// $parr = array();
				// foreach($pdetailsets as $pditems){
				// 	$parr[] = $pditems->pdetail_user;
				// }

				$getrecord_detail = $this->db->where(['cl_id'=>$cl_id,'status'=>1])->get('checklist_details')->result();
				// $getrecord_detail1 = $this->db->where(['asset_id'=>$eq_id,'status',1])->join('eq_master','eqm_master.asset_id=eq_master.eq_id')->join('supplier_master','eqm_master.vendor_id=supplier_master.sup_id')->get('eqm_master')->result();
				// $getrecord_detail2 = $this->db->where(['eq_id'=>$eq_id,'status',1])->order_by('eqh_created_date','DESC')->get('eq_history')->result();
				if (count((array)$eqdetailsets) > 0)
				{
					echo json_encode(array('msg' => 1, 's_msg' => $eqdetailsets, 'c_msg' => $getrecord_detail));
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

	// get checklist items
	public function get_checklist_items(){
		if($_POST){
			$cl_id = $this->input->post("cl_id");

			$this->form_validation->set_rules('cl_id', 'Checklist ID', 'trim|required|is_natural');

			if ($this->form_validation->run() == TRUE) {

				$eqdetailsets = $this->db->where(['cl_id'=>$cl_id,'status'=>1])->get('checklist_details')->result();
				// $parr = array();
				// foreach($pdetailsets as $pditems){
				// 	$parr[] = $pditems->pdetail_user;
				// }

				// $getrecord_detail = $this->db->where(['cl_id'=>$cl_id,'status'=>1])->get('checklist_details')->result();
				// $getrecord_detail1 = $this->db->where(['asset_id'=>$eq_id,'status',1])->join('eq_master','eqm_master.asset_id=eq_master.eq_id')->join('supplier_master','eqm_master.vendor_id=supplier_master.sup_id')->get('eqm_master')->result();
				// $getrecord_detail2 = $this->db->where(['eq_id'=>$eq_id,'status',1])->order_by('eqh_created_date','DESC')->get('eq_history')->result();
				if (count((array)$eqdetailsets) > 0)
				{
					echo json_encode(array('msg' => 1, 's_msg' => $eqdetailsets));
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

	// Update Checklist
	public function update_checklist_submission(){
		if($_POST){
			
			$cl_id = $this->input->post("cl_id");
			$cl_name = $this->input->post("cl_name");
			$cl_frequency = $this->input->post("cl_frequency");
			$cl_eq_ids = $this->input->post("cl_eq_ids");
			$cl_user_ids = $this->input->post("cl_user_ids");
			$cl_start_date = $this->input->post("cl_start_date");

			for($i=1;$i<=$this->input->post("row_count");$i++){
				${"cli_id".$i}=$this->input->post("cli_id".$i);
				${"clitem_description".$i}=$this->input->post("clitem_description".$i);
			}

            $this->form_validation->set_rules('cl_name', 'Checklist Name', 'trim|required');
            $this->form_validation->set_rules('cl_frequency', 'Checklist Frequency', 'trim|required');
		
			for($i=1;$i<=$this->input->post("row_count");$i++){
				$this->form_validation->set_rules("clitem_description".$i, 'Required', 'trim|required');
			}
			
			if ($this->form_validation->run() == TRUE) {
					
					$row = array(
						'cl_name' => trim($cl_name),
						'cl_frequency' => trim($cl_frequency),
						'cl_eq_ids' => $this->input->post("cl_eq_ids"),
						'cl_user_ids' => $this->input->post("cl_user_ids"),
						'cl_start_date' => trim($cl_start_date),
						'modified_date' => date('Y-m-d H:i:s')
					);
					
					// $result = $this->Equipment_Model->updateMaintenance($row, 'eqm_master',$eqm_id);	
					if ($this->Equipment_Model->updateCheckList($row, 'checklist_master',$cl_id, json_decode($this->input->post("cli_delete_ids"))))
					{
						for($i=1;$i<=$this->input->post("row_count");$i++){
							$row1 = array(
								'cl_id' => trim($cl_id),
								'cli_item' => trim(${"clitem_description".$i})
							);
							$this->Equipment_Model->updateCheckListDetails($row1, 'checklist_details',$cl_id,${"cli_id".$i});
						}

						if ($this->admin_m->get_Notification_SettingByKey('is_checklist_assigned')) {

							$template = $this->admin_m->GetEmailTemplateWithID($this->admin_m->get_Notification_SettingByKey('checklist_assigned_template'));
							$setting = $this->admin_m->get_CompanySMTP_Setting();


							$attData['checklist'] = $checklist = $this->db->get_where('checklist_master',['cl_id'=>$cl_id,'status'=>1])->row();
							$attData['checklist_detailsets'] = $this->db->get_where('checklist_details',['cl_id'=>$cl_id,'status'=>1])->result();
							$attData['checklist_users'] = $this->db->select('*')->from('user_info')->where_in('u_id',json_decode($checklist->cl_user_ids))->get()->result();
							$attData['checklist_equipments'] = $this->db->select('*')->from('eq_master')->where_in('eq_id',json_decode($checklist->cl_eq_ids))->get()->result();
							$attData['company'] = $this->admin_m->getCompanySetting();

							$toEmail = [];

							$checkin_user = json_decode($this->input->post("cl_user_ids"));

							if(count($checkin_user) > 0) {
								foreach($checkin_user as $key => $value) {
									$user = $this->admin_m->GetDetailsofUsers($value);
									if(isset($user)) {
										$toEmail[] = [
											'name' => $user->username,
											'email' => $user->email
										];
									}
								}
							}

							$params = [];

							$cc = [
								$template->email_cc
							];

							$data = $this->admin_m->prepareEmailBody($template->email_key, $params);

							$attachment = $this->admin_m->makePOPdf('checklist', $attData,$checklist->cl_name);
							$this->sendSMTPEmail($toEmail, $data['subject'], $data['content'], $cc, $setting, $attachment);
						}
						// if ($this->Equipment_Model->insertMaintenanceDetails($row1, 'eqm_details',$eqm_asset))
						// {
							echo json_encode(array('msg' => 1, 's_msg' => ''));
						// }
						// else{
						// 	echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Insert Data, Try Again.'));
						// }
					}
					else{
						echo json_encode(array('msg' => 0, 'e_msg' => $result));
					}
            }else{
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
		// 	exit;
		}else{
			redirect('default404');
		}
	}
	
	// Perform Checklist
	public function perform_checklist_submission(){
		if($_POST){
			
			$cl_id = $this->input->post("cl_id");
			$cl_eq_id = $this->input->post("cl_eq_id");
			$cl_p_date = $this->input->post("cl_p_date");

            $this->form_validation->set_rules('cl_eq_id', 'Checklist Name', 'trim|required');
            $this->form_validation->set_rules('cl_p_date', 'Checklist Frequency', 'trim|required');
			
			if ($this->form_validation->run() == TRUE) {
					
					$row = array(
						'cl_id' => trim($cl_id),
						'cl_eq_id' => trim($cl_eq_id),
						'cl_p_date' => trim($cl_p_date),
						'cl_p_item_values' => $this->input->post("cl_item_values"),
						'created_date' => date('Y-m-d H:i:s')
					);
					
					$result = $this->Equipment_Model->insertNewCheckListPerformance($row, 'cl_perform_master');	
					if ($result!='FALSE')
					{


						// for($i=1;$i<=$this->input->post("row_count");$i++){
						// 	$row1 = array(
						// 		'cl_id' => trim($result),
						// 		'cli_item' => trim(${"clitem_description".$i}),
						// 		'created_date' => date('Y-m-d H:i:s')
						// 	);
						// 	$this->Equipment_Model->insertCheckListDetails($row1, 'checklist_details');
						// }
						// if ($this->Equipment_Model->insertMaintenanceDetails($row1, 'eqm_details',$eqm_asset))
						// {
							echo json_encode(array('msg' => 1, 's_msg' => $row));
						// }
						// else{
						// 	echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Insert Data, Try Again.'));
						// }
					}
					else{
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Insert Data, Try Again.'));
					}
            }else{
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
		// 	exit;
		}else{
			redirect('default404');
		}
	}

	public function delete_checklist($id)
	{
		// echo $id;
		if($this->session->userdata('utype')==1 || $this->data['templateDetails']->pt_t_rcorder<3){
			$resultrow = $this->db->get_where('checklist_master', array('cl_id' => $id))->row();
			if ($resultrow) {
				if($this->db->delete('checklist_details', array('cl_id' => $id))) {
					$this->db->delete('checklist_master', array('cl_id' => $id));
					$this->session->set_flashdata("success", "Record Deleted successfully");
					return redirect('admincontrol/checklist/all_checklist_list');
				}
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
				return redirect('admincontrol/checklist/all_checklist_list');
			} else {
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
				return redirect('admincontrol/checklist/all_checklist_list');
			}
		} else {
			redirect('default404');
		}
	}


	public function print_checklist_setpdf($cl_id)
	{
		$attData['checklist'] = $checklist = $this->db->get_where('checklist_master',['cl_id'=>$cl_id,'status'=>1])->row();
		$attData['checklist_detailsets'] = $this->db->get_where('checklist_details',['cl_id'=>$cl_id,'status'=>1])->result();
		$attData['checklist_users'] = $this->db->select('*')->from('user_info')->where_in('u_id',json_decode($checklist->cl_user_ids))->get()->result();
		$attData['checklist_equipments'] = $this->db->select('*')->from('eq_master')->where_in('eq_id',json_decode($checklist->cl_eq_ids))->get()->result();
		$attData['company'] = $this->admin_m->getCompanySetting();
		$title = $attData['checklist']->cl_name;

		return $this->admin_m->showPOPDF('checklist', $attData,$title);

	}

	
}
