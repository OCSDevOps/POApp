<?php

class Procore_Model extends MY_Model
{

    function __construct() {
        parent::__construct();
    }


	// get procore auth

	function getProcoreAuth(){
		$this->db->select('*');
		$this->db->from('procore_auth');
		$query = $this->db->get();
		return $query->row();
	}
	
	// get line item cost code

	function getLineItemCostCode($cc,$project_id){
		$this->db->select('*');
		$this->db->from('procore_project_cost_codes');
		$this->db->where(array('project_id'=>$project_id,'procore_cc'=>$cc));
		$query = $this->db->get();
		return $query->row();
	}
	
	// get line item cost code

	function getLineItemDescription($item){
		$this->db->select('*');
		$this->db->from('item_master');
		$this->db->where(array('item_code'=>$item));
		$query = $this->db->get();
		return $query->row();
	}


	// get project nos

	function getProjectNos(){
		$this->db->select('proj_number,procore_project_id');
		$this->db->from('project_master');
		$query = $this->db->get();
		return $query->result();
	}
	
	// get cost code nos

	function getCostCodeNos(){
		$this->db->select('cc_no,procore_cc_id');
		$this->db->from('cost_code_master');
		$query = $this->db->get();
		return $query->result();
	}
	
	// get project cost codes

	function getProjectCostCodes($project_id){
		$this->db->select('procore_cc');
		$this->db->from('procore_project_cost_codes');
		$this->db->where('project_id',$project_id);
		$query = $this->db->get();
		return $query->result();
	}
	
	// get uoms

	function getUoms(){
		$this->db->select('uom_name');
		$this->db->from('unit_of_measure_tab');
		$query = $this->db->get();
		return $query->result();
	}
	
	// get Blis

	function getBlis(){
		$this->db->select('procore_budget_id');
		$this->db->from('budget_line_item_master');
		$query = $this->db->get();
		return $query->result();
	}
	
	// get budget summary

	function getBudgetSummary(){
		$this->db->select('project_id');
		$this->db->from('budget_summary_master');
		$query = $this->db->get();
		return $query->result();
	}
	
	// get SupplierNames

	function getSupplierNames(){
		$this->db->select('sup_name');
		$this->db->from('supplier_master');
		$query = $this->db->get();
		return $query->result();
	}
	
	// get TaxGroupNames

	function getTaxGroupNames(){
		$this->db->select('name');
		$this->db->from('taxgroup_master');
		$query = $this->db->get();
		return $query->result();
	}
	
	// get Ready to Export PO

	function getReadyToExportCommitments(){
		$this->db->select('porder_id,porder_address,porder_description,porder_no,(select proj_name from project_master where proj_id=purchase_order_master.porder_project_ms) as project,(select procore_project_id from project_master where proj_id=purchase_order_master.porder_project_ms) as project_id,(select sup_name from supplier_master where sup_id=purchase_order_master.porder_supplier_ms) as supplier,(select procore_supplier_id from supplier_master where sup_id=purchase_order_master.porder_supplier_ms) as supplier_id,porder_total_item,porder_delivery_date');
		$this->db->from('purchase_order_master');
		$this->db->where('integration_status','rte');
		$query = $this->db->get();
		return $query->result();
	}
	
	// get Synced PO

	function getSyncedCommitments(){
		$this->db->select('porder_id,porder_address,porder_description,porder_no,(select proj_name from project_master where proj_id=purchase_order_master.porder_project_ms) as project,(select sup_name from supplier_master where sup_id=purchase_order_master.porder_supplier_ms) as supplier,porder_total_item');
		$this->db->from('purchase_order_master');
		$this->db->where('integration_status','synced');
		$query = $this->db->get();
		return $query->result();
	}
	
	// get Synced PO

	function getFailedCommitments(){
		$this->db->select('*,(select proj_name from project_master where procore_project_id=failed_po_details.fpo_project_id) as fpo_project,(select sup_name from supplier_master where procore_supplier_id=failed_po_details.fpo_supplier_id) as fpo_supplier');
		$this->db->from('failed_po_details');
		$this->db->where('status',1);
		$query = $this->db->get();
		return $query->result();
	}
	
	// get PO Line Items

	function getCommitmentLineItems($id){
		$this->db->select('*,(select procore_cc_id from cost_code_master where cc_id = purchase_order_details.po_detail_cc) as cost_code_id,(select cc_no from cost_code_master where cc_id = purchase_order_details.po_detail_cc) as cost_code,(select procore_tax_code_id from taxgroup_master where id = purchase_order_details.po_detail_tax_group) as tax_code_id');
		$this->db->from('purchase_order_details');
		$this->db->where('po_detail_porder_ms',$id);
		$query = $this->db->get();
		return $query->result();
	}
	
