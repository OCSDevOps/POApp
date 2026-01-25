<?php  

$templateData = $this->db->get_where('permission_master',['pt_id'=>$this->session->userdata('pt_id'),'status'=>1])->row();
  $utype = $this->session->userdata['utype']; 
?>
<!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <aside class="left-sidebar" data-sidebarbg="skin5">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav" class="p-t-30">
                        <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="<?php echo base_url('admincontrol/dashboard'); ?>" aria-expanded="false"><i class="mdi mdi-view-dashboard"></i><span class="hide-menu">Dashboard</span></a></li>
                        <?php 
                          if($this->session->userdata('utype')!=4){
                          if($this->session->userdata('utype')==1 || ($templateData->pt_t_porder<4 || $templateData->pt_t_rorder<4 || $templateData->pt_t_rcorder<4 || $templateData->pt_t_rfq<4)){
                        ?>
						            <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="fas fa-tasks"></i><span class="hide-menu">Tasks </span></a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <?php if($this->session->userdata('utype')==1 || $templateData->pt_t_porder<4){?>
                                  <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/porder/all_purchase_order_list'); ?>" class="sidebar-link"><i class="fas fa-dolly-flatbed"></i><span class="hide-menu"> Purchase Order</span></a></li>
                                <?php }?>
                                <?php if($this->session->userdata('utype')==1 || $templateData->pt_t_rorder<4){?>
								                  <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/rorder/all_rental_order_list'); ?>" class="sidebar-link"><i class="fas fa-cubes"></i><span class="hide-menu"> Rental Order</span></a></li>
                                <?php }?>
                                <?php if($this->session->userdata('utype')==1 || $templateData->pt_t_rcorder<4){?>
								                  <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/recvorder/all_receive_order_list'); ?>" class="sidebar-link"><i class="fas fa-truck-moving"></i><span class="hide-menu"> Receive Order</span></a></li>
                                <?php }?>
                                <?php if($this->session->userdata('utype')==1 || $templateData->pt_t_rfq<4){?>
								                  <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/rfqorder/all_rfq_list'); ?>" class="sidebar-link"><i class="fas fa-copy"></i><span class="hide-menu"> Request Form Quote</span></a></li>
                                <?php }?>
                            </ul>
                        </li>
                        <?php 
                          }}else{
                        ?>
						            <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="fas fa-tasks"></i><span class="hide-menu">Tasks </span></a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/rfqorder/all_rfq_list'); ?>" class="sidebar-link"><i class="fas fa-copy"></i><span class="hide-menu"> Request Form Quote</span></a></li>
                            </ul>
                        </li>
                        <?php }?>
                        <?php 
                          if($this->session->userdata('utype')!=4){
                          if($this->session->userdata('utype')==1 || ($templateData->pt_m_item<4 || $templateData->pt_m_uom<4 || $templateData->pt_m_costcode<4 || $templateData->pt_m_projects<4 || $templateData->pt_m_suppliers<4 || $templateData->pt_m_taxgroup<4 || $templateData->pt_m_budget<4 || $templateData->pt_m_email<4)){
                        ?>
						            <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-settings"></i><span class="hide-menu">Master Setup</span></a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <?php if($this->session->userdata('utype')==1 || $templateData->pt_m_item<4){?>
                                  <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/itemcategory/item_category_list'); ?>" class="sidebar-link"><i class="mdi mdi-chemical-weapon"></i><span class="hide-menu"> Item Categories</span></a></li>
                                <?php }?>
                                <?php if($this->session->userdata('utype')==1 || $templateData->pt_m_uom<4){?>
                                <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/uom/unit_of_measures_list'); ?>" class="sidebar-link"><i class="mdi mdi-ungroup"></i><span class="hide-menu"> Unit of Measures</span></a></li>
                                <?php }?>
                                <?php if($this->session->userdata('utype')==1 || $templateData->pt_m_costcode<4){?>
                                <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/costcode/cost_code_list'); ?>" class="sidebar-link"><i class="mdi mdi-application"></i><span class="hide-menu"> Cost Code</span></a></li>
                                <?php }?>
                                <?php if($this->session->userdata('utype')==1 || $templateData->pt_m_projects<4){?>
                                <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/projects/all_project_list'); ?>" class="sidebar-link"><i class="mdi mdi-presentation-play"></i><span class="hide-menu"> Projects</span></a></li>
                                <?php }?>
                                <?php if($this->session->userdata('utype')==1 || $templateData->pt_m_suppliers<4){?>
                                <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/suppliers/supplier_list'); ?>" class="sidebar-link"><i class="fas fa-address-card   "></i><span class="hide-menu"> Suppliers</span></a></li>
                                <?php }?>
                                <?php if($this->session->userdata('utype')==1 || $templateData->pt_m_taxgroup<4){?>
                                <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/taxgroup/tax_group_list'); ?>" class="sidebar-link"><i class="fas fa-calculator"></i><span class="hide-menu"> Tax Group</span></a></li>
                                <?php }?>
                                <?php if($this->session->userdata('utype')==1 || $templateData->pt_m_budget<4){?>
                                <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/budget/budget_summary'); ?>" class="sidebar-link"><i class="fas fa-donate"></i><span class="hide-menu"> Budget</span></a></li>
                                <?php }?>
                                <?php if($this->session->userdata('utype')==1 || $templateData->pt_m_email<4){?>
                                <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/template/template_list'); ?>" class="sidebar-link"><i class="fas fa-envelope-square"></i><span class="hide-menu">Email Template</span></a></li>
                                <?php }?>
							              </ul>
                        </li>
                        <?php 
                          }}
                        ?>
                        <?php 
                          if($this->session->userdata('utype')!=4){
                          if($this->session->userdata('utype')==1 || ($templateData->pt_i_item<4 || $templateData->pt_i_itemp<4 || $templateData->pt_i_supplierc<4)){
                        ?>
						            <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-settings"></i><span class="hide-menu">Item Setup</span></a>
                          <ul aria-expanded="false" class="collapse  first-level">
                            <?php if($this->session->userdata('utype')==1 || $templateData->pt_i_item<4){?>
                              <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/items/item_list'); ?>" class="sidebar-link"><i class="fas fa-tag"></i><span class="hide-menu"> Items</span></a></li>
                            <?php }?>
                            <?php if($this->session->userdata('utype')==1 || $templateData->pt_i_itemp<4){?>
                              <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/packages/all_package_list'); ?>" class="sidebar-link"><i class="fas fa-briefcase"></i><span class="hide-menu"> Item Packages</span></a></li>
                            <?php }?>
                            <?php if($this->session->userdata('utype')==1 || $templateData->pt_i_supplierc<4){?>
                              <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/sup_catalog/supplier_catalog_list'); ?>" class="sidebar-link"><i class="fas fa-tags"></i><span class="hide-menu"> Supplier Catalogs </span></a></li>
                            <?php }?>
                          </ul>
                        </li>
                        <?php 
                          }}else{
                        ?>
						            <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-settings"></i><span class="hide-menu">Item Setup</span></a>
                          <ul aria-expanded="false" class="collapse  first-level">
                              <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/sup_catalog/supplier_catalog_list'); ?>" class="sidebar-link"><i class="fas fa-tags"></i><span class="hide-menu"> Supplier Catalogs </span></a></li>
                          </ul>
                        </li>
                        <?php }?>
                        <?php 
                          if($this->session->userdata('utype')!=4){
                          if($this->session->userdata('utype')==1 || ($templateData->pt_e_eq<4 || $templateData->pt_e_eqm<4)){
                        ?>
						            <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-settings"></i><span class="hide-menu">Equipment Setup</span></a>
                          <ul aria-expanded="false" class="collapse  first-level">
                            <!-- <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/items/item_list'); ?>" class="sidebar-link"><i class="mdi mdi-stackoverflow"></i><span class="hide-menu"> Items</span></a></li> -->
                            <?php if($this->session->userdata('utype')==1 || $templateData->pt_e_eq<4){?>
                              <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/equipments/all_equipment_list'); ?>" class="sidebar-link"><i class="mdi mdi-store"></i><span class="hide-menu"> Equipment Master</span></a></li>
                            <?php }?>
                            <?php if($this->session->userdata('utype')==1 || $templateData->pt_e_eqm<4){?>
                              <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/maintenance/all_maintenance_list'); ?>" class="sidebar-link"><i class="mdi mdi-wrench"></i><span class="hide-menu"> Equipment Maintenance </span></a></li>
                            <?php }?>
                            <?php if($this->session->userdata('utype')==1 || $templateData->pt_e_eqm<4){?>
                              <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/checklist/all_checklist_list'); ?>" class="sidebar-link"><i class="fas fa-list"></i><span class="hide-menu"> Equipment Checklist</span></a></li>
                            <?php }?>
                            <?php if($this->session->userdata('utype')==1 || $templateData->pt_e_eqm<4){?>
                              <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/PerformChecklist/all_checklist_list'); ?>" class="sidebar-link"><i class="fas fa-check-circle "></i><span class="hide-menu"> Perform Checklist</span></a></li>
                            <?php }?>
                            <?php if($this->session->userdata('utype')==1 || $templateData->pt_e_eqm<4){?>
                              <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/PerformedChecklists/all_checklist_list'); ?>" class="sidebar-link"><i class="fas fa-eye"></i><span class="hide-menu"> Performed Checklists</span></a></li>
                            <?php }?>
                          </ul>
                        </li>
                        <?php 
                          }}
                        ?>
                        <?php 
                          if($this->session->userdata('utype')!=4){
                          if($this->session->userdata('utype')==1 || ($templateData->pt_a_user<4 || $templateData->pt_a_permissions<4 || $templateData->pt_a_cinfo<4 || $templateData->pt_a_procore<4)){
                        ?>
						            <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="fas fa-sitemap"></i><span class="hide-menu">App Settings </span></a>
                            <ul aria-expanded="false" class="collapse  first-level">
                              <?php if($this->session->userdata('utype')==1 || $templateData->pt_a_user<4){?>
                                <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/dashboard/administrator'); ?>" class="sidebar-link"><i class="mdi mdi-account-circle"></i><span class="hide-menu"> User Setup </span></a></li>
                              <?php }?>
                              <?php if($this->session->userdata('utype')==1){?>
                                <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/permissions/all_permissions_list'); ?>" class="sidebar-link"><i class="fas fa-lock"></i><span class="hide-menu"> Permissions </span></a></li>
                              <?php }?>
                              <?php if($this->session->userdata('utype')==1 || $templateData->pt_a_cinfo<4){?>
                                <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/company/company_view'); ?>" class="sidebar-link"><i class="fas fa-wrench"></i><span class="hide-menu"> Company Information </span></a></li>
                              <?php }?>
                              <?php if($this->session->userdata('utype')==1 || $templateData->pt_a_procore<4){?>
                                <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/procore/procore_view'); ?>" class="sidebar-link"><i class="fas fa-handshake"></i><span class="hide-menu"> Procore Integration </span></a></li>
                              <?php }?>
                            </ul>
                        </li>
                        <?php 
                          }}
                        ?>
						<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="<?php echo base_url('admincontrol/support'); ?>" aria-expanded="false"><i class="fas fa-envelope"></i><span class="hide-menu">Support Ticket</span></a></li>

					</ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->









