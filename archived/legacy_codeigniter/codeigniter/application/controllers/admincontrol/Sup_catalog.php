<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sup_catalog extends Admin_Controller {
	
	 public function __construct() {
        parent::__construct();
        //date_default_timezone_set("Asia/Kolkata");
        $this->data["u_details"] = $this->admin_m->GetDetailsofUsers($this->session->userdata['uid']);
		$this->data['templateDetails'] = $this->db->get_where('permission_master',['pt_id'=>$this->session->userdata('pt_id'),'status'=>1])->row();
    
	}
	
    public function index() {
		redirect('admincontrol/sup_catalog/supplier_catalog_list');
    }
    
    public function supplier_catalog_list(){


		$category = $this->input->get('category');
		$supplier = $this->input->get('supplier');
		$cost_code = $this->input->get('cost_code');
		$rentable = $this->input->get('rentable');

		if($this->session->userdata('utype')==4){
			$userInfo=$this->db->get_where('user_info',['username' => $this->session->userdata('username')])->row();
			// print_r($userInfo);
			$supplierPid=$userInfo->procore_supplier_id;
			$suppplierInfo=$this->db->get_where('supplier_master',['procore_supplier_id'=>$supplierPid,'sup_status'=>1])->row();
			if(!empty($suppplierInfo)){
				$supplier= $suppplierInfo->sup_id;
			}
		}

		$filterClauses = [];

		if ($supplier != null) {
			$filterClauses = $filterClauses + array('supcat_supplier' => $supplier);
		}
		if ($category != null) {
			$filterClauses = $filterClauses + array('item_cat_ms' => $category);
		}
		if ($cost_code != null) {
			$filterClauses = $filterClauses + array('item_ccode_ms' => $cost_code);
		}
		if ($rentable != null) {
			$filterClauses = $filterClauses + array('supcat_is_rentable' => $rentable);
		}

		$this->data['getrecord_list'] = $this->admin_m->get_All_SupplierCatalog_Set(null,$filterClauses);
		$this->data['item_list'] = $this->db->order_by('item_code','ASC')->where('item_status',1)->get('item_master')->result();
		$this->data['supp_list'] = $this->db->order_by('sup_name','ASC')->where('sup_status',1)->get('supplier_master')->result();
		$this->data['uom_list'] = $this->db->order_by('uom_name','ASC')->where('uom_status',1)->get('unit_of_measure_tab')->result();
		$this->data['cat_list'] = $this->db->order_by('icat_id','ASC')->where('icat_status',1)->get('item_category_tab')->result();

		$this->data['ccode_list'] = $this->db->order_by('cc_no', 'ASC')->where('cc_status', 1)->get('cost_code_master')->result();
		$this->data['items'] = $this->admin_m->get_All_Items();
		$this->data['suppliers'] = $this->admin_m->get_All_Suppliers();
		$this->data['item_units'] = $this->admin_m->get_All_UOM();
		$this->data['filters'] = $filterClauses;
		$this->load->view('admin/catalog/catalog_list_view', $this->data);
	}

	public function get_rentable_items() {
		$is_rentable = $this->input->post("is_rentable");
		if($is_rentable == 1) {
			$item_list = $this->db->order_by('item_code','ASC')->where('item_status',1)->where('item_is_rentable',1)->get('item_master')->result();
		} else {
			$item_list = $this->db->order_by('item_code','ASC')->where('item_status',1)->where('item_is_rentable',0)->get('item_master')->result();
		}
		echo json_encode(array('msg' => 1, 'e_msg' => $item_list));
	}
	
	public function add_new_supplier_catalog_sets(){
		if($_POST){
			$sc_sku_code = $this->input->post("sc_sku_code");
			$sc_supplier = $this->input->post("sc_supplier");
			$sc_itm_code = $this->input->post("sc_itm_code");
			$sc_daily_price = $this->input->post("sc_daily_price");
			$unit_price = $this->input->post("sc_price");
			$sc_weekly_price = $this->input->post("sc_weekly_price");
			$sc_monthly_price = $this->input->post("sc_monthly_price");
//			$sc_uom = $this->input->post("sc_uom");
			$sc_lastdate = $this->input->post("sc_lastdate");
            $this->form_validation->set_rules('sc_sku_code', 'SKU Code', 'trim|required');
            $this->form_validation->set_rules('sc_supplier', 'Supplier Name', 'trim|required|is_natural_no_zero');
            $this->form_validation->set_rules('sc_itm_code', 'Item Code', 'trim|required');
            $this->form_validation->set_rules('sc_lastdate', 'Last Date', 'trim|required');
            $this->form_validation->set_rules('sc_daily_price', 'Daily Price', 'trim|numeric');
            $this->form_validation->set_rules('sc_weekly_price', 'Weekly Price', 'trim|numeric');
            $this->form_validation->set_rules('sc_monthly_price', 'Monthly Price', 'trim|numeric');
//            $this->form_validation->set_rules('sc_uom', 'Unit of Measure', 'trim|required|is_natural_no_zero');
			
			if ($this->form_validation->run() == TRUE) {
                	//echo "1st";
				if($this->admin_m->check_scatalog_skusets_exist($sc_sku_code) == TRUE)
				{			
					//date_default_timezone_set("Asia/Kolkata");
					//$sc_lastdate = str_replace("/","-",$sc_lastdate);
					$row = array(
							'supcat_supplier' => $sc_supplier,
							'supcat_item_code' => $sc_itm_code,
							'supcat_sku_no' => trim($sc_sku_code),
//							'supcat_uom' => $sc_uom,
							'supcat_price' => trim($unit_price),
							'supcat_daily_price' => trim($sc_daily_price),
							'supcat_weekly_price' => trim($sc_weekly_price),
							'supcat_monthly_price' => trim($sc_monthly_price),
							'supcat_lastdate' => date('Y-m-d',strtotime($sc_lastdate)),
							'supcat_createdate' => date('Y-m-d H:i:s'),
							'supcat_createby' => $this->session->userdata['uid']
						);

						if($this->input->post("sc_is_rentable")==1){
							$row+=[
								'supcat_is_rentable' => 1
							];
						}
						
					if ($this->admin_m->common_Insertion_in_DB($row, 'supplier_catalog_tab') == TRUE)
					{
						echo json_encode(array('msg' => 1, 's_msg' => ''));
					}
					else{
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Insert Data, Try Again.'));
					}
				}
				else
				{
					echo json_encode(array('msg' => 0, 'e_msg' => 'SKU Code already Exist, please check it.'));
				}
            }else{
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		}else{
			redirect('default404');
		}
	}
	
	public function lock_sup_catlogset($uid = NULL){
		if($this->session->userdata('utype')==1 || $this->data['templateDetails']->pt_i_supplierc<3){
			if($uid == NULL){
				redirect('admincontrol/sup_catalog/supplier_catalog_list');
			}
			$row_arr = array(
				'supcat_status' => 0
			);
			if($this->admin_m->common_Updation_in_DB($row_arr,'supplier_catalog_tab', 'supcat_id', $uid) == TRUE)
			{
				$this->session->set_flashdata("success","Record is Locked successfully");
				redirect('admincontrol/sup_catalog/supplier_catalog_list');
			}
			else
			{
				$this->session->set_flashdata("e_error","There is some Problem. Please try again.");
				redirect('admincontrol/sup_catalog/supplier_catalog_list');
			}
		}else{
			redirect('default404');
		}
	}
	
	public function unlock_sup_catlogset($uid = NULL){
		if($this->session->userdata('utype')==1 || $this->data['templateDetails']->pt_i_supplierc<3){
			if($uid == NULL){
				redirect('admincontrol/sup_catalog/supplier_catalog_list');
			}
			$row_arr = array(
				'supcat_status' => 1
			);
			if($this->admin_m->common_Updation_in_DB($row_arr,'supplier_catalog_tab', 'supcat_id', $uid) == TRUE)
			{
				$this->session->set_flashdata("success","Record is Unlocked successfully");
				redirect('admincontrol/sup_catalog/supplier_catalog_list');
			}
			else
			{
				$this->session->set_flashdata("e_error","There is some Problem. Please try again.");
				redirect('admincontrol/sup_catalog/supplier_catalog_list');
			}
		}else{
			redirect('default404');
		}
	}

	public function modify_sup_catlog_sets(){
		if($_POST){
			$update_id_spcat = $this->input->post("update_id_spcat");
			$update_sc_sku_code = $this->input->post("update_sc_sku_code");
			$update_sc_supplier = $this->input->post("update_sc_supplier");
			$update_sc_itm_code = $this->input->post("update_sc_itm_code");
			$update_sc_itm_name = $this->input->post("update_sc_itm_name");
			$update_sc_daily_price = $this->input->post("update_sc_daily_price");
			$update_sc_price = $this->input->post("update_sc_price");
			$update_sc_weekly_price = $this->input->post("update_sc_weekly_price");
			$update_sc_monthly_price = $this->input->post("update_sc_monthly_price");
			$update_sc_lastdate = $this->input->post("update_sc_lastdate");
			$update_rentable = $this->input->post("update_rentable");

            $this->form_validation->set_rules('update_id_spcat', 'Item ID', 'trim|required|is_natural_no_zero');
            $this->form_validation->set_rules('update_sc_sku_code', 'SKU Code', 'trim|required');
            $this->form_validation->set_rules('update_sc_supplier', 'Supplier Name', 'trim|required|is_natural_no_zero');
            $this->form_validation->set_rules('update_sc_itm_code', 'Item Code', 'trim|required');
			$this->form_validation->set_rules('update_sc_lastdate', 'Last Date', 'trim|required');
            $this->form_validation->set_rules('update_sc_daily_price', 'Daily Price', 'trim|numeric');
            $this->form_validation->set_rules('update_sc_weekly_price', 'Weekly Price', 'trim|numeric');
            $this->form_validation->set_rules('update_sc_monthly_price', 'Monthly Price', 'trim|numeric');

			if ($this->form_validation->run() == TRUE) {
                	//echo "1st";
				if($this->admin_m->check_scatalog_skusets_exist($update_sc_sku_code, $update_id_spcat) == TRUE)
				{			
					//date_default_timezone_set("Asia/Kolkata");
					//$update_sc_lastdate = str_replace("/","-",$update_sc_lastdate);
					$row = array(
							'supcat_supplier' => $update_sc_supplier,
							'supcat_item_code' => $update_sc_itm_code,
							'supcat_sku_no' => trim($update_sc_sku_code),
							'supcat_price' =>  trim($update_sc_price),
							'supcat_daily_price' => trim($update_sc_daily_price),
							'supcat_weekly_price' => trim($update_sc_weekly_price),
							'supcat_monthly_price' => trim($update_sc_monthly_price),
							'supcat_is_rentable' => $update_rentable,
							'supcat_lastdate' => date('Y-m-d',strtotime($update_sc_lastdate)),
							'supcat_modifydate' => date('Y-m-d H:i:s'),
							'supcat_modifyby' => $this->session->userdata['uid']
						);

						if($this->input->post("update_sc_is_renyable")==1){
							$row+=[
								'supcat_is_rentable' => 1
							];
						}
						
					if ($this->admin_m->common_Updation_in_DB($row, 'supplier_catalog_tab', 'supcat_id', $update_id_spcat) == TRUE)
					{
						echo json_encode(array('msg' => 1, 's_msg' => ''));
					}
					else{
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Update Data, Try Again.'));
					}
				}
				else
				{
					echo json_encode(array('msg' => 0, 'e_msg' => 'Item Code already Exist, please check it.'));
				}
            }else{
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		}else{
			redirect('default404');
		}
	}
	
	public function get_details_of_sup_catlog_sets(){
		if($_POST){
			$name_scid = $this->input->post("name_scid");
            
            $this->form_validation->set_rules('name_scid', 'Supplier Catalog ID', 'trim|required|is_natural_no_zero');
			
			if ($this->form_validation->run() == TRUE) {
                		
					$getrecord_detail = $this->admin_m->get_All_SupplierCatalog_Set($name_scid);
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

	public function get_info_details(){
		if($_POST){
			$item_value = $this->input->post("item_value");
            
            $this->form_validation->set_rules('item_value', 'Item', 'trim|required');
			
			if ($this->form_validation->run() == TRUE) {
                		
					$getrecord_detail = $this->admin_m->get_info_details($item_value);
					if (count((array)$getrecord_detail) > 0)
					{
						echo json_encode(array('msg' => 1, 's_msg' => $getrecord_detail ));
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

	// bulk upload function
	public function bulkitem_upload_section_sets(){
		if($_POST){
			$filename = $_FILES['files']['name'];
			if(!empty($filename)){

				//print_r($data_upload);
				$this->load->helper(array('form', 'url'));
				$this->load->library('upload');
				
				$config['upload_path'] = realpath('upload_file/bulk_file/'); 
				$config['allowed_types'] = 'csv';
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
					$this->load->library('CSVReader');

					$csvData = $this->csvreader->parse_csv($_FILES['files']['tmp_name']);
					$uploadError=0;
					if(!empty($csvData)){
						foreach($csvData as $row) {
							$item = $this->admin_m->getItemByCode($row['Item Code']);
							if(!empty($item)){
								$supplier = $this->admin_m->getSupplierByName($row['Supplier']);
								if(!empty($supplier)){
									$unit = $this->admin_m->get_UnitByName($row['UOM']);
									if(!empty($unit)){
										$memData = array(
											'supcat_supplier' => isset($supplier) ? $supplier->sup_id : null,
											'supcat_item_code' => isset($row['Item Code']) ? $row['Item Code'] : null,
											'supcat_sku_no' => isset($row['Sku No']) ? $row['Sku No'] : null,
											'supcat_uom' => isset($unit) ? $unit->uom_id : null,
											'supcat_price' => isset($row['Unit Price']) ? $row['Unit Price'] : null,
											'supcat_daily_price' => isset($row['Rental Daily Price']) ? $row['Rental Daily Price'] : null,
											'supcat_weekly_price' => isset($row['Rental Weekly Price']) ? $row['Rental Weekly Price'] : null,
											'supcat_monthly_price' => isset($row['Rental Monthly Price']) ? $row['Rental Monthly Price'] : null,
											'supcat_lastdate' => isset($row['Price Expiry Date']) ? date('Y-m-d',strtotime($row['Price Expiry Date'])) : null,
										);
										$this->admin_m->common_Insertion_in_DB_SupCat($memData, 'supplier_catalog_tab');
									}else{
										$uploadError=1;
										break;
									}
								}else{
									$uploadError=2;
									break;
								}
							}else{
								$uploadError=3;
								break;
							}
						}
					}

					if($uploadError==0){
						echo json_encode(array('msg' => 1, 's_msg' => '' ));
					}else if($uploadError==1){
						echo json_encode(array('msg'=>0,'e_msg'=>'UOM '.$row['UOM'].' not setup in app, Please check and add the UOM required in the app first before bulk upload.'));
					}else if($uploadError==2){
						echo json_encode(array('msg'=>0,'e_msg'=>'Supplier '.$row['Supplier'].' not setup in app, Please check and add the supplier required in the app first before bulk upload.'));
					}else if($uploadError==3){
						echo json_encode(array('msg'=>0,'e_msg'=>'Item with '.$row['Item Code'].' code not setup in app, Please check and add the item required in the app first before bulk upload.'));
					}


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

	// bulk upload function
	public function bulkitem_upload_section_sets_prices(){
		if($_POST){
			$filename = $_FILES['files']['name'];
			if(!empty($filename)){

				//print_r($data_upload);
				$this->load->helper(array('form', 'url'));
				$this->load->library('upload');
				
				$config['upload_path'] = realpath('upload_file/bulk_file/'); 
				$config['allowed_types'] = 'csv';
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
					$this->load->library('CSVReader');

					$csvData = $this->csvreader->parse_csv($_FILES['files']['tmp_name']);
					$errorStatus=0;
					if(!empty($csvData)){
						foreach($csvData as $row) {
						$supplier = $this->admin_m->getSupplierByName($row['Supplier']);
						$supcat_supplier = $supplier->sup_id;
						$supcat_item_code = $row['Item Code'];
						//  $unit = $this->admin_m->get_UnitByName($row['unit_of_measure']);
							$memData = array(
								// 'supcat_supplier' => isset($supplier) ? $supplier->sup_id : null,
								// 'supcat_item_code' => isset($row['item_code']) ? $row['item_code'] : null,
								// 'supcat_sku_no' => isset($row['sku_no']) ? $row['sku_no'] : null,
								// 'supcat_uom' => isset($unit) ? $unit->uom_id : null,
								'supcat_price' => isset($row['Unit Price']) ? $row['Unit Price'] : null,
								'supcat_daily_price' => isset($row['Rental Daily Price']) ? $row['Rental Daily Price'] : null,
								'supcat_weekly_price' => isset($row['Rental Weekly Price']) ? $row['Rental Weekly Price'] : null,
								'supcat_monthly_price' => isset($row['Rental Monthly Price']) ? $row['Rental Monthly Price'] : null,
								'supcat_lastdate' => isset($row['Price Expiry Date']) ? date('Y-m-d',strtotime(str_replace("/", "-", $row['Price Expiry Date']))) : null,
							);
							if($this->admin_m->common_Insertion_in_DB_SupCat_Prices($memData,$supcat_supplier,$supcat_item_code, 'supplier_catalog_tab')){

							}else{
								$errorStatus=1;
							}
						}
					}

					if($errorStatus==0){
						echo json_encode(array('msg' => 1, 's_msg' => $supcat_supplier));
					}else{
						echo json_encode(array('msg'=>0,'e_msg'=>'Error in updating Prices'));
					}

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

	public function export_csv(){
		/* file name */
		$getrecord_list =$this->admin_m->get_All_SupplierCatalog_Set();

		$filename = 'supplier_'.date('Ymd').'.csv';
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=$filename");
		header("Content-Type: application/csv; ");
		/* get data */

		/* file creation */
		$file = fopen('php://output', 'w');
		$header = array("Supplier","Item Code","Sku No","UOM","Unit Price","Rental Daily Price","Rental Weekly Price","Rental Monthly Price","Price Expiry Date");
		fputcsv($file, $header);
		foreach ($getrecord_list as $key=>$line){
			$data = [
				$line->sup_name,
				$line->supcat_item_code,
				$line->supcat_sku_no,
				$line->uom_name,
				$line->supcat_price,
				$line->supcat_daily_price,
				$line->supcat_weekly_price,
				$line->supcat_monthly_price,
				$line->supcat_lastdate,
			];
			fputcsv($file,$data);
		}
		fclose($file);
		exit;
	}


	// delete supplier catalog function
	public function delete_itemset($id)
	{

		if($this->session->userdata('utype')==1 || $this->data['templateDetails']->pt_i_supplierc<2){
			$resultrow = $this->db->get_where('supplier_catalog_tab', array('supcat_id' => $id))->row();
			if ($resultrow) {
				if($this->db->delete('supplier_catalog_tab', array('supcat_id' => $resultrow->supcat_id))) {
					$this->session->set_flashdata("success", "Record Deleted successfully");
					return redirect('admincontrol/sup_catalog/supplier_catalog_list');
				}
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
				return redirect('admincontrol/sup_catalog/supplier_catalog_list');
			} else {
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
				return redirect('admincontrol/sup_catalog/supplier_catalog_list');
			}
		}else{
			redirect('default404');
		}
	}
}
