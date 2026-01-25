<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
 <title>View Upload List</title>
    <!-- plugins:css -->
    <link rel="icon" type="image/ico" href="<?php echo base_url().'images/favicon.ico'; ?>" />
    <!-- Bootstrap 3.3.2 -->
    <link href="<?php echo base_url().'bootstrap-admin/bootstrap/css/bootstrap.min.css'; ?>" rel="stylesheet" type="text/css" />    
    <!-- FontAwesome 4.3.0 -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons 2.0.0 -->
    <link href="https://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css" rel="stylesheet" type="text/css" />  
    <link href="<?php echo base_url().'bootstrap-admin/plugins/timepicker/bootstrap-timepicker.min.css'; ?>" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="<?php echo base_url().'bootstrap-admin/dist/css/AdminLTE.min.css'; ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url().'bootstrap-admin/styles.css'; ?>" rel="stylesheet" type="text/css" />
    <!-- AdminLTE Skins. Choose a skin from the css/skins 
         folder instead of downloading all of them to reduce the load. -->
    <link href="<?php echo base_url().'bootstrap-admin/dist/css/skins/_all-skins.min.css'; ?>" rel="stylesheet" type="text/css" />
    <!-- iCheck -->
    <link href="<?php echo base_url().'bootstrap-admin/plugins/iCheck/flat/blue.css'; ?>" rel="stylesheet" type="text/css" />
    <!-- Morris chart -->
    <link href="<?php echo base_url().'bootstrap-admin/plugins/morris/morris.css'; ?>" rel="stylesheet" type="text/css" />
    <!-- jvectormap -->
    <link href="<?php echo base_url().'bootstrap-admin/plugins/jvectormap/jquery-jvectormap-1.2.2.css'; ?>" rel="stylesheet" type="text/css" />
    <!-- Date Picker -->
    <link href="<?php echo base_url().'bootstrap-admin/plugins/datepicker/datepicker3.css'; ?>" rel="stylesheet" type="text/css" />
    <!-- Daterange picker -->
    <link href="<?php echo base_url().'bootstrap-admin/plugins/daterangepicker/daterangepicker-bs3.css'; ?>" rel="stylesheet" type="text/css" />
    <!-- bootstrap wysihtml5 - text editor -->
    <link href="<?php echo base_url().'bootstrap-admin/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'; ?>" rel="stylesheet" type="text/css" />
	<script src="<?php echo base_url().'bootstrap-admin/plugins/jQuery/jQuery-2.1.3.min.js'; ?>"></script>
	
<style>
div.gallery {
  margin: 5px;
  border: 1px solid #ccc;
  float: left;
  width: 165px;
}

div.gallery:hover {
  border: 1px solid #777;
}

div.gallery img {
  width: 100%;
  height: auto;
}

div.desc {
  padding: 10px;
  text-align: center;
  overflow:hidden;
  height:65px;
}
</style>    
</head>

