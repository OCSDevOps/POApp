<?php $this->load->view('admin/component/header') ?>


<?php $this->load->view('admin/component/menu') ?>


		<!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-12 d-flex no-block align-items-center">
                        <h4 class="page-title">Profile</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url('admincontrol/dashboard'); ?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Profile</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->
                <div class="row">
                    <div class="col-12">
						<div class="card">
                            <div class="card-body">
								<?php if($this->session->flashdata('success')) { ?>
								<div id="alert_msg" class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
								<?php $this->session->unset_userdata('success'); }
								elseif($this->session->flashdata('e_error')) { ?>                
								<div id="alert_msg" class="alert alert-danger"><?php echo $this->session->flashdata('e_error'); ?></div>
								<?php $this->session->unset_userdata('e_error'); } ?>
                                <h5 class="card-title">Profile</h5>
                                <div style="font-size: 16px;">
            		<?php //$usr_detail = $this->admin_m->GetDetailsofUsers($adminid); ?>
            	<table class="table table-striped">
            	<tr>
            		<td><strong>First Name :</strong></td>
            		<td><?php echo $usr_detail->firstname; ?></td>
            	</tr>
            	<tr>
            		<td><strong>Last Name :</strong></td>
            		<td><?php echo $usr_detail->lastname; ?></td>
            	</tr>
				<tr>
            		<td><strong>User Type :</strong></td>
            		<td><?php echo $usr_detail->mu_name; ?></td>
            	</tr>
            	<tr>
            		<td><strong>Registered Email :</strong></td>
            		<td><?php echo $usr_detail->email; ?></td>
            	</tr>
				<tr>
            		<td><strong>Mobile :</strong></td>
            		<td><?php echo $usr_detail->phone; ?></td>
            	</tr>
            	
            	<tr>
            		<td><strong>Username :</strong></td>
            		<td><?php echo $usr_detail->username; ?></td>
            	</tr>
				<tr>
            		<td><strong>Address :</strong></td>
            		<td><?php echo $usr_detail->address; ?></td>
            	</tr>
            	</table>	
            		
            	<div><span><a href="<?= site_url('admincontrol/dashboard/editprofile') ?>" class="btn btn-warning">Edit Profile</a></span>
            	&nbsp;<span><a href="<?= site_url('admincontrol/dashboard/changepassword') ?>" class="btn btn-primary">Change Password</a></span></div>	
            	</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- End PAge Content -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Right sidebar -->
                <!-- ============================================================== -->
                <!-- .right-sidebar -->
                <!-- ============================================================== -->
                <!-- End Right sidebar -->
                <!-- ============================================================== -->
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            


<?php $this->load->view('admin/component/footer') ?>
