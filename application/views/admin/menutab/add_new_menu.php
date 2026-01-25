<?php $this->load->view('admin/component/header'); ?>
<?php $this->load->view('admin/component/menu'); ?>
<style>
label{ font-weight:bold; }
input[type="number"], input[type="file"]{
  padding: 0.64rem 1.375rem !important;
}
.alert-error, .text-error, .redclass {
    	color: red !important;
	}
</style>


<div class="content-wrapper">
        <section class="content-header">
			  <h1>
			  New Menu
			  </h1>
			  <ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li class="active">New Menu</li>
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
                  <h3 class="box-title">Add New User</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
				
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
				    <label class="col-sm-3 control-label text-right">Menu Name<font style="color: red;">*</font></label>
				    <div class="col-sm-3">
				      <input type="text" class="form-control" name="m_name" id="m_name" placeholder="Enter Menu Name" autocomplete="off" />
				      <small class="text-error m_name"><?php echo form_error('m_name'); ?></small>
				    </div>
				    <label class="col-sm-2 control-label text-right">Menu Bengali Name</label>
				    <div class="col-sm-3">
				      <input type="text" class="form-control" name="menu_name_bengali" id="menu_name_bengali" placeholder="Enter Menu Bengali Name" autocomplete="off" />
				      <small class="text-error menu_name_bengali"><?php echo form_error('menu_name_bengali'); ?></small>
				    </div>
				 </div>
				 <div class="form-group">
				    <label class="col-sm-3 control-label text-right">Menu Link<font style="color: red;">*</font></label>
				    <div class="col-sm-6">
				      <input type="text" class="form-control" name="m_link" id="m_link" placeholder="Enter Menu Link" autocomplete="off" />
				      <small class="text-error m_link"><?php echo form_error('m_link'); ?></small>
				    </div>
				 </div>
				 <div class="form-group">
				  	<label class="col-sm-3 control-label text-right">Menu Open in New Tab<font style="color: red;">*</font></label>
				    <div class="col-sm-6">
						<label class="radio-inline"><input class="form-check-input" type="radio" name="menu_new_tab_open" id="menu_new_tab_open_1" value="Y" autocomplete="off">YES</label>
						<label class="radio-inline"><input class="form-check-input" type="radio" name="menu_new_tab_open" id="menu_new_tab_open_2" value="N" checked="checked" autocomplete="off">NO</label>
						<small class="text-error menu_new_tab_open"><?php echo form_error('menu_new_tab_open'); ?></small>
				    </div>
				  </div>
				  <div class="form-group">
				  	<label class="col-sm-3 control-label text-right">Menu Type<font style="color: red;">*</font></label>
				    <div class="col-sm-6">
						<label class="radio-inline"><input class="form-check-input" type="radio" name="m_type" id="m_type_1" value="0" checked="checked" autocomplete="off">Primary</label>
						<label class="radio-inline"><input class="form-check-input" type="radio" name="m_type" id="m_type_2" value="1" autocomplete="off">Sub Menu</label>
						<label class="radio-inline"><input class="form-check-input" type="radio" name="m_type" id="m_type_3" value="2" autocomplete="off">Sub-Sub Menu</label>
						<label class="radio-inline"><input class="form-check-input" type="radio" name="m_type" id="m_type_4" value="3" autocomplete="off">Sub-Sub-Sub Menu</label>
						<small class="text-error m_type"><?php echo form_error('m_type'); ?></small>
				    </div>
				  </div>
					<div class="form-group super_menu" id="super_menu" style="display:none">
						<label class="col-sm-3 control-label text-right">Primary Menu<font style="color: red;">*</font></label>
						<div class="col-sm-3">
						  <select class="form-control" name="m_primary" id="m_primary">
							<option value="">---Select---</option>
							<?php foreach($main_menu_list as $main_menu){ ?>
							<option value="<?php echo $main_menu->menu_id; ?>"><?php echo $main_menu->menu_name; ?></option>
							<?php } ?>
						  </select>
						  <small class="text-error m_primary"><?php echo form_error('m_primary'); ?></small>
						</div>
					</div>
					<div class="form-group sub_menu" id="sub_menu" style="display:none">
						<label class="col-sm-3 control-label text-right">Sub Menu<font style="color: red;">*</font></label>
						<div class="col-sm-3">
						  <select class="form-control" name="m_sub" id="m_sub">
							<option value="">---Select---</option>
							<?php foreach($submenu_list as $mainsub_menu){ ?>
							<option value="<?php echo $mainsub_menu->menu_id; ?>"><?php echo $mainsub_menu->menu_name; ?></option>
							<?php } ?>
						  </select>
						  <small class="text-error m_sub"><?php echo form_error('m_sub'); ?></small>
						</div>
					</div>
					<div class="form-group sub_sub_menu" id="sub_sub_menu" style="display:none;" >
						<label class="col-sm-3 control-label text-right">Sub Sub Menu<font style="color: red;">*</font></label>
						<div class="col-sm-3">
							<select class="form-control" name="m_sub_sub" id="m_sub_sub">
								<option value="">---Select---</option>
								<?php foreach($sub_sub_menu_list as $main_subsub_menu){ ?>
								<option value="<?php echo $main_subsub_menu->menu_id; ?>"><?php echo $main_subsub_menu->menu_name; ?></option>
								<?php } ?>
							</select>
							<small class="text-error m_sub_sub"><?php echo form_error('m_sub_sub'); ?></small>
						</div>
					</div>
					<div class="form-group">
                        <label class="col-sm-3 control-label text-right">Menu Position</label>
						<div class="col-sm-3">
							<input type="text" class="form-control" name="m_order" id="m_order" placeholder="Enter Menu Position" autocomplete="off" />
							<small>(Leave blank OR enter 0 for 1st position)</small><br/>
							<small class="text-error m_order"><?php echo form_error('m_order'); ?></small>
						</div>	                        
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
						  &nbsp;<a href="<?= site_url('admincontrol/menupanel/menulist') ?>" class="btn btn-danger">Cancel</a>
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
		  
		  
		  
		  
		  
		  
		  
