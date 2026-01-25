<?php

class Equipment_Model extends MY_Model
{

    function __construct() {
        parent::__construct();
    }

	//insert equipment

	public function insertEquipment($row_arrary, $table_name)
	{
		$this->db->set($row_arrary);
		if ($this->db->insert($table_name, $row_arrary)) {
			$insert_id = $this->db->insert_id();
			$history=[
				'eq_id' => $insert_id,
				'eqh_description' => 'Equipment Created',
				'eqh_created_date' => date('Y-m-d H:i:s')
			];
			$this->db->insert('eq_history', $history);
			return $insert_id;
		} else {
			return FALSE;
		}
	}

	//insert checkout

	public function insertCheckOut($row_arrary, $table_name)
	{
		$this->db->set($row_arrary);
		if ($this->db->insert($table_name, $row_arrary)) {
			$insert_id = $this->db->insert_id();
			$history=[
				'eq_id' => $row_arrary['checkout_eq_id'],
				'eqh_description' => 'Equipment Checked Out',
				'eqh_created_date' => date('Y-m-d H:i:s')
			];
			$this->db->insert('eq_history', $history);
			return $insert_id;
		} else {
			return FALSE;
		}
	}

	//insert checkin

	public function insertCheckIn($row_arrary, $table_name)
	{
		$this->db->set($row_arrary);
		if ($this->db->insert($table_name, $row_arrary)) {
			$insert_id = $this->db->insert_id();
			$history=[
				'eq_id' => $row_arrary['checkin_eq_id'],
				'eqh_description' => 'Equipment Checked In',
				'eqh_created_date' => date('Y-m-d H:i:s')
			];
			$this->db->insert('eq_history', $history);
			return $insert_id;
		} else {
			return FALSE;
		}
	}

	//update equipment reading

	public function updateEquipmentReadingDetails($row_arrary, $table_name)
	{
		$this->db->set($row_arrary);
		if ($this->db->insert($table_name, $row_arrary)) {
			$insert_id = $this->db->insert_id();
			$history=[
				'eq_id' => $row_arrary['eq_id'],
				'eqh_description' => 'Updated Equipment Current reading',
				'eqh_created_date' => date('Y-m-d H:i:s')
			];
			$this->db->insert('eq_history', $history);
			return $insert_id;
		} else {
			return FALSE;
		}
	}

	//insert Permission Template

