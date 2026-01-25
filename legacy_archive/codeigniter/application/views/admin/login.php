<?php $this->load->view("admin/login_comp/header"); ?>

<!-- Login box.scss -->
        <!-- ============================================================== -->
        <div class="auth-wrapper d-flex no-block justify-content-center align-items-center bg-dark">
            <div class="auth-box bg-dark border-top border-secondary">
                <div>
                    <div class="text-center p-t-20 p-b-20 mb-3">
                        <span class="db"><img src="<?php echo base_url(); ?>style/assets/images/logo.png" alt="logo" class="img-fluid" /></span>
                    </div>
                    <!-- Form -->
                    <form class="form-horizontal m-t-20" method="POST" enctype="multipart/form-data" id="recoverform" action="<?php echo base_url()."admin_access/forgot_password" ?>">
                        <div class="row p-b-30">
                            <div class="col-12">
								<?php if(isset($error)) :?>
				                <div class="alert alert-danger" id="alert_msg">
						            <?php 
							            echo $error;
						            ?>
								</div>
								<?php endif;?>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-success text-white" id="basic-addon1"><i class="ti-email"></i></span>
                                    </div>
                                    <input type="text" class="form-control form-control-lg" id="email" name="email" autocomplete="off" placeholder="Email Address" aria-label="Email" aria-describedby="basic-addon1" required="">
									<small class="text-error"><?php echo form_error('email');?></small>
                                        
                                </div>
                            </div>
                        </div>
                        <div class="row border-top border-secondary">
                            <div class="col-12">
                                <div class="form-group">
                                    <div class="p-t-20">
										<button class="btn btn-info" id="to-login" type="button"><i class="fa fa-lock m-r-5"></i> Login</button>
										<button class="btn btn-success float-right" type="submit">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

					<form class="form-horizontal m-t-20" method="POST" id="loginform" action="">
                        <div class="row p-b-30">
                            <div class="col-12">
								<?php if(isset($error)) :?>
				                <div class="alert alert-danger" id="alert_msg">
						            <?php
							            echo $error;
						            ?>
								</div>
								<?php endif;?>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-success text-white" id="basic-addon1"><i class="ti-user"></i></span>
                                    </div>
                                    <input type="text" class="form-control form-control-lg" id="username" name="username" autocomplete="off" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1" required="">
									<small class="text-error"><?php echo form_error('username');?></small>

                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-warning text-white" id="basic-addon2"><i class="ti-pencil"></i></span>
                                    </div>
                                    <input type="password" class="form-control form-control-lg" id="password" name="password" autocomplete="off" placeholder="Password" aria-label="Password" aria-describedby="basic-addon1" required="">
									<small class="text-error"><?php echo form_error('password');?></small>

                                </div>
                            </div>
                        </div>
                        <div class="row border-top border-secondary">
                            <div class="col-12">
                                <div class="form-group">
                                    <div class="p-t-20">
                                        <button class="btn btn-info" id="to-recover" type="button"><i class="fa fa-lock m-r-5"></i> Lost password?</button>
                                        <button class="btn btn-success float-right" type="submit">Login</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- Login box.scss -->
        
    
<?php $this->load->view('admin/login_comp/footer'); ?>

<script>
	$(function(){
		//history.go(1); // disable the browser's back button
		//var Backlen=history.length;   
		//history.go(-Backlen);   
		//window.location.href=page url
	});

function reload_capcha_img(){
	//alert("hi");
	var pm = '';
	$.ajax({
		method:'POST',
		url:'<?php echo base_url()."admin_access/get_new_capcha_set"; ?>',
		data:{pm: pm},
		dataType:'JSON',
		success:function(data){
			//alert(data.msg);
			if(data.msg == 1)
			{
				console.log(data);
				//alert(data.cap_set.word);
				//$('#plot_otherinfo').val('');
				//$('.otherplot_view').fadeOut(500);
				$('#capcha_pic').html(data.cap_set.image);
				
			}else{
				$('.captcha').html('Problem to Generate Captcha, Refresh the Page');
				//$('#plot_otherinfo').val('');
				$('.captcha').fadeOut(500);
			}
			
		}
	});
}
</script>
