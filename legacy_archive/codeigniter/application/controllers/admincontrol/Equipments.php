<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Equipments extends Admin_Controller {
	
	 public function __construct() {
        parent::__construct();
		$this->load->model('Equipment_Model');
        //date_default_timezone_set("Asia/Kolkata");
        $this->data["u_details"] = $this->admin_m->GetDetailsofUsers($this->session->userdata['uid']);
		$this->data['templateDetails'] = $this->db->get_where('permission_master',['pt_id'=>$this->session->userdata('pt_id'),'status'=>1])->row();
    
	}
	
    public function index() {
		redirect('admincontrol/equipments/all_equipment_list');
    }
    
    public function all_equipment_list(){
		$location = $this->input->get('project');
		$category = $this->input->get('category');
		$type = $this->input->get('type');
		$status = $this->input->get('status');
		$user = $this->input->get('user');

		$filterClauses = [];

		if ($location != null) {
			$filterClauses = $filterClauses + array('eqm_location' => $location);
		}
		if ($category != null) {
			$filterClauses = $filterClauses + array('eqm_category' => $category);
		}
		if ($type != null) {
			$filterClauses = $filterClauses + array('eqm_asset_type' => $type);
		}
		if ($status != null) {
			$filterClauses = $filterClauses + array('eqm_status' => $status);
		}
		if ($user != null) {
			$filterClauses = $filterClauses + array('eqm_current_operator' => $user);
		}
		$this->data['getrecord_list'] = $this->db->where('status',1)->where($filterClauses)->order_by('eq_id','DESC')->get('eq_master')->result();
		$this->data['suppliers'] = $this->db->get('supplier_master')->result();
		$this->data['taxcodes'] = $this->db->get('taxgroup_master')->result();
		$this->data['projects'] = $this->db->get('project_master')->result();
		$this->data['users'] = $this->db->get('user_info')->result();
		$this->data['filters'] = $filterClauses;
		$this->data['availableEquipments'] = $this->db->where(['eqm_status'=>'Available','status'=>1])->get('eq_master')->result();
		$this->data['inUseEquipments'] = $this->db->where(['eqm_status'=>'In Use','status'=>1])->get('eq_master')->result();
		// $this->data['usr_list'] = $this->db->order_by('firstname','ASC')->where('status','1')->get('user_views')->result();
		$this->load->view('admin/equipment/equipment_list_view', $this->data);
	}
	
	// public function add_new_project(){
	// 	$this->data['usr_list'] = $this->db->order_by('firstname','ASC')->where('u_type != 1')->where('status','1')->get('user_views')->result();
	// 	$this->load->view('admin/project/add_project', $this->data);
	// }
	
	public function new_equipment_submission(){
		if($_POST){
			
			$eq_name = $this->input->post("eq_name");
			$eq_desc = $this->input->post("eq_desc");
			$eq_type = $this->input->post("eq_type");
			$eq_tag = $this->input->post("eq_tag");
			if(isset($_FILES['eq_picture'])){
				$eq_picture = $_FILES['eq_picture']['name'];
			}else{
				$eq_picture = '';
			}
			$eq_condition = $this->input->post("eq_condition");
			$eq_category = $this->input->post("eq_category");
			$eq_license_plate = $this->input->post("eq_license_plate");
			$eq_current_operator = $this->input->post("eq_current_operator");
			$eq_status = $this->input->post("eq_status");
			$eq_existing_reading = $this->input->post("eq_existing_reading");
			$eq_estimate_usage = $this->input->post("eq_estimate_usage");
			$eq_location = $this->input->post("eq_location");
			$eq_supplier = $this->input->post("eq_supplier");
			$eq_purchase_price = $this->input->post("eq_purchase_price");
			$eq_purchase_date = $this->input->post("eq_purchase_date");
			$eq_current_value = $this->input->post("eq_current_value");
			$eq_brand = $this->input->post("eq_brand");
			$eq_model = $this->input->post("eq_model");
			$eq_serial = $this->input->post("eq_serial");
			$eq_year = $this->input->post("eq_year");
			$eq_war_expiry_date = $this->input->post("eq_war_expiry_date");
			$eq_dep_method = $this->input->post("eq_dep_method");
			$eq_rental_total_value = $this->input->post("eq_rental_total_value");
			$eq_rental_insurance = $this->input->post("eq_rental_insurance");
			$eq_rental_insurance_amt = $this->input->post("eq_rental_insurance_amt");

			if(isset($_FILES['eq_picture'])){
				$filename = $_FILES['eq_picture']['name'];
			}else{
				$filename = '';
			}

			if (!empty($filename)) {

				$config['upload_path'] = realpath('upload_file/equipment/');
				$config['allowed_types'] = 'jpg|png|jpeg|JPG|PNG|JPEG';
				$config['overwrite'] = TRUE;
				$config['remove_spaces'] = TRUE;
				$config['max_size'] = '5000';
				$config['file_name'] = date('His') . $filename;

				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				if ($this->upload->do_upload('eq_picture')) {
					$upload_data = $this->upload->data();

					$eq_picture = $upload_data['file_name'];
				}else {
					$this->data["error"] = $this->upload->display_errors();
				}
			} else {
				$eq_picture = null;
			}

            $this->form_validation->set_rules('eq_name', 'Equipment Name', 'trim|required');
            $this->form_validation->set_rules('eq_desc', 'Equipment Description', 'trim|required');
            $this->form_validation->set_rules('eq_type', 'Equipment Type', 'trim|required');
            $this->form_validation->set_rules('eq_tag', 'Equipment Tag', 'trim|required|is_unique[eq_master.eqm_asset_tag]');
            // $this->form_validation->set_rules('eq_picture', 'Equipment Picture', 'trim|required');
            // $this->form_validation->set_rules('eq_condition', 'Equipment Condition', 'trim|required');
            // $this->form_validation->set_rules('eq_category', 'Equipment Category', 'trim|required');

			// if($eq_category=='Vehicles'){
			// 	$this->form_validation->set_rules('eq_license_plate', 'License Plate', 'trim|required');
			// 	$this->form_validation->set_rules('eq_current_operator', 'Current Operator', 'trim|required');
			// }
            // $this->form_validation->set_rules('eq_status', 'Equipment Status', 'trim|required');
            // $this->form_validation->set_rules('eq_existing_reading', 'Exiting Reading', 'trim|required');
            // $this->form_validation->set_rules('eq_estimate_usage', 'Estimate Usage', 'trim|required');
            // $this->form_validation->set_rules('eq_location', 'Location', 'trim|required');
            // $this->form_validation->set_rules('eq_supplier', 'Equipment Supplier', 'trim|required');

			// if($eq_type!='Rental'){
			// 	$this->form_validation->set_rules('eq_purchase_price', 'Equipment Purchase Price', 'trim|required');
			// 	$this->form_validation->set_rules('eq_purchase_date', 'Equipment Purchase Date', 'trim|required');
			// 	$this->form_validation->set_rules('eq_current_value', 'Equipment Current Value', 'trim|required');
			// 	$this->form_validation->set_rules('eq_brand', 'Equipment Brand', 'trim|required');
			// 	$this->form_validation->set_rules('eq_model', 'Equipment Model', 'trim|required');
			// 	$this->form_validation->set_rules('eq_war_expiry_date', 'Equipment Warrenty Expity Date', 'trim|required');
			// 	$this->form_validation->set_rules('eq_dep_method', 'Equipment Depreciation Method', 'trim|required');
			// }else{
			// 	$this->form_validation->set_rules('eq_rental_total_value', 'Total Value', 'trim|required');
			// 	$this->form_validation->set_rules('eq_rental_insurance', 'Rental Insurance', 'trim|required');
			// }

			// if($eq_rental_insurance=='YES'){
			// 	$this->form_validation->set_rules('eq_rental_insurance_amt', 'Rental Insurance Amount', 'trim|required');
			// }
            // $this->form_validation->set_rules('eq_serial', 'Equipment Serial No', 'trim|required');
            // $this->form_validation->set_rules('eq_year', 'Equipment Year', 'trim|required');
			
			if ($this->form_validation->run() == TRUE) {
					
					$remainingLife='';

					if($eq_estimate_usage!='' && $eq_existing_reading!=''){
						$remainingLife=($eq_estimate_usage-$eq_existing_reading);
					}

					$row = array(
						'eqm_asset_name' => trim($eq_name),
						'eqm_asset_description' => trim($eq_desc),
						'eqm_asset_type' => trim($eq_type),
						'eqm_asset_tag' => trim($eq_tag),
						'eqm_asset_picture' => trim($eq_picture),
						'eqm_asset_condition' => trim($eq_condition),
						'eqm_category' => trim($eq_category),
						'eqm_status' => trim($eq_status),
						'eqm_existing_reading' => trim($eq_existing_reading),
						'eqm_estimate_usage' => trim($eq_estimate_usage),
						'eqm_remaining_life' => trim($remainingLife),
						'eqm_location' => trim($eq_location),
						'eqm_supplier' => trim($eq_supplier),
						'eqm_serial' => trim($eq_serial),
						'eqm_year' => trim($eq_year),
						'eqm_created_date' => date('Y-m-d H:i:s')
					);

					if($eq_category=='Vehicles'){
						$row+=[
							'eqm_license_plate' => trim($eq_license_plate),
							'eqm_current_operator' => trim($eq_current_operator)
						];
					}else{
						$row+=[
							'eqm_license_plate' => '',
							'eqm_current_operator' => 0
						];
					}



					if($eq_type!='Rental'){
						$row+=[
							'eqm_purchase_price' => trim($eq_purchase_price),
							'eqm_purchase_date' => trim($eq_purchase_date),
							'eqm_current_value' => trim($eq_current_value),
							'eqm_brand' => trim($eq_brand),
							'eqm_model' => trim($eq_model),
							'eqm_warranty_expiry_date' => trim($eq_war_expiry_date),
							'eqm_depreciation_method' => trim($eq_dep_method),
							'eqm_rental_total_value' => 0,
							'eqm_rental_insurance' => ''
						];
					}else{
						$row+=[
							'eqm_purchase_price' => 0,
							'eqm_purchase_date' => '',
							'eqm_current_value' => 0,
							'eqm_brand' => '',
							'eqm_model' => '',
							'eqm_warranty_expiry_date' => '',
							'eqm_depreciation_method' => '',
							'eqm_rental_total_value' => trim($eq_rental_total_value),
							'eqm_rental_insurance' => trim($eq_rental_insurance)
						];
					}

					if($eq_rental_insurance=='YES'){
						$row+=[
							'eqm_rental_insurance_amt' => trim($eq_rental_insurance_amt)
						];
					}else{
						$row+=[
							'eqm_rental_insurance_amt' => 0
						];
					}
					
					// $rowids = ;	
					if ($this->Equipment_Model->insertEquipment($row, 'eq_master'))
					{
						echo json_encode(array('msg' => 1, 's_msg' => ''));
					}
					else{
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Insert Data, Try Again.'));
					}
            }else{
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
		// 	exit;
		}else{
			redirect('default404');
		}
	}

	public function update_equipment_submission(){
		if($_POST){
			
			$eq_id = $this->input->post("eq_id");
			$eq_name = $this->input->post("eq_name");
			$eq_desc = $this->input->post("eq_desc");
			$eq_type = $this->input->post("eq_type");
			$eq_tag = $this->input->post("eq_tag");
			if(isset($_FILES['eq_picture'])){
				$eq_picture = $_FILES['eq_picture']['name'];
			}else{
				$eq_picture = '';
			}
			$eq_condition = $this->input->post("eq_condition");
			$eq_category = $this->input->post("eq_category");
			$eq_license_plate = $this->input->post("eq_license_plate");
			$eq_current_operator = $this->input->post("eq_current_operator");
			$eq_status = $this->input->post("eq_status");
			$eq_existing_reading = $this->input->post("eq_existing_reading");
			$eq_estimate_usage = $this->input->post("eq_estimate_usage");
			$eq_location = $this->input->post("eq_location");
			$eq_supplier = $this->input->post("eq_supplier");
			$eq_purchase_price = $this->input->post("eq_purchase_price");
			$eq_purchase_date = $this->input->post("eq_purchase_date");
			$eq_current_value = $this->input->post("eq_current_value");
			$eq_brand = $this->input->post("eq_brand");
			$eq_model = $this->input->post("eq_model");
			$eq_serial = $this->input->post("eq_serial");
			$eq_year = $this->input->post("eq_year");
			$eq_war_expiry_date = $this->input->post("eq_war_expiry_date");
			$eq_dep_method = $this->input->post("eq_dep_method");
			$eq_rental_total_value = $this->input->post("eq_rental_total_value");
			$eq_rental_insurance = $this->input->post("eq_rental_insurance");
			$eq_rental_insurance_amt = $this->input->post("eq_rental_insurance_amt");

			if(isset($_FILES['eq_picture'])){
				$filename = $_FILES['eq_picture']['name'];
			}else{
				$filename = '';
			}
			// $filename = $_FILES['eq_picture']['name'];

			if (!empty($filename)) {

				$config['upload_path'] = realpath('upload_file/equipment/');
				$config['allowed_types'] = 'jpg|png|jpeg|JPG|PNG|JPEG';
				$config['overwrite'] = TRUE;
				$config['remove_spaces'] = TRUE;
				$config['max_size'] = '5000';
				$config['file_name'] = date('His') . $filename;

				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				if ($this->upload->do_upload('eq_picture')) {
					$upload_data = $this->upload->data();

					$eq_picture = $upload_data['file_name'];
				}else {
					$this->data["error"] = $this->upload->display_errors();
				}
			} else {
			}

            $this->form_validation->set_rules('eq_name', 'Equipment Name', 'trim|required');
            $this->form_validation->set_rules('eq_desc', 'Equipment Description', 'trim|required');
            $this->form_validation->set_rules('eq_type', 'Equipment Type', 'trim|required');
            // $this->form_validation->set_rules('eq_tag', 'Equipment Tag', 'trim|required|unique');
            // $this->form_validation->set_rules('eq_condition', 'Equipment Condition', 'trim|required');
            // $this->form_validation->set_rules('eq_category', 'Equipment Category', 'trim|required');

			// if($eq_category=='Vehicles'){
			// 	$this->form_validation->set_rules('eq_license_plate', 'License Plate', 'trim|required');
			// 	$this->form_validation->set_rules('eq_current_operator', 'Current Operator', 'trim|required');
			// }
            // $this->form_validation->set_rules('eq_status', 'Equipment Status', 'trim|required');
            // $this->form_validation->set_rules('eq_existing_reading', 'Exiting Reading', 'trim|required');
            // $this->form_validation->set_rules('eq_estimate_usage', 'Estimate Usage', 'trim|required');
            // $this->form_validation->set_rules('eq_location', 'Location', 'trim|required');
            // $this->form_validation->set_rules('eq_supplier', 'Equipment Supplier', 'trim|required');

			// if($eq_type!='Rental'){
			// 	$this->form_validation->set_rules('eq_purchase_price', 'Equipment Purchase Price', 'trim|required');
			// 	$this->form_validation->set_rules('eq_purchase_date', 'Equipment Purchase Date', 'trim|required');
			// 	$this->form_validation->set_rules('eq_current_value', 'Equipment Current Value', 'trim|required');
			// 	$this->form_validation->set_rules('eq_brand', 'Equipment Brand', 'trim|required');
			// 	$this->form_validation->set_rules('eq_model', 'Equipment Model', 'trim|required');
			// 	$this->form_validation->set_rules('eq_war_expiry_date', 'Equipment Warrenty Expity Date', 'trim|required');
			// 	$this->form_validation->set_rules('eq_dep_method', 'Equipment Depreciation Method', 'trim|required');
			// }else{
			// 	$this->form_validation->set_rules('eq_rental_total_value', 'Rental Total Value', 'trim|required');
			// 	$this->form_validation->set_rules('eq_rental_insurance', 'Rental Insurance', 'trim|required');
			// }

			// if($eq_rental_insurance=='YES'){
			// 	$this->form_validation->set_rules('eq_rental_insurance_amt', 'Rental Insurance Amount', 'trim|required');
			// }
            // $this->form_validation->set_rules('eq_serial', 'Equipment Serial No', 'trim|required');
            // $this->form_validation->set_rules('eq_year', 'Equipment Year', 'trim|required');
			
			if ($this->form_validation->run() == TRUE) {
					
						$readingDetails=json_encode($this->db->get_where('eq_reading_record',['eq_id'=>$eq_id,'status'=>1])->result());
						$readingDetails=json_decode($readingDetails,true);
						$readingTotal=0;
						foreach($readingDetails as $reading){
							$readingTotal=($readingTotal+$reading['eqr_current_reading']);
						}
						$remainingLife=($eq_estimate_usage-$eq_existing_reading-$readingTotal);
						$row = array(
							'eqm_asset_name' => trim($eq_name),
							'eqm_asset_description' => trim($eq_desc),
							'eqm_asset_type' => trim($eq_type),
							'eqm_asset_tag' => trim($eq_tag),
							'eqm_asset_condition' => trim($eq_condition),
							'eqm_category' => trim($eq_category),
							'eqm_status' => trim($eq_status),
							'eqm_existing_reading' => trim($eq_existing_reading),
							'eqm_estimate_usage' => trim($eq_estimate_usage),
							'eqm_remaining_life' => trim($remainingLife),
							'eqm_location' => trim($eq_location),
							'eqm_supplier' => trim($eq_supplier),
							'eqm_serial' => trim($eq_serial),
							'eqm_year' => trim($eq_year),
							'eqm_created_date' => date('Y-m-d H:i:s')
						);

						if($eq_category=='Vehicles'){
							$row+=[
								'eqm_license_plate' => trim($eq_license_plate),
								'eqm_current_operator' => trim($eq_current_operator)
							];
						}else{
							$row+=[
								'eqm_license_plate' => '',
								'eqm_current_operator' => 0
							];
						}

						if($eq_type!='Rental'){
							$row+=[
								'eqm_purchase_price' => trim($eq_purchase_price),
								'eqm_purchase_date' => trim($eq_purchase_date),
								'eqm_current_value' => trim($eq_current_value),
								'eqm_brand' => trim($eq_brand),
								'eqm_model' => trim($eq_model),
								'eqm_warranty_expiry_date' => trim($eq_war_expiry_date),
								'eqm_depreciation_method' => trim($eq_dep_method),
								'eqm_rental_total_value' => 0,
								'eqm_rental_insurance' => ''
							];
						}else{
							$row+=[
								'eqm_purchase_price' => 0,
								'eqm_purchase_date' => '',
								'eqm_current_value' => 0,
								'eqm_brand' => '',
								'eqm_model' => '',
								'eqm_warranty_expiry_date' => '',
								'eqm_depreciation_method' => '',
								'eqm_rental_total_value' => trim($eq_rental_total_value),
								'eqm_rental_insurance' => trim($eq_rental_insurance)
							];
						}

						if($eq_rental_insurance=='YES'){
							$row+=[
								'eqm_rental_insurance_amt' => trim($eq_rental_insurance_amt)
							];
						}else{
							$row+=[
								'eqm_rental_insurance_amt' => 0
							];
						}

						if(!empty($filename)){
							$row+=[
								'eqm_asset_picture' => trim($eq_picture)
							];
						}
					if ($this->Equipment_Model->updateEquipment($row, 'eq_master',$eq_id))
					{
						echo json_encode(array('msg' => 1, 's_msg' => ''));
					}
					else{
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Insert Data, Try Again.'));
					}
            }else{
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
		}else{
			redirect('default404');
		}
	}

	public function new_component_submission(){
		if($_POST){
			
			$row_id = $this->input->post("row_id");
			$eq_id = $this->input->post("eq_id");
			$eqc_id = $this->input->post("eqc_id");
			$eqc_component_name = $this->input->post("eqc_component_name");
			$eqc_value = $this->input->post("eqc_value");
			$eqc_expected_life = $this->input->post("eqc_expected_life");
			$eqc_condition = $this->input->post("eqc_condition");

            $this->form_validation->set_rules('eqc_component_name', 'Equipment Component Name', 'trim|required');
            $this->form_validation->set_rules('eqc_value', 'Equipment Component Value', 'trim|required');
            $this->form_validation->set_rules('eqc_expected_life', 'Equipment Component Expected Life', 'trim|required');
            $this->form_validation->set_rules('eqc_condition', 'Equipment Component Condition', 'trim|required');
			
			if ($this->form_validation->run() == TRUE) {
					
					$row = array(
						'eq_id' => trim($eq_id),
						'eqc_component_name' => trim($eqc_component_name),
						'eqc_value' => trim($eqc_value),
						'eqc_expected_life' => trim($eqc_expected_life),
						'eqc_condition' => trim($eqc_condition)
					);
					
					if ($this->Equipment_Model->insertComponent($row, 'eq_component',json_decode($this->input->post("eqc_delete_ids")),$eqc_id))
					{
							echo json_encode(array('msg' => 1, 's_msg' => '', 'e_msg' => $this->input->post("eqc_delete_ids")));
					}
					else{
						echo json_encode(array('msg' => 2, 'e_msg' => 'There have some Problem to Insert Data, Try Again.'));
					}
            }else{
				echo json_encode(array('msg' => 3, 'e_msg' => validation_errors()));
			}
		// 	exit;
		}else{
			redirect('default404');
		}
	}

	public function new_maintenance_submission(){
		if($_POST){
			
			$eq_id = $this->input->post("eq_id");
			$eqm_type = $this->input->post("eqm_type");
			$eqm_cost = $this->input->post("eqm_cost");

            $this->form_validation->set_rules('eqm_type', 'Equipment Maintenance Type', 'trim|required');
            $this->form_validation->set_rules('eqm_cost', 'Equipment Maintenance Cost', 'trim|required');
			
			if ($this->form_validation->run() == TRUE) {
					
					$row = array(
						'eq_id' => trim($eq_id),
						'eqm_type' => trim($eqm_type),
						'eqm_cost' => trim($eqm_cost)
					);
					
					if ($this->Equipment_Model->insertMaintenance($row, 'eq_maintenance'))
					{
							echo json_encode(array('msg' => 1, 's_msg' => ''));
					}
					else{
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Insert Data, Try Again.'));
					}
            }else{
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
		// 	exit;
		}else{
			redirect('default404');
		}
	}

	public function get_details_of_equipments(){
		if($_POST){
			$eq_id = $this->input->post("eq_id");

			$this->form_validation->set_rules('eq_id', 'Equipment ID', 'trim|required|is_natural');

			if ($this->form_validation->run() == TRUE) {

				$eqdetailsets = $this->db->where(['eq_id'=>$eq_id,'status',1])->get('eq_master')->result();
				// $parr = array();
				// foreach($pdetailsets as $pditems){
				// 	$parr[] = $pditems->pdetail_user;
				// }

				$getrecord_detail = $this->db->where(['eq_id'=>$eq_id,'status'=>1])->get('eq_component')->result();
				$getrecord_detail1 = $this->db->where(['asset_id'=>$eq_id,'eqm_master.status'=>1])->join('eq_master','eqm_master.asset_id=eq_master.eq_id')->join('supplier_master','eqm_master.vendor_id=supplier_master.sup_id')->get('eqm_master')->result();
				$getrecord_detail2 = $this->db->where(['eq_id'=>$eq_id,'status',1])->order_by('eqh_created_date','DESC')->get('eq_history')->result();
				if (count((array)$eqdetailsets) > 0)
				{
					echo json_encode(array('msg' => 1, 's_msg' => $eqdetailsets, 'c_msg' => $getrecord_detail, 'm_msg' => $getrecord_detail1, 'h_msg' => $getrecord_detail2));
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

	//delete Equipment
	public function delete_equipment($id)
	{
		if($this->session->userdata('utype')==1 || $this->data['templateDetails']->pt_e_eq<2){
			$this->db->where('eq_id',$id);
			if($this->db->update('eq_master', array('status' => 0))) 
			{
				$this->db->where('eq_id',$id);
				if($this->db->update('eq_component', array('status' => 0))) {
					$this->db->where('asset_id',$id);
					if($this->db->update('eqm_master', array('status' => 0))) {
						$this->db->where('eq_id',$id);
						if($this->db->update('eq_history', array('status' => 0))) {
							$this->session->set_flashdata("success", "Record Deleted successfully");
							return redirect('admincontrol/equipments/all_equipment_list');
						}else {
							$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
							return redirect('admincontrol/equipments/all_equipment_list');
						}
					}else {
						$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
						return redirect('admincontrol/equipments/all_equipment_list');
					}
				}else {
					$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
					return redirect('admincontrol/equipments/all_equipment_list');
				}
			}else {
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
				return redirect('admincontrol/equipments/all_equipment_list');
			}
		}else{
			redirect('default404');
		}
	}

	//delete Maintenance
	public function delete_maintenance($id)
	{
		$this->db->where('eqm_id',$id);
		if($this->db->update('eqm_master', array('status' => 0))) 
		{
			$this->db->where('eqm_id',$id);
			if($this->db->update('eqm_details', array('status' => 0))) {
				$eq_id= $this->db->where('eqm_id',$id)->get('eqm_master')->row()->asset_id;
				$this->db->where('eq_id',$eq_id);
				if($this->db->update('eq_history', array('status' => 0))) {
					$this->session->set_flashdata("success", "Record Deleted successfully");
					return redirect('admincontrol/equipments/all_equipment_list');
				}else {
					$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
					return redirect('admincontrol/equipments/all_equipment_list');
				}
			}else {
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
				return redirect('admincontrol/equipments/all_equipment_list');
			}
		}else {
			$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
			return redirect('admincontrol/equipments/all_equipment_list');
		}
	}

	// New checkout submission
	public function new_checkout_submission(){
		if($_POST){
			
			$checkout_eq_id = $this->input->post("checkout_eq_id");
			$checkout_location_id = $this->input->post("checkout_location_id");
			$checkout_user_id = $this->input->post("checkout_user_id");
			$checkout_date = $this->input->post("checkout_date");

            $this->form_validation->set_rules('checkout_eq_id', 'Equipment Maintenance Type', 'trim|required');
            $this->form_validation->set_rules('checkout_location_id', 'Equipment Maintenance Cost', 'trim|required');
            $this->form_validation->set_rules('checkout_user_id', 'Equipment Maintenance Cost', 'trim|required');
            $this->form_validation->set_rules('checkout_date', 'Equipment Maintenance Cost', 'trim|required');
			
			if ($this->form_validation->run() == TRUE) {
					
					$row = array(
						'checkout_eq_id' => trim($checkout_eq_id),
						'checkout_location_id' => trim($checkout_location_id),
						'checkout_user_id' => trim($checkout_user_id),
						'checkout_date' => trim($checkout_date),
						'checkout_created_date' => date('Y-m-d H:i:s')
					);
					
					if ($this->Equipment_Model->insertCheckOut($row, 'eq_checkout'))
					{
						$this->db->where('eq_id',$checkout_eq_id);
						if($this->db->update('eq_master',['eqm_status'=>'In Use'])){

							if ($this->admin_m->get_Notification_SettingByKey('is_checkout_email')) {

								$template = $this->admin_m->GetEmailTemplateWithID($this->admin_m->get_Notification_SettingByKey('checkout_template'));
								$setting = $this->admin_m->get_CompanySMTP_Setting();

								$toEmail = [];

								$checkin_user = json_decode($this->admin_m->get_Notification_SettingByKey('notify_checkout_users'));
								if(count($checkin_user) > 0) {
									foreach($checkin_user as $key => $value) {
										$user = $this->admin_m->GetDetailsofUsers($value);
										if(isset($user)) {
											$toEmail[] = [
												'name' => $user->username,
												'email' => $user->email
											];
										}
									}
								}

								$params = [];

								if(isset($template->email_cc) && $template->email_cc != "") {
									$cc = [
										$template->email_cc
									];
								} else{
									$cc = [];
								}

								$data = $this->admin_m->prepareEmailBody($template->email_key, $params);

								$this->sendSMTPEmail($toEmail, $data['subject'], $data['content'], $cc, $setting);
							}

							echo json_encode(array('msg' => 1, 's_msg' => ''));
						}
					}
					else{
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Insert Data, Try Again.'));
					}
            }else{
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
		// 	exit;
		}else{
			redirect('default404');
		}
	}

	// New checkin submission
	public function new_checkin_submission(){
		if($_POST){
			
			$checkin_eq_id = $this->input->post("checkin_eq_id");
			$checkin_date = $this->input->post("checkin_date");

            $this->form_validation->set_rules('checkin_eq_id', 'Equipment Maintenance Type', 'trim|required');
            $this->form_validation->set_rules('checkin_date', 'Equipment Maintenance Cost', 'trim|required');
			
			if ($this->form_validation->run() == TRUE) {
					$checkoutDetails=$this->db->get_where('eq_checkout',['checkout_eq_id'=>$checkin_eq_id,'status'=>1])->row();
					$row = array(
						'checkin_eq_id' => trim($checkin_eq_id),
						'checkin_location_id' => trim($checkoutDetails->checkout_location_id),
						'checkin_user_id' => trim($checkoutDetails->checkout_user_id),
						'checkin_date' => trim($checkin_date),
						'checkin_created_date' => date('Y-m-d H:i:s')
					);
					
					if ($this->Equipment_Model->insertCheckIn($row, 'eq_checkin'))
					{
						$this->db->where('eq_id',$checkin_eq_id);
						if($this->db->update('eq_master',['eqm_status'=>'Available'])){

							if ($this->admin_m->get_Notification_SettingByKey('is_checkin_email')) {

								$template = $this->admin_m->GetEmailTemplateWithID($this->admin_m->get_Notification_SettingByKey('checkin_template'));
								$setting = $this->admin_m->get_CompanySMTP_Setting();

								$toEmail = [];

								$checkin_user = json_decode($this->admin_m->get_Notification_SettingByKey('notify_checkin_users'));
								if(count($checkin_user) > 0) {
									foreach($checkin_user as $key => $value) {
										$user = $this->admin_m->GetDetailsofUsers($value);
										if(isset($user)) {
											$toEmail[] = [
												'name' => $user->username,
												'email' => $user->email
											];
										}
									}
								}

								$params = [];

								if(isset($template->email_cc) && $template->email_cc != "") {
									$cc = [
										$template->email_cc
									];
								} else{
									$cc = [];
								}

								$data = $this->admin_m->prepareEmailBody($template->email_key, $params);

								$this->sendSMTPEmail($toEmail, $data['subject'], $data['content'], $cc, $setting);
							}
							echo json_encode(array('msg' => 1, 's_msg' => $row));
						}
					}
					else{
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Insert Data, Try Again.'));
					}
            }else{
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
		// 	exit;
		}else{
			redirect('default404');
		}
	}

	public function get_equipment_reading_details(){
		if($_POST){
			$eq_id = $this->input->post("eq_id");

			$this->form_validation->set_rules('eq_id', 'Equipment ID', 'trim|required|is_natural');

			if ($this->form_validation->run() == TRUE) {

				$eqdetailsets = $this->db->where(['eq_id'=>$eq_id,'status',1])->get('eq_master')->result();

				if (count((array)$eqdetailsets) > 0)
				{
					echo json_encode(array('msg' => 1, 's_msg' => $eqdetailsets));
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

	// update reading submission
	public function update_reading_submission(){
		if($_POST){
			
			$eq_id = $this->input->post("eq_id");
			$current_reading = $this->input->post("current_reading");
			$current_reading_date = $this->input->post("current_reading_date");

            $this->form_validation->set_rules('eq_id', 'Equipmentid', 'trim|required');
            $this->form_validation->set_rules('current_reading', 'Equipment Current Reading', 'trim|required');
            $this->form_validation->set_rules('current_reading_date', 'Equipment Reading Date', 'trim|required');
			
			if ($this->form_validation->run() == TRUE) {
					$row = array(
						'eq_id' => trim($eq_id),
						'eqr_current_reading' => trim($current_reading),
						'eqr_current_reading_date' => trim($current_reading_date),
						'eqr_created_date' => date('Y-m-d H:i:s')
					);
					
					if ($this->Equipment_Model->updateEquipmentReadingDetails($row, 'eq_reading_record'))
					{
						$this->db->set('eqm_remaining_life', 'eqm_remaining_life-'.$current_reading, FALSE);
						$this->db->where('eq_id',$eq_id);
						if($this->db->update('eq_master')){
							echo json_encode(array('msg' => 1, 's_msg' => $row));
						}
					}
					else{
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Insert Data, Try Again.'));
					}
            }else{
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
		// 	exit;
		}else{
			redirect('default404');
		}
	}


	
}
