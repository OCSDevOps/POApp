<?php
class Member_Controller extends MY_Controller {

	public $sessmembersessionid = NULL;
	public $sessmemberaccessip = NULL;
	public $sessmemberid = NULL;
	public $sessmemberuname = NULL;
	function __construct() {
		parent::__construct();
		
		$this->load->model('member_m');
		//$this->load->model('cms_m');
		
		$exception_uris = array(
				'member/logout'
		);
		
		if (isset($this->session->userdata['member_id'])) {
			//$this->sessmembersessionid = $this->session->userdata('member_uniquedevice_ip');
			$this->sessmemberid = $this->session->userdata('member_id');
			$this->sessmemberuname = $this->session->userdata('member_uname');
			//$this->sessmemberaccessip = $this->session->userdata('member_loginip');
		}
		if (in_array(uri_string(), $exception_uris) == FALSE) {
			if ($this->member_m->member_loggedin() == FALSE) {
				
				//redirect('admin/user/login');
				//$this->member_m->logout();
				redirect('login');
			}			

		}

	}
	
	protected function sendSMTPEmail($toEmail, $subject, $template, $data = NULL){
        
        require_once 'PHPMailer/class.phpmailer.php';
		require_once 'PHPMailer/class.smtp.php';
		
		$mail = new PHPMailer;
		
		
		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = 'cp-in-10.webhostbox.net';  // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                               // Enable SMTP authentication

		$mail->Username = 'noreply@addataxonline.in';                 // SMTP username
		$mail->Password = 'Pass@DDA#!Ma!L';
		$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 465;
		$mail->Priority = 1;                                    // TCP port to connect to
		
		$mail->From = 'noreply@addataxonline.in';
		$mail->FromName = 'ADDA Admin';
		$mail->addAddress($toEmail, '');     // Add a recipient               // Name is optional
		//$mail->addBCC("amit@albatrossoft.com");
		$mail->addReplyTo('info@addataxonline.in', 'ADDA Support');
		
		$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
		$mail->isHTML(true);                                  // Set email format to HTML
		
		$mail->Subject = $subject;
		if($data == NULL){
			$mail->Body = $template;
		}else{
			$mail->Body = $this->load->view($template, $data, true);
		}
		 
		
		if(!$mail->send()) {
			//echo 'Message could not be sent.';
			//echo 'Mailer Error: ' . $mail->ErrorInfo;
			return FALSE;
		}
		else
		{	return TRUE;  }
		
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