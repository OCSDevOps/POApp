<?php

class Main_M extends MY_Model {

    function __construct() {
        parent::__construct();
    }

    public function checklogin($username, $password) {

        $sql = $this->db->select('*')->from($this->_users)->where('username', $username)->where('password', $password)->where('status', '1');
        $res = $this->db->get();
        //var_dump($sql);


        if ($res->num_rows() > 0) {
            $data = $res->row_array();
			//
			
            $this->session->set_userdata('uid', $data["u_id"]);
            $this->session->set_userdata('username', $data["username"]);
            $this->session->set_userdata('utype', $data["u_type"]);
            $this->session->set_userdata('loggedin', TRUE);
			$this->session->set_userdata('log_type','1');
            return true;
        } else {
            return false;
        }

        $res->free_result();
    }

    public function loggedin() {

        return (bool) $this->session->userdata('loggedin');
    }

    public function hash($string) {

        return hash('sha512', $string . config_item('encryption_key'));
	}
	
	public function checkAll_for_Mobile_Validate($mobile){
		$this->db->select('*');
		$this->db->from('frontend_users');
		$this->db->where('f_mobile', $mobile);
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	public function page_details($link){
		$this->db->select('*');
		$this->db->from('static_page');
		$this->db->where('url_link', $link);
		$query = $this->db->get();
		return $query->row();
	}
	
	public function addform_against_user_signup_update($rows, $f_uid = NULL){
		$this->db->set($rows);
		if($f_uid != NULL){
			$this->db->where('f_uid', $f_uid);
			if($this->db->update("frontend_users", $rows)){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			if($this->db->insert("frontend_users", $rows)){
				return TRUE;
			}else{
				return FALSE;
			}
	    }
	}
	
	/*((`event_start_datetime` >= '".$start_time."' AND `event_start_datetime` < '".$end_time."')
        OR (`event_end_datetime` > '".$start_time."' AND `event_end_datetime` <= '".$end_time."') OR
        (`event_start_datetime` <= '".$start_time."' AND `event_end_datetime` >= '".$end_time."'))*/
		
	public function check_Existing_Event_On_SameDate($start_date,$end_date){
		$this->db->select('*');
		$this->db->from('event_tab');
		$this->db->where("(event_startdate >= '".$start_date."' AND event_startdate < '".$end_date."')");
		$this->db->or_where("(event_enddate > '".$start_date."' AND event_enddate <= '".$end_date."')");
		$this->db->or_where("(event_startdate <= '".$start_date."' AND event_enddate >= '".$end_date."')");
		$this->db->where('event_approval', 1);
		/*$this->db->where('event_startdate <= '.$start_date.' AND event_enddate >= '.$start_date);
		$this->db->or_where('event_startdate <= '.$end_date.' AND event_enddate >= '.$end_date);
		$this->db->or_where('event_startdate > '.$start_date.' AND event_enddate < '.$end_date);*/
		$query = $this->db->get();
		//return $query->result();
		$result =  $query->num_rows();
        if($result > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	public function addform_against_user_Event_set($rows, $e_id = NULL){
		$this->db->set($rows);
		if($e_id != NULL){
			$this->db->where('event_no', $e_id);
			if($this->db->update("event_tab", $rows)){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			if($this->db->insert("event_tab", $rows)){
				return TRUE;
			}else{
				return FALSE;
			}
	    }
	}

	public function addupdate_copyfrw_form($rows, $cfid = NULL){
		$this->db->set($rows);
		if($cfid != NULL){
			$this->db->where('cf_id', $cfid);
			if($this->db->update("copy_fw_tab", $rows)){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			if($this->db->insert("copy_fw_tab", $rows)){
				return TRUE;
			}else{
				return FALSE;
			}
	    }
	}

	public function get_highest_memono_application(){
		$this->db->select_max('appli_m_no');
		$this->db->from('application_tab');
		$query = $this->db->get();
		return $query->row();
		 //$result->mno;
	}

	public function get_all_conditions_copys_DB($c_arr){
		$this->db->select('*');
		$this->db->from('copy_fw_tab');
		$this->db->where('cf_id IN ('.$c_arr.')');
		$query = $this->db->get();
		return $query->result();
	}

	public function addUpdate_against_movement_Incovid($rows, $idm_uid = NULL){
		$this->db->set($rows);
		if($idm_uid != NULL){
			$this->db->where('idm_id', $idm_uid);
			if($this->db->update("idm_application_tab", $rows)){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			if($this->db->insert("idm_application_tab", $rows)){
				return TRUE;
			}else{
				return FALSE;
			}
	    }
	}

	public function get_highest_memono_IN_IDM_application(){
		$this->db->select_max('idm_m_no');
		$this->db->from('idm_application_tab');
		$query = $this->db->get();
		return $query->row();
		 //$result->mno;
	}

	public function addupdate_FUser_inDB($rows, $uid = NULL){
		$this->db->set($rows);
		if($uid != NULL){
			$this->db->where('fuser_id', $uid);
			if($this->db->update("frontend_users", $rows)){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			if($this->db->insert("frontend_users", $rows)){
				return TRUE;
			}else{
				return FALSE;
			}
	    }
	}

	public function addupdate_Laboratory_inDB($rows, $lab_uid = NULL){
		$this->db->set($rows);
		if($lab_uid != NULL){
			$this->db->where('lab_id', $lab_uid);
			if($this->db->update("laboratory_tab", $rows)){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			if($this->db->insert("laboratory_tab", $rows)){
				return TRUE;
			}else{
				return FALSE;
			}
	    }
	}

	public function addform_against_LabourResult_covid($rows, $c_uid = NULL){
		$this->db->set($rows);
		if($c_uid != NULL){
			$this->db->where('collect_id', $c_uid);
			if($this->db->update("collection_application_tab", $rows)){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			if($this->db->insert("collection_application_tab", $rows)){
				return TRUE;
			}else{
				return FALSE;
			}
	    }
	}

	public function addUpdateform_against_BDO_inDB($row1, $row2, $c_uid, $row3 = NULL, $row4 = NULL){
		$this->db->trans_start();
		$this->db->set($row2);
		$this->db->insert('collection_app_details', $row2);
        
        $this->db->set($row1);
        $this->db->where('collect_id', $c_uid);
        $this->db->update("collection_application_tab",$row1);
		
		if($row3 != NULL){
			$this->db->set($row3);
			$this->db->insert('occupation_tab', $row3);
		}
		if($row4 != NULL){
			$this->db->set($row4);
			$this->db->insert('q_center_tab', $row4);
		}
        $this->db->trans_complete();
        if ($this->db->trans_status() === TRUE){
            return TRUE;
        }else{
            return FALSE;
        }
	}

	public function addUpdateform_against_PositiveCase_inDB($row1, $c_uid = NULL, $row3 = NULL){
		$this->db->trans_start();

		$this->db->set($row1);
		if($c_uid != NULL){
			$this->db->where('ca_master_caseid', $c_uid);
        	$this->db->update("positive_case_details",$row1);
		}else{
			$this->db->insert('positive_case_details', $row1);
		}
		if($row3 != NULL){
			$this->db->set($row3);
			$this->db->insert('safe_home_tab', $row3);
		}
		
		$this->db->trans_complete();
        if ($this->db->trans_status() === TRUE){
            return TRUE;
        }else{
            return FALSE;
        }
	}

	public function addUpdateform_against_VTMStock_inDB($rows, $row2, $distid){
		$this->db->trans_start();
		$this->db->set($rows);
		$this->db->insert('vtm_tab', $rows);
        
        $this->db->set($row2);
        $this->db->where('hd_id', $distid);
        $this->db->update("health_dist_tab",$row2);
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === TRUE) {
            return TRUE;
        }else{
            return FALSE;
        }	
	}
	/*public function addUpdateform_against_VTMStock_inDB($rows, $vtmid = NULL){
		$this->db->set($rows);
		if($vtmid != NULL){
			$this->db->where('vtm_id', $vtmid);
			if($this->db->update("vtm_tab", $rows)){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			if($this->db->insert("vtm_tab", $rows)){
				return TRUE;
			}else{
				return FALSE;
			}
	    }
	}*/

	public function get_All_Duplicate_SRF_against_application_Form(){
		$this->db->select('collect_srf');
		$this->db->from('collection_application_tab');
		$this->db->group_by('collect_srf');
		$this->db->having('COUNT(*) > 1 AND collect_srf IS NOT NULL');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_All_Duplicate_application_FormDB($srfarray){
		$this->db->select('collection_application_tab.*, block_tab.block_name, gp_tab.gp_name, health_dist_tab.hd_name, st.state_name, os.state_name as outstate_name, district_tab.dist_name, f_user_details.fuser_name');
		$this->db->from('collection_application_tab');
		$this->db->join('f_user_details', 'f_user_details.fuser_id = collection_application_tab.collect_createby');
		$this->db->join('health_dist_tab', 'health_dist_tab.hd_id = f_user_details.fuser_district','LEFT');
		$this->db->join('health_block_tab', 'health_block_tab.hb_id = f_user_details.fuser_details','LEFT');
		$this->db->join('block_tab', 'block_tab.block_id = collection_application_tab.collect_block','LEFT');
		$this->db->join('gp_tab', 'gp_tab.gp_id = collection_application_tab.collect_gp','LEFT');
		$this->db->join('state_tab st', 'st.state_id = collection_application_tab.collect_state','LEFT');
		$this->db->join('state_tab os', 'os.state_id = collection_application_tab.collect_outstate','LEFT');
		$this->db->join('district_tab', 'district_tab.dist_id = collection_application_tab.collect_outdist','LEFT');
		$this->db->where_in('collection_application_tab.collect_srf', $srfarray);
		$this->db->order_by('collection_application_tab.collect_srf DESC');
		$query = $this->db->get();
		return $query->result();
	}

	public function getAll_StockList_fromDB($hd_set = NULL){
		$this->db->select('f_user_details.*');
		$this->db->from('f_user_details');
		$this->db->join('health_dist_tab', 'health_dist_tab.hd_id = f_user_details.fuser_district');
		if($this->session->userdata('member_utype') >= 3){
		$this->db->where('f_user_details.fuser_id', $this->session->userdata('member_id'));
		}elseif($this->session->userdata('member_utype') == 2){
			$this->db->where('health_dist_tab.hd_dist', $hd_set);
		}
		$query = $this->db->get();
		return $query->result();
	}

	public function get_last_Balance_VTM_fromDB(){
		$this->db->select('*');
		$this->db->from('vtm_tab');
		$this->db->where('vtm_createby', $this->session->userdata('member_id'));
		$this->db->order_by('vtm_id DESC');
		$this->db->limit(1, 0);
		$query = $this->db->get();
		return $query->row();
	}

	public function getAll_VTMStockList_fromDB($hd_set = NULL){
		$this->db->select('vtm_tab.*, health_dist_tab.hd_name');
		$this->db->from('vtm_tab');
		$this->db->join('f_user_details', 'f_user_details.fuser_id = vtm_tab.vtm_createby');
		$this->db->join('health_dist_tab', 'health_dist_tab.hd_id = f_user_details.fuser_district');
		if($this->session->userdata('member_utype') == 3){
			$this->db->where('vtm_tab.vtm_createby', $this->session->userdata('member_id'));
		}
		if($this->session->userdata('member_utype') == 2){
			$this->db->where('health_dist_tab.hd_dist', $hd_set);
		}
		$this->db->order_by('vtm_tab.vtm_id DESC');
		$query = $this->db->get();
		return $query->result();
	}

	public function getAll_StockListfor_Dashboard_fromDB($hd_set = NULL, $user = NULL){
		$this->db->select('f_user_details.*');
		$this->db->from('f_user_details');
		$this->db->join('health_dist_tab', 'health_dist_tab.hd_id = f_user_details.fuser_district');
		if($this->session->userdata('member_utype') == 3){
		$this->db->where('f_user_details.fuser_id', $this->session->userdata('member_id'));
		}elseif($this->session->userdata('member_utype') == 2){
			$this->db->where('health_dist_tab.hd_dist', $hd_set);
		}elseif($this->session->userdata('member_utype') == 4){
			$this->db->where('f_user_details.fuser_id', $this->session->userdata('member_id'));
			$this->db->or_where_in('f_user_details.fuser_id', $user);
		}elseif($this->session->userdata('member_utype') == 5){
			$this->db->where('f_user_details.fuser_id', $this->session->userdata('member_id'));
			$this->db->or_where('f_user_details.fuser_id', $user);
		}
		$query = $this->db->get();
		return $query->result();
	}

	public function getAll_ForDashboard_FormList_fromDB($hd_set = NULL, $s_date = NULL, $e_date = NULL, $user = NULL){
		$this->db->select('collection_application_tab.*, block_tab.block_name, gp_tab.gp_name, health_dist_tab.hd_name, st.state_name, os.state_name as outstate_name, district_tab.dist_name');
		$this->db->from('collection_application_tab');
		$this->db->join('f_user_details', 'f_user_details.fuser_id = collection_application_tab.collect_createby');
		$this->db->join('health_dist_tab', 'health_dist_tab.hd_id = f_user_details.fuser_district','LEFT');
		$this->db->join('health_block_tab', 'health_block_tab.hb_id = f_user_details.fuser_details','LEFT');
		$this->db->join('block_tab', 'block_tab.block_id = collection_application_tab.collect_block','LEFT');
		$this->db->join('gp_tab', 'gp_tab.gp_id = collection_application_tab.collect_gp','LEFT');
		$this->db->join('state_tab st', 'st.state_id = collection_application_tab.collect_state','LEFT');
		$this->db->join('state_tab os', 'os.state_id = collection_application_tab.collect_outstate','LEFT');
		$this->db->join('district_tab', 'district_tab.dist_id = collection_application_tab.collect_outdist','LEFT');
		if($this->session->userdata('member_utype') == 3){
			$this->db->where('collection_application_tab.collect_createby', $this->session->userdata('member_id'));
		}elseif($this->session->userdata('member_utype') == 2){
			$this->db->where('health_dist_tab.hd_dist', $hd_set);
		}elseif($this->session->userdata('member_utype') == 4){
			$this->db->where('collection_application_tab.collect_createby', $this->session->userdata('member_id'));
			$this->db->or_where_in('collection_application_tab.collect_createby', $user);
		}elseif($this->session->userdata('member_utype') == 5){
			$this->db->where('collection_application_tab.collect_createby', $this->session->userdata('member_id'));
			$this->db->or_where('collection_application_tab.collect_createby', $user);
		}
		if($s_date != NULL){
			$this->db->where('collection_application_tab.collect_date >=', $s_date);
		}
		if($e_date != NULL){
			$this->db->where('collection_application_tab.collect_date <=', $e_date);
		}
		$this->db->order_by('collection_application_tab.collect_id DESC');
		$query = $this->db->get();
		return $query->result();
	}

	public function getAll_BlockWise_FormList_fromDB(){
		$this->db->select('collection_application_tab.*, block_tab.block_name, positive_case_details.pcase_release');
		$this->db->from('collection_application_tab');
		$this->db->join('positive_case_details', 'positive_case_details.ca_master_caseid = collection_application_tab.collect_id','LEFT');
		//$this->db->join('f_user_details', 'f_user_details.fuser_id = collection_application_tab.collect_createby');
		//$this->db->join('health_dist_tab', 'health_dist_tab.hd_id = f_user_details.fuser_district','LEFT');
		//$this->db->join('health_block_tab', 'health_block_tab.hb_id = f_user_details.fuser_details','LEFT');
		$this->db->join('block_tab', 'block_tab.block_id = collection_application_tab.collect_block','LEFT');
		$this->db->where('collection_application_tab.collect_swap',"Yes");
		$query = $this->db->get();
		return $query->result();
	}

	public function getAll_ForFinalResult_FormList_fromDB($user = NULL, $s_date = NULL, $e_date = NULL){
		$this->db->select('collection_application_tab.*, health_dist_tab.hd_name');
		$this->db->from('collection_application_tab');
		$this->db->join('f_user_details', 'f_user_details.fuser_id = collection_application_tab.collect_createby');
		$this->db->join('health_dist_tab', 'health_dist_tab.hd_id = f_user_details.fuser_district','LEFT');
		$this->db->join('health_block_tab', 'health_block_tab.hb_id = f_user_details.fuser_details','LEFT');
		$this->db->where('collection_application_tab.collect_swap',"Yes");
		//$this->db->join('block_tab', 'block_tab.block_id = collection_application_tab.collect_block','LEFT');
		//$this->db->join('gp_tab', 'gp_tab.gp_id = collection_application_tab.collect_gp','LEFT');
		//$this->db->join('state_tab st', 'st.state_id = collection_application_tab.collect_state','LEFT');
		//$this->db->join('state_tab os', 'os.state_id = collection_application_tab.collect_outstate','LEFT');
		//$this->db->join('district_tab', 'district_tab.dist_id = collection_application_tab.collect_outdist','LEFT');
		if($user != NULL){
			$this->db->where('collection_application_tab.collect_createby', $user);
		}
		if($s_date != NULL){
			$this->db->where('collection_application_tab.collect_date >=', $s_date);
		}
		if($e_date != NULL){
			$this->db->where('collection_application_tab.collect_date <=', $e_date);
		}
		$query = $this->db->get();
		return $query->result();
	}

	public function getAll_FormList_fromDB($hd_set = NULL, $s_date = NULL, $e_date = NULL){
		$this->db->select('collection_application_tab.*, block_tab.block_name, gp_tab.gp_name, health_dist_tab.hd_name, st.state_name, os.state_name as outstate_name, district_tab.dist_name, f_user_details.fuser_name');
		$this->db->from('collection_application_tab');
		$this->db->join('f_user_details', 'f_user_details.fuser_id = collection_application_tab.collect_createby');
		$this->db->join('health_dist_tab', 'health_dist_tab.hd_id = f_user_details.fuser_district','LEFT');
		$this->db->join('health_block_tab', 'health_block_tab.hb_id = f_user_details.fuser_details','LEFT');
		$this->db->join('block_tab', 'block_tab.block_id = collection_application_tab.collect_block','LEFT');
		$this->db->join('gp_tab', 'gp_tab.gp_id = collection_application_tab.collect_gp','LEFT');
		$this->db->join('state_tab st', 'st.state_id = collection_application_tab.collect_state','LEFT');
		$this->db->join('state_tab os', 'os.state_id = collection_application_tab.collect_outstate','LEFT');
		$this->db->join('district_tab', 'district_tab.dist_id = collection_application_tab.collect_outdist','LEFT');
		if($this->session->userdata('member_utype') >= 3){
			$this->db->where('collection_application_tab.collect_createby', $this->session->userdata('member_id'));
		}elseif($this->session->userdata('member_utype') == 2){
			$this->db->where('health_dist_tab.hd_dist', $hd_set);
		}
		if($s_date != NULL){
			$this->db->where('collection_application_tab.collect_date >=', $s_date);
		}
		if($e_date != NULL){
			$this->db->where('collection_application_tab.collect_date <=', $e_date);
		}
		$this->db->order_by('collection_application_tab.collect_id DESC');
		$query = $this->db->get();
		return $query->result();
	}

	public function getAll_Sample_noT_CollectFormList_fromDB($hd_set = NULL, $s_date = NULL, $e_date = NULL, $s_block = NULL){
		$this->db->select('collection_application_tab.*, block_tab.block_name, gp_tab.gp_name, health_dist_tab.hd_name, st.state_name, os.state_name as outstate_name, district_tab.dist_name, f_user_details.fuser_name');
		$this->db->from('collection_application_tab');
		$this->db->join('f_user_details', 'f_user_details.fuser_id = collection_application_tab.collect_createby');
		$this->db->join('health_dist_tab', 'health_dist_tab.hd_id = f_user_details.fuser_district','LEFT');
		$this->db->join('health_block_tab', 'health_block_tab.hb_id = f_user_details.fuser_details','LEFT');
		$this->db->join('block_tab', 'block_tab.block_id = collection_application_tab.collect_block','LEFT');
		$this->db->join('gp_tab', 'gp_tab.gp_id = collection_application_tab.collect_gp','LEFT');
		$this->db->join('state_tab st', 'st.state_id = collection_application_tab.collect_state','LEFT');
		$this->db->join('state_tab os', 'os.state_id = collection_application_tab.collect_outstate','LEFT');
		$this->db->join('district_tab', 'district_tab.dist_id = collection_application_tab.collect_outdist','LEFT');
		$this->db->where('collection_application_tab.collect_swap',"No");
		if($this->session->userdata('member_utype') >= 3){
			$this->db->where('collection_application_tab.collect_createby', $this->session->userdata('member_id'));
		}elseif($this->session->userdata('member_utype') == 2){
			$this->db->where('health_dist_tab.hd_dist', $hd_set);
		}
		if($s_block != NULL){
			$this->db->where('collection_application_tab.collect_block', $s_block);
		}
		if($s_date != NULL){
			$this->db->where('collection_application_tab.collect_date >=', $s_date);
		}
		if($e_date != NULL){
			$this->db->where('collection_application_tab.collect_date <=', $e_date);
		}
		$this->db->order_by('collection_application_tab.collect_id DESC');
		$query = $this->db->get();
		return $query->result();
	}

	public function getAll_Datewise_FormList_fromDB($sdate = NULL, $edate = NULL, $hd_set = NULL, $lab_set = NULL){
		$this->db->select('collection_application_tab.*, block_tab.block_name, gp_tab.gp_name, health_dist_tab.hd_name, st.state_name, os.state_name as outstate_name, district_tab.dist_name, f_user_details.fuser_name');
		$this->db->from('collection_application_tab');
		$this->db->join('f_user_details', 'f_user_details.fuser_id = collection_application_tab.collect_createby');
		$this->db->join('health_dist_tab', 'health_dist_tab.hd_id = f_user_details.fuser_district','LEFT');
		$this->db->join('health_block_tab', 'health_block_tab.hb_id = f_user_details.fuser_details','LEFT');
		$this->db->join('block_tab', 'block_tab.block_id = collection_application_tab.collect_block','LEFT');
		$this->db->join('gp_tab', 'gp_tab.gp_id = collection_application_tab.collect_gp','LEFT');
		$this->db->join('state_tab st', 'st.state_id = collection_application_tab.collect_state','LEFT');
		$this->db->join('state_tab os', 'os.state_id = collection_application_tab.collect_outstate','LEFT');
		$this->db->join('district_tab', 'district_tab.dist_id = collection_application_tab.collect_outdist','LEFT');
		if($sdate != NULL){
			$this->db->where('collection_application_tab.collect_date >=', $sdate);
		}
		if($edate != NULL){
			$this->db->where('collection_application_tab.collect_date <=', $edate);
		}
		if($hd_set != NULL){
			$this->db->where('health_dist_tab.hd_dist', $hd_set);
		}
		if($lab_set != NULL){
			$this->db->where('collection_application_tab.collect_lab', $lab_set);
		}
		$this->db->order_by('collection_application_tab.collect_id DESC');
		$query = $this->db->get();
		return $query->result();
	}

	public function getAll_PositiveResult_Forms_fromDB(){
		$this->db->select('collection_application_tab.*, block_tab.block_name, gp_tab.gp_name, health_dist_tab.hd_name, st.state_name, os.state_name as outstate_name, district_tab.dist_name, f_user_details.fuser_name, fud.fuser_name as update_user, positive_case_details.*');
		$this->db->from('collection_application_tab');
		$this->db->join('f_user_details', 'f_user_details.fuser_id = collection_application_tab.collect_createby');
		$this->db->join('health_dist_tab', 'health_dist_tab.hd_id = f_user_details.fuser_district','LEFT');
		$this->db->join('health_block_tab', 'health_block_tab.hb_id = f_user_details.fuser_details','LEFT');
		$this->db->join('block_tab', 'block_tab.block_id = collection_application_tab.collect_block','LEFT');
		$this->db->join('gp_tab', 'gp_tab.gp_id = collection_application_tab.collect_gp','LEFT');
		$this->db->join('state_tab st', 'st.state_id = collection_application_tab.collect_state','LEFT');
		$this->db->join('state_tab os', 'os.state_id = collection_application_tab.collect_outstate','LEFT');
		$this->db->join('district_tab', 'district_tab.dist_id = collection_application_tab.collect_outdist','LEFT');
		$this->db->join('f_user_details fud', 'fud.fuser_id = collection_application_tab.collect_modifyby','LEFT');
		$this->db->join('positive_case_details', 'positive_case_details.ca_master_caseid = collection_application_tab.collect_id','LEFT');
		$this->db->where('collection_application_tab.collect_swap',"Yes");
		$this->db->where('collection_application_tab.collect_result',"Positive");
		$this->db->order_by('collection_application_tab.collect_id DESC');
		$query = $this->db->get();
		return $query->result();
	}

	public function getAll_PositiveResult_UpdateForms_fromDB(){
		$this->db->select('collection_application_tab.*, block_tab.block_name, gp_tab.gp_name, health_dist_tab.hd_name, st.state_name, os.state_name as outstate_name, district_tab.dist_name, f_user_details.fuser_name, fud.fuser_name as update_user, positive_case_details.*');
		$this->db->from('collection_application_tab');
		$this->db->join('f_user_details', 'f_user_details.fuser_id = collection_application_tab.collect_createby');
		$this->db->join('positive_case_details', 'positive_case_details.ca_master_caseid = collection_application_tab.collect_id');
		$this->db->join('health_dist_tab', 'health_dist_tab.hd_id = f_user_details.fuser_district','LEFT');
		$this->db->join('health_block_tab', 'health_block_tab.hb_id = f_user_details.fuser_details','LEFT');
		$this->db->join('block_tab', 'block_tab.block_id = collection_application_tab.collect_block','LEFT');
		$this->db->join('gp_tab', 'gp_tab.gp_id = collection_application_tab.collect_gp','LEFT');
		$this->db->join('state_tab st', 'st.state_id = collection_application_tab.collect_state','LEFT');
		$this->db->join('state_tab os', 'os.state_id = collection_application_tab.collect_outstate','LEFT');
		$this->db->join('district_tab', 'district_tab.dist_id = collection_application_tab.collect_outdist','LEFT');
		$this->db->join('f_user_details fud', 'fud.fuser_id = collection_application_tab.collect_modifyby','LEFT');
		$this->db->where('collection_application_tab.collect_swap',"Yes");
		$this->db->where('collection_application_tab.collect_result',"Positive");
		$this->db->order_by('collection_application_tab.collect_id DESC');
		$query = $this->db->get();
		return $query->result();
	}

	public function getAll_Resultwise_FormList_fromDB($rset = NULL, $r_report = NULL, $lab_set = NULL, $hd_set = NULL){
		$this->db->select('collection_application_tab.*, block_tab.block_name, gp_tab.gp_name, health_dist_tab.hd_name, st.state_name, os.state_name as outstate_name, district_tab.dist_name, f_user_details.fuser_name, fud.fuser_name as update_user');
		$this->db->from('collection_application_tab');
		$this->db->join('f_user_details', 'f_user_details.fuser_id = collection_application_tab.collect_createby');
		$this->db->join('health_dist_tab', 'health_dist_tab.hd_id = f_user_details.fuser_district','LEFT');
		$this->db->join('health_block_tab', 'health_block_tab.hb_id = f_user_details.fuser_details','LEFT');
		$this->db->join('block_tab', 'block_tab.block_id = collection_application_tab.collect_block','LEFT');
		$this->db->join('gp_tab', 'gp_tab.gp_id = collection_application_tab.collect_gp','LEFT');
		$this->db->join('state_tab st', 'st.state_id = collection_application_tab.collect_state','LEFT');
		$this->db->join('state_tab os', 'os.state_id = collection_application_tab.collect_outstate','LEFT');
		$this->db->join('district_tab', 'district_tab.dist_id = collection_application_tab.collect_outdist','LEFT');
		$this->db->join('f_user_details fud', 'fud.fuser_id = collection_application_tab.collect_modifyby','LEFT');
		$this->db->where('collection_application_tab.collect_swap',"Yes");
		if($this->session->userdata('member_utype') == 3){
			$this->db->where('collection_application_tab.collect_createby', $this->session->userdata('member_id'));
		}elseif($this->session->userdata('member_utype') == 2){
			$this->db->where('health_dist_tab.hd_dist', $hd_set);
		}
		if($rset != NULL){
			if($rset == "Received"){
				$this->db->where('collection_application_tab.collect_result IS NOT NULL');
			}elseif($rset == "Pending"){
				$this->db->where('collection_application_tab.collect_result IS NULL');
			}
		}
		if($r_report != NULL){
			$this->db->where('collection_application_tab.collect_result', $r_report);
		}
		if($lab_set != NULL){
			$this->db->where('collection_application_tab.collect_lab', $lab_set);
		}
		$this->db->order_by('collection_application_tab.collect_id DESC');
		$query = $this->db->get();
		return $query->result();
	}

	public function getAll_BlockMuni_wise_FormList_fromDB($s_block = NULL, $s_muni = NULL, $sdate = NULL, $edate = NULL){
		$this->db->select('collection_application_tab.*, block_tab.block_name, gp_tab.gp_name, health_dist_tab.hd_name, st.state_name, os.state_name as outstate_name, district_tab.dist_name, collection_app_details.*');
		$this->db->from('collection_application_tab');
		$this->db->join('f_user_details', 'f_user_details.fuser_id = collection_application_tab.collect_createby');
		$this->db->join('health_dist_tab', 'health_dist_tab.hd_id = f_user_details.fuser_district','LEFT');
		$this->db->join('health_block_tab', 'health_block_tab.hb_id = f_user_details.fuser_details','LEFT');
		$this->db->join('collection_app_details', 'collection_app_details.ca_master_id = collection_application_tab.collect_id','LEFT');
		$this->db->join('block_tab', 'block_tab.block_id = collection_application_tab.collect_block','LEFT');
		$this->db->join('gp_tab', 'gp_tab.gp_id = collection_application_tab.collect_gp','LEFT');
		$this->db->join('state_tab st', 'st.state_id = collection_application_tab.collect_state','LEFT');
		$this->db->join('state_tab os', 'os.state_id = collection_application_tab.collect_outstate','LEFT');
		$this->db->join('district_tab', 'district_tab.dist_id = collection_application_tab.collect_outdist','LEFT');
		if($s_block != NULL){
			$this->db->where('collection_application_tab.collect_block', $s_block);
		}
		if($s_muni != NULL){
			$this->db->where('collection_application_tab.collect_munici', $s_muni);
		}
		if($sdate != NULL){
			$this->db->where('collection_application_tab.collect_date >=', $sdate);
		}
		if($edate != NULL){
			$this->db->where('collection_application_tab.collect_date <=', $edate);
		}
		$this->db->order_by('collection_application_tab.collect_id DESC');
		$query = $this->db->get();
		return $query->result();
	}

	public function getAll_BlockMuni_wise_UpdatedFormList($s_block = NULL, $s_muni = NULL){
		$this->db->select('collection_application_tab.*, block_tab.block_name, gp_tab.gp_name, health_dist_tab.hd_name, st.state_name, os.state_name as outstate_name, district_tab.dist_name, collection_app_details.*, f_user_details.fuser_name');
		$this->db->from('collection_application_tab');
		$this->db->join('f_user_details', 'f_user_details.fuser_id = collection_application_tab.collect_createby');
		$this->db->join('health_dist_tab', 'health_dist_tab.hd_id = f_user_details.fuser_district','LEFT');
		$this->db->join('health_block_tab', 'health_block_tab.hb_id = f_user_details.fuser_details','LEFT');
		$this->db->join('collection_app_details', 'collection_app_details.ca_master_id = collection_application_tab.collect_id');
		$this->db->join('block_tab', 'block_tab.block_id = collection_application_tab.collect_block','LEFT');
		$this->db->join('gp_tab', 'gp_tab.gp_id = collection_application_tab.collect_gp','LEFT');
		$this->db->join('state_tab st', 'st.state_id = collection_application_tab.collect_state','LEFT');
		$this->db->join('state_tab os', 'os.state_id = collection_application_tab.collect_outstate','LEFT');
		$this->db->join('district_tab', 'district_tab.dist_id = collection_application_tab.collect_outdist','LEFT');
		if($s_block != NULL){
			$this->db->where('collection_application_tab.collect_block', $s_block);
		}
		if($s_muni != NULL){
			$this->db->where('collection_application_tab.collect_munici', $s_muni);
		}
		$this->db->order_by('collection_application_tab.collect_id DESC');
		$query = $this->db->get();
		return $query->result();
	}

	public function getAll_Statewise_MigrantList_fromDB($sdate = NULL, $edate = NULL, $state_id = NULL, $hd_set = NULL){
		$this->db->select('collection_application_tab.*, block_tab.block_name, gp_tab.gp_name, health_dist_tab.hd_name, st.state_name, os.state_name as outstate_name, district_tab.dist_name');
		$this->db->from('collection_application_tab');
		$this->db->join('f_user_details', 'f_user_details.fuser_id = collection_application_tab.collect_createby');
		$this->db->join('health_dist_tab', 'health_dist_tab.hd_id = f_user_details.fuser_district','LEFT');
		$this->db->join('health_block_tab', 'health_block_tab.hb_id = f_user_details.fuser_details','LEFT');
		$this->db->join('block_tab', 'block_tab.block_id = collection_application_tab.collect_block','LEFT');
		$this->db->join('gp_tab', 'gp_tab.gp_id = collection_application_tab.collect_gp','LEFT');
		$this->db->join('state_tab st', 'st.state_id = collection_application_tab.collect_state','LEFT');
		$this->db->join('state_tab os', 'os.state_id = collection_application_tab.collect_outstate','LEFT');
		$this->db->join('district_tab', 'district_tab.dist_id = collection_application_tab.collect_outdist','LEFT');
		$this->db->where('collection_application_tab.collect_worker', 'Yes');
		if($this->session->userdata('member_utype') == 3){
			$this->db->where('collection_application_tab.collect_createby', $this->session->userdata('member_id'));
		}elseif($this->session->userdata('member_utype') == 2){
			$this->db->where('health_dist_tab.hd_dist', $hd_set);
		}
		if($sdate != NULL){
			$this->db->where('collection_application_tab.collect_date >=', $sdate);
		}
		if($edate != NULL){
			$this->db->where('collection_application_tab.collect_date <=', $edate);
		}
		if($state_id != NULL){
			$this->db->where('collection_application_tab.collect_outstate', $state_id);
		}
		$this->db->order_by('collection_application_tab.collect_id DESC');
		$query = $this->db->get();
		return $query->result();
	}

	public function getAll_SwabNot_CollectedList_fromDB($sdate = NULL, $edate = NULL, $state_id = NULL, $hd_set = NULL){
		$this->db->select('collection_application_tab.*, block_tab.block_name, gp_tab.gp_name, health_dist_tab.hd_name, st.state_name, os.state_name as outstate_name, district_tab.dist_name');
		$this->db->from('collection_application_tab');
		$this->db->join('f_user_details', 'f_user_details.fuser_id = collection_application_tab.collect_createby');
		$this->db->join('health_dist_tab', 'health_dist_tab.hd_id = f_user_details.fuser_district','LEFT');
		$this->db->join('health_block_tab', 'health_block_tab.hb_id = f_user_details.fuser_details','LEFT');
		$this->db->join('block_tab', 'block_tab.block_id = collection_application_tab.collect_block','LEFT');
		$this->db->join('gp_tab', 'gp_tab.gp_id = collection_application_tab.collect_gp','LEFT');
		$this->db->join('state_tab st', 'st.state_id = collection_application_tab.collect_state','LEFT');
		$this->db->join('state_tab os', 'os.state_id = collection_application_tab.collect_outstate','LEFT');
		$this->db->join('district_tab', 'district_tab.dist_id = collection_application_tab.collect_outdist','LEFT');
		$this->db->where('collection_application_tab.collect_swap', 'No');
		if($this->session->userdata('member_utype') == 3){
			$this->db->where('collection_application_tab.collect_createby', $this->session->userdata('member_id'));
		}elseif($this->session->userdata('member_utype') == 2){
			$this->db->where('health_dist_tab.hd_dist', $hd_set);
		}
		if($sdate != NULL){
			$this->db->where('collection_application_tab.collect_date >=', $sdate);
		}
		if($edate != NULL){
			$this->db->where('collection_application_tab.collect_date <=', $edate);
		}
		if($state_id != NULL){
			$this->db->where('collection_application_tab.collect_outstate', $state_id);
		}
		$this->db->order_by('collection_application_tab.collect_id DESC');
		$query = $this->db->get();
		return $query->result();
	}

	public function getAll_dist_loc_FromDistrictHospital(){
		$this->db->distinct();
		$this->db->select('hd_dist');
		$this->db->from('health_dist_tab');
		$query = $this->db->get();
		return $query->result();
	}

	/*public function getAll_Hdistrictwise_FormList_fromDB($hd_set = NULL){
		$this->db->select('collection_application_tab.*,f_user_details.hd_name, f_user_details.hd_dist');
		$this->db->from('collection_application_tab');
		$this->db->join('f_user_details', 'f_user_details.fuser_id = collection_application_tab.collect_createby');
		$this->db->join('health_dist_tab', 'health_dist_tab.hd_id = f_user_details.fuser_district');
		$this->db->where('health_dist_tab.hd_dist', $hd_set);
		$this->db->order_by('collection_application_tab.collect_id DESC');
		$query = $this->db->get();
		return $query->result();
	}

	public function getAll_Labwise_FormList_fromDB($lab_set = NULL){
		$this->db->select('collection_application_tab.*,f_user_details.hd_name, f_user_details.hd_dist');
		$this->db->from('collection_application_tab');
		$this->db->join('f_user_details', 'f_user_details.fuser_id = collection_application_tab.collect_createby');
		$this->db->where('collection_application_tab.collect_lab', $lab_set);
		$this->db->order_by('collection_application_tab.collect_id DESC');
		$query = $this->db->get();
		return $query->result();
	}*/








	public function get_all_Distinct_Year_against_voucher($sec_uri){
		$this->db->distinct();
		$this->db->select('file_year');
		$this->db->from('content_files_tab');
		$this->db->join('section_tab','section_tab.section_id = content_files_tab.file_section');
		$this->db->where('section_tab.section_uri', $sec_uri);
		$this->db->where('content_files_tab.f_status', '1');
		$this->db->order_by('content_files_tab.file_year ASC');
		$query = $this->db->get();
		return $query->result();
	}
	
	public function search_All_Data_against_Parameter($s_year, $s_date, $s_date_end, $s_voucher, $s_party, $s_content, $section_id = NULL){
		$this->db->select('*');
		$this->db->from('content_files_tab');
		$this->db->join('section_tab','section_tab.section_id = content_files_tab.file_section');
		if($s_content == ''){
			if($s_year != ''){
				$this->db->where('content_files_tab.file_year', $s_year);
			}
			if($s_date != '' & $s_date_end != ''){
				$this->db->where('content_files_tab.file_date >= ', $s_date);
				$this->db->where('content_files_tab.file_date <= ', $s_date_end);
			}
			if($s_voucher != ''){
				$this->db->like('content_files_tab.file_voucher_no', $s_voucher);
			}
			if($s_party != ''){
				$this->db->like('content_files_tab.file_party_name', $s_party);
			}
		}
		if($section_id != NULL){
			$this->db->where('content_files_tab.file_section', $section_id);
		}
		$this->db->where('content_files_tab.f_status', '1');
		$this->db->order_by('content_files_tab.file_date ASC');
		$query = $this->db->get();
		return $query->result();
	}












    public function update_adminuser_modified($now) {
        $id = $this->session->userdata('uid');

        $this->db->set($now);
        $this->db->where('u_id', $id);
        $this->db->update($this->_users);
    }
    
    public function update_all_timeStatus_Exam($r_array, $setid, $u_id){
		
		$this->db->set($r_array);
        $this->db->where('stu_set_id', $setid);
        $this->db->where('exam_unique_id', $u_id);
        if($this->db->update("student_xam_set", $r_array)){
            return TRUE;
        }else{
            return FALSE;
        }
	}
	
	public function StudentRegistration_SaveUpdate_inDB($rows, $row1, $student_id = NULL) {
        
        if($student_id == NULL){
        	$this->db->trans_start();
			$this->db->set($rows);
			$this->db->insert("student_information", $rows);
	        $user_id = $this->db->insert_id();
	        
	        $row1['std_master_id'] = $user_id;
	        $this->db->set($row1);
	        $this->db->insert("student_details",$row1);
	        
	        $this->db->trans_complete();
	        if ($this->db->trans_status() === TRUE) {
	            return TRUE;
	        }else{
	            return FALSE;
	        }
        }elseif($student_id != NULL){
        	$this->db->trans_start();
			$this->db->set($rows);
			$this->db->where('student_id', $student_id);
			$this->db->update("student_information", $rows);
			
			if($row1 != NULL){
				$this->db->set($row1);
				$this->db->where('std_master_id', $student_id);
				$this->db->update("student_details", $row1);
			}
			
			$this->db->trans_complete();
	        if ($this->db->trans_status() === TRUE) {
	            return TRUE;
	        }else{
	            return FALSE;
	        }
        }	
    }
	
	public function get_username_from_ForgotAccess($stu_dob, $stu_mobile, $stu_email){
		$this->db->select('*');
		$this->db->from('student_information');
		$this->db->join('student_details', 'student_details.std_master_id = student_information.student_id');
		$this->db->where('student_details.std_dob', $stu_dob);
		$this->db->where('student_details.std_phone', $stu_mobile);
		$this->db->where('student_details.std_email_id', $stu_email);
		$this->db->where('student_status', 1);
		$query = $this->db->get();
		return $query->row();
	}
	
	public function getAllDetails_ofAMember_againstID($stu_id, $stu_uname = NULL){
		$this->db->select('*');
		$this->db->from('student_information');
		$this->db->join('student_details', 'student_details.std_master_id = student_information.student_id');
		if($stu_id != NULL){
			$this->db->where('student_information.student_id', $stu_id);
		}elseif($stu_uname != NULL){
			$this->db->where('student_information.student_username', $stu_uname);
		}
		//$this->db->where('student_status', 1);
		$query = $this->db->get();
		return $query->row();
	}
	
	public function get_Details_ForA_product($packid = NULL){
		$current_date = date('Y-m-d');
		$this->db->select('*');
		$this->db->from('package_master');
		$this->db->join('package_set_details', 'package_set_details.pak_set_master_id = package_master.package_id');
		$this->db->where('package_master.package_id', $packid);
		$this->db->where('package_master.package_expire_date >=', $current_date);
		$query = $this->db->get();
		return $query->result();
	}
	
	public function store_OrderDetails_BeforePay($rows, $o_id = NULL){
		$this->db->set($rows);
		if($o_id != NULL){
			$this->db->where('order_id', $o_id);
			if($this->db->update("order_master", $rows)){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			$this->db->insert("order_master", $rows);
        	$order_id = $this->db->insert_id();
        	return $order_id;
		}
		
	}
	
	public function storeSlave_OrderDetails_BeforePay($rows, $order_sid = NULL){
		$this->db->set($rows);
		if($order_sid != NULL){
			$this->db->where('order_master_id', $order_sid);
			if($this->db->update("order_slave", $rows)){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			$this->db->insert("order_slave", $rows);
	        $order_slave_id = $this->db->insert_id();
	        return $order_slave_id;
	    }
	}
	
	public function storeSet_AgainStudent_AfterPay($rows, $xam_sid = NULL){
		
		$this->db->set($rows);
		if($xam_sid != NULL){
			$this->db->where('set_id', $xam_sid);
			if($this->db->update("student_xam_set", $rows)){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			if($this->db->insert("student_xam_set", $rows)){
				return TRUE;
			}else{
				return FALSE;
			}
	    }
	
	}
	
	public function get_All_XamAfter_Package_Buy($student_id, $order_id = NULL, $pack_id = NULL, $xam_setid = NULL, $xam_unqid = NULL){
		
		$current_date = date('Y-m-d');
		$this->db->select('*');
		$this->db->from('student_xam_set');
		$this->db->join('package_master', 'package_master.package_id = student_xam_set.exam_package_id');
		$this->db->join('order_slave', 'order_slave.order_master_id = student_xam_set.student_order_id AND order_slave.order_packageid = student_xam_set.exam_package_id');
		$this->db->where('student_xam_set.student_id', $student_id);
		$this->db->where('student_xam_set.stu_set_status', 1);
		if($order_id != NULL && $pack_id != NULL && $xam_setid != NULL){
			$this->db->where('date(order_slave.order_package_expire) >=', $current_date);
			$this->db->where('student_xam_set.student_order_id', $order_id);
			$this->db->where('student_xam_set.exam_package_id', $pack_id);
			$this->db->where('student_xam_set.stu_set_id', $xam_setid);
			if($xam_unqid != NULL){
				$this->db->where('student_xam_set.exam_unique_id', $xam_unqid);
			}
			$query = $this->db->get();
			return $query->row();
		}else{
			$this->db->order_by('student_xam_set.stu_set_id ASC');
			$query = $this->db->get();
			return $query->result();
		}
		
	}
	
	public function get_All_XamAfter_ExamPackage_Buy($student_id, $order_id = NULL, $pack_id = NULL, $xam_setid = NULL, $xam_unqid = NULL){
		
		$current_date = date('Y-m-d');
		$this->db->select('*');
		$this->db->from('student_xam_set');
		$this->db->join('package_master', 'package_master.package_id = student_xam_set.exam_package_id');
		$this->db->join('order_slave', 'order_slave.order_master_id = student_xam_set.student_order_id AND order_slave.order_packageid = student_xam_set.exam_package_id');
		$this->db->where('student_xam_set.student_id', $student_id);
		$this->db->where('student_xam_set.stu_set_status', 1);
		if($order_id != NULL && $pack_id != NULL && $xam_setid != NULL){
			$this->db->where('date(order_slave.order_package_expire) >=', $current_date);
			$this->db->where('student_xam_set.student_order_id', $order_id);
			$this->db->where('student_xam_set.exam_package_id', $pack_id);
			$this->db->where('student_xam_set.exam_set_id', $xam_setid);
			if($xam_unqid != NULL){
				$this->db->where('student_xam_set.exam_unique_id', $xam_unqid);
			}
			$query = $this->db->get();
			return $query->row();
		}else{
			$this->db->order_by('student_xam_set.stu_set_id ASC');
			$query = $this->db->get();
			return $query->result();
		}
		
	}
	
	public function get_All_Subject_Details_fromSet($set_id){
		$this->db->select('*');
		$this->db->from('set_subject_module');
		$this->db->join('subject_master', 'subject_master.subject_id = set_subject_module.set_module_subject');
		$this->db->where('set_subject_module.set_id', $set_id);
		$query = $this->db->get();
		return $query->result();
	}
	
	public function getAll_OrderPackages_Against_student($student_id){
		$this->db->select('*');
		$this->db->from('order_master');
		$this->db->join('order_slave', 'order_slave.order_master_id = order_master.order_id');
		$this->db->join('package_master', 'package_master.package_id = order_slave.order_packageid','LEFT');
		$this->db->where('order_master.order_createby', $student_id);
		$this->db->where('order_master.order_status', 1);
		$query = $this->db->get();
		return $query->result();
	}
	
	public function get_All_MetarialsAfter_Package_Buy($student_id, $order_id = NULL, $pack_id = NULL, $xam_setid = NULL){
		
		$current_date = date('Y-m-d');
		$this->db->select('*');
		$this->db->from('order_master');
		$this->db->join('order_slave', 'order_slave.order_master_id = order_master.order_id');
		$this->db->join('package_master', 'package_master.package_id = order_slave.order_packageid','LEFT');
		$this->db->join('package_set_details', 'package_set_details.pak_set_master_id = package_master.package_id','LEFT');
		$this->db->where('order_master.order_createby', $student_id);
		$this->db->where('package_set_details.pak_set_type', 2);
		$this->db->where('order_master.order_status', 1);
		$this->db->where('date(order_slave.order_package_expire) >=', $current_date);
		
		if($order_id != NULL && $pack_id != NULL && $xam_setid != NULL){
			$this->db->where('order_master.order_id', $order_id);
			$this->db->where('order_master.order_id', $pack_id);
			$this->db->where('order_master.order_id', $xam_setid);
			$query = $this->db->get();
			return $query->row();
		}else{
			$this->db->order_by('order_master.order_id DESC');
			$query = $this->db->get();
			return $query->result();
		}
		
	}
	
	public function Get_Full_Question_from_Set($setid){
		$this->db->select('*');
		$this->db->from('set_master');
		$this->db->join('set_subject_module', 'set_subject_module.set_id = set_master.set_id');
		$this->db->join('set_question_module', 'set_question_module.set_module_id = set_subject_module.set_module_id');
		$this->db->where('set_master.set_id', $setid);
		$query = $this->db->get();
		return $query->result();
	}
	
	public function addNewQuestion_fromSet_forExam($rows, $xam_qs_uid = NULL){
		$this->db->set($rows);
		if($xam_qs_uid != NULL){
			$this->db->where('exm_qid', $xam_qs_uid);
			if($this->db->update("stu_xamset_question", $rows)){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			if($this->db->insert("stu_xamset_question", $rows)){
				return TRUE;
			}else{
				return FALSE;
			}
	    }
	}
	
	public function get_all_module_against_SetID($setid){
		$this->db->select('*');
		$this->db->from('set_master');
		$this->db->join('set_subject_module', 'set_subject_module.set_id = set_master.set_id');
		$this->db->join('subject_master', 'subject_master.subject_id = set_subject_module.set_module_subject');
		//$this->db->join('set_question_module', 'set_question_module.set_module_id = set_subject_module.set_module_id AND set_question_module.set_id = set_subject_module.set_id','LEFT');
		$this->db->where('set_master.set_id', $setid);
		$this->db->order_by('set_subject_module.set_module_id ASC');
		$query = $this->db->get();
		return $query->result();
	}
	
	public function get_all_Question_against_SetExamStart($student_id, $orderid, $packid, $setid){
		$this->db->select('*');
		$this->db->from('stu_xamset_question');
		$this->db->join('question_bank_slave', 'question_bank_slave.qu_slave_id = stu_xamset_question.qs_slv_id AND question_bank_slave.qu_master_id = stu_xamset_question.qs_master_id');
		$this->db->join('question_bank_master', 'question_bank_master.question_id = stu_xamset_question.qs_master_id');
		$this->db->where('stu_xamset_question.student_id', $student_id);
		$this->db->where('stu_xamset_question.st_order_id', $orderid);
		$this->db->where('stu_xamset_question.st_pack_id', $packid);
		$this->db->where('stu_xamset_question.st_set_id', $setid);
		$this->db->order_by('stu_xamset_question.st_set_module_id ASC');
		$query = $this->db->get();
		return $query->result();
	}
	
	
	public function Student_XamAnswer_Save_inDB($rows, $student_ans_id = NULL){
		$this->db->set($rows);
		if($student_ans_id == NULL){
			if($this->db->insert('student_xamset_answer'))
	        	return TRUE;
	        else
	        	return FALSE;
	    }elseif($student_ans_id != NULL){
	    	$this->db->where('stu_answer_id', $student_ans_id);
			if($this->db->update('student_xamset_answer', $rows))
	        	return TRUE;
	        else
	        	return FALSE;
		}
	}
	
	public function getAll_SevenDays_Before_Application_FormDB($collect_date){
		$this->db->select('collection_application_tab.*');
		$this->db->from('collection_application_tab');
		$this->db->join('f_user_details', 'f_user_details.fuser_id = collection_application_tab.collect_createby');
		/*$this->db->join('health_dist_tab', 'health_dist_tab.hd_id = f_user_details.fuser_district','LEFT');
		$this->db->join('health_block_tab', 'health_block_tab.hb_id = f_user_details.fuser_details','LEFT');
		$this->db->join('block_tab', 'block_tab.block_id = collection_application_tab.collect_block','LEFT');
		$this->db->join('gp_tab', 'gp_tab.gp_id = collection_application_tab.collect_gp','LEFT');
		$this->db->join('state_tab st', 'st.state_id = collection_application_tab.collect_state','LEFT');
		$this->db->join('state_tab os', 'os.state_id = collection_application_tab.collect_outstate','LEFT');
		$this->db->join('district_tab', 'district_tab.dist_id = collection_application_tab.collect_outdist','LEFT');*/
		if($collect_date != NULL){
			$this->db->where('collection_application_tab.collect_date', $collect_date);
		}
		$this->db->order_by('collection_application_tab.collect_id DESC');
		$query = $this->db->get();
		return $query->result();
	}

	public function getAll_SendToHomeList_fromDB($s_date = NULL, $e_date = NULL){
		$this->db->select('collection_application_tab.*, block_tab.block_name, gp_tab.gp_name, health_dist_tab.hd_name, st.state_name, os.state_name as outstate_name, district_tab.dist_name, f_user_details.fuser_name');
		$this->db->from('collection_application_tab');
		$this->db->join('f_user_details', 'f_user_details.fuser_id = collection_application_tab.collect_createby');
		$this->db->join('health_dist_tab', 'health_dist_tab.hd_id = f_user_details.fuser_district','LEFT');
		$this->db->join('health_block_tab', 'health_block_tab.hb_id = f_user_details.fuser_details','LEFT');
		$this->db->join('block_tab', 'block_tab.block_id = collection_application_tab.collect_block','LEFT');
		$this->db->join('gp_tab', 'gp_tab.gp_id = collection_application_tab.collect_gp','LEFT');
		$this->db->join('state_tab st', 'st.state_id = collection_application_tab.collect_state','LEFT');
		$this->db->join('state_tab os', 'os.state_id = collection_application_tab.collect_outstate','LEFT');
		$this->db->join('district_tab', 'district_tab.dist_id = collection_application_tab.collect_outdist','LEFT');
		$this->db->where('collection_application_tab.collect_sendhome IS NOT NULL');
		if($s_date != NULL){
			$this->db->where('collection_application_tab.collect_date >=', $s_date);
		}
		if($e_date != NULL){
			$this->db->where('collection_application_tab.collect_date <=', $e_date);
		}
		$this->db->order_by('collection_application_tab.collect_id DESC');
		$query = $this->db->get();
		return $query->result();
	}

	public function updateform_against_SendHome_covid($rows, $row_array){
		$this->db->set($rows);
		$this->db->where_in('collect_id', $row_array);
		if($this->db->update("collection_application_tab", $rows)){
			return TRUE;
		}else{
			return FALSE;
		}
	}

	
}
