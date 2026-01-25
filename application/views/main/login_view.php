<?php $this->load->view('main/component/header')?>

<style>
.alert-error, .text-error, .redclass {
    	color: red !important;
	}
</style>       

        <!-- Presentation -->
        <div class="presentation-container">
        	  <div class="container">
	            		
	            <div class="row">
	            	<div class="col-sm-12 text-center">
						<h1 class="header_search"> Login Portal</h1><br/>
						<?php if(isset($error)) :?>
						  <div align="center" style="color:red;">
							  <?php 
								echo $error;
							  ?>
						  </div>
						<?php endif;?>
						
						<?php if($this->session->flashdata('success')) { ?>
							<div id="alert_msg" class="alert bg-success lead"><?php echo $this->session->flashdata('success'); ?></div>
					  <?php $this->session->unset_userdata('success'); }
					  elseif($this->session->flashdata('e_error')) { ?>                
					  <div id="alert_msg" class="alert bg-danger lead"><?php echo $this->session->flashdata('e_error'); ?></div>
					  <?php $this->session->unset_userdata('e_error'); } ?>
				</div>
				</div>
				<div class="row justify-content-center">
				<div class="col-sm-4">
				<form class="form-horizontal" method="POST">
					<div class="form-group">
					  <label class="control-label col-sm-12" for="username">Mobile No:</label>
					  <div class="col-sm-12">
						<input type="text" class="form-control" id="username" placeholder="Enter Mobile No." name="username" required="" value="<?php echo set_value('username'); ?>" autocomplete="off">
						<small class="text-error"><?php echo form_error('username');?></small>
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-12" for="pwd">Password:</label>
					  <div class="col-sm-12">          
						<input type="password" class="form-control" id="password" placeholder="Enter Password" name="password" required="" autocomplete="off">
						<small class="text-error"><?php echo form_error('password');?></small>
					  </div>
					</div>
					
					<div class="form-group">        
					  <div class="col-sm-12 text-center">
						<button type="submit" class="btn btn-lg btn-info">Login</button>
					  </div>
					  <div class="col-sm-12 mt-3 text-center">
						<a href="<?php echo base_url('main/forgot_password'); ?>" class="btn btn-outline-secondary">Forger your Password</a>
					  </div>
					</div>
				</form>
      

				</div>

	            </div>
	            
	          
        	</div>
        </div>

<?php $this->load->view('main/component/footer'); ?>