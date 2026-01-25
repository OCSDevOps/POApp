<?php $this->load->view('admin/component/header'); ?>
<?php $this->load->view('admin/component/menu'); ?>
<style>
.td_image{ border-radius:2px !important;
		width:100px !important;
		height:50px !important;
}
input[type="number"], input[type="file"]{
  padding: 0.64rem 1.375rem !important;
}
.alert-error, .text-error, .redclass {
    	color: red !important;
	}
</style>
<!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Dashboard
            <small>Control panel</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Add New User</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <!-- Main row -->
          <div class="row">
            <section class="col-lg-12">
              <!-- Custom tabs (Charts with tabs)-->
			
			<?php if (isset($error)) { ?>
            <div class="alert alert-error">                
                <h4>Error!</h4>
                <?php echo $error; ?>
            </div>
        	<?php } ?>
			
			<?php if($this->session->flashdata('success')) { ?>
			
			<div id="alert_msg" class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
		    <?php $this->session->unset_userdata('success'); }
		    	elseif($this->session->flashdata('e_error')) { ?>                
	        <div id="alert_msg" class="alert alert-danger"><?php echo $this->session->flashdata('e_error'); ?></div>
		    <?php $this->session->unset_userdata('e_error'); } ?>
			
              <!-- TO DO List -->
              <div class="box box-warning">
                <div class="box-header">
                  <i class="ion ion-clipboard"></i>
                  <h3 class="box-title">Add New User</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                
                <?php echo form_open_multipart('','class="form-horizontal" id="myForm"'); ?>
                 <div class="form-group">
					<label class="col-sm-3 control-label text-right">Select File<font style="color: red;">*</font></label>
				    <div class="col-sm-6">
						<input type="file" class="form-control" id="userfile" name="userfile" placeholder="Upload Document"> 
						<small class="text-error userfile"><?php echo form_error('userfile'); ?></small>
				    </div>
					<input type="hidden" class="form-control" id="usertest" name="usertest">
				 </div>
				  	<div class="form-group">
						<div  class="col-sm-12 text-center">
							<div align="center">
								<div class="get_error_total" align="center" style="background-color: #bf0000;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
								<div class="get_success_total" align="center" style="background-color: #174b10;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
								<div class="div_roller_total" align="center" style="display: none;"><img src="<?php echo base_url(); ?>images/ajax_loader.gif" style="max-width: 60px;" /></div>
							</div>
						</div>
					</div>
                  <div class="form-group">
				    <div class="col-sm-offset-3 col-sm-9">
				      <button type="button" onclick="gotoclclickbutton();" class="btn btn-primary">Submit</button>
                      &nbsp;<a href="<?= site_url('admincontrol/Cmspage/upload_file_list') ?>" class="btn btn-danger">Cancel</a>
				    </div>
				  </div>
                  <?php form_close(); ?>
                  
                </div><!-- /.box-body -->
                <div class="box-footer clearfix">
                &nbsp;
                </div>
              </div><!-- /.box -->

            </section>
          </div><!-- /.row (main row) -->

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->	  

<?php $this->load->view('admin/component/footer'); ?>
<script type="text/javascript">
	$(function(){
	      $('.alert-error, .text-error').delay(6000).fadeOut();
	});
	
	function gotoclclickbutton(){
		$('.div_roller_total').fadeIn();
		var delay = 8000;
		var e_error = 0;
		var error_message = 'There have some errors plese check above, Try again.';
		var alphaletters_spaces = /^[A-Za-z ]+$/;
		var alphaletters = /^[A-Za-z]+$/;
		var alphanumerics = /^[A-Za-z0-9/() ]+$/;
		var alphanumerics_spaces = /^[A-Za-z0-9_,\- ]+$/;
		var alphanumerics_no = /^[A-Za-z0-9_/&(@):.,\- ]+$/;
		var onlynumerics = /^[0-9]+$/;
		var specials_char = /[~`!#$%\^&*+=\[\]\\';./{}()|\\":<>\?]/g;
		var emailpattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;
		
    	/*var u_state = $('#u_state').val();
		var u_city = $('#u_city').val();
		var u_pincode = $('#u_pincode').val();*/
		
		//var ap_symptom = $("input[name='ap_symptom']:checked").val();
		//var ap_quaran = $("input[name='ap_quaran']:checked").val();
		
		if(document.getElementById("userfile").files.length == 0){
			e_error = 1;
			$('.userfile').html('File is Required.');
		}else{
			/*var fileInput = document.getElementById('userworker'); 
			var filePath = fileInput.value;
			if(!allowedExtensions.exec(filePath)){
				e_error = 1;
				$('.userworker').html('Worker Details File type Invalid.(Use PDF/JPG)');
			}else{
				$('.userworker').html('');
			}*/
			$('.userfile').html('');
		}
		
		//alert(salts);
		if(e_error == 1){
			$('.div_roller_total').fadeOut();
			$('.get_error_total').html(error_message);
			$(".get_error_total").fadeIn();
			$(".text-error").fadeIn();
			/*e_error = 0;
			error_message = '';*/
			setTimeout(function(){ $('.text-error, .get_error_total').fadeOut(); }, delay);
		}else{
			//alert(newhash);
			//alert(rehash);
			$("#myForm").submit();
		}

  	}
	
</script>		  
