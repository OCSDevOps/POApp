<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Itemcategory extends Admin_Controller {
	
	 public function __construct() {
        parent::__construct();
        //date_default_timezone_set("Asia/Kolkata");
        $this->data["u_details"] = $this->admin_m->GetDetailsofUsers($this->session->userdata['uid']);
		$this->data['templateDetails'] = $this->db->get_where('permission_master',['pt_id'=>$this->session->userdata('pt_id'),'status'=>1])->row();
    
	}
	
    public function index() {
		redirect('admincontrol/itemcategory/item_category_list');
    }
    
    public function item_category_list(){
		$this->data['getrecord_list'] = $this->db->order_by('icat_name','ASC')->get_where('item_category_tab')->result();
		$this->load->view('admin/category/item_category_list_view', $this->data);
	}
	
	public function add_new_item_category(){
		if($_POST){
			$name_cat = $this->input->post("name_cat");
            
            $this->form_validation->set_rules('name_cat', 'Category Name', 'trim|required');
			
			if ($this->form_validation->run() == TRUE) {
                	//echo "1st";
				if($this->admin_m->check_itemcategory_exist($name_cat) == TRUE)
				{			
					//date_default_timezone_set("Asia/Kolkata");
					
					$row = array(
							'icat_name' => trim($name_cat),
							'icat_createdate' => date('Y-m-d H:i:s'),
							'icat_createby' => $this->session->userdata['uid']
						);
						
					if ($this->admin_m->common_Insertion_in_DB($row, 'item_category_tab') == TRUE)
					{
						echo json_encode(array('msg' => 1, 's_msg' => ''));
					}
					else{
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Insert Data, Try Again.'));
					}
				}
				else
				{
					echo json_encode(array('msg' => 0, 'e_msg' => 'Category Name already Exist, please check it.'));
				}
            }else{
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		}else{
			redirect('default404');
		}
	}
	
	public function lock_item_category_set($uid = NULL){
		if($this->session->userdata('utype')==1 || $this->data['templateDetails']->pt_m_item<3){
			if($uid == NULL){
				redirect('admincontrol/itemcategory/item_category_list');
			}
			$row_arr = array(
				'icat_status' => 0
			);
			if($this->admin_m->common_Updation_in_DB($row_arr,'item_category_tab', 'icat_id', $uid) == TRUE)
			{
				$this->session->set_flashdata("success","Record is Locked successfully");
				redirect('admincontrol/itemcategory/item_category_list');
			}
			else
			{
				$this->session->set_flashdata("e_error","There is some Problem. Please try again.");
				redirect('admincontrol/itemcategory/item_category_list');
			}
		}else{
			redirect('default404');
		}
	}
	
	public function unlock_item_category_set($uid = NULL){
		if($this->session->userdata('utype')==1 || $this->data['templateDetails']->pt_m_item<3){
			if($uid == NULL){
				redirect('admincontrol/itemcategory/item_category_list');
			}
			$row_arr = array(
				'icat_status' => 1
			);
			if($this->admin_m->common_Updation_in_DB($row_arr,'item_category_tab', 'icat_id', $uid) == TRUE)
			{
				$this->session->set_flashdata("success","Record is Unlocked successfully");
				redirect('admincontrol/itemcategory/item_category_list');
			}
			else
			{
				$this->session->set_flashdata("e_error","There is some Problem. Please try again.");
				redirect('admincontrol/itemcategory/item_category_list');
			}
		}else{
			redirect('default404');
		}
	}

	public function modify_item_categorysets(){
		if($_POST){
			$update_id_cat = $this->input->post("update_id_cat");
			$update_name_cat = $this->input->post("update_name_cat");
            
            $this->form_validation->set_rules('update_id_cat', 'Category ID', 'trim|required|is_natural');
            $this->form_validation->set_rules('update_name_cat', 'Category Name', 'trim|required');
			
			if ($this->form_validation->run() == TRUE) {
                	//echo "1st";
				if($this->admin_m->check_itemcategory_exist($update_name_cat, $update_id_cat) == TRUE)
				{			
					//date_default_timezone_set("Asia/Kolkata");
					
					$row = array(
							'icat_name' => trim($update_name_cat),
							'icat_modifydate' => date('Y-m-d H:i:s'),
							'icat_modifyby' => $this->session->userdata['uid']
						);
						
					if ($this->admin_m->common_Updation_in_DB($row, 'item_category_tab', 'icat_id', $update_id_cat) == TRUE)
					{
						echo json_encode(array('msg' => 1, 's_msg' => ''));
					}
					else{
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Update Data, Try Again.'));
					}
				}
				else
				{
					echo json_encode(array('msg' => 0, 'e_msg' => 'Unit Name already Exist, please check it.'));
				}
            }else{
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		}else{
			redirect('default404');
		}
	}
	
	public function get_details_of_category(){
		if($_POST){
			$name_catid = $this->input->post("name_catid");
            
            $this->form_validation->set_rules('name_catid', 'Unit ID', 'trim|required|is_natural');
			
			if ($this->form_validation->run() == TRUE) {
                		
					$getrecord_detail = $this->db->where('icat_id',$name_catid)->get('item_category_tab')->row();
					if (count((array)$getrecord_detail) > 0)
					{
						echo json_encode(array('msg' => 1, 's_msg' => $getrecord_detail));
					}
					else{
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Retrieve Data, Try Again.'));
					}

            }else{
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		}else{
			redirect('default404');
		}	
	}

	public function bulkitem_upload_section_sets(){
		if($_POST){
			$filename = $_FILES['files']['name'];
			if(!empty($filename)){

				//print_r($data_upload);
				$this->load->helper(array('form', 'url'));
				$this->load->library('upload');
				
				$config['upload_path'] = realpath('upload_file/bulk_file/'); 
				$config['allowed_types'] = 'xls|xlsx';
				$config['overwrite'] = FALSE;
				$config['remove_spaces'] = TRUE;
	            $config['max_size'] = '9000';
				$config['file_name'] = date('dmYHis').$filename;
				
				$this->load->library('upload', $config);
				$this->upload->initialize($config);
			   
				if($this->upload->do_upload('files')){
					
					$upload_data = $this->upload->data();
	            	$filename = $upload_data['file_name'];
	            	$file_extension = $upload_data['file_ext'];
					
					$this->load->library('excel');

					if($file_extension == ".xls"){
						$objReader =PHPExcel_IOFactory::createReader('Excel5');     //For excel 2003 
					}elseif($file_extension == ".xlsx"){
						$objReader= PHPExcel_IOFactory::createReader('Excel2007');	// For excel 2007 
					}
	            	//$objReader =PHPExcel_IOFactory::createReader('Excel5');     //For excel 2003 
					//$objReader= PHPExcel_IOFactory::createReader('Excel2007');	// For excel 2007 	  
			        //Set to read only
			        $objReader->setReadDataOnly(true); 		  
			        //Load excel file
					$objPHPExcel=$objReader->load(FCPATH.'upload_file/bulk_file/'.$filename);		 
			        $totalrows=$objPHPExcel->setActiveSheetIndex(0)->getHighestRow();   //Count Numbe of rows avalable in excel      	 
			        $objWorksheet=$objPHPExcel->setActiveSheetIndex(0);                
			        //loop from first data untill last data
			        //$get_err = array();
			        $get_err = '';
			        $get_err_cnt = 0;
			        $get_success_cnt = 0;
					$sms_main_array = array();
					//$sms_string_set = "";
					//$sms_counter = 1;
					for($i=2;$i<=$totalrows;$i++)
			        {
			            $category_name = $objWorksheet->getCellByColumnAndRow(1,$i)->getValue(); //Excel Column 2			
			            //$patient_time = $objWorksheet->getCellByColumnAndRow(9,$i)->getValue(); //Excel Column 10
						
						//$patient_time_set = PHPExcel_Style_NumberFormat::toFormattedString($patient_time, 'hh:mm:ss');
						//$mobile_number = str_replace(' ', '', $mobile_number);
						if($this->admin_m->check_itemcategory_exist(trim($category_name)) == TRUE)
						{
							$rows_array = array(
								'icat_name' => trim($category_name),
								'icat_createdate' => date('Y-m-d H:i:s'),
								'icat_createby' => $this->session->userdata['uid']
							);
							$this->admin_m->common_Insertion_in_DB($rows_array, 'item_category_tab');
						}

					}
					echo json_encode(array('msg' => 1, 's_msg' => ''));

				}else{
					echo json_encode(array('msg'=>0,'e_msg'=>$this->upload->display_errors()));
				}

			}else{
				echo json_encode(array('msg'=>0,'e_msg'=>'Please Select a File to Upload, Chcek Agian.'));
			}
			exit;
		}else{
			redirect('default404');
		}
	}

	public function delete_itemset($id)
	{
		if($this->session->userdata('utype')==1 || $this->data['templateDetails']->pt_m_item<2){
			$resultrow = $this->db->get_where('item_category_tab', array('icat_id' => $id))->row();
			if ($resultrow) {
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
				$res = $this->admin_m->check_usage('item_master','item_cat_ms',$id);
				if(!$res) {
					if ($this->db->delete('item_category_tab', array('icat_id' => $resultrow->icat_id))) {
						$this->session->set_flashdata("success", "Record Deleted successfully");
						return redirect('admincontrol/itemcategory/item_category_list');
					}
				} else
				{
					$this->session->set_flashdata("e_error", "This category is linked with items, please unlink before delete.");
				}
				return redirect('admincontrol/itemcategory/item_category_list');
			} else {
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
				return redirect('admincontrol/itemcategory/item_category_list');
			}
		}else{
			redirect('default404');
		}
	}
}
