<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Permissions extends Admin_Controller {
	
	 public function __construct() {
        parent::__construct();
		$this->load->model('Equipment_Model');
        //date_default_timezone_set("Asia/Kolkata");
        $this->data["u_details"] = $this->admin_m->GetDetailsofUsers($this->session->userdata['uid']);
    
	}

    public function index() {
		redirect('admincontrol/equipments/all_equipment_list');
    }
    
	//Main view function
    public function all_permissions_list(){
		$this->data['getrecord_list'] = $this->db->where('status',1)->order_by('pt_id','DESC')->get('permission_master')->result();
		$this->data['users'] = $this->db->get_where('user_info',['u_type >' => '1'])->result();
		// $this->data['assets'] = $this->db->get('eq_master')->result();
		// $this->data['taxcodes'] = $this->db->get('taxgroup_master')->result();
		$this->load->view('admin/permission/permission_list_view', $this->data);
	}
	
	// Add New Permission Template
	public function new_permission_submission(){
		if($_POST){
			
			$pt_template_name = $this->input->post("pt_template_name");
			$pt_template_users = json_decode($this->input->post("pt_template_users"),true);

			$pt_t_porder = $this->input->post("pt_t_porder");
			$pt_t_rorder = $this->input->post("pt_t_rorder");
			$pt_t_rcorder = $this->input->post("pt_t_rcorder");
			$pt_t_rfq = $this->input->post("pt_t_rfq");

			$pt_m_item = $this->input->post("pt_m_item");
			$pt_m_uom = $this->input->post("pt_m_uom");
			$pt_m_costcode = $this->input->post("pt_m_costcode");
			$pt_m_projects = $this->input->post("pt_m_projects");
			$pt_m_suppliers = $this->input->post("pt_m_suppliers");
			$pt_m_taxgroup = $this->input->post("pt_m_taxgroup");
			$pt_m_budget = $this->input->post("pt_m_budget");
			$pt_m_email = $this->input->post("pt_m_email");

			$pt_i_item = $this->input->post("pt_i_item");
			$pt_i_itemp = $this->input->post("pt_i_itemp");
			$pt_i_supplierc = $this->input->post("pt_i_supplierc");

			$pt_e_eq = $this->input->post("pt_e_eq");
			$pt_e_eqm = $this->input->post("pt_e_eqm");
			$pt_e_checklist = $this->input->post("pt_e_checklist");
			
			$pt_a_user = $this->input->post("pt_a_user");
			$pt_a_permissions = $this->input->post("pt_a_permissions");
			$pt_a_cinfo = $this->input->post("pt_a_cinfo");
			$pt_a_procore = $this->input->post("pt_a_procore");

            $this->form_validation->set_rules('pt_template_name', 'Permission Template Name', 'trim|required');
            $this->form_validation->set_rules('pt_t_porder', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_t_rorder', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_t_rcorder', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_t_rfq', 'Required', 'trim|required');

            $this->form_validation->set_rules('pt_m_item', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_m_uom', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_m_costcode', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_m_projects', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_m_suppliers', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_m_taxgroup', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_m_budget', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_m_email', 'Required', 'trim|required');

            $this->form_validation->set_rules('pt_i_item', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_i_itemp', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_i_supplierc', 'Required', 'trim|required');

            $this->form_validation->set_rules('pt_e_eq', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_e_eqm', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_e_checklist', 'Required', 'trim|required');

            $this->form_validation->set_rules('pt_a_user', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_a_permissions', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_a_cinfo', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_a_procore', 'Required', 'trim|required');
			
			if ($this->form_validation->run() == TRUE) {
					
					$row = array(
						'pt_template_name' => trim($pt_template_name),
						'pt_template_users' => $this->input->post("pt_template_users"),
						'pt_t_porder' => trim($pt_t_porder),
						'pt_t_rorder' => trim($pt_t_rorder),
						'pt_t_rcorder' => trim($pt_t_rcorder),
						'pt_t_rfq' => trim($pt_t_rfq),
						'pt_m_item' => trim($pt_m_item),
						'pt_m_uom' => trim($pt_m_uom),
						'pt_m_costcode' => trim($pt_m_costcode),
						'pt_m_projects' => trim($pt_m_projects),
						'pt_m_suppliers' => trim($pt_m_suppliers),
						'pt_m_taxgroup' => trim($pt_m_taxgroup),
						'pt_m_budget' => trim($pt_m_budget),
						'pt_m_email' => trim($pt_m_email),
						'pt_i_item' => trim($pt_i_item),
						'pt_i_itemp' => trim($pt_i_itemp),
						'pt_i_supplierc' => trim($pt_i_supplierc),
						'pt_e_eq' => trim($pt_e_eq),
						'pt_e_eqm' => trim($pt_e_eqm),
						'pt_e_checklist' => trim($pt_e_checklist),
						'pt_a_user' => trim($pt_a_user),
						'pt_a_permissions' => trim($pt_a_permissions),
						'pt_a_cinfo' => trim($pt_a_cinfo),
						'pt_a_procore' => trim($pt_a_procore),
						'created_date' => date('Y-m-d H:i:s')
					);
					
					$result = $this->Equipment_Model->insertPermissions($row, 'permission_master');	
					if ($result!='FALSE')
					{
						foreach($pt_template_users as $user){
							$this->db->where('u_id',$user);
							$this->db->update('user_info',['pt_id'=>$result,'modify_date'=>date('Y-m-d H:i:s')]);
						}
						echo json_encode(array('msg' => 1));
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

	// Update Permission Template
	public function update_permission_submission(){
		if($_POST){
			
			$pt_id = $this->input->post("pt_id");
			$pt_template_name = $this->input->post("pt_template_name");
			$pt_template_users = json_decode($this->input->post("pt_template_users"),true);

			$pt_t_porder = $this->input->post("pt_t_porder");
			$pt_t_rorder = $this->input->post("pt_t_rorder");
			$pt_t_rcorder = $this->input->post("pt_t_rcorder");
			$pt_t_rfq = $this->input->post("pt_t_rfq");

			$pt_m_item = $this->input->post("pt_m_item");
			$pt_m_uom = $this->input->post("pt_m_uom");
			$pt_m_costcode = $this->input->post("pt_m_costcode");
			$pt_m_projects = $this->input->post("pt_m_projects");
			$pt_m_suppliers = $this->input->post("pt_m_suppliers");
			$pt_m_taxgroup = $this->input->post("pt_m_taxgroup");
			$pt_m_budget = $this->input->post("pt_m_budget");
			$pt_m_email = $this->input->post("pt_m_email");

			$pt_i_item = $this->input->post("pt_i_item");
			$pt_i_itemp = $this->input->post("pt_i_itemp");
			$pt_i_supplierc = $this->input->post("pt_i_supplierc");

			$pt_e_eq = $this->input->post("pt_e_eq");
			$pt_e_eqm = $this->input->post("pt_e_eqm");
			$pt_e_checklist = $this->input->post("pt_e_checklist");

			$pt_a_user = $this->input->post("pt_a_user");
			$pt_a_permissions = $this->input->post("pt_a_permissions");
			$pt_a_cinfo = $this->input->post("pt_a_cinfo");
			$pt_a_procore = $this->input->post("pt_a_procore");

            $this->form_validation->set_rules('pt_template_name', 'Permission Template Name', 'trim|required');
            $this->form_validation->set_rules('pt_t_porder', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_t_rorder', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_t_rcorder', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_t_rfq', 'Required', 'trim|required');

            $this->form_validation->set_rules('pt_m_item', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_m_uom', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_m_costcode', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_m_projects', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_m_suppliers', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_m_taxgroup', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_m_budget', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_m_email', 'Required', 'trim|required');

            $this->form_validation->set_rules('pt_i_item', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_i_itemp', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_i_supplierc', 'Required', 'trim|required');

            $this->form_validation->set_rules('pt_e_eq', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_e_eqm', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_e_checklist', 'Required', 'trim|required');

            $this->form_validation->set_rules('pt_a_user', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_a_permissions', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_a_cinfo', 'Required', 'trim|required');
            $this->form_validation->set_rules('pt_a_procore', 'Required', 'trim|required');
			
			if ($this->form_validation->run() == TRUE) {
					
					$row = array(
						'pt_template_name' => trim($pt_template_name),
						'pt_template_users' => $this->input->post("pt_template_users"),
						'pt_t_porder' => trim($pt_t_porder),
						'pt_t_rorder' => trim($pt_t_rorder),
						'pt_t_rcorder' => trim($pt_t_rcorder),
						'pt_t_rfq' => trim($pt_t_rfq),
						'pt_m_item' => trim($pt_m_item),
						'pt_m_uom' => trim($pt_m_uom),
						'pt_m_costcode' => trim($pt_m_costcode),
						'pt_m_projects' => trim($pt_m_projects),
						'pt_m_suppliers' => trim($pt_m_suppliers),
						'pt_m_taxgroup' => trim($pt_m_taxgroup),
						'pt_m_budget' => trim($pt_m_budget),
						'pt_m_email' => trim($pt_m_email),
						'pt_i_item' => trim($pt_i_item),
						'pt_i_itemp' => trim($pt_i_itemp),
						'pt_i_supplierc' => trim($pt_i_supplierc),
						'pt_e_eq' => trim($pt_e_eq),
						'pt_e_eqm' => trim($pt_e_eqm),
						'pt_e_checklist' => trim($pt_e_checklist),
						'pt_a_user' => trim($pt_a_user),
						'pt_a_permissions' => trim($pt_a_permissions),
						'pt_a_cinfo' => trim($pt_a_cinfo),
						'pt_a_procore' => trim($pt_a_procore),
						'modified_date' => date('Y-m-d H:i:s')
					);
					
					// $result = $this->Equipment_Model->updatePermissions($row, 'permission_master',$pt_id);	
					if ($this->Equipment_Model->updatePermissions($row, 'permission_master',$pt_id))
					{
							foreach($pt_template_users as $user){
								$this->db->where('u_id',$user);
								$this->db->update('user_info',['pt_id'=>$pt_id,'modify_date'=>date('Y-m-d H:i:s')]);
							}
							echo json_encode(array('msg' => 1));
					}
					else{
						echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Insert Data, Try Again.'));
					}
            }else{
				echo json_encode(array('msg' => $pt_id, 'e_msg' => validation_errors()));
			}
		// 	exit;
		}else{
			redirect('default404');
		}
	}

	// get permissions template details
	public function get_details_of_permissions(){
		if($_POST){
			$eq_id = $this->input->post("pt_id");

			$this->form_validation->set_rules('pt_id', 'Permissions ID', 'trim|required|is_natural');

			if ($this->form_validation->run() == TRUE) {

				$eqdetailsets = $this->db->where('pt_id',$eq_id)->get('permission_master')->result();
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

	//delete permission template
	public function delete_permission_template($id)
	{
		$this->db->where('pt_id',$id);
		if($this->db->update('permission_master', array('status' => 0))) {
			$this->session->set_flashdata("success", "Record Deleted successfully");
			return redirect('admincontrol/permissions/all_permissions_list');
		}else {
			$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
			return redirect('admincontrol/permissions/all_permissions_list');
		}
	}
	
}
