<?php


class Admin_Controller extends MY_Controller
{


	function __construct()
	{

		require 'PHPMailer/src/PHPMailer.php';
		require 'PHPMailer/src/SMTP.php';
		require 'PHPMailer/src/Exception.php';

		parent::__construct();

		$this->load->model('admin_m');
		$this->data['company_assets'] = $this->db->where('company_id', 1)->get('company_tab')->row();

		if (isset($this->session->userdata['uid'])) {

			$this->data['adminid'] = $this->session->userdata['uid'];

			$this->data['adminname'] = $this->session->userdata['username'];

			$this->data['admintype'] = $this->session->userdata['utype'];

			$this->data['adminaccess'] = $this->session->userdata['uaccess'];

		}


		// Login check

		$exception_uris = array(
			'admin_access',
			'admin_access',
			'admincontrol/reminder',
			'admin_access/forgot_password',
			'admin_access/reset_password',
			'admin_access/update_password',
			'admin_access/get_new_capcha_set',
			'admincontrol/dashboard/logout'
		);


		if (in_array(uri_string(), $exception_uris) == FALSE) {

			if ($this->admin_m->loggedin() == FALSE) {

				redirect('admin_access');

			}

		}

	}


	public function sendSMTPEmail($toEmail, $subject, $template, $cc = NULL, $smtp =  NULL, $attachment = null, $bcc = null, $custom_attachment = null)
	{


		try {

			$mail = new PHPMailer\PHPMailer\PHPMailer(true);

			$mail->isSMTP();                                      // Set mailer to use SMTP

			if($smtp) {
				$mail->Host = $smtp->smtp_host;  // Specify main and backup SMTP servers
				$mail->SMTPAuth = true;                               // Enable SMTP authentication
				$mail->Username = $smtp->smtp_username;                 // SMTP username
				$mail->Password = $smtp->smtp_password;
				$mail->SMTPSecure = $smtp->smtp_encryption;                            // Enable TLS encryption, `ssl` also accepted
				$mail->Port = $smtp->smtp_port;
				$mail->From = $smtp->smtp_from_address;
				$mail->FromName = $smtp->smtp_from_name	;

			}

			foreach($toEmail as $email) {
				$mail->addAddress($email['email'], $email['name']);     // Add a recipient               // Name is optional
			}

			if (!empty($cc) && count($cc) > 0) {
				for ($i = 0; $i < count($cc); $i++) {
					$mail->addCC($cc[$i]);
				}
			}


			if (!empty($bcc)) {
				for ($i = 0; $i < count($bcc); $i++) {
					if($bcc[$i] != "") {
						$mail->addBCC($bcc[$i]);
					}
				}
			}

			if($attachment != null) {
				$mail->addAttachment($attachment);
			}

			if($custom_attachment != null) {
				$mail->addAttachment($custom_attachment);
			}

			$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
			$mail->isHTML(true);                                  // Set email format to HTML

			$mail->Subject = $subject;

			$mail->Body = $template;


			$mail->send();
		} catch (Exception $e) {
			echo json_encode($e->getMessage().' - '.$e->getCode().' - '.$e->getFile().' - '.$e->getLine());
			return false;
		}

	}

	public function sendSMTPEmail2($toEmail, $subject, $template, $cc = NULL, $smtp =  NULL, $attachment = null, $bcc = null, $custom_attachment = null)
	{

		try {

			$mail = new PHPMailer\PHPMailer\PHPMailer(true);

			$mail->isSMTP();                                      // Set mailer to use SMTP

			if($smtp) {
				$mail->Host = $smtp->smtp_host;  // Specify main and backup SMTP servers
				$mail->SMTPAuth = true;                               // Enable SMTP authentication
				$mail->Username = $smtp->smtp_username;                 // SMTP username
				$mail->Password = $smtp->smtp_password;
				$mail->SMTPSecure = $smtp->smtp_encryption;                            // Enable TLS encryption, `ssl` also accepted
				$mail->Port = $smtp->smtp_port;
				$mail->From = $smtp->smtp_from_address;
				$mail->FromName = $smtp->smtp_from_name	;

			}

			foreach($toEmail as $email) {
				$mail->addAddress($email['email'], $email['name']);     // Add a recipient               // Name is optional
			}

			if (!empty($cc) && count($cc) > 0) {
				for ($i = 0; $i < count($cc); $i++) {
					$mail->addCC($cc[$i]);
				}
			}


			if (!empty($bcc)) {
				for ($i = 0; $i < count($bcc); $i++) {
					if($bcc[$i] != "") {
						$mail->addBCC($bcc[$i]);
					}
				}
			}

			if($attachment != null) {
				$mail->addAttachment($attachment);
			}

			if($custom_attachment != null) {
				$mail->addAttachment($custom_attachment);
			}

			$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
			$mail->isHTML(true);                                  // Set email format to HTML

			$mail->Subject = $subject;

			$mail->Body = $template;


			$mail->send();
		} catch (Exception $e) {
			echo json_encode($e->getMessage().' - '.$e->getCode().' - '.$e->getFile().' - '.$e->getLine());
			return false;
		}

	}


}

