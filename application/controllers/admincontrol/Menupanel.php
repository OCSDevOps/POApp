<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Menupanel extends Admin_Controller {
	
	public function __construct() { 

	 			parent::__construct(); 

			date_default_timezone_set("Asia/Kolkata");			

			$this->data["u_details"] = $this->admin_m->GetDetailsofUsers($this->session->userdata['uid']);
			$this->load->model('Manupanel_m');
		}
		
    public function index() {
        //$this->load->view('admin/main', $this->data);
        redirect('admincontrol/menupanel/menulist');
    }
    
    public function menulist(){
		$this->data['menu_list'] = $this->Manupanel_m->showAllMenu();
		$this->load->view('admin/menutab/menu_list_view', $this->data);
	}
	
	public function add_newmenu(){
		if($_POST){
			$m_name = $this->input->post("m_name");
            $m_link = $this->input->post("m_link");
            $m_type = $this->input->post("m_type");
            $m_order = $this->input->post("m_order");
            $m_primary = $this->input->post("m_primary");
			$menu_name_bengali = $this->input->post("menu_name_bengali");
            $m_sub = $this->input->post("m_sub");
			$m_sub_sub = $this->input->post("m_sub_sub");
			$menu_new_tab_open = $this->input->post('menu_new_tab_open');
            
            /*$this->form_validation->set_rules('m_name', 'Menu Name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('m_link', 'Menu Link', 'trim|required|xss_clean');
            $this->form_validation->set_rules('m_type', 'Menu Type', 'trim|required|is_natural|xss_clean');
            $this->form_validation->set_rules('m_order', 'Menu Order', 'trim|is_natural|xss_clean');*/

			$chk_no = 0;
			$chk_msg = '';
			if($m_name == ""){
				$chk_no++;
				$chk_msg = $chk_msg . 'Menu Name is required, Check Again.<br/>';
			}else{
				if(preg_match('/[^A-Za-z0-9_\/():.,\- ]/', $m_name))
				{
					$chk_no++;
					$chk_msg = $chk_msg . 'Menu Name not match proper format, Check Again.<br/>';
				}
			}
			if($m_type == ""){
				$chk_no++;
				$chk_msg = $chk_msg . 'Menu Type is required, Check Again.<br/>';
			}else{
				if(preg_match('/[^0-9]/', $m_type))
				{
					$chk_no++;
					$chk_msg = $chk_msg . 'Menu Type not match proper format, Check Again.<br/>';
				}else{
					if($m_type == 0){
						
					}elseif($m_type == 1){
						if($m_primary == ""){
							$chk_no++;
							$chk_msg = $chk_msg . 'Primary Menu is required, Check Again.<br/>';
						}else{
							if(preg_match('/[^0-9]/', $m_primary))
							{
								$chk_no++;
								$chk_msg = $chk_msg . 'Primary Menu not match proper format, Check Again.<br/>';
							}
						}
					}elseif($m_type == 2){
						if($m_sub == ""){
							$chk_no++;
							$chk_msg = $chk_msg . 'Sub Menu is required, Check Again.<br/>';
						}else{
							if(preg_match('/[^0-9]/', $m_sub))
							{
								$chk_no++;
								$chk_msg = $chk_msg . 'Sub Menu not match proper format, Check Again.<br/>';
							}
						}
					}elseif($m_type == 3){
						if($m_sub_sub == ""){
							$chk_no++;
							$chk_msg = $chk_msg . 'Sub-Sub Menu is required, Check Again.<br/>';
						}else{
							if(preg_match('/[^0-9]/', $m_sub_sub))
							{
								$chk_no++;
								$chk_msg = $chk_msg . 'Sub-Sub Menu not match proper format, Check Again.<br/>';
							}
						}
					}else{
						$chk_no++;
						$chk_msg = $chk_msg . 'Menu Type not match proper format, Check Again.<br/>';
					}
				}
			}
			if($m_order != ""){
				if(preg_match('/[^0-9]/', $m_order))
				{
					$chk_no++;
					$chk_msg = $chk_msg . 'Menu Order not match proper format, Check Again.<br/>';
				}
			}
			if($menu_new_tab_open == ""){
				$chk_no++;
				$chk_msg = $chk_msg . 'Menu New Tab is required, Check Again.<br/>';
			}else{
				if(preg_match('/[^A-Z]/', $menu_new_tab_open)){
					$chk_no++;
					$chk_msg = $chk_msg . 'Menu New Tab not match proper format, Check Again.<br/>';
				}
			}
			if($m_link == ""){
				$chk_no++;
				$chk_msg = $chk_msg . 'Menu link is required, Check Again.<br/>';
			}else{
				if(preg_match('/[~`!\^*\[\]\'{}()|\"<>]/i', $m_link)){
					$chk_no++;
					$chk_msg = $chk_msg . 'Menu link not match proper format, Check Again.<br/>';
				}
			}

			if($menu_name_bengali != ""){
				if(preg_match('/[~`!\^*\[\]\'{}()|\"<>]/i', $menu_name_bengali)){
					$chk_no++;
					$chk_msg = $chk_msg . 'Bengali Title not match proper format, Check Again.<br/>';
				}
			}
			
			if($chk_no != 0){
				$this->data['msg'] = $chk_msg;
			}else{
            	
            	date_default_timezone_set("Asia/Kolkata");
				
				if($m_type == 0){
					$p_menu = 0;
				}elseif($m_type == 1){
					$p_menu = $m_primary;
				}elseif($m_type == 2){
					$p_menu = $m_sub;
				}elseif($m_type == 3){
					$p_menu = $m_sub_sub;
				}
					
				$rows = array(
						'menu_name' => $m_name,
						'menu_new_tab_open' => $menu_new_tab_open,
						'menu_name_bengali' => $menu_name_bengali,
						'menu_link' => $m_link,
						'parent_menu' => $p_menu,
						'menu_level' => $m_type,
						'menu_createdate' => date('Y-m-d H:i:s'),
	                    'menu_createby' => $this->session->userdata('uid')
	                    //'menu_modifydate' => date('Y-m-d H:i:s'),
	                    //'menu_modifyby' => $this->session->userdata('uid')
	                );
            	
            	if($m_order != ""){
					$rows['menu_order'] = $m_order;
				}
            	
				if($this->Manupanel_m->MasterMenu_SaveUpdate_inDB($rows) == TRUE){
					$this->session->set_flashdata("success","New Menu is added successfully");
		    		redirect('admincontrol/menupanel/menulist','refresh');
				}else{
					$this->data["error"] = "There have some updatation Error, Try Again";
		    	}
				
            }
		}
		
		$this->data['main_menu_list'] = $this->db->order_by('menu_order', 'ASC')->get_where('menu_tab', array('parent_menu' => 0, 'menu_level' => 0, 'menu_status' => 1))->result();
		$this->data['submenu_list'] = $this->db->order_by('menu_order', 'ASC')->get_where('menu_tab', array('menu_level' => 1, 'menu_status' => 1))->result();
		$this->data['sub_sub_menu_list'] = $this->db->order_by('menu_order', 'ASC')->get_where('menu_tab', array('menu_level' => 2, 'menu_status' => 1))->result();
		$this->load->view('admin/menutab/add_new_menu', $this->data);
		
	}
	
	public function lock_menu($m_id = NULL){
		if($m_id == NULL){
			redirect('admincontrol/menupanel/menulist');
		}
		
		$rows['menu_status'] = 0;
		
		if($this->Manupanel_m->MasterMenu_SaveUpdate_inDB($rows, $m_id) == TRUE){
			$this->session->set_flashdata("success","Menu is locked successfully");
    		redirect('admincontrol/menupanel/menulist','refresh');
		}else{
			$this->session->set_flashdata("success","There have some Error, Try Again");
    		redirect('admincontrol/menupanel/menulist','refresh');
		}
		
	}
	
	public function unlock_menu($m_id = NULL){
		if($m_id == NULL){
			redirect('admincontrol/menupanel/menulist');
		}
		
		$rows['menu_status'] = 1;
		
		if($this->Manupanel_m->MasterMenu_SaveUpdate_inDB($rows, $m_id) == TRUE){
			$this->session->set_flashdata("success","Menu is unlocked successfully");
    		redirect('admincontrol/menupanel/menulist','refresh');
		}else{
			$this->session->set_flashdata("success","There have some Error, Try Again");
    		redirect('admincontrol/menupanel/menulist','refresh');
		}
		
	}

	public function edit_menu($m_id = NULL){
		
		if($m_id == NULL){
			redirect('admincontrol/menupanel/menulist');
		}
		
		if($_POST){
			$m_name = $this->input->post("m_name");
            $m_link = $this->input->post("m_link");
            $m_type = $this->input->post("m_type");
            $m_order = $this->input->post("m_order");
            $m_primary = $this->input->post("m_primary");
			$menu_name_bengali = $this->input->post("menu_name_bengali");
            $m_sub = $this->input->post("m_sub");
			$m_sub_sub = $this->input->post("m_sub_sub");
			$menu_new_tab_open = $this->input->post('menu_new_tab_open');
            
            /*$this->form_validation->set_rules('m_name', 'Menu Name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('m_link', 'Menu Link', 'trim|required|xss_clean');
            $this->form_validation->set_rules('m_type', 'Menu Type', 'trim|required|is_natural|xss_clean');
            $this->form_validation->set_rules('m_order', 'Menu Order', 'trim|is_natural|xss_clean');*/

			$chk_no = 0;
			$chk_msg = '';
			if($m_name == ""){
				$chk_no++;
				$chk_msg = $chk_msg . 'Menu Name is required, Check Again.<br/>';
			}else{
				if(preg_match('/[^A-Za-z0-9_\/():.,\- ]/', $m_name))
				{
					$chk_no++;
					$chk_msg = $chk_msg . 'Menu Name not match proper format, Check Again.<br/>';
				}
			}
			if($m_type == ""){
				$chk_no++;
				$chk_msg = $chk_msg . 'Menu Type is required, Check Again.<br/>';
			}else{
				if(preg_match('/[^0-9]/', $m_type))
				{
					$chk_no++;
					$chk_msg = $chk_msg . 'Menu Type not match proper format, Check Again.<br/>';
				}else{
					if($m_type == 0){
						
					}elseif($m_type == 1){
						if($m_primary == ""){
							$chk_no++;
							$chk_msg = $chk_msg . 'Primary Menu is required, Check Again.<br/>';
						}else{
							if(preg_match('/[^0-9]/', $m_primary))
							{
								$chk_no++;
								$chk_msg = $chk_msg . 'Primary Menu not match proper format, Check Again.<br/>';
							}
						}
					}elseif($m_type == 2){
						if($m_sub == ""){
							$chk_no++;
							$chk_msg = $chk_msg . 'Sub Menu is required, Check Again.<br/>';
						}else{
							if(preg_match('/[^0-9]/', $m_sub))
							{
								$chk_no++;
								$chk_msg = $chk_msg . 'Sub Menu not match proper format, Check Again.<br/>';
							}
						}
					}elseif($m_type == 3){
						if($m_sub_sub == ""){
							$chk_no++;
							$chk_msg = $chk_msg . 'Sub-Sub Menu is required, Check Again.<br/>';
						}else{
							if(preg_match('/[^0-9]/', $m_sub_sub))
							{
								$chk_no++;
								$chk_msg = $chk_msg . 'Sub-Sub Menu not match proper format, Check Again.<br/>';
							}
						}
					}else{
						$chk_no++;
						$chk_msg = $chk_msg . 'Menu Type not match proper format, Check Again.<br/>';
					}
				}
			}
			if($m_order != ""){
				if(preg_match('/[^0-9]/', $m_order))
				{
					$chk_no++;
					$chk_msg = $chk_msg . 'Menu Order not match proper format, Check Again.<br/>';
				}
			}
			if($menu_new_tab_open == ""){
				$chk_no++;
				$chk_msg = $chk_msg . 'Menu New Tab is required, Check Again.<br/>';
			}else{
				if(preg_match('/[^A-Z]/', $menu_new_tab_open)){
					$chk_no++;
					$chk_msg = $chk_msg . 'Menu New Tab not match proper format, Check Again.<br/>';
				}
			}
			if($m_link == ""){
				$chk_no++;
				$chk_msg = $chk_msg . 'Menu link is required, Check Again.<br/>';
			}else{
				if(preg_match('/[~`!\^*\[\]\'{}()|\"<>]/i', $m_link)){
					$chk_no++;
					$chk_msg = $chk_msg . 'Menu link not match proper format, Check Again.<br/>';
				}
			}

			if($menu_name_bengali != ""){
				if(preg_match('/[~`!\^*\[\]\'{}()|\"<>]/i', $menu_name_bengali)){
					$chk_no++;
					$chk_msg = $chk_msg . 'Bengali Title not match proper format, Check Again.<br/>';
				}
			}
			
			if($chk_no != 0){
				$this->data['msg'] = $chk_msg;
			}else{
            	
            	date_default_timezone_set("Asia/Kolkata");
				
				if($m_type == 0){
					$p_menu = 0;
				}elseif($m_type == 1){
					$p_menu = $m_primary;
				}elseif($m_type == 2){
					$p_menu = $m_sub;
				}elseif($m_type == 3){
					$p_menu = $m_sub_sub;
				}
					
				$rows = array(
						'menu_name' => $m_name,
						'menu_new_tab_open' => $this->input->post('menu_new_tab_open'),
						'menu_name_bengali' => $menu_name_bengali,
						'menu_link' => $m_link,
						'parent_menu' => $p_menu,
						'menu_level' => $m_type,
						//'menu_order' => $m_order,
						'menu_modifydate' => date('Y-m-d H:i:s'),
	                    'menu_modifyby' => $this->session->userdata('uid')
	                );
				if($m_order != ""){
					$rows['menu_order'] = $m_order;
				}else{
					$rows['menu_order'] = 0;
				}
            	
				if($this->Manupanel_m->MasterMenu_SaveUpdate_inDB($rows, $m_id) == TRUE){
					$this->session->set_flashdata("success","Menu is updated successfully");
		    		redirect('admincontrol/menupanel/menulist','refresh');
				}else{
					$this->data["error"] = "There have some updatation Error, Try Again";
				}
				
            }
		}
		
		$this->data['main_menu_list'] = $this->db->order_by('menu_order', 'ASC')->get_where('menu_tab', array('menu_id !=' => $m_id, 'parent_menu' => 0, 'menu_level' => 0, 'menu_status' => 1))->result();
		$this->data['submenu_list'] = $this->db->order_by('menu_order', 'ASC')->get_where('menu_tab', array('menu_id !=' => $m_id, 'menu_level' => 1, 'menu_status' => 1))->result();
		$this->data['sub_sub_menu_list'] = $this->db->order_by('menu_order', 'ASC')->get_where('menu_tab', array('menu_id !=' => $m_id, 'menu_level' => 2, 'menu_status' => 1))->result();
		
		$this->data['m_detail'] = $this->db->get_where('menu_tab', array('menu_id' => $m_id))->row();
		
		$this->load->view('admin/menutab/edit_exist_menu', $this->data);
		
	}

}
