<?php



class Manupanel_m extends CI_Model {


	
	public function showAllMenu(){
		//SELECT p2.*,p1.menu_name AS p_menu FROM `p_menu_tab` p1 RIGHT JOIN `p_menu_tab` p2 ON `p1`.`menu_id` = `p2`.`parent_menu`;
		$this->db->select('p2.*, p1.menu_name AS p_menu');
		$this->db->from('menu_tab p1');
		$this->db->join('menu_tab p2', 'p1.menu_id = p2.parent_menu', 'right');
		$query = $this->db->get();
		return $query->result();
	
	}
	public function MasterMenu_SaveUpdate_inDB($rows, $m_id = NULL){
		$this->db->set($rows);
		if($m_id == NULL){
			if($this->db->insert('menu_tab'))
	        	return TRUE;
	        else
	        	return FALSE;
	    }elseif($m_id != NULL){
	    	$this->db->where('menu_id', $m_id);
			if($this->db->update('menu_tab', $rows))
	        	return TRUE;
	        else
	        	return FALSE;
		}
	}


}

