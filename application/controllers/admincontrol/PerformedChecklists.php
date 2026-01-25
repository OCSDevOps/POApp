<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class PerformedChecklists extends Admin_Controller {
	
	 public function __construct() {
        parent::__construct();
		$this->load->model('Equipment_Model');
        //date_default_timezone_set("Asia/Kolkata");
        $this->data["u_details"] = $this->admin_m->GetDetailsofUsers($this->session->userdata['uid']);
		$this->data['templateDetails'] = $this->db->get_where('permission_master',['pt_id'=>$this->session->userdata('pt_id'),'status'=>1])->row();
    
	}

    public function index() {
		redirect('admincontrol/perform_checklist/perform_checklist_list_view');
    }
    
	//Main view function
    public function all_checklist_list(){
		$this->data['getrecord_list'] = $this->db->where('clp.status',1)->join('checklist_master cl', 'cl.cl_id=clp.cl_id')->join('eq_master eq','eq.eq_id=clp.cl_eq_id')->order_by('cl_p_id','DESC')->get('cl_perform_master clp')->result();
		$this->data['users'] = $this->db->get_where('user_info',['u_type >' => '1'])->result();
		$this->data['assets'] = $this->db->get_where('eq_master',['status'=>1])->result();
		// $this->data['taxcodes'] = $this->db->get('taxgroup_master')->result();
		$this->load->view('admin/performed_checklists/performed_checklists_list_view', $this->data);
	}

	// get checklist items
	public function get_perform_checklist_items(){
		if($_POST){
			$cl_id = $this->input->post("cl_p_id");

			$this->form_validation->set_rules('cl_p_id', 'Checklist ID', 'trim|required|is_natural');

			if ($this->form_validation->run() == TRUE) {

				$eqdetailsets = $this->db->where(['cl_p_id'=>$cl_id,'status'=>1])->get('cl_perform_master')->row();
				$eqdetailsets1 = $this->db->where(['cl_p_id'=>$cl_id,'clpd.status'=>1])->join('checklist_details cd','cd.cli_id=clpd.cl_pd_cli_id')->get('cl_perform_details clpd')->result();
				// $parr = array();
				// foreach($pdetailsets as $pditems){
				// 	$parr[] = $pditems->pdetail_user;
				// }

				// $getrecord_detail = $this->db->where(['cl_id'=>$cl_id,'status'=>1])->get('checklist_details')->result();
				// $getrecord_detail1 = $this->db->where(['asset_id'=>$eq_id,'status',1])->join('eq_master','eqm_master.asset_id=eq_master.eq_id')->join('supplier_master','eqm_master.vendor_id=supplier_master.sup_id')->get('eqm_master')->result();
				// $getrecord_detail2 = $this->db->where(['eq_id'=>$eq_id,'status',1])->order_by('eqh_created_date','DESC')->get('eq_history')->result();
				if (count((array)$eqdetailsets1) > 0)
				{
					echo json_encode(array('msg' => 1, 's_msg' => $eqdetailsets, 'cl_msg' => $eqdetailsets1));
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


	public function print_performed_checklist_setpdf($cl_p_id)
	{
		$attData['checklist_perform'] = $getPermormedChecklistData = $this->db->get_where('cl_perform_master',['cl_p_id'=>$cl_p_id,'status'=>1])->row();
		$attData['checklist'] = $checklist = $this->db->get_where('checklist_master',['cl_id'=>$getPermormedChecklistData->cl_id,'status'=>1])->row();
		$attData['checklist_perform_detailsets'] = $this->db->join('checklist_details','checklist_details.cli_id=cl_perform_details.cl_pd_cli_id')->get_where('cl_perform_details',['cl_perform_details.cl_p_id'=>$cl_p_id,'cl_perform_details.status'=>1])->result();
		$attData['checklist_user'] = $this->db->select('*')->from('user_info')->where('u_id',$this->session->userdata('uid'))->get()->row();
		$attData['checklist_equipment'] = $this->db->select('*')->from('eq_master')->where('eq_id',$getPermormedChecklistData->cl_eq_id)->get()->row();
		$attData['company'] = $this->admin_m->getCompanySetting();
		$title = $attData['checklist']->cl_name;

		return $this->admin_m->showPOPDF('checklist_perform', $attData,$title);

	}

	
}
