<?php



class Cmspage_m extends CI_Model {

	public function submit_new_gallery_type($data){
		$this->db->insert('gallery_tab_type',$data);
		return $this->db->insert_id();
			
	}

	public function page_list(){

		$this->db->from('static_page');
		$this->db->Order_by('stat_id','desc');
		return $this->db->get()->result();

	}
	
	public function gallery_type_list($id= NULL){
		$this->db->select('gallery_tab_type.*');
		$this->db->from('gallery_tab_type');
		
		if($id != NULL){
			$this->db->where('gallery_tab_type.gal_t_id',$id);
			$query = $this->db->get()->row();		
		}else{
			$query = $this->db->get()->result();	
		}
		return $query;

	}
	
	public function gallery_type_list2($id= NULL){
		$this->db->select('gallery_tab_type.*');
		$this->db->from('gallery_tab_type');
		
		if($id != NULL){
			$this->db->where('gallery_tab_type.gal_t_id',$id);
			
			$query = $this->db->get()->row();		
		}else{
			$this->db->where('gallery_tab_type.gal_t_status',1);
			$this->db->Order_by('gal_t_id','desc');
			$query = $this->db->get()->result();	
		}
		return $query;

	}
	
	public function gallery_tab_update($data,$id){
		$this->db->set($data);
		$this->db->where('pic_id',$id);
		if($this->db->update('gallery_tab')){
		return true;
		}else{
		return true;
		}
	}
	public function gallery_tab_type_update($type_data,$gal_t_id){
		$this->db->set($type_data);
		$this->db->where('gal_t_id',$gal_t_id);
		if($this->db->update('gallery_tab_type')){
		return true;
		}else{
		return true;
		}
	}
	
	
	
	
	public function gallery_entry($data_upload){
		 $this->db->insert('gallery_tab',$data_upload);
	
	}
	public function gallery_list($type= NULL,$id= NULL){
		$this->db->select('gallery_tab.*');
		$this->db->from('gallery_tab');
		if($type != NULL){
		$this->db->where('gal_t_id',$type);
		}
		if($id != NULL){
		$this->db->where('pic_id',$id);
		$query = $this->db->get()->row();
		}else{
		$this->db->Order_by('pic_id','desc');
		$query = $this->db->get()->result();
		}
		return $query;

	}
	
	public function upload_doc_entry($data_upload){
		 if($this->db->insert('uploadfile_tab',$data_upload)){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	public function file_list($id= NULL){

		$this->db->from('uploadfile_tab');
		if($id != NULL){
		$this->db->where('up_id',$id);
		$query = $this->db->get()->row();
		}else{
		
		$query = $this->db->get()->result();
		}
		return $query;

	}
	

	public function banner_entry($data_upload){

		 $this->db->insert('home_banner',$data_upload);
	

	}
	public function banner_update($data,$id){
	 
		$this->db->set($data);
		$this->db->where('b_id',$id);
		 $this->db->update('home_banner');

	}
	public function chairmain_update($data,$id){
	 
		$this->db->set($data);
		$this->db->where('id',$id);
		 $this->db->update('chairmain_details');

	}

	public function banner_list(){

		$this->db->from('home_banner');
		//$this->db->where('status', 1);
		$query = $this->db->get();
		return $query->result();

	}
	
	public function submit_image_slider_link($data){
			$this->db->insert('image_slide_link',$data);
	}
	
	public function image_slider_link_details($id = NULL){
			$this->db->from('image_slide_link');
			
			if($id != NULL){
			$this->db->where('im_id',$id);
			$pri =$this->db->get()->row();
			
			}else{
				$this->db->Order_by('im_order',"asc");
				//$this->db->where('im_status','1');
				$pri =$this->db->get()->result();

			}
			return $pri;
			
	}

	public function video_gallery_inserUpdate_DB($rowarray, $v_id = NULL){
		$this->db->set($rowarray);
		if($v_id != NULL){
			$this->db->where('video_id',$v_id);
			if($this->db->update('video_tab', $rowarray)){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			if($this->db->insert('video_tab', $rowarray)){
				return TRUE;
			}else{
				return FALSE;
			}
		}	
	}

	public function add_new_page($data){
		if($this->db->insert('static_page',$data)){
			return true;
		}else{
			return false;	
		}
	}

	public function page_details($link){

		$this->db->from('static_page');
		$this->db->where('url_link',$link);
		return $this->db->get()->row();

	}

	public function page_update($data,$link){

		$this->db->where('url_link',$link);
		$this->db->set($data);	 
		 if($this->db->update('static_page')){
		   return true;
	   }else{
		   return false;	
	   }
	   
    }

}

