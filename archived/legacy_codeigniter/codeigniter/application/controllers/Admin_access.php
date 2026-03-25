<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin_access extends Admin_Controller
{

	public function index()
	{


		$this->load->model('admin_m');
		$this->load->model('Procore_Model');

		// Redirect a user if he's already logged in
		$dashboard = 'admincontrol/dashboard';
		$this->admin_m->loggedin() == FALSE || redirect($dashboard);

		if ($_POST) {

			$uid = $this->input->post('username');
			$pwd = $this->input->post('password');


			$this->form_validation->set_error_delimiters('<span style="color:#F00;font-size:12px;">', '</span>');

			$this->form_validation->set_rules("username", "UserName", "trim|required|xss_clean");
			$this->form_validation->set_rules("password", "Password", "trim|required|xss_clean");


			// Process the form

			if ($this->form_validation->run() == TRUE) {

				if ($this->loginprocess($uid, $pwd) == true) {
					//redirect($this->input->server('HTTP_REFERER'));
					$countRows = $this->db->get_where('permission_master', ['pt_id' => $this->session->userdata('pt_id'), 'status' => 1])->num_rows();
					if ($countRows != 0 || $this->session->userdata('utype') == 1 || $this->session->userdata('utype') == 4) {
						$now = array(
							'modify_date' => date('Y-m-d H:i:s'),
							'access_ip' => $this->input->ip_address()
						);

						$this->admin_m->update_adminuser_modified($now);
						$this->updateBli();
						redirect($dashboard);
					} else {
						$this->data["error"] = 'Please contact admin for permissions';
					}
				} else {
					$this->data["error"] = 'Sorry Wrong Username or Password';
				}

			}

		}

		$this->load->view('admin/login', $this->data);

	}

	public function loginprocess($uid, $pwd)
	{
		$username = $this->security->sanitize_filename($uid);
		$password = $this->security->sanitize_filename($this->admin_m->hash($pwd));
		//$usertype = $utype;
		//var_dump($password);exit;

		$boolean = $this->admin_m->checklogin($username, $password);

		return $boolean;


	}

	public function check_username_password()
	{


		$username = $this->input->post('username');
		$password = $this->input->post('password');
		//$pwd = $this->admin_m->hash($password);

		$pri = $this->db->get_where('user_info', array('username' => $username, 'password' => $password))->num_rows();
		echo json_encode($pri);

	}

	public function check_email_exist($email)
	{

		return $this->db->get_where('user_info', array('email' => $email))->num_rows();
	}

	public function get_new_capcha_set()
	{
		if ($_POST) {
			//$location_set = $this->input->post('location_select');
			$this->load->helper('captcha');
			$this->db->query("DELETE FROM captcha WHERE ip_address = '" . $this->input->ip_address() . "'");
			$vals = array(
				'img_path' => 'captcha/',
				'img_url' => base_url() . 'captcha/',
				'font_path' => 'fonts/ARLRDBD.TTF',
				'word_length' => 6,
				'img_width' => '170',
				'img_height' => 40,
				'expiration' => 900
			);

			/* Generate the captcha */
			$caps = create_captcha($vals);

			$datas = array(
				'captcha_time' => $caps['time'],
				'ip_address' => $this->input->ip_address(),
				'word' => $caps['word']
			);

			$query = $this->db->insert_string('captcha', $datas);
			$this->db->query($query);
			$msg = 0;
			if (count($caps) > 0) {
				echo json_encode(array('msg' => 1, 'cap_set' => $caps));
			} else {
				echo json_encode(array('msg' => $msg));
			}
			exit;
		}
	}

	public function updateBli()
	{
		$getProcoreAuth = json_encode($this->Procore_Model->getProcoreAuth());
		$procoreAuthDetails = json_decode($getProcoreAuth, true);

		$client_id = $procoreAuthDetails['CLIENT_ID'];
		$secret_key = $procoreAuthDetails['SECRET_KEY'];
		$company_id = $procoreAuthDetails['COMPANY_ID'];
		$accessToken = "";

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://login.procore.com/oauth/token?client_id=' . $client_id . '&client_secret=' . $secret_key . '&grant_type=client_credentials',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				': ',
				'Cookie: AWSELB=83CFCB891680B364063F7F0C947C28DE1B1D31FDB8E07115BF9E0043272653CADB1E9A77E5663714E3444D46C69B24A6E28B7142E3F10DF651E49EA035CC127FCC4C909074; AWSELBCORS=83CFCB891680B364063F7F0C947C28DE1B1D31FDB8E07115BF9E0043272653CADB1E9A77E5663714E3444D46C69B24A6E28B7142E3F10DF651E49EA035CC127FCC4C909074'
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		$responseData = json_decode($response, true);
		if (isset($responseData['access_token'])) {
			$accessToken = $responseData['access_token'];
		}
		$getProjectIds = json_encode($this->db->get_where('budget_summary_master', ['bs_status' => 1])->result());
		$projectSummarys = json_decode($getProjectIds, true);
		// print_r($projectSummarys);

		foreach ($projectSummarys as $bs) {
			$curl5 = curl_init();

			curl_setopt_array($curl5, array(
				CURLOPT_URL => 'https://api.procore.com/rest/v1.0/budget_views/562949953449296/detail_rows?company_id=' . $company_id . '&project_id=' . $bs['project_id'] . '',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json',
					'Authorization: Bearer ' . $accessToken . '',
					'Cookie: AWSELB=C9678F371A99D572E6A6DEEE540CB386CBB813BDB12225ED167FFC6539231467CB1079A16045A69F2153C111E32E7B23CD43F7ADFEFD6421AAA944B4446E721A9B43FA179C; AWSELBCORS=C9678F371A99D572E6A6DEEE540CB386CBB813BDB12225ED167FFC6539231467CB1079A16045A69F2153C111E32E7B23CD43F7ADFEFD6421AAA944B4446E721A9B43FA179C'
				),
			));

			$response5 = curl_exec($curl5);

			curl_close($curl5);
			$response5Data = json_decode($response5, true);
			// print_r($response5Data);
			foreach ($response5Data as $bli) {
				$budgetId = $bli['id'];
				$projectId = $bli['project_id'];
				// echo $projectId;
				if (isset($bli['cost_code_level_2']) && $bli['cost_code_level_2'] != NULL && $bli['cost_code_level_3'] != "" && $bli['cost_code_level_3'] != "None") {
					$divisionParts = explode('-', strrev($bli['cost_code_level_2']), 2);
					$divisionName = strrev($divisionParts[0]);
					$divisionCode = strrev($divisionParts[1]);
				} else {
					$divisionName = '-';
					$divisionCode = '-';
				}
				if (isset($bli['cost_code_level_3']) && $bli['cost_code_level_3'] != NULL && $bli['cost_code_level_3'] != "" && $bli['cost_code_level_3'] != "None") {
					$ccParts = explode('-', strrev($bli['cost_code_level_3']), 2);
					$costCodeName = strrev($ccParts[0]);
					$costCode = strrev($ccParts[1]);
				} else {
					$costCodeName = '-';
					$costCode = '-';
				}
				$originalBudget = $bli['original_budget_amount'];
				$revisedBudget = $bli['Revised Budget'];
				$commitedCost1 = $bli['Committed Costs'];
				$this->Procore_Model->syncBli($budgetId, $projectId, $divisionName, $divisionCode, $costCodeName, $costCode, $originalBudget, $revisedBudget, $commitedCost1);
			}
		}
	}

	public function forgot_password()
	{
		if ($_POST) {

			$email = $this->input->post('email');

			$this->form_validation->set_error_delimiters('<span style="color:#F00;font-size:12px;">', '</span>');

			$this->form_validation->set_rules("email", "Email", "trim|required|xss_clean");

			// Process the form

			if ($this->form_validation->run() == TRUE) {

				if ($this->check_email_exist($email) == true) {
					$this->load->library('encrypt');
					if ($this->admin_m->get_Notification_SettingByKey('is_forgot_password')) {

						$template = $this->admin_m->GetEmailTemplateWithID($this->admin_m->get_Notification_SettingByKey('forgot_password_template'));
						$setting = $this->admin_m->get_CompanySMTP_Setting();
						$user = $this->db->get_where('user_info', array('email' => $email))->row();

						$toEmail = [
							[
								'email' => $email,
								'name' => $user->firstname,
							]
						];


						if ($template->email_cc != null) {
							$cc = [
								$template->email_cc
							];
						} else {
							$cc = [];
						}

						$encrypted_password = $user->password;
						$key = config_item('encryption_key');

						$decrypted_string = $this->encrypt->decode($encrypted_password, $key);
						$link = base_url() . 'admin_access/reset_password?email=' . $email;
						$params = [
							"#Link#" => $link,
							"#Password#" => $decrypted_string,
							"#FirstName#" => $user->firstname,
							"#UserName#" => $user->username,
						];

						$data = $this->admin_m->prepareEmailBody($template->email_key, $params);

						$this->sendSMTPEmail($toEmail, $data['subject'], $data['content'], $cc, $setting);
						$this->session->set_flashdata("success", "Email has been sent to your email address");
					}
					redirect('admin_access');

				} else {
					$this->data["error"] = 'Sorry Wrong Username or Password';
				}

			}

		}
	}

	public function reset_password()
	{
		if ($_GET) {

			$email = $this->input->get('email');

			$this->form_validation->set_error_delimiters('<span style="color:#F00;font-size:12px;">', '</span>');

			if ($this->check_email_exist($email) == true) {
				$this->data['email'] = $email;

				$this->load->view('admin/reset_password', $this->data);

			}

		}

	}

	public function update_password()
	{
		if ($_POST) {

			$email = $this->input->post('email');
			$password = $this->input->post('password');

			$this->form_validation->set_error_delimiters('<span style="color:#F00;font-size:12px;">', '</span>');

			$this->form_validation->set_rules("email", "Email", "trim|required|xss_clean");
			$this->form_validation->set_rules("password", "Password", "trim|required|xss_clean|matches[confirm_password]");
			$this->form_validation->set_rules("confirm_password", "Confirm Password", "trim|required|xss_clean");

			if ($this->form_validation->run() == TRUE) {

				if ($this->check_email_exist($email) == true) {
					$this->load->library('encrypt');
					$encrypt_pass = $this->admin_m->hash($password);
					$row = array(
						'password' => $encrypt_pass,
					);

					$this->admin_m->common_Updation_in_DB($row, 'user_info', 'email', $email);
					$template = $this->admin_m->GetEmailTemplateWithKey("ResetPassword");
					$setting = $this->admin_m->get_CompanySMTP_Setting();
					$user = $this->db->get_where('user_info', array('email' => $email))->row();

					$toEmail = [
						[
							'email' => $email,
							'name' => $user->firstname,
						]
					];


					if ($template->email_cc != null) {
						$cc = [
							$template->email_cc
						];
					} else {
						$cc = [];
					}

					$encrypted_password = $user->password;
					$key = config_item('encryption_key');

					$decrypted_string = $this->encrypt->decode($encrypted_password, $key);
					$link = base_url() . 'admin_access/reset_password?email=' . $email;
					$params = [
						"#Link#" => $link,
						"#Password#" => $decrypted_string,
						"#FirstName#" => $user->firstname,
						"#UserName#" => $user->username,
					];

					$data = $this->admin_m->prepareEmailBody($template->email_key, $params);

					$this->sendSMTPEmail($toEmail, $data['subject'], $data['content'], $cc, $setting);

					$this->session->set_flashdata("success", "Email has been sent to your email address");
					redirect('admin_access');

				} else {
					$this->data["error"] = 'Sorry Wrong Username or Password';

				}

			} else {
				$this->data['email'] = $email;
				$this->load->view('admin/reset_password', $this->data);
			}

		}
	}
}
