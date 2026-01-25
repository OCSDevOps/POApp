<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Cmspage extends Admin_Controller{
	 public function __construct() { 
	 			parent::__construct(); 
			date_default_timezone_set("Asia/Kolkata");			
			$this->data["u_details"] = $this->admin_m->GetDetailsofUsers($this->session->userdata['uid']);
			$this->load->model('Cmspage_m');
			
		}
	public function index(){
			$this->data['page_detail']= $this->Cmspage_m->page_list();
			$this->load->view('admin/page/all_page_list',$this->data);
	}

	public function edit_page($link){
			
			if($_POST){
				$data['page_title'] = $this->input->post('title');
				$data['page_title_bengali'] = $this->input->post('title_bengali');
				$data['page_details'] = $this->input->post('details',FALSE);
				$data['page_details_bengali'] = $this->input->post('details_bengali',FALSE);
				$msg = NULL;
				if (($data['page_title']== '') || (strpos($data['page_title'], '<script>') !== false) || (strpos($data['page_title'], '[removed]') !== false) || (strpos($data['page_title'],'</script>') !== false)){$msg = "Enter Correct Title";	}
				if ((strpos($data['page_title_bengali'], '<script>') !== false) || (strpos($data['page_title_bengali'], '[removed]') !== false) || (strpos($data['page_title_bengali'],'</script>') !== false)){$msg = "Enter Correct Bengali Title";	}
				if ((strpos($data['page_details'], '<script>') !== false) || (strpos($data['page_details'], '[removed]') !== false) || (strpos($data['page_details'],'</script>') !== false)){ if($msg == NULL){  $msg = "Enter Correct Page Contents "; }else{ $msg .= "<br>Enter Correct Page Contents "; }	}
				if ((strpos($data['page_details_bengali'], '<script>') !== false) || (strpos($data['page_details_bengali'], '[removed]') !== false) || (strpos($data['page_details_bengali'],'</script>') !== false)){ if($msg == NULL){  $msg = "Enter Correct Bengali Contents "; }else{ $msg .= "<br>Enter Correct Bengali Contents "; }	}
				if($msg == NULL){
					$data['page_modify_date'] = date('Y-m-d H:i:s');
					$data['page_modify_by'] = $this->session->userdata['uid']; //$_SERVER['REMOTE_ADDR'];
					if($this->Cmspage_m->page_update($data,$link)){
						$this->session->set_flashdata("success","Page Content is Updated successfully");
						redirect('admincontrol/Cmspage','refresh');
					}else{
						$this->data['msg'] = "There is a problem in server side, Please try after some time......"; 	
					}
				}else{
					$this->data['msg'] = $msg; 
				}
			}
			$this->data['details']= $this->Cmspage_m->page_details($link);
			$this->load->view('admin/page/edit_page',$this->data);
	
	}
	
	public function add_new_page(){
			
			if($_POST){
				
			$data['page_title'] = $this->input->post('title');
			$data['page_title_bengali'] = $this->input->post('title_bengali');
			$data['page_details'] = $this->input->post('details',FALSE);
			$data['page_details_bengali'] = $this->input->post('details_bengali',FALSE);
			$msg = NULL;
			if (($data['page_title']== '') || (strpos($data['page_title'], '<script>') !== false) || (strpos($data['page_title'], '[removed]') !== false) || (strpos($data['page_title'],'</script>') !== false)){$msg = "Enter Correct Title";	}
			if ((strpos($data['page_title_bengali'], '<script>') !== false) || (strpos($data['page_title_bengali'], '[removed]') !== false) || (strpos($data['page_title_bengali'],'</script>') !== false)){$msg = "Enter Correct Bengali Title";	}
			if ((strpos($data['page_details'], '<script>') !== false) || (strpos($data['page_details'], '[removed]') !== false) || (strpos($data['page_details'],'</script>') !== false)){ if($msg == NULL){  $msg = "Enter Correct Page Contents "; }else{ $msg .= "<br>Enter Correct Page Contents "; }	}
			if ((strpos($data['page_details_bengali'], '<script>') !== false) || (strpos($data['page_details_bengali'], '[removed]') !== false) || (strpos($data['page_details_bengali'],'</script>') !== false)){ if($msg == NULL){  $msg = "Enter Correct Bengali Contents "; }else{ $msg .= "<br>Enter Correct Bengali Contents "; }	}
			 
			if($msg == NULL){
			$random = substr(md5(mt_rand()), 0, 6);
			$result_string  = preg_replace('/[^a-zA-Z0-9_ -]/s','',$data['page_title']);
   			$str = str_replace(" ","_",$result_string);
			$data['url_link'] = trim($str).$random;
			
			$data['page_create_date'] = date('Y-m-d H:i:s');
			$data['page_create_by'] = $this->session->userdata['uid']; //$_SERVER['REMOTE_ADDR'];
			 	if($this->Cmspage_m->add_new_page($data)){
			 		$this->session->set_flashdata("success","New Page Content is Upload successfully");
		     		redirect('admincontrol/Cmspage','refresh');
				}else{
					$this->data['msg'] = "There is a problem in server side, Please try after some time......"; 	
				}
			 }else{
				$this->data['msg'] = $msg; 
			 }
			 
			}
			
			$this->load->view('admin/page/add_new_page',$this->data);
	
	}

	public function view_upload_list(){
		$this->data['image'] = $this->db->Order_by('up_id','desc')->get_where('uploadfile_tab',array('up_type'=>1))->result();
		$this->data['doc'] = $this->db->Order_by('up_id','desc')->get_where('uploadfile_tab',array('up_type'=>2))->result();
		
		$this->load->view('admin/page/view_upload_list',$this->data);
	
	}


	public function upload_file_list(){
		$this->data['view'] = $this->Cmspage_m->file_list();
		
		$this->load->view('admin/page/upload_file_list',$this->data);
	}

	public function upload_doc_entry(){

		if($_POST){
			//print_r($filename);exit;
			$filename = $_FILES['userfile']['name'];
			if(!empty($filename)){

				$extension = pathinfo($filename, PATHINFO_EXTENSION);
				
				$ext_list= array('jpg','png','jpeg','JPG','JPEG','PNG','gif','icon');
				if(in_array($extension, $ext_list))
				{
					$data_upload['up_type'] = 1;
				}else{
					$data_upload['up_type']= 2;
				}
			   
	   
			   $this->load->library('upload');
			   $this->load->library('image_lib');
			   
			   $config['upload_path'] =realpath('upload_file/file_doc/'); 
			   $config['allowed_types'] = 'pdf|PDF|docx|doc|txt|csv|xlsx|jpg|png|jpeg|JPG|JPEG|PNG|gif|GIF|icon';
			   $config['overwrite'] = FALSE;
			   $config['remove_spaces'] = TRUE;
			   $config['file_name'] = $filename;
			   
			   $this->load->library('upload', $config);
			   $this->upload->initialize($config);
			   
				if($this->upload->do_upload()){
					$upload_data = $this->upload->data();
					$data_upload['up_file'] = $upload_data['file_name'];
					$data_upload['up_date'] = date('Y-m-d H:i:s');
					
					
					if($data_upload['up_type'] == 1){
						if($upload_data['image_width'] >= $upload_data['image_height'] && $upload_data['image_width'] > 900){
							$resize_conf = array(
								'source_image' => $upload_data['full_path'],
								'new_image' => 'upload_file/file_doc/',
								'overwrite' => true,
								'width' => 900
							);
							$this->image_lib->initialize($resize_conf);
							$this->image_lib->resize();
							
						}
						elseif($upload_data['image_height'] >= $upload_data['image_width'] && $upload_data['image_height'] > 900){
							
							$resize_conf = array(
								'source_image' => $upload_data['full_path'],
								'new_image' => 'upload_file/file_doc/',
								'overwrite' => true,
								'height' => 900
							);
							$this->image_lib->initialize($resize_conf);
							$this->image_lib->resize();
						}
					}
					
					if($this->Cmspage_m->upload_doc_entry($data_upload) == TRUE){
						$this->session->set_flashdata('success','File is Uploaded Successfully.');
						redirect('admincontrol/Cmspage/upload_doc_entry');
					}else{
						$this->session->set_flashdata('e_error','Uploading Error in DB, Try Again.');
						redirect('admincontrol/Cmspage/upload_doc_entry');
					}
				}else{
					$this->data['error']=$this->upload->display_errors();
				}

			}else{
				$this->data['error']= 'Please Select a File, Chcek Agian.';
			}

		}	
		$this->load->view('admin/page/doc_entry',$this->data);
	}
	
	public function delete_document($record_id = NULL){
		if($record_id == NULL){
			redirect('admincontrol/Cmspage/upload_file_list');
		}
		$doc = $this->db->get_where('uploadfile_tab',array('up_id'=> $record_id))->row()->up_file;
		//print_r($doc);exit;
		if($this->db->where('up_id', $record_id)->delete('uploadfile_tab')){
			unlink('upload_file/file_doc/'.$doc);
			$this->session->set_flashdata("success","Document is Removed successfully");
		    redirect('admincontrol/Cmspage/upload_file_list','refresh');
		}else{
			$this->session->set_flashdata("e_error","There is some Problem. Please try again.");
		    redirect('admincontrol/Cmspage/upload_file_list','refresh');
		}
		/*if($_POST){
			$record_id=$this->input->post('record_id');
			$details = $this->Cmspage_m->file_list($record_id);
			$image= $details->up_file;
			
			if($this->db->where('up_id', $record_id)->delete('uploadfile_tab')){
				//unlink('upload_file/file_doc/'.$image);
				echo json_encode(array('msg'=>1));
			}else{
				echo json_encode(array('msg'=>0));
			}
		}else{
			redirect('default404');
		}*/

	}

	public function add_all_doc(){
		if($_POST){
			//print_r($_FILES);
			//exit;
			$filename = $_FILES['files']['name'];
			//print_r($filename);
			if(!empty($filename)){

				$extension = pathinfo($filename, PATHINFO_EXTENSION);
				
				$ext_list= array('jpg','png','jpeg','JPG','JPEG','PNG','gif','icon');
				if(in_array($extension, $ext_list))
				{
					$data_upload['up_type'] = 1;
				}else{
					$data_upload['up_type']= 2;
				}
			   
				//print_r($data_upload);
				$this->load->library('upload');
				$this->load->library('image_lib');
				
				$config['upload_path'] =realpath('upload_file/file_doc/'); 
				$config['allowed_types'] = 'pdf|PDF|docx|doc|txt|csv|xlsx|jpg|png|jpeg|JPG|JPEG|PNG|gif|GIF|icon';
				$config['overwrite'] = FALSE;
				$config['remove_spaces'] = TRUE;
				$config['file_name'] = $filename;
				
				$this->load->library('upload', $config);
				$this->upload->initialize($config);
			   
				if($this->upload->do_upload('files')){
					$upload_data = $this->upload->data();
					$data_upload['up_file'] = $upload_data['file_name'];
					$data_upload['up_date'] = date('Y-m-d H:i:s');
					
					if($data_upload['up_type'] == 1){
						if($upload_data['image_width'] >= $upload_data['image_height'] && $upload_data['image_width'] > 900){
							$resize_conf = array(
								'source_image' => $upload_data['full_path'],
								'new_image' => 'upload_file/file_doc/',
								'overwrite' => true,
								'width' => 900
							);
							$this->image_lib->initialize($resize_conf);
							$this->image_lib->resize();
							
						}
						elseif($upload_data['image_height'] >= $upload_data['image_width'] && $upload_data['image_height'] > 900){
							
							$resize_conf = array(
								'source_image' => $upload_data['full_path'],
								'new_image' => 'upload_file/file_doc/',
								'overwrite' => true,
								'height' => 900
							);
							$this->image_lib->initialize($resize_conf);
							$this->image_lib->resize();
						}
					}
					
					if($this->Cmspage_m->upload_doc_entry($data_upload)){
						//$this->session->set_flashdata('success','File is Uploaded Successfully.');
						echo json_encode('success');
					}else{
						echo json_encode('Problem To Upload in DB, Chcek Agian.');
					}
					
				}else{
					echo json_encode($this->upload->display_errors());
				}

			}else{
				echo json_encode('Please Select a File, Chcek Agian.');
			}
		}
	}


}