<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Template extends Admin_Controller {
	
	 public function __construct() {
        parent::__construct();
        //date_default_timezone_set("Asia/Kolkata");
		$this->data['templateDetails'] = $this->db->get_where('permission_master',['pt_id'=>$this->session->userdata('pt_id'),'status'=>1])->row();
	}
	
    public function index() {
		redirect('admincontrol/template/template_list');
    }
    
    public function template_list() {
	 			$this->data['getrecord_list'] = $this->db->order_by('id','DESC')->get_where('template_master')->result();
		$this->load->view('admin/template/template_list_view', $this->data);
	}

	public function add_new_template(){
		if($_POST){
			$email_name = $this->input->post("email_name");
			$email_key = $this->input->post("email_key");
			$email_body = $this->input->post("email_body");
			$email_cc = $this->input->post("email_cc");
			$email_subject = $this->input->post("email_subject");
			$email_bcc = $this->input->post("email_bcc");

			$this->form_validation->set_rules('email_name', 'Email Name', 'trim|required');
			$this->form_validation->set_rules('email_key', 'Email Key', 'trim|required');
			$this->form_validation->set_rules('email_body', 'Email Body', 'trim|required');

			if ($this->form_validation->run() == TRUE) {
				//echo "1st";
				if($this->admin_m->check_template_exist($email_key) == TRUE)
				{
					$row = array(
						'email_name' => trim($email_name),
						'email_key' => trim($email_key),
						'email_body' => trim($email_body),
						'email_subject' => trim($email_subject),
						'email_cc' => trim($email_cc),
						'email_bcc' => trim($email_bcc),
						'created_at' => date('Y-m-d H:i:s'),
					);

					if ($this->admin_m->common_Insertion_in_DB($row, 'template_master') == TRUE)
					{
						echo json_encode(array('msg' => 1, 's_msg' => ''));
					}
					else{
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Insert Data, Try Again.'));
					}
				}
				else
				{
					echo json_encode(array('msg' => 0, 'e_msg' => 'Template already Exist, please check it.'));
				}
			}else{
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		}else{
			redirect('default404');
		}
	}

	public function get_details_of_template(){
		if($_POST){
			$id = $this->input->post("id");

			$this->form_validation->set_rules('id', 'Template ID', 'trim|required|is_natural');

			if ($this->form_validation->run() == TRUE) {

				$getrecord_detail = $this->db->where('id',$id)->get('template_master')->row();
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

	public function modify_email(){
		if($_POST){
			$update_id_template = $this->input->post("update_id_template");
			$update_email_name = $this->input->post("update_email_name");
			$update_email_key = $this->input->post("update_email_key");
			$update_email_body = $this->input->post("update_email_body");
			$update_email_cc = $this->input->post("update_email_cc");
			$update_email_bcc = $this->input->post("update_email_bcc");
			$update_email_subject = $this->input->post("update_email_subject");

			$this->form_validation->set_rules('update_id_template', 'Email Template ID', 'trim|required');
			$this->form_validation->set_rules('update_email_name', 'Email name', 'trim|required');
			$this->form_validation->set_rules('update_email_key', 'Email Key', 'trim|required');
			$this->form_validation->set_rules('update_email_body', 'Email Body', 'trim|required');

			if ($this->form_validation->run() == TRUE) {
				//echo "1st";
				if($this->admin_m->check_template_exist($update_email_key, $update_id_template) == TRUE)
				{
					//date_default_timezone_set("Asia/Kolkata");

					$row = array(
						'email_name' => trim($update_email_name),
						'email_body' => trim($update_email_body),
						'email_subject' => trim($update_email_subject),
						'email_cc' => trim($update_email_cc),
						'email_bcc' => trim($update_email_bcc),
					);

					if ($this->admin_m->common_Updation_in_DB($row, 'template_master', 'id', $update_id_template) == TRUE)
					{
						echo json_encode(array('msg' => 1, 's_msg' => ''));
					}
					else{
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Update Data, Try Again.'));
					}
				}
				else
				{
					echo json_encode(array('msg' => 0, 'e_msg' => 'Email Template not Exist, please check it.'));
				}
			}else{
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		}else{
			redirect('default404');
		}
	}

	public function delete_record($id) {

		if($this->session->userdata('utype')==1 || $this->data['templateDetails']->pt_m_suppliers<2){

			$resultrow = $this->db->get_where('template_master', array('id' => $id))->row();
			if ($resultrow) {
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
				$res = $this->admin_m->checkNotificationUsage('notification_setting_master', 'checkin_template', $id);
				$res2 = $this->admin_m->checkNotificationUsage('notification_setting_master', 'checkout_template', $id);
				$res3 = $this->admin_m->checkNotificationUsage('notification_setting_master', 'checklist_assigned_template', $id);
				$res4 = $this->admin_m->checkNotificationUsage('notification_setting_master', 'checklist_performed_template', $id);
				$res5 = $this->admin_m->checkNotificationUsage('notification_setting_master', 'maintenance_template', $id);
				$res6 = $this->admin_m->checkNotificationUsage('notification_setting_master', 'price_expiry_template', $id);
				$res7 = $this->admin_m->checkNotificationUsage('notification_setting_master', 'item_approval_template', $id);
				$res8 = $this->admin_m->checkNotificationUsage('notification_setting_master', 'purchase_order_template', $id);
				$res9 = $this->admin_m->checkNotificationUsage('notification_setting_master', 'rfq_order_template', $id);
				$res10 = $this->admin_m->checkNotificationUsage('notification_setting_master', 'receive_order_template', $id);
				$res11 = $this->admin_m->checkNotificationUsage('notification_setting_master', 'procore_template', $id);
				$res12 = $this->admin_m->checkNotificationUsage('notification_setting_master', 'new_user_template', $id);
				$res13 = $this->admin_m->checkNotificationUsage('notification_setting_master', 'forgot_password_template', $id);


				if (!$res && !$res2 && !$res3 && !$res4 && !$res5 && !$res6 && !$res7 && !$res8 && !$res9 && !$res10 && !$res11 && !$res12 && !$res13) {
					if($this->db->delete('template_master', array('id' => $resultrow->id))) {
						$this->session->set_flashdata("success", "Record Deleted successfully");
						return redirect('admincontrol/template/template_list');
					}
				} else {
					$this->session->set_flashdata("e_error", "This template is linked with notification settings, please unlink before delete.");
				}

				return redirect('admincontrol/template/template_list');
			} else {
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
				return redirect('admincontrol/template/template_list');
			}

		}else{
			redirect('default404');
		}
	}
}
