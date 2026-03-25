<?php $this->load->view('admin/component/header') ?>


<?php $this->load->view('admin/component/menu') ?>

<style>
.box-body textarea,input,select {max-width: 500px;}
.box-body textarea { resize: vertical; }
</style>   

		<!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-12 d-flex no-block align-items-center">
                        <h4 class="page-title">Edit Profile</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url('admincontrol/dashboard'); ?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Edit Profile</li>
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
                                <h5 class="card-title">Edit Profile</h5>
								<?php if (isset($error)) { ?>
								<div class="alert alert-error alert-danger">                
									<h4>Error!</h4>
									<?php echo $error; ?>
								</div>
								<?php } ?>
			
                                <?php echo form_open_multipart('','class="form-horizontal"'); ?>
                  <div class="form-group row">
				    <label class="col-sm-3 control-label text-right">First Name<font style="color: red;">*</font></label>
				    <div class="col-sm-9">
				      <input type="text" class="form-control" name="fname" id="fname" placeholder="Enter First Name" value="<?php echo $profile_list->firstname; ?>" autocomplete="off" required>
				      <small class="invalid-feedback"><?php echo form_error('fname'); ?></small>
				    </div>
				  </div>
                  <div class="form-group row">
				    <label class="col-sm-3 control-label text-right">Last Name<font style="color: red;">*</font></label>
				    <div class="col-sm-9">
				      <input type="text" class="form-control" name="lname" id="lname" placeholder="Enter Last Name" value="<?php echo $profile_list->lastname; ?>" autocomplete="off" required>
				      <small class="invalid-feedback"><?php echo form_error('lname'); ?></small>
				    </div>
				  </div>
                  <div class="form-group row">
				    <label class="col-sm-3 control-label text-right">Email Address</label>
				    <div class="col-sm-9">
				      <input type="email" class="form-control" name="emailid" id="emailid" placeholder="Enter Email" value="<?php echo $profile_list->email; ?>" disabled="">
				      <small class="invalid-feedback"><?php echo form_error('emailid'); ?></small>
				    </div>
				  </div>
                  <div class="form-group row">
				    <label class="col-sm-3 control-label text-right">User Name</label>
				    <div class="col-sm-9">
				      <input type="text" class="form-control" name="username" id="username" placeholder="Enter UserName" value="<?php echo $profile_list->username; ?>" disabled="">
				      <small class="invalid-feedback"><?php echo form_error('username'); ?></small>
				    </div>
				  </div>
				  <div class="form-group row">
				    <label class="col-sm-3 control-label text-right">Phone/Mobile</label>
				    <div class="col-sm-9">
				      <input type="text" class="form-control" name="u_mobile" id="u_mobile" placeholder="Enter phone/mobile" value="<?php echo $profile_list->phone; ?>" autocomplete="off" disabled="">
				      <small class="invalid-feedback"><?php echo form_error('u_mobile'); ?></small>
				    </div>
				  </div>
				  
				  <div class="form-group row">
				    <label class="col-sm-3 control-label text-right">Address (in full)</label>
				    <div class="col-sm-6">
				      <textarea class="form-control" name="u_address" id="u_address" placeholder="Enter Full Address"><?php echo $profile_list->address; ?></textarea>
				      <small class="invalid-feedback"><?php echo form_error('u_address'); ?></small>
				    </div>
				  </div>
				  <div class="col-sm-3">&nbsp;</div>
                  <div class="col-sm-9">
                    <input type="submit" class="btn btn-success" name="submit" value="Submit" />
                    <a href="<?php echo site_url('admincontrol/dashboard/profile'); ?>" class="btn btn-danger">Cancel</a>
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
