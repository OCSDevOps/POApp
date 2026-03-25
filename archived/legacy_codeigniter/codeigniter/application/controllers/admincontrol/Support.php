<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Support extends Admin_Controller {
	
	 public function __construct() {
        parent::__construct();
        //date_default_timezone_set("Asia/Kolkata");
	}
	
    public function index() {
		$this->load->view('admin/support/index');
	}

}
