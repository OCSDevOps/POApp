<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Panel extends Admin_Controller {
	
	 public function __construct() {
        parent::__construct();
        date_default_timezone_set("Asia/Kolkata");
        $this->data["u_details"] = $this->admin_m->GetDetailsofUsers($this->session->userdata['uid']);
        $this->load->model('main_m');
    }
	
    public function index() {
		/*$this->data['users'] = $this->admin_m->getMembers();
        $this->load->view('admin/main', $this->data);*/
        redirect('admincontrol/panel/work_list');
    }
    
    public function work_list(){
		$this->data['work_list'] = $this->admin_m->getAll_work_fromDB();
		$this->load->view('admin/work/work_list_view', $this->data);
	}

	public function add_new_work(){
		if($_POST){
			$f_year = $this->input->post('f_year');
			$w_name = $this->input->post('w_name');
			$w_loc = $this->input->post('w_loc');
			$w_fund = $this->input->post('w_fund');
			$w_fund_name = $this->input->post('w_fund_name');
			$w_sector = $this->input->post('w_sector');
			$w_sector_name = $this->input->post('w_sector_name');
			$w_t_float = $this->input->post('w_t_float');

			$w_t_amount = $this->input->post('w_t_amount');
			$w_t_date = $this->input->post('w_t_date');
			$w_t_mode = $this->input->post('w_t_mode');
			$w_nitno = $this->input->post('w_nitno');
			$w_t_mature = $this->input->post('w_t_mature');
			$w_order_date = $this->input->post('w_order_date');
			$w_award_amount = $this->input->post('w_award_amount');
			$w_agency_name = $this->input->post('w_agency_name');
			$w_agency_mobile = $this->input->post('w_agency_mobile');
			$w_agency_gst = $this->input->post('w_agency_gst');
			$w_emd_amount = $this->input->post('w_emd_amount');
			$w_com_date = $this->input->post('w_com_date');
			$w_tent_date = $this->input->post('w_tent_date');
			
			$this->form_validation->set_rules('f_year', 'Financial Year', 'trim|required');
			$this->form_validation->set_rules('w_name', 'Name of Work', 'trim|required');
			$this->form_validation->set_rules('w_loc', 'Location of the scheme', 'trim|required');
			$this->form_validation->set_rules('w_fund', 'Sources of fund', 'trim|required');
			$this->form_validation->set_rules('w_sector', 'Work Sector', 'trim|required');
			$this->form_validation->set_rules('w_t_float', 'Tender floated', 'trim|required|alpha');

			if($w_fund == "Others"){
				$this->form_validation->set_rules('w_fund_name', 'Sources of fund Name', 'trim|required');
			}
			if($w_sector == "Others"){
				$this->form_validation->set_rules('w_sector_name', 'Work Sector Name', 'trim|required');
			}
			if($w_t_float == "Yes"){
				$this->form_validation->set_rules('w_t_amount', 'Amount of Tender', 'trim|required');
				$this->form_validation->set_rules('w_t_date', 'Tender Date', 'trim|required');
				$this->form_validation->set_rules('w_t_mode', 'Tender Mode', 'trim|required');
				$this->form_validation->set_rules('w_nitno', 'NIT No.', 'trim|required');
				$this->form_validation->set_rules('w_t_mature', 'Tender matured', 'trim|required|alpha');
				$this->form_validation->set_rules('w_order_date', 'Workorder Date', 'trim|required');
				$this->form_validation->set_rules('w_award_amount', 'Awarded Cost', 'trim|required');
				$this->form_validation->set_rules('w_agency_name', 'Name of the Agency/contractor', 'trim|required');
				$this->form_validation->set_rules('w_agency_mobile', 'Agency/contractor Mobile', 'trim|required|exact_length[10]|is_natural');
				$this->form_validation->set_rules('w_agency_gst', 'GST No. of the Agency/constrictor', 'trim|required');
				$this->form_validation->set_rules('w_emd_amount', 'EMD Amount', 'trim|required');
				$this->form_validation->set_rules('w_com_date', 'Date of commencement', 'trim|required');
				$this->form_validation->set_rules('w_tent_date', 'Tentative date of completion', 'trim|required');
			}
			
			if($this->form_validation->run() == TRUE)
            {
				
				$row2 = $row3 = NULL;
				$final_fund = $final_sector = NULL;
				if($w_fund == "Others"){
					$row2 = array(
						'fs_name' => trim($w_fund_name),
						'fs_createdate' => date('Y-m-d H:i:s'),
						'fs_createby' => $this->session->userdata['uid']
					);
				}else{
					$final_fund = $w_fund;
				}
				if($w_sector == "Others"){
					$row3 = array(
						'ws_name' => trim($w_sector_name),
						'ws_createdate' => date('Y-m-d H:i:s'),
						'ws_createby' => $this->session->userdata['uid']
					);
				}else{
					$final_sector = $w_sector;
				}
				
				if($w_t_float == "Yes"){
					if($_FILES["w_nit_doc"]["name"] != '' && $_FILES["w_order_doc"]["name"] != ''){

						$config["upload_path"] =  'upload_file/nit_doc/';
						$config["allowed_types"] = 'jpg|jpeg|png|JPG|JPEG|PNG|pdf|PDF';
						$config['remove_spaces'] = TRUE;
						$config['overwrite'] = FALSE;
						$config['max_size'] = '10000';
						
						$this->load->library('upload', $config);
						$this->upload->initialize($config);

						$_FILES["file"]["name"] = $_FILES["w_nit_doc"]["name"];
						$_FILES["file"]["type"] = $_FILES["w_nit_doc"]["type"];
						$_FILES["file"]["tmp_name"] = $_FILES["w_nit_doc"]["tmp_name"];
						$_FILES["file"]["error"] = $_FILES["w_nit_doc"]["error"];
						$_FILES["file"]["size"] = $_FILES["w_nit_doc"]["size"];

						if($this->upload->do_upload('file'))
						{
							$upload_data = $this->upload->data();
							$up_nit = $upload_data['file_name'];

							$config["upload_path"] =  'upload_file/worder_doc/';
							$config["allowed_types"] = 'jpg|jpeg|png|JPG|JPEG|PNG|pdf|PDF';
							$config['remove_spaces'] = TRUE;
							$config['overwrite'] = FALSE;
							$config['max_size'] = '10000';
							
							$this->load->library('upload', $config);
							$this->upload->initialize($config);

							$_FILES["file"]["name"] = $_FILES["w_order_doc"]["name"];
							$_FILES["file"]["type"] = $_FILES["w_order_doc"]["type"];
							$_FILES["file"]["tmp_name"] = $_FILES["w_order_doc"]["tmp_name"];
							$_FILES["file"]["error"] = $_FILES["w_order_doc"]["error"];
							$_FILES["file"]["size"] = $_FILES["w_order_doc"]["size"];

							if($this->upload->do_upload('file'))
							{
								$upload_data = $this->upload->data();
								$up_worder = $upload_data['file_name'];

								$random_keys = 'W'. date('dmYHis') . $this->generateRandomString();
								$row_array = array(
									'mw_year' => $f_year,
									'mw_name' => trim($w_name),
									'mw_unique_id' => $random_keys,
									'mw_location' => trim($w_loc),
									'mw_fund_source' => $final_fund,
									'mw_sector' => $final_sector,
									'mw_tender_float' => $w_t_float,
									'mw_tender_amount' => trim($w_t_amount),
									'mw_tender_date' => date('Y-m-d',strtotime($w_t_date)),
									'mw_tender_mode' => $w_t_mode,
									'mw_nit_no' => trim($w_nitno),
									'mw_nit_doc' => $up_nit,
									'mw_tender_mature' => $w_t_mature,
									'mw_order_issue_date' => date('Y-m-d',strtotime($w_order_date)),
									'mw_award_cost' => trim($w_award_amount),
									'mw_workorder_doc' => $up_worder,
									'mw_agency_name' => trim($w_agency_name),
									'mw_agency_mobile' => $w_agency_mobile,
									'mw_agency_gst' => trim($w_agency_gst),
									'mw_emd_amount' => trim($w_emd_amount),
									'mw_commence_date' => date('Y-m-d',strtotime($w_com_date)),
									'mw_tentative_date' => date('Y-m-d',strtotime($w_tent_date)),
									'mw_createdate' => date('Y-m-d H:i:s'),
									'mw_createby' => $this->session->userdata['uid']
								);

								if($this->admin_m->addUpdateform_ofWork_inDB($row_array, $row2, $row3) == TRUE){
									$this->session->set_flashdata("success","New Work successfully submitted.");
									redirect('admincontrol/panel/work_list','refresh');
								}else{
									$this->data['error'] = "There have some problem to Update DB, Try Again.";
								}

							}else{
								$this->data['error'] = "Workorder File not Upload Properly, Try Again.";
							}

						}else{
							$this->data['error'] = "NIT Document not Upload Properly, Try Again.";
						}
					}else{
						$this->data['error'] = "Upload Files not found, Check Again.";
					}

				}else{
					$random_keys = 'W'. date('dmYHis') . $this->generateRandomString();
					$row_array = array(
						'mw_year' => $f_year,
						'mw_name' => trim($w_name),
						'mw_unique_id' => $random_keys,
						'mw_location' => trim($w_loc),
						'mw_fund_source' => $final_fund,
						'mw_sector' => $final_sector,
						'mw_tender_float' => $w_t_float,
						'mw_createdate' => date('Y-m-d H:i:s'),
						'mw_createby' => $this->session->userdata['uid']
					);

					if($this->admin_m->addUpdateform_ofWork_inDB($row_array, $row2, $row3) == TRUE){
						$this->session->set_flashdata("success","New Work successfully submitted.");
						redirect('admincontrol/panel/work_list','refresh');
					}else{
						$this->data['error'] = "There have some problem to Update DB, Try Again.";
					}
				}
					
				
			}else{
				$this->data['error'] = validation_errors();
			}
		}
		$this->data['fund_list'] = $this->db->get_where('fund_source_tab',array('fs_status'=>1))->result();
		$this->data['sector_list'] = $this->db->get_where('work_sector_tab',array('ws_status'=>1))->result();
		$this->load->view('admin/work/add_work_view', $this->data);
	}

	protected function generateRandomString($length = 4){
		$characters = '0123456789';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	public function edit_work($workid = NULL){
		if($workid == NULL){
			redirect('admincontrol/panel/work_list');
		}
		if($_POST){
			$f_year = $this->input->post('f_year');
			$w_name = $this->input->post('w_name');
			$w_loc = $this->input->post('w_loc');
			$w_fund = $this->input->post('w_fund');
			$w_fund_name = $this->input->post('w_fund_name');
			$w_sector = $this->input->post('w_sector');
			$w_sector_name = $this->input->post('w_sector_name');
			$w_t_float = $this->input->post('w_t_float');

			$w_t_amount = $this->input->post('w_t_amount');
			$w_t_date = $this->input->post('w_t_date');
			$w_t_mode = $this->input->post('w_t_mode');
			$w_nitno = $this->input->post('w_nitno');
			$w_t_mature = $this->input->post('w_t_mature');
			$w_order_date = $this->input->post('w_order_date');
			$w_award_amount = $this->input->post('w_award_amount');
			$w_agency_name = $this->input->post('w_agency_name');
			$w_agency_mobile = $this->input->post('w_agency_mobile');
			$w_agency_gst = $this->input->post('w_agency_gst');
			$w_emd_amount = $this->input->post('w_emd_amount');
			$w_com_date = $this->input->post('w_com_date');
			$w_tent_date = $this->input->post('w_tent_date');
			
			$this->form_validation->set_rules('f_year', 'Financial Year', 'trim|required');
			$this->form_validation->set_rules('w_name', 'Name of Work', 'trim|required');
			$this->form_validation->set_rules('w_loc', 'Location of the scheme', 'trim|required');
			$this->form_validation->set_rules('w_fund', 'Sources of fund', 'trim|required');
			$this->form_validation->set_rules('w_sector', 'Work Sector', 'trim|required');
			$this->form_validation->set_rules('w_t_float', 'Tender floated', 'trim|required|alpha');

			if($w_fund == "Others"){
				$this->form_validation->set_rules('w_fund_name', 'Sources of fund Name', 'trim|required');
			}
			if($w_sector == "Others"){
				$this->form_validation->set_rules('w_sector_name', 'Work Sector Name', 'trim|required');
			}
			if($w_t_float == "Yes"){
				$this->form_validation->set_rules('w_t_amount', 'Amount of Tender', 'trim|required');
				$this->form_validation->set_rules('w_t_date', 'Tender Date', 'trim|required');
				$this->form_validation->set_rules('w_t_mode', 'Tender Mode', 'trim|required');
				$this->form_validation->set_rules('w_nitno', 'NIT No.', 'trim|required');
				$this->form_validation->set_rules('w_t_mature', 'Tender matured', 'trim|required|alpha');
				$this->form_validation->set_rules('w_order_date', 'Workorder Date', 'trim|required');
				$this->form_validation->set_rules('w_award_amount', 'Awarded Cost', 'trim|required');
				$this->form_validation->set_rules('w_agency_name', 'Name of the Agency/contractor', 'trim|required');
				$this->form_validation->set_rules('w_agency_mobile', 'Agency/contractor Mobile', 'trim|required|exact_length[10]|is_natural');
				$this->form_validation->set_rules('w_agency_gst', 'GST No. of the Agency/constrictor', 'trim|required');
				$this->form_validation->set_rules('w_emd_amount', 'EMD Amount', 'trim|required');
				$this->form_validation->set_rules('w_com_date', 'Date of commencement', 'trim|required');
				$this->form_validation->set_rules('w_tent_date', 'Tentative date of completion', 'trim|required');
			}
			
			if($this->form_validation->run() == TRUE)
            {
				
				$row2 = $row3 = NULL;
				$final_fund = $final_sector = NULL;
				if($w_fund == "Others"){
					$row2 = array(
						'fs_name' => trim($w_fund_name),
						'fs_createdate' => date('Y-m-d H:i:s'),
						'fs_createby' => $this->session->userdata['uid']
					);
				}else{
					$final_fund = $w_fund;
				}
				if($w_sector == "Others"){
					$row3 = array(
						'ws_name' => trim($w_sector_name),
						'ws_createdate' => date('Y-m-d H:i:s'),
						'ws_createby' => $this->session->userdata['uid']
					);
				}else{
					$final_sector = $w_sector;
				}
				
				if($w_t_float == "Yes"){

					$fileset_cnt = 0;
					if($_FILES["w_nit_doc"]["name"] != ''){
						$config["upload_path"] =  'upload_file/nit_doc/';
						$config["allowed_types"] = 'jpg|jpeg|png|JPG|JPEG|PNG|pdf|PDF';
						$config['remove_spaces'] = TRUE;
						$config['overwrite'] = FALSE;
						$config['max_size'] = '10000';
						
						$this->load->library('upload', $config);
						$this->upload->initialize($config);

						$_FILES["file"]["name"] = $_FILES["w_nit_doc"]["name"];
						$_FILES["file"]["type"] = $_FILES["w_nit_doc"]["type"];
						$_FILES["file"]["tmp_name"] = $_FILES["w_nit_doc"]["tmp_name"];
						$_FILES["file"]["error"] = $_FILES["w_nit_doc"]["error"];
						$_FILES["file"]["size"] = $_FILES["w_nit_doc"]["size"];

						if($this->upload->do_upload('file'))
						{
							$upload_data = $this->upload->data();
							$up_nit = $upload_data['file_name'];
						}else{
							$fileset_cnt++;
							$this->data['error'] = "NIT Document not Upload Properly, Try Again.";
						}
					}

					if($_FILES["w_order_doc"]["name"] != ''){
						$config["upload_path"] =  'upload_file/worder_doc/';
						$config["allowed_types"] = 'jpg|jpeg|png|JPG|JPEG|PNG|pdf|PDF';
						$config['remove_spaces'] = TRUE;
						$config['overwrite'] = FALSE;
						$config['max_size'] = '10000';
						
						$this->load->library('upload', $config);
						$this->upload->initialize($config);

						$_FILES["file"]["name"] = $_FILES["w_order_doc"]["name"];
						$_FILES["file"]["type"] = $_FILES["w_order_doc"]["type"];
						$_FILES["file"]["tmp_name"] = $_FILES["w_order_doc"]["tmp_name"];
						$_FILES["file"]["error"] = $_FILES["w_order_doc"]["error"];
						$_FILES["file"]["size"] = $_FILES["w_order_doc"]["size"];

						if($this->upload->do_upload('file'))
						{
							$upload_data = $this->upload->data();
							$up_worder = $upload_data['file_name'];
						}else{
							$fileset_cnt++;
							$this->data['error'] = "Workorder File not Upload Properly, Try Again.";
						}
					}

					if($fileset_cnt == 0){
						$row_array = array(
							'mw_year' => $f_year,
							'mw_name' => trim($w_name),
							'mw_location' => trim($w_loc),
							'mw_fund_source' => $final_fund,
							'mw_sector' => $final_sector,
							'mw_tender_float' => $w_t_float,
							'mw_tender_amount' => trim($w_t_amount),
							'mw_tender_date' => date('Y-m-d',strtotime($w_t_date)),
							'mw_tender_mode' => $w_t_mode,
							'mw_nit_no' => trim($w_nitno),
							//'mw_nit_doc' => $up_nit,
							'mw_tender_mature' => $w_t_mature,
							'mw_order_issue_date' => date('Y-m-d',strtotime($w_order_date)),
							'mw_award_cost' => trim($w_award_amount),
							//'mw_workorder_doc' => $up_worder,
							'mw_agency_name' => trim($w_agency_name),
							'mw_agency_mobile' => $w_agency_mobile,
							'mw_agency_gst' => trim($w_agency_gst),
							'mw_emd_amount' => trim($w_emd_amount),
							'mw_commence_date' => date('Y-m-d',strtotime($w_com_date)),
							'mw_tentative_date' => date('Y-m-d',strtotime($w_tent_date)),
							'mw_modifydate' => date('Y-m-d H:i:s'),
							'mw_modifyby' => $this->session->userdata['uid']
						);

						if($_FILES["w_nit_doc"]["name"] != ''){
							$row_array['mw_nit_doc'] = $up_nit;
						}
						if($_FILES["w_order_doc"]["name"] != ''){
							$row_array['mw_workorder_doc'] = $up_worder;
						}
	
						if($this->admin_m->addUpdateform_ofWork_inDB($row_array, $row2, $row3, $workid) == TRUE){
							$this->session->set_flashdata("success","Work is successfully updated.");
							redirect('admincontrol/panel/work_list','refresh');
						}else{
							$this->data['error'] = "There have some problem to Update DB, Try Again.";
						}
					}

				}else{
					
					$row_array = array(
						'mw_year' => $f_year,
						'mw_name' => trim($w_name),
						'mw_location' => trim($w_loc),
						'mw_fund_source' => $final_fund,
						'mw_sector' => $final_sector,
						'mw_tender_float' => $w_t_float,
						'mw_tender_amount' => NULL,
						'mw_tender_date' => NULL,
						'mw_tender_mode' => NULL,
						'mw_nit_no' => NULL,
						'mw_nit_doc' => NULL,
						'mw_tender_mature' => NULL,
						'mw_order_issue_date' => NULL,
						'mw_award_cost' => NULL,
						'mw_workorder_doc' => NULL,
						'mw_agency_name' => NULL,
						'mw_agency_mobile' => NULL,
						'mw_agency_gst' => NULL,
						'mw_emd_amount' => NULL,
						'mw_commence_date' => NULL,
						'mw_tentative_date' => NULL,
						'mw_modifydate' => date('Y-m-d H:i:s'),
						'mw_modifyby' => $this->session->userdata['uid']
					);

					if($this->admin_m->addUpdateform_ofWork_inDB($row_array, $row2, $row3, $workid) == TRUE){
						$this->session->set_flashdata("success","Work is successfully updated.");
						redirect('admincontrol/panel/work_list','refresh');
					}else{
						$this->data['error'] = "There have some problem to Update DB, Try Again.";
					}
				}
					
				
			}else{
				$this->data['error'] = validation_errors();
			}
		}
		$this->data['fund_list'] = $this->db->get_where('fund_source_tab',array('fs_status'=>1))->result();
		$this->data['sector_list'] = $this->db->get_where('work_sector_tab',array('ws_status'=>1))->result();
		$this->data['work_detail'] = $this->db->get_where('main_work_tab',array('mw_id'=>$workid,'mw_status'=>1))->row();
		$this->load->view('admin/work/edit_work_view', $this->data);

	}

	public function alocate_new_work(){
		if($_POST){
			$f_year = $this->input->post('f_year');
			$w_name = $this->input->post('w_name');
			$w_ae = $this->input->post('w_ae');
			$w_sae = $this->input->post('w_sae');
			
			$this->form_validation->set_rules('f_year', 'Financial Year', 'trim|required');
			$this->form_validation->set_rules('w_name', 'Name of Work', 'trim|required');
			$this->form_validation->set_rules('w_ae', 'Assistant Engineer', 'trim|required|is_natural');
			$this->form_validation->set_rules('w_sae', 'Sub Assistant Engineer', 'trim|required|is_natural');

			if($this->form_validation->run() == TRUE)
            {
				
				$row_array = array(
					'work_master_id' => $w_name,
					'work_se_id' => $w_ae,
					'work_ase_id' => $w_sae,
					'work_createdate' => date('Y-m-d H:i:s'),
					'work_createby' => $this->session->userdata['uid']
				);

				if($this->admin_m->addUpdateform_of_WorkAllocation_inDB($row_array) == TRUE){
					$this->session->set_flashdata("success","Work is successfully Allocated.");
					redirect('admincontrol/panel/alocate_work_list','refresh');
				}else{
					$this->data['error'] = "There have some problem to Update DB, Try Again.";
				}
					
			}else{
				$this->data['error'] = validation_errors();
			}
		}
		$this->data['yr_list'] = $this->db->distinct()->select('mw_year')->get('main_work_tab')->result();
		$this->data['ae_list'] = $this->db->get_where('user_views',array('u_type'=>8,'status'=>'1'))->result();
		$this->data['sae_list'] = $this->db->get_where('user_views',array('u_type'=>9,'status'=>'1'))->result();
		$this->load->view('admin/work/add_work_for_allocate_view', $this->data);
	}

	public function alocate_work_list(){
		$this->data['work_list'] = $this->admin_m->getAll_workAllocation_fromDB();
		$this->load->view('admin/work/work_allocation_list_view', $this->data);
	}

	public function get_work_against_fyear(){
		if($_POST){
			$f_year = $this->input->post('f_year');
			$msg = 0;
			if($f_year != ""){
				//$response_name = $this->db->get_where('main_work_tab',array('mw_year'=>$f_year, 'mw_tender_float'=>'Yes'))->result();
				$response_name = $this->admin_m->get_all_NewWork_for_Allocate($f_year);
				
				$work_set = '<option value="">---Select---</option>';
				
				if(count((array)$response_name) > 0){
					foreach($response_name as $work_names){
						$work_set = $work_set.'<option value="'.$work_names->mw_unique_id.'">'.$work_names->mw_name.'</option>';
					}
					echo json_encode(array('msg'=>1, 'work_set' => $work_set));
				}else{
					echo json_encode(array('msg'=>$msg));
				}
			}else{
				echo json_encode(array('msg'=>$msg));
			}
			exit;
		}else{
			redirect('default404');
		}
	}

	public function edit_work_allocation($workid = NULL){
		if($workid == NULL){
			redirect('admincontrol/panel/alocate_work_list');
		}
		if($_POST){
			$w_ae = $this->input->post('w_ae');
			$w_sae = $this->input->post('w_sae');
			
			$this->form_validation->set_rules('w_ae', 'Assistant Engineer', 'trim|required|is_natural');
			$this->form_validation->set_rules('w_sae', 'Sub Assistant Engineer', 'trim|required|is_natural');

			if($this->form_validation->run() == TRUE)
            {
				
				$row_array = array(
					'work_se_id' => $w_ae,
					'work_ase_id' => $w_sae,
					'work_modifydate' => date('Y-m-d H:i:s'),
					'work_modifyby' => $this->session->userdata['uid']
				);

				if($this->admin_m->addUpdateform_of_WorkAllocation_inDB($row_array, $workid) == TRUE){
					$this->session->set_flashdata("success","Allocation of Work is updated successfully.");
					redirect('admincontrol/panel/alocate_work_list','refresh');
				}else{
					$this->data['error'] = "There have some problem to Update DB, Try Again.";
				}
					
			}else{
				$this->data['error'] = validation_errors();
			}
		}
		
		$this->data['ae_list'] = $this->db->get_where('user_views',array('u_type'=>8,'status'=>'1'))->result();
		$this->data['sae_list'] = $this->db->get_where('user_views',array('u_type'=>9,'status'=>'1'))->result();
		$this->data['work_detail'] = $this->admin_m->getAll_workAllocation_fromDB($workid);
		$this->load->view('admin/work/edit_work_allocate_view', $this->data);
	}

	public function allocaded_work_progress_list(){
		$this->data['work_list'] = $this->admin_m->getAll_workAllocation_fromDB_byProgress();
		$this->load->view('admin/work/work_allocation_list_progress_view', $this->data);
	}

	public function visitsite_workprogress($wuid = NULL){
		if($wuid == NULL){
			redirect('admincontrol/panel/allocaded_work_progress_list');
		}
		if($_POST){
			$v_count = $this->input->post('v_count');
			$v_date = $this->input->post('v_date');
			$p_progress = $this->input->post('p_progress');
			$c_date = $this->input->post('c_date');
			$w_descrip = $this->input->post('w_descrip');
			
			$this->form_validation->set_rules('v_count', 'Visit Count', 'trim|required');
			$this->form_validation->set_rules('v_date', 'Visiting Date', 'trim|required');
			$this->form_validation->set_rules('p_progress', 'Physical Progress', 'trim|required');
			$this->form_validation->set_rules('w_descrip', 'Description of Work Progress', 'trim');
			if($p_progress == 100.00){
				$this->form_validation->set_rules('c_date', 'Completion Date', 'trim|required');
			}
			
			if($this->form_validation->run() == TRUE)
            {
				$this->load->helper(array('form', 'url'));
				$this->load->library('upload');
				
				//$config['upload_path'] = realpath('upload_file/proj_photo/');
				$config['upload_path'] = 'upload_file/proj_photo/';
				$config['allowed_types'] = 'jpg|png|jpeg|JPEG|JPG|PNG';
				$config['overwrite'] = FALSE;
				$config['remove_spaces'] = TRUE;
				$config['max_size'] = '10000';
				
				$this->load->library('upload', $config);
				$this->upload->initialize($config);
				
				$pic_cnt = 1;
				$pic_error = '';
				$pic_array = array();
				/* File Uploads */
				if(isset($_FILES["proj_pics"]["name"])){
					for($count = 0; $count<count($_FILES["proj_pics"]["name"]); $count++)
					{
						$_FILES["file"]["name"] = $_FILES["proj_pics"]["name"][$count];
						$_FILES["file"]["type"] = $_FILES["proj_pics"]["type"][$count];
						$_FILES["file"]["tmp_name"] = $_FILES["proj_pics"]["tmp_name"][$count];
						$_FILES["file"]["error"] = $_FILES["proj_pics"]["error"][$count];
						$_FILES["file"]["size"] = $_FILES["proj_pics"]["size"][$count];
						if($this->upload->do_upload('file'))
						{
							$upload_data = $this->upload->data();
							$pic_array[] = $upload_data['file_name'];
						}else{
							$pic_cnt = 0;
							$pic_error = $pic_error . $_FILES["file"]["name"] ." - ". $this->upload->display_errors() . "<br/>";
						}
					}
				}else{
					$pic_cnt = 0;
					$pic_error = $pic_error . "No Photograph found for Upload. Check Again.";
				}
				/* File Uploads */
				if($pic_cnt == 1){
					//print_r($pic_array);exit;
					if($p_progress == 100.00){$c_date = date('Y-m-d',strtotime($c_date));}else{$c_date = NULL;}
					$row_array = array(
						'wp_masterid' => $wuid,
						'wp_progstatus' => $p_progress,
						'wp_comment' => $w_descrip,
						'wp_visitno' => $v_count,
						'wp_visit_date' => date('Y-m-d',strtotime($v_date)),
						'wp_completion' => $c_date,
						'wp_createdate' => date('Y-m-d H:i:s'),
						'wp_createby' => $this->session->userdata['uid']
					);
					$row2 = array(
						'mw_progress_stat' => $p_progress
					);

					if($this->admin_m->addUpdate_WorkProgress_inDB($row_array, $row2, $pic_array, $wuid) == TRUE){
						$this->session->set_flashdata("success","Work's Progress is successfully Inserted.");
						redirect('admincontrol/panel/allocaded_work_progress_list','refresh');
					}else{
						$this->data['error'] = "There have some problem to Update DB, Try Again.";
					}
				}else{
					$this->data['error'] = $pic_error;
				}
					
			}else{
				$this->data['error'] = validation_errors();
			}
		}
		$this->data['work_detail'] = $wdetail = $this->admin_m->getAll_workAllocation_fromDB_byProgress($wuid);
		if(count((array)$wdetail) == 0){
			redirect('admincontrol/panel/allocaded_work_progress_list');
		}elseif($wdetail->mw_progress_stat == 100.00){
			redirect('admincontrol/panel/allocaded_work_progress_list');
		}
		$this->data['visit_count'] = $this->db->get_where('work_progress_tab',array('wp_masterid'=>$wuid))->num_rows();
		$this->load->view('admin/work/add_work_progress_view', $this->data);
	}

	public function submit_bill_afterwork_progress($wuid = NULL){
		if($wuid == NULL){
			redirect('admincontrol/panel/allocaded_work_progress_list');
		}
		if($_POST){
			$b_final = $this->input->post('b_final');
			$b_count = $this->input->post('b_count');
			$b_submission = $this->input->post('b_submission');
			$b_amount = $this->input->post('b_amount');
			$b_amt_release = $this->input->post('b_amt_release');
			$b_claim_emd = $this->input->post('b_claim_emd');
			$b_release_emd = $this->input->post('b_release_emd');
			$b_revised = $this->input->post('b_revised');
			$b_add_amount = $this->input->post('b_add_amount');
			
			$this->form_validation->set_rules('b_final', 'Is it Final Bill', 'trim|required');
			$this->form_validation->set_rules('b_count', 'Bill Count', 'trim|required');
			$this->form_validation->set_rules('b_submission', 'Date of submission', 'trim|required');
			$this->form_validation->set_rules('b_amount', 'Amount Released', 'trim|required');
			$this->form_validation->set_rules('b_amt_release', 'Released Date', 'trim|required');
			if($b_final == "Yes"){
				$this->form_validation->set_rules('b_claim_emd', 'Date of claim of EMD', 'trim|required');
				$this->form_validation->set_rules('b_release_emd', 'Date of release of EMD', 'trim|required');
				$this->form_validation->set_rules('b_revised', 'Any revised Estimate', 'trim|required');
				if($b_revised == "Yes"){
					$this->form_validation->set_rules('b_add_amount', 'Additional Amount', 'trim|required');
				}
			}
			
			if($this->form_validation->run() == TRUE)
            {
				$finalcount = 0;
				if($b_final == "Yes"){
					//$b_count = NULL;
					$finalcount = 1;
					$b_claim_emd = date('Y-m-d',strtotime($b_claim_emd));
					$b_release_emd = date('Y-m-d',strtotime($b_release_emd));
					if($b_revised != "Yes"){
						$b_add_amount = NULL;
					}
				}else{
					$b_claim_emd = $b_release_emd = $b_revised = $b_add_amount = NULL;
				}
				
					$row_array = array(
						'wb_master_id' => $wuid,
						'wb_bill_final' => $b_final,
						'wb_ra_no' => $b_count,
						'wb_submission' => date('Y-m-d',strtotime($b_submission)),
						'wb_amount' => $b_amount,
						'wb_release' => date('Y-m-d',strtotime($b_amt_release)),
						'wb_claim_emd' => $b_claim_emd,
						'wb_release_emd' => $b_release_emd,
						'wb_revised_estimate' => $b_revised,
						'wb_additional_amt' => $b_add_amount,
						'wb_createdate' => date('Y-m-d H:i:s'),
						'wb_createby' => $this->session->userdata['uid']
					);
					$row2 = array(
						'mw_finalbill_put' => $finalcount
					);

					if($this->admin_m->addUpdate_WorkProgress_Bill_inDB($row_array, $row2, $wuid) == TRUE){
						$this->session->set_flashdata("success","Bill of Work is successfully Submitted.");
						redirect('admincontrol/panel/allocaded_work_progress_list','refresh');
					}else{
						$this->data['error'] = "There have some problem to Update DB, Try Again.";
					}
				
					
			}else{
				$this->data['error'] = validation_errors();
			}
		}
		$this->data['work_detail'] = $wdetail = $this->admin_m->getAll_workAllocation_fromDB_byProgress($wuid);
		if(count((array)$wdetail) == 0){
			redirect('admincontrol/panel/allocaded_work_progress_list');
		}elseif($wdetail->mw_finalbill_put == 1){
			redirect('admincontrol/panel/allocaded_work_progress_list');
		}
		$this->data['bill_count'] = $this->db->get_where('work_bill_tab',array('wb_master_id'=>$wuid))->num_rows();
		$this->load->view('admin/work/add_work_bill_view', $this->data);
	}

	public function work_progress_detail_list($wuid = NULL){
		if($wuid == NULL){
			redirect('admincontrol/panel/allocaded_work_progress_list');
		}
		$this->data['work_prog_list'] = $this->admin_m->getAll_workProgress_fromDB_byVisit($wuid);
		$this->data['work_detail'] = $wdetail = $this->admin_m->getAll_workAllocation_fromDB_byProgress($wuid);
		if(count((array)$wdetail) == 0){
			redirect('admincontrol/panel/allocaded_work_progress_list');
		}
		$this->load->view('admin/work/work_progress_list_view', $this->data);
	}

	public function work_progress_bill_detail_list($wuid = NULL){
		if($wuid == NULL){
			redirect('admincontrol/panel/allocaded_work_progress_list');
		}
		$this->data['work_bill_list'] = $this->admin_m->getAll_workProgress_Bill_fromDB($wuid);
		$this->data['work_detail'] = $wdetail = $this->admin_m->getAll_workAllocation_fromDB_byProgress($wuid);
		if(count((array)$wdetail) == 0){
			redirect('admincontrol/panel/allocaded_work_progress_list');
		}
		$this->load->view('admin/work/work_bill_list_view', $this->data);
	}

	public function work_progress_photographs($pid = NULL){
		if($pid == NULL){
			redirect('admincontrol/panel/work_progress_detail_list');
		}
		$this->data['prog_detail'] = $pdetail = $this->db->get_where('work_progress_tab',array('wp_id'=>$pid))->row();
		if(count((array)$pdetail) == 0){
			redirect('admincontrol/panel/work_progress_detail_list');
		}
		$this->data['work_detail'] = $this->admin_m->getAll_workAllocation_fromDB_byProgress($pdetail->wp_masterid);
		$this->data['photo_list'] = $this->db->get_where('work_prog_pictures',array('wpp_master_progrid'=>$pid))->result();
		$this->load->view('admin/work/progress_photo_list_view', $this->data);
	}



































	
	public function view_application($appid = NULL){
		if($appid == NULL){
			redirect('admincontrol/panel/application_list');
		}
		$this->data['doc_detail'] = $this->db->get_where('full_application',array('app_id'=>$appid))->row();
		if(count((array)$this->data['doc_detail']) == 0){
			redirect('admincontrol/panel/application_list');
		}
		if($this->data['doc_detail']->appli_status == 1){
			$row_array = array(
				'appli_status' => 2,
				'appli_modifydate' => date('Y-m-d H:i:s')
			);
			if($this->main_m->addform_against_epass_covid($row_array, $appid) == FALSE){
				redirect('admincontrol/panel/application_list');
			}
		}
		$this->load->view('admin/document_detail_view', $this->data);
	}
	
	public function approve_application($appid = NULL){
		if($appid == NULL){
			redirect('admincontrol/panel/application_list');
		}
		$this->data['doc_detail'] = $this->db->get_where('full_application',array('app_id'=>$appid))->row();
		if(count((array)$this->data['doc_detail']) == 0){
			redirect('admincontrol/panel/application_list');
		}
		$this->data['fwd_list'] = $this->db->order_by('cf_id','ASC')->get_where('copy_fw_tab')->result();
		/*$setid = 61;
		$invID = str_pad($setid, 4, '0', STR_PAD_LEFT);
		echo $invID;*/
		$this->load->view('admin/document_approval_view', $this->data);
	}

	public function application_form_approval(){
		if($_POST){
			$gen_id = $this->input->post('gen_id');
			$w_number = $this->input->post('w_number');
			$copy_set = $this->input->post('copy_set');
			$msg = 0;
			if(count((array)$copy_set)>0 && $gen_id != "" && $w_number != ""){
				$copystring = implode(",",$copy_set);
				$result_no = $this->main_m->get_highest_memono_application();
				if($result_no->appli_m_no != ""){
					$gennumber = $result_no->appli_m_no + 1;
				}else{
					$gennumber = 1;
				}
				$gen_Set = str_pad($gennumber, 4, '0', STR_PAD_LEFT).'/ZP/'.date('Y');
				$ap_detail = $this->db->get_where('full_application',array('app_id'=>$gen_id))->row();
				$mailarray = array();
				$mailarray[] = $ap_detail->sub_div_email;
				$mailarray[] = $ap_detail->block_email;
				$mailarray[] = $ap_detail->ps_email;
				$mailarray[] = $ap_detail->gp_email;
				$row_array = array(
					'appli_worker' => $w_number,
					'appli_modifydate' => date('Y-m-d H:i:s'),
					'appli_m_no' => $gennumber,
					'appli_memo_no' => $gen_Set,
					'appli_memo_date' => date('Y-m-d'),
					'appli_copy_fwd' => $copystring,
					'appli_status' => 3
				);
				if($this->main_m->addform_against_epass_covid($row_array, $gen_id) == TRUE){
					$profile_email = $ap_detail->appli_email;
					$e_sub = "Permission Approval - Bankura";
					$e_msg = '<h2>Welcome to Portal for Permission to resume works in Bankura District<br/>(During Lockdown period of COVID-19)</h2><p style="font-size:18px;">Dear '.$ap_detail->appli_name.',<br/>Your Permission is Approved Successfully.<br/>Your Application Number :- <strong>'.$ap_detail->app_ucode.'</strong></p><br/><br/>
					<p style="font-size:18px;">Please check the Below Link for your Approval Document -<br/>
					http://bankuradistrict.in/main/print_final_permission_sheet/'.$ap_detail->app_ucode.'</p>
					<br/><br/><br/>
					<p style="font-size:16px;">*For any queries please contact the District Admin.</p>';
					$this->sendSMTPEmail($profile_email, $e_sub, $e_msg, $mailarray);

					echo json_encode(array('msg'=>1));
				}else{
					echo json_encode(array('msg'=>$msg, 'e_msg'=>'There have some probelm to Update DB, Try Again'));
				}
			}else{
				echo json_encode(array('msg'=>$msg, 'e_msg'=>'Check all fields properly, Try Again'));
			}
			exit;
		}else{
			redirect('admincontrol/panel/application_list');
		}	
	}




	public function reject_application($appid = NULL){
		if($appid == NULL){
			redirect('admincontrol/panel/application_list');
		}
		if($_POST){
			$reject_id = $this->input->post('reject_id');
			$reject_details = $this->input->post('reject_details');
			
			$this->form_validation->set_rules('reject_id', 'Reject ID', 'trim|required');
			$this->form_validation->set_rules('reject_details', 'Reject Details', 'trim|required');

			if($this->form_validation->run() == TRUE)
            {
				$row_array = array(
					'appli_status' => 4,
					'appli_memo_no' => NULL,
					'appli_memo_date' => NULL,
					'appli_copy_fwd' => NULL,
					'appli_admin_msg' => trim($reject_details),
					'appli_modifydate' => date('Y-m-d H:i:s')
				);
				if($this->main_m->addform_against_epass_covid($row_array, $reject_id) == TRUE){
					$this->session->set_flashdata("success","Application is Rejected successfully.");
					redirect('admincontrol/panel/application_list','refresh');
				}else{
					$this->data['error'] = "There have some problem to Update DB, Try Again.";
				}
			}
		}
		$this->data['doc_detail'] = $this->db->get_where('full_application',array('app_id'=>$appid))->row();
		if(count((array)$this->data['doc_detail']) == 0){
			redirect('admincontrol/panel/application_list');
		}
		$this->load->view('admin/reject_view', $this->data);
	}

	public function cpy_fwd_list(){
		$this->data['fwd_list'] = $this->db->order_by('cf_id','DESC')->get_where('copy_fw_tab')->result();
		$this->load->view('admin/copy_list_view', $this->data);
	}

	public function add_cpy_fwd(){
		if($_POST){
			$cf_details = $this->input->post('cf_details');
			
			$this->form_validation->set_rules('cf_details', 'Terms Title', 'trim|required');

			if($this->form_validation->run() == TRUE)
            {	
				$row_array = array(
					'cf_title' => trim($cf_details),
					'cf_createdate' => date('Y-m-d H:i:s')
				);

				if($this->main_m->addupdate_copyfrw_form($row_array) == TRUE){
					$this->session->set_flashdata("success","Terms & Conditions Title successfully inserted.");
					redirect('admincontrol/panel/add_cpy_fwd','refresh');
				}else{
					$this->data['error'] = "There have some problem to Update DB, Try Again.";
				}
			}
		}
		$this->load->view('admin/add_copy_view', $this->data);
	}

	public function lock_cpy_fwd($cid = NULL){
		if($cid == NULL){
			redirect('admincontrol/panel/cpy_fwd_list');
		}
		$row_array = array(
			'cf_status' => 0
		);

		if($this->main_m->addupdate_copyfrw_form($row_array, $cid) == TRUE){
			$this->session->set_flashdata("success","Terms & Conditions is Locked successfully.");
			redirect('admincontrol/panel/cpy_fwd_list','refresh');
		}else{
			$this->session->set_flashdata("e_error","There have some problem to Update DB, Try Again.");
			redirect('admincontrol/panel/cpy_fwd_list','refresh');
		}
	}

	public function unlock_cpy_fwd($cid = NULL){
		if($cid == NULL){
			redirect('admincontrol/panel/cpy_fwd_list');
		}
		$row_array = array(
			'cf_status' => 1
		);

		if($this->main_m->addupdate_copyfrw_form($row_array, $cid) == TRUE){
			$this->session->set_flashdata("success","Terms & Conditions is Unlocked successfully.");
			redirect('admincontrol/panel/cpy_fwd_list','refresh');
		}else{
			$this->session->set_flashdata("e_error","There have some problem to Update DB, Try Again.");
			redirect('admincontrol/panel/cpy_fwd_list','refresh');
		}
	}

	public function delete_cpy_fwd($cid = NULL){
		if($cid == NULL){
			redirect('admincontrol/panel/cpy_fwd_list');
		}
		if($this->db->delete('copy_fw_tab',array('cf_id' => $cid))){
			$this->session->set_flashdata("success","Terms & Conditions is deleted successfully.");
			redirect('admincontrol/panel/cpy_fwd_list','refresh');
		}else{
			$this->session->set_flashdata("e_error","There is some Problem. Please try again.");
			redirect('admincontrol/panel/cpy_fwd_list','refresh');
		}	 
	}



	public function print_application($app_no = NULL)
	{
		//print_r($this->session->userdata('uid'));exit;
		if($app_no == "" || $app_no == NULL){
			redirect('default404');
		}
		
		$app_details = $this->db->get_where('full_application',array('app_ucode'=>$app_no))->row();
		
		if(count((array)$app_details) == 0){
			redirect('default404');
		}
		//echo "hi";exit;
		$this->load->helper("tcpdf_helper");
		tcpdf();
		$obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$obj_pdf->SetCreator(PDF_CREATOR);
		$title = $app_no;
		$obj_pdf->SetTitle($title);
		
		$obj_pdf->SetPrintHeader(false);
		$obj_pdf->SetPrintFooter(false);
		
		//$obj_pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $title, PDF_HEADER_STRING);
		//$obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		//$obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		//$obj_pdf->SetDefaultMonospacedFont('helvetica');
		$obj_pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		$obj_pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_RIGHT);
		$obj_pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		//$obj_pdf->SetFont('helvetica', '', 9);
		//$obj_pdf->setFontSubsetting(false);
		$obj_pdf->AddPage();
		//ob_start();
		    // we can have any view part here like HTML, PHP etc


		$my_html = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\">
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />
<title>".$title."</title>
</head>
<body>
<div class=\"header\">";	
$my_html = $my_html."<table style=\"width: 100%\" style=\"font-size: 16px;\">
		<tr>
			<td colspan=\"2\" style=\"width:100%;\"><div align=\"center\" style=\"font-size:20px;\"><img src=\"".base_url()."images/wb_logo.png\" /><br/>GOVERNMENT OF WEST BENGAL<br/>
			OFFICE OF THE DISTRICT MAGISTRATE<br/>
			BANKURA</div></td>
		</tr>
  <tr><td colspan=\"2\">&nbsp;</td></tr>
  <tr>
    <td colspan=\"2\" align=\"center\"><h3 style=\"text-decoration: underline;\">Application for Permission</h3></td>
  </tr>
  <tr><td colspan=\"2\">&nbsp;</td></tr>
  <tr><td colspan=\"2\">&nbsp;</td></tr>
  <tr>
    <td align=\"left\">&nbsp;</td>
    <td align=\"right\">Date:- <strong>".date('d/m/Y',strtotime($app_details->appli_createdate))."</strong></td>
  </tr>
  <tr>
    <td colspan=\"2\"><br/><br/>
    <table width=\"100%\" style=\"font-size: 16px;\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\">
    	<tr>
            <td width=\"25%\" bgcolor=\"#CCCCCC\"><b>Application Number</b></td>
            <td colspan=\"3\">".$app_details->app_ucode."</td>
        </tr>
        <tr>
            <td bgcolor=\"#CCCCCC\"><b>Applicant/Agency Name</b></td>
            <td colspan=\"3\">".$app_details->appli_name."</td>
		</tr>
		<tr>
            <td bgcolor=\"#CCCCCC\"><b>Applicant/Agency Address</b></td>
            <td colspan=\"3\">".$app_details->appli_address."</td>
        </tr>
        <tr>
            <td bgcolor=\"#CCCCCC\"><b>Applicant/Agency Email</b></td>
            <td>".$app_details->appli_email."</td>
            <td bgcolor=\"#CCCCCC\"><b>Applicant/Agency Mobile</b></td>
            <td>".$app_details->appli_mobile."</td>
        </tr>
        <tr>
            <td bgcolor=\"#CCCCCC\"><b>Work Name</b></td>
            <td colspan=\"3\">".$app_details->appli_work."</td>
		</tr>
		<tr>
            <td bgcolor=\"#CCCCCC\"><b>Work Location</b></td>
            <td colspan=\"3\">".$app_details->appli_work_loc."</td>
		</tr>
		<tr>
            <td bgcolor=\"#CCCCCC\"><b>Sub Division</b></td>
            <td>".$app_details->sub_div_name."</td>
            <td bgcolor=\"#CCCCCC\"><b>Block</b></td>
            <td>".$app_details->block_name."</td>
		</tr>
		<tr>
            <td bgcolor=\"#CCCCCC\"><b>GP Name</b></td>
            <td>".$app_details->gp_name."</td>
            <td bgcolor=\"#CCCCCC\"><b>Police Station</b></td>
            <td>".$app_details->ps_name."</td>
        </tr>
		<tr>
            <td bgcolor=\"#CCCCCC\"><b>Number of Workers</b></td>
            <td colspan=\"3\">".$app_details->appli_worker."</td>
		</tr>
		<tr>
            <td bgcolor=\"#CCCCCC\"><b>Workers are local/ from outside</b></td>
            <td colspan=\"3\">".$app_details->appli_worker_loc."</td>
		</tr>
		</table><br/><br/><br/><br/><br/><br/>
    </td>
  </tr>
<tr>
    <td>&nbsp;</td>
    <td align=\"center\"><div align=\"center\" style=\"border:1px solid #000;\"><br/>This document has been digitally generated. No Signature is required.<br/>GOVERNMENT OF WEST BENGAL<br/></div></td>
  </tr>
  <tr>
			<td colspan=\"2\">&nbsp;</td>
		</tr>
</table>
</div>
</body>
</html>";
		
		$content = $my_html; //ob_get_contents();
		//ob_end_clean();
		$obj_pdf->writeHTML($content, true, false, true, false, '');
		$obj_pdf->Output($app_no.'.pdf', 'I');
		//$obj_pdf->Output(FCPATH.'/pdf/'.$advice_detail->advice_id.'.pdf', 'D');
		
		//$this->session->set_flashdata("success","Report is Generated Successfully");
		
	}


	public function print_final_application($app_no = NULL)
	{
		//print_r($this->session->userdata('uid'));exit;
		if($app_no == "" || $app_no == NULL){
			redirect('default404');
		}
		
		$app_details = $this->db->get_where('full_application',array('app_ucode'=>$app_no))->row();
		
		if(count((array)$app_details) == 0){
			redirect('default404');
		}
		if($app_details->appli_status != 3){
			redirect('default404');
		}
		$copy_arr = explode(",", $app_details->appli_copy_fwd);
		$copy_set = $this->main_m->get_all_conditions_copys_DB($app_details->appli_copy_fwd);
		if(count((array)$copy_set) == 0){
			redirect('default404');
		}
		//echo "hi";exit;
		$this->load->helper("tcpdf_helper");
		tcpdf();
		$obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$obj_pdf->SetCreator(PDF_CREATOR);
		$title = $app_no;
		$obj_pdf->SetTitle($title);
		
		$obj_pdf->SetPrintHeader(false);
		$obj_pdf->SetPrintFooter(false);
		
		//$obj_pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $title, PDF_HEADER_STRING);
		//$obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		//$obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		//$obj_pdf->SetDefaultMonospacedFont('helvetica');
		$obj_pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		$obj_pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_RIGHT);
		$obj_pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		//$obj_pdf->SetFont('helvetica', '', 9);
		//$obj_pdf->setFontSubsetting(false);
		$obj_pdf->AddPage();
		//ob_start();
		    // we can have any view part here like HTML, PHP etc


		$my_html = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\">
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />
<title>".$title."</title>
</head>
<body>
<div class=\"header\">";	
$my_html = $my_html."<table style=\"width: 100%\" style=\"font-size: 20px;\">
		<tr>
			<td colspan=\"2\" style=\"width:100%;\"><div align=\"center\" style=\"font-size:22px;\"><img src=\"".base_url()."images/wb_logo.png\" /><br/>GOVERNMENT OF WEST BENGAL<br/>
			OFFICE OF THE DISTRICT MAGISTRATE<br/>
			BANKURA</div></td>
		</tr>
  <tr><td colspan=\"2\">&nbsp;</td></tr>
  <tr><td colspan=\"2\"><hr/>
	<table width=\"100%\" style=\"font-size: 20px;\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">
		<tr><td><b>Ph. No.</b> Office: 03242-255450</td>
		<td align=\"right\"><b>E-mail:</b> aeozp-bnk@nic.in, aeozp.bnk@gmail.com</td></tr>
	</table>
	<hr/></td></tr>
  <tr>
    <td align=\"left\"><b>Memo No.</b> ".$app_details->appli_memo_no."</td>
    <td align=\"right\">Date:- <strong>".date('d/m/Y',strtotime($app_details->appli_memo_date))."</strong></td>
  </tr>
  <tr>
    <td colspan=\"2\"><br/><br/>
    <table width=\"100%\" style=\"font-size: 20px;\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">
    	<tr>
			<td><b>To,<br/>
			".$app_details->appli_name."<br/>
			".$app_details->appli_mobile."<br/>
			".$app_details->appli_address."<br/>
			".$app_details->app_ucode."<br/></b>
			</td>
            <td colspan=\"2\">&nbsp;</td>
		</tr>
		<tr>
			<td colspan=\"3\" align=\"center\"><b>Sub:</b> Grant of Permission in reference to your application for the work <b>".$app_details->appli_work."</b> and undertaking dated ".date('d/m/Y',strtotime($app_details->appli_createdate))."<br/></td>
		</tr>
		<tr>
			<td colspan=\"3\">
			<p align=\"justify\"><b>Sir,</b><br/>
			In reference to your application for starting operations during the current lockdown period, permission is granted for <b>".$app_details->appli_worker."</b> nos of employees subject to fulfillment of conditions as mentioned in guideline issued vide Memo No. 652/WBSRDA/IE-5/2017 Dated 22/04/2020 issued by P&RD Deptt., Govt. of West Bengal and fulfilling all other statutory obligations as applicable.</p>
			<p>The following conditions are reiterated.</p>
			</td>
		</tr>
		<tr>
			<td colspan=\"3\">";
		foreach($copy_set as $keyset=>$condis){
			$my_html = $my_html.($keyset + 1).". ".$condis->cf_title."<br/>";
		}
		$my_html = $my_html."</td>
		</tr>
		<tr>
			<td colspan=\"3\">
			<p align=\"justify\">Please note that you are liable to be prosecuted as given in above mentioned memo for submitting incorrect
			information or for violation of any lockdown measures.</p>
			</td>
		</tr>
		</table><br/><br/>
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
	<td align=\"center\"><div align=\"center\"><img src=\"".base_url()."images/signature.jpg\" />
	<br/>Additional District Magistrate (ZP), Bankura</div></td>
  </tr>
  <tr>
	<td colspan=\"2\">&nbsp;</td>
  </tr>
  <tr>
    <td align=\"left\"><b>Memo No.</b> ".$app_details->appli_memo_no."</td>
	<td align=\"right\">Date:- <strong>".date('d/m/Y',strtotime($app_details->appli_memo_date))."</strong></td>
  </tr>
  <tr>
	<td colspan=\"2\">&nbsp;</td>
  </tr>
  <tr>
	<td colspan=\"2\"><p>Copy forwarded for kind information and necessary action to the -</p></td>
  </tr>
  <tr>
	<td colspan=\"2\">
	<p>1. Sub Divisional Officer, ".$app_details->sub_div_name." Sub Division<br/>
	2. SDPO, ".$app_details->sub_div_name." Sub Division<br/>
	3. Block Development Officer, ".$app_details->block_name." Development Block<br/>
	4. IC/OC, ".$app_details->ps_name." Police Station<br/>
	5. Pradhan, ".$app_details->gp_name." Gram Panchayat
	</p></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
	<td align=\"center\"><div align=\"center\"><img src=\"".base_url()."images/signature.jpg\" />
	<br/>Additional District Magistrate (ZP), Bankura</div></td>
  </tr>
</table>
</div>
</body>
</html>";
		
		$content = $my_html; //ob_get_contents();
		//ob_end_clean();
		$obj_pdf->writeHTML($content, true, false, true, false, '');
		$obj_pdf->Output($app_no.'.pdf', 'I');
		//$obj_pdf->Output(FCPATH.'/pdf/'.$advice_detail->advice_id.'.pdf', 'D');
		
		//$this->session->set_flashdata("success","Report is Generated Successfully");
		
	}










    public function lock_document($doc_id = NULL){
		if($doc_id == NULL){
			redirect('admincontrol/panel/document_list');
		}
		$row_array = array(
			'f_status' => '0',
			'file_modifydate' => date('Y-m-d H:i:s')
		);
		if($this->admin_m->common_Updation_in_DB($row_array, 'content_files_tab', 'file_id', $doc_id) == TRUE){
			$this->session->set_flashdata("success","Document is locked successfully.");
			redirect('admincontrol/panel/document_list','refresh');
		}else{
			$this->session->set_flashdata("e_error","There is some Problem. Please try again.");
			redirect('admincontrol/panel/document_list','refresh');
		}
		 
	}
	
	public function unlock_document($doc_id = NULL){
		if($doc_id == NULL){
			redirect('admincontrol/panel/document_list');
		}
		$row_array = array(
			'f_status' => '1',
			'file_modifydate' => date('Y-m-d H:i:s')
		);
		if($this->admin_m->common_Updation_in_DB($row_array, 'content_files_tab', 'file_id', $doc_id) == TRUE){
			$this->session->set_flashdata("success","Document is unlocked successfully.");
			redirect('admincontrol/panel/document_list','refresh');
		}else{
			$this->session->set_flashdata("e_error","There is some Problem. Please try again.");
			redirect('admincontrol/panel/document_list','refresh');
		}
		 
	}
    
    public function edit_document($doc_id = NULL){
		if($doc_id == NULL){
			redirect('admincontrol/panel/document_list');
		}
		$this->data['section_list'] = $this->db->get_where('section_tab',array('section_status'=>1))->result();
		$this->data['doc_detail'] = $this->db->get_where('content_files_tab',array('file_id'=> $doc_id))->row();
		$this->load->view('admin/edit_new_document_view', $this->data);
	}
    
    public function edit_new_document(){
		if($_POST){
			//form_data.append('html_file',html_file[0]);
			//form_data.append('pdf_file',pdf_file[0]);
			$docid = $this->input->post('docid');
			$section_type = $this->input->post('section_type');
			$voucher_date = $this->input->post('voucher_date');
			$voucher_no = $this->input->post('voucher_no');
			$voucher_year = $this->input->post('voucher_year');
			$party_name = $this->input->post('party_name');
			$file_details = $this->input->post('file_details');
			$msg = 0;
			//print_r($_FILES["html_file"]["name"]);
			//print_r($_FILES["pdf_file"]["name"]);
			
			$this->form_validation->set_rules('docid', 'Document ID', 'trim|required|xss_clean');
			$this->form_validation->set_rules('section_type', 'Section Type', 'trim|required|xss_clean');
			$this->form_validation->set_rules('voucher_date', 'Voucher Date', 'trim|required|xss_clean');
			$this->form_validation->set_rules('voucher_no', 'Voucher Number', 'trim|required|xss_clean');
			$this->form_validation->set_rules('voucher_year', 'Voucher Year', 'trim|required|is_natural_no_zero|exact_length[4]|xss_clean');
			$this->form_validation->set_rules('party_name', 'Party Name', 'trim|required|xss_clean');
			$this->form_validation->set_rules('file_details', 'File Details', 'trim|xss_clean');
			
			if($this->form_validation->run() == TRUE){
				
				$row_array = array(
					'file_section' => $section_type,
					'file_date' => date('Y-m-d',strtotime($voucher_date)),
					'file_year' => $voucher_year,
					'file_voucher_no' => $voucher_no,
					'file_party_name' => $party_name,
					'file_title' => $file_details,
					'file_modifydate' => date('Y-m-d H:i:s')
				);
				if($this->admin_m->common_Updation_in_DB($row_array, 'content_files_tab', 'file_id', $docid) == TRUE){
					echo json_encode(array('msg'=>1, 'e_msg'=>'Document is updated Successfully.'));
				}else{
					echo json_encode(array('msg'=>$msg, 'e_msg'=>'There have some Database issue, Please Try Again.'));
				}
				
			}else{
				echo json_encode(array('msg'=>$msg, 'e_msg'=>validation_errors()));
			}
			exit;
		}
	}
    
    public function delete_document($doc_id = NULL){
		if($doc_id == NULL){
			redirect('admincontrol/panel/document_list');
		}
		$doc_detail = $this->db->get_where('content_files_tab',array('file_id'=> $doc_id))->row();
		if(count($doc_detail) > 0){
			
			if($this->db->delete('content_files_tab',array('file_id' => $doc_id))){
				unlink('src_files/'.$doc_detail->file_name);
				unlink('src_files/pdf_file/'.$doc_detail->file_document);
				$this->session->set_flashdata("success","Document is deleted successfully.");
				redirect('admincontrol/panel/document_list','refresh');
			}else{
				$this->session->set_flashdata("e_error","There is some Problem. Please try again.");
				redirect('admincontrol/panel/document_list','refresh');
			}
			
		}else{
			$this->session->set_flashdata("e_error","Document Not Found. Please check again.");
			redirect('admincontrol/panel/document_list','refresh');
		}
		 
	}
    
	public function section_list(){
		$this->data['section_list'] = $this->db->get_where('section_tab')->result();
		$this->load->view('admin/section_list_view', $this->data);
	}
	
	public function add_section(){
		$this->load->view('admin/add_section_view', $this->data);
	}
	
	public function add_new_section(){
		if($_POST){
			$san_name = $this->input->post('san_name');
			$san_detail = $this->input->post('san_detail');
			$msg = 0;
			
			$this->form_validation->set_rules('san_name', 'Section Number', 'trim|required|is_unique[section_tab.section_name]|xss_clean');
			$this->form_validation->set_rules('san_detail', 'Section Details', 'trim|xss_clean');
			
			if($this->form_validation->run() == TRUE){
				
				$san_name = trim($san_name);
				$san_uri = strtolower(str_replace(" ","_",$san_name));
				$row_array = array(
					'section_name' => $san_name,
					'section_uri' => $san_uri,
					'section_details' => $san_detail,
					'section_createdate' => date('Y-m-d H:i:s')
				);
				if($this->admin_m->common_Insertion_in_DB($row_array, 'section_tab') == TRUE){
					echo json_encode(array('msg'=>1, 'e_msg'=>'New Section is created Successfully.'));
				}else{
					echo json_encode(array('msg'=>$msg, 'e_msg'=>'There have some Database issue, Please Try Again.'));
				}
				
			}else{
				echo json_encode(array('msg'=>$msg, 'e_msg'=>validation_errors()));
			}
			exit;
		}
	}
	
	public function lock_section($sec_id = NULL){
		if($sec_id == NULL){
			redirect('admincontrol/panel/section_list');
		}
		$row_array = array(
			'section_status' => 0,
			'section_modifydate' => date('Y-m-d H:i:s')
		);
		if($this->admin_m->common_Updation_in_DB($row_array, 'section_tab', 'section_id', $sec_id) == TRUE){
			$this->session->set_flashdata("success","Section is locked successfully.");
			redirect('admincontrol/panel/section_list','refresh');
		}else{
			$this->session->set_flashdata("e_error","There is some Problem. Please try again.");
			redirect('admincontrol/panel/section_list','refresh');
		}
		 
	}
	
	public function unlock_section($sec_id = NULL){
		if($sec_id == NULL){
			redirect('admincontrol/panel/section_list');
		}
		$row_array = array(
			'section_status' => 1,
			'section_modifydate' => date('Y-m-d H:i:s')
		);
		if($this->admin_m->common_Updation_in_DB($row_array, 'section_tab', 'section_id', $sec_id) == TRUE){
			$this->session->set_flashdata("success","Section is unlocked successfully.");
			redirect('admincontrol/panel/section_list','refresh');
		}else{
			$this->session->set_flashdata("e_error","There is some Problem. Please try again.");
			redirect('admincontrol/panel/section_list','refresh');
		}
		 
	}
	
	public function delete_section($sec_id = NULL){
		if($sec_id == NULL){
			redirect('admincontrol/panel/section_list');
		}
		
		if($this->db->delete('section_tab',array('section_id' => $sec_id))){
			$this->session->set_flashdata("success","Section is deleted successfully.");
			redirect('admincontrol/panel/section_list','refresh');
		}else{
			$this->session->set_flashdata("e_error","There is some Problem. Please try again.");
			redirect('admincontrol/panel/section_list','refresh');
		}
		 
	}
	
	public function edit_section($sec_id = NULL){
		if($sec_id == NULL){
			redirect('admincontrol/panel/section_list');
		}
		$this->data['section_detail'] = $this->db->get_where('section_tab',array('section_id' => $sec_id))->row();
		$this->load->view('admin/edit_section_view', $this->data);
	}
	
	public function edit_new_section(){
		if($_POST){
			$san_id = $this->input->post('san_id');
			$san_name = $this->input->post('san_name');
			$san_detail = $this->input->post('san_detail');
			$msg = 0;
			
			$this->form_validation->set_rules('san_id', 'Section ID', 'trim|required|is_unique[section_tab.section_name]|xss_clean');
			$this->form_validation->set_rules('san_name', 'Section Number', 'trim|required|is_unique[section_tab.section_name]|xss_clean');
			$this->form_validation->set_rules('san_detail', 'Section Details', 'trim|xss_clean');
			
			if($this->form_validation->run() == TRUE){
				
				$san_name = trim($san_name);
				$san_uri = strtolower(str_replace(" ","_",$san_name));
				$row_array = array(
					'section_name' => $san_name,
					'section_uri' => $san_uri,
					'section_details' => $san_detail,
					'section_modifydate' => date('Y-m-d H:i:s')
				);
				if($this->admin_m->common_Updation_in_DB($row_array, 'section_tab', 'section_id', $san_id) == TRUE){
					echo json_encode(array('msg'=>1, 'e_msg'=>'Section is updated Successfully.'));
				}else{
					echo json_encode(array('msg'=>$msg, 'e_msg'=>'There have some Database issue, Please Try Again.'));
				}
				
			}else{
				echo json_encode(array('msg'=>$msg, 'e_msg'=>validation_errors()));
			}
			exit;
		}
	}
	
}
