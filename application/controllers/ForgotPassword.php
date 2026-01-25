<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ForgotPassword extends Admin_Controller {
	
	public function index()
	{


		$this->load->model('admin_m');
		$this->load->model('Procore_Model');
		
		// Redirect a user if he's already logged in
		$dashboard = 'admincontrol/dashboard';
		$this->admin_m->loggedin() == FALSE || redirect($dashboard);

		$email = $this->input->post('email');

		echo json_encode('asdasd');
		die();

		if ($_POST) {
				    
		          $uid = $this->input->post('username');
		          $pwd = $this->input->post('password');
				  
				    
				    $this->form_validation->set_error_delimiters('<span style="color:#F00;font-size:10px;">', '</span>');
		            
		            $this->form_validation->set_rules("username", "UserName", "trim|required|xss_clean");
		            $this->form_validation->set_rules("password", "Password", "trim|required|xss_clean");
		          
		          
						// Process the form

		            	if ($this->form_validation->run() == TRUE) {
		            	
			                if ($this->loginprocess($uid, $pwd) == true) {
			                    //redirect($this->input->server('HTTP_REFERER'));
								$countRows=$this->db->get_where('permission_master',['pt_id'=>$this->session->userdata('pt_id'),'status'=>1])->num_rows();
			                    if($countRows!=0 || $this->session->userdata('utype')==1){
									$now = array(
										'modify_date' => date('Y-m-d H:i:s'),
										'access_ip' => $this->input->ip_address()
									);

									$this->admin_m->update_adminuser_modified($now);
									$this->updateBli();
									redirect($dashboard);
								}else{
									$this->data["error"] = 'Please contact admin for permissions';
								}
			                } else {
			                    $this->data["error"] = 'Sorry Wrong Username or Password';
			                }
			            
		            	}
					
		     }
			
          $this->load->view('admin/login', $this->data);
		
	}
	
	public function loginprocess($uid, $pwd){
		$username = $this->security->sanitize_filename($uid);
		$password = $this->security->sanitize_filename($this->admin_m->hash($pwd));
		//$usertype = $utype;
		//var_dump($password);exit;
	
		$boolean = $this->admin_m->checklogin($username,$password);
			
		return $boolean;
	
	
	}

}