<body>
<div class="col-12">
<div class="row">
                      <div class="col-md-12 mx-auto">
						<hr style="border:#aaa 2px solid;" />
						<div class="row text-center">
							<div class="form-group">
								<label class="col-md-2 col-md-offset-1 text-right" style="margin-top: 7px;">Upload New File -</label>
								<div class="col-md-6">
									<input type="file" class="form-control" name="files" id="files" />
									<small class="text-error files"><?php echo form_error('files'); ?></small>
								</div>
								<div class="col-md-1 text-left"><input type="button" id="p_id_submit" class="btn btn-primary" onclick="imageupload();" value="Upload" /></div>
							</div>
							<div class="form-group">
								<div class="col-sm-offset-1 col-sm-9">
									<div class="get_success_total" align="center" style="background-color: #174b10;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
									<div class="get_success" align="center" style="display:none"><img src="<?php echo base_url('images/ajax_loader.gif'); ?>" style="max-width: 60px;" /></div>
									<div class="get_error_total" align="center" style="background-color: #bf0000;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px; display:none"></div>
								</div>
							</div>
						</div>
						<hr style="border:#aaa 2px solid;" />
                        <ul class="nav nav-pills nav-pills-custom" id="pills-tab" role="tablist">
                          <li class="nav-item active">
                            <a class="nav-link active in" id="pills-home-tab" data-toggle="pill" href="#pills-health" role="tab" aria-controls="pills-home" aria-selected="true"> IMAGE </a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-career" role="tab" aria-controls="pills-profile" aria-selected="false"> DOCUMENT </a>
                          </li>
                        </ul>
                        <div class="tab-content tab-content-custom-pill" id="pills-tabContent">
                          <div class="tab-pane fade active in" id="pills-health" role="tabpanel" aria-labelledby="pills-home-tab">
                          <?php foreach($image as $row){ ?>
                       		<div class="gallery" align="center">
                                <a href="javascript:select_image('<?php echo base_url('upload_file/file_doc/'.$row->up_file); ?>');">
                                   <img src="<?php echo base_url('upload_file/file_doc/'.$row->up_file); ?>" alt="<?php echo $row->up_file ?>" class="profile-pic"  style="width:120px; height:100px;margin-top:10px;">
                                </a>
                                <div class="desc"><?php echo $row->up_file ?></div>
                              </div>
                       		
                          <?php } ?>
                       
                       
                          </div>
                          <div class="tab-pane fade" id="pills-career" role="tabpanel" aria-labelledby="pills-profile-tab">
                            
                            <table id="example" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                              <thead  style="font-weight: bold;">
                                <tr>
                                       <th width="13%"><strong>SL NO.</strong></th>
                                       <th ><strong>Document Name</strong> </th>
                                      <th width="8%"><strong>Action</strong></th>
                                 
                                </tr>
                              </thead>
                              <tbody>
                          <?php $a=1;  foreach($doc as $users)
                                  { ?>
                                <tr >
                                      <td ><?php echo $a++; ?></td>
                                      <td><?php echo $users->up_file ?></td>
                                    
                                      
                                  <td>
          
                                      <a class="btn btn-primary" href="javascript:select_image('<?php echo base_url('upload_file/file_doc/'.$users->up_file); ?>');" >ADD</a>
                                      
                                      </td>
                                  
                                </tr>
                             <?php } ?>
                              </tbody>
                            </table>
                            
                            
                          </div>
                      
                        </div>
                      </div>
                    </div>
                
 </div>

