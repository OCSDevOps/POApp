<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Packages extends Admin_Controller {
	
	 public function __construct() {
        parent::__construct();
		$this->load->model('Equipment_Model');
        //date_default_timezone_set("Asia/Kolkata");
        $this->data["u_details"] = $this->admin_m->GetDetailsofUsers($this->session->userdata['uid']);
		$this->data['templateDetails'] = $this->db->get_where('permission_master',['pt_id'=>$this->session->userdata('pt_id'),'status'=>1])->row();
    
	}
	
    public function index() {
		redirect('admincontrol/packages/all_package_list');
    }
    
    public function all_package_list(){
		$this->data['getrecord_list'] = $this->db->order_by('ipack_name','ASC')->join('project_master pm','pm.proj_id=ipm.ipack_project','left')->get('item_package_master ipm')->result();
		$this->load->view('admin/package/package_list_view', $this->data);
	}
	
	public function add_new_package(){
		if($this->session->userdata('utype')==1 || $this->data['templateDetails']->pt_i_itemp<3){
			$this->data['itm_list'] = $this->db->order_by('item_name','ASC')->where('item_status',1)->get('item_master')->result();
			$this->data['icat_list'] = $this->db->order_by('icat_id', 'ASC')->get('item_category_tab')->result();
			$this->data['project_list'] = $this->db->order_by('proj_id', 'ASC')->get('project_master')->result();
			$this->load->view('admin/package/add_package', $this->data);
		}else{
			redirect('default404');
		}
	}
	
	public function get_code_from_itemfind(){
		if($_POST){
			$pk_item = $this->input->post("pk_item");
            
            $this->form_validation->set_rules('pk_item', 'Item', 'trim|required');
            
			if ($this->form_validation->run() == TRUE) {
					
				$getresult = $this->admin_m->GetCCode_from_ItemCode_serch($pk_item);
				if(count((array)$getresult) > 0){
					echo json_encode(array('msg' => 1, 's_msg' => $getresult));
				}else{
					echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Update Details Data, Try Again.'));
				}
					
            }else{
				echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		}else{
			redirect('default404');
		}
	}
	
	public function new_package_item_submission(){
		if ($_POST) {
			$ipack_itm_no = $this->input->post("ipack_itm_no");
			$pk_item = $this->input->post("pk_item");
			$pk_ccode = $this->input->post("pk_ccode");
			$pk_itm_qnty = $this->input->post("pk_itm_qnty");
			
			$this->form_validation->set_rules('pk_item', 'Item', 'trim|required');
			$this->form_validation->set_rules('pk_ccode', 'CostCode', 'trim|required');
			$this->form_validation->set_rules('pk_itm_qnty', 'Quantity', 'trim|required|is_natural_no_zero');
			$this->form_validation->set_rules('ipack_itm_no', 'AUTOGEN ID', 'trim|required');

			if ($this->form_validation->run()) {
	  
			  if ($this->admin_m->check_Existing_Item_asperAUTOGEN_inDB($pk_item, $ipack_itm_no) == TRUE) {

				  $row_arr = array(
					'ipdetail_autogen' => $ipack_itm_no,
					'ipdetail_item_ms' => $pk_item,
					'ipdetail_quantity' => $pk_itm_qnty,
					'ipdetail_createdate' => date('Y-m-d H:i:s')
				  );

				  $resultset = $this->admin_m->addupdate_tempItem_inDB($row_arr);
				  if ($resultset != FALSE) {
					  $resultbunch = $this->admin_m->getDetails_Item_from_DB($resultset);
					  echo json_encode(array('msg' => 1, 'cat_set' => $resultbunch));
				  } else {
					echo json_encode(array('msg' => 0, 'e_msg' => 'DB insertion Problem, check again.'));
				  }

			  } else {
				echo json_encode(array('msg' => 0, 'e_msg' => 'Item already inserted, check again.'));
			  }
			} else {
			  echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
			}
			exit;
		} else {
			redirect('dafault404');
		}
    }
	
	public function delete_itemset_update()
    {
      if ($_POST) {
        $qid = $this->input->post("qid");
        $this->form_validation->set_rules('qid', 'ITEM Detail ID', 'trim|required|is_natural_no_zero');

        if ($this->form_validation->run()) {

          $resultrow = $this->db->get_where('item_package_details', array('ipdetail_id' => $qid))->row();
          if (count((array)$resultrow) > 0) {
              if ($this->db->delete('item_package_details', array('ipdetail_id' => $qid))) {
                echo json_encode(array('msg' => 1, 'expmarks' => $resultrow));
              } else {
                echo json_encode(array('msg' => 0, 'e_msg' => 'Data not Deleted from DB, check again.'));
              }
          } else {
            echo json_encode(array('msg' => 0, 'e_msg' => 'DB Data not found, check again.'));
          }
        } else {
          echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
        }
        exit;
      } else {
        redirect('dafault404');
      }
    }

	public function new_package_submission(){
		// if($_POST){
			
		// 	$ipack_itm_no = $this->input->post("ipack_itm_no");
		// 	$itemdtl_counter = $this->input->post("itemdtl_counter");
		// 	$itemdtl_qty = $this->input->post("itemdtl_qty");
		// 	$pkg_name = $this->input->post("pkg_name");
		// 	$pkg_detail = $this->input->post("pkg_detail");
            
        //     $this->form_validation->set_rules('ipack_itm_no', 'Autogen ID', 'trim|required');
        //     $this->form_validation->set_rules('itemdtl_counter', 'Item', 'trim|required|is_natural_no_zero');
        //     $this->form_validation->set_rules('itemdtl_qty', 'Quantity', 'trim|required|is_natural_no_zero');
        //     $this->form_validation->set_rules('pkg_name', 'Package Name', 'trim|required');
        //     $this->form_validation->set_rules('pkg_detail', 'Package Details', 'trim');
			
		// 	if ($this->form_validation->run() == TRUE) {
        //         	//echo "1st";
		// 		if($this->admin_m->check_package_set_nos_exist($pkg_name) == TRUE)
		// 		{			
		// 			//date_default_timezone_set("Asia/Kolkata");
		// 			if($pkg_detail != ""){$pkg_detail = trim($pkg_detail);}else{$pkg_detail = NULL;}
		// 			$row = array(
		// 					'ipack_name' => trim($pkg_name),
		// 					'ipack_details' => $pkg_detail,
		// 					'ipack_totalitem' => $itemdtl_counter,
		// 					'ipack_total_qty' => $itemdtl_qty,
		// 					'ipack_createdate' => date('Y-m-d H:i:s'),
		// 					'ipack_createby' => $this->session->userdata['uid']
		// 				);
					
		// 			$rowids = $this->admin_m->common_Insertion_in_DB_with_ID($row, 'item_package_master');	
		// 			if ($rowids != FALSE)
		// 			{
		// 				$detail_counter = 0;
		// 				$row2 = array(
		// 					'ipdetail_ipack_ms' => $rowids
		// 				);
		// 				if ($this->admin_m->common_Updation_in_DB($row2, 'item_package_details', 'ipdetail_autogen', $ipack_itm_no) == FALSE){
		// 					$detail_counter++;
		// 				}
		// 				if($detail_counter == 0){
		// 					echo json_encode(array('msg' => 1, 's_msg' => ''));
		// 				}else{
		// 					$this->db->delete('item_package_master', array('ipack_id' => $rowids));
		// 					//$this->db->delete('project_details', array('pdetail_proj_ms' => $rowids));
		// 					echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Insert Details Table Data, Try Again.'));
		// 				}
		// 			}
		// 			else{
		// 				echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Insert Data, Try Again.'));
		// 			}
		// 		}
		// 		else
		// 		{
		// 			echo json_encode(array('msg' => 0, 'e_msg' => 'Package already Exist, please check it.'));
		// 		}
        //     }else{
		// 		echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
		// 	}
		// 	exit;
		// }else{
		// 	redirect('default404');
		// }

		if($_POST){
			
			$pk_project = $this->input->post("pk_project");
			$pkg_name = $this->input->post("pkg_name");
			$pkg_detail = $this->input->post("pkg_detail");

			$totalQty=0;
			for($i=1;$i<=$this->input->post("row_count");$i++){
				${"pk_item".$i}=$this->input->post("pk_item".$i);
				${"pk_ccode".$i}=$this->input->post("pk_ccode".$i);
				${"pk_itm_qnty".$i}=$this->input->post("pk_itm_qnty".$i);
				$totalQty=($totalQty+${"pk_itm_qnty".$i});
			}

            $this->form_validation->set_rules('pkg_name', 'Package Name', 'trim|required');
            $this->form_validation->set_rules('pkg_detail', 'Package Details', 'trim|required');
		
			for($i=1;$i<=$this->input->post("row_count");$i++){
				$this->form_validation->set_rules("pk_item".$i, 'Required', 'trim|required');
				$this->form_validation->set_rules("pk_ccode".$i, 'Required', 'trim|required');
				$this->form_validation->set_rules("pk_itm_qnty".$i, 'Required', 'trim|required');
			}
			
			if ($this->form_validation->run() == TRUE) {
					
					$row = array(
						'ipack_project' => trim($pk_project),
						'ipack_name' => trim($pkg_name),
						'ipack_details' => trim($pkg_detail),
						'ipack_totalitem' => trim($this->input->post("row_count")),
						'ipack_total_qty' => trim($totalQty),
						'ipack_createdate' => date('Y-m-d H:i:s')
					);
					
					$result = $this->Equipment_Model->insertNewPackage($row, 'item_package_master');	
					if ($result!='FALSE')
					{
						for($i=1;$i<=$this->input->post("row_count");$i++){
							$row1 = array(
								'ipdetail_ipack_ms' => trim($result),
								'ipdetail_item_ms' => trim(${"pk_item".$i}),
								'ipdetail_quantity' => trim(${"pk_itm_qnty".$i}),
								'ipdetail_createdate' => date('Y-m-d H:i:s')
							);
							$this->Equipment_Model->insertNewPackageDetails($row1, 'item_package_details');
						}
						// if ($this->Equipment_Model->insertMaintenanceDetails($row1, 'eqm_details',$eqm_asset))
						// {
							echo json_encode(array('msg' => 1, 's_msg' => ''));
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
		// // 	exit;
		}else{
			redirect('default404');
		}
	}


	public function delete_itemset($id)
	{

		if($this->session->userdata('utype')==1 || $this->data['templateDetails']->pt_i_itemp<2){
			$resultrow = $this->db->get_where('item_package_master', array('ipack_id' => $id))->row();
			if ($resultrow) {
				if($this->db->delete('item_package_details', array('ipdetail_ipack_ms' => $resultrow->ipack_id))) {
					$this->db->delete('item_package_master', array('ipack_id' => $resultrow->ipack_id));
					$this->session->set_flashdata("success", "Record Deleted successfully");
					return redirect('admincontrol/packages/all_package_list');
				}
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
				return redirect('admincontrol/packages/all_package_list');
			} else {
				$this->session->set_flashdata("e_error", "There is some Problem. Please try again.");
				return redirect('admincontrol/packages/all_package_list');
			}
		}else{
			redirect('default404');
		}
	}

	public function lock_package_set($uid = NULL){
		if($this->session->userdata('utype')==1 || $this->data['templateDetails']->pt_i_itemp<3){
			if($uid == NULL){
				redirect('admincontrol/packages/all_package_list');
			}
			$row_arr = array(
				'ipack_status' => 0
			);
			if($this->admin_m->common_Updation_in_DB($row_arr,'item_package_master', 'ipack_id', $uid) == TRUE)
			{
				$this->session->set_flashdata("success","Record is Locked successfully");
				redirect('admincontrol/packages/all_package_list');
			}
			else
			{
				$this->session->set_flashdata("e_error","There is some Problem. Please try again.");
				redirect('admincontrol/packages/all_package_list');
			}
		}else{
			redirect('default404');
		}
	}
	
	public function unlock_package_set($uid = NULL){
		if($this->session->userdata('utype')==1 || $this->data['templateDetails']->pt_i_itemp<3){
			if($uid == NULL){
				redirect('admincontrol/packages/all_package_list');
			}
			$row_arr = array(
				'ipack_status' => 1
			);
			if($this->admin_m->common_Updation_in_DB($row_arr,'item_package_master', 'ipack_id', $uid) == TRUE)
			{
				$this->session->set_flashdata("success","Record is Unlocked successfully");
				redirect('admincontrol/packages/all_package_list');
			}
			else
			{
				$this->session->set_flashdata("e_error","There is some Problem. Please try again.");
				redirect('admincontrol/packages/all_package_list');
			}
		}else{
			redirect('default404');
		}
	}

	public function modify_package_submission(){
		if($_POST){
		// 	$pkg_id = $this->input->post("pkg_id");
		// 	$ipack_itm_no = $this->input->post("ipack_itm_no");
		// 	$itemdtl_counter = $this->input->post("itemdtl_counter");
		// 	$itemdtl_qty = $this->input->post("itemdtl_qty");
		// 	$pkg_name = $this->input->post("pkg_name");
		// 	$pkg_detail = $this->input->post("pkg_detail");
            
		// 	$this->form_validation->set_rules('pkg_id', 'Package ID', 'trim|required|is_natural_no_zero');
        //     $this->form_validation->set_rules('ipack_itm_no', 'Autogen ID', 'trim|required');
        //     $this->form_validation->set_rules('itemdtl_counter', 'Item', 'trim|required|is_natural_no_zero');
        //     $this->form_validation->set_rules('itemdtl_qty', 'Quantity', 'trim|required|is_natural_no_zero');
        //     $this->form_validation->set_rules('pkg_name', 'Package Name', 'trim|required');
        //     $this->form_validation->set_rules('pkg_detail', 'Package Details', 'trim');
			
		// 	if ($this->form_validation->run() == TRUE) {
        //         	//echo "1st";
		// 		if($this->admin_m->check_package_set_nos_exist($pkg_name, $pkg_id) == TRUE)
		// 		{			
		// 			//date_default_timezone_set("Asia/Kolkata");
		// 			if($pkg_detail != ""){$pkg_detail = trim($pkg_detail);}else{$pkg_detail = NULL;}
		// 			$row = array(
		// 					'ipack_name' => trim($pkg_name),
		// 					'ipack_details' => $pkg_detail,
		// 					'ipack_totalitem' => $itemdtl_counter,
		// 					'ipack_total_qty' => $itemdtl_qty,
		// 					'ipack_modifydate' => date('Y-m-d H:i:s'),
		// 					'ipack_modifyby' => $this->session->userdata['uid']
		// 				);
					
		// 			if ($this->admin_m->common_Updation_in_DB($row, 'item_package_master', 'ipack_id', $pkg_id) == TRUE)
		// 			{
		// 				$detail_counter = 0;
		// 				$row2 = array(
		// 					'ipdetail_ipack_ms' => $pkg_id
		// 				);
		// 				if ($this->admin_m->common_Updation_in_DB($row2, 'item_package_details', 'ipdetail_autogen', $ipack_itm_no) == FALSE){
		// 					$detail_counter++;
		// 				}
		// 				if($detail_counter == 0){
		// 					echo json_encode(array('msg' => 1, 's_msg' => ''));
		// 				}else{
		// 					echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Update Details Table Data, Try Again.'));
		// 				}
		// 			}
		// 			else{
		// 				echo json_encode(array('msg' => 0, 'e_msg' => 'There have some Problem to Update Data, Try Again.'));
		// 			}
		// 		}
		// 		else
		// 		{
		// 			echo json_encode(array('msg' => 0, 'e_msg' => 'Package already Exist, please check it.'));
		// 		}
        //     }else{
		// 		echo json_encode(array('msg' => 0, 'e_msg' => validation_errors()));
		// 	}
		// 	exit;

			$pkg_id = $this->input->post("pkg_id");
			$pk_project = $this->input->post("pk_project");
			$pkg_name = $this->input->post("pkg_name");
			$pkg_detail = $this->input->post("pkg_detail");

			$totalQty=0;
			for($i=1;$i<=$this->input->post("row_count");$i++){
				${"ipdetail_id".$i}=$this->input->post("ipdetail_id".$i);
				${"pk_item".$i}=$this->input->post("pk_item".$i);
				${"pk_ccode".$i}=$this->input->post("pk_ccode".$i);
				${"pk_itm_qnty".$i}=$this->input->post("pk_itm_qnty".$i);
				$totalQty=($totalQty+${"pk_itm_qnty".$i});
			}

			$this->form_validation->set_rules('pkg_name', 'Package Name', 'trim|required');
			$this->form_validation->set_rules('pkg_detail', 'Package Details', 'trim|required');
		
			for($i=1;$i<=$this->input->post("row_count");$i++){
				$this->form_validation->set_rules("pk_item".$i, 'Required', 'trim|required');
				$this->form_validation->set_rules("pk_ccode".$i, 'Required', 'trim|required');
				$this->form_validation->set_rules("pk_itm_qnty".$i, 'Required', 'trim|required');
			}
			
			if ($this->form_validation->run() == TRUE) {
					
					$row = array(
						'ipack_project' => trim($pk_project),
						'ipack_name' => trim($pkg_name),
						'ipack_details' => trim($pkg_detail),
						'ipack_totalitem' => trim($this->input->post("row_count")),
						'ipack_total_qty' => trim($totalQty),
						'ipack_modifydate' => date('Y-m-d H:i:s')
					);
					
					// $result = $this->Equipment_Model->updatePackage($row, 'item_package_master',$pkg_id, json_decode($this->input->post("pkg_delete_ids")));	
					if ($this->Equipment_Model->updatePackage($row, 'item_package_master',$pkg_id, json_decode($this->input->post("pkg_delete_ids"))))
					{
						for($i=1;$i<=$this->input->post("row_count");$i++){
							$row1 = array(
								'ipdetail_ipack_ms' => trim($pkg_id),
								'ipdetail_item_ms' => trim(${"pk_item".$i}),
								'ipdetail_quantity' => trim(${"pk_itm_qnty".$i})
							);
							$this->Equipment_Model->updatePackageDetails($row1, 'item_package_details',$pkg_id, ${"ipdetail_id".$i});
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
		}else{
			redirect('default404');
		}
	}
	
	public function modify_package_sets($prid){
		
		$this->data['pkg_list'] = $this->db->where('ipack_id',$prid)->get('item_package_master')->row();
		$this->data['icat_list'] = $this->db->order_by('icat_id', 'ASC')->get('item_category_tab')->result();
		$this->data['item_detailsets'] = $detailset = $this->admin_m->getDetails_Detail_ItemList_from_DB($prid);
		$this->data['project_list'] = $this->db->order_by('proj_id', 'ASC')->get('project_master')->result();
		foreach($detailset as $ditems){
			$autonuber = $ditems->ipdetail_autogen;
			break;
		}
		$this->data['at_no'] = $autonuber;
		$this->data['itm_list'] = $this->db->order_by('item_name','ASC')->where('item_status',1)->get('item_master')->result();
		$this->load->view('admin/package/edit_package', $this->data);
	}

	
}