	public function insertPermissions($row_arrary, $table_name)
	{
		$this->db->set($row_arrary);
		if ($this->db->insert($table_name, $row_arrary)) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return FALSE;
		}
	}

	//update Permission Template

	public function updatePermissions($row_arrary, $table_name,$pt_id)
	{
		$this->db->where('pt_id',$pt_id);
		if ($this->db->update($table_name, $row_arrary)) {
			// $insert_id = $this->db->insert_id();
			// $this->db->insert('eq_history', $history);
			return TRUE;
		} else {
			return FALSE;
		}
	}

	//insert Maintenance

	public function insertNewMaintenance($row_arrary, $table_name)
	{
		$this->db->set($row_arrary);
		if ($this->db->insert($table_name, $row_arrary)) {
			$insert_id = $this->db->insert_id();
			$history=[
				'eq_id' => $row_arrary['asset_id'],
				'eqh_description' => 'Maintenance Created',
				'eqh_created_date' => date('Y-m-d H:i:s')
			];
			$this->db->insert('eq_history', $history);
			return $insert_id;
		} else {
			return FALSE;
		}
	}

	//update Maintenance

	public function updateMaintenance($row_arrary, $table_name, $eqm_id, $deleteIds)
	{
		if(!empty($deleteIds)){
			foreach($deleteIds as $delete){
				$result=$this->db->where(['eqmd_id'=>$delete,'status'=>1])->get('eqm_details')->num_rows();
				if($result>0){
					$this->db->where('eqmd_id',$delete);
					if($this->db->update('eqm_details',['status'=>0])){
						$history=[
							'eq_id' => $row_arrary['asset_id'],
							'eqh_description' => 'Miantenance Detail Deleted',
							'eqh_created_date' => date('Y-m-d H:i:s')
						];
						$this->db->insert('eq_history', $history);
					}
				}
			}
		}
		$this->db->where('eqm_id',$eqm_id);
		if ($this->db->update($table_name, $row_arrary)) {
			// $insert_id = $this->db->insert_id();
			$history=[
				'eq_id' => $row_arrary['asset_id'],
				'eqh_description' => 'Maintenance Updated',
				'eqh_created_date' => date('Y-m-d H:i:s')
			];
			$this->db->insert('eq_history', $history);
			return TRUE;
		} else {
			return FALSE;
		}
	}

	//insert Maintenance details

	public function insertMaintenanceDetails($row_arrary, $table_name, $asset_id)
	{
		$this->db->set($row_arrary);
		if ($this->db->insert($table_name, $row_arrary)) {
			$insert_id = $this->db->insert_id();
			$history=[
				'eq_id' => $asset_id,
				'eqh_description' => 'Maintenance Details Added',
				'eqh_created_date' => date('Y-m-d H:i:s')
			];
			$this->db->insert('eq_history', $history);
			return $insert_id;
		} else {
			return FALSE;
		}
	}

	//update Maintenance details

	public function updateMaintenanceDetails($row_arrary, $table_name, $asset_id, $eqmd_id)
	{
		if($eqmd_id!=''){
			$row_arrary+=[
				'modified_date' => date('Y-m-d H:i:s')
			];
			$this->db->where('eqmd_id',$eqmd_id);
			if ($this->db->update($table_name, $row_arrary)) {
				// $insert_id = $this->db->insert_id();
				$history=[
					'eq_id' => $asset_id,
					'eqh_description' => 'Maintenance Details updated',
					'eqh_created_date' => date('Y-m-d H:i:s')
				];
				$this->db->insert('eq_history', $history);
				return TRUE;
			} else {
				return FALSE;
			}
		}else{
			$row_arrary+=[
				'created_date' => date('Y-m-d H:i:s')
			];
			$this->db->set($row_arrary);
			if ($this->db->insert($table_name, $row_arrary)) {
				$insert_id = $this->db->insert_id();
				$history=[
					'eq_id' => $asset_id,
					'eqh_description' => 'Maintenance Details Added',
					'eqh_created_date' => date('Y-m-d H:i:s')
				];
				$this->db->insert('eq_history', $history);
				return $insert_id;
			} else {
				return FALSE;
			}
		}
	}

	//update equipment

	public function updateEquipment($row_arrary, $table_name, $eq_id)
	{
		$this->db->where('eq_id',$eq_id);
		if ($this->db->update($table_name, $row_arrary)) {
			// $insert_id = $this->db->insert_id();
			$history=[
				'eq_id' => $eq_id,
				'eqh_description' => 'Equipment Details Updated',
				'eqh_created_date' => date('Y-m-d H:i:s')
			];
			$this->db->insert('eq_history', $history);
			return TRUE;
		} else {
			return FALSE;
		}
	}

	//insert component

	public function insertComponent($row_arrary, $table_name, $deleteIds,$eqc_id)
	{
		if(!empty($deleteIds)){
			foreach($deleteIds as $delete){
				$result=$this->db->where(['eqc_id'=>$delete,'status'=>1])->get($table_name)->num_rows();
				if($result>0){
					$this->db->where('eqc_id',$delete);
					if($this->db->update($table_name,['status'=>0])){
						$history=[
							'eq_id' => $row_arrary['eq_id'],
							'eqh_description' => 'Component Deleted',
							'eqh_created_date' => date('Y-m-d H:i:s')
						];
						$this->db->insert('eq_history', $history);
					}
				}
			}
		}
		if($eqc_id!=''){
			$row_arrary+=[
				'eqc_modified_date' => date('Y-m-d H:i:s')
			];
			if ($this->db->where(['eq_id'=>$row_arrary['eq_id'],'eqc_id'=>$eqc_id])->update($table_name,$row_arrary)) {
				$history=[
					'eq_id' => $row_arrary['eq_id'],
					'eqh_description' => 'Component Updated',
					'eqh_created_date' => date('Y-m-d H:i:s')
				];
				$this->db->insert('eq_history', $history);
				return TRUE;
			} else {
				return FALSE;
			}
		}else{
			$row_arrary+=[
				'eqc_created_date' => date('Y-m-d H:i:s')
			];
			$this->db->set($row_arrary);
			if ($this->db->insert($table_name, $row_arrary)) {
				$insert_id = $this->db->insert_id();
				$history=[
					'eq_id' => $row_arrary['eq_id'],
					'eqh_description' => 'Component Added',
					'eqh_created_date' => date('Y-m-d H:i:s')
				];
				$this->db->insert('eq_history', $history);
				return $insert_id;
			} else {
				return FALSE;
			}
		}
	}

	//insert maintenance

	public function insertMaintenance($row_arrary, $table_name)
	{
		if($this->db->where('eq_id',$row_arrary['eq_id'])->get('eq_maintenance')->num_rows()>0){
			$row_arrary+=[
				'eqm_modified_date' => date('Y-m-d H:i:s')
			];
			if ($this->db->where(['eq_id'=>$row_arrary['eq_id']])->update($table_name,$row_arrary)) {
				$history=[
					'eq_id' => $row_arrary['eq_id'],
					'eqh_description' => 'Maintenance Details Updated',
					'eqh_created_date' => date('Y-m-d H:i:s')
				];
				$this->db->insert('eq_history', $history);
				// return $insert_id;
				return TRUE;
			} else {
				return FALSE;
			}
		}else{
			$row_arrary+=[
				'eqm_created_date' => date('Y-m-d H:i:s')
			];
			$this->db->set($row_arrary);
			if ($this->db->insert($table_name, $row_arrary)) {
				$history=[
					'eq_id' => $row_arrary['eq_id'],
					'eqh_description' => 'Maintenance Details Added',
					'eqh_created_date' => date('Y-m-d H:i:s')
				];
				$this->db->insert('eq_history', $history);
				$insert_id = $this->db->insert_id();
				return $insert_id;
			} else {
				return FALSE;
			}
		}
	}
	
	//insert Checklist

	public function insertNewCheckList($row_arrary, $table_name)
	{
		$this->db->set($row_arrary);
		if ($this->db->insert($table_name, $row_arrary)) {
			// foreach($row_arrary['cl_eq_ids'] as $equip){
			// 	$history=[
			// 		'eq_id' => $equip,
			// 		'eqh_description' => 'Added CheckList',
			// 		'eqh_created_date' => date('Y-m-d H:i:s')
			// 	];
			// 	$this->db->insert('eq_history', $history);
			// }
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return FALSE;
		}
	}

	//insert Checklist details

	public function insertCheckListDetails($row_arrary, $table_name)
	{
		$this->db->set($row_arrary);
		if ($this->db->insert($table_name, $row_arrary)) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return FALSE;
		}
	}
	
	//update Checklist

	public function updateCheckList($row_arrary, $table_name, $cl_id, $deleteIds)
	{
		if(!empty($deleteIds)){
			foreach($deleteIds as $delete){
				$result=$this->db->where(['cli_id'=>$delete,'status'=>1])->get('checklist_details')->num_rows();
				if($result>0){
					$this->db->where('cli_id',$delete);
					if($this->db->update('checklist_details',['status'=>0])){
					}
				}
			}
		}
		$this->db->where('cl_id',$cl_id);
		if ($this->db->update($table_name, $row_arrary)) {
			// foreach($eq_ids as $equip){
			// 	$history=[
			// 		'eq_id' => $equip,
			// 		'eqh_description' => 'Added CheckList',
			// 		'eqh_created_date' => date('Y-m-d H:i:s')
			// 	];
			// 	$this->db->insert('eq_history', $history);
			// }
			// $insert_id = $this->db->insert_id();
			return TRUE;
		} else {
			return FALSE;
		}
	}

	//update Checklist details

	public function updateCheckListDetails($row_arrary, $table_name, $cl_id, $cli_id)
	{
		if($cli_id!=''){
			$row_arrary+=[
				'modified_date' => date('Y-m-d H:i:s')
			];
			$this->db->where('cli_id',$cli_id);
			if ($this->db->update($table_name, $row_arrary)) {
				// $insert_id = $this->db->insert_id();
				return TRUE;
			} else {
				return FALSE;
			}
		}else{
			$row_arrary+=[
				'created_date' => date('Y-m-d H:i:s')
			];
			$this->db->set($row_arrary);
			if ($this->db->insert($table_name, $row_arrary)) {
				$insert_id = $this->db->insert_id();
				return $insert_id;
			} else {
				return FALSE;
			}
		}
	}

	//insert Checklist Performance

	public function insertNewCheckListPerformance($row_arrary, $table_name)
	{
		$this->db->set($row_arrary);
		if ($this->db->insert($table_name, $row_arrary)) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return FALSE;
		}
	}

	//insert Checklist Performance

	public function insertNewCheckListPerformanceDetails($row_arrary, $table_name)
	{
		$this->db->set($row_arrary);
		if ($this->db->insert($table_name, $row_arrary)) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return FALSE;
		}
	}

	//insert package

	public function insertNewPackage($row_arrary, $table_name)
	{
		$this->db->set($row_arrary);
		if ($this->db->insert($table_name, $row_arrary)) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return FALSE;
		}
	}

	//insert Checklist Performance

	public function insertNewPackageDetails($row_arrary, $table_name)
	{
		$this->db->set($row_arrary);
		if ($this->db->insert($table_name, $row_arrary)) {
			// $insert_id = $this->db->insert_id();
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	//update Package

	public function updatePackage($row_arrary, $table_name, $ipack_id, $deleteIds)
	{
		if(!empty($deleteIds)){
			foreach($deleteIds as $delete){
				$result=$this->db->where(['ipdetail_id'=>$delete,'ipdetail_status'=>1])->get('item_package_details')->num_rows();
				if($result>0){
					$this->db->where('ipdetail_id',$delete);
					if($this->db->update('item_package_details',['ipdetail_status'=>0])){
					}
				}
			}
		}
		$this->db->where('ipack_id',$ipack_id);
		if ($this->db->update($table_name, $row_arrary)) {
			// $insert_id = $this->db->insert_id();
			return TRUE;
		} else {
			return FALSE;
		}
	}

	//update Package details

	public function updatePackageDetails($row_arrary, $table_name, $ipack_id, $ipdetail_id)
	{
		if($ipdetail_id!=''){
			$row_arrary+=[
				'ipdetail_modifydate' => date('Y-m-d H:i:s')
			];
			$this->db->where('ipdetail_id',$ipdetail_id);
			if ($this->db->update($table_name, $row_arrary)) {
				// $insert_id = $this->db->insert_id();
				return TRUE;
			} else {
				return FALSE;
			}
		}else{
			$row_arrary+=[
				'ipdetail_createdate' => date('Y-m-d H:i:s')
			];
			$this->db->set($row_arrary);
			if ($this->db->insert($table_name, $row_arrary)) {
				$insert_id = $this->db->insert_id();
				return $insert_id;
			} else {
				return FALSE;
			}
		}
	}

}

?>