<?php $this->load->view('admin/component/footer'); ?>
<script type="text/javascript">
	$(function(){
	      $('.alert-error, .alert, .text-error').delay(6000).fadeOut();
	      //$('#ajx_load').hide();
	      
	      /*$("#formsub").submit(function(event){
	      	$('#ajx_load').show();
	      });*/
	      
	      $('.sub_sub_menu, .super_menu, .sub_menu').hide();
	      
	      $('input:radio[name="m_type"]').click(function(){
		//  alert('ww');
		        if($(this).attr("value")=="0"){
		            $('.sub_sub_menu, .super_menu, .sub_menu').hide(300);
		        }
		        if($(this).attr("value")=="1"){
		        	$('.sub_menu').hide(300);
		        	$('.sub_sub_menu').hide(300);
		            $('.super_menu').show(300);
		        }
		        if($(this).attr("value")=="2"){
		            $('.super_menu').hide(300);
		            $('.sub_sub_menu').hide(300);
		            $('.sub_menu').show(300);
		        }
		        if($(this).attr("value")=="3"){
		            $('.super_menu').hide(300);
		            $('.sub_menu').hide(300);
		            $('.sub_sub_menu').show(300);
		        }
		    });
	      
	});

	function gotoclclickbutton(){
		$('.div_roller_total').fadeIn();
		var delay = 8000;
		var e_error = 0;
		var error_message = 'There have some errors plese check above, Try again.';
		var alphaletters = /^[A-Za-z ]+$/;
		var alphanumerics = /^[A-Za-z0-9 ]+$/;
		var alphanumerics_spaces = /^[A-Za-z0-9_,\- ]+$/;
		var onlynumerics = /^[0-9]+$/;
    	var specials_char = /[~`!#$%\^&*+=\[\]\\';./{}()|\\":<>\?]/g;
		var specials_links = /[~`!\^*\[\]\\'{}()|\\"<>]/g;
    
		var m_name = $('#m_name').val();
		var menu_name_bengali = $('#menu_name_bengali').val();
		var m_link = $('#m_link').val();
		var m_order = $('#m_order').val();
		var m_type = $('input[name="m_type"]:checked').val();
		var m_primary = $( "#m_primary option:selected" ).val();
		var m_sub = $( "#m_sub option:selected" ).val();
		var m_sub_sub = $( "#m_sub_sub option:selected" ).val();

		if(m_type == ""){
			e_error = 1;
			$('.m_type').html('Menu Type need to Select.');
		}else{
			if(m_type.match(onlynumerics)){
				if(m_type == 1){
					if(m_primary == ""){
						e_error = 1;
						$('.m_primary').html('Primary Menu need to Select.');
					}else{
						$('.m_primary').html('');
					}
				}else if(m_type == 2){
					if(m_sub == ""){
						e_error = 1;
						$('.m_sub').html('Sub Menu need to Select.');
					}else{
						$('.m_sub').html('');
					}
				}else if(m_type == 3){
					if(m_sub_sub == ""){
						e_error = 1;
						$('.m_sub_sub').html('Sub-Sub Menu need to Select.');
					}else{
						$('.m_sub_sub').html('');
					}
				}
			}else{
				e_error = 1;
				$('.m_type').html('Menu Type Value not proper.');
			}
		}

		if(m_name == ""){
			e_error = 1;
			$('.m_name').html('Menu Name is Required.');
		}else{
			if(!m_name.match(alphanumerics_spaces)){
				e_error = 1;
				$('.m_name').html('Menu Name not use special carecters (without _ , -), Check again.');
			}else{
				$('.m_name').html('');
			}	
		}
		if(m_order != ""){
			if(!m_order.match(onlynumerics) || (m_order.length > 2)){
				e_error = 1;
				$('.m_order').html('Menu Order is Numeric value only and Maximum 2 digit.');
			}else{
				$('.m_order').html('');
			}	
		}
		if(menu_name_bengali != ""){
			if(specials_char.test(menu_name_bengali)){
				e_error = 1;
				$('.menu_name_bengali').html('Bengali Name not use special carecters (without , -), Check again.');
			}else{
				$('.menu_name_bengali').html('');
			}
		}
		if(m_link == ""){
			e_error = 1;
			$('.m_link').html('Menu Link is Required.');
		}else{
			if(specials_links.test(m_link)){
				e_error = 1;
				$('.m_link').html('Menu Link not use special carecters, Check again.');
			}else{
				$('.m_link').html('');
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
			//alert(newhash);
			//alert(rehash);
			$("#myForm").submit();
		}

	}

</script>