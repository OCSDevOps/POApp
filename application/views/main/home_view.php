<?php $this->load->view('main/component/header')?>

       

        <!-- Presentation -->

  <div class="container">
    <div class="row">
	        		
	        		
	        		
	        		
	        		<div class="col-sm-12 text-center">
	            
              <?php if($this->session->flashdata('success')) { ?>
			        <div id="alert_msg" class="alert bg-success lead"><?php echo $this->session->flashdata('success'); ?></div>
              <?php $this->session->unset_userdata('success'); }
              elseif($this->session->flashdata('e_error')) { ?>                
              <div id="alert_msg" class="alert bg-danger lead"><?php echo $this->session->flashdata('e_error'); ?></div>
              <?php $this->session->unset_userdata('e_error'); } ?>
              </div>
              <div class="col-sm-6 text-center">
                <div style="border:1px #000 solid;padding:10px;">
                  <a href="<?php echo base_url().'main/new_user_signup'; ?>" type="button" class="btn btn-primary btn-md">SignUp</a>
                </div>
                <!-- <br/><br/>
                 -->
              </div>
              <div class="col-sm-6 text-center">
                <div style="border:1px #000 solid;padding:10px;">
                  <a href="<?php echo base_url().'login'; ?>" type="button" class="btn btn-primary btn-md">Login</a>
                </div>
              </div>
	          <div class="col-sm-12 mt-5 mb-5 text-center">
				<div style="border:1px #000 solid;padding:10px;">
	            <a href="<?php echo base_url('event/registration'); ?>" class="btn btn-outline-secondary">Event Form</a>
	            <a href="<?php echo base_url('event/status_details'); ?>" class="btn btn-outline-secondary">Event Status</a>
				</div>
	          </div> 
	            
	            
	            
	            	</div>
            	</div>

        

<?php $this->load->view('main/component/footer'); ?>

<script type="text/javascript" language="javascript">
	$('document').ready(function(){
		setTimeout(function(){$('#error_con').fadeOut()}, 5000);
	});
			
</script>