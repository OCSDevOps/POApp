<?php $this->load->view('admin/component/header') ?>

<?php $this->load->view('admin/component/menu') ?>

<link href="<?php echo base_url().'bootstrap-admin/plugins/datatables/dataTables.bootstrap.css'; ?>" rel="stylesheet" type="text/css" />


        
<!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
		  Event List
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Event List</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <!-- Main row -->
          <div class="row">
            <section class="col-lg-12">
              <!-- Custom tabs (Charts with tabs)-->
			
			<?php if($this->session->flashdata('success')) { ?>
			<div id="alert_msg" class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
		    <?php $this->session->unset_userdata('success'); }
		    	elseif($this->session->flashdata('e_error')) { ?>                
	        <div id="alert_msg" class="alert alert-danger"><?php echo $this->session->flashdata('e_error'); ?></div>
		    <?php $this->session->unset_userdata('e_error'); } ?>
			
              <!-- TO DO List -->
              <div class="box box-warning">
                <!-- /.box-header -->
                <div class="box-body">
				  <div class="table-responsive">
                  <table class="table table-striped" id="datatable_tab" width="100%">
	                  <thead style="font-weight: bold;">
	                  		<td>Sl No.</td>
	                  		<td>Event No.</td>
	                  		<td>Event Name</td>
	                  		<td>Event Start Time</td>
	                  		<td>Event End Time</td>
	                  		<td>Contact Person</td>
	                  		<td>CP Mobile</td>
	                  		<td>Create Date</td>
	                  		<td>Is Approve</td>
	                  		<?php if($this->session->userdata['utype'] <= 2){ ?>
	                  		<td>Action</td>
							<?php } ?>
	                  </thead>
                  	<tbody>
                  		<?php foreach($ev_list as $keys=>$quaries)
                  		{ ?>
                  		<tr>
                  			<td><?php echo $keys+1; ?></td>
                  			<td><?php echo $quaries->event_no; ?></td>
                  			<td><?php echo $quaries->event_name; ?></td>
                  			<td><?php echo date('d-m-Y h:i A',strtotime($quaries->event_startdate)); ?></td>
                  			<td><?php echo date('d-m-Y h:i A',strtotime($quaries->event_enddate)); ?></td>
                  			<td><?php echo $quaries->event_contact_person; ?></td>
							<td><?php echo $quaries->event_cp_mobile; ?></td>
                  			<td><?php echo date('d-m-Y',strtotime($quaries->event_createdate)); ?></td>
							<td><?php if($quaries->event_approval == 0){ echo '<span style="color:blue">Not Given Yet</span>';}elseif($quaries->event_approval == 1){echo '<span style="color:green">Approved</span>';}elseif($quaries->event_approval == 2){echo '<span style="color:red">Rejected</span>';} ?></td>
							<?php if($this->session->userdata['utype'] <= 2){ ?>
                  			<td>
                  				<!--<a onclick="goto_details_view('<?php echo $quaries->event_no; ?>');" href="javascript:;" title="View Record"><i class="fa fa-eye text-warning"></i></a>&nbsp;-->
                  				<?php if($quaries->event_approval == 0){ ?>
									<a href="<?php echo base_url().'admincontrol/event_set/approve_the_event/'.$quaries->event_no; ?>" title="Approve Record"><i class="fa fa-thumbs-o-up text-warning"></i></a>&nbsp;
									<a href="<?php echo base_url().'admincontrol/event_set/reject_the_event/'.$quaries->event_no; ?>" title="Reject Record"><i class="fa fa-thumbs-o-down text-warning"></i></a>&nbsp;
								<?php } ?>
								<!--<a onclick="return confirm('You are about to Reject a record. This cannot be undone. Are you sure?');" href="<?php //echo base_url().'admincontrol/event_set/delete_the_event/'.$quaries->event_no; ?>" title="Delete Record"><i class="fa fa-trash-o text-warning"></i></a>-->
                  			</td>
							<?php } ?>
                  		</tr>	
                  		<?php } ?>
                  	</tbody>
                  </table>
				  </div>
                </div><!-- /.box-body -->
                
              </div><!-- /.box -->

            </section>
          </div><!-- /.row (main row) -->

        </section><!-- /.content -->
      </div><!-- /.content-wrapper --> 



<div id="myModalView" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
<div class="modal-dialog modal-lg">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
		<h4 class="modal-title">Query Number : <span class="q_number_set"></span></h4>
	  </div>
	  <div class="modal-body">
			<div class="container-fluid">
				<div class="row">
				  <div class="col-sm-12" style="padding-bottom:5px;">
					<strong class="">Subject : <span class="q_subject_set"></span></strong>
				  </div>
				  <div class="col-md-7">
					<div class="alert-info" role="alert" style="padding:5px;">
						<div class="q_detail_set" style="margin-bottom:5px;"></div>
						<div class="text-bold mainAttachment">Attachment : <span class="q_attach_set"></span></div>
					</div>
				  </div>
				</div>
				<div>&nbsp;</div>
				<div class="row replytab">
				  <div class="col-md-7 col-md-offset-5">
					<div class="" style="padding:5px;">
						<strong class="">Administrator Reply:-</strong>
					</div>
					<div class="alert-warning" role="alert" style="padding:5px;">
						<div class="q_reply_set" style="margin-bottom:5px;"></div>
						<div class="text-bold haveAttachment">Attachment : <span class="q_reply_attach_set"></span></div>
					</div>
				  </div>
				</div>
			</div>
		
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
	  </div>
	</div>