</body>
<!-- jQuery 2.1.3 -->
    <script src="<?php echo base_url().'bootstrap-admin/plugins/jQuery/jQuery-2.1.3.min.js'; ?>"></script>
    <!-- jQuery UI 1.11.2 -->
    <!--<script src="https://code.jquery.com/ui/1.11.2/jquery-ui.min.js" type="text/javascript"></script>-->
    <script src="<?php echo base_url().'bootstrap-admin/dist/js/jquery-ui.min.js'; ?>" type="text/javascript"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
      $.widget.bridge('uibutton', $.ui.button);
    </script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="<?php echo base_url().'bootstrap-admin/bootstrap/js/bootstrap.min.js'; ?>" type="text/javascript"></script>    
    <!-- Morris.js charts -->
    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>-->
    <script src="<?php echo base_url().'bootstrap-admin/dist/js/raphael-min.js'; ?>" type="text/javascript"></script>
    <script src="<?php echo base_url().'bootstrap-admin/plugins/morris/morris.min.js'; ?>" type="text/javascript"></script>
    <!-- Sparkline -->
    <script src="<?php echo base_url().'bootstrap-admin/plugins/sparkline/jquery.sparkline.min.js'; ?>" type="text/javascript"></script>
    <!-- jvectormap -->
    <script src="<?php echo base_url().'bootstrap-admin/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js'; ?>" type="text/javascript"></script>
    <script src="<?php echo base_url().'bootstrap-admin/plugins/jvectormap/jquery-jvectormap-world-mill-en.js'; ?>" type="text/javascript"></script>
    <!-- jQuery Knob Chart -->
    <script src="<?php echo base_url().'bootstrap-admin/plugins/knob/jquery.knob.js'; ?>" type="text/javascript"></script>
    <!-- daterangepicker 
    <script src="<?php echo base_url().'bootstrap-admin/plugins/daterangepicker/daterangepicker.js'; ?>" type="text/javascript"></script>-->
    <!-- datepicker
    <script src="<?php echo base_url().'bootstrap-admin/plugins/datepicker/bootstrap-datepicker.js'; ?>" type="text/javascript"></script> -->
    <script src="<?php echo site_url().'bootstrap-admin/plugins/timepicker/bootstrap-timepicker.min.js'; ?>" type="text/javascript"></script>
    <!-- Bootstrap WYSIHTML5 -->
    <script src="<?php echo base_url().'bootstrap-admin/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'; ?>" type="text/javascript"></script>
    <!-- iCheck -->
    <script src="<?php echo base_url().'bootstrap-admin/plugins/iCheck/icheck.min.js'; ?>" type="text/javascript"></script>
    <!-- Slimscroll -->
    <script src="<?php echo base_url().'bootstrap-admin/plugins/slimScroll/jquery.slimscroll.min.js'; ?>" type="text/javascript"></script>
    <!-- FastClick -->
    <script src="<?php echo base_url().'bootstrap-admin/plugins/fastclick/fastclick.min.js'; ?>"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo base_url().'bootstrap-admin/dist/js/app.min.js'; ?>" type="text/javascript"></script>

    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <!--<script src="<?php echo base_url().'bootstrap-admin/dist/js/pages/dashboard.js'; ?>" type="text/javascript"></script>-->

    <!-- AdminLTE for demo purposes -->
    <!--<script src="<?php echo base_url().'bootstrap-admin/dist/js/demo.js'; ?>" type="text/javascript"></script>-->
</html>
<script type="text/javascript">
$(function(){
	      //alert('123OK');
		  $('#files').val('');
	});
function select_image(url) {
var CKEditorFuncNum = <?php echo $_GET['CKEditorFuncNum']; ?>;
window.parent.opener.CKEDITOR.tools.callFunction( CKEditorFuncNum,url, '' );
self.close();
}

	function imageupload(){
		//alert('OK');
		var upload = $('#files').val();
		$(".get_success").fadeIn();
		var files = $('#files')[0].files;
			
		var form_data = new FormData();
		form_data.append("files", files[0]);
		form_data.append("title","success");

		if(upload == ""){
			$(".get_error_total").html('Select a File');
			$(".get_success").fadeOut();
			$(".get_error_total").fadeIn();
			delay = 3000;
			setTimeout(function(){ $('.get_error_total').fadeOut(); }, delay);
		}else{
			$("#p_id_submit").attr("disabled", "disabled");
			jQuery.ajax({
				type: "POST",
				url: "<?php echo site_url('admincontrol/Cmspage/add_all_doc') ?>",
				dataType: 'json',
				data: form_data,
				contentType:false,
				cache:true,
				processData:false,
				success: function(res) {
				//alert(res);
				if(res =='success'){
						
						$(".get_success").fadeOut();
						$(".get_success_total").html('File is Uploaded Successfully.');
						
						$(".get_success_total").fadeIn();
						delay = 3000;
						setTimeout(function(){ $('.get_success_total').fadeOut(); }, delay);
						location.reload();
						//setTimeout(function(){ window.location.replace("<?php echo site_url('admincontrol/gallery/image_list')?>/"); }, delay);
					
					}else{
					
						$(".get_success").fadeOut();
						$('.get_error_total').html(res);
						$('.get_error_total').fadeIn();	
						$("#p_id_submit").removeAttr('disabled');
						delay = 6000;
						setTimeout(function(){ $('.get_error_total').fadeOut(); }, delay);	
					}
				
				}
			});
		}
	}
</script>