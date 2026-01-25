<?php


use App\Models\EmailTemplate;

class Admin_M extends MY_Model
{

	function __construct()
	{
		parent::__construct();
	}

	public function checklogin($username, $password)
	{

		$sql = $this->db->select('*')->from($this->_adminusers)->where('username', $username)->where('password', $password)->where('status', '1');
		$res = $this->db->get();
		//var_dump($sql);


		if ($res->num_rows() > 0) {
			$data = $res->row_array();
			//

			$this->session->set_userdata('uid', $data["u_id"]);
			$this->session->set_userdata('username', $data["username"]);
			$this->session->set_userdata('utype', $data["u_type"]);
			$this->session->set_userdata('uaccess', $data["u_access"]);
			$this->session->set_userdata('loggedin', TRUE);
			$this->session->set_userdata('pt_id', $data["pt_id"]);
			$this->session->set_userdata('log_type', '1');
			return true;
		} else {
			return false;
		}

		$res->free_result();
	}

	public function loggedin()
	{

		return (bool)$this->session->userdata('loggedin');
	}

	public function hash($string)
	{

		return hash('sha512', $string . config_item('encryption_key'));
	}

	public function get_ALLdocument_for_DMS($id = NULL)
	{

		$this->db->select('*');
		$this->db->from('content_files_tab');
		$this->db->join('section_tab', 'section_tab.section_id = content_files_tab.file_section');
		//$this->db->where('content_files_tab.f_status','1');
		$query = $this->db->get();
		return $query->result();
	}

	public function update_adminuser_modified($now)
	{
		$id = $this->session->userdata('uid');

		$this->db->set($now);
		$this->db->where('u_id', $id);
		$this->db->update($this->_adminusers);
	}

	public function GetDetailsofUsers($uid)
	{
		$this->db->select('user_info.*, master_user_type.mu_name');
		$this->db->from('user_info');
		$this->db->join('master_user_type', 'master_user_type.mu_id = user_info.u_type');
		$this->db->where('u_id', $uid);
		$query = $this->db->get();
		return $query->row();
	}

	public function getUsersByIDs($ids)
	{
		$this->db->select('user_info.*');
		$this->db->from('user_info');
		$this->db->where_in('u_id', $ids);
		$query = $this->db->get();
		return $query->result();
	}

	public function getPurchaseOrderByID($id)
	{
		$this->db->select('*');
		$this->db->from('purchase_order_master');
		$this->db->where('porder_id',$id);
		$query = $this->db->get();
		return $query->row();
	}

	public function getProjectDetails($id)
	{
		$this->db->select('project_details.*');
		$this->db->from('project_details');
		$this->db->where('pdetail_proj_ms',$id);
		$query = $this->db->get();
		return $query->result();
	}

	public function get_Users_List()
	{
		$this->db->select('*');
		$this->db->from('user_info');
		$query = $this->db->get();
		return $query->result();
	}

	public function GetEmailTemplateWithKey($key)
	{
		$this->db->select('*');
		$this->db->from('template_master');
		$this->db->where('email_key', $key);
		$query = $this->db->get();
		return $query->row();
	}

	public function GetEmailTemplateWithID($id)
	{
		$this->db->select('*');
		$this->db->from('template_master');
		$this->db->where('id', $id);
		$query = $this->db->get();
		return $query->row();
	}

	public function GetDetailsofSupplier($sid)
	{
		$this->db->select('supplier_master.*');
		$this->db->from('supplier_master');
		$this->db->where('sup_id', $sid);
		$query = $this->db->get();
		return $query->row();
	}

	public function searchallAdminUser()
	{
		$this->db->select('user_info.*, master_user_type.mu_name as parent_type');
		$this->db->from('user_info');
		$this->db->join('master_user_type', 'master_user_type.mu_id = user_info.u_type', 'LEFT');
		$this->db->where('u_id !=', 1);
		$query = $this->db->get();
		return $query->result();
	}

	public function getTotalPO($proj_id = 0)
	{

		$this->db->where('porder_type != "null"');
		if($proj_id != 0)
		{
			$this->db->where('porder_project_ms',$proj_id);
		}

		$query = $this->db->get('purchase_order_master');
		return $query->num_rows();
	}

	public function getPendingPO($proj_id=0)
	{
		$this->db->where('porder_type != "null"');
		$this->db->where('porder_general_status', 'pending');
		if($proj_id != 0)
		{
			$this->db->where('porder_project_ms',$proj_id);
		}
		$query = $this->db->get('purchase_order_master');
		return $query->num_rows();
	}

	public function getSubmittedPO($proj_id=0)
	{
		$this->db->where('porder_type != "null"');
		$this->db->where('porder_general_status', 'submitted');
		if($proj_id != 0)
		{
			$this->db->where('porder_project_ms',$proj_id);
		}
		$query = $this->db->get('purchase_order_master');
		return $query->num_rows();
	}

	public function getRTEPO($proj_id=0)
	{
		$this->db->where('porder_type != "null"');
		$this->db->where('integration_status', 'rte');
		if($proj_id != 0)
		{
			$this->db->where('porder_project_ms',$proj_id);
		}
		$query = $this->db->get('purchase_order_master');
		return $query->num_rows();
	}

	public function getTotalReceive()
	{
		$this->db->select('*');
		$this->db->group_by('rorder_porder_ms');
		$query = $this->db->get('receive_order_master');
		return $query->num_rows();
	}

	public function getPartiallyReceive($proj_id=0)
	{
		$this->db->where('porder_delivery_status', '2');
		if($proj_id != 0)
		{
			$this->db->where('porder_project_ms',$proj_id);
		}
		$query = $this->db->get('purchase_order_master');
		return $query->num_rows();
	}

	public function getIntegrationSyncPO($proj_id=0)
	{
		$this->db->where('integration_status', 'synced');
		if($proj_id != 0)
		{
			$this->db->where('porder_project_ms',$proj_id);
		}
		$query = $this->db->get('purchase_order_master');
		return $query->num_rows();
	}

	public function getPOByType($type,$proj_id=0)
	{
		$this->db->where('porder_type', $type);
		if($proj_id != 0)
		{
			$this->db->where('porder_project_ms',$proj_id);
		}
		$query = $this->db->get('purchase_order_master');
		return $query->num_rows();
	}

	public function getIntegrationPendingPO($proj_id=0)
	{
		$this->db->where('integration_status', 'pending');
		if($proj_id != 0)
		{
			$this->db->where('porder_project_ms',$proj_id);
		}
		$query = $this->db->get('purchase_order_master');
		return $query->num_rows();
	}

	public function getFullyReceive($proj_id=0)
	{
		$this->db->where('porder_delivery_status', '1');
		if($proj_id != 0)
		{
			$this->db->where('porder_project_ms',$proj_id);
		}
		$query = $this->db->get('purchase_order_master');
		return $query->num_rows();
	}

	public function getNotReceive($proj_id=0)
	{
		$this->db->where('porder_delivery_status', '0');
		if($proj_id != 0)
		{
			$this->db->where('porder_project_ms',$proj_id);
		}
		$query = $this->db->get('purchase_order_master');
		return $query->num_rows();
	}