<!--
      <aside class="main-sidebar">
        <section class="sidebar">
          <div class="user-panel">
            <div class="pull-left image">
              <img src="<?php //echo base_url().'bootstrap-admin/dist/img/administrator.png'; ?>" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
              <p><?php //echo $u_details->firstname.' '.$u_details->lastname; ?></p>

              <a href="javascript:;"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
          </div>
          
		  
          <ul class="sidebar-menu">
            <li class="header">MAIN NAVIGATION</li>
            <li>
              <a href="<?php //echo site_url('admincontrol/dashboard'); ?>">
                <i class="fa fa-dashboard"></i> <span>Dashboard</span>
              </a>
            </li>
            <?php //if($utype == 2 || $utype == 1) 
            //{ ?>
            <li class="treeview">
              <a href="#">
                <i class="fa fa-user"></i> <span>Administrator</span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href=""><i class="fa fa-circle-o text-warning"></i>All User Permission</a></li>
              </ul>
            </li>
            <li class="treeview">
              <a href="#">
                <i class="fa fa-paste"></i>
                <span>CMS</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
				<li><a href=""><i class="fa fa-circle-o text-warning"></i> Add New Doc</a></li>
              </ul>
            </li>
            <?php //} ?>
            <li class="treeview">
              <a href="#">
                <i class="fa fa-comments-o"></i>
                <span>Forum</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href=""><i class="fa fa-circle-o text-warning"></i> Query List</a></li>
              </ul>
            </li>
			<li class="treeview">
              <a href="#">
                <i class="fa fa-users"></i>
                <span>Frontend Users</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href=""><i class="fa fa-circle-o text-warning"></i> User List</a></li>
              </ul>
            </li>
			<li class="treeview">
              <a href="#">
                <i class="fa fa-building"></i>
                <span>Event</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href=""><i class="fa fa-circle-o text-warning"></i> Event List</a></li>
              </ul>
            </li>
			<li class="treeview">
              <a href="#">
                <i class="fa fa-truck"></i>
                <span>Supplier</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href=""><i class="fa fa-circle-o text-warning"></i> Supplier List</a></li>
                <li><a href=""><i class="fa fa-circle-o text-warning"></i> Add Supplier</a></li>
              </ul>
            </li>
			<li class="treeview">
              <a href="#">
                <i class="fa fa-book"></i>
                <span>Guideline/Instruction</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href=""><i class="fa fa-circle-o text-warning"></i> Record List</a></li>
                <li><a href=""><i class="fa fa-circle-o text-warning"></i> Add New Record</a></li>
              </ul>
            </li>
            -->