	// insert Procore Auth

	function insertProcoreAuth($client_id,$client_secret,$company_id){
		date_default_timezone_set("Asia/Calcutta"); 
		$data=[
			'CLIENT_ID' => $client_id,
			'SECRET_KEY' => $client_secret,
			'COMPANY_ID' => $company_id,
			'UPDATED_DATE' => date('Y-m-d H:i:s')
		];
		if($this->db->insert('procore_auth',$data)){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	// update Procore Auth

	function updateProcoreAuth($client_id,$client_secret,$company_id){
		date_default_timezone_set("Asia/Calcutta"); 
		$data=[
			'CLIENT_ID' => $client_id,
			'SECRET_KEY' => $client_secret,
			'COMPANY_ID' => $company_id,
			'MODIFIED_DATE' => date('Y-m-d H:i:s')
		];
		if($this->db->update('procore_auth',$data)){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	// update PO Integration status

	function updatePoIntegrationStatus($id){
		$this->db->where('porder_id',$id);
		if($this->db->update('purchase_order_master',['integration_status'=>'synced'])){
			$this->db->where('fpo_porder_id',$id);
			if($this->db->update('failed_po_details',['status'=>0])){
				return TRUE;
			}
		}else{
			return FALSE;
		}
	}

	// sync projects data

	public function syncProjects($project_id,$project_number,$name,$address)
	{
		$data=[
			'procore_project_id' => $project_id,
			'proj_number' => $project_number,
			'proj_name' => $name,
			'proj_address' => $address,
			'procore_integration_status' => 'YES',
			'proj_createdate' => date('Y-m-d h:m:s')
		];
		if ($this->db->insert('project_master', $data)) {
			// $q_id = $this->db->insert_id();
			return TRUE;
		} else {
			return FALSE;
		}
	}

	// Re sync projects data

	public function reSyncProjects($project_id,$project_number,$name,$address)
	{
		$data=[
			'proj_number' => $project_number,
			'proj_name' => $name,
			'proj_address' => $address,
			'procore_integration_status' => 'YES',
			'proj_modifydate' => date('Y-m-d h:m:s')
		];
		$this->db->where('procore_project_id',$project_id);
		if ($this->db->update('project_master', $data)) {
			// $q_id = $this->db->insert_id();
			return TRUE;
		} else {
			return FALSE;
		}
	}

	// sync cost code data

	public function syncCostCode($cc_id,$cc_no,$name)
	{
		$data=[
			'procore_cc_id' => $cc_id,
			'cc_no' => $cc_no,
			'cc_description' => $name,
			'procore_integration_status' => 'YES',
			'cc_createdate' => date('Y-m-d h:m:s')
		];

		$data1=[
			'cc_no' => $cc_no,
			'cc_description' => $name,
			'procore_integration_status' => 'YES',
			'cc_modifydate' => date('Y-m-d h:m:s')
		];

		if($this->db->where('procore_cc_id',$cc_id)->get('cost_code_master')->num_rows()>0){
			$this->db->where('procore_cc_id',$cc_id);
			if ($this->db->update('cost_code_master', $data1)) {
				// $q_id = $this->db->insert_id();
				return TRUE;
			} else {
				return FALSE;
			}
		}else{
			if ($this->db->insert('cost_code_master', $data)) {
				// $q_id = $this->db->insert_id();
				return TRUE;
			} else {
				return FALSE;
			}
		}
	}

	// sync project cost code data

	public function syncProjectCostCodes($project_id,$project_name,$cc_id,$cc,$cc_name)
	{
		$data=[
			'project_id' => $project_id,
			'project_name' => $project_name,
			'procore_cc_id' => $cc_id,
			'procore_cc' => $cc,
			'procore_cc_name' => $cc_name,
			'created_date' => date('Y-m-d h:m:s')
		];
		if ($this->db->insert('procore_project_cost_codes', $data)) {
			// $q_id = $this->db->insert_id();
			return TRUE;
		} else {
			return FALSE;
		}
	}

	// Re sync project cost code data

	public function reSyncProjectCostCodes($project_id,$project_name,$cc_id,$cc,$cc_name)
	{
		$data=[
			'project_id' => $project_id,
			'project_name' => $project_name,
			'procore_cc_id' => $cc_id,
			'procore_cc' => $cc,
			'procore_cc_name' => $cc_name,
			'created_date' => date('Y-m-d h:m:s')
		];

		$data1=[
			'project_name' => $project_name,
			'procore_cc' => $cc,
			'procore_cc_name' => $cc_name,
			'modify_date' => date('Y-m-d h:m:s')
		];

		$where=[
			'project_id' => $project_id,
			'procore_cc_id' => $cc_id
		];

		if($this->db->where($where)->get('procore_project_cost_codes')->num_rows()>0){
			$this->db->where($where);
			if ($this->db->update('procore_project_cost_codes', $data1)) {
				// $q_id = $this->db->insert_id();
				return TRUE;
			} else {
				return FALSE;
			}
		}else{
			if ($this->db->insert('procore_project_cost_codes', $data)) {
				// $q_id = $this->db->insert_id();
				return TRUE;
			} else {
				return FALSE;
			}
		}
	}

	// sync uom data

	public function syncUom($name)
	{
		$data=[
			'uom_name' => $name,
			'procore_integration_status' => 'YES',
			'uom_createdate' => date('Y-m-d h:m:s')
		];
		if ($this->db->insert('unit_of_measure_tab', $data)) {
			// $q_id = $this->db->insert_id();
			return TRUE;
		} else {
			return FALSE;
		}
	}

	// sync bli data

	public function syncBli($budgetId,$projectId,$divisionName,$divisionCode,$costCodeName,$costCode,$originalBudget,$revisedBudget,$commitedCost)
	{
		$data=[
			'project_id' => $projectId,
			'procore_budget_id' => $budgetId,
			'division_name' => $divisionName,
			'division_code' => $divisionCode,
			'cost_code_name' => $costCodeName,
			'cost_code' => $costCode,
			'original_budget' => $originalBudget,
			'revised_budget' => $revisedBudget,
			'committed_cost' => $commitedCost,
			'procore_integration_status' => 'YES',
			'bli_createdate' => date('Y-m-d h:m:s')
		];

		$data1=[
			'division_name' => $divisionName,
			'division_code' => $divisionCode,
			'cost_code_name' => $costCodeName,
			'original_budget' => $originalBudget,
			'revised_budget' => $revisedBudget,
			'committed_cost' => $commitedCost,
			'procore_integration_status' => 'YES',
			'bli_modifydate' => date('Y-m-d h:m:s')
		];

		$where=[
			'project_id' => $projectId,
			'procore_budget_id' => $budgetId,
			'cost_code' => $costCode
		];

		if($this->db->where($where)->get('budget_line_item_master')->num_rows()>0){
			$this->db->where($where);
			if($this->db->update('budget_line_item_master', $data1)) {
				// $q_id = $this->db->insert_id();
				return TRUE;
			} else {
				return FALSE;
			}
		}else{
			if ($this->db->insert('budget_line_item_master', $data)) {
				// $q_id = $this->db->insert_id();
				return TRUE;
			} else {
				return FALSE;
			}
		}
	}

	// sync budget summary data

	public function syncBudgetSummary($projectId,$originalBudget,$revisedBudget,$commitedCost)
	{
		$data=[
			'project_id' => $projectId,
			'original_budget' => $originalBudget,
			'revised_budget' => $revisedBudget,
			'commited_cost' => $commitedCost,
			'procore_integration_status' => 'YES',
			'bs_createdate' => date('Y-m-d h:m:s')
		];

		$data1=[
			'original_budget' => $originalBudget,
			'revised_budget' => $revisedBudget,
			'commited_cost' => $commitedCost,
			'procore_integration_status' => 'YES',
			'bs_modifydate' => date('Y-m-d h:m:s')
		];

		if($this->db->where('project_id',$projectId)->get('budget_summary_master')->num_rows()>0){
			$this->db->where('project_id',$projectId)->get('budget_summary_master');
			if ($this->db->update('budget_summary_master', $data1)) {
				// $q_id = $this->db->insert_id();
				return TRUE;
			} else {
				return FALSE;
			}
		}else{
			if ($this->db->insert('budget_summary_master', $data)) {
				// $q_id = $this->db->insert_id();
				return TRUE;
			} else {
				return FALSE;
			}
		}
	}

	// sync Suppliers data

	public function syncSuppliers($supplierId,$supplierName,$supplierContact,$supplierMobile,$supplierEmail,$supplierAddress)
	{
		$data=[
			'procore_supplier_id' => $supplierId,
			'sup_name' => $supplierName,
			'sup_contact_person' => $supplierContact,
			'sup_phone' => $supplierMobile,
			'sup_email' => $supplierEmail,
			'sup_address' => $supplierAddress,
			'procore_integration_status' => 'YES',
			'sup_createdate' => date('Y-m-d h:m:s')
		];

		$data1=[
			'sup_name' => $supplierName,
			'sup_contact_person' => $supplierContact,
			'sup_phone' => $supplierMobile,
			'sup_email' => $supplierEmail,
			'sup_address' => $supplierAddress,
			'procore_integration_status' => 'YES',
			'sup_modifydate' => date('Y-m-d h:m:s')
		];

		if($this->db->where('procore_supplier_id',$supplierId)->get('supplier_master')->num_rows()>0){
			$this->db->where('procore_supplier_id',$supplierId);
			if ($this->db->update('supplier_master', $data1)) {
				// $q_id = $this->db->insert_id();
				return TRUE;
			} else {
				return FALSE;
		}
		}else{
			if ($this->db->insert('supplier_master', $data)) {
				// $q_id = $this->db->insert_id();
				return TRUE;
			} else {
				return FALSE;
			}
		}
	}

	// sync Suppliers user data

	public function syncSupplierUsers($contactId,$userType,$username,$password,$phone,$email,$firstname,$lastname,$procoreSupplierId,$procoreIntegrationStatus)
	{
		$user=$username;
		$userCount=$this->db->where('username',$username)->get('user_info')->num_rows();
		if($this->db->where('username',$username)->get('user_info')->num_rows()>0){
			$userCount=$userCount+1;
			$user=$username.$userCount;
		}
		$data=[
			'procore_contact_id' => $contactId,
			'u_type' => $userType,
			'username' => $user,
			'password' => $password,
			'phone' => $phone,
			'email' => $email,
			'firstname' => $firstname,
			'lastname' => $lastname,
			'procore_supplier_id' => $procoreSupplierId,
			'procore_integration_status' => 'YES',
			'create_date' => date('Y-m-d h:m:s')
		];

		$data1=[
			'u_type' => $userType,
			'username' => $user,
			'password' => $password,
			'phone' => $phone,
			'email' => $email,
			'firstname' => $firstname,
			'lastname' => $lastname,
			'procore_integration_status' => 'YES',
			'modify_date' => date('Y-m-d h:m:s')
		];

		$where=[
			'procore_contact_id' => $contactId,
			'procore_supplier_id' => $procoreSupplierId
		];

		if($this->db->where($where)->get('user_info')->num_rows()>0){
			$this->db->where($where);
			if ($this->db->update('user_info', $data1)) {
				// $q_id = $this->db->insert_id();
				return TRUE;
			} else {
				return FALSE;
			}
		}else{
			if ($this->db->insert('user_info', $data)) {
				// $q_id = $this->db->insert_id();
				return TRUE;
			} else {
				return FALSE;
			}
		}
	}

	// sync Tax Groups data

	public function syncTaxGroups($taxId,$taxName,$taxDesc,$taxRate)
	{
		$data=[
			'procore_tax_code_id' => $taxId,
			'name' => $taxName,
			'description' => $taxDesc,
			'percentage' => $taxRate,
			'procore_integration_status' => 'YES',
			'created_at' => date('Y-m-d h:m:s')
		];
		if ($this->db->insert('taxgroup_master', $data)) {
			// $q_id = $this->db->insert_id();
			return TRUE;
		} else {
			return FALSE;
		}
	}

	// update failed po details

	public function updateFailedPoDetails($poId,$poNo,$poProject,$poSupplier,$poTotalItems,$reason)
	{
		$data=[
			'fpo_porder_id' => $poId,
			'fpo_porder_no' => $poNo,
			'fpo_project_id' => $poProject,
			'fpo_supplier_id' => $poSupplier,
			'fpo_total_items' => $poTotalItems,
			'fpo_reason' => $reason,
			'fpo_createdate' => date('Y-m-d h:m:s'),
			'fpo_modifydate' => date('Y-m-d h:m:s')
		];

		$data1=[
			'fpo_porder_no' => $poNo,
			'fpo_project_id' => $poProject,
			'fpo_supplier_id' => $poSupplier,
			'fpo_total_items' => $poTotalItems,
			'fpo_reason' => $reason,
			'fpo_modifydate' => date('Y-m-d h:m:s')
		];

		if($this->db->where('fpo_porder_id',$poId)->get('failed_po_details')->num_rows()>0){
			$this->db->where('fpo_porder_id',$poId);
			if ($this->db->update('failed_po_details', $data1)) {
				// $q_id = $this->db->insert_id();
				return TRUE;
			} else {
				return FALSE;
			}
		}else{
			if ($this->db->insert('failed_po_details', $data)) {
				// $q_id = $this->db->insert_id();
				return TRUE;
			} else {
				return FALSE;
			}
		}
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

}

?>