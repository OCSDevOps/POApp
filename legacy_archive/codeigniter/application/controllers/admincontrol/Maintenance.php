<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Maintenance extends Admin_Controller {
	
	 public function __construct() {
        parent::__construct();
		$this->load->model('Equipment_Model');
        //date_default_timezone_set("Asia/Kolkata");
        $this->data["u_details"] = $this->admin_m->GetDetailsofUsers($this->session->userdata['uid']);
		$this->data['templateDetails'] = $this->db->get_where('permission_master',['pt_id'=>$this->session->userdata('pt_id'),'status'=>1])->row();
    
	}

    public function index() {
		redirect('admincontrol/maintenance/all_miantenance_list');
    }
    
	//Main view function
    public function all_maintenance_list(){
		$this->data['getrecord_list'] = $this->db->where('eqm_master.status',1)->join('eq_master','eqm_master.asset_id=eq_master.eq_id')->join('supplier_master','eqm_master.vendor_id=supplier_master.sup_id')->order_by('eqm_id','DESC')->get('eqm_master')->result();
		$this->data['suppliers'] = $this->db->get('supplier_master')->result();
		$this->data['assets'] = $this->db->where('status',1)->get('eq_master')->result();
		$this->data['taxcodes'] = $this->db->get('taxgroup_master')->result();
		$this->load->view('admin/maintenance/maintenance_list_view', $this->data);
	}
	
	// Add New Maintenance
	public function new_maintenance_submission(){
		if($_POST){
			
			$eqm_asset = $this->input->post("eqm_asset");
			$eqm_supplier = $this->input->post("eqm_supplier");
			$eqm_service_date = $this->input->post("eqm_service_date");
			$eqm_service_type = $this->input->post("eqm_service_type");
			$eqm_notes = $this->input->post("eqm_notes");
			$eqm_total = $this->input->post("eqm_total");
			$eqm_attachment = $_FILES['eqm_attachment']['name'];

			for($i=1;$i<=$this->input->post("row_count");$i++){
				${"eqmd_description".$i}=$this->input->post("eqmd_description".$i);
				${"eqmd_notes".$i}=$this->input->post("eqmd_notes".$i);
				${"eqmd_taxcode".$i}=$this->input->post("eqmd_taxcode".$i);
				${"eqmd_qty".$i}=$this->input->post("eqmd_qty".$i);
				${"eqmd_rate".$i}=$this->input->post("eqmd_rate".$i);
				${"eqmd_pre_tax_amt".$i}=$this->input->post("eqmd_pre_tax_amt".$i);
				${"eqmd_tax_amt".$i}=$this->input->post("eqmd_tax_amt".$i);
			}

			$filename = $_FILES['eqm_attachment']['name'];

			if (!empty($filename)) {

				$config['upload_path'] = realpath('upload_file/maintenance/');
				$config['allowed_types'] = 'jpg|png|jpeg|JPG|PNG|JPEG';
				$config['overwrite'] = TRUE;
				$config['remove_spaces'] = TRUE;
				$config['max_size'] = '5000';
				$config['file_name'] = date('His') . $filename;

				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				if ($this->upload->do_upload('eqm_attachment')) {
					$upload_data = $this->upload->data();

					$eqm_attachment = $upload_data['file_name'];
				}else {
					$this->data["error"] = $this->upload->display_errors();
				}
			} else {
				$eq_picture = null;
			}

            $this->form_validation->set_rules('eqm_asset', 'Asset Name', 'trim|required');
            $this->form_validation->set_rules('eqm_supplier', 'Supplier', 'trim|required');
            $this->form_validation->set_rules('eqm_service_date', 'Service date', 'trim|required');
            $this->form_validation->set_rules('eqm_service_type', 'Service Type', 'trim|required');
            $this->form_validation->set_rules('eqm_notes', 'Notes', 'trim|required');
		
			for($i=1;$i<=$this->input->post("row_count");$i++){
				$this->form_validation->set_rules("eqmd_description".$i, 'Required', 'trim|required');
				$this->form_validation->set_rules("eqmd_notes".$i, 'Required', 'trim|required');
				$this->form_validation->set_rules("eqmd_taxcode".$i, 'Required', 'trim|required');
				$this->form_validation->set_rules("eqmd_qty".$i, 'Required', 'trim|required');
				$this->form_validation->set_rules("eqmd_rate".$i, 'Required', 'trim|required');
				$this->form_validation->set_rules("eqmd_pre_tax_amt".$i, 'Required', 'trim|required');
				$this->form_validation->set_rules("eqmd_tax_amt".$i, 'Required', 'trim|required');
			}
			
			if ($this->form_validation->run() == TRUE) {
					
					$row = array(
						'asset_id' => trim($eqm_asset),
						'vendor_id' => trim($eqm_supplier),
						'service_date' => trim($eqm_service_date),
						'service_type' => trim($eqm_service_type),
						'maintenance_notes' => trim($eqm_notes),
						'attachment' => trim($eqm_attachment),
						'maintenance_total' => trim($eqm_total),
						'created_date' => date('Y-m-d H:i:s')
					);
					
					$result = $this->Equipment_Model->insertNewMaintenance($row, 'eqm_master');	
					if ($result!='FALSE')
					{
						for($i=1;$i<=$this->input->post("row_count");$i++){
							$row1 = array(
								'eqm_id' => trim($result),
								'description' => trim(${"eqmd_description".$i}),
								'notes' => trim(${"eqmd_notes".$i}),
								'tax_code' => trim(${"eqmd_taxcode".$i}),
								'qty' => trim(${"eqmd_qty".$i}),
								'rate' => trim(${"eqmd_rate".$i}),
								'pre_tax_amount' => trim(${"eqmd_pre_tax_amt".$i}),
								'tax_amount' => trim(${"eqmd_tax_amt".$i}),
								'created_date' => date('Y-m-d H:i:s')
							);
							$this->Equipment_Model->insertMaintenanceDetails($row1, 'eqm_details',$eqm_asset);
						}

						if ($this->admin_m->get_Notification_SettingByKey('is_maintenance')) {

							$template = $this->admin_m->GetEmailTemplateWithID($this->admin_m->get_Notification_SettingByKey('maintenance_template'));
							$setting = $this->admin_m->get_CompanySMTP_Setting();

							$toEmail = [];

							$checkin_user = json_decode($this->admin_m->get_Notification_SettingByKey('notify_maintenance_user'));


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

							$cc = [
								$template->email_cc
							];

							$data = $this->admin_m->prepareEmailBody($template->email_key, $params);

							$this->sendSMTPEmail($toEmail, $data['subject'], $data['content'], $cc, $setting);
						}

						// if ($this->Equipment_Model->insertMaintenanceDetails($row1, 'eqm_details',$eqm_asset))
						// {
							echo json_encode(array('msg' => 1, 's_msg' => $row));
						// }
						// else{
						// 	echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Insert Data, Try Again.'));
						// }
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

	// Update Maintenance
	public function update_maintenance_submission(){
		if($_POST){
			
			$eqm_id = $this->input->post("eqm_id");
			$eqm_asset = $this->input->post("eqm_asset");
			$eqm_supplier = $this->input->post("eqm_supplier");
			$eqm_service_date = $this->input->post("eqm_service_date");
			$eqm_service_type = $this->input->post("eqm_service_type");
			$eqm_notes = $this->input->post("eqm_notes");
			$eqm_total = $this->input->post("eqm_total");
			if(isset($_FILES['eqm_attachment'])){
				$eqm_attachment = $_FILES['eqm_attachment']['name'];
			}else{
				$eqm_attachment = '';
			}

			for($i=1;$i<=$this->input->post("row_count");$i++){
				${"eqmd_id".$i}=$this->input->post("eqmd_id".$i);
				${"eqmd_description".$i}=$this->input->post("eqmd_description".$i);
				${"eqmd_notes".$i}=$this->input->post("eqmd_notes".$i);
				${"eqmd_taxcode".$i}=$this->input->post("eqmd_taxcode".$i);
				${"eqmd_qty".$i}=$this->input->post("eqmd_qty".$i);
				${"eqmd_rate".$i}=$this->input->post("eqmd_rate".$i);
				${"eqmd_pre_tax_amt".$i}=$this->input->post("eqmd_pre_tax_amt".$i);
				${"eqmd_tax_amt".$i}=$this->input->post("eqmd_tax_amt".$i);
			}

			if(isset($_FILES['eqm_attachment'])){
				$filename = $_FILES['eqm_attachment']['name'];
			}else{
				$filename = '';
			}

			if (!empty($filename)) {

				$config['upload_path'] = realpath('upload_file/maintenance/');
				$config['allowed_types'] = 'jpg|png|jpeg|JPG|PNG|JPEG';
				$config['overwrite'] = TRUE;
				$config['remove_spaces'] = TRUE;
				$config['max_size'] = '5000';
				$config['file_name'] = date('His') . $filename;

				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				if ($this->upload->do_upload('eqm_attachment')) {
					$upload_data = $this->upload->data();

					$eqm_attachment = $upload_data['file_name'];
				}else {
					$this->data["error"] = $this->upload->display_errors();
				}
			} else {
				$eqm_attachment = null;
			}

            $this->form_validation->set_rules('eqm_asset', 'Asset Name', 'trim|required');
            $this->form_validation->set_rules('eqm_supplier', 'Supplier', 'trim|required');
            $this->form_validation->set_rules('eqm_service_date', 'Service date', 'trim|required');
            $this->form_validation->set_rules('eqm_service_type', 'Service Type', 'trim|required');
            $this->form_validation->set_rules('eqm_notes', 'Notes', 'trim|required');
		
			for($i=1;$i<=$this->input->post("row_count");$i++){
				$this->form_validation->set_rules("eqmd_description".$i, 'Required', 'trim|required');
				$this->form_validation->set_rules("eqmd_notes".$i, 'Required', 'trim|required');
				$this->form_validation->set_rules("eqmd_taxcode".$i, 'Required', 'trim|required');
				$this->form_validation->set_rules("eqmd_qty".$i, 'Required', 'trim|required');
				$this->form_validation->set_rules("eqmd_rate".$i, 'Required', 'trim|required');
				$this->form_validation->set_rules("eqmd_pre_tax_amt".$i, 'Required', 'trim|required');
				$this->form_validation->set_rules("eqmd_tax_amt".$i, 'Required', 'trim|required');
			}
			
			if ($this->form_validation->run() == TRUE) {
					
					$row = array(
						'asset_id' => trim($eqm_asset),
						'vendor_id' => trim($eqm_supplier),
						'service_date' => trim($eqm_service_date),
						'service_type' => trim($eqm_service_type),
						'maintenance_notes' => trim($eqm_notes),
						'maintenance_total' => trim($eqm_total),
						'modified_date' => date('Y-m-d H:i:s')
					);

					if(isset($_FILES['eqm_attachment'])){
						$row+= [
							'attachment' => trim($eqm_attachment),
						];
					}
					
					// $result = $this->Equipment_Model->updateMaintenance($row, 'eqm_master',$eqm_id);	
					if ($this->Equipment_Model->updateMaintenance($row, 'eqm_master',$eqm_id, json_decode($this->input->post("eqmd_delete_ids"))))
					{
						for($i=1;$i<=$this->input->post("row_count");$i++){
							$row1 = array(
								'eqm_id' => trim($eqm_id),
								'description' => trim(${"eqmd_description".$i}),
								'notes' => trim(${"eqmd_notes".$i}),
								'tax_code' => trim(${"eqmd_taxcode".$i}),
								'qty' => trim(${"eqmd_qty".$i}),
								'rate' => trim(${"eqmd_rate".$i}),
								'pre_tax_amount' => trim(${"eqmd_pre_tax_amt".$i}),
								'tax_amount' => trim(${"eqmd_tax_amt".$i})
							);
							$this->Equipment_Model->updateMaintenanceDetails($row1, 'eqm_details',$eqm_asset,${"eqmd_id".$i});
						}
						// if ($this->Equipment_Model->insertMaintenanceDetails($row1, 'eqm_details',$eqm_asset))
						// {
							echo json_encode(array('msg' => 1, 's_msg' => $row));
						// }
						// else{
						// 	echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Insert Data, Try Again.'));
						// }
					}
					else{
						echo json_encode(array('msg' => 0, 'e_msg' => $result));
					}
            }else{
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
		// 	exit;
		}else{
			redirect('default404');
		}
	}

	// get maintenance details
	public function get_details_of_maintenance(){
		if($_POST){
			$eq_id = $this->input->post("eqm_id");

			$this->form_validation->set_rules('eqm_id', 'Equipment ID', 'trim|required|is_natural');

			if ($this->form_validation->run() == TRUE) {

				$eqdetailsets = $this->db->where(['eqm_id'=>$eq_id,'status',1])->get('eqm_master')->result();
				// $parr = array();
				// foreach($pdetailsets as $pditems){
				// 	$parr[] = $pditems->pdetail_user;
				// }

				$getrecord_detail = $this->db->where(['eqm_id'=>$eq_id,'status'=>1])->get('eqm_details')->result();
				// $getrecord_detail1 = $this->db->where(['asset_id'=>$eq_id,'status',1])->join('eq_master','eqm_master.asset_id=eq_master.eq_id')->join('supplier_master','eqm_master.vendor_id=supplier_master.sup_id')->get('eqm_master')->result();
				// $getrecord_detail2 = $this->db->where(['eq_id'=>$eq_id,'status',1])->order_by('eqh_created_date','DESC')->get('eq_history')->result();
				if (count((array)$eqdetailsets) > 0)
				{
					echo json_encode(array('msg' => 1, 's_msg' => $eqdetailsets, 'c_msg' => $getrecord_detail));
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

	//delete Maintenance
	public function delete_maintenance($id)
	{
		if($this->session->userdata('utype')==1 || $this->data['templateDetails']->pt_e_eqm<2){
			$this->db->where('eqm_id',$id);
			if($this->db->update('eqm_master', array('status' => 0))) 
			{
				$this->db->where('eqm_id',$id);
				if($this->db->update('eqm_details', array('status' => 0))) {
					$eq_id= $this->db->where('eqm_id',$id)->get('eqm_master')->row()->asset_id;
					$this->db->where('eq_id',$eq_id);
					if($this->db->update('eq_history', array('status' => 0))) {
						$this->session->set_flashdata("success", "Record Deleted successfully");
						return redirect('admincontrol/maintenance/all_maintenance_list');
					}else {
						$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
						return redirect('admincontrol/maintenance/all_maintenance_list');
					}
				}else {
					$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
					return redirect('admincontrol/maintenance/all_maintenance_list');
				}
			}else {
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
				return redirect('admincontrol/maintenance/all_maintenance_list');
			}
		}else{
			redirect('default404');
		}
	}
	
}
