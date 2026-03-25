<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Procore extends Admin_Controller {
	
	 public function __construct() {
        parent::__construct();
		$this->load->model('Procore_Model');
        //date_default_timezone_set("Asia/Kolkata");
        $this->data["u_details"] = $this->admin_m->GetDetailsofUsers($this->session->userdata['uid']);
		$this->data['templateDetails'] = $this->db->get_where('permission_master',['pt_id'=>$this->session->userdata('pt_id'),'status'=>1])->row();
    
	}
	
    public function index() {
		redirect('admincontrol/procore/procore_view');
    }
    
    public function procore_view(){
		if($_POST){
			
			$cname = $this->input->post("cname");
            $c_address = $this->input->post("c_address");
            
            $this->form_validation->set_rules('cname', 'Company Name', 'trim|required');
            $this->form_validation->set_rules('c_address', 'Company Address', 'trim|required');
            
            if ($this->form_validation->run() == TRUE) {
            

				$filename = $_FILES['c_logo']['name'];
				
				if(empty($filename)) {
	                $this->data['error'] = "Please select a file";
	            }
	            else
				{
					$this->load->helper(array('form', 'url'));
					$this->load->library('upload');
					
					$config['upload_path'] = realpath('upload_file/company/');
					$config['allowed_types'] = 'jpg|png|jpeg|JPG|PNG|JPEG';
					$config['overwrite'] = FALSE;
					$config['remove_spaces'] = TRUE;
		            $config['max_size'] = '5000';
		            $config['file_name'] = date('His').$filename;
					
					$this->load->library('upload', $config);
					$this->upload->initialize($config);
					
					if($this->upload->do_upload('c_logo'))
					{
						$upload_data = $this->upload->data();
		            	$filename = $upload_data['file_name'];
		            	
		            	$row2 = array(
							'company_name' => trim($cname),
							'company_address' => trim($c_address),
							'company_logo' => $filename,
							'company_createdate' => date('Y-m-d H:i:s')
						);
						
						if ($this->admin_m->common_Updation_in_DB($row2, 'company_tab', 'company_id', 1) == TRUE){
		                    $this->session->set_flashdata("success","Company Details is updated successfully.");
		                    redirect('admincontrol/procore/procore_view');
		                }
		                else
		                    $this->data["error"] = "There is an error. Please try again";
					}
					else
					 	$this->data["error"] = $this->upload->display_errors();	
				}
			}
		}
		$this->data['getrecord_list'] = $this->db->where('company_id',1)->get('company_tab')->row();
		$this->data['getsmtp_record'] = $this->admin_m->get_CompanySMTP_Setting();
		$this->data['getProjects'] = json_encode($this->Procore_Model->getProjectNos());
		$this->data['getCcs'] = json_encode($this->Procore_Model->getCostCodeNos());
		$this->data['getUoms'] = json_encode($this->Procore_Model->getUoms());
		$this->data['getBlis'] = json_encode($this->Procore_Model->getBlis());
		$this->data['getBudgetSummary'] = json_encode($this->Procore_Model->getBudgetSummary());
		$this->data['getSuppliers'] = json_encode($this->Procore_Model->getSupplierNames());
		$this->data['getTaxGroups'] = json_encode($this->Procore_Model->getTaxGroupNames());
		$this->data['getRtePo'] = json_encode($this->Procore_Model->getReadyToExportCommitments());
		$this->data['getSyncedPo'] = json_encode($this->Procore_Model->getSyncedCommitments());
		$this->data['getFailedPo'] = json_encode($this->Procore_Model->getFailedCommitments());
		$this->data['getProcoreAuth'] = json_encode($this->Procore_Model->getProcoreAuth());
		$this->load->view('admin/procore/procore_set_view', $this->data);
	}

	public function updatemail_setting() {
		if($_POST){
			$smtp_host = $this->input->post("smtp_host");
			$smtp_username = $this->input->post("smtp_username");
			$smtp_password = $this->input->post("smtp_password");
			$smtp_port = $this->input->post("smtp_port");
			$smtp_encryption = $this->input->post("smtp_encryption");
			$smtp_from_address = $this->input->post("smtp_from_address");
			$smtp_from_name = $this->input->post("smtp_from_name");

			$this->form_validation->set_rules('smtp_host', 'SMTP Host', 'trim|required');
			$this->form_validation->set_rules('smtp_username', 'SMTP Username', 'trim|required');
			$this->form_validation->set_rules('smtp_password', 'SMTP Password', 'trim|required');
			$this->form_validation->set_rules('smtp_port', 'SMTP Port', 'trim|required');
			$this->form_validation->set_rules('smtp_encryption', 'SMTP Encryption', 'trim|required');
			$this->form_validation->set_rules('smtp_from_address', 'SMTP From Address', 'trim|required');
			$this->form_validation->set_rules('smtp_from_name', 'SMTP From Name', 'trim|required');

			if ($this->form_validation->run() == TRUE) {
				//echo "1st";

					$row = array(
						'smtp_host' => trim($smtp_host),
						'smtp_username' => trim($smtp_username),
						'smtp_password' => $smtp_password,
						'smtp_port' => $smtp_port,
						'smtp_encryption' => $smtp_encryption,
						'smtp_from_address' => $smtp_from_address,
						'smtp_from_name' => $smtp_from_name
					);

					if ($this->admin_m->common_InsertorUpdation_in_DB($row, 'company_setting_master',1) == TRUE)
					{
						$this->session->set_flashdata("success","SMTP Settings updated successfully");
						redirect('admincontrol/company/company_view');
					} else
					{
						$this->session->set_flashdata("e_error","There have some Problem to Insert Data, Try Again.");
						redirect('admincontrol/company/company_view');
					}

			}else{
				$this->data['getrecord_list'] = $this->db->where('company_id',1)->get('company_tab')->row();
				$this->data['getsmtp_record'] = $this->admin_m->get_CompanySMTP_Setting();
				$this->load->view('admin/company/company_set_view', $this->data);
			}
		}else{
			redirect('default404');
		}
	}

	public function updatemail_body() {
		if($_POST){
			$smtp_cc = $this->input->post("smtp_cc");
			$smtp_bcc = $this->input->post("smtp_bcc");
			$smtp_mail_body = $this->input->post("smtp_mail_body");

			$this->form_validation->set_rules('smtp_cc', 'Mail CC', 'trim|required');
			$this->form_validation->set_rules('smtp_bcc', 'Mail BCC', 'trim|required');
			$this->form_validation->set_rules('smtp_mail_body', 'Mail Body', 'trim|required');

			if ($this->form_validation->run() == TRUE) {

					$row = array(
						'smtp_cc' => trim($smtp_cc),
						'smtp_bcc' => trim($smtp_bcc),
						'smtp_mail_body' => $smtp_mail_body,

					);

				if ($this->admin_m->common_InsertorUpdation_in_DB($row, 'company_setting_master',1) == TRUE)
				{
					$this->session->set_flashdata("success","SMTP Settings updated successfully");
					redirect('admincontrol/company/company_view');
				} else
				{
					$this->session->set_flashdata("e_error","There have some Problem to Insert Data, Try Again.");
					redirect('admincontrol/company/company_view');
				}

			}else{
				$this->data['getrecord_list'] = $this->db->where('company_id',1)->get('company_tab')->row();
				$this->data['getsmtp_record'] = $this->admin_m->get_CompanySMTP_Setting();
				$this->load->view('admin/company/company_set_view', $this->data);
			}
		}else{
			redirect('default404');
		}
	}

	public function test_email() {
		echo "this istest";
		die();
		$setting = $this->admin_m->get_CompanySMTP_Setting();
		$this->load->library('email');

		$config = Array(
			'protocol' => 'smtp',
			'smtp_host' => $setting->smtp_encryption.'://'.$setting->smtp_host,
			'smtp_port' => $setting->smtp_port,
			'smtp_user' => $setting->smtp_username,
			'smtp_pass' => $setting->smtp_password,
			'mailtype'  => 'html',
			'charset'   => 'iso-8859-1'
		);

		$this->email->initialize($config);
		$this->email->set_newline("\r\n");

		$from_email = "owaisimam2@gmail.com";
		$to_email = $this->input->post('email');
		//Load email library
		$this->email->from($from_email, 'Identification');
		$this->email->to($to_email);
		$this->email->subject('Send Email Codeigniter');
		$this->email->message('The email send using codeigniter library');
		//Send mail
//		if($this->email->send())
//			$this->session->set_flashdata("email_sent","Congragulation Email Send Successfully.");
//		else
//			$this->session->set_flashdata("email_sent","You have encountered an error");
		$this->email->send();
		echo $this->email->print_debugger();
die();
		redirect('admincontrol/company/company_view');
	}

	public function add_new_costcode_set(){
		if($_POST){
			$name_cc = $this->input->post("name_cc");
			$desc_cc = $this->input->post("desc_cc");
            
            $this->form_validation->set_rules('name_cc', 'Cost Code', 'trim|required');
            $this->form_validation->set_rules('desc_cc', 'Description', 'trim|required');
			
			if ($this->form_validation->run() == TRUE) {
                	//echo "1st";
				if($this->admin_m->check_ccode_exist($name_cc) == TRUE)
				{			
					//date_default_timezone_set("Asia/Kolkata");
					
					$row = array(
							'cc_no' => trim($name_cc),
							'cc_description' => trim($desc_cc),
							'cc_createdate' => date('Y-m-d H:i:s'),
							'cc_createby' => $this->session->userdata['uid']
						);
						
					if ($this->admin_m->common_Insertion_in_DB($row, 'cost_code_master') == TRUE)
					{
						echo json_encode(array('msg' => 1, 's_msg' => ''));
					}
					else{
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Insert Data, Try Again.'));
					}
				}
				else
				{
					echo json_encode(array('msg' => 0, 'e_msg' => 'Cost Code already Exist, please check it.'));
				}
            }else{
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		}else{
			redirect('default404');
		}
	}
	
	public function lock_costcodeset($uid = NULL){
		if($uid == NULL){
			redirect('admincontrol/costcode/cost_code_list');
		}
		$row_arr = array(
			'cc_status' => 0
		);
		if($this->admin_m->common_Updation_in_DB($row_arr,'cost_code_master', 'cc_id', $uid) == TRUE)
		{
			$this->session->set_flashdata("success","Record is Locked successfully");
		    redirect('admincontrol/costcode/cost_code_list');
		}
		else
		{
			$this->session->set_flashdata("e_error","There is some Problem. Please try again.");
		    redirect('admincontrol/costcode/cost_code_list');
		}
	}
	
	public function unlock_costcodeset($uid = NULL){
		if($uid == NULL){
			redirect('admincontrol/costcode/cost_code_list');
		}
		$row_arr = array(
			'cc_status' => 1
		);
		if($this->admin_m->common_Updation_in_DB($row_arr,'cost_code_master', 'cc_id', $uid) == TRUE)
		{
			$this->session->set_flashdata("success","Record is Unlocked successfully");
		    redirect('admincontrol/costcode/cost_code_list');
		}
		else
		{
			$this->session->set_flashdata("e_error","There is some Problem. Please try again.");
		    redirect('admincontrol/costcode/cost_code_list');
		}
	}

	public function modify_costcode_sets(){
		if($_POST){
			$update_id_cc = $this->input->post("update_id_cc");
			$update_name_cc = $this->input->post("update_name_cc");
			$update_desc_cc = $this->input->post("update_desc_cc");
            
            $this->form_validation->set_rules('update_name_cc', 'Cost Code', 'trim|required');
            $this->form_validation->set_rules('update_desc_cc', 'Description', 'trim|required');
			$this->form_validation->set_rules('update_id_cc', 'Costcode ID', 'trim|required|is_natural');
            
			if ($this->form_validation->run() == TRUE) {
                	//echo "1st";
				if($this->admin_m->check_ccode_exist($update_name_cc, $update_id_cc) == TRUE)
				{			
					//date_default_timezone_set("Asia/Kolkata");
					
					$row = array(
							'cc_no' => trim($update_name_cc),
							'cc_description' => trim($update_desc_cc),
							'cc_modifydate' => date('Y-m-d H:i:s'),
							'cc_modifyby' => $this->session->userdata['uid']
						);
						
					if ($this->admin_m->common_Updation_in_DB($row, 'cost_code_master', 'cc_id', $update_id_cc) == TRUE)
					{
						echo json_encode(array('msg' => 1, 's_msg' => ''));
					}
					else{
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Update Data, Try Again.'));
					}
				}
				else
				{
					echo json_encode(array('msg' => 0, 'e_msg' => 'Unit Name already Exist, please check it.'));
				}
            }else{
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		}else{
			redirect('default404');
		}
	}
	
	public function get_details_of_ccode(){
		if($_POST){
			$name_ccid = $this->input->post("name_ccid");
            
            $this->form_validation->set_rules('name_ccid', 'Costcode ID', 'trim|required|is_natural');
			
			if ($this->form_validation->run() == TRUE) {
                		
					$getrecord_detail = $this->db->where('cc_id',$name_ccid)->get('cost_code_master')->row();
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

	// insert procore auth details
	
	function insertProcoreAuth(){
		if($_POST){
			$client_id=$_POST['client_id'];
			$client_secret=$_POST['client_secret'];
			$company_id=$_POST['company_id'];
			if ($this->Procore_Model->insertProcoreAuth($client_id,$client_secret,$company_id) == TRUE)
			{
				redirect('admincontrol/procore/procore_view');
			}
			else{
				redirect('admincontrol/procore/procore_view');
			}
		}else{
			redirect('default404');
		}
	} 

	// update procore auth details
	
	function updateProcoreAuth(){
		if($_POST){
			$client_id=$_POST['u_client_id'];
			$client_secret=$_POST['u_client_secret'];
			$company_id=$_POST['u_company_id'];
			if ($this->Procore_Model->updateProcoreAuth($client_id,$client_secret,$company_id) == TRUE)
			{
				redirect('admincontrol/procore/procore_view');
			}
			else{
				redirect('admincontrol/procore/procore_view');
			}
		}else{
			redirect('default404');
		}
	} 

	// sync projects data
	
	function syncProjects(){
		if($_POST){
			$project_id=$_POST['h-project-id'];
			$project_number=$_POST['h-project-number'];
			$name=$_POST['h-project-name'];
			$address=$_POST['h-project-address'];
			if ($this->Procore_Model->syncProjects($project_id,$project_number,$name,$address) == TRUE)
			{
				$curl = curl_init();

				curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://api.procore.com/rest/v1.0/projects/'.$_POST['h-project-id'].'/work_breakdown_structure/wbs_codes?company_id='.$_POST['h-company-id'].'',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
					'Authorization: Bearer '.$_POST['h-access-token'].'',
					'Cookie: AWSALB=1Wf/delKWrWbRrB6zI5/YSu1wFvHY2H4lARVids6eBtc3UMrC8XZ1OBgAeQmaCktWW30RG8hKQgATNYeoIRC3l4KmKtknIq+E1gteQYFP3yKTk+6yLyQmM2IMBYm; AWSALBCORS=1Wf/delKWrWbRrB6zI5/YSu1wFvHY2H4lARVids6eBtc3UMrC8XZ1OBgAeQmaCktWW30RG8hKQgATNYeoIRC3l4KmKtknIq+E1gteQYFP3yKTk+6yLyQmM2IMBYm; AWSALBTG=LordY6W6MzmH/oIrLvey4gHQ36+Cv/joXa/EGYPB9jGcZIQhbPjgDko04oN3SjA0Go46V+4v/q24RLe7g153AjIvXVsZuFuigjfXgyWIH6K83d06D318obAdWYdGS579YLKB0ZBkyqNPBW1V2grEHqha3ZyZ1TBwIPsqrAIun/+6aThuCTU=; AWSALBTGCORS=LordY6W6MzmH/oIrLvey4gHQ36+Cv/joXa/EGYPB9jGcZIQhbPjgDko04oN3SjA0Go46V+4v/q24RLe7g153AjIvXVsZuFuigjfXgyWIH6K83d06D318obAdWYdGS579YLKB0ZBkyqNPBW1V2grEHqha3ZyZ1TBwIPsqrAIun/+6aThuCTU='
				),
				));

				$response = curl_exec($curl);

				curl_close($curl);
				$costCodeData=json_decode($response,true);
				foreach($costCodeData as $cc){
					$costCodeId=$cc['segment_items'][0]['id'];
					$costCode=$cc['segment_items'][0]['code'];
					$costCodeName=$cc['segment_items'][0]['name'];
					$this->Procore_Model->syncProjectCostCodes($project_id,$name,$costCodeId,$costCode,$costCodeName);
				}

				$curl7 = curl_init();

				curl_setopt_array($curl7, array(
				CURLOPT_URL => 'https://api.procore.com/rest/v1.0/budget_views/562949953449296/summary_rows?company_id='.$_POST['h-company-id'].'&project_id='.$_POST['h-project-id'].'',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
					'Authorization: Bearer '.$_POST['h-access-token'].'',
					'Cookie: AWSALB=+6mz1ExHb+GachZgGG4hUPKN07Nc04Bt7V/0N8useGy3QS4PU3kVk+/dVlIpxtrrbeM7LfSy4hoUGxPwFH5kHtblz5t5qtty2cCQCM7LpRxUFkq7oIQYDOzwkfrg; AWSALBCORS=+6mz1ExHb+GachZgGG4hUPKN07Nc04Bt7V/0N8useGy3QS4PU3kVk+/dVlIpxtrrbeM7LfSy4hoUGxPwFH5kHtblz5t5qtty2cCQCM7LpRxUFkq7oIQYDOzwkfrg; AWSALBTG=xUQdvlcm704QxUdCR0yyYnD21oZCmsM0XaVswBRFpDDWY07KTqoiD6yMVf4lMQv7yTe911OstEGPODC4bHi6Qzs0g8xXo51mZAIMocm0buxHFkPaPNfAAu6olt6zYXhJnIg2sBlE5PV1Go9RznMt3pGzyHu8nIjL9eKjjRe9SwbwWQDWJUA=; AWSALBTGCORS=xUQdvlcm704QxUdCR0yyYnD21oZCmsM0XaVswBRFpDDWY07KTqoiD6yMVf4lMQv7yTe911OstEGPODC4bHi6Qzs0g8xXo51mZAIMocm0buxHFkPaPNfAAu6olt6zYXhJnIg2sBlE5PV1Go9RznMt3pGzyHu8nIjL9eKjjRe9SwbwWQDWJUA='
				),
				));

				$response7 = curl_exec($curl7);
				curl_close($curl7);
				$response7Data=json_decode($response7,true);
				$ProjectId=$response7Data[0]['id'];
				$originalBudget=$response7Data[0]['original_budget_amount'];
				$revisedBudget=$response7Data[0]['Revised Budget'];
				$committedCost=$response7Data[0]['Committed Costs'];
				if($this->Procore_Model->syncBudgetSummary($ProjectId,$originalBudget,$revisedBudget,$committedCost) == TRUE){
					$curl5 = curl_init();

					curl_setopt_array($curl5, array(
					CURLOPT_URL => 'https://api.procore.com/rest/v1.0/budget_views/562949953449296/detail_rows?company_id='.$_POST['h-company-id'].'&project_id='.$_POST['h-project-id'].'',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'GET',
					CURLOPT_HTTPHEADER => array(
						'Content-Type: application/json',
						'Authorization: Bearer '.$_POST['h-access-token'].'',
						'Cookie: AWSELB=C9678F371A99D572E6A6DEEE540CB386CBB813BDB12225ED167FFC6539231467CB1079A16045A69F2153C111E32E7B23CD43F7ADFEFD6421AAA944B4446E721A9B43FA179C; AWSELBCORS=C9678F371A99D572E6A6DEEE540CB386CBB813BDB12225ED167FFC6539231467CB1079A16045A69F2153C111E32E7B23CD43F7ADFEFD6421AAA944B4446E721A9B43FA179C'
					),
					));

					$response5 = curl_exec($curl5);

					curl_close($curl5);
					$response5Data=json_decode($response5,true);
					foreach($response5Data as $bli){
						$budgetId=$bli['id'];
						$projectId=$bli['project_id'];
						if(isset($bli['cost_code_level_2']) && $bli['cost_code_level_2']!=NULL && $bli['cost_code_level_3']!="" && $bli['cost_code_level_3']!="None"){
							$divisionParts = explode('-', strrev($bli['cost_code_level_2']), 2);
							$divisionName=strrev($divisionParts[0]);
							$divisionCode=strrev($divisionParts[1]);
						}else{
							$divisionName='-';
							$divisionCode='-';
						}
						if(isset($bli['cost_code_level_3']) && $bli['cost_code_level_3']!=NULL && $bli['cost_code_level_3']!="" && $bli['cost_code_level_3']!="None"){
							$ccParts = explode('-', strrev($bli['cost_code_level_3']), 2);
							$costCodeName=strrev($ccParts[0]);
							$costCode=strrev($ccParts[1]);
						}else{
							$costCodeName='-';
							$costCode='-';
						}
						$originalBudget=$bli['original_budget_amount'];
						$revisedBudget=$bli['Revised Budget'];
						$commitedCost1=$bli['Committed Costs'];
						$this->Procore_Model->syncBli($budgetId,$projectId,$divisionName,$divisionCode,$costCodeName,$costCode,$originalBudget,$revisedBudget,$commitedCost1);
					}
						redirect('admincontrol/procore/procore_view');
				}

				redirect('admincontrol/procore/procore_view');
			}
			else{
				redirect('admincontrol/procore/procore_view');
			}
		}else{
			redirect('default404');
		}
	} 

	// Re sync projects data
	
	function reSyncProjects(){
		if($_POST){
			$project_id=$_POST['h-project-id'];
			$project_number=$_POST['h-project-number'];
			$name=$_POST['h-project-name'];
			$address=$_POST['h-project-address'];
			if ($this->Procore_Model->reSyncProjects($project_id,$project_number,$name,$address) == TRUE)
			{
				$curl = curl_init();

				curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://api.procore.com/rest/v1.0/projects/'.$_POST['h-project-id'].'/work_breakdown_structure/wbs_codes?company_id='.$_POST['h-company-id'].'',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
					'Authorization: Bearer '.$_POST['h-access-token'].'',
					'Cookie: AWSALB=1Wf/delKWrWbRrB6zI5/YSu1wFvHY2H4lARVids6eBtc3UMrC8XZ1OBgAeQmaCktWW30RG8hKQgATNYeoIRC3l4KmKtknIq+E1gteQYFP3yKTk+6yLyQmM2IMBYm; AWSALBCORS=1Wf/delKWrWbRrB6zI5/YSu1wFvHY2H4lARVids6eBtc3UMrC8XZ1OBgAeQmaCktWW30RG8hKQgATNYeoIRC3l4KmKtknIq+E1gteQYFP3yKTk+6yLyQmM2IMBYm; AWSALBTG=LordY6W6MzmH/oIrLvey4gHQ36+Cv/joXa/EGYPB9jGcZIQhbPjgDko04oN3SjA0Go46V+4v/q24RLe7g153AjIvXVsZuFuigjfXgyWIH6K83d06D318obAdWYdGS579YLKB0ZBkyqNPBW1V2grEHqha3ZyZ1TBwIPsqrAIun/+6aThuCTU=; AWSALBTGCORS=LordY6W6MzmH/oIrLvey4gHQ36+Cv/joXa/EGYPB9jGcZIQhbPjgDko04oN3SjA0Go46V+4v/q24RLe7g153AjIvXVsZuFuigjfXgyWIH6K83d06D318obAdWYdGS579YLKB0ZBkyqNPBW1V2grEHqha3ZyZ1TBwIPsqrAIun/+6aThuCTU='
				),
				));

				$response = curl_exec($curl);

				curl_close($curl);
				$costCodeData=json_decode($response,true);
				foreach($costCodeData as $cc){
					$costCodeId=$cc['segment_items'][0]['id'];
					$costCode=$cc['segment_items'][0]['code'];
					$costCodeName=$cc['segment_items'][0]['name'];
					$this->Procore_Model->reSyncProjectCostCodes($project_id,$name,$costCodeId,$costCode,$costCodeName);
				}

				if($name!='Sandbox Test Project'){
					$curl7 = curl_init();

					curl_setopt_array($curl7, array(
					CURLOPT_URL => 'https://api.procore.com/rest/v1.0/budget_views/562949953449296/summary_rows?company_id='.$_POST['h-company-id'].'&project_id='.$_POST['h-project-id'].'',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'GET',
					CURLOPT_HTTPHEADER => array(
						'Authorization: Bearer '.$_POST['h-access-token'].'',
						'Cookie: AWSALB=+6mz1ExHb+GachZgGG4hUPKN07Nc04Bt7V/0N8useGy3QS4PU3kVk+/dVlIpxtrrbeM7LfSy4hoUGxPwFH5kHtblz5t5qtty2cCQCM7LpRxUFkq7oIQYDOzwkfrg; AWSALBCORS=+6mz1ExHb+GachZgGG4hUPKN07Nc04Bt7V/0N8useGy3QS4PU3kVk+/dVlIpxtrrbeM7LfSy4hoUGxPwFH5kHtblz5t5qtty2cCQCM7LpRxUFkq7oIQYDOzwkfrg; AWSALBTG=xUQdvlcm704QxUdCR0yyYnD21oZCmsM0XaVswBRFpDDWY07KTqoiD6yMVf4lMQv7yTe911OstEGPODC4bHi6Qzs0g8xXo51mZAIMocm0buxHFkPaPNfAAu6olt6zYXhJnIg2sBlE5PV1Go9RznMt3pGzyHu8nIjL9eKjjRe9SwbwWQDWJUA=; AWSALBTGCORS=xUQdvlcm704QxUdCR0yyYnD21oZCmsM0XaVswBRFpDDWY07KTqoiD6yMVf4lMQv7yTe911OstEGPODC4bHi6Qzs0g8xXo51mZAIMocm0buxHFkPaPNfAAu6olt6zYXhJnIg2sBlE5PV1Go9RznMt3pGzyHu8nIjL9eKjjRe9SwbwWQDWJUA='
					),
					));

					$response7 = curl_exec($curl7);
					curl_close($curl7);
					$response7Data=json_decode($response7,true);
					$ProjectId=$response7Data[0]['id'];
					$originalBudget=$response7Data[0]['original_budget_amount'];
					$revisedBudget=$response7Data[0]['Revised Budget'];
					$committedCost=$response7Data[0]['Committed Costs'];
					if($this->Procore_Model->syncBudgetSummary($ProjectId,$originalBudget,$revisedBudget,$committedCost) == TRUE){
						$curl5 = curl_init();

						curl_setopt_array($curl5, array(
						CURLOPT_URL => 'https://api.procore.com/rest/v1.0/budget_views/562949953449296/detail_rows?company_id='.$_POST['h-company-id'].'&project_id='.$_POST['h-project-id'].'',
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_ENCODING => '',
						CURLOPT_MAXREDIRS => 10,
						CURLOPT_TIMEOUT => 0,
						CURLOPT_FOLLOWLOCATION => true,
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						CURLOPT_CUSTOMREQUEST => 'GET',
						CURLOPT_HTTPHEADER => array(
							'Content-Type: application/json',
							'Authorization: Bearer '.$_POST['h-access-token'].'',
							'Cookie: AWSELB=C9678F371A99D572E6A6DEEE540CB386CBB813BDB12225ED167FFC6539231467CB1079A16045A69F2153C111E32E7B23CD43F7ADFEFD6421AAA944B4446E721A9B43FA179C; AWSELBCORS=C9678F371A99D572E6A6DEEE540CB386CBB813BDB12225ED167FFC6539231467CB1079A16045A69F2153C111E32E7B23CD43F7ADFEFD6421AAA944B4446E721A9B43FA179C'
						),
						));

						$response5 = curl_exec($curl5);

						curl_close($curl5);
						$response5Data=json_decode($response5,true);
						foreach($response5Data as $bli){
							$budgetId=$bli['id'];
							$projectId=$bli['project_id'];
							if(isset($bli['cost_code_level_2']) && $bli['cost_code_level_2']!=NULL && $bli['cost_code_level_3']!="" && $bli['cost_code_level_3']!="None"){
								$divisionParts = explode('-', strrev($bli['cost_code_level_2']), 2);
								$divisionName=strrev($divisionParts[0]);
								$divisionCode=strrev($divisionParts[1]);
							}else{
								$divisionName='-';
								$divisionCode='-';
							}
							if(isset($bli['cost_code_level_3']) && $bli['cost_code_level_3']!=NULL && $bli['cost_code_level_3']!="" && $bli['cost_code_level_3']!="None"){
								$ccParts = explode('-', strrev($bli['cost_code_level_3']), 2);
								$costCodeName=strrev($ccParts[0]);
								$costCode=strrev($ccParts[1]);
							}else{
								$costCodeName='-';
								$costCode='-';
							}
							$originalBudget=$bli['original_budget_amount'];
							$revisedBudget=$bli['Revised Budget'];
							$commitedCost1=$bli['Committed Costs'];
							$this->Procore_Model->syncBli($budgetId,$projectId,$divisionName,$divisionCode,$costCodeName,$costCode,$originalBudget,$revisedBudget,$commitedCost1);
						}
							redirect('admincontrol/procore/procore_view');
					}
				}

				redirect('admincontrol/procore/procore_view');
			}
			else{
				redirect('admincontrol/procore/procore_view');
			}
		}else{
			redirect('default404');
		}
	} 

	// sync cost code data
	
	function syncCostCode(){
		if($_POST){
			$cc_id=$_POST['h-cost-code-id'];
			$cc_no=$_POST['h-cost-code'];
			$name=$_POST['h-name'];
			if ($this->Procore_Model->syncCostCode($cc_id,$cc_no,$name) == TRUE)
			{
				redirect('admincontrol/procore/procore_view');
			}
			else{
				redirect('admincontrol/procore/procore_view');
			}
		}else{
			redirect('default404');
		}
	} 

	// sync all cost code data
	
	function syncAllCostCode(){
		if($_POST){
			$companyId=$_POST['h-company-id'];
			$accessToken=$_POST['h-access-token'];
			$ccArray=[];
			$ccData = json_encode($this->Procore_Model->getCostCodeNos());
			$ccData = json_decode($ccData,true);

			foreach($ccData as $c){
				array_push($ccArray,$c['procore_cc_id']);
			}
			$curl2 = curl_init();
			curl_setopt_array($curl2, array(
			CURLOPT_URL => 'https://api.procore.com/rest/v1.0/standard_cost_codes?company_id='.$companyId.'&standard_cost_code_list_id=562949953441378',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Authorization: Bearer '.$accessToken.'',
				'Cookie: AWSELB=C9678F371A99D572E6A6DEEE540CB386CBB813BDB128FC55E97BB004E7E76AD08316BF833957A1C35E9CA51FDC14011F96AF9CEA213614BBDC569F01E9493AF50411A5AD89; AWSELBCORS=C9678F371A99D572E6A6DEEE540CB386CBB813BDB128FC55E97BB004E7E76AD08316BF833957A1C35E9CA51FDC14011F96AF9CEA213614BBDC569F01E9493AF50411A5AD89'
			),
			));

			$response2 = curl_exec($curl2);

			curl_close($curl2);
			$response2Data=json_decode($response2,true);
			foreach($response2Data as $cc){
				if(!in_array($cc['id'],$ccArray) && strlen($cc['full_code'])>=7){
					$cc_id=$cc['id'];
					$cc_no=$cc['full_code'];
					$name=$cc['name'];

					$this->Procore_Model->syncCostCode($cc_id,$cc_no,$name);
				}
			}
			redirect('admincontrol/procore/procore_view');
		}else{
			redirect('default404');
		}
	} 

	// sync uom data
	
	function syncUom(){
		if($_POST){
			$name=$_POST['h-name'];

			$curl3 = curl_init();


			if ($this->Procore_Model->syncUom($name) == TRUE)
			{
				redirect('admincontrol/procore/procore_view');
			}
			else{
				redirect('admincontrol/procore/procore_view');
			}
		}else{
			redirect('default404');
		}
	}

	// sync All uom data
	
	function syncAllUom(){
		if($_POST){
			$companyId=$_POST['h-company-id'];
			$accessToken=$_POST['h-access-token'];
			$uomArray=[];
			$uomData = json_encode($this->Procore_Model->getUoms());
			$uomData = json_decode($uomData,true);

			foreach($uomData as $u){
				array_push($uomArray,$u['uom_name']);
			}

			$curl3 = curl_init();
			curl_setopt_array($curl3, array(
			CURLOPT_URL => 'https://api.procore.com/rest/v1.0/companies/'.$companyId.'/uoms',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Authorization: Bearer '.$accessToken.'',
				'Cookie: AWSELB=C9678F371A99D572E6A6DEEE540CB386CBB813BDB128FC55E97BB004E7E76AD08316BF833945A69F2153C111E32E7B23CD43F7ADFEFD6421AAA944B4446E721A9B43FA179C; AWSELBCORS=C9678F371A99D572E6A6DEEE540CB386CBB813BDB128FC55E97BB004E7E76AD08316BF833945A69F2153C111E32E7B23CD43F7ADFEFD6421AAA944B4446E721A9B43FA179C'
			),
			));
	
			$response3 = curl_exec($curl3);

			curl_close($curl3);
			$response3Data=json_decode($response3,true);
			foreach($response3Data as $uom){
				if(!in_array($uom['name'],$uomArray)){
					$name=$uom['name'];
					$this->Procore_Model->syncUom($name);
				}
			}
			redirect('admincontrol/procore/procore_view');
		}else{
			redirect('default404');
		}
	}  

	// sync Budget Line Item data
	
	function syncBli(){
		if($_POST){
			$ProjectId=$_POST['h-project-id'];
			$originalBudget=$_POST['h-original-budget'];
			$revisedBudget=$_POST['h-revised-budget'];
			$committedCost=$_POST['h-committed-cost'];
			if($this->Procore_Model->syncBudgetSummary($ProjectId,$originalBudget,$revisedBudget,$committedCost) == TRUE){
				$curl5 = curl_init();

				curl_setopt_array($curl5, array(
				CURLOPT_URL => 'https://api.procore.com/rest/v1.0/budget_views/562949953449296/detail_rows?company_id='.$_POST['h-company-id'].'&project_id='.$_POST['h-project-id'].'',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json',
					'Authorization: Bearer '.$_POST['h-access-token'].'',
					'Cookie: AWSELB=C9678F371A99D572E6A6DEEE540CB386CBB813BDB12225ED167FFC6539231467CB1079A16045A69F2153C111E32E7B23CD43F7ADFEFD6421AAA944B4446E721A9B43FA179C; AWSELBCORS=C9678F371A99D572E6A6DEEE540CB386CBB813BDB12225ED167FFC6539231467CB1079A16045A69F2153C111E32E7B23CD43F7ADFEFD6421AAA944B4446E721A9B43FA179C'
				),
				));

				$response5 = curl_exec($curl5);

				curl_close($curl5);
				$response5Data=json_decode($response5,true);
				foreach($response5Data as $bli){
					$budgetId=$bli['id'];
					$projectId=$bli['project_id'];
					if(isset($bli['cost_code_level_2']) && $bli['cost_code_level_2']!=NULL && $bli['cost_code_level_3']!="" && $bli['cost_code_level_3']!="None"){
						$divisionParts = explode('-', strrev($bli['cost_code_level_2']), 2);
						$divisionName=strrev($divisionParts[0]);
						$divisionCode=strrev($divisionParts[1]);
					}else{
						$divisionName='-';
						$divisionCode='-';
					}
					if(isset($bli['cost_code_level_3']) && $bli['cost_code_level_3']!=NULL && $bli['cost_code_level_3']!="" && $bli['cost_code_level_3']!="None"){
						$ccParts = explode('-', strrev($bli['cost_code_level_3']), 2);
						$costCodeName=strrev($ccParts[0]);
						$costCode=strrev($ccParts[1]);
					}else{
						$costCodeName='-';
						$costCode='-';
					}
					$originalBudget=$bli['original_budget_amount'];
					$revisedBudget=$bli['Revised Budget'];
					$commitedCost1=$bli['Committed Costs'];
					$this->Procore_Model->syncBli($budgetId,$ProjectId,$divisionName,$divisionCode,$costCodeName,$costCode,$originalBudget,$revisedBudget,$commitedCost1);
				}
					redirect('admincontrol/procore/procore_view');
			}
		}else{
			redirect('default404');
		}
	}

	// sync Suppliers data
	
	function syncSuppliers(){
		if($_POST){
			$supplierId=$_POST['h-id'];
			$supplierName=$_POST['h-name'];
			$supplierContact=$_POST['h-primary-contact'];
			$supplierMobile=$_POST['h-mobile'];
			$supplierEmail=$_POST['h-email'];
			$supplierAddress=$_POST['h-address'];
			$accessToken=$_POST['h-access-token'];
			$companyId=$_POST['h-company-id'];
			if ($this->Procore_Model->syncSuppliers($supplierId,$supplierName,$supplierContact,$supplierMobile,$supplierEmail,$supplierAddress) == TRUE)
			{
				$curl = curl_init();

				curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://api.procore.com/rest/v1.1/companies/'.$companyId.'/users',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
					'Authorization: Bearer '.$accessToken.'',
					'Cookie: AWSALB=wLkV6mow/HQpExfDAxyIH/bKoqTpc7KVCPzg36aoZSk/XvCh9WKhE0ZCfYb8a/TrozJW4yS6GAL4N6AM50hvRF9SueE3bhsuEKz0s7DR2EtgtfKlN8MVfx0CKd1b; AWSALBCORS=wLkV6mow/HQpExfDAxyIH/bKoqTpc7KVCPzg36aoZSk/XvCh9WKhE0ZCfYb8a/TrozJW4yS6GAL4N6AM50hvRF9SueE3bhsuEKz0s7DR2EtgtfKlN8MVfx0CKd1b; AWSALBTG=o5k2MBvzR5lk/meQxEoumPtMe/L5WmUorkDr8mLw5SZCRBM4oRsaznHFbsujEQ53sryKF0b4IA6TCxaGbfOKgYMhXO/Phh78zvENBA9hFRjsBtSU3n+sbEIXajwcHKC0r5iPEVSaaeSVbfaJPMMTAvBL5rVVRgvVp6X3eDS8jO9lpVO1x5s=; AWSALBTGCORS=o5k2MBvzR5lk/meQxEoumPtMe/L5WmUorkDr8mLw5SZCRBM4oRsaznHFbsujEQ53sryKF0b4IA6TCxaGbfOKgYMhXO/Phh78zvENBA9hFRjsBtSU3n+sbEIXajwcHKC0r5iPEVSaaeSVbfaJPMMTAvBL5rVVRgvVp6X3eDS8jO9lpVO1x5s='
				),
				));

				$response = curl_exec($curl);

				curl_close($curl);
				$usersData= json_decode($response,true);
				foreach($usersData as $user){
					if(!empty($user['vendor']['id'])){
					if($user['vendor']['id'] == $supplierId){
						// echo $user['vendor']['id'].'<br>'; 
						$contactId=$user['id']; 
						if($user['vendor']['name']=='Essence Properties'){
							$userType=2;
						}else{
							$userType=4;
						}
						$firstname=$user['first_name'];
						$lastname=$user['last_name'];
						$username=$firstname.'_'.$lastname;
						$password=hash('sha512', 'essence@123' . config_item('encryption_key'));
						if(empty($user['mobile_phone'])){
							$phone='-';
						}else{
							$phone=$user['mobile_phone'];
						}
						if(empty($user['email_address'])){
							$email='-';
						}else{
							$email=$user['email_address'];
						}
						$procoreSupplierId=$user['vendor']['id'];
						$procoreIntegrationStatus='YES';
						$this->Procore_Model->syncSupplierUsers($contactId,$userType,$username,$password,$phone,$email,$firstname,$lastname,$procoreSupplierId,$procoreIntegrationStatus);
					}}
				}

				redirect('admincontrol/procore/procore_view');
			}
			else{
				redirect('admincontrol/procore/procore_view');
			}
		}else{
			redirect('default404');
		}
	}

	// sync Tax Groups data
	
	function syncTaxGroups(){
		if($_POST){
			$taxId=$_POST['h-id'];
			$taxName=$_POST['h-name'];
			$taxDesc=$_POST['h-description'];
			$taxRate=$_POST['h-rate'];
			if ($this->Procore_Model->syncTaxGroups($taxId,$taxName,$taxDesc,$taxRate) == TRUE)
			{
				redirect('admincontrol/procore/procore_view');
			}
			else{
				redirect('admincontrol/procore/procore_view');
			}
		}else{
			redirect('default404');
		}
	}

	// sync Budget Line Item data
	
	function syncCommitments(){
		if($_POST){
			$poCostCodesArray=[];
			$projectCostCodesArray=[];
			$poCostCodes=json_encode($this->Procore_Model->getCommitmentLineItems($_POST['h-porder-id']));
			$poCostCodes=json_decode($poCostCodes,true);
			$projectCostCodes=json_encode($this->Procore_Model->getProjectCostCodes($_POST['h-project-id']));
			$projectCostCodes=json_decode($projectCostCodes,true);
			foreach($poCostCodes as $pocc){
				array_push($poCostCodesArray,$pocc['cost_code']);
			}
			foreach($projectCostCodes as $procc){
				array_push($projectCostCodesArray,$procc['procore_cc']);
			}
			print_r($poCostCodesArray).'echo <br><br>';
			print_r($projectCostCodesArray).'echo <br><br>';
			$diff=array_diff($poCostCodesArray, $projectCostCodesArray);
			if(empty($diff)){
				$curl = curl_init();
				curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://api.procore.com/rest/v1.0/purchase_order_contracts?project_id='.$_POST['h-project-id'].'&company_id='.$_POST['h-company-id'].'',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS =>'{
				"purchase_order_contract": {
					"title": "'.$_POST['h-porder-description'].'",
					"number": "'.$_POST['h-porder-no'].'",
					"ship_to_address": "'.$_POST['h-porder-address'].'",
					"delivery_date" : "2022-05-01",
					"vendor_id":'.$_POST['h-supplier-id'].'
				}
				}',
				CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json',
					'Authorization: Bearer '.$_POST['h-access-token'].'',
					'Cookie: AWSELB=C9678F371A99D572E6A6DEEE540CB386CBB813BDB128FC55E97BB004E7E76AD08316BF833957A1C35E9CA51FDC14011F96AF9CEA213614BBDC569F01E9493AF50411A5AD89; AWSELBCORS=C9678F371A99D572E6A6DEEE540CB386CBB813BDB128FC55E97BB004E7E76AD08316BF833957A1C35E9CA51FDC14011F96AF9CEA213614BBDC569F01E9493AF50411A5AD89'
				),
				));

				$response = curl_exec($curl);

				curl_close($curl);
				$commitmentResponse=json_decode($response,true);
				print_r($commitmentResponse);
				$poOrderContractId=$commitmentResponse['id'];

				$poCommitmentLineItems=json_encode($this->Procore_Model->getCommitmentLineItems($_POST['h-porder-id']));
				$poCommitmentLineItems=json_decode($poCommitmentLineItems,true);

				foreach($poCommitmentLineItems as $item){
						$lineItemCostCode=json_encode($this->Procore_Model->getLineItemCostCode($item['cost_code'],$_POST['h-project-id']));
						$lineItemCostCode=json_decode($lineItemCostCode,true);
						
						$lineItemDescription=json_encode($this->Procore_Model->getLineItemDescription($item['po_detail_item']));
						$lineItemDescription=json_decode($lineItemDescription,true);

						$curl = curl_init();

						curl_setopt_array($curl, array(
						CURLOPT_URL => 'https://api.procore.com/rest/v1.0/purchase_order_contracts/'.$poOrderContractId.'/line_items?project_id='.$_POST['h-project-id'].'&company_id='.$_POST['h-company-id'].'',
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_ENCODING => '',
						CURLOPT_MAXREDIRS => 10,
						CURLOPT_TIMEOUT => 0,
						CURLOPT_FOLLOWLOCATION => true,
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						CURLOPT_CUSTOMREQUEST => 'POST',
						CURLOPT_POSTFIELDS =>'{
						"line_item": {
							"amount": "'.$item['po_detail_subtotal'].'",
							"cost_code_id": '.$lineItemCostCode['procore_cc_id'].',
							"line_item_type_id": 562949953566963,
							"description": "'.$lineItemDescription['item_description'].'",
							"quantity": "'.$item['po_detail_quantity'].'",
							"unit_cost": "'.$item['po_detail_unitprice'].'",
							"tax_code_id": "'.$item['tax_code_id'].'",
							"uom": "'.$item['porder_detail_uom'].'"
						}
						}',
						CURLOPT_HTTPHEADER => array(
							'Content-Type: application/json',
							'Authorization: Bearer '.$_POST['h-access-token'].'',
							'Cookie: AWSELB=C9678F371A99D572E6A6DEEE540CB386CBB813BDB12225ED167FFC6539231467CB1079A16057A1C35E9CA51FDC14011F96AF9CEA213614BBDC569F01E9493AF50411A5AD89; AWSELBCORS=C9678F371A99D572E6A6DEEE540CB386CBB813BDB12225ED167FFC6539231467CB1079A16057A1C35E9CA51FDC14011F96AF9CEA213614BBDC569F01E9493AF50411A5AD89'
						),
						));

						$response1 = curl_exec($curl);

						curl_close($curl);

						$commitmentListItemResponse = json_decode($response1,true);
						$commitmentListItemId = $commitmentListItemResponse['id'];

				}

				if(!empty($commitmentListItemId)){

					if ($this->Procore_Model->updatePoIntegrationStatus($_POST['h-porder-id']) == TRUE)
					{
						redirect('admincontrol/procore/procore_view');
					}
					else{
						redirect('admincontrol/procore/procore_view');
					}

				}
			}else{
				$this->session->set_flashdata('commitment_err','yes');
				$poId=$_POST['h-porder-id'];
				$poNo=$_POST['h-porder-no'];
				$poProject=$_POST['h-project-id'];
				$poSupplier=$_POST['h-supplier-id'];
				$poTotalItems=$_POST['h-porder-total-items'];
				$reason='Cost Cost Not Linked with Project';
				$this->Procore_Model->updateFailedPoDetails($poId,$poNo,$poProject,$poSupplier,$poTotalItems,$reason);
				redirect('admincontrol/procore/procore_view');
			}
		}else{
			redirect('default404');
		}
	}

	// unlink Commintment
	
	function unLinkCommitments(){
		if($_POST){		
			$porderId=$_POST['h-porder-id'];
			$setting = $this->admin_m->get_CompanySMTP_Setting();
			$poDetails = $this->db->get_where('purchase_order_master',['porder_id'=>$porderId])->row();
			$createdBy = $this->admin_m->GetDetailsofUsers($poDetails->porder_createby);
			if($createdBy->username != null) {
				$toEmail = [
					[
						'email' => $createdBy->email,
						'name' => $createdBy->username,
					]
				];
			}
			$cc = [
				'puneet.nomad@gmail.com',
			];
			$data=[
				'subject' => 'Porder RTE to Pending',
				'content' => 'Porder converted back to pending from rte, please check and make necessary changes'
			];
			$this->db->where('porder_id',$porderId);
			if($this->db->update('purchase_order_master',['porder_general_status'=>'pending','integration_status'=>'pending'])){
				$this->sendSMTPEmail($toEmail, $data['subject'] , $data['content'],$cc , $setting);
				$this->db->where('fpo_porder_id',$porderId);
				if($this->db->update('failed_po_details',['status'=>0])){
					redirect('admincontrol/procore/procore_view');
				}else{
					redirect('default404');
				}
			}else{
				redirect('default404');
			}
		}else{
			redirect('default404');
		}
	}
}
