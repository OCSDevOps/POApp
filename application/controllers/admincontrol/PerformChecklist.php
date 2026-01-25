<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class PerformChecklist extends Admin_Controller {
	
	 public function __construct() {
        parent::__construct();
		$this->load->model('Equipment_Model');
        //date_default_timezone_set("Asia/Kolkata");
        $this->data["u_details"] = $this->admin_m->GetDetailsofUsers($this->session->userdata['uid']);
		$this->data['templateDetails'] = $this->db->get_where('permission_master',['pt_id'=>$this->session->userdata('pt_id'),'status'=>1])->row();
    
	}

    public function index() {
		redirect('admincontrol/perform_checklist/perform_checklist_list_view');
    }
    
	//Main view function
    public function all_checklist_list(){
		$this->data['getrecord_list'] = $this->db->where('status',1)->order_by('cl_id','DESC')->get('checklist_master')->result();
		$this->data['users'] = $users = $this->db->get_where('user_info',['u_type <=' => '2'])->result();
		$this->data['usersArray']=[];
		foreach($users as $user){
			$this->data['usersArray']+=[
				$user->u_id => $user->firstname.' '.$user->lastname
			];
		}
		$this->data['assets'] = $this->db->get_where('eq_master',['status'=>1])->result();
		// $this->data['taxcodes'] = $this->db->get('taxgroup_master')->result();
		$this->load->view('admin/perform_checklist/perform_checklist_list_view', $this->data);
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
	
	// Perform Checklist
	public function perform_checklist_submission(){
		if($_POST){
			
			$cl_id = $this->input->post("cl_id");
			$cl_eq_id = $this->input->post("cl_eq_id");
			$cl_p_date = $this->input->post("cl_p_date");

			for($i=1;$i<=$this->input->post("row_count");$i++){
				${"cl_pd_cli_id".$i}=$this->input->post("perform_clitem_id".$i);
				${"cl_pd_cli_value".$i}=$this->input->post("perform_clitem_value".$i);
				${"cl_pd_cli_notes".$i}=$this->input->post("perform_clitem_notes".$i);
				if(isset($_FILES["perform_clitem_attachment".$i])){
					${"cl_pd_cli_attachment".$i}=$_FILES["perform_clitem_attachment".$i]['name'];
				}else{
					${"cl_pd_cli_attachment".$i} = '';
				}

				if (!empty(${"cl_pd_cli_attachment".$i})) {

					$config['upload_path'] = realpath('upload_file/checklist_perform/');
					$config['allowed_types'] = 'jpg|png|jpeg|JPG|PNG|JPEG';
					$config['overwrite'] = TRUE;
					$config['remove_spaces'] = TRUE;
					$config['max_size'] = '5000';
					$config['file_name'] = date('His') . ${"cl_pd_cli_attachment".$i};
	
					$this->load->library('upload', $config);
					$this->upload->initialize($config);
	
					if ($this->upload->do_upload("perform_clitem_attachment".$i)) {
						$upload_data = $this->upload->data();
	
						${"cl_pd_cli_attachment".$i} = $upload_data['file_name'];
					}else {
						$this->data["error"] = $this->upload->display_errors();
					}
				} else {
					${"cl_pd_cli_attachment".$i} = null;
				}
			}

            $this->form_validation->set_rules('cl_eq_id', 'Checklist Name', 'trim|required');
            $this->form_validation->set_rules('cl_p_date', 'Checklist Frequency', 'trim|required');
			
			if ($this->form_validation->run() == TRUE) {
					
					$row = array(
						'cl_id' => trim($cl_id),
						'cl_eq_id' => trim($cl_eq_id),
						'cl_p_date' => trim($cl_p_date),
						'created_date' => date('Y-m-d H:i:s')
					);
					
					$result = $this->Equipment_Model->insertNewCheckListPerformance($row, 'cl_perform_master');	
					if ($result!='FALSE')
					{
						for($i=1;$i<=$this->input->post("row_count");$i++){
							$row1 = array(
								'cl_p_id' => trim($result),
								'cl_pd_cli_id' => trim(${"cl_pd_cli_id".$i}),
								'cl_pd_cli_value' => trim(${"cl_pd_cli_value".$i}),
								'cl_pd_cli_notes' => trim(${"cl_pd_cli_notes".$i}),
								'cl_pd_cli_attachment' => trim(${"cl_pd_cli_attachment".$i}),
								'created_date' => date('Y-m-d H:i:s')
							);
							$this->Equipment_Model->insertNewCheckListPerformanceDetails($row1, 'cl_perform_details');
						}

						if ($this->admin_m->get_Notification_SettingByKey('is_checklist_performed')) {

							$template = $this->admin_m->GetEmailTemplateWithID($this->admin_m->get_Notification_SettingByKey('checklist_performed_template'));
							$setting = $this->admin_m->get_CompanySMTP_Setting();

							$attData['checklist_perform'] = $getPermormedChecklistData = $this->db->get_where('cl_perform_master',['cl_p_id'=>$result,'status'=>1])->row();
							$attData['checklist'] = $checklist = $this->db->get_where('checklist_master',['cl_id'=>$getPermormedChecklistData->cl_id,'status'=>1])->row();
							$attData['checklist_perform_detailsets'] = $this->db->join('checklist_details','checklist_details.cli_id=cl_perform_details.cl_pd_cli_id')->get_where('cl_perform_details',['cl_perform_details.cl_p_id'=>$result,'cl_perform_details.status'=>1])->result();
							$attData['checklist_user'] = $this->db->select('*')->from('user_info')->where('u_id',$this->session->userdata('uid'))->get()->row();
							$attData['checklist_equipment'] = $this->db->select('*')->from('eq_master')->where('eq_id',$getPermormedChecklistData->cl_eq_id)->get()->row();
							$attData['company'] = $this->admin_m->getCompanySetting();

							$toEmail = [];

							$checkin_user = json_decode($this->admin_m->get_Notification_SettingByKey('notify_checklist_performed_users'));

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
							$attachment = $this->admin_m->makePOPdf('checklist_perform', $attData,$checklist->cl_name);
							$this->sendSMTPEmail($toEmail, $data['subject'], $data['content'], $cc, $setting, $attachment);
						}

						// if ($this->Equipment_Model->insertMaintenanceDetails($row1, 'eqm_details',$eqm_asset))
						// {
							echo json_encode(array('msg' => 1, 's_msg' => $cl_pd_cli_attachment1));
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

	
}
