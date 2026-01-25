<?php $this->load->view('admin/component/header') ?>

<?php $this->load->view('admin/component/menu') ?>
<link href="<?php echo base_url().'bootstrap-admin/plugins/datatables/dataTables.bootstrap.css'; ?>" rel="stylesheet" type="text/css" />
<script type="text/javascript">
	$(function(){
	      $('#alert_msg').delay(6000).fadeOut();
	});
</script>
        
<!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Page List
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Page List</li>
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
                <div class="box-body table-responsive" >
                  <div class="col-sm-12">
				  <table  class="table table-striped" id="example123" >
                    <thead  style="font-weight: bold;">
                      <tr>
			 
	                  		<th>Serial No. </th>
	                  		<th>Page </th>
	                  		<th>Url </th>
	                  		<th>Details </th>
	                  		<th>Status </th>
	                  		<th>Action</th>
                       
						
						
                      </tr>
                    </thead>
                    <tbody>
					 <?php foreach($page_detail as $key=>$pages){ ?>
					  <tr>
							<td> <?php echo $key+1; ?></td>
							<td> <?php echo $pages->page_title; ?></td>
							<td> <?php if($pages->page_status == 1){ $link = base_url()."main/page/".$pages->url_link; } ?>
							<scan id="page_<?php echo $key+1; ?>"><?php echo $link; ?></scan>&nbsp;&nbsp;&nbsp;<a href="javascript:;" onclick="copyToClipboard('#page_<?php echo $key+1; ?>')" title="Copy Link"  class="text-danger"><i class="fa fa-copy text-secondary"></i>       </a></td>
							<td> <?php echo htmlentities(substr($pages->page_details, 0, 100)); ?></td>
							<td> <?php if($pages->page_status == 1){echo '<span style="color:green;">Active</span>';}else{echo '<span style="color:red;">InActive</span>';} ?></td>
							<td><a href="<?php echo base_url('admincontrol/Cmspage/edit_page/'.$pages->url_link); ?>" ><button class="btn btn-xs btn-warning">Edit</button></a></td>
					  </tr>
					  <?php } ?>
					  
                    </tbody>
                    
                  </table></div>
                </div><!-- /.box-body -->
                <div class="box-footer clearfix no-border"><?php if($this->session->userdata['utype'] == 1){ ?>	

                  <a href="<?= site_url('admincontrol/Cmspage/add_new_page') ?>" class="btn btn-danger pull-right"><i class="fa fa-plus"></i> Add New Page</a><?php } ?>

                </div>
              </div><!-- /.box -->

            </section>
          </div><!-- /.row (main row) -->

        </section><!-- /.content -->
      </div><!-- /.content-wrapper --> 

<script>

function copyToClipboard(element) {
  var $temp = $("<input>");
  $("body").append($temp);
  $temp.val($(element).text()).select();
  document.execCommand("copy");
  $temp.remove();

}
</script>

<?php $this->load->view('admin/component/footer') ?>

<script src="<?php echo base_url().'bootstrap-admin/plugins/datatables/jquery.dataTables.js'; ?>" type="text/javascript"></script>
<script src="<?php echo base_url().'bootstrap-admin/plugins/datatables/dataTables.bootstrap.js'; ?>" type="text/javascript"></script>
<script type="text/javascript">
      $(function () {
        $("#example123").dataTable();
      });
    </script>