<div id="topbar" class="topbar">
            <div class="header">



<?php if($this->session->userdata('utype') == 4){ ?>
 <div id="head-nav" class="navbar navbar-default navbar-fixed-top">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="fa fa-gear"></span>
                    </button>
                </div>
                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li class="active"><a href="home"><h5>Dashboard</h5></a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><h5>Applications<b class="caret"></b></h5></a>
                            <ul class="dropdown-menu">
                                <li class="dropdown-submenu"><a href="#">Swimming Pool</a>
                                    <ul class="dropdown-menu">
                                        <li><a href="swimmingallapplications">Pending Applications</a></li>
                                        <li><a href="swimmingapprovedapplications">Approved Applications</a></li>
                                        <li><a href="swimmingrejectedapplications">Rejected Applications</a></li>
                                    </ul>
                                </li>             
                                <li class="dropdown-submenu"><a href="#">Tennis</a>
                                    <ul class="dropdown-menu">
                                        <li><a href="tennisallapplications">Pending Applications</a></li>
                                        <li><a href="tennisapprovedapplications">Approved Applications</a></li>
                                        <li><a href="tennisrejectedapplications">Rejected Applications</a></li>
                                    </ul>
                                </li>  
                            </ul>
                        </li>
                        <li><a href="receivepayments"><h5>Payments</h5></a></li>
                         
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><h5>Report<b class="caret"></b></h5></a>
                            <ul class="dropdown-menu">
                                <li><a href="reports">Report All</a>
                                </li> 
                                <li class="dropdown-submenu"><a href="#">Shift Wise Report</a>
                                    <ul class="dropdown-menu">
                                        <c:forEach var="shiftBean" items="${shiftBeans}">
                                            <li><a href="reportbyshiftid?shiftId=${shiftBean.shiftId}">${shiftBean.shiftName}(${shiftBean.facilityName})</a></li>
                                            </c:forEach>
                                    </ul>
                                </li>             
                                <li class="dropdown-submenu"><a href="#">Season Wise Report</a>
                                    <ul class="dropdown-menu">
                                        <c:forEach var="seasonBean" items="${seasonBeans}">
                                            <li><a href="reportbyseasonid?seasonId=${seasonBean.seasonId}">From ${seasonBean.seasonStartDate} - To ${seasonBean.seasonEndDate}(${seasonBean.facilityName})</a></li>
                                            </c:forEach>
                                    </ul>
                                </li> 
                                <li><a href="reportsall">Reports All</a>
                                </li> 
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><h5>Application Form<b class="caret"></b></h5></a>
                            <ul class="dropdown-menu">
                                <li><a href="swimmingpoolmembershipform">Swimming Pool</a>
                                </li>             
                                <li><a href="tennismembershipform">Tennis</a>
                                </li>  
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><h5>Users<b class="caret"></b></h5></a>
                            <ul class="dropdown-menu">
                                <li><a href="createuser">Create New user</a>
                                </li>             
                                <li><a href="viewalladmins">Manage User</a>
                                </li>
                                
                            </ul>
                        </li>
                        
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><h5>Services<b class="caret"></b></h5></a>
                            <ul class="dropdown-menu">
                                
                                <li><a href="<?php echo base_url(); ?>admincontrol/panels/slot_creation">slot creation</a></li>
                                <li><a href="<?php echo base_url(); ?>admincontrol/panels/role_creation">role creation</a></li>
                                <li><a href="<?php echo base_url(); ?>admincontrol/panels/news_creation">Newsletter upload</a></li>
                                <li><a href="<?php echo base_url(); ?>admincontrol/panels/gallery_creation">Photo Gallery</a></li>
                            </ul>
                        </li>
                        <!--                <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><h5>Manage&nbsp;Data<b class="caret"></b></h5></a>
                                            <ul class="dropdown-menu">
                                                <li><a href="#">Season Details</a></li>
                                                <li><a href="#">Shift Details</a></li>
                                                <li><a href="#">Shift Timings</a></li>
                                            </ul>
                                        </li>-->
                    </ul>
                    <ul class="nav navbar-nav navbar-right user-nav">
                        <li class="dropdown profile_menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><h5>Welcome <?=$this->session->userdata('username')?><b class="caret"></b></h5></a>
                            <ul class="dropdown-menu">
                                <li><a href="#">Profile</a></li>
                                <li><a href="#" data-toggle="modal" data-target="#changePassword">Change Password</a></li>
                                <li class="divider"></li>
                                <li><a href="<?php echo base_url(); ?>admincontrol/dashboard/logout">Sign Out</a></li>
                            </ul>
                        </li>
                    </ul>			
                </div><!--/.nav-collapse -->
            </div>
        </div>

<?php }elseif($this->session->userdata('utype') == 2){?>

        <div id="head-nav" class="navbar navbar-default navbar-fixed-top">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="fa fa-gear"></span>
                    </button>
                </div>
                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li class="active"><a href="localadminhome"><h5>Dashboard</h5></a></li>
                            <?php if(1){
							//if(swimmingApprovalPermission == 'Y' || swimmingRejectPermission == 'Y' || swimmingUpdateInfoPermission == 'Y' || tennisApprovalPermission == 'Y' || tennisRejectPermission == 'Y' || tennisUpdateInfoPermission == 'Y'){ ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><h5>Applications<b class="caret"></b></h5></a>
                                <ul class="dropdown-menu">
                                    <?php if(1){
									//if(swimmingApprovalPermission == 'Y' || swimmingRejectPermission == 'Y' || swimmingUpdateInfoPermission == 'Y'){ ?>
                                        <li class="dropdown-submenu"><a href="#">Swimming Pool</a>
                                            <ul class="dropdown-menu">
                                                <li><a href="swimmingallapplications">Pending Applications</a></li>
                                                <li><a href="swimmingapprovedapplications">Approved Applications</a></li>
                                                <li><a href="swimmingrejectedapplications">Rejected Applications</a></li>
                                            </ul>
                                        </li>    
                                    <?php }
                                    if(1){
									//if(tennisApprovalPermission == 'Y' || tennisRejectPermission == 'Y' || tennisUpdateInfoPermission == 'Y'){ ?>
                                        <li class="dropdown-submenu"><a href="#">Tennis</a>
                                            <ul class="dropdown-menu">
                                                <li><a href="tennisallapplications">Pending Applications</a></li>
                                                <li><a href="tennisapprovedapplications">Approved Applications</a></li>
                                                <li><a href="tennisrejectedapplications">Rejected Applications</a></li>
                                            </ul>
                                        </li>  
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } ?>
                        <li><a href="receivepayments"><h5>Payments</h5></a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><h5>Report<b class="caret"></b></h5></a>
                            <ul class="dropdown-menu">
                                <li><a href="reports">Report All</a>
                                </li> 
                                <li class="dropdown-submenu"><a href="#">Shift Wise Report</a>
                                    <ul class="dropdown-menu">
                                        <c:forEach var="shiftBean" items="${shiftBeans}">
                                            <li><a href="reportbyshiftid?shiftId=${shiftBean.shiftId}">${shiftBean.shiftName}(${shiftBean.facilityName})</a></li>
                                            </c:forEach>
                                    </ul>
                                </li>             
                                <li class="dropdown-submenu"><a href="#">Season Wise Report</a>
                                    <ul class="dropdown-menu">
                                        <c:forEach var="seasonBean" items="${seasonBeans}">
                                            <li><a href="reportbyseasonid?seasonId=${seasonBean.seasonId}">From ${seasonBean.seasonStartDate} - To ${seasonBean.seasonEndDate}(${seasonBean.facilityName})</a></li>
                                            </c:forEach>
                                    </ul>
                                </li>  
                            </ul>
                        </li>
                        <?php if(1){
						//if(swimmingCreateMemberPermission == 'Y' || tennisCreateMemberPermission == 'Y'){ ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><h5>Application Form<b class="caret"></b></h5></a>
                                <ul class="dropdown-menu">
                                    <?php if(swimmingCreateMemberPermission == 'Y'){ ?>
                                        <li><a href="swimmingpoolmembershipform">Swimming Pool</a>
                                        </li>    
                                    <?php }
                                    if(tennisCreateMemberPermission == 'Y'){ ?>
                                        <li><a href="tennismembershipform">Tennis</a>
                                        </li>  
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } ?>
                    </ul>
                    <ul class="nav navbar-nav navbar-right user-nav">
                        <li class="dropdown profile_menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><h5>Welcome <?=$this->session->userdata('username')?><b class="caret"></b></h5></a>
                            <ul class="dropdown-menu">
                                <li><a href="#">Profile</a></li>
                                <li><a href="#" data-toggle="modal" data-target="#changePassword">Change Password</a></li>
                                <li class="divider"></li>
                                <li><a href="<?php echo base_url(); ?>admincontrol/dashboard/logout">Sign Out</a></li>
                            </ul>
                        </li>
                    </ul>			
                </div><!--/.nav-collapse -->
            </div>
        </div>
        
        
<?php } ?>
    
    

</div>
        </div>
        




<div class="modal fade" id="changePassword" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form style="margin-bottom: 0px !important;" class="form-horizontal" method="POST" action="#">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Change Password</h4>
                </div>
                <hr />
                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-xs-8" style="margin-left: 16%; margin-top: -3%;">
                            <p>Enter Current Password:</p>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                <input type="password" placeholder="Current Password" id="password" name="password" class="form-control" required>
                            </div>
                            <p>Enter New Password:</p>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                <input type="password" placeholder="New Password" id="newPassword" name="newPassword" class="form-control" required>
                            </div>
                            <p>Confirm New Password:</p>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                <input type="password" placeholder="Confirm New Password" data-parsley-equalto="#newPassword" id="confirmPassword" name="confirmPassword" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>