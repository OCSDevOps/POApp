
<?php  $this->load->view('admin/component/header') ?>

<?php   $this->load->view('admin/component/menu') ?>



<script type="text/javascript">
	$(function(){
	      $('#alert_msg').delay(6000).fadeOut();
	});
</script>
 
        
<!-- Content Wrapper. Contains page content --><div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
          New Page
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">New Page</li>
          </ol>
        </section>

        
		<section class="content">
		<div class="row">
            <section class="col-lg-12">
              <!-- Custom tabs (Charts with tabs)-->
			
			<?php if (isset($error)) { ?>
            <div class="alert alert-error">                
                <h4>Error!</h4>
                <?php echo $error; ?>
            </div>
        	<?php } ?>
			
              <!-- TO DO List -->
              <div class="box box-warning">
                <div class="box-header">
                  <i class="ion ion-clipboard"></i>
                  <h3 class="box-title">Add New Page</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
				
                <?php if(!empty($msg)){ if($msg == "success"){ ?><div id="alert_msg" class="alert alert-success">Page is Created Successfully</div> <?php } } ?>
				<?php if(!empty($msg)){ if($msg != "success"){ ?><div id="alert_msg" class="alert alert-danger"><?php echo $msg ;  ?><br />
				</div> <?php } } ?>
				<?php if($this->session->flashdata('success')) { ?>
				<div id="alert_msg" class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
				<?php $this->session->unset_userdata('success'); }
					elseif($this->session->flashdata('e_error')) { ?>                
				<div id="alert_msg" class="alert alert-danger"><?php echo $this->session->flashdata('e_error'); ?></div>
				<?php $this->session->unset_userdata('e_error'); } ?>
			
                <?php echo form_open_multipart('','class="form-horizontal" id="myForm"'); ?>
                 <div class="form-group">
				    <label class="col-sm-2 control-label text-right">Title<font style="color: red;">*</font></label>
				    <div class="col-sm-4">
				      <input type="text" class="form-control" name="title" id="title" placeholder="Enter Page Title" value="<?php echo set_value('title'); ?>" autocomplete="off" />
				      <small class="text-error title"><?php echo form_error('title'); ?></small>
				    </div>
				    <label class="col-sm-2 control-label text-right">Bengali Title</label>
				    <div class="col-sm-4">
				      <input type="text" class="form-control" name="title_bengali" id="title_bengali" placeholder="Enter Bengali Title" value="<?php echo set_value('title_bengali'); ?>" autocomplete="off" />
				      <small class="text-error title_bengali"><?php echo form_error('title_bengali'); ?></small>
				    </div>
				 </div>
				 <div class="form-group">
				 <div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><strong>Page Contents</strong><font style="color: red;">*</font></h3>
						</div> 
						<textarea class="form-control" id="editor" name="details"><?php echo set_value('details'); ?></textarea>
						<small class="text-error editor"><?php echo form_error('editor'); ?></small>
					</div>
				 </div>
				 </div>
				 <div class="form-group">
				 <div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><strong>Page Contents Bengali</strong></h3>
						</div> 
						<textarea class="form-control" id="editor1" name="details_bengali"><?php echo set_value('details_bengali'); ?></textarea>
						<small class="text-error editor1"><?php echo form_error('editor1'); ?></small>
					</div>
				 </div>
				 </div>
				 
				  	<div class="form-group">
						<div  class="col-sm-12 text-center">
							<div align="center">
								<div class="get_error_total" align="center" style="background-color: #bf0000;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
								<div class="get_success_total" align="center" style="background-color: #174b10;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
								<div class="get_success" align="center" style="display: none;"><img src="<?php echo base_url(); ?>images/ajax_loader.gif" style="max-width: 60px;" /></div>
							</div>
						</div>
					</div>
					  <div class="form-group">
						<div class="col-sm-offset-3 col-sm-9">
						  <!--<button type="button" onclick="gotoclclickbutton();" class="btn btn-primary">Submit</button>-->
						  <input type="submit" class="btn btn-primary" id="p_id_submit" name="submit" value="Submit" />
						  &nbsp;<a href="<?= site_url('admincontrol/Cmspage') ?>" class="btn btn-danger">Back</a>
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

	</div>		
			
			
<script>
	function add_image(){
		var upload = $('#files').val();
	
		$(".get_success").fadeIn();
		var files = $('#files')[0].files;
			
			
		var form_data = new FormData();
		form_data.append("files[]", files[0]);
		form_data.append("title","success");

		if(upload == ""){
		$(".get_error_total").html('Select Gallery Image');
		$(".get_success").fadeOut();
			$(".get_error_total").fadeIn();
			delay = 3000;
			setTimeout(function(){ $('.get_error_total').fadeOut(); }, delay);
			
		}else{
			$("#p_id_submit").attr("disabled", "disabled");
			jQuery.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/gallery/add_image') ?>",
				dataType: 'json',
				data: form_data,
				contentType:false,
				cache:true,
				processData:false,
				
					success: function(res) {
					//alert(res);
					if(res =='success'){
							
							$(".get_success").fadeOut();
							$(".get_success_total").html('Upload image is Successfully.');
							
							$(".get_success_total").fadeIn();
							delay = 3000;
							setTimeout(function(){ $('.get_success_total').fadeOut(); }, delay);
							setTimeout(function(){ window.location.replace("<?php echo site_url('admincontrol/gallery/image_list')?>/"); }, delay);
						
						}else{
						
							$(".get_success").fadeOut();
							$('.get_error_total').html(res);
							$('.get_error_total').fadeIn();	
							$("#p_id_submit").removeAttr('disabled');
							delay = 3000;
							setTimeout(function(){ $('.get_error_total').fadeOut(); }, delay);	
						}
						
						
					}
				});
			
						
	
	}
	
		
	
	} 
</script>
	

<script src="<?php echo base_url('ckeditor/ckeditor.js'); ?>"></script>
<script src="<?php echo base_url('ckeditor/samples/js/sample.js'); ?>"></script>
<script src="<?php echo base_url(''); ?>js/jquery-3.4.1.min.js"></script>	
		
<script>
	 initSample();
</script>
	
<?php   $this->load->view('admin/component/footer') ?>

