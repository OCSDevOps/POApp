<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Default404 extends Frontend_Controller {
	
	public $breadcrumb = "";

    	public function __construct() {
        	parent::__construct();
        	
	}
	public function index()
	{
		
		$this->load->view('main/my404_view', $this->data);
		
	}
	
}