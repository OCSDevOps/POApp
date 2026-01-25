<?php $this->load->view('admin/component/header'); ?>
<?php $this->load->view('admin/component/menu'); ?>

<link href="<?php echo base_url().'bootstrap-admin/plugins/datatables/dataTables.bootstrap.css'; ?>" rel="stylesheet" type="text/css" />
<style>
.td_image{ border-radius:2px !important;
		width:70px !important;
		
}

</style>


<div class="content-wrapper">
		<section class="content-header">
          <h1>
            Menu List
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Menu List</li>
          </ol>
        </section>

		<!-- Main content -->
        <section class="content">
          <!-- Main row -->
			<div class="row">
            <section class="col-lg-12">
              <!-- Custom tabs (Charts with tabs)-->
			<?php if(!empty($msg)){ if($msg == "success"){ ?><div id="alert_msg" class="alert alert-success">New Menu is Insert Successfully</div> <?php } } ?>
			<?php if(!empty($msg)){ if($msg != "success"){ ?><div id="alert_msg" class="alert alert-danger"><?php echo $msg ;  ?>
			</div> <?php } } ?>
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
				<!-- <div class="box-footer clearfix no-border">
                  <a href="<?php //site_url('admincontrol/dashboard/add_administrator') ?>" class="btn btn-primary pull-right"><i class="fa fa-user"></i>&nbsp; Add New User</a>
                </div> -->
				  <div class="table-responsive">
                  <table class="table table-striped" id="datatable_tab" width="100%">
	                  <thead style="font-weight: bold;">
	                  		<td>Sl No.</td>
	                  		<td>Name</td>
	                  		<td>Link</td>
	                  		<td>Parent Menu</td>
	                  		<td>Order</td>
	                  		<td>Create Date</td>
	                  		<td>Status</td>
							<td>Action</td>
	                  </thead>
                  	<tbody>
                  		<?php foreach($menu_list as $key=>$menus)
                  		{ ?>
                  		<tr>
                  			<td><?php echo $key + 1; ?></td>
							<td style="max-width: 200px;"><?php echo $menus->menu_name; ?></td>
                  			<td style="max-width: 250px;"><?php echo $menus->menu_link; ?></td>
                  			<td><?php echo $menus->p_menu; ?></td>
                  			<td><?php echo $menus->menu_order; ?></td>
                  			<td><?php echo date('d-m-Y',strtotime($menus->menu_createdate)); ?></td>
                  			<td><?php if($menus->menu_status == 1)
                  						echo '<font style="color:green;">Active</font>';
                  					  else
                  					  	echo '<font style="color:red;">Inactive</font>'; ?></td>
                  			<td>
                  				<a href="<?php echo base_url().'admincontrol/menupanel/edit_menu/'.$menus->menu_id; ?>" title="Edit"><i class="fa fa-edit text-danger"></i></a>
                  				<?php if($menus->menu_status == 1){ ?>	
                  				<a href="<?php echo base_url().'admincontrol/menupanel/lock_menu/'.$menus->menu_id; ?>" title="Lock"><i class="fa fa-unlock text-danger"></i></a>
                  				<?php } else { ?>
								<a href="<?php echo base_url().'admincontrol/menupanel/unlock_menu/'.$menus->menu_id; ?>" title="Unock"><i class="fa fa-lock text-danger"></i></a>
								<?php } ?>
								<a onclick="return confirm('You are about to delete a record. This cannot be undone. Are you sure?');" href="<?php echo base_url().'admincontrol/menupanel/delete_menu/'.$menus->menu_id; ?>" title="Delete"><i class="fa fa-trash-o text-danger"></i></a>
                  				
                  			</td>
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

</div>
		  
		  
		  
<div class="modal fade" id="demoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content col-sm-9"  >
<div class="modal-header">

<h4 class="modal-title" id="myModalLabel">Do you want To Delete The FILE</h4>
</div>
<div class="modal-body">
<div class="row">
		    				<div class="col-xs-6 col-sm-6 col-md-6" align="center" id="news_delete_link">
			    				</div>
								<div class="col-xs-6 col-sm-6 col-md-6" align="center">
			    					<div class="form-group">

			    			<button type="button" class="btn btn-info" data-dismiss="modal">NO</button>
			    					</div>
			    				</div>
			    			</div>
</div>
<div class="modal-footer">
<!---<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>-->
</div>
</div>
</div>
</div>	  
		  
<script type="text/javascript">
function delete_image(id){
//alert(id);

 $("#news_delete_link").empty();

 $("#news_delete_link").append("<a href='<?php echo base_url()."admincontrol/gallery/delete_document/"; ?>"+id+"' class='btn btn-warning' >YES</a>");
$('#demoModal').modal('show');
}
</script>		  
<script>

function copyToClipboard(element) {
  var $temp = $("<input>");
  $("body").append($temp);
  $temp.val($(element).text()).select();
  document.execCommand("copy");
  $temp.remove();
  alert('copied');

}
</script>		  
<script type="text/javascript">
	$(function(){
	      $('.alert').delay(6000).fadeOut();
	});
</script>		  
<?php $this->load->view('admin/component/footer'); ?>
<script src="<?php echo base_url().'bootstrap-admin/plugins/datatables/jquery.dataTables.js'; ?>" type="text/javascript"></script>
<script src="<?php echo base_url().'bootstrap-admin/plugins/datatables/dataTables.bootstrap.js'; ?>" type="text/javascript"></script>
<script type="text/javascript">
      $(function () {
        $("#datatable_tab").dataTable();
      });
    </script>
