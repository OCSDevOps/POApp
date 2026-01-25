<?php
class Frontend_Controller extends MY_Controller {

	function __construct() {		
		parent::__construct();

 		/*$this->load->model('main_m');
		$this->data['menu_1']=$this->main_m->menu_catagory(1);
		$this->data['menu_2']=$this->main_m->menu_catagory(2);
		$this->data['menu_3']=$this->main_m->menu_catagory(3);
		$this->data['menu_4']=$this->main_m->menu_catagory(4);
		$this->data['footer'] = $this->main_m->footer_link_list(2);*/
 
	}
	 

	protected function sendSMTPEmail($toEmail, $subject, $template, $data = NULL){
        
        require_once 'PHPMailer/class.phpmailer.php';
		require_once 'PHPMailer/class.smtp.php';
		
		$mail = new PHPMailer;
		
		
		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = 'sg2plcpnl0242.prod.sin2.secureserver.net';  // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                               // Enable SMTP authentication

		$mail->Username = 'noreply@coochbehargmch.org';                 // SMTP username
		$mail->Password = 's1b1VzY[%I]@';
		$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 465;
		$mail->Priority = 1;                                    // TCP port to connect to
		
		$mail->From = 'aeozp-bnk@nic.in';
		$mail->FromName = 'Bankura Admin';
		$mail->addAddress($toEmail, '');     // Add a recipient               // Name is optional
		//$mail->addBCC("amit@albatrossoft.com");
		$mail->addReplyTo('aeozp-bnk@nic.in', 'Bankura Support');
		
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