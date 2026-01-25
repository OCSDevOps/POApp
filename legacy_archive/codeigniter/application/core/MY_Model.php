<?php

class MY_Model extends CI_Model {

    protected $_users = 'frontend_users';
    protected $_adminusers = 'user_info';
    /*protected $_nboards = 'health_notice';
    protected $_tboards = 'health_tender';
    protected $_oboards = 'health_order';
    protected $_contents = 'cms_content';
    protected $_contacts = 'contact_info';
    protected $_actions = 'plan_actions';
    protected $_homoeo = 'homoepathy_tab';
    protected $_samity = 'welfare_samity';
    protected $f_users = 'frontend_users';
    protected $_asha = 'ashalist_tab';*/
    function __construct() {
        parent::__construct();
    }

}
