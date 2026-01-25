<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Company extends Admin_Controller
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
		redirect('admincontrol/company/company_view');
	}

	public function company_view()
	{
		if ($_POST) {

			$cname = $this->input->post("cname");
			$c_address = $this->input->post("c_address");

			$this->form_validation->set_rules('cname', 'Company Name', 'trim|required');
			$this->form_validation->set_rules('c_address', 'Company Address', 'trim|required');

			if ($this->form_validation->run() == TRUE) {

				$filename = $_FILES['c_logo']['name'];
				$app_logo_one = $_FILES['app_logo_one']['name'];
				$app_logo_two = $_FILES['app_logo_two']['name'];


				if (!empty($filename)) {
					$config['upload_path'] = realpath('upload_file/company/');
					$config['allowed_types'] = 'jpg|png|jpeg|JPG|PNG|JPEG';
					$config['overwrite'] = TRUE;
					$config['remove_spaces'] = TRUE;
					$config['max_size'] = '5000';
					$config['file_name'] = date('His') . $filename;

					$this->load->library('upload', $config);
					$this->upload->initialize($config);

					if ($this->upload->do_upload('c_logo')) {
						$upload_data = $this->upload->data();

						$filename = $upload_data['file_name'];
					} else {
						$this->data["error"] = $this->upload->display_errors();
					}
				} else {
					$filename = null;
				}

				if (!empty($app_logo_one)) {
					$config['upload_path'] = realpath('upload_file/company/');
					$config['allowed_types'] = 'jpg|png|jpeg|JPG|PNG|JPEG';
					$config['overwrite'] = FALSE;
					$config['remove_spaces'] = TRUE;
					$config['max_size'] = '5000';
					$config['file_name'] = date('His') . $app_logo_one;

					$this->load->library('upload', $config);
					$this->upload->initialize($config);

					if ($this->upload->do_upload('app_logo_one')) {
						$upload_data = $this->upload->data();
						$app_logo_one = $upload_data['file_name'];
					} else {
						$this->data["error"] = $this->upload->display_errors();
					}
				} else {
					$app_logo_one = null;
				}

				if (!empty($app_logo_two)) {
					$config['upload_path'] = realpath('upload_file/company/');
					$config['allowed_types'] = 'jpg|png|jpeg|JPG|PNG|JPEG';
					$config['overwrite'] = FALSE;
					$config['remove_spaces'] = TRUE;
					$config['max_size'] = '5000';
					$config['file_name'] = date('His') . $app_logo_two;

					$this->load->library('upload', $config);
					$this->upload->initialize($config);

					if ($this->upload->do_upload('app_logo_two')) {
						$upload_data = $this->upload->data();
						$app_logo_two = $upload_data['file_name'];
					} else {
						$this->data["error"] = $this->upload->display_errors();
					}
				} else {
					$app_logo_two = null;
				}

				$row2 = array(
					'company_name' => trim($cname),
					'company_address' => trim($c_address),
					'company_createdate' => date('Y-m-d H:i:s')
				);

				if (!empty($filename)) {
					$row2['company_logo'] = $filename;
				}
				if (!empty($app_logo_one)) {
					$row2['app_logo_one'] = $app_logo_one;
				}
				if (!empty($app_logo_two)) {
					$row2['app_logo_two'] = $app_logo_two;
				}

				if ($this->admin_m->common_Updation_in_DB($row2, 'company_tab', 'company_id', 1) == TRUE) {
					$this->session->set_flashdata("success", "Company Details is updated successfully.");
					redirect('admincontrol/company/company_view');
				} else
					$this->data["error"] = "There is an error. Please try again";

			}
		}
		$this->data['getrecord_list'] = $this->db->where('company_id', 1)->get('company_tab')->row();
		$this->data['getsmtp_record'] = $this->admin_m->get_CompanySMTP_Setting();
		$this->data['gettemplate_list'] = $this->admin_m->get_EmailTemplate();
		$this->data['users_list'] = $this->admin_m->get_Users_List();
		$this->data['suppliers'] = $this->admin_m->get_All_Suppliers();
		$this->data['equip_list'] = $this->db->where('status',1)->order_by('eq_id','ASC')->get('eq_master')->result();
		$this->data['insurance_info'] = $this->db->get_where('insurance_master',['company_id'=>1,'status'=>1])->row();
		if(!empty($this->data['insurance_info'])){
			$this->data['insurance_equipments'] = $this->db->get_where('insurance_equipments',['insurance_id'=>$this->data['insurance_info']->insurance_id,'status'=>1])->result();
		}

		$this->load->view('admin/company/company_set_view', $this->data);
	}

	public function updateinsurance_settings(){
		if($_POST){
			$ins_vendor = $this->input->post("ins_vendor");
			$ins_policy_no = $this->input->post("ins_policy_no");
			$ins_policy_start_date = $this->input->post("ins_policy_start_date");
			$ins_policy_end_date = $this->input->post("ins_policy_end_date");
			$ins_coverage_amt_1 = $this->input->post("ins_coverage_amt_1");
			$ins_coverage_amt_2 = $this->input->post("ins_coverage_amt_2");
			$ins_coverage_amt_3 = $this->input->post("ins_coverage_amt_3");
			$ins_coverage_desc_1 = $this->input->post("ins_coverage_desc_1");
			$ins_coverage_desc_2 = $this->input->post("ins_coverage_desc_2");
			$ins_coverage_desc_3 = $this->input->post("ins_coverage_desc_3");
			$ins_basic_valuation = $this->input->post("ins_basic_valuation");
			$ins_deductible_amt_1 = $this->input->post("ins_deductible_amt_1");
			$ins_deductible_amt_2 = $this->input->post("ins_deductible_amt_2");
			$ins_deductible_amt_3 = $this->input->post("ins_deductible_amt_3");
			$ins_deductible_desc_1 = $this->input->post("ins_deductible_desc_1");
			$ins_deductible_desc_2 = $this->input->post("ins_deductible_desc_2");
			$ins_deductible_desc_3 = $this->input->post("ins_deductible_desc_3");
			$ins_equipments = array();
			$attachment = $_FILES['ins_attachment']['name'];

			foreach($this->input->post("ins_equipments") as $equip){
				array_push($ins_equipments,$equip);
			}

			$this->form_validation->set_rules('ins_vendor', 'Insurance Vendor', 'trim|required');
			$this->form_validation->set_rules('ins_policy_no', 'Insurance Policy No', 'trim|required');
			$this->form_validation->set_rules('ins_policy_start_date', 'Insurance Policy Start Date', 'trim|required');
			$this->form_validation->set_rules('ins_policy_end_date', 'Insurance Policy End Date', 'trim|required');
			$this->form_validation->set_rules('ins_coverage_amt_1', 'Insurance Coverange Amount', 'trim|required');
			$this->form_validation->set_rules('ins_coverage_amt_2', 'Insurance Coverange Amount', 'trim|required');
			$this->form_validation->set_rules('ins_coverage_amt_3', 'Insurance Coverange Amount', 'trim|required');
			$this->form_validation->set_rules('ins_coverage_desc_1', 'Insurance Coverange Description', 'trim|required');
			$this->form_validation->set_rules('ins_coverage_desc_2', 'Insurance Coverange Description', 'trim|required');
			$this->form_validation->set_rules('ins_coverage_desc_3', 'Insurance Coverange Description', 'trim|required');
			$this->form_validation->set_rules('ins_basic_valuation', 'Insurance Basic Valuation', 'trim|required');
			$this->form_validation->set_rules('ins_deductible_amt_1', 'Insurance Deductible Amount', 'trim|required');
			$this->form_validation->set_rules('ins_deductible_amt_2', 'Insurance Deductible Amount', 'trim|required');
			$this->form_validation->set_rules('ins_deductible_amt_3', 'Insurance Deductible Amount', 'trim|required');
			$this->form_validation->set_rules('ins_deductible_desc_1', 'Insurance Deductible Description', 'trim|required');
			$this->form_validation->set_rules('ins_deductible_desc_2', 'Insurance Deductible Description', 'trim|required');
			$this->form_validation->set_rules('ins_deductible_desc_3', 'Insurance Deductible Description', 'trim|required');
			$this->form_validation->set_rules('ins_equipments[]', 'Insurance Deductible Description', 'trim|required');
			if ($this->form_validation->run() == TRUE) {

				if (!empty($attachment)) {
					$config['upload_path'] = realpath('upload_file/insurance/');
					$config['allowed_types'] = 'jpg|png|jpeg|JPG|PNG|JPEG|pdf';
					$config['overwrite'] = TRUE;
					$config['remove_spaces'] = TRUE;
					$config['max_size'] = '5000';
					$config['file_name'] = date('His') . $attachment;

					$this->load->library('upload', $config);
					$this->upload->initialize($config);

					if ($this->upload->do_upload('ins_attachment')) {
						$upload_data = $this->upload->data();

						$filename = $upload_data['file_name'];
					} else {
						$this->data["error"] = $this->upload->display_errors();
					}
				} else {
					$attachment = null;
				}

				$insuranceMasterData=[
					'insurance_vendor' => $ins_vendor,
					'policy_no' => $ins_policy_no,
					'policy_start_date' => $ins_policy_start_date,
					'policy_end_date' => $ins_policy_end_date,
					'coverage_amt_1' => $ins_coverage_amt_1,
					'coverage_amt_2' => $ins_coverage_amt_2,
					'coverage_amt_3' => $ins_coverage_amt_3,
					'coverage_desc_1' => $ins_coverage_desc_1,
					'coverage_desc_2' => $ins_coverage_desc_2,
					'coverage_desc_3' => $ins_coverage_desc_3,
					'basic_valuation' => $ins_basic_valuation,
					'deductible_amt_1' => $ins_deductible_amt_1,
					'deductible_amt_2' => $ins_deductible_amt_2,
					'deductible_amt_3' => $ins_deductible_amt_3,
					'deductible_desc_1' => $ins_deductible_desc_1,
					'deductible_desc_2' => $ins_deductible_desc_2,
					'deductible_desc_3' => $ins_deductible_desc_3,
					'attachment' => $attachment,
				];
				$insuranceCount = $this->db->where('company_id', 1)->get('insurance_master')->num_rows();
				if($insuranceCount > 0){
					$insuranceMasterData+=[
						'modified_date' => date('Y-m-d H:i:s')
					];
					$this->db->where('company_id',1);
					if($this->db->update('insurance_master',$insuranceMasterData)){
						$insurranceData=$this->db->where('company_id', 1)->get('insurance_master')->row();

						for($j=0;$j<count($ins_equipments);$j++){
							$equipmentCount=$this->db->get_where('insurance_equipments',['status' => 1,'insurance_id' => $insurranceData->insurance_id,'equip_id'=>$ins_equipments[$j]])->num_rows();
							
							$insuranceEquipmentsData=[
								'equip_id' => $ins_equipments[$j],
								'status' => 1
							];
							if($equipmentCount > 0){
								$this->db->where('insurance_id',$insurranceData->insurance_id);
								$this->db->update('insurance_equipments',['status'=>0]);
								$insuranceEquipmentsData+=[
									'modified_date' => date('Y-m-d H:i:s')
								];
								$this->db->where('equip_id',$ins_equipments[$j]);
								$this->db->update('insurance_equipments',$insuranceEquipmentsData);

							}else{
								$insuranceEquipmentsData+=[
									'insurance_id' => $insurranceData->insurance_id,
									'created_date' => date('Y-m-d H:i:s')
								];
								$this->db->insert('insurance_equipments',$insuranceEquipmentsData);

							}
						}
						redirect('admincontrol/company/company_view');
					}
				}else{
					$insuranceMasterData+=[
						'company_id' => 1,
						'created_date' => date('Y-m-d H:i:s')
					];
					if($this->db->insert('insurance_master',$insuranceMasterData)){
						$insurranceData=$this->db->where('company_id', 1)->get('insurance_master')->row();

						for($j=0;$j<count($ins_equipments);$j++){
							$equipmentCount=$this->db->get_where('insurance_equipments',['status' => 1,'insurance_id' => $insurranceData->insurance_id,'equip_id'=>$ins_equipments[$j]])->num_rows();
							
							$insuranceEquipmentsData=[
								'equip_id' => $ins_equipments[$j],
								'status' => 1
							];
							if($equipmentCount > 0){
								$this->db->where('insurance_id',$insurranceData->insurance_id);
								$this->db->update('insurance_equipments',['status'=>0]);
								$insuranceEquipmentsData+=[
									'modified_date' => date('Y-m-d H:i:s')
								];
								$this->db->where('equip_id',$ins_equipments[$j]);
								$this->db->update('insurance_equipments',$insuranceEquipmentsData);

							}else{
								$insuranceEquipmentsData+=[
									'insurance_id' => $insurranceData->insurance_id,
									'created_date' => date('Y-m-d H:i:s')
								];
								$this->db->insert('insurance_equipments',$insuranceEquipmentsData);

							}
						}
						redirect('admincontrol/company/company_view');
					}
				}
			} else {
				redirect('admincontrol/company/company_view');
			}
		}else{
			redirect('default404');
		}
	}

	public function updatemail_setting()
	{
		if ($_POST) {
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

				if ($this->admin_m->common_InsertorUpdation_in_DB($row, 'company_setting_master', 1) == TRUE) {
					$this->session->set_flashdata("success", "SMTP Settings updated successfully");
					redirect('admincontrol/company/company_view');
				} else {
					$this->session->set_flashdata("e_error", "There have some Problem to Insert Data, Try Again.");
					redirect('admincontrol/company/company_view');
				}

			} else {
				$this->data['getrecord_list'] = $this->db->where('company_id', 1)->get('company_tab')->row();
				$this->data['getsmtp_record'] = $this->admin_m->get_CompanySMTP_Setting();
				$this->load->view('admin/company/company_set_view', $this->data);
			}
		} else {
			redirect('default404');
		}
	}

	public function update_notification_setting()
	{
		if ($_POST) {
			$types = $this->input->post("key");

			$result = true;

			foreach ($types as $key => $type) {

				$value = $this->input->post($type);

				if (gettype($value) == 'array') {
					$data['value'] = json_encode($value);
				} else {
					$data['value'] = $value;
				}

				$row = array(
					'key' => $type,
					'value' => $data['value']
				);

				$result = $this->admin_m->common_InsertorUpdationNotification($row, 'notification_setting_master');
			}

			if ($result) {
				$this->session->set_flashdata("success", "Notification Settings updated successfully");
				redirect('admincontrol/company/company_view');
			} else {
				$this->session->set_flashdata("e_error", "There have some Problem to Insert Data, Try Again.");
				redirect('admincontrol/company/company_view');
			}

		} else {
			redirect('default404');
		}
	}

	public function updatemail_body()
	{
		if ($_POST) {
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

				if ($this->admin_m->common_InsertorUpdation_in_DB($row, 'company_setting_master', 1) == TRUE) {
					$this->session->set_flashdata("success", "SMTP Settings updated successfully");
					redirect('admincontrol/company/company_view');
				} else {
					$this->session->set_flashdata("e_error", "There have some Problem to Insert Data, Try Again.");
					redirect('admincontrol/company/company_view');
				}

			} else {
				$this->data['getrecord_list'] = $this->db->where('company_id', 1)->get('company_tab')->row();
				$this->data['getsmtp_record'] = $this->admin_m->get_CompanySMTP_Setting();
				$this->load->view('admin/company/company_set_view', $this->data);
			}
		} else {
			redirect('default404');
		}
	}

	public function test_email()
	{
		if ($_POST) {
			$setting = $this->admin_m->get_CompanySMTP_Setting();

			$email = $this->input->post("email");
			$toEmail = [
				[
					"email" => $email,
					"name" => ""
				]
			];
			$this->sendSMTPEmail($toEmail, 'SMTP Test Email', 'SMTP test email', null, $setting);

			redirect('admincontrol/company/company_view');
		}
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
	}

	public function unlock_costcodeset($uid = NULL)
	{
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
}
