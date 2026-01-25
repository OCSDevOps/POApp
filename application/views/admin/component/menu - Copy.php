<?php $utype = $this->session->userdata['utype']; ?>
<!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <aside class="left-sidebar" data-sidebarbg="skin5">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav" class="p-t-30">
                        <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="<?php echo base_url('admincontrol/dashboard'); ?>" aria-expanded="false"><i class="mdi mdi-view-dashboard"></i><span class="hide-menu">Dashboard</span></a></li>
						
						<li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-account"></i><span class="hide-menu">Users </span></a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/dashboard/administrator'); ?>" class="sidebar-link"><i class="mdi mdi-account-circle"></i><span class="hide-menu"> User List </span></a></li>
                            </ul>
                        </li>
						<li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-presentation"></i><span class="hide-menu">Project </span></a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/projects/all_project_list'); ?>" class="sidebar-link"><i class="mdi mdi-presentation-play"></i><span class="hide-menu"> Project List </span></a></li>
                            </ul>
                        </li>
						<li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-unity"></i><span class="hide-menu">Unit of Measures </span></a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/uom/unit_of_measures_list'); ?>" class="sidebar-link"><i class="mdi mdi-ungroup"></i><span class="hide-menu"> Unit of Measures List </span></a></li>
                            </ul>
                        </li>
						<li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-animation"></i><span class="hide-menu">Cost Code </span></a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/costcode/cost_code_list'); ?>" class="sidebar-link"><i class="mdi mdi-application"></i><span class="hide-menu"> Cost Code list </span></a></li>
                            </ul>
                        </li>
						<li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-run"></i><span class="hide-menu">Supplier </span></a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/suppliers/supplier_list'); ?>" class="sidebar-link"><i class="mdi mdi-run-fast"></i><span class="hide-menu"> Supplier list </span></a></li>
                            </ul>
                        </li>
						<li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-script"></i><span class="hide-menu">Supplier Catalog </span></a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/sup_catalog/supplier_catalog_list'); ?>" class="sidebar-link"><i class="mdi mdi-sd"></i><span class="hide-menu"> Supplier Catalog list </span></a></li>
                            </ul>
                        </li>
						<li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-sitemap"></i><span class="hide-menu">Item </span></a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/items/item_list'); ?>" class="sidebar-link"><i class="mdi mdi-stackoverflow"></i><span class="hide-menu"> Item list </span></a></li>
                            </ul>
                        </li>
						<li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-checkerboard"></i><span class="hide-menu">Item Category </span></a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/itemcategory/item_category_list'); ?>" class="sidebar-link"><i class="mdi mdi-chemical-weapon"></i><span class="hide-menu"> Item Category list </span></a></li>
                            </ul>
                        </li>
						<li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-package-variant-closed"></i><span class="hide-menu">Item Package </span></a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/packages/all_package_list'); ?>" class="sidebar-link"><i class="mdi mdi-package"></i><span class="hide-menu"> Item Package list </span></a></li>
                            </ul>
                        </li>
						<li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-clipboard-text"></i><span class="hide-menu">Purchase Order </span></a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item"><a href="<?php echo base_url('admincontrol/porder/all_purchase_order_list'); ?>" class="sidebar-link"><i class="mdi mdi-clipboard-outline"></i><span class="hide-menu"> Purchase Order list </span></a></li>
                            </ul>
                        </li>
						<li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-briefcase-check"></i><span class="hide-menu">Receive Order </span></a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item"><a href="javascript:;" class="sidebar-link"><i class="mdi mdi-briefcase"></i><span class="hide-menu"> Receive Order list </span></a></li>
                            </ul>
                        </li>
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