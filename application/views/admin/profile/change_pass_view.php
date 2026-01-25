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
                        <h4 class="page-title">Change Password</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url('admincontrol/dashboard'); ?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Change Password</li>
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
                                <?php if (isset($error)) { ?>
								<div class="alert alert-error alert-danger">                
									<h4>Error!</h4>
									<?php echo $error; ?>
								</div>
								<?php } ?>
								<?php echo form_open_multipart('','class="form-horizontal"'); ?>
                  <div class="form-group row">
				    <label class="col-sm-3 control-label text-right">Current Password<font style="color: red;">*</font></label>
				    <div class="col-sm-9">
				      <input type="password" class="form-control" name="c_pass" id="c_pass" placeholder="Enter Current Password" required>
				      <small class="invalid-feedback"><?php echo form_error('c_pass'); ?></small>
				    </div>
				  </div>
				  <div class="form-group row">
				    <label class="col-sm-3 control-label text-right">New Password<font style="color: red;">*</font></label>
				    <div class="col-sm-9">
				      <input type="password" class="form-control" name="n_pass" id="n_pass" placeholder="Enter New Password" required> <small>only use this special charecters(!,@,#,$,%,*)</small>
				      <small class="invalid-feedback"><?php echo form_error('n_pass'); ?></small>
				    </div>
				  </div>
                  <div class="form-group row">
				    <label class="col-sm-3 control-label text-right">New Password Again<font style="color: red;">*</font></label>
				    <div class="col-sm-9">
				      <input type="password" class="form-control" name="n_repass" id="n_repass" placeholder="Enter New Password Again" required>
				      <small class="invalid-feedback"><?php echo form_error('n_repass'); ?></small>
				    </div>
				  </div>
                  
                  <br/><br/>
                  <div class="form-group">
				    <div class="col-sm-offset-3 col-sm-9">
				      <input type="submit" class="btn btn-success" name="submit" value="Submit" />
                      &nbsp;<a href="<?= site_url('admincontrol/dashboard/profile') ?>" class="btn btn-danger">Cancel</a>
				    </div>
				  </div>
                  
                <?php form_close(); ?>
                
                                
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

<script type="text/javascript">
	$(function(){
	      $('.alert-error, .invalid-feedback').delay(6000).fadeOut();
	});
</script>