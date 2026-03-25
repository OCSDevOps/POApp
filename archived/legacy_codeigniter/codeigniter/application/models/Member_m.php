<?php

class Member_M extends MY_Model {

    function __construct() {
        parent::__construct();
    }
	
	public function checklogin_member($username, $password) {
        //$sql = $this->db->select('*')->from('frontend_users')->where('f_mobile', $username)->where('f_password', $password)->where('f_status', 1)->where('f_active', 1);
        $sql = $this->db->select('*')->from('frontend_users')->where('f_mobile', $username)->where('f_password', $password)->where('f_status', 1);
        $res = $this->db->get();

        if ($res->num_rows() > 0) {
            $data = $res->row_array();
            $this->session->set_userdata('member_id', $data["f_uid"]);
            $this->session->set_userdata('member_uname', $data["f_mobile"]);
            $this->session->set_userdata('member_fname', $data["f_full_name"]);
            $this->session->set_userdata('member_utype', $data["f_utype"]);
            $this->session->set_userdata('member_loggedin', TRUE);
            return TRUE;
        } else {
            return FALSE;
        }

        $res->free_result();
    }
	
    public function member_loggedin() {

        return (bool) $this->session->userdata('member_loggedin');
    }
    
    public function update_user_modified($now, $id = NULL) {
        $id = $this->session->userdata('member_id');

        $this->db->set($now);
        $this->db->where('f_uid', $id);
        $this->db->update('frontend_users', $now);
    }
    
    public function member_loggedin_check() {
		
		$this->db->select('*');
		$this->db->from('student_information');
		$this->db->where('student_id', $this->session->userdata('member_id'));
		$this->db->where('student_username', $this->session->userdata('member_uname'));
		$this->db->where('student_login_ip', $this->session->userdata('member_loginip'));
		$this->db->where('student_session_id', $this->session->userdata('member_uniquedevice_ip'));
		$this->db->where('student_status', 1);
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
		
    }
    
    public function logout() {

        $this->session->sess_destroy();
    }
    
    public function Show_StudentDetailsIDwise($member_id, $member_uname){
		$this->db->select('*');
		$this->db->from('student_information');
		$this->db->join('student_details', 'student_details.std_master_id = student_information.student_id');
		$this->db->where('student_information.student_id', $member_id);
		$this->db->where('student_information.student_username', $member_uname);
		$this->db->where('student_status', 1);
		$query = $this->db->get();
		return $query->row();
    }
    
    public function change_existingUser_password($rows, $fid) {
		
		$this->db->set($rows);
        $this->db->where('f_uid', $fid);
        if($this->db->update("frontend_users", $rows)){
            return TRUE;
        }else{
            return FALSE;
        }
		
    }
	
	public function addform_against_UserQuery_inDB($rows, $qid = NULL){
		$this->db->set($rows);
		if($qid != NULL){
			$this->db->where('query_no', $qid);
			if($this->db->update("query_tab", $rows)){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			if($this->db->insert("query_tab", $rows)){
				return TRUE;
			}else{
				return FALSE;
			}
	    }
	}

    /*public function getMembers($id = NULL) {

        if ($id != NULL) {
            $filter = $this->_primary_filter;
            $id = $filter($id);
            $method = 'row';
            $this->db->join('users', 'userdetails.userid = users.id and users.utype >= 3 and users.id =' . $id);
        } else {
            $this->db->join('users', 'userdetails.userid = users.id and users.utype >= 3');
            $method = 'result';
        }

        $this->db->select('*');
        $this->db->from('userdetails');
        $this->db->order_by("users.id", "desc");
        $query = $this->db->get();
        return $query->$method();
    }*/

    public function hash($string) {

        return hash('sha512', $string . config_item('encryption_key'));
    }

    public function updateUserDetails($firstname, $lastname, $s_question, $s_answer, $pwd, $arr, $id) {
        $this->db->trans_start();
        if ($pwd != "") {
            $row = array(
                "password" => $this->hash($pwd),
            );
            $this->db->where("u_id", $id);
            $this->db->update("user_info", $row);
        }
        $rows = array(
            "firstname" => $firstname,
            "lastname" => $lastname,
            "security_question" => $s_question,
            "security_answer" => $s_answer
        );
        $this->db->where("u_id", $id);
        $this->db->update("user_info", $rows);
        
        //////////////////////////////////
        $this->db->select('*');
        $this->db->from('user_details');
        $this->db->where("uid", $id);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
        	$this->db->where("uid", $id);
        	$this->db->update("user_details", $arr);
        } else {
			$arr['uid'] = $id;
			$this->db->set($arr);
			$this->db->insert("user_details");
		}
        
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return false;
        } else {
            return true;
        }
    }

}
