<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Dashboard extends Admin_Controller
{

	public function __construct()
	{
		parent::__construct();
		//date_default_timezone_set("Asia/Kolkata");
		$this->data["u_details"] = $this->admin_m->GetDetailsofUsers($this->session->userdata['uid']);

	}

	public function index()
	{

		$this->data['total_po'] = $this->admin_m->getTotalPO();
		$this->data['pending_po'] = $this->admin_m->getPendingPO();
		$this->data['submitted_po'] = $this->admin_m->getSubmittedPO();
		$this->data['rte_po'] = $this->admin_m->getRTEPO();
		$this->data['proj_list'] = $this->db->order_by('proj_name', 'ASC')->where('proj_status', 1)->get('project_master')->result();
		$this->data['total_receive'] = $this->admin_m->getTotalReceive();
		$this->data['partially_received'] = $this->admin_m->getPartiallyReceive();
		$this->data['fully_received'] = $this->admin_m->getFullyReceive();
		$this->data['not_received'] = $this->admin_m->getNotReceive();
		if($this->session->userdata('utype')==4){
			$userInfo=$this->db->get_where('user_info',['username' => $this->session->userdata('username')])->row();
			// print_r($userInfo);
			$supplierPid=$userInfo->procore_supplier_id;
			$suppplierInfo=$this->db->get_where('supplier_master',['procore_supplier_id'=>$supplierPid,'sup_status'=>1])->row();
			if(!empty($suppplierInfo)){
				$supplier= $suppplierInfo->sup_id;
			}
			$this->data['total_rfqs']=$this->db->get_where('request_purchase_order',['rporder_supplier_ms'=>$supplier])->num_rows();
			$this->data['waiting_rfqs']=$this->db->get_where('request_purchase_order',['rporder_supplier_ms'=>$supplier,'rporder_status'=>'waiting for response'])->num_rows();
			$this->data['total_items']=$this->db->get_where('supplier_catalog_tab',['supcat_supplier'=>$supplier,'supcat_status'=>1])->num_rows();
			$currentDate=date('Y-m-d');
			$checkDate=date( "Y-m-d", strtotime( "$currentDate +7 day" ) );
			$this->data['expiring_items']=$this->db->get_where('supplier_catalog_tab',['supcat_supplier'=>$supplier,'supcat_lastdate <=' => $checkDate,'supcat_status'=>1])->num_rows();
		}	
		$this->load->view('admin/main', $this->data);
	}

	public function logout()
	{
		$this->session->sess_destroy();
		redirect('Admin_access');
	}

	public function getPODataForChart()
	{
		$proj_id = $this->input->get('proj_id');
		$this->data['total_po'] = $this->admin_m->getTotalPO($proj_id);
		$this->data['pending_po'] = $this->admin_m->getPendingPO($proj_id);
		$this->data['submitted_po'] = $this->admin_m->getSubmittedPO($proj_id);
		$this->data['rte_po'] = $this->admin_m->getRTEPO($proj_id);
		$this->data['integration_sync_po'] = $this->admin_m->getIntegrationSyncPO($proj_id);
		$this->data['integration_pending'] = $this->admin_m->getIntegrationPendingPO($proj_id);
		$this->data['partially_received'] = $this->admin_m->getPartiallyReceive($proj_id);
		$this->data['fully_received'] = $this->admin_m->getFullyReceive($proj_id);
		$this->data['not_received'] = $this->admin_m->getNotReceive($proj_id);
		$this->data['material_po'] = $this->admin_m->getPOByType("Material PO",$proj_id);
		$this->data['rental_po'] = $this->admin_m->getPOByType("Rental PO",$proj_id);
		echo json_encode($this->data,true);
	}

	public function administrator()
	{
		if ($this->session->userdata['utype'] > 2) {
			redirect('admincontrol/dashboard');
		}
		$this->data['userlist'] = $this->admin_m->searchallAdminUser();
		$this->data["utype_list"] = $this->db->where('mu_status = 1')->get("master_user_type")->result();
		$this->data["supplier_list"] = $this->db->get("supplier_master")->result();

		if ($_POST) {
			$f_name = $this->input->post("fname");
			$l_name = $this->input->post("lname");
			$user_type = $this->input->post("u_type");
			//$parent_utype = $this->input->post("parent_utype");
			//$u_dist = $this->input->post("u_dist");
			$email_id = $this->input->post("emailid");
			$user_name = $this->input->post("username");
			$password = $this->input->post("password");
			$re_password = $this->input->post("re_password");
			$address = $this->input->post('u_address');
			$supplier = $this->input->post('supplier');
			$mobile = $this->input->post('u_mobile');
			/*$state = $this->input->post('u_state');
			$city = $this->input->post('u_city');
			$pincode = $this->input->post('u_pincode');*/

			$this->form_validation->set_rules('fname', 'First Name', 'trim|required');
			$this->form_validation->set_rules('lname', 'Last Name', 'trim|required');
			$this->form_validation->set_rules('u_type', 'User Type', 'trim|required');
			$this->form_validation->set_rules('emailid', 'Email ID', 'trim|required|is_unique[user_info.email]',
				array('is_unique' => 'You must provide a Proper Unique Email Id.'));
			$this->form_validation->set_rules('username', 'User Name', 'trim|required|is_unique[user_info.username]',
				array('is_unique' => 'User Name already exit, Please change it..'));
			$this->form_validation->set_rules('password', 'Password', 'trim|required|matches[re_password]');
			$this->form_validation->set_rules('re_password', 'Re-Password', 'trim|required');

			$this->form_validation->set_rules('u_address', 'Address', 'trim');
			$this->form_validation->set_rules('u_mobile', 'Phone/Mobile', 'trim|required|is_unique[user_info.phone]');

			/*$this->form_validation->set_rules('u_state', 'State', 'trim');
            $this->form_validation->set_rules('u_city', 'City', 'trim');
            $this->form_validation->set_rules('u_pincode', 'Pincode', 'trim');*/

			//echo "22222";exit;
			if ($this->form_validation->run() == TRUE) {
				// echo "1st";
				if ($this->admin_m->check_email_exist($email_id) == TRUE && $this->admin_m->check_phone_exist($mobile) == TRUE) {
					// echo "2nd";
					if ($this->admin_m->check_username_exist($user_name) == TRUE) {
						//date_default_timezone_set("Asia/Kolkata");
						$encrip_pass = $this->admin_m->hash($password);
						$row = array(
							'u_type' => $user_type,
							'username' => $user_name,
							'password' => $encrip_pass,
							'email' => $email_id,
							'phone' => $mobile,
							'firstname' => $f_name,
							'lastname' => $l_name,
							'supplier_id' => $supplier,
							'create_date' => date('Y-m-d H:i:s'),
							'modify_date' => date('Y-m-d H:i:s'),
							'access_ip' => $this->input->ip_address()
						);
						$row1 = array(
							'address' => $address,
							'state' => NULL,
							'city' => NULL,
							'pincode' => NULL
						);
						// print_r($row);

						if ($this->admin_m->saveNewUser($row, $row1)) {

							if ($this->admin_m->get_Notification_SettingByKey('is_new_user')) {

								$template = $this->admin_m->GetEmailTemplateWithID($this->admin_m->get_Notification_SettingByKey('new_user_template'));
								$setting = $this->admin_m->get_CompanySMTP_Setting();

								$toEmail = [
									[
										'email' => $email_id,
										'name' => $user_name,
									]
								];


								$params = [
									"#FirstName#" => $f_name,
									"#LastName#" => $l_name,
									"#UserName#" => $user_name,
									"#Password#" => $password
								];

								$data = $this->admin_m->prepareEmailBody($template->email_key, $params);

								$this->sendSMTPEmail($toEmail, $data['subject'], $data['content'], null, $setting);
							}
							$response['msg'] = 1;
							$this->session->set_flashdata("success", "User is Added successfully.");
							// redirect('admincontrol/dashboard/administrator', 'refresh');
						} else
						$response['msg'] = 0;
							$this->data["error"] = "There is an error. Please try again";
					} else {
						$response['msg'] = 0;
						$this->data["error"] = "UserName already Exist, please use another Username.";
					}

				} else {
					$response['msg'] = 0;
					$this->data["error"] = "Email-ID/ Mobile No. already Exist, please use another Email-ID.";
				}
			}
			echo json_encode($response);
			exit;
		}

		$this->load->view('admin/subadmin_list', $this->data);
	}

	public function add_administrator()
	{
		if ($this->session->userdata['utype'] > 2) {
			redirect('admincontrol/dashboard');
		}
		if ($_POST) {
			$f_name = $this->input->post("fname");
			$l_name = $this->input->post("lname");
			$user_type = $this->input->post("u_type");
			//$parent_utype = $this->input->post("parent_utype");
			//$u_dist = $this->input->post("u_dist");
			$email_id = $this->input->post("emailid");
			$user_name = $this->input->post("username");
			$password = $this->input->post("password");
			$re_password = $this->input->post("re_password");
			$address = $this->input->post('u_address');
			$supplier = $this->input->post('supplier');
			$mobile = $this->input->post('u_mobile');
			/*$state = $this->input->post('u_state');
			$city = $this->input->post('u_city');
			$pincode = $this->input->post('u_pincode');*/

			$this->form_validation->set_rules('fname', 'First Name', 'trim|required');
			$this->form_validation->set_rules('lname', 'Last Name', 'trim|required');
			$this->form_validation->set_rules('u_type', 'User Type', 'trim|required');
			$this->form_validation->set_rules('emailid', 'Email ID', 'trim|required|is_unique[user_info.email]',
				array('is_unique' => 'You must provide a Proper Unique Email Id.'));
			$this->form_validation->set_rules('username', 'User Name', 'trim|required|is_unique[user_info.username]',
				array('is_unique' => 'User Name already exit, Please change it..'));
			$this->form_validation->set_rules('password', 'Password', 'trim|required|matches[re_password]');
			$this->form_validation->set_rules('re_password', 'Re-Password', 'trim|required');

			$this->form_validation->set_rules('u_address', 'Address', 'trim');
			$this->form_validation->set_rules('u_mobile', 'Phone/Mobile', 'trim|required|is_unique[user_info.phone]');

			/*$this->form_validation->set_rules('u_state', 'State', 'trim');
            $this->form_validation->set_rules('u_city', 'City', 'trim');
            $this->form_validation->set_rules('u_pincode', 'Pincode', 'trim');*/

			//echo "22222";exit;
			if ($this->form_validation->run() == TRUE) {
				//echo "1st";
				if ($this->admin_m->check_email_exist($email_id) == TRUE && $this->admin_m->check_phone_exist($mobile) == TRUE) {
					//echo "2nd";
					if ($this->admin_m->check_username_exist($user_name) == TRUE) {
						//date_default_timezone_set("Asia/Kolkata");
						$encrip_pass = $this->admin_m->hash($password);
						$row = array(
							'u_type' => $user_type,
							'username' => $user_name,
							'password' => $encrip_pass,
							'email' => $email_id,
							'phone' => $mobile,
							'firstname' => $f_name,
							'lastname' => $l_name,
							'supplier_id' => $supplier,
							'create_date' => date('Y-m-d H:i:s'),
							'modify_date' => date('Y-m-d H:i:s'),
							'access_ip' => $this->input->ip_address()
						);
						$row1 = array(
							'address' => $address,
							'state' => NULL,
							'city' => NULL,
							'pincode' => NULL
						);


						if ($this->admin_m->saveNewUser($row, $row1)) {

							if ($this->admin_m->get_Notification_SettingByKey('is_new_user')) {

								$template = $this->admin_m->GetEmailTemplateWithID($this->admin_m->get_Notification_SettingByKey('new_user_template'));
								$setting = $this->admin_m->get_CompanySMTP_Setting();

								$toEmail = [
									[
										'email' => $email_id,
										'name' => $user_name,
									]
								];


								$params = [
									"#FirstName#" => $f_name,
									"#LastName#" => $l_name,
									"#UserName#" => $user_name,
									"#Password#" => $password
								];

								$data = $this->admin_m->prepareEmailBody($template->email_key, $params);

								$this->sendSMTPEmail($toEmail, $data['subject'], $data['content'], null, $setting);
							}
							$this->session->set_flashdata("success", "User is Added successfully.");
							// redirect('admincontrol/dashboard/administrator', 'refresh');
						} else
							$this->data["error"] = "There is an error. Please try again";
					} else {
						$this->data["error"] = "UserName already Exist, please use another Username.";
					}

				} else {
					$this->data["error"] = "Email-ID/ Mobile No. already Exist, please use another Email-ID.";
				}
			}
		}

		// $this->data["utype_list"] = $this->db->where('mu_status = 1')->get("master_user_type")->result();
		// $this->data["supplier_list"] = $this->db->get("supplier_master")->result();
		// $this->load->view('admin/add_subadmin_user', $this->data);
	}

	public function lock_user($uid)
	{
		if ($this->session->userdata['utype'] > 2) {
			redirect('admincontrol/dashboard');
		}
		$cng_status = '0';
		if ($this->admin_m->change_user_status($uid, $cng_status) == TRUE) {
			$this->session->set_flashdata("success", "User is Locked successfully");
			redirect('admincontrol/dashboard/administrator', 'refresh');
		} else {
			$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
			redirect('admincontrol/dashboard/administrator', 'refresh');
		}
	}

	public function unlock_user($uid)
	{
		if ($this->session->userdata['utype'] > 2) {
			redirect('admincontrol/dashboard');
		}
		$cng_status = '1';
		if ($this->admin_m->change_user_status($uid, $cng_status) == TRUE) {
			$this->session->set_flashdata("success", "User is Unlocked successfully");
			redirect('admincontrol/dashboard/administrator', 'refresh');
		} else {
			$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
			redirect('admincontrol/dashboard/administrator', 'refresh');
		}
	}

	public function delete_user($uid)
	{
		if ($this->session->userdata['utype'] > 2) {
			redirect('admincontrol/dashboard');
		}
		if ($this->db->delete('user_info', array('u_id' => $uid))) {
			$this->db->delete('user_details', array('uid' => $uid));
			$this->session->set_flashdata("success", "User is Removed successfully");
			redirect('admincontrol/dashboard/administrator', 'refresh');
		} else {
			$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
			redirect('admincontrol/dashboard/administrator', 'refresh');
		}
	}

	public function edit_user()
	{
		$user_id = $this->input->post('user_id');

		$this->data["supplier_list"] = $this->db->get("supplier_master")->result();

		//$this->data["branchlist"] = $this->db->get_where("govorder_branch", array("b_status" => '1'))->result();
		$this->data["data_list"] = $this->admin_m->getUserDetails($user_id);
		$this->data["utype_list"] = $this->db->where('mu_id != 1 AND mu_status = 1')->get("master_user_type")->result();
		$this->data["msg"] = 1;
		//$this->data["dist_list"] = $this->db->order_by('district_name ASC')->where('district_status = 1')->get("district_master")->result();
		echo json_encode($this->data);
		exit;
	}

	public function update_user()
	{
		if ($_POST) {
			$f_name = $this->input->post("fname");
			$l_name = $this->input->post("lname");
			$user_type = $this->input->post("u_type");
			//$parent_utype = $this->input->post("parent_utype");
			//$u_dist = $this->input->post("u_dist");
			$email_id = $this->input->post("emailid");
			$user_name = $this->input->post("username");
			$password = $this->input->post("password");
			$re_password = $this->input->post("re_password");

			$address = $this->input->post('u_address');
			$mobile = $this->input->post('u_mobile');
			/*$state = $this->input->post('u_state');
			$city = $this->input->post('u_city');
			$pincode = $this->input->post('u_pincode');*/
			$supplier = $this->input->post('supplier');
			$user_id = $this->input->post('user_id');

			$this->form_validation->set_rules('fname', 'First Name', 'trim|required');
			$this->form_validation->set_rules('lname', 'Last Name', 'trim|required');
			$this->form_validation->set_rules('emailid', 'Email ID', 'trim|required');
			$this->form_validation->set_rules('u_mobile', 'Mobile', 'trim|required');
			$this->form_validation->set_rules('username', 'User Name', 'trim|required');
			$this->form_validation->set_rules('u_type', 'User Type', 'trim|required');
			$this->form_validation->set_rules('password', 'Password', 'trim|matches[re_password]');
			$this->form_validation->set_rules('re_password', 'Re-Password', 'trim');

			if ($this->form_validation->run() == TRUE) {
				if ($this->admin_m->check_email_exist($email_id, $user_id) == TRUE && $this->admin_m->check_phone_exist($mobile, $user_id) == TRUE) {
					if ($this->admin_m->check_username_exist($user_name, $user_id) == TRUE) {
						$accessgrant = 0;

						if ($password != '') {
							$encrip_pass = $this->admin_m->hash($password);
							$row = array(
								'u_type' => $user_type,
								'password' => $encrip_pass,
								'firstname' => $f_name,
								'lastname' => $l_name,
								'username' => $user_name,
								'supplier_id' => $supplier,
								'email' => $email_id,
								'phone' => $mobile
							);
						} else {
							$row = array(
								'u_type' => $user_type,
								'username' => $user_name,
								'email' => $email_id,
								'firstname' => $f_name,
								'supplier_id' => $supplier,
								'lastname' => $l_name,
								'phone' => $mobile
							);
						}

						$row1 = array(
							'address' => $address,
							'state' => NULL,
							'city' => NULL,
							'pincode' => NULL

						);

						if ($this->admin_m->UpdateSavedUser($row, $row1, $user_id) == TRUE) {
							$response['msg'] = 1;
							$response['e_msg'] = 'User Details is Updated successfully';
						} else {
							$response['msg'] = 0;
							$response['e_msg'] = 'There is an error. Please try again';
						}
					} else {
						$response['msg'] = 0;
						$response['e_msg'] = 'UserName already Exist, please use another Username.';
					}

				} else {
					$response['msg'] = 0;
					$response['e_msg'] = 'Email-ID / Mobile already Exist, please use another Email-ID.';}
			} else
			{
				$response['msg'] = 0;
				$response['e_msg'] = validation_errors();
			}
			echo json_encode($response);
			exit;
		}
	}
	public function permit_user($uid = NULL)
	{
		if ($this->session->userdata['utype'] > 2) {
			redirect('admincontrol/dashboard');
		}
		if ($uid == NULL) {
			redirect('admincontrol/dashboard/administrator');
		}
		if ($_POST) {
			$p_type = $this->input->post("p_type");
			$pr_lvl = $this->input->post("pr_lvl");
			$this->form_validation->set_rules('p_type', 'Permit Application', 'trim|required|is_natural_no_zero');
			$this->form_validation->set_rules('pr_lvl[]', 'Permit Level', 'trim|required');

			if ($this->form_validation->run() == TRUE) {
				$row = array(
					'up_master_user' => $uid,
					'up_application' => $p_type,
					'up_createdate' => date('Y-m-d H:i:s'),
					'up_createby' => $this->session->userdata['uid']
				);
				foreach ($pr_lvl as $per_lvls) {
					if ($per_lvls == "View") {
						$row['up_view'] = 1;
					}
					if ($per_lvls == "Add") {
						$row['up_add'] = 1;
					}
					if ($per_lvls == "Edit") {
						$row['up_edit'] = 1;
					}
					if ($per_lvls == "Delete") {
						$row['up_delete'] = 1;
					}
				}

				if ($this->admin_m->permission_Inserted_DB($row) == TRUE) {
					$this->session->set_flashdata("success", "Permission is Added successfully.");
					redirect('admincontrol/dashboard/permit_user/' . $uid, 'refresh');
				} else {
					$this->data["error"] = "There is an error to insert DB. Please try again";
				}
			}
		}
		$u_permit = $this->db->distinct()->select('up_application')->get_where("user_permission_tab", array("up_master_user" => $uid))->result();
		if (count($u_permit) > 0) {
			$app_array = array();
			foreach ($u_permit as $pers) {
				$app_array[] = $pers->up_application;
			}
			$this->data["per_appli"] = $this->db->order_by('papp_name', 'ASC')->where_not_in("papp_id", $app_array)->where("papp_status", 1)->get("permit_application")->result();
		} else {
			$this->data["per_appli"] = $this->db->order_by('papp_name', 'ASC')->get_where("permit_application", array("papp_status" => 1))->result();
		}
		$this->data["current_permit"] = $this->admin_m->get_all_permitApplication_details($uid);
		$this->data["current_user"] = $this->db->get_where("user_views", array("u_id" => $uid))->row();
		//echo "<pre>";
		//print_r($this->data["current_permit"]);exit;
		$this->load->view('admin/permission_user', $this->data);
	}

	public function delete_permit($uid = NULL, $pid = NULL)
	{
		if ($this->session->userdata['utype'] > 2) {
			redirect('admincontrol/dashboard');
		}
		if ($uid == NULL || $pid == NULL) {
			redirect('admincontrol/dashboard/administrator');
		}
		if ($this->db->delete('user_permission_tab', array('up_master_user' => $uid, 'up_id' => $pid))) {
			$this->session->set_flashdata("success", "Permission is Removed successfully");
			redirect('admincontrol/dashboard/permit_user/' . $uid, 'refresh');
		} else {
			$this->session->set_flashdata("e_error", "There is some Problem to Delete from DB. Please try again.");
			redirect('admincontrol/dashboard/permit_user/' . $uid, 'refresh');
		}
	}

	public function all_user_permit()
	{
		if ($this->session->userdata['utype'] > 2) {
			redirect('admincontrol/dashboard');
		}
		if ($_POST) {
			$user_sets = $this->input->post("user_sets");
			$p_type = $this->input->post("p_type");
			$pr_lvl = $this->input->post("pr_lvl");

			$this->form_validation->set_rules('user_sets[]', 'User List', 'trim|required');
			$this->form_validation->set_rules('p_type', 'Permit Application', 'trim|required|is_natural_no_zero');
			$this->form_validation->set_rules('pr_lvl[]', 'Permit Level', 'trim|required');

			if ($this->form_validation->run() == TRUE) {
				$all_check = 0;
				foreach ($user_sets as $users) {
					if ($this->admin_m->permission_Exist_inDB_forUser($p_type, $users) == TRUE) {
						$permit_id = $this->db->get_where("user_permission_tab", array("up_master_user" => $users, "up_application" => $p_type))->row()->up_id;
						$row = array(
							'up_modifydate' => date('Y-m-d H:i:s'),
							'up_modifyby' => $this->session->userdata['uid']
						);
						foreach ($pr_lvl as $per_lvls) {
							if ($per_lvls == "View") {
								$row['up_view'] = 1;
							}
							if ($per_lvls == "Add") {
								$row['up_add'] = 1;
							}
							if ($per_lvls == "Edit") {
								$row['up_edit'] = 1;
							}
							if ($per_lvls == "Delete") {
								$row['up_delete'] = 1;
							}
						}
						if (empty($row['up_view'])) {
							$row['up_view'] = 0;
						}
						if (empty($row['up_add'])) {
							$row['up_add'] = 0;
						}
						if (empty($row['up_edit'])) {
							$row['up_edit'] = 0;
						}
						if (empty($row['up_delete'])) {
							$row['up_delete'] = 0;
						}
						if ($this->admin_m->permission_Inserted_DB($row, $permit_id) == FALSE) {
							$all_check++;
						}
					} else {
						$row2 = array(
							'up_master_user' => $users,
							'up_application' => $p_type,
							'up_createdate' => date('Y-m-d H:i:s'),
							'up_createby' => $this->session->userdata['uid']
						);
						foreach ($pr_lvl as $per_lvls) {
							if ($per_lvls == "View") {
								$row2['up_view'] = 1;
							}
							if ($per_lvls == "Add") {
								$row2['up_add'] = 1;
							}
							if ($per_lvls == "Edit") {
								$row2['up_edit'] = 1;
							}
							if ($per_lvls == "Delete") {
								$row2['up_delete'] = 1;
							}
						}

						if ($this->admin_m->permission_Inserted_DB($row2) == FALSE) {
							$all_check++;
						}
					}
				}
				if ($all_check == 0) {
					$this->session->set_flashdata("success", "Permission is Added or Updated successfully.");
					redirect('admincontrol/dashboard/all_user_permit', 'refresh');
				} else {
					$this->data["error"] = "There is an error to insert or update DB. Total " . $all_check . " error occured.";
				}
			}
		}
		$this->data["per_appli"] = $this->db->order_by('papp_name', 'ASC')->get_where("permit_application", array("papp_status" => 1))->result();
		$this->data["user_lsit"] = $this->db->order_by('firstname', 'ASC')->get_where("user_views", array("u_id != " => 1, "status" => '1'))->result();
		//echo "<pre>";
		//print_r($this->data["current_permit"]);exit;
		$this->load->view('admin/permission_all_user', $this->data);
	}

	public function profile()
	{
		$this->data["usr_detail"] = $this->db->get_where("user_views", array("u_id" => $this->session->userdata['uid']))->row();
		$this->data["utype_list"] = $this->db->where('mu_id != 1 AND mu_status = 1')->get("master_user_type")->result();
		$this->load->view('admin/profile/profile_view', $this->data);
	}

	public function editprofile()
	{
		if ($this->input->post("submit")) {
			$f_name = $this->input->post("fname");
			$l_name = $this->input->post("lname");

			$address = $this->input->post('u_address');
			//$mobile = $this->input->post('u_mobile');
			/*$country = $this->input->post('u_country');
			$state = $this->input->post('u_state');
			$city = $this->input->post('u_city');
			$pincode = $this->input->post('u_pincode');*/

			$this->form_validation->set_rules('fname', 'First Name', 'trim|required');
			$this->form_validation->set_rules('lname', 'Last Name', 'trim|required');

			$this->form_validation->set_rules('u_address', 'Address', 'trim');
			//$this->form_validation->set_rules('u_mobile', 'Phone/Mobile', 'trim');
			/*$this->form_validation->set_rules('u_country', 'Country', 'trim');
			$this->form_validation->set_rules('u_state', 'State', 'trim');
			$this->form_validation->set_rules('u_city', 'City', 'trim');
			$this->form_validation->set_rules('u_pincode', 'Pincode', 'trim');*/

			if ($this->form_validation->run() == TRUE) {

				//date_default_timezone_set("Asia/Kolkata");
				$rows = array(
					'firstname' => $f_name,
					'lastname' => $l_name,
					'modify_date' => date('Y-m-d H:i:s'),
					'access_ip' => $this->input->ip_address()
				);

				$row1 = array(
					'address' => $address
				);

				if ($this->admin_m->UpdateSavedUser($rows, $row1, $this->session->userdata['uid'])) {
					$this->session->set_flashdata("success", "Profile is Updated successfully");
					redirect('admincontrol/dashboard/profile');
				} else
					$this->data["error"] = "There is an error. Please try again";

			}
		}
		$this->data["profile_list"] = $this->db->get_where("user_views", array("u_id" => $this->session->userdata['uid']))->row();
		$this->load->view('admin/profile/edit_profile_view', $this->data);
	}

	public function changepassword()
	{
		if ($this->input->post("submit")) {
			$c_pass = $this->input->post("c_pass");
			$n_pass = $this->input->post("n_pass");
			$n_repass = $this->input->post("n_repass");

			$this->form_validation->set_rules('c_pass', 'Current Password', 'trim|required');
			$this->form_validation->set_rules('n_pass', 'New Password', 'trim|required|matches[n_repass]');
			$this->form_validation->set_rules('n_repass', 'Re-Password', 'trim|required');

			if ((preg_match('/[\^£(&;)?\-}\/:{~\[\]\"\',.><>`|=+¬]/', $c_pass) == 1) || (preg_match('/[\^£(&;)?\-}\/:{~\[\]\"\',.><>`|=+¬]/', $n_pass) == 1) || (preg_match('/[\^£(&;)?\-}\/:{~\[\]\"\',.><>`|=+¬]/', $n_repass) == 1)) {
				$this->data["error"] = "Some Special Charecters not allow, Please try again.";
			} else {
				if ($this->form_validation->run() == TRUE) {

					$encrip_pass = $this->admin_m->hash($c_pass);

					if ($this->admin_m->check_password_exist($encrip_pass, $this->session->userdata['uid']) == TRUE) {
						$encrip_newpass = $this->admin_m->hash($n_pass);
						$rows = array(
							'password' => $encrip_newpass,
							'modify_date' => date('Y-m-d H:i:s'),
							'access_ip' => $this->input->ip_address()
						);
						if ($this->admin_m->UpdateSavedUser_Password($rows, $this->session->userdata['uid'])) {
							$this->session->set_flashdata("success", "Password is changed successfully");
							redirect('admincontrol/dashboard/profile');
						} else
							$this->data["error"] = "There is an error. Please try again";
					} else {
						$this->data["error"] = "Old Password not Matched. Please try again";
					}
				}
			}
		}
		$this->load->view('admin/profile/change_pass_view', $this->data);
	}

	function mypdf()
	{

		$supplier = $this->admin_m->GetDetailsofSupplier(6);
		$data['supplier'] = $supplier;
		$data['purchase_order'] = $this->admin_m->get_All_purchaseorder_list_fromDB(50);
		$data['project'] = $this->admin_m->get_Project_By_ID(7);
		$data['item_detailsets'] = $this->admin_m->getDetails_ItemList_POrder_from_DB(50);

		$this->load->library('pdf');
		$this->pdf->load_view('admin/pdf_view/purchase_order', $data);
		$this->pdf->set_paper("A4", "portrait");
		$this->pdf->render();
		ob_end_clean();
		return $this->pdf->stream('test.pdf', ["Attachment" => 0]);
		file_put_contents('./upload_file/test.pdf', $this->pdf->output());


//			$template = $this->admin_m->GetEmailTemplateWithKey('po_order_template');
//			$setting = $this->admin_m->get_CompanySMTP_Setting();
//			$supplier = $this->admin_m->GetDetailsofSupplier(22);
//
//			$toEmail = [
//				[
//					'email' => $supplier->sup_email,
//					'name' => $supplier->sup_name,
//				]
//			];
//
//			$cc = [
//				$template->email_cc
//			];
//
//			$params = [
//				"#SupName#" =>$supplier->sup_name
//			];
//
//			$data = $this->admin_m->prepareEmailBody('po_order_template', $params);
//
//			$this->sendSMTPEmail($toEmail, $data['subject'], $data['content'], $cc,$setting, "./upload_file/test.pdf");
//


	}
}