</div>
</div>


<div id="myModalReply" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
<div class="modal-dialog modal-lg">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
		<h4 class="modal-title">Query Number : <span class="q_number_set"></span></h4>
	  </div>
	  <div class="modal-body">
			<div class="container-fluid">
				<div class="row">
				  <div class="col-sm-12" style="padding-bottom:5px;">
					<strong class="">Subject : <span class="q_subject_set"></span></strong>
				  </div>
				  <div class="col-md-7">
					<div class="alert-info" role="alert" style="padding:5px;">
						<div class="q_detail_set" style="margin-bottom:5px;"></div>
						<div class="text-bold mainAttachment">Attachment : <span class="q_attach_set"></span></div>
					</div>
				  </div>
				</div>
				<div class="row">
					<div class="form-group">
						<div class="col-sm-12 text-center">
							<h3>Administrator Reply</h3>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label text-right">Reply in Details <span style="color:red">*</span></label>
						<div class="col-sm-9">
						  <input type="hidden" name="query_no" id="query_no" autocomplete="off" />
						  <textarea class="form-control" name="reply_comment" style="resize:none;" id="reply_comment" autocomplete="off"></textarea>
						  <small class="text-error reply_comment"><?php echo form_error('reply_comment'); ?></small>
						</div>
					</div>
					<div style="clear:both;">&nbsp;</div>
					<div class="form-group">
						<label class="col-md-3 text-right" style="margin-top: 7px;">Upload Attachment</label>
						<div class="col-md-6">
							<input type="file" class="form-control" name="files" id="files" autocomplete="off" />
							<small class="text-error files"><?php echo form_error('files'); ?></small>
						</div>
					</div>
					<div style="clear:both;">&nbsp;</div>
					<div  class="col-sm-12 text-center">
						<div align="center">
							<div class="get_error_total" align="center" style="background-color: #bf0000;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
							<div class="get_success_total" align="center" style="background-color: #174b10;color: #fff;max-width: 500px;margin: 0 auto;padding: 10px 20px;display: none;"></div>
							<div class="div_roller_total" align="center" style="display: none;"><img src="<?php echo base_url(); ?>images/ajax_loader.gif" style="max-width: 60px;" /></div>
						</div>
					</div>
				</div>
			</div>
		
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
		<button type="button" class="btn btn-primary" onclick="goto_clickbutton_administrator();">Submit</button>
	  </div>
	</div>

</div>
</div>
<?php $this->load->view('admin/component/footer') ?>