	public function saveNewUser($rows, $row1)
	{
		$this->db->trans_start();
		$this->db->set($rows);
		$this->db->insert($this->_adminusers, $rows);
		$user_id = $this->db->insert_id();

		$row1['uid'] = $user_id;
		$this->db->set($row1);
		$this->db->insert("user_details", $row1);

		$this->db->trans_complete();
		if ($this->db->trans_status() === TRUE) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function allSuppliers_InsertUpdate($rows, $s_id = NULL)
	{
		$this->db->set($rows);
		if ($s_id != NULL) {
			$this->db->where('supp_id', $s_id);
			if ($this->db->update("supplier_master", $rows)) {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			if ($this->db->insert("supplier_master", $rows)) {
				return TRUE;
			} else {
				return FALSE;
			}
		}
	}

	public function check_supplier_mobile_exist($mobileno, $sid = NULL)
	{
		$this->db->select('*');
		$this->db->from('supplier_master');
		$this->db->where('supp_mobile', $mobileno);
		if ($sid != NULL)
			$this->db->where('supp_id != ', $sid);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	public function getUserDetails($uid) {

		$this->db->select('user_info.*, user_details.address');
		$this->db->from('user_info');
		$this->db->join('user_details', 'user_details.uid = user_info.u_id','left');
		$this->db->where('u_id', $uid);
		$query = $this->db->get();
		return $query->row();
	}

	public function allGuideline_Instruction_InsertUpdate($rows, $g_id = NULL)
	{
		$this->db->set($rows);
		if ($g_id != NULL) {
			$this->db->where('gi_id', $g_id);
			if ($this->db->update("gudie_instruct_tab", $rows)) {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			if ($this->db->insert("gudie_instruct_tab", $rows)) {
				return TRUE;
			} else {
				return FALSE;
			}
		}
	}

	public function check_phone_exist($mobile, $uid = NULL)
	{
		$this->db->select('*');
		$this->db->from('user_info');
		$this->db->where('phone', $mobile);
		if ($uid != NULL)
			$this->db->where('u_id != ', $uid);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	public function check_email_exist($emailid, $uid = NULL)
	{
		$this->db->select('*');
		$this->db->from('user_info');
		$this->db->where('email', $emailid);
		if ($uid != NULL)
			$this->db->where('u_id != ', $uid);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	public function check_password_exist($passward, $uid)
	{
		$this->db->select('*');
		$this->db->from($this->_adminusers);
		$this->db->where('u_id', $uid);
		$this->db->where('password', $passward);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function check_username_exist($username, $uid = NULL)
	{
		$this->db->select('*');
		$this->db->from('user_info');
		$this->db->where('username', $username);
		if ($uid != NULL)
			$this->db->where('u_id != ', $uid);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	public function check_usertype_Already_exist($utype, $uid = NULL)
	{
		$this->db->select('*');
		$this->db->from('user_info');
		$this->db->where('u_type', $utype);
		if ($uid != NULL)
			$this->db->where('u_id != ', $uid);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	public function check_usertype_Already_existinDistrict($p_utype, $dist, $uid = NULL)
	{
		$this->db->select('*');
		$this->db->from('user_info');
		$this->db->where('u_masteruser', $p_utype);
		$this->db->where('u_dist', $dist);
		if ($uid != NULL)
			$this->db->where('u_id != ', $uid);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	public function change_user_status($uid, $cng_status)
	{
		$data = array(
			'status' => $cng_status
		);
		$this->db->where('u_id', $uid);
		if ($this->db->update($this->_adminusers, $data)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function get_all_permitApplication_details($uid)
	{
		$this->db->select('user_permission_tab.*, permit_application.papp_name');
		$this->db->from('user_permission_tab');
		$this->db->join('permit_application', 'permit_application.papp_id = user_permission_tab.up_application');
		$this->db->where('user_permission_tab.up_master_user', $uid);
		$query = $this->db->get();
		return $query->result();
	}

	public function permission_Inserted_DB($rows, $p_id = NULL)
	{
		$this->db->set($rows);
		if ($p_id != NULL) {
			$this->db->where('up_id', $p_id);
			if ($this->db->update("user_permission_tab", $rows)) {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			if ($this->db->insert("user_permission_tab", $rows)) {
				return TRUE;
			} else {
				return FALSE;
			}
		}
	}

	public function permission_Exist_inDB_forUser($p_app, $users)
	{
		$this->db->select('*');
		$this->db->from('user_permission_tab');
		$this->db->where('up_master_user', $users);
		$this->db->where('up_application', $p_app);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/*public function UpdateSavedUser($rows, $uid)
	{
		$this->db->set($rows);
        $this->db->where('u_id', $uid);
        if($this->db->update($this->_adminusers))
        	return TRUE;
        else
        	return FALSE;
	}*/

	public function getAll_discussion_byUser_fromDB($q_no = NULL)
	{
		$this->db->select('query_tab.*, f_user_views.*');
		$this->db->from('query_tab');
		$this->db->join('f_user_views', 'f_user_views.f_uid = query_tab.query_user');
		if ($q_no != NULL) {
			$this->db->where('query_tab.query_no', $q_no);
			$query = $this->db->get();
			return $query->row();
		} else {
			$this->db->order_by('query_tab.query_id', 'DESC');
			$query = $this->db->get();
			return $query->result();
		}
	}

	public function UpdateSavedUser_Password($rows, $uid)
	{
		$this->db->set($rows);
		$this->db->where('u_id', $uid);
		if ($this->db->update($this->_adminusers, $rows)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function UpdateSavedUser($rows, $row1, $uid)
	{
		$this->db->trans_start();
		$this->db->set($rows);
		$this->db->where('u_id', $uid);
		$this->db->update($this->_adminusers, $rows);

		$this->db->set($row1);
		$this->db->where('uid', $uid);
		$this->db->update("user_details", $row1);

		$this->db->trans_complete();
		if ($this->db->trans_status() === TRUE) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function common_Insertion_in_DB($row_arrary, $table_name)
	{

		// $result=$this->db->where(['item_code'=>$row_arrary['item_code'],'item_status'=>1])->get('item_master')->num_rows();
		// if($result>0){
		// 	$this->db->where(['item_code'=>$row_arrary['item_code'],'item_status'=>1]);
		// 	$row_arrary['item_modifydate']=date('Y-m-d H:i:s');
		// 	if($this->db->update($table_name,$row_arrary)){
		// 		return TRUE;
		// 	}else{
		// 		return FALSE;
		// 	}
		// }else{
			$this->db->set($row_arrary);
			if ($this->db->insert($table_name, $row_arrary)) {
				return TRUE;
			} else {
				return FALSE;
			}
		// }
	}

	public function common_Insertion_in_DB_Items($row_arrary, $table_name)
	{

		$result=$this->db->where(['item_code'=>$row_arrary['item_code'],'item_status'=>1])->get('item_master')->num_rows();
		if($result>0){
			$this->db->where(['item_code'=>$row_arrary['item_code'],'item_status'=>1]);
			$row_arrary['item_modifydate']=date('Y-m-d H:i:s');
			if($this->db->update($table_name,$row_arrary)){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			$this->db->set($row_arrary);
			if ($this->db->insert($table_name, $row_arrary)) {
				return TRUE;
			} else {
				return FALSE;
			}
		}
	}

	public function common_Insertion_in_DB_SupCat($row_arrary, $table_name)
	{

		$result=$this->db->where(['supcat_item_code'=>$row_arrary['supcat_item_code'],'supcat_status'=>1])->get($table_name)->num_rows();
		if($result>0){
			$this->db->where(['supcat_item_code'=>$row_arrary['supcat_item_code'],'supcat_status'=>1]);
			$row_arrary['supcat_modifydate']=date('Y-m-d H:i:s');
			if($this->db->update($table_name,$row_arrary)){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			$this->db->set($row_arrary);
			if ($this->db->insert($table_name, $row_arrary)) {
				return TRUE;
			} else {
				return FALSE;
			}
		}
	}

	public function common_Insertion_in_DB_SupCat_Prices($row_arrary,$supcat_supplier,$supcat_item_code, $table_name)
	{

		$result=$this->db->get_where($table_name,['supcat_supplier'=>$supcat_supplier,'supcat_item_code'=>$supcat_item_code,'supcat_status'=>1])->num_rows();
		if($result>0){
			$this->db->where(['supcat_supplier'=>$supcat_supplier,'supcat_item_code'=>$supcat_item_code,'supcat_status'=>1]);
			$row_arrary['supcat_modifydate']=date('Y-m-d H:i:s');
			if($this->db->update($table_name,$row_arrary)){
				return TRUE;
			}else{
				return FALSE;
			}
		}
	}

	public function checkNotificationUsage($table, $key, $value)
	{
		$this->db->select('*');
		$this->db->from($table);
		$this->db->where('key', $key);
		$this->db->where('value', $value);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function check_usage($table, $column, $value)
	{
		$this->db->select('*');
		$this->db->from($table);
		$this->db->where($column, $value);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function common_Updation_in_DB($row_arrary, $table_name, $table_column, $column_value)
	{
		$this->db->set($row_arrary);
		$this->db->where($table_column, $column_value);
		if ($this->db->update($table_name, $row_arrary)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function common_InsertorUpdation_in_DB($row_arrary, $table_name, $id)
	{
		$this->db->where('id', $id);
		$q = $this->db->get($table_name);
		$this->db->reset_query();

		if ($q->num_rows() > 0) {

			if ($this->db->where('id', $id)->update($table_name, $row_arrary)) {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {

			if ($this->db->set('id', $id)->insert($table_name, $row_arrary)) {
				return TRUE;
			} else {
				return FALSE;
			}
		}

	}

	public function common_InsertorUpdationNotification($row_array, $table_name)
	{
		$this->db->where('key', $row_array['key']);
		$q = $this->db->get($table_name);
		$this->db->reset_query();

		if ($q->num_rows() > 0) {

			if ($this->db->where('key', $row_array['key'])->update($table_name, $row_array)) {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {

			if ($this->db->set('key', $row_array['key'])->insert($table_name, $row_array)) {
				return TRUE;
			} else {
				return FALSE;
			}
		}

	}

	public function common_Insertion_in_DB_with_ID($row_arrary, $table_name)
	{
		$this->db->set($row_arrary);
		if ($this->db->insert($table_name, $row_arrary)) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return FALSE;
		}
	}

	public function get_All_purchaseorder_list_fromDB($recordid = NULL, $filter = [], $type = "Material PO")
	{
		$this->db->select('purchase_order_master.*, purchase_order_master.porder_type, supplier_master.sup_name, project_master.proj_name');
		$this->db->from('purchase_order_master');
		$this->db->join('project_master', 'project_master.proj_id = purchase_order_master.porder_project_ms');
		$this->db->join('supplier_master', 'supplier_master.sup_id = purchase_order_master.porder_supplier_ms');
		$this->db->where('purchase_order_master.porder_type', $type);
		if(!empty($filter)){
			$this->db->where(($filter));
		}
		if ($recordid != NULL) {
			$this->db->where('purchase_order_master.porder_id', $recordid);
			$query = $this->db->get();
			return $query->row();
		} else {
			$this->db->order_by('purchase_order_master.porder_id', 'DESC');
			$query = $this->db->get();
			return $query->result();
		}
	}

	public function get_All_rfq_list_fromDB($recordid = NULL, $filter = [])
	{
		$this->db->select('request_purchase_order.*, supplier_master.sup_name, project_master.proj_name');
		$this->db->from('request_purchase_order');
		$this->db->join('project_master', 'project_master.proj_id = request_purchase_order.rporder_project_ms');
		$this->db->join('supplier_master', 'supplier_master.sup_id = request_purchase_order.rporder_supplier_ms');
		$this->db->where($filter);
		if ($recordid != NULL) {
			$this->db->where('request_purchase_order.rporder_id', $recordid);
			$query = $this->db->get();
			return $query->row();
		} else {
			$this->db->order_by('request_purchase_order.rporder_id', 'DESC');
			$query = $this->db->get();
			return $query->result();
		}
	}

	public function get_TotalQtyofPOByPoID($po_id)
	{
		$this->db->select_sum('po_detail_quantity');
		$this->db->from('purchase_order_details');
		$this->db->where('purchase_order_details.po_detail_porder_ms', $po_id);
		$query = $this->db->get();
		return $query->row()->po_detail_quantity;
	}


	public function get_All_rentalorder_list_fromDB($recordid = NULL, $filter = [])
	{
		$this->db->select('purchase_order_master.*, supplier_master.sup_name, project_master.proj_name');
		$this->db->from('purchase_order_master');
		$this->db->join('project_master', 'project_master.proj_id = purchase_order_master.porder_project_ms');
		$this->db->join('supplier_master', 'supplier_master.sup_id = purchase_order_master.porder_supplier_ms');
		$this->db->where('purchase_order_master.porder_type', 'Rental PO');
		$this->db->where($filter);
		if ($recordid != NULL) {
			$this->db->where('purchase_order_master.porder_id', $recordid);
			$query = $this->db->get();
			return $query->row();
		} else {
			$this->db->order_by('purchase_order_master.porder_id', 'DESC');
			$query = $this->db->get();
			return $query->result();
		}
	}

	public function get_All_SupplierCatalog_Set($recordid = NULL, $filter = [])
	{
		$this->db->select('item_category_tab.icat_name,cost_code_master.cc_no,cost_code_master.cc_description,unit_of_measure_tab.uom_name,supplier_catalog_tab.*, item_master.item_name, item_master.item_code, item_master.item_is_rentable,supplier_master.sup_email, supplier_master.sup_name, unit_of_measure_tab.uom_name');
		$this->db->from('supplier_catalog_tab');
		$this->db->join('item_master', 'item_master.item_code = supplier_catalog_tab.supcat_item_code','left');
		$this->db->join('supplier_master', 'supplier_master.sup_id = supplier_catalog_tab.supcat_supplier','left');
		$this->db->join('unit_of_measure_tab', 'item_master.item_unit_ms = unit_of_measure_tab.uom_id','left');
		$this->db->join('cost_code_master', 'item_master.item_ccode_ms = cost_code_master.cc_id','left');
		$this->db->join('item_category_tab', 'item_master.item_cat_ms = item_category_tab.icat_id','left');
		if ($recordid != NULL) {
			$this->db->where('supplier_catalog_tab.supcat_id', $recordid);
			$query = $this->db->get();
			return $query->row();
		} else {
			$this->db->where($filter);
			$this->db->order_by('supplier_catalog_tab.supcat_id', 'DESC');
			$query = $this->db->get();
			return $query->result();
		}
	}
	
	public function get_info_details($recordid = NULL, $filter = [])
	{
		$this->db->select('item_category_tab.icat_name,cost_code_master.cc_no,cost_code_master.cc_description,unit_of_measure_tab.uom_name');
		$this->db->from('item_master');
		$this->db->join('unit_of_measure_tab', 'item_master.item_unit_ms = unit_of_measure_tab.uom_id','left');
		$this->db->join('cost_code_master', 'item_master.item_ccode_ms = cost_code_master.cc_id','left');
		$this->db->join('item_category_tab', 'item_master.item_cat_ms = item_category_tab.icat_id','left');
		$this->db->where('item_master.item_code', $recordid);
		$query = $this->db->get();
		return $query->row();
	}

	public function get_CompanySMTP_Setting()
	{
		$this->db->select('*');
		$this->db->from('company_setting_master');
		$this->db->where('id', 1);
		$query = $this->db->get();
		return $query->row();
	}

	public function getCompanySetting()
	{
		$this->db->select('*');
		$this->db->from('company_tab');
		$this->db->where('company_id', 1);
		$query = $this->db->get();
		return $query->row();
	}

	public function get_Notification_SettingByKey($key)
	{
		$this->db->select('*');
		$this->db->from('notification_setting_master');
		$this->db->where('key', $key);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return $query->row()->value;
		}
		return null;
	}

	public function get_EmailTemplate()
	{
		$this->db->select('*');
		$this->db->from('template_master');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_All_ItemBundle_Set($itmid = NULL, $filter=[])
	{
		$this->db->select('item_master.*, cost_code_master.cc_no, cost_code_master.cc_description, item_category_tab.icat_name, unit_of_measure_tab.uom_name');
		$this->db->from('item_master');
		$this->db->join('item_category_tab', 'item_category_tab.icat_id = item_master.item_cat_ms');
		$this->db->join('cost_code_master', 'cost_code_master.cc_id = item_master.item_ccode_ms');
		$this->db->join('unit_of_measure_tab', 'unit_of_measure_tab.uom_id = item_master.item_unit_ms');
		// $this->db->where($filter);

		if ($itmid != NULL) {
			$this->db->where('item_master.item_id', $itmid);
			$query = $this->db->get();
			return $query->row();
		} else {
			$this->db->where($filter);
			$this->db->order_by('item_master.item_id', 'DESC');
			$query = $this->db->get();
			return $query->result();
		}
	}

	public function get_All_Items()
	{
		$this->db->select('*');
		$this->db->from('item_master');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_All_UOM()
	{
		$this->db->select('*');
		$this->db->from('unit_of_measure_tab');
		$query = $this->db->get();
		return $query->result();
	}

	public function GetSupplier_Item_cataLog_search($supp, $item)
	{
		$this->db->select('supplier_catalog_tab.*, supplier_master.sup_name');
		$this->db->from('supplier_catalog_tab');
		//$this->db->join('item_master','item_master.item_code = supplier_catalog_tab.supcat_item_code');
		$this->db->join('supplier_master', 'supplier_master.sup_id = supplier_catalog_tab.supcat_supplier');
		//$this->db->join('unit_of_measure_tab','unit_of_measure_tab.uom_id = supplier_catalog_tab.supcat_uom');
		$this->db->where('supplier_catalog_tab.supcat_supplier', $supp);
		$this->db->where('supplier_catalog_tab.supcat_item_code', $item);
		$query = $this->db->get();
		return $query->row();
	}

	public function get_detail_tax_group($tax_group)
	{
		$this->db->select('*');
		$this->db->from('taxgroup_master');
		$this->db->where('id', $tax_group);
		$query = $this->db->get();
		return $query->row();
	}

	public function GetCCode_from_ItemCode_serch($itmcode)
	{
		$this->db->select('item_master.*, cost_code_master.cc_no, cost_code_master.cc_id, item_category_tab.icat_name, unit_of_measure_tab.uom_name, unit_of_measure_tab.uom_id');
		$this->db->from('item_master');
		$this->db->join('item_category_tab', 'item_category_tab.icat_id = item_master.item_cat_ms');
		$this->db->join('cost_code_master', 'cost_code_master.cc_id = item_master.item_ccode_ms');
		$this->db->join('unit_of_measure_tab', 'unit_of_measure_tab.uom_id = item_master.item_unit_ms');
		$this->db->where('item_master.item_code', $itmcode);
		$query = $this->db->get();
		return $query->row();
	}

	public function check_Existing_Item_asper_POrder_inDB($item, $autogen)
	{
		$this->db->select('*');
		$this->db->from('purchase_order_details');
		$this->db->where('po_detail_autogen', $autogen);
		$this->db->where('po_detail_item', $item);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function check_Existing_Item_asper_RFQ_inDB($item, $autogen)
	{
		$this->db->select('*');
		$this->db->from('request_purchase_order_details');
		$this->db->where('rfq_detail_autogen', $autogen);
		$this->db->where('rfq_detail_item', $item);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	public function check_Item_Exist_RFQ_inDB($item, $autogen)
	{
		$this->db->select('*');
		$this->db->from('request_purchase_order_details');
		$this->db->where('rfq_detail_autogen', $autogen);
		$this->db->where('rfq_detail_item', $item);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function check_Item_Exist_PO_inDB($item, $autogen)
	{
		$this->db->select('*');
		$this->db->from('purchase_order_details');
		$this->db->where('po_detail_autogen', $autogen);
		$this->db->where('po_detail_item', $item);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function check_Existing_Item_asperAUTOGEN_inDB($item, $autogen)
	{
		$this->db->select('*');
		$this->db->from('item_package_details');
		$this->db->where('ipdetail_autogen', $autogen);
		$this->db->where('ipdetail_item_ms', $item);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	public function addupdate_temp_Porder_Item_inDB($rows, $genid = NULL)
	{

		$this->db->set($rows);
		if ($genid != NULL) {
			$this->db->from('purchase_order_details');
			$this->db->where('po_detail_autogen', $genid);
			$this->db->where('po_detail_porder_ms', $rows['po_detail_porder_ms']);
			$this->db->where('po_detail_item', isset($rows['po_detail_item']) ? $rows['po_detail_item'] : $rows['po_detail_description']);
			$result = $this->db->get();
			if ($result->num_rows() > 0) {
				$result = $this->db->update('purchase_order_details', $rows);
				if ($result) {
					return TRUE;
				}
			} else {
				if ($this->db->insert('purchase_order_details', $rows)) {
					$q_id = $this->db->insert_id();
					return $q_id;
				} else {
					return FALSE;
				}
			}
		} else {
			if ($this->db->insert('purchase_order_details', $rows)) {
				$q_id = $this->db->insert_id();
				return $q_id;
			} else {
				return FALSE;
			}
		}
	}

	public function addupdate_temp_Rfq_Item_inDB($rows, $genid = NULL)
	{
		$this->db->set($rows);
		if ($genid != NULL) {
			$this->db->where('rfq_detail_autogen', $genid);
			$this->db->where('rfq_detail_porder_ms', $rows['po_detail_porder_ms']);
			$this->db->where('rfq_detail_item', $rows['po_detail_item']);
			$result = $this->db->get();
			if ($result->num_rows() > 0) {
				$result = $this->db->update('request_purchase_order_details', $rows);
				if ($result) {
					return TRUE;
				}
			} else {
				if ($this->db->insert('request_purchase_order_details', $rows)) {
					$q_id = $this->db->insert_id();
					return $q_id;
				} else {
					return FALSE;
				}
			}
		} else {
			if ($this->db->insert('request_purchase_order_details', $rows)) {
				$q_id = $this->db->insert_id();
				return $q_id;
			} else {
				return FALSE;
			}
		}
	}

	public function update_Rfq_Item_inDB($rows, $genid = NULL)
	{
		$this->db->set($rows);
		if ($genid != NULL) {

			$this->db->where('rfq_detail_autogen', $genid);
			// $this->db->where('rfq_detail_porder_ms', $rows['rfq_detail_porder_ms']);
			$this->db->where('rfq_detail_item', isset($rows['rfq_detail_item']) && $rows['rfq_detail_item'] != "" ? $rows['rfq_detail_item'] : $rows['rfq_detail_description']);
			if ($this->db->update('request_purchase_order_details', $rows)) {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			if ($this->db->insert('request_purchase_order_details', $rows)) {
				$q_id = $this->db->insert_id();
				return $q_id;
			} else {
				return FALSE;
			}
		}
	}

	function str_contains($haystack, $needle)
	{
		return '' === $needle || false !== strpos($haystack, $needle);
	}


	public function prepareEmailBody($templateKey, $params = [], $subjectParams = [])
	{
		$this->db->select('*');
		$this->db->from('template_master');
		$this->db->where('email_key', $templateKey);
		$template = $this->db->get()->row();
		$subject = $template->email_subject;
		$content = $template->email_body;

		if (count($subjectParams) > 0) {

			foreach ($subjectParams as $key => $value) {

				$subject = $this->str_contains($subject, $key) ? str_replace($key, $value, $subject) : $subject;
			}
		}

		if (count($params) > 0) {

			foreach ($params as $key => $value) {

				$content = $this->str_contains($content, $key) ? str_replace($key, $value, $content) : $content;
			}
		}

		return [
			'subject' => $subject,
			'content' => $content,
		];
	}

	public function update_PO_Item_inDB($rows, $genid = NULL)
	{
		if ($genid != NULL) {
			$this->db->where('po_detail_autogen', $genid);
			if ($this->db->update('purchase_order_details', $rows)) {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			$this->db->set($rows);
			if ($this->db->insert('purchase_order_details', $rows)) {
				$q_id = $this->db->insert_id();
				return $q_id;
			} else {
				return FALSE;
			}
		}
	}

	public function addupdate_tempItem_inDB($rows, $genid = NULL)
	{
		$this->db->set($rows);
		if ($genid != NULL) {
			$this->db->where('ipdetail_autogen', $genid);
			if ($this->db->update('item_package_details', $rows)) {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			if ($this->db->insert('item_package_details', $rows)) {
				$q_id = $this->db->insert_id();
				return $q_id;
			} else {
				return FALSE;
			}
		}
	}

	public function getDetails_Porder_Item_from_DB($detailid)
	{
		$this->db->select('purchase_order_details.*, item_master.item_name, cost_code_master.cc_no, unit_of_measure_tab.uom_name');
		$this->db->from('purchase_order_details');
		$this->db->join('item_master', 'item_master.item_code = purchase_order_details.po_detail_item');
		//$this->db->join('item_category_tab','item_category_tab.icat_id = item_master.item_cat_ms');
		$this->db->join('cost_code_master', 'cost_code_master.cc_id = item_master.item_ccode_ms');
		$this->db->join('unit_of_measure_tab', 'unit_of_measure_tab.uom_id = item_master.item_unit_ms');
		$this->db->where('purchase_order_details.po_detail_id', $detailid);
		$query = $this->db->get();
		return $query->row();
	}

	public function getTodayPurchaseOrder()
	{
		$this->db->select('purchase_order_master.*, project_master.proj_name, supplier_master.sup_name');
		$this->db->from('purchase_order_master');
		$this->db->join('project_master','purchase_order_master.porder_project_ms = project_master.proj_id');
		$this->db->join('supplier_master','purchase_order_master.porder_supplier_ms = supplier_master.sup_id');
		$this->db->where('purchase_order_master.porder_createdate > DATE(now())');
		$query = $this->db->get();
		return $query->result();
	}

	public function getDetails_Item_from_DB($detailid)
	{
		$this->db->select('item_package_details.*, item_master.item_name, cost_code_master.cc_no, item_category_tab.icat_name');
		$this->db->from('item_package_details');
		$this->db->join('item_master', 'item_master.item_code = item_package_details.ipdetail_item_ms');
		$this->db->join('item_category_tab', 'item_category_tab.icat_id = item_master.item_cat_ms');
		$this->db->join('cost_code_master', 'cost_code_master.cc_id = item_master.item_ccode_ms');
		$this->db->where('item_package_details.ipdetail_id', $detailid);
		$query = $this->db->get();
		return $query->row();
	}

	public function getDetails_Detail_ItemList_from_DB($detailid)
	{
		$this->db->select('item_package_details.*, item_master.item_name, cost_code_master.cc_no, item_category_tab.icat_name');
		$this->db->from('item_package_details');
		$this->db->join('item_master', 'item_master.item_code = item_package_details.ipdetail_item_ms');
		$this->db->join('item_category_tab', 'item_category_tab.icat_id = item_master.item_cat_ms');
		$this->db->join('cost_code_master', 'cost_code_master.cc_id = item_master.item_ccode_ms');
		$this->db->where(['item_package_details.ipdetail_ipack_ms'=>$detailid,'item_package_details.ipdetail_status'=>1]);
		$query = $this->db->get();
		return $query->result();
	}

	public function getDetails_ItemList_POrder_from_DB($detailid)
	{
		$this->db->select('purchase_order_details.*,supplier_catalog_tab.supcat_sku_no, item_master.item_name, item_master.item_code, item_master.item_description,cost_code_master.cc_no, taxgroup_master.name as tax_group_name, item_category_tab.icat_name, unit_of_measure_tab.uom_name');
		$this->db->from('purchase_order_details');
		$this->db->join('item_master', 'item_master.item_code = purchase_order_details.po_detail_item', 'left');
		$this->db->join('unit_of_measure_tab', 'unit_of_measure_tab.uom_id = purchase_order_details.porder_detail_uom', 'left');
		$this->db->join('item_category_tab', 'item_category_tab.icat_id = item_master.item_cat_ms', 'left');
//		$this->db->join('cost_code_master', 'cost_code_master.cc_id = item_master.item_ccode_ms', 'left');
		$this->db->join('cost_code_master', 'cost_code_master.cc_id = purchase_order_details.po_detail_cc', 'left');
		$this->db->join('supplier_catalog_tab', 'supplier_catalog_tab.supcat_item_code = item_master.item_code', 'left');
		$this->db->join('taxgroup_master', 'purchase_order_details.po_detail_tax_group = taxgroup_master.id', 'left');
		$this->db->where('purchase_order_details.po_detail_porder_ms', $detailid);
		$query = $this->db->get();
		return $query->result();
	}

	public function getPurchaseOrderForReceiveOrder()
	{
		$this->db->select('purchase_order_master.*');
		$this->db->from('purchase_order_master');
		$this->db->where('purchase_order_master.porder_status', 1);
		$this->db->where('purchase_order_master.porder_delivery_status !=', 1);
		$query = $this->db->get();
		return $query->result();
	}


	public function getDetails_ItemList_RFQ_from_DB($detailid)
	{
		$this->db->select('request_purchase_order_details.*,supplier_catalog_tab.supcat_sku_no, item_master.item_name, item_master.item_description, item_master.item_code, cost_code_master.cc_no, taxgroup_master.name as tax_group_name, item_category_tab.icat_name, unit_of_measure_tab.uom_name');
		$this->db->from('request_purchase_order_details');
		$this->db->join('item_master', 'item_master.item_code = request_purchase_order_details.rfq_detail_item', 'left');
		$this->db->join('unit_of_measure_tab', 'unit_of_measure_tab.uom_id = item_master.item_unit_ms', 'left');
		$this->db->join('item_category_tab', 'item_category_tab.icat_id = item_master.item_cat_ms', 'left');
		$this->db->join('cost_code_master', 'cost_code_master.cc_id = item_master.item_ccode_ms', 'left');
		$this->db->join('supplier_catalog_tab', 'supplier_catalog_tab.supcat_item_code = item_master.item_code', 'left');
		$this->db->join('taxgroup_master', 'request_purchase_order_details.rfq_detail_tax_group = taxgroup_master.id', 'left');
		$this->db->where('request_purchase_order_details.rfq_detail_porder_ms', $detailid);
		$query = $this->db->get();
		return $query->result();
	}

	public function getDetails_ItemList_for_Receive_Order_from_DB($detailid, $item = NULL)
	{
		$this->db->select('receive_order_details.ro_detail_item, receive_order_master.rorder_id, receive_order_details.ro_detail_remaining', FALSE);
		$this->db->from('receive_order_details');
		//$this->db->join('item_master','item_master.item_code = receive_order_details.ro_detail_item');
		$this->db->join('receive_order_master', 'receive_order_master.rorder_id = receive_order_details.ro_detail_rorder_ms');
		$this->db->join('purchase_order_master', 'purchase_order_master.porder_id = receive_order_master.rorder_porder_ms');
		//$this->db->join('unit_of_measure_tab','unit_of_measure_tab.uom_id = item_master.item_unit_ms');
		//$this->db->join('item_category_tab','item_category_tab.icat_id = item_master.item_cat_ms');
		//$this->db->join('cost_code_master','cost_code_master.cc_id = item_master.item_ccode_ms');
		$this->db->where('purchase_order_master.porder_id', $detailid);
		$this->db->where('receive_order_details.ro_detail_item', $item);
		$this->db->order_by('receive_order_master.rorder_id', 'desc');
		$this->db->limit('1');
		$query = $this->db->get();
		return $query->row();
	}

	public function getDetailsOfReceiveOrder($detailid)
	{
		$this->db->select('receive_order_details.*');
		$this->db->from('receive_order_details');
		$this->db->where('receive_order_details.ro_detail_rorder_ms', $detailid);
		$query = $this->db->get();
		return $query->result();
	}

	public function getSingleRecordOfReceiveOrder($id)
	{
		$this->db->select('receive_order_details.*');
		$this->db->from('receive_order_details');
		$this->db->where('receive_order_details.ro_detail_id', $id);
		$query = $this->db->get();
		return $query->row();
	}

	public function getMINOfReceiveOrderItem($id, $item)
	{
		$this->db->select('MIN(receive_order_details.ro_detail_remaining) as remaining');
		$this->db->from('receive_order_details');
		$this->db->join('receive_order_master', 'receive_order_master.rorder_id = receive_order_details.ro_detail_rorder_ms');
		$this->db->where('receive_order_master.rorder_porder_ms', $id);
		$this->db->where('receive_order_details.ro_detail_item', $item);
		$query = $this->db->get();
		return $query->row();
	}

	public function getSUMOfReceiveOrderItem($id, $item, $itemid)
	{
		$this->db->select('SUM(receive_order_details.ro_detail_quantity) as previous_purchase');
		$this->db->from('receive_order_details');
		$this->db->join('receive_order_master', 'receive_order_master.rorder_id = receive_order_details.ro_detail_rorder_ms');
		$this->db->where('receive_order_master.rorder_porder_ms', $id);
		$this->db->where('receive_order_details.ro_detail_item', $item);
		$this->db->where('receive_order_details.ro_detail_id <', $itemid);
		$query = $this->db->get();
		return $query->row();
	}
	
	public function getSUMOfReceiveOrderItemAdd($id, $item, $itemid)
	{
		$this->db->select('SUM(receive_order_details.ro_detail_quantity) as previous_purchase');
		$this->db->from('receive_order_details');
		$this->db->join('receive_order_master', 'receive_order_master.rorder_id = receive_order_details.ro_detail_rorder_ms');
		$this->db->where('receive_order_master.rorder_porder_ms', $id);
		$this->db->where('receive_order_details.ro_detail_item', $item);
		$this->db->where('receive_order_details.ro_detail_id <=', $itemid);
		$query = $this->db->get();
		return $query->row();
	}

	public function getDetailItemsofPOForReceiveOrder($id)
	{
		$this->db->select('purchase_order_details.*');
		$this->db->from('purchase_order_details');
		$this->db->where('purchase_order_details.po_detail_porder_ms', $id);
		$this->db->order_by('purchase_order_details.po_detail_id', 'asc');
		$query = $this->db->get();
		return $query->result();
	}

	public function getSingleItemDetailPOForReceiveOrder($id)
	{
		$this->db->select('purchase_order_details.*');
		$this->db->from('purchase_order_details');
		$this->db->where('purchase_order_details.po_detail_id', $id);
		$query = $this->db->get();
		return $query->row();
	}


	public function getLatestDetails_ItemList_for_Receive_Order_from_DB($ro_detail_id, $item)
	{
		$this->db->select('receive_order_details.ro_detail_item,  receive_order_details.ro_detail_remaining', FALSE);
		$this->db->from('receive_order_details');
		$this->db->where('receive_order_details.ro_detail_item', $item);
		$this->db->where('receive_order_details.ro_detail_rorder_ms', $ro_detail_id);
		$this->db->order_by('receive_order_details.ro_detail_id', 'desc');
		$this->db->limit('1');
		$query = $this->db->get();
		return $query->row();
	}



	public function check_recv_order_set_nos_exist($nameset, $sid = NULL)
	{
		$this->db->select('*');
		$this->db->from('receive_order_master');
		$this->db->where('rorder_slip_no', $nameset);
		if ($sid != NULL)
			$this->db->where('rorder_id != ', $sid);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	public function check_porder_set_nos_exist($nameset, $sid = NULL)
	{
		$this->db->select('*');
		$this->db->from('purchase_order_master');
		$this->db->where('porder_no', $nameset);
		if ($sid != NULL)
			$this->db->where('porder_id != ', $sid);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	public function check_rfq_set_nos_exist($nameset, $sid = NULL)
	{
		$this->db->select('*');
		$this->db->from('request_purchase_order');
		$this->db->where('rporder_no ', $nameset);
		if ($sid != NULL) {
			$this->db->where('rporder_id = ', $sid);
		}
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}


	public function makePOPdf($template_view,$data, $name = null) {
		$name = ($name != null) ? $name : date('ymdhis');
		$this->load->library('pdf');
		$this->pdf->load_view('admin/pdf_view/'.$template_view,$data);
		$this->pdf->set_paper("A4", "portrait");
		$this->pdf->render();
		ob_end_clean();
		file_put_contents('./upload_file/'.$name.'.pdf', $this->pdf->output());
		return './upload_file/'.$name.'.pdf';
	}

	// public function makePOPdf1($template_view,$data, $name = []) {
	// 	$name = ($name != null) ? $name : date('ymdhis');
	// 	$this->load->library('pdf');
	// 	$this->pdf->load_view('admin/pdf_view/'.$template_view,$data);
	// 	$this->pdf->set_paper("A4", "portrait");
	// 	$this->pdf->render();
	// 	ob_end_clean();
	// 	file_put_contents('./upload_file/'.$name.'.pdf', $this->pdf->output());
	// 	return './upload_file/'.$name.'.pdf';
	// }

	public function showPOPDF($template_view,$data, $title = "welcome") {
		$name =  date('ymdhis');
		$this->load->library('pdf');
		$this->pdf->load_view('admin/pdf_view/'.$template_view,$data);
		$this->pdf->set_paper("A4", "portrait");
		$this->pdf->render();
		ob_end_clean();
		return 	$this->pdf->stream($title.".pdf",["Attachment"=>0]);
	}

	public function check_package_set_nos_exist($nameset, $sid = NULL)
	{
		$this->db->select('*');
		$this->db->from('item_package_master');
		$this->db->where('ipack_name', $nameset);
		if ($sid != NULL)
			$this->db->where('ipack_id != ', $sid);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	public function check_project_nos_exist($nameset, $sid = NULL)
	{
		$this->db->select('*');
		$this->db->from('project_master');
		$this->db->where('proj_number', $nameset);
		if ($sid != NULL)
			$this->db->where('proj_id != ', $sid);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	public function check_scatalog_skusets_exist($nameset, $sid = NULL)
	{
		$this->db->select('*');
		$this->db->from('supplier_catalog_tab');
		$this->db->where('supcat_sku_no', $nameset);
		if ($sid != NULL)
			$this->db->where('supcat_id != ', $sid);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	public function check_itemsets_exist($nameset, $sid = NULL)
	{
		$this->db->select('*');
		$this->db->from('item_master');
		$this->db->where('item_code', $nameset);
		if ($sid != NULL)
			$this->db->where('item_id != ', $sid);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	public function check_suppliers_exist($nameset, $sid = NULL)
	{
		$this->db->select('*');
		$this->db->from('supplier_master');
		$this->db->where('sup_name', $nameset);
		if ($sid != NULL)
			$this->db->where('sup_id != ', $sid);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	public function nextPONumber($project_id) {

		$getresult = $this->db->get_where('project_master', array('proj_id' => $project_id, 'proj_status' => 1))->row();

		$this->db->select('porder_id, porder_no');
		$this->db->from('purchase_order_master');
		$this->db->where('porder_project_ms',$project_id);
		$this->db->order_by('porder_id', 'DESC')->limit(1);
		$po_record = $this->db->get()->row();

		$proj_id = str_replace('-', '', $getresult->proj_number);
		$number = isset($po_record) ? explode('-', $po_record->porder_no) : [];
		if (isset($number[1])) {
			$number_ = $number[1];
		} else {
			$number_ = 0;
		}
		$porder_id = str_pad(isset($po_record->porder_id) ? $number_ + 1 : 1, 4, '0', STR_PAD_LEFT);
		$po_number = $porder_id;
		$po_number_prefix = $proj_id;

		if (count((array)$getresult) > 0) {
			return array('po_number' => $po_number, 'po_number_prefix' => $po_number_prefix);
		} else {
			return false;
		}
	}

	public function quote_to_purchase_order($rporder_list, $item_detailsets)
	{
		$newPONumber = $this->nextPONumber($rporder_list->rporder_project_ms);

		$row = array(
			'porder_project_ms' => $rporder_list->rporder_project_ms,
			'porder_no' => $newPONumber['po_number_prefix'].'-'.$newPONumber['po_number'],
			'porder_supplier_ms' => $rporder_list->rporder_supplier_ms,
			'porder_address' => $rporder_list->rporder_address,
			'porder_delivery_date' => trim($rporder_list->rporder_delivery_date),
			'porder_delivery_note' => trim($rporder_list->rporder_delivery_note),
			'porder_total_item' => $rporder_list->rporder_total_item,
			'porder_total_amount' => $rporder_list->rporder_total_amount,
			'porder_type' => $rporder_list->rporder_type == "Material RPO" ? "Material PO" : "",
			'porder_description' => $rporder_list->rporder_description,
			'porder_createdate' => date('Y-m-d H:i:s'),
			'porder_createby' => $this->session->userdata['uid'],
			'is_rfq' => 1
		);
		$rowids = $this->common_Insertion_in_DB_with_ID($row, 'purchase_order_master');

		for ($i = 0; $i < count(($item_detailsets)); $i++) {

			$row_arr = array(
				'po_detail_autogen' => $item_detailsets[$i]->rfq_detail_autogen,
				'po_detail_porder_ms' => $rowids,
				'po_detail_item' => $item_detailsets[$i]->rfq_detail_item != "" ? $item_detailsets[$i]->rfq_detail_item : null,
				'po_detail_sku' => isset($item_detailsets[$i]->rfq_detail_sku) ? $item_detailsets[$i]->rfq_detail_sku : null,
				'po_detail_taxcode' => isset($item_detailsets[$i]->rfq_detail_taxcode) ? $item_detailsets[$i]->rfq_detail_taxcode : null,
				'po_detail_quantity' => isset($item_detailsets[$i]->rfq_detail_quantity) ? $item_detailsets[$i]->rfq_detail_quantity : null,
				'po_detail_unitprice' => isset($item_detailsets[$i]->rfq_detail_unitprice) ? $item_detailsets[$i]->rfq_detail_unitprice : null,
				'po_detail_subtotal' => isset($item_detailsets[$i]->rfq_detail_subtotal) ? $item_detailsets[$i]->rfq_detail_subtotal : null,
				'po_detail_taxamount' => isset($item_detailsets[$i]->rfq_detail_taxamount) ? $item_detailsets[$i]->rfq_detail_taxamount : null,
				'po_detail_description' => isset($item_detailsets[$i]->rfq_detail_description) ? $item_detailsets[$i]->rfq_detail_description : null,
				'po_detail_total' => isset($item_detailsets[$i]->rfq_detail_total) ? $item_detailsets[$i]->rfq_detail_total : null,
				'porder_detail_uom' => isset($item_detailsets[$i]->rfq_detail_uom) ? $item_detailsets[$i]->rfq_detail_uom  : null,
				'po_detail_cc' => isset($item_detailsets[$i]->rfq_detail_cc) ? $item_detailsets[$i]->rfq_detail_cc : null,
				'po_detail_tax_group' => isset($item_detailsets[$i]->rfq_detail_tax_group) ? $item_detailsets[$i]->rfq_detail_tax_group : null,
				'po_detail_createdate' => date('Y-m-d H:i:s')
			);

			$resultset = $this->addupdate_temp_Porder_Item_inDB($row_arr);

//			$this->db->delete('request_purchase_order_details', array('rfq_detail_id' => $item_detailsets[$i]->rfq_detail_id));
		}

//		$this->db->delete('request_purchase_order', array('rporder_id' => $rporder_list->rporder_id));

		return true;
	}

	public function check_taxgroup_exist($nameset, $sid = NULL)
	{
		$this->db->select('*');
		$this->db->from('taxgroup_master');
		$this->db->where('name', $nameset);
		if ($sid != NULL)
			$this->db->where('id != ', $sid);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	public function check_template_exist($nameset, $sid = NULL)
	{
		$this->db->select('*');
		$this->db->from('template_master');
		$this->db->where('email_key', $nameset);
		if ($sid != NULL)
			$this->db->where('id != ', $sid);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	public function check_itemcategory_exist($nameset, $sid = NULL)
	{
		$this->db->select('*');
		$this->db->from('item_category_tab');
		$this->db->where('icat_name', $nameset);
		if ($sid != NULL)
			$this->db->where('icat_id != ', $sid);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	public function check_ccode_exist($nameset, $sid = NULL)
	{
		$this->db->select('*');
		$this->db->from('cost_code_master');
		$this->db->where('cc_no', $nameset);
		if ($sid != NULL)
			$this->db->where('cc_id != ', $sid);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	public function check_uom_exist($nameset, $sid = NULL)
	{
		$this->db->select('*');
		$this->db->from('unit_of_measure_tab');
		$this->db->where('uom_name', $nameset);
		if ($sid != NULL)
			$this->db->where('uom_id != ', $sid);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return FALSE;
		} else {
			return TRUE;
		}
	}


	public function FindSubAdminName()
	{
		$this->db->select('u_id, username');
		$this->db->from('user_views');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_ALL_FrontEnduser_DMS($fuid = NULL)
	{
		$this->db->select('*');
		$this->db->from('frontend_users');
		if ($fuid != NULL) {
			$this->db->where('fuser_id', $fuid);
			$query = $this->db->get();
			return $query->row();
		} else {
			$query = $this->db->get();
			return $query->result();
		}
	}

	public function getAll_work_fromDB()
	{
		$this->db->select('main_work_tab.*, work_sector_tab.ws_name, fund_source_tab.fs_name');
		$this->db->from('main_work_tab');
		$this->db->join('fund_source_tab', 'fund_source_tab.fs_id = main_work_tab.mw_fund_source');
		$this->db->join('work_sector_tab', 'work_sector_tab.ws_id = main_work_tab.mw_sector');
		$this->db->order_by('main_work_tab.mw_id', 'DESC');
		$query = $this->db->get();
		return $query->result();
	}

	public function addUpdateform_ofWork_inDB($row1, $row2 = NULL, $row3 = NULL, $workid = NULL)
	{
		$this->db->trans_start();

		if ($row2 != NULL) {
			$this->db->set($row2);
			$this->db->insert('fund_source_tab', $row2);
			$fund_id = $this->db->insert_id();
			$row1['mw_fund_source'] = $fund_id;
		}
		if ($row3 != NULL) {
			$this->db->set($row3);
			$this->db->insert('work_sector_tab', $row3);
			$sector_id = $this->db->insert_id();
			$row1['mw_sector'] = $sector_id;
		}
		$this->db->set($row1);
		if ($workid != NULL) {
			$this->db->where('mw_id', $workid);
			$this->db->update('main_work_tab', $row1);
		} else {
			$this->db->insert('main_work_tab', $row1);
		}

		$this->db->trans_complete();
		if ($this->db->trans_status() === TRUE) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function addUpdateform_of_WorkAllocation_inDB($rows, $wid = NULL)
	{
		$this->db->set($rows);
		if ($wid != NULL) {
			$this->db->where('work_id', $wid);
			if ($this->db->update("work_allocate_details", $rows)) {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			if ($this->db->insert("work_allocate_details", $rows)) {
				return TRUE;
			} else {
				return FALSE;
			}
		}
	}

	public function get_all_NewWork_for_Allocate($f_year)
	{
		$this->db->select('main_work_tab.mw_name, main_work_tab.mw_unique_id, work_allocate_details.work_id');
		$this->db->from('main_work_tab');
		$this->db->join('work_allocate_details', 'work_allocate_details.work_master_id = main_work_tab.mw_unique_id', 'LEFT');
		$this->db->where('main_work_tab.mw_year', $f_year);
		$this->db->where('main_work_tab.mw_tender_float', 'Yes');
		$this->db->where('work_allocate_details.work_id IS NULL');
		$this->db->order_by('main_work_tab.mw_id', 'DESC');
		$query = $this->db->get();
		return $query->result();
	}

	public function getAll_workAllocation_fromDB($workallocid = NULL)
	{
		$this->db->select('work_allocate_details.*,main_work_tab.mw_name, main_work_tab.mw_year, uv1.username as ae_name, uv2.username as sae_name');
		$this->db->from('work_allocate_details');
		$this->db->join('main_work_tab', 'main_work_tab.mw_unique_id = work_allocate_details.work_master_id');
		$this->db->join('user_views uv1', 'uv1.u_id = work_allocate_details.work_se_id');
		$this->db->join('user_views uv2', 'uv2.u_id = work_allocate_details.work_ase_id');
		if ($workallocid != NULL) {
			$this->db->where('work_allocate_details.work_id', $workallocid);
			$query = $this->db->get();
			return $query->row();
		} else {
			$this->db->order_by('work_allocate_details.work_id', 'DESC');
			$query = $this->db->get();
			return $query->result();
		}
	}

	public function getAll_workAllocation_fromDB_byProgress($workuid = NULL)
	{
		$this->db->select('work_allocate_details.*,main_work_tab.mw_name, main_work_tab.mw_year, main_work_tab.mw_unique_id, main_work_tab.mw_progress_stat, main_work_tab.mw_finalbill_put');
		$this->db->from('work_allocate_details');
		$this->db->join('main_work_tab', 'main_work_tab.mw_unique_id = work_allocate_details.work_master_id');
		//$this->db->join('user_views uv1','uv1.u_id = work_allocate_details.work_se_id');
		//$this->db->join('user_views uv2','uv2.u_id = work_allocate_details.work_ase_id');
		if ($workuid != NULL) {
			$this->db->where('main_work_tab.mw_unique_id', $workuid);
			$query = $this->db->get();
			return $query->row();
		} else {
			$this->db->order_by('work_allocate_details.work_id', 'DESC');
			$query = $this->db->get();
			return $query->result();
		}
	}

	public function addUpdate_WorkProgress_inDB($row1, $row2, $pic_row = NULL, $wpid = NULL)
	{
		$this->db->trans_start();

		$this->db->set($row1);
		$this->db->insert('work_progress_tab', $row1);
		$w_progress_id = $this->db->insert_id();

		$this->db->set($row2);
		$this->db->where('mw_unique_id', $wpid);
		$this->db->update('main_work_tab', $row2);

		if ($pic_row != NULL) {
			foreach ($pic_row as $pics) {
				$pic_arr = array(
					'wpp_master_progrid' => $w_progress_id,
					'wpp_pic_source' => $pics,
					'wpp_createdate' => date('Y-m-d H:i:s')
				);
				$this->db->set($pic_arr);
				$this->db->insert('work_prog_pictures', $pic_arr);
			}
		}

		$this->db->trans_complete();
		if ($this->db->trans_status() === TRUE) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function addUpdate_WorkProgress_Bill_inDB($row1, $row2, $wpid)
	{
		$this->db->trans_start();

		$this->db->set($row1);
		$this->db->insert('work_bill_tab', $row1);

		$this->db->set($row2);
		$this->db->where('mw_unique_id', $wpid);
		$this->db->update('main_work_tab', $row2);

		$this->db->trans_complete();
		if ($this->db->trans_status() === TRUE) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function getAll_workProgress_fromDB_byVisit($workuid)
	{
		$this->db->select('work_progress_tab.*,main_work_tab.mw_name, main_work_tab.mw_year, main_work_tab.mw_progress_stat, main_work_tab.mw_finalbill_put');
		$this->db->from('work_progress_tab');
		$this->db->join('main_work_tab', 'main_work_tab.mw_unique_id = work_progress_tab.wp_masterid');
		$this->db->join('user_views', 'user_views.u_id = work_progress_tab.wp_createby');
		$this->db->where('main_work_tab.mw_unique_id', $workuid);
		$this->db->order_by('work_progress_tab.wp_id', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function getAll_workProgress_Bill_fromDB($workuid)
	{
		$this->db->select('work_bill_tab.*,main_work_tab.mw_name, main_work_tab.mw_year, main_work_tab.mw_progress_stat, main_work_tab.mw_finalbill_put');
		$this->db->from('work_bill_tab');
		$this->db->join('main_work_tab', 'main_work_tab.mw_unique_id = work_bill_tab.wb_master_id');
		$this->db->join('user_views', 'user_views.u_id = work_bill_tab.wb_createby');
		$this->db->where('main_work_tab.mw_unique_id', $workuid);
		$this->db->order_by('work_bill_tab.wb_id', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_All_POrder_Items($autogen)
	{
		$this->db->select('*');
		$this->db->from('purchase_order_details');
		$this->db->where('po_detail_autogen', $autogen);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function get_All_POrder_Items_TotalAmount($autogen)
	{
		$this->db->select('SUM(po_detail_total) as pdtotal');
		$this->db->from('purchase_order_details');
		$this->db->where('po_detail_autogen', $autogen);
		$query = $this->db->get();
		return $query->row();
	}

	public function get_All_Project()
	{
		$this->db->select('*');
		$this->db->from('project_master');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_Project_By_ID($id)
	{
		$this->db->select('*');
		$this->db->from('project_master');
		$this->db->where('proj_id', $id);
		$query = $this->db->get();
		return $query->row();
	}

	public function get_All_PurchaseOrder()
	{
		$this->db->select('*');
		$this->db->from('purchase_order_master');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_All_Suppliers()
	{
		$this->db->select('*');
		$this->db->from('supplier_master');
		$query = $this->db->get();
		return $query->result();
	}

	public function getSupplierByName($name)
	{
		$this->db->select('*');
		$this->db->from('supplier_master');
		$this->db->where('sup_name',$name);
		$query = $this->db->get();
		return $query->row();
	}

	public function getItemByCode($code)
	{
		$this->db->select('*');
		$this->db->from('item_master');
		$this->db->where('item_code',$code);
		$query = $this->db->get();
		return $query->row();
	}



	public function get_CostCodeByNo($costCode)
	{
		$this->db->select('*');
		$this->db->from('cost_code_master');
		$this->db->where('cc_no', $costCode);
		$query = $this->db->get();
		return $query->row();
	}

	public function get_CategoryByName($category)
	{
		$this->db->select('*');
		$this->db->from('item_category_tab');
		$this->db->where('icat_name', $category);
		$query = $this->db->get();
		return $query->row();
	}

	public function get_UnitByName($unit)
	{
		$this->db->select('*');
		$this->db->from('unit_of_measure_tab');
		$this->db->where('uom_name', $unit);
		$query = $this->db->get();
		return $query->row();
	}

	public function get_All_Receive_order_list_fromDB($recordid = NULL, $filter = [])
	{
		$this->db->select('receive_order_master.*, purchase_order_master.porder_no, project_master.billing_name, project_master.billing_address,purchase_order_master.porder_type, project_master.proj_number, project_master.proj_name, purchase_order_master.porder_createdate, purchase_order_master.porder_description, purchase_order_master.porder_delivery_note , purchase_order_master.porder_id, purchase_order_master.porder_supplier_ms, purchase_order_master.porder_address, supplier_master.sup_name, project_master.proj_name');
		$this->db->from('receive_order_master');
		$this->db->join('purchase_order_master', 'purchase_order_master.porder_id = receive_order_master.rorder_porder_ms');
		$this->db->join('project_master', 'project_master.proj_id = purchase_order_master.porder_project_ms');
		$this->db->join('supplier_master', 'supplier_master.sup_id = purchase_order_master.porder_supplier_ms');
		$this->db->where($filter);
		if ($recordid != NULL) {
			$this->db->where('receive_order_master.rorder_id', $recordid);
			$query = $this->db->get();
			return $query->row();
		} else {
			$this->db->order_by('receive_order_master.rorder_id', 'DESC');
			$query = $this->db->get();
			return $query->result();
		}
	}

	public function getDetails_ItemList_forRecv_Order_from_DB($id)
	{
		$this->db->select('receive_order_details.*, item_master.item_name, item_master.item_code, cost_code_master.cc_no, item_category_tab.icat_name, unit_of_measure_tab.uom_name');
		$this->db->from('receive_order_details');
		$this->db->join('item_master', 'item_master.item_code = receive_order_details.ro_detail_item', 'left');
		$this->db->join('unit_of_measure_tab', 'unit_of_measure_tab.uom_id = item_master.item_unit_ms', 'left');
		$this->db->join('item_category_tab', 'item_category_tab.icat_id = item_master.item_cat_ms', 'left');
		$this->db->join('cost_code_master', 'cost_code_master.cc_id = item_master.item_ccode_ms', 'left');
		$this->db->join('receive_order_master','receive_order_details.ro_detail_rorder_ms = receive_order_master.rorder_id');
		$this->db->where('receive_order_details.ro_detail_rorder_ms', $id);
		$query = $this->db->get();
		return $query->result();
	}

	public function getReceiveOrder($id)
	{
		$this->db->select('*');
		$this->db->from('receive_order_master');
		$this->db->where('rorder_id', $id);
		$query = $this->db->get();
		return $query->row();
	}

}
