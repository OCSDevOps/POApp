<?php $this->load->view('admin/component/header'); ?>
<?php $this->load->view('admin/component/menu'); ?>
<link href="<?php echo base_url().'bootstrap-admin/plugins/datatables/dataTables.bootstrap.css'; ?>" rel="stylesheet" type="text/css" />
<style>
.td_image{ border-radius:2px !important;
		width:70px !important;
		
}

</style>

<div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
          Upload File List
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">File list</li>
          </ol>
        </section>

        <!-- Main content -->
        <div class="content">
          <!-- Main row -->
          <div class="row">
            <div class="col-lg-12">
           	
				<?php if($this->session->flashdata('success')) { ?>
				<div id="alert_msg" class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
				<?php $this->session->unset_userdata('success'); }
					elseif($this->session->flashdata('e_error')) { ?>                
				<div id="alert_msg" class="alert alert-danger"><?php echo $this->session->flashdata('e_error'); ?></div>
				<?php $this->session->unset_userdata('e_error'); } ?>
            
				<div class="box box-warning" >

                <div class="box-body">
                
                
				  	<div class="table-responsive">
					
                                    <table  id="datatable_tab" width="100%" class="table table-bordered table-hover table-striped">
                                        <thead>
                                            <tr>
												 <th width="10%"><strong>SL NO.</strong></th>
												 <th width="50%" ><strong>File Name</strong> </th>
												 <th ><strong>Type</strong> </th>
												  <th ><strong>Link</strong> </th>
												 
												 <th ><strong>File</strong> </th>
											   
												<th width="8%"><strong>Action</strong></th>
										   
											</tr>
                                    
											
											</thead>
									<tbody>
				<?php if(!empty($view)){ $a=1;  foreach($view as $key=>$users){
				$link =  base_url('upload_file/file_doc/'.$users->up_file);
                  		?>
                      <tr >
					        <td ><?php echo $a; ?></td>
                              <td ><?php echo $users->up_file ?> </td>
                               <td ><?php echo $users->up_type ?> </td>
							<td><scan  style="display:none;" id="page_<?php echo $key; ?>"><?php echo $link; ?></scan>	<a href="javascript:;" onclick="copyToClipboard('#page_<?php echo $key; ?>')" title="Copy Link" > <button type="button" class="btn btn-inverse-primary btn-rounded btn-icon"><i class="fa fa-copy"></i>
                          </button>    </a></td>
                            <td>
                            
							 <?php
				  $filename =$users->up_file; $ext = pathinfo($filename, PATHINFO_EXTENSION);
				 	
				   if($ext == 'gif' || $ext == 'png' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'icon') { ?>		  
				  <a href="<?php echo base_url('upload_file/file_doc/'.$users->up_file); ?>" target="_blank"> <img class="td_image" src="<?php echo base_url('upload_file/file_doc/'.$users->up_file); ?>" style="width:70px;height:70px;" /></a>
				  
				<?php }else{ ?>	
				
				 <a href="<?php echo base_url('upload_file/file_doc/'.$users->up_file); ?>" target="_blank"> <img class="td_image" src="<?php echo base_url('images/document.png'); ?>" style="width:70px;height:70px;" /></a>
				 
				 <?php } ?>	
				</td>
                  		<td>

						<a onclick="return confirm('You are about to delete a record. This cannot be undone. Are you sure?');" href="<?php echo base_url().'admincontrol/Cmspage/delete_document/'.$users->up_id; ?>" title="Delete Record"><i class="fa fa-trash text-danger"></i></a>
                            </td>
						
                      </tr>
                   <?php $a++; } } ?>
                    </tbody>
											</table>
											
                        
						

                </div><!-- /.box-body -->
                
              </div><!-- /.box -->
			  
			</div>
            </div>
          </div><!-- /.row (main row) -->

        </div><!-- /.content -->
      </div><!-- /.content-wrapper --> 
	

<?php $this->load->view('admin/component/footer') ?>
<script src="<?php echo base_url().'bootstrap-admin/plugins/datatables/jquery.dataTables.js'; ?>" type="text/javascript"></script>
<script src="<?php echo base_url().'bootstrap-admin/plugins/datatables/dataTables.bootstrap.js'; ?>" type="text/javascript"></script>


		  		  
<script type="text/javascript">

function copyToClipboard(element) {
  var $temp = $("<input>");
  $("body").append($temp);
  $temp.val($(element).text()).select();
  document.execCommand("copy");
  $temp.remove();
  alert('copied');

}

	$(function(){
			$("#datatable_tab").dataTable();
			$('.alert').delay(4000).fadeOut();
	});

    function delete_image(id){
			$("#myModalLabel").html('Do you want To Remove the File !!');
			 $("#news_delete_link").empty();
			 $("#news_delete_link").append("<a href='javascript:;' onclick='deletetherecord("+id+");' class='btn btn-warning' >YES</a>");
			$('#delete_Modal').modal('show');
    }
    
  function deletetherecord(record_id){
    var csrfName = $('#csrfName').val();
    var csrfHash = $('#csrfHash').val();
    jQuery.ajax({
        type: "POST",
        url: "<?php echo site_url('admincontrol/gallery/delete_document') ?>",
        dataType: 'json',
        data: {[csrfName]: csrfHash, record_id:record_id},
          success: function(data) { if (data.msg == 1){
            $('#delete_Modal').modal('hide');
            $('#success_Modal').modal('show');
            setTimeout(function(){ $('#success_Modal').modal('hide'); }, 3000);
		        setTimeout(function(){ window.location.replace("<?php echo site_url('admincontrol/Gallery/upload_file_list')?>"); }, 2000);
          
            }else{
                $('#delete_Modal').modal('hide');
                $error_message = "There have some problem to Save the Data, Try again.";
                alert($error_message);
            }
        }

      });
  }
</script>		  