<script src="<?php echo base_url().'bootstrap-admin/plugins/datatables/jquery.dataTables.js'; ?>" type="text/javascript"></script>
<script src="<?php echo base_url().'bootstrap-admin/plugins/datatables/dataTables.bootstrap.js'; ?>" type="text/javascript"></script>
	<script type="text/javascript">
		$(function(){
	      $('#alert_msg').delay(6000).fadeOut();
	      $("#datatable_tab").dataTable();
		});
		
		function goto_details_view(q_no){
			if(q_no != ""){
				$.ajax({
					method:'POST',
					url:'<?php echo base_url()."admincontrol/discussion/getinfo_fromquery_no"; ?>',
					data:{q_no: q_no},
					dataType:'JSON',
					success:function(data){
						//alert(data.msg);
						if(data.msg == 1)
						{
							//console.log(data);
							//alert(data.msg[0].space_rate);
							$('.q_number_set').html(q_no);
							if(data.info_set.query_is_reply == 0){
								$('.replytab').fadeOut();
							}else{
								$('.replytab').fadeIn();
								$('.q_reply_set').html(data.info_set.query_reply_details);
								if(data.info_set.query_reply_attach == null){
									$('.haveAttachment').fadeOut();
								}else{
									$('.haveAttachment').fadeIn();
									$('.q_reply_attach_set').html('<a href="<?php echo base_url(); ?>upload_file/forum_doc/reply/' + data.info_set.query_reply_attach + '" target="_blank" style="color:#111;">Attached Reply Document</a>');
								}
							}
							$('.q_subject_set').html(data.info_set.query_subject);
							$('.q_detail_set').html(data.info_set.query_details);
							if(data.info_set.query_attachment == null){
								$('.mainAttachment').fadeOut();
							}else{
								$('.mainAttachment').fadeIn();
								$('.q_attach_set').html('<a href="<?php echo base_url(); ?>upload_file/forum_doc/' + data.info_set.query_attachment + '" target="_blank" style="color:#111;">Attached Document</a>');
							}
							$('#myModalView').modal('show');
							//setTimeout(function(){ $('.get_success_total').fadeOut(); }, 3000);
							//setTimeout(function(){ window.location.replace("<?php echo site_url('member/testing_list')?>/"); }, 3000);
							
							
						}else{
							error_message = data.e_msg;
							$('.alert-error').html(error_message);
							$(".alert-error").fadeIn();
							//setTimeout(function(){ $('.alert-error').fadeOut(); }, delay);
						}
						
					}
				});
				/*$('#given_by').val(<?php echo $this->session->userdata['utype']; ?>);
				$('#work_no').val(tid);
				*/
			}
		}
		
		function goto_reply_view(q_no){
			if(q_no != ""){
				
				$.ajax({
					method:'POST',
					url:'<?php echo base_url()."admincontrol/discussion/getinfo_fromquery_no"; ?>',
					data:{q_no: q_no},
					dataType:'JSON',
					success:function(data){
						//alert(data.msg);
						if(data.msg == 1)
						{
							//console.log(data);
							//alert(data.msg[0].space_rate);
							$('.q_number_set').html(q_no);
							$('.q_subject_set').html(data.info_set.query_subject);
							$('.q_detail_set').html(data.info_set.query_details);
							if(data.info_set.query_attachment == null){
								$('.mainAttachment').fadeOut();
							}else{
								$('.mainAttachment').fadeIn();
								$('.q_attach_set').html('<a href="<?php echo base_url(); ?>upload_file/forum_doc/' + data.info_set.query_attachment + '" target="_blank" style="color:#111;">Attached Document</a>');
							}
							$('#files,#reply_comment').val('');
							$('#query_no').val(q_no);
							$('#myModalReply').modal('show');
							
							
						}else{
							error_message = data.e_msg;
							$('.alert-error').html(error_message);
							$(".alert-error").fadeIn();
							//setTimeout(function(){ $('.alert-error').fadeOut(); }, delay);
						}
						
					}
				});
				
			}
		}
		
		function goto_clickbutton_administrator(){
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
			var allowedExtensions = /(\.pdf|\.PDF|\.jpg|\.jpeg|\.png|\.JPG|\.JPEG|\.PNG|\.txt|\.doc|\.docx|\.xls|\.xlsx|\.ppt|\.pptx|\.mp4|\.MP4)$/i;
			
			var form_data = new FormData();
			//var stat_type = $('#stat_type option:selected').val();
			//var work_no = $('#work_no').val();
			//var given_by = $('#given_by').val();
			var reply_comment = $('#reply_comment').val();
			var query_no = $('#query_no').val();
			
			var upload = $('#files').val();
			var files = $('#files')[0].files;
			
			form_data.append("files", files[0]);
			form_data.append("reply_comment", reply_comment);
			form_data.append("query_no", query_no);
			
			if(query_no == ""){
				e_error = 1;
				error_message = error_message + '<br/>There have some problem in page ID, Reload the Page and Try again.';
			}
			
			if(reply_comment == ""){
				e_error = 1;
				$('.reply_comment').html('Reply is Required.');
			}else{
				reply_comment = reply_comment.replace(/(\r\n|\n|\r)/gm, " ");
				if(!reply_comment.match(alphanumerics_no)){
					e_error = 1;
					$('.reply_comment').html('Reply not use special carecters [without _ / : ( @ . & ) , -], Check again.');
				}else{
					$('.reply_comment').html('');
				}	
			}
			
			if(document.getElementById("files").files.length != 0){
				var fileInput = document.getElementById('files'); 
				var filePath = fileInput.value;
				if(!allowedExtensions.exec(filePath)){
					e_error = 1;
					$('.files').html('Attachment File type Invalid.');
				}else{
					$('.files').html('');
				}
				
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
				//alert(task_start_time);exit;
				//alert(rehash);
				//$("#myForm").submit();
				$.ajax({
					type: "POST",
					url: "<?php echo site_url('admincontrol/discussion/update_reply_against_query') ?>",
					dataType: 'json',
					data: form_data,
					contentType:false,
					cache:true,
					processData:false,
					success:function(data){
						//alert(data.msg);
						if(data.msg == 1)
						{
							//console.log(data);
							//alert(data.msg[0].space_rate);
							$('.div_roller_total').fadeOut();
							$('.get_success_total').html('Reply is updated agasint the Query Successfully.');
							$(".get_success_total").fadeIn();
							$('input, textarea').val('');
							$('input, textarea').html('');
							setTimeout(function(){ $('.get_success_total').fadeOut(); }, 3000);
							setTimeout(function(){ window.location.replace("<?php echo site_url('admincontrol/discussion/all_query_list')?>"); }, 3000);
							
						}else{
							$('.div_roller_total').fadeOut();
							error_message = "There have some problem to Update Data, Try again.";
							error_message = error_message + "<br/>" + data.e_msg;
							$('.get_error_total').html(error_message);
							$(".get_error_total").fadeIn();
							setTimeout(function(){ $('.get_error_total').fadeOut(); }, delay);
						}
						
					}
				});
			}
		}
		
    </script>