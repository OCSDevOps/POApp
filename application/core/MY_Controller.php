<?php

class MY_Controller extends CI_Controller
{


	public $data = array();

	function __construct()
	{

		parent::__construct();

		$this->load->library('email');


	}

	public function email_bill($from_email, $name, $subject, $template, $data = NULL)
	{
		//$toEmail= $subject= $template= "pritamjyoti1991@gmail.com";

		require_once 'PHPMailer/class.phpmailer.php';
		require_once 'PHPMailer/class.smtp.php';

		$mail = new PHPMailer;


		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = 'a2plcpnl0521.prod.iad2.secureserver.net';  // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                               // Enable SMTP authentication

		$mail->Username = 'info@remotestart.swautotoys.com';                 // SMTP username
		$mail->Password = 'Remotestart@123';
		$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 465;
		$mail->Priority = 1;                                    // TCP port to connect to

		$mail->From = $from_email;
		$mail->FromName = $name;//'NKDA Monthly Bill';
		$mail->addAddress('pritam@albatrossoft.com', '');     // Add a recipient               // Name is optional
		$mail->addBCC("");
		$mail->addReplyTo('', '');

		$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
		$mail->isHTML(true);

		$mail->Subject = $subject;
		if ($data == NULL) {
			$mail->Body = $template;
		} else {
			$mail->Body = "true";// $this->load->view($template, $data, true);
		}


		if (!$mail->send()) {
			//echo 'Message could not be sent.';
			//echo 'Mailer Error: ' . $mail->ErrorInfo;
			return FALSE;
		} else {
			return TRUE;
		}

		/*$config = array(
			'protocol' => 'smtp',
			'smtp_host' => "p3plcpnl0125.prod.phx3.secureserver.net",
			'smtp_port' => 465,
			'smtp_user' => "info@email.shopsnob.com",
			'smtp_pass' => "Albatross123",
			'mailtype' => 'html',
			'charset' => 'utf-8',
			'priority' => 1
		);
		$this->email->initialize($config);
		$this->email->from("info@email.shopsnob.com", 'The Snob Shop');
		$this->email->to($toEmail);
		$this->email->subject($subject);
		$this->email->message($this->load->view($template, $data, true));
		if($this->email->send()){
			return true;
		}else{
			return false;
		}
		echo $this->email->print_debugger();*/

	}


}



