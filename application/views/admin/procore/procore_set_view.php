<?php $this->load->view('admin/component/header') ?>

<?php $this->load->view('admin/component/menu') ?>

<?php

$projectsList=json_decode($getProjects,true);
$ccList=json_decode($getCcs,true);
$uomList=json_decode($getUoms,true);
// $bliList=json_decode($getBlis,true);
$summaryList=json_decode($getBudgetSummary,true);
$suppliersList=json_decode($getSuppliers,true);
$taxGroupList=json_decode($getTaxGroups,true);
$procoreAuthDetails=json_decode($getProcoreAuth,true);

$client_id=$procoreAuthDetails['CLIENT_ID'];
$secret_key=$procoreAuthDetails['SECRET_KEY'];
$company_id=$procoreAuthDetails['COMPANY_ID'];
$accessToken="";

$curl = curl_init();

curl_setopt_array($curl, array(
	CURLOPT_URL => 'https://login.procore.com/oauth/token?client_id='.$client_id.'&client_secret='.$secret_key.'&grant_type=client_credentials',
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => '',
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 0,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => 'POST',
	CURLOPT_HTTPHEADER => array(
	  'Content-Type: application/json',
	  ': ',
	  'Cookie: AWSELB=83CFCB891680B364063F7F0C947C28DE1B1D31FDB8E07115BF9E0043272653CADB1E9A77E5663714E3444D46C69B24A6E28B7142E3F10DF651E49EA035CC127FCC4C909074; AWSELBCORS=83CFCB891680B364063F7F0C947C28DE1B1D31FDB8E07115BF9E0043272653CADB1E9A77E5663714E3444D46C69B24A6E28B7142E3F10DF651E49EA035CC127FCC4C909074'
	),
  ));

$response = curl_exec($curl);

curl_close($curl);
$responseData=json_decode($response,true);
if(isset($responseData['access_token'])){
$accessToken=$responseData['access_token'];
}
?>

<?php

$curl1 = curl_init();

curl_setopt_array($curl1, array(
  CURLOPT_URL => 'https://api.procore.com/rest/v1.0/projects?company_id='.$company_id.'',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'Authorization: Bearer '.$accessToken.'',
    'Cookie: AWSELB=C9678F371A99D572E6A6DEEE540CB386CBB813BDB128FC55E97BB004E7E76AD08316BF833957A1C35E9CA51FDC14011F96AF9CEA213614BBDC569F01E9493AF50411A5AD89; AWSELBCORS=C9678F371A99D572E6A6DEEE540CB386CBB813BDB128FC55E97BB004E7E76AD08316BF833957A1C35E9CA51FDC14011F96AF9CEA213614BBDC569F01E9493AF50411A5AD89'
  ),
));

$response1 = curl_exec($curl1);

curl_close($curl1);
$response1Data=json_decode($response1,true);
$projectsArray="";
foreach($response1Data as $proj){
	if($projectsArray==''){
		$projectsArray.=$proj['id'];
	}else{
		$projectsArray.=','.$proj['id'];
	}
}
?>
<?php

$curl2 = curl_init();

curl_setopt_array($curl2, array(
  CURLOPT_URL => 'https://api.procore.com/rest/v1.0/standard_cost_codes?company_id='.$company_id.'&standard_cost_code_list_id=562949953441378',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'Authorization: Bearer '.$accessToken.'',
    'Cookie: AWSELB=C9678F371A99D572E6A6DEEE540CB386CBB813BDB128FC55E97BB004E7E76AD08316BF833957A1C35E9CA51FDC14011F96AF9CEA213614BBDC569F01E9493AF50411A5AD89; AWSELBCORS=C9678F371A99D572E6A6DEEE540CB386CBB813BDB128FC55E97BB004E7E76AD08316BF833957A1C35E9CA51FDC14011F96AF9CEA213614BBDC569F01E9493AF50411A5AD89'
  ),
));

$response2 = curl_exec($curl2);

curl_close($curl2);
$response2Data=json_decode($response2,true);
?>
<?php

$curl3 = curl_init();

curl_setopt_array($curl3, array(
  CURLOPT_URL => 'https://api.procore.com/rest/v1.0/companies/'.$company_id.'/uoms',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'Authorization: Bearer '.$accessToken.'',
    'Cookie: AWSELB=C9678F371A99D572E6A6DEEE540CB386CBB813BDB128FC55E97BB004E7E76AD08316BF833945A69F2153C111E32E7B23CD43F7ADFEFD6421AAA944B4446E721A9B43FA179C; AWSELBCORS=C9678F371A99D572E6A6DEEE540CB386CBB813BDB128FC55E97BB004E7E76AD08316BF833945A69F2153C111E32E7B23CD43F7ADFEFD6421AAA944B4446E721A9B43FA179C'
  ),
));

$response3 = curl_exec($curl3);

curl_close($curl3);
$response3Data=json_decode($response3,true);
?>

<?php

$curl4 = curl_init();

curl_setopt_array($curl4, array(
  CURLOPT_URL => 'https://api.procore.com/rest/v1.0/vendors?company_id='.$company_id.'',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'Authorization: Bearer '.$accessToken.'',
    'Cookie: AWSELB=C9678F371A99D572E6A6DEEE540CB386CBB813BDB12225ED167FFC6539231467CB1079A16045A69F2153C111E32E7B23CD43F7ADFEFD6421AAA944B4446E721A9B43FA179C; AWSELBCORS=C9678F371A99D572E6A6DEEE540CB386CBB813BDB12225ED167FFC6539231467CB1079A16045A69F2153C111E32E7B23CD43F7ADFEFD6421AAA944B4446E721A9B43FA179C'
  ),
));

$response4 = curl_exec($curl4);

curl_close($curl4);
$response4Data=json_decode($response4,true);
?>

<?php
// $budgetLineItemsArray=array();
// foreach($response1Data as $projectData){
// $curl5 = curl_init();

// curl_setopt_array($curl5, array(
//   CURLOPT_URL => 'https://api.procore.com/rest/v1.0/budget_views/562949953449296/detail_rows?company_id=562949953441734&project_id='.$projectData['id'].'',
//   CURLOPT_RETURNTRANSFER => true,
//   CURLOPT_ENCODING => '',
//   CURLOPT_MAXREDIRS => 10,
//   CURLOPT_TIMEOUT => 0,
//   CURLOPT_FOLLOWLOCATION => true,
//   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//   CURLOPT_CUSTOMREQUEST => 'GET',
//   CURLOPT_HTTPHEADER => array(
//     'Content-Type: application/json',
//     'Authorization: Bearer '.$accessToken.'',
//     'Cookie: AWSELB=C9678F371A99D572E6A6DEEE540CB386CBB813BDB12225ED167FFC6539231467CB1079A16045A69F2153C111E32E7B23CD43F7ADFEFD6421AAA944B4446E721A9B43FA179C; AWSELBCORS=C9678F371A99D572E6A6DEEE540CB386CBB813BDB12225ED167FFC6539231467CB1079A16045A69F2153C111E32E7B23CD43F7ADFEFD6421AAA944B4446E721A9B43FA179C'
//   ),
// ));

// $response5 = curl_exec($curl5);

// curl_close($curl5);
// $response5Data=json_decode($response5,true);
// array_push($budgetLineItemsArray,$response5Data);
// }
// $c=0;
// foreach($budgetLineItemsArray as $b){
// foreach($b[$c] as $b1){
// 	echo $b1['cost_code_level_2'].'<br>';
// }$c++;}
?>
<?php

$curl6 = curl_init();

curl_setopt_array($curl6, array(
  CURLOPT_URL => 'https://api.procore.com/rest/v1.0/tax_codes?company_id='.$company_id.'',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer '.$accessToken.'',
    'Cookie: AWSELB=C9678F371A99D572E6A6DEEE540CB386CBB813BDB12225ED167FFC6539231467CB1079A16057A1C35E9CA51FDC14011F96AF9CEA213614BBDC569F01E9493AF50411A5AD89; AWSELBCORS=C9678F371A99D572E6A6DEEE540CB386CBB813BDB12225ED167FFC6539231467CB1079A16057A1C35E9CA51FDC14011F96AF9CEA213614BBDC569F01E9493AF50411A5AD89'
  ),
));

$response6 = curl_exec($curl6);

curl_close($curl6);
$response6Data=json_decode($response6,true);
?>

<?php

$budgetSummaryArray=array();
$projectIdsArray=[];
foreach($projectsList as $pl){
	array_push($projectIdsArray,$pl['procore_project_id']);
}
foreach($response1Data as $projectData){
	if(in_array($projectData['id'],$projectIdsArray)){
	$curl7 = curl_init();

curl_setopt_array($curl7, array(
  CURLOPT_URL => 'https://api.procore.com/rest/v1.0/budget_views/562949953449296/summary_rows?company_id='.$company_id.'&project_id='.$projectData['id'].'',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer '.$accessToken.'',
    'Cookie: AWSALB=+6mz1ExHb+GachZgGG4hUPKN07Nc04Bt7V/0N8useGy3QS4PU3kVk+/dVlIpxtrrbeM7LfSy4hoUGxPwFH5kHtblz5t5qtty2cCQCM7LpRxUFkq7oIQYDOzwkfrg; AWSALBCORS=+6mz1ExHb+GachZgGG4hUPKN07Nc04Bt7V/0N8useGy3QS4PU3kVk+/dVlIpxtrrbeM7LfSy4hoUGxPwFH5kHtblz5t5qtty2cCQCM7LpRxUFkq7oIQYDOzwkfrg; AWSALBTG=xUQdvlcm704QxUdCR0yyYnD21oZCmsM0XaVswBRFpDDWY07KTqoiD6yMVf4lMQv7yTe911OstEGPODC4bHi6Qzs0g8xXo51mZAIMocm0buxHFkPaPNfAAu6olt6zYXhJnIg2sBlE5PV1Go9RznMt3pGzyHu8nIjL9eKjjRe9SwbwWQDWJUA=; AWSALBTGCORS=xUQdvlcm704QxUdCR0yyYnD21oZCmsM0XaVswBRFpDDWY07KTqoiD6yMVf4lMQv7yTe911OstEGPODC4bHi6Qzs0g8xXo51mZAIMocm0buxHFkPaPNfAAu6olt6zYXhJnIg2sBlE5PV1Go9RznMt3pGzyHu8nIjL9eKjjRe9SwbwWQDWJUA='
  ),
));

$response7 = curl_exec($curl7);

curl_close($curl7);
$response7Data=json_decode($response7,true);
array_push($budgetSummaryArray,$response7Data);
}}
?>

<?php
$projectsArray=[];
$ccArray=[];
$uomArray=[];
// $bliArray=[];
$summaryArray=[];
$suppliersArray=[];
$taxGroupArray=[];
foreach($projectsList as $p){
	array_push($projectsArray,$p['proj_number']);
}
foreach($ccList as $c){
	array_push($ccArray,$c['cc_no']);
}
foreach($uomList as $u){
	array_push($uomArray,$u['uom_name']);
}
// foreach($bliList as $b){
// 	array_push($bliArray,$b['procore_budget_id']);
// }
foreach($summaryList as $bs){
	array_push($summaryArray,$bs['project_id']);
}
foreach($suppliersList as $s){
	array_push($suppliersArray,$s['sup_name']);
}
foreach($taxGroupList as $t){
	array_push($taxGroupArray,$t['name']);
}

$rtePoData=json_decode($getRtePo,true);
$failedPoData=json_decode($getFailedPo,true);
$syncedPoData=json_decode($getSyncedPo,true);
?>
<style>
	.box-body textarea, input, select {
		max-width: 500px;
	}

	.box-body textarea {
		resize: vertical;
	}

	.filter-active{
		font-weight:bold;
		text-decoration:underline;
	}
</style>

<!-- Page wrapper  -->
<!-- ============================================================== -->
<div class="page-wrapper">
	<!-- ============================================================== -->
	<!-- Bread crumb and right sidebar toggle -->
	<!-- ============================================================== -->
	<div class="page-breadcrumb">
		<div class="row">
			<div class="col-12 d-flex no-block align-items-center">
				<h4 class="page-title">Procore Details</h4>
				<div class="ml-auto text-right">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a
										href="<?php echo base_url('admincontrol/dashboard'); ?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Procore Details</li>
						</ol>
					</nav>
				</div>
			</div>
		</div>
	</div>
	<!-- ============================================================== -->
	<!-- End Bread crumb and right sidebar toggle -->
	<!-- ============================================================== -->
	<!-- ============================================================== -->
	<!-- Container fluid  -->
	<!-- ============================================================== -->
	<div class="container-fluid">
		<!-- ============================================================== -->
		<!-- Start Page Content -->
		<!-- ============================================================== -->
		<?php if(empty($procoreAuthDetails)){?>
		<div class="row">
			<div class="col-12" style="padding:0px">
				<div class="card">
					<div class="card-body">
						<form action="<?=base_url('admincontrol/Procore/insertProcoreAuth')?>" method="post" enctype="multipart/form-data">
						<div class="form-row">
							<div class="col-4">
								<input type="text" name="client_id" id="client_id" class="form-control" placeholder="Client Id">
								<div id="client_id_err" style="padding:0px;color:red"></div>
							</div>
							<div class="col-4">
								<input type="text" name="client_secret" id="client_secret" class="form-control" placeholder="Client Secret">
								<div id="client_secret_err" style="padding:0px;color:red"></div>
							</div>
							<div class="col-2">
								<input type="number" name="company_id" id="company_id" class="form-control" placeholder="Company Id">
								<div id="company_id_err" style="padding:0px;color:red"></div>
							</div>
							<div class="col-2">
								<button type="submit" id="insert_procore_auth_btn" class="btn btn-primary" style="width:100%">Update</button>
							</div>
						</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<?php }?>

		<div class="row" id="update_procore_auth_row" style="display:none">
			<div class="col-12" style="padding:0px">
				<div class="card">
					<div class="card-body">
						<form action="<?=base_url('admincontrol/Procore/updateProcoreAuth')?>" method="post" enctype="multipart/form-data">
						<div class="form-row">
							<div class="col-4">
								<input type="text" name="u_client_id" id="u_client_id" class="form-control" placeholder="Client Id">
								<div id="u_client_id_err" style="padding:0px;color:red"></div>
							</div>
							<div class="col-4">
								<input type="text" name="u_client_secret" id="u_client_secret" class="form-control" placeholder="Client Secret">
								<div id="u_client_secret_err" style="padding:0px;color:red"></div>
							</div>
							<div class="col-2">
								<input type="number" name="u_company_id" id="u_company_id" class="form-control" placeholder="Company Id">
								<div id="u_company_id_err" style="padding:0px;color:red"></div>
							</div>
							<div class="col-1">
								<button type="submit" id="update_procore_auth_btn" class="btn btn-primary" style="width:100%">Update</button>
							</div>
							<div class="col-1">
								<button type="button" id="close_procore_update_auth_btn" class="btn btn-danger" style="width:100%">close</button>
							</div>
						</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<div class="row">

			<input type="hidden" id="hidden-commitment-status" value="<?php echo $this->session->flashdata('commitment_err');?>">
			<div class="alert alert-danger col-md-12" id="commitment-error-div" style="display:none;text-align:center;font-size:16px">
				<strong>Alert!</strong> Sync Failed. check failed to export section for details.
			</div>
			<div class="col-md-12" style="padding:0px">

				<div class="card" style="padding:0px">
					<div class="card-body" style="padding:0px">
						<div class="row" style="padding:0px">
							<div class="col-md-10">

								<nav>
									<div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
										<a class="nav-item nav-link active" id="nav-projects-tab" data-toggle="tab" href="#nav-projects"
										role="tab" aria-controls="nav-projects" aria-selected="true">Projects</a>
										<a class="nav-item nav-link" id="nav-cost-code-tab" data-toggle="tab" href="#nav-cost-code"
										role="tab" aria-controls="nav-cost-code" aria-selected="false">Cost Codes</a>
										<a class="nav-item nav-link" id="nav-uom-tab" data-toggle="tab" href="#nav-uom"
										role="tab" aria-controls="nav-uom" aria-selected="false">Units Of Measure</a>
										<a class="nav-item nav-link" id="nav-suppliers-tab" data-toggle="tab" href="#nav-suppliers"
										role="tab" aria-controls="nav-suppliers" aria-selected="false">Suppliers</a>
										<a class="nav-item nav-link" id="nav-bli-tab" data-toggle="tab" href="#nav-bli"
										role="tab" aria-controls="nav-bli" aria-selected="false">Budget</a>
										<a class="nav-item nav-link" id="nav-commitments-tab" data-toggle="tab" href="#nav-commitments"
										role="tab" aria-controls="nav-commitments" aria-selected="false">Commitments</a>
										<a class="nav-item nav-link" id="nav-taxgroup-tab" data-toggle="tab" href="#nav-taxgroup"
										role="tab" aria-controls="nav-taxgroup" aria-selected="false">Tax Group</a>
									</div>
								</nav>
								<div class="tab-content" id="nav-tabContent" style="padding:10px">
									<div class="tab-pane fade show active" id="nav-projects" role="tabpanel"
										aria-labelledby="nav-projects-tab">
										<div id="projects-available" class="table-responsive">
											<table id="zero_config" class="table table-striped table-bordered">
												<thead>
												<tr style="font-weight: bold;">
													<th>Sl No.</th>
													<th>Project No.</th>
													<th>Name</th>
													<th>Address</th>
													<?php 
													if($this->session->userdata('utype')==1 || $templateDetails->pt_a_procore<3){?>
														<th>Action</th>
						                        	<?php }?>
												</tr>
												</thead>
												<tbody>
													<?php
														$count=1;
														if(!empty($response1Data)){
															if(!isset($response1Data['error'])){
														foreach($response1Data as $projects){
															if(in_array($projects['project_number'],$projectsArray)){}else{
													?>
													<tr>
														<td><?php echo $count;?></td>
														<td><?php echo $projects['project_number'];?></td>
														<td><?php echo $projects['name'];?></td>
														<td><?php if($projects['address']==''){echo '-';}else{echo $projects['address'].', '.$projects['city'],', '.$projects['state_code'],', '.$projects['country_code'].'( '.$projects['zip'].' )';}?></td>
														<?php 
														if($this->session->userdata('utype')==1 || $templateDetails->pt_a_procore<3){?>
														<td>
															<form action="<?=base_url('admincontrol/procore/syncProjects')?>" method="post">
																<input type="hidden" name="h-company-id" value="<?php echo $company_id;?>">
																<input type="hidden" name="h-access-token" value="<?php echo $accessToken;?>">
																<input type="hidden" name="h-project-id" value="<?php echo $projects['id'];?>">
																<input type="hidden" name="h-project-number" value="<?php echo $projects['project_number'];?>">
																<input type="hidden" name="h-project-name" value="<?php echo $projects['name'];?>">
																<input type="hidden" name="h-project-address" value="<?php if($projects['address']==''){echo '-';}else{echo $projects['address'].', '.$projects['city'],', '.$projects['state_code'],', '.$projects['country_code'].'( '.$projects['zip'].' )';}?>">
																<button type="submit" class="btn btn-sm btn-danger">Sync</button>
															</form>
														</td>
						                        		<?php }?>
													</tr>
													<?php $count++;}}}}?>
												</tbody>
											</table>
										</div>
										<div id="projects-synced" class="table-responsive" style="display:none">
											<table id="zero_config" class="table table-striped table-bordered">
												<thead>
												<tr style="font-weight: bold;">
													<th>Sl No.</th>
													<th>Project No.</th>
													<th>Name</th>
													<th>Address</th>
													<?php 
													if($this->session->userdata('utype')==1 || $templateDetails->pt_a_procore<3){?>
														<th>Action</th>
													<?php }?>
												</tr>
												</thead>
												<tbody>
													<?php
														$scount=1;
														if(!empty($response1Data)){
															if(!isset($response1Data['error'])){
														foreach($response1Data as $sprojects){
															if(in_array($sprojects['project_number'],$projectsArray)){
													?>
													<tr>
														<td><?php echo $scount;?></td>
														<td><?php echo $sprojects['project_number'];?></td>
														<td><?php echo $sprojects['name'];?></td>
														<td><?php if($sprojects['address']==''){echo '-';}else{echo $sprojects['address'].', '.$sprojects['city'],', '.$sprojects['state_code'],', '.$sprojects['country_code'].'( '.$sprojects['zip'].' )';}?></td>
														<?php 
														if($this->session->userdata('utype')==1 || $templateDetails->pt_a_procore<3){?>
															<td>
																<form action="<?=base_url('admincontrol/procore/reSyncProjects')?>" method="post">
																	<input type="hidden" name="h-company-id" value="<?php echo $company_id;?>">
																	<input type="hidden" name="h-access-token" value="<?php echo $accessToken;?>">
																	<input type="hidden" name="h-project-id" value="<?php echo $sprojects['id'];?>">
																	<input type="hidden" name="h-project-number" value="<?php echo $sprojects['project_number'];?>">
																	<input type="hidden" name="h-project-name" value="<?php echo $sprojects['name'];?>">
																	<input type="hidden" name="h-project-address" value="<?php if($sprojects['address']==''){echo '-';}else{echo $sprojects['address'].', '.$sprojects['city'],', '.$sprojects['state_code'],', '.$sprojects['country_code'].'( '.$sprojects['zip'].' )';}?>">
																	<button type="submit" class="btn btn-sm btn-danger">Re-Sync</button>
																</form>
															</td>
														<?php }?>
													</tr>
													<?php $scount++;}else{?>
													<?php }}}}?>
												</tbody>
											</table>
										</div>
									</div>
									<div class="tab-pane fade" id="nav-cost-code" role="tabpanel"
										aria-labelledby="nav-cost-code-tab">
										<div id="cost-code-available" class="table-responsive">
											<?php 
											if($this->session->userdata('utype')==1 || $templateDetails->pt_a_procore<3){?>
											<?php 	
												$showHideSyncAll=0;
												if(!empty($response2Data)){
													if(!isset($response2Data['error'])){
														foreach($response2Data as $cc){
															if(in_array($cc['full_code'],$ccArray) || strlen($cc['full_code'])<7){}else{
																$showHideSyncAll++;
															}}}}
											?>
											<?php if($showHideSyncAll>0){?>
												<div class="col-xs-12" style="padding:10px;text-align:right">
													<form action="<?=base_url('admincontrol/procore/syncAllCostCode')?>" method="post">
														<input type="hidden" name="h-company-id" value="<?php echo $company_id;?>">
														<input type="hidden" name="h-access-token" value="<?php echo $accessToken;?>">
														<button type="submit" class="btn btn-sm btn-danger">Sync All</button>
													</form>
												</div>
											<?php }?>
											<?php }?>
											<table id="zero_config_1" class="table table-striped table-bordered">
												<thead>
													<tr style="font-weight: bold;">
														<th>Sl No.</th>
														<th>Code</th>
														<th>Description</th>
														<?php 
														if($this->session->userdata('utype')==1 || $templateDetails->pt_a_procore<3){?>
															<th>Action</th>
														<?php }?>
													</tr>
												</thead>
												<tbody>
													<?php
														$count1=1;
														if(!empty($response2Data)){
															if(!isset($response2Data['error'])){
														foreach($response2Data as $cc){
															if(in_array($cc['full_code'],$ccArray) || strlen($cc['full_code'])<7){}else{
													?>
													<tr>
														<td><?php echo $count1;?></td>
														<td><?php echo $cc['full_code'];?></td>
														<td><?php echo $cc['name'];?></td>
														<?php 
														if($this->session->userdata('utype')==1 || $templateDetails->pt_a_procore<3){?>
														<td>
															<form action="<?=base_url('admincontrol/procore/syncCostCode')?>" method="post">
																<input type="hidden" name="h-cost-code-id" value="<?php echo $cc['id'];?>">
																<input type="hidden" name="h-cost-code" value="<?php echo $cc['full_code'];?>">
																<input type="hidden" name="h-name" value="<?php echo $cc['name'];?>">
																<button type="submit" class="btn btn-sm btn-danger">Sync</button>
															</form>
														</td>
														<?php }?>
													</tr>
													<?php $count1++;}}}}?>
												</tbody>
											</table>
										</div>
										<div id="cost-code-synced" class="table-responsive" style="display:none">
											<table id="zero_config_1" class="table table-striped table-bordered">
												<thead>
													<tr style="font-weight: bold;">
														<th>Sl No.</th>
														<th>Code</th>
														<th>Description</th>
														<?php 
														if($this->session->userdata('utype')==1 || $templateDetails->pt_a_procore<3){?>
															<th>Action</th>
														<?php }?>
													</tr>
												</thead>
												<tbody>
													<?php
														$scount1=1;
														if(!empty($response2Data)){
															if(!isset($response2Data['error'])){
														foreach($response2Data as $cc){
															if(in_array($cc['full_code'],$ccArray)){
													?>
													<tr>
														<td><?php echo $scount1;?></td>
														<td><?php echo $cc['full_code'];?></td>
														<td><?php echo $cc['name'];?></td>
														<?php 
														if($this->session->userdata('utype')==1 || $templateDetails->pt_a_procore<3){?> 
															<td>
																<form action="<?=base_url('admincontrol/procore/syncCostCode')?>" method="post">
																	<input type="hidden" name="h-cost-code-id" value="<?php echo $cc['id'];?>">
																	<input type="hidden" name="h-cost-code" value="<?php echo $cc['full_code'];?>">
																	<input type="hidden" name="h-name" value="<?php echo $cc['name'];?>">
																	<button type="submit" class="btn btn-sm btn-danger">Re-Sync</button>
																</form>
															</td>
														<?php }?>
													</tr>
													<?php $scount1++;}else{?>
													<?php }}}}?>
												</tbody>
											</table>
										</div>
									</div>
									<div class="tab-pane fade" id="nav-uom" role="tabpanel"
										aria-labelledby="nav-uom-tab">
										<div id="uom-available" class="table-responsive"><?php 
											if($this->session->userdata('utype')==1 || $templateDetails->pt_a_procore<3){?>
											<?php 	
												$showHideSyncAll=0;
												if(!empty($response3Data)){
													if(!isset($response3Data['error'])){
														foreach($response3Data as $uom){
															if(in_array($uom['name'],$uomArray)){}else{
																$showHideSyncAll++;
															}}}}
											?>
											<?php if($showHideSyncAll>0){?>
												<div class="col-xs-12" style="padding:10px;text-align:right">
													<form action="<?=base_url('admincontrol/procore/syncAllUom')?>" method="post">
														<input type="hidden" name="h-company-id" value="<?php echo $company_id;?>">
														<input type="hidden" name="h-access-token" value="<?php echo $accessToken;?>">
														<button type="submit" class="btn btn-sm btn-danger">Sync All</button>
													</form>
												</div>
											<?php }?>
											<?php }?>
											<table id="zero_config" class="table table-striped table-bordered">
												<thead>
													<tr style="font-weight: bold;">
														<th>Sl No.</th>
														<th>Name</th>
														<?php 
														if($this->session->userdata('utype')==1 || $templateDetails->pt_a_procore<3){?>
															<th>Action</th>
														<?php }?>
													</tr>
												</thead>
												<tbody>
													<?php
														$count2=1;
														if(!empty($response3Data)){
															if(!isset($response3Data['error'])){
														foreach($response3Data as $uom){
															if(in_array($uom['name'],$uomArray)){}else{
													?>
													<tr>
														<td><?php echo $count2;?></td>
														<td><?php echo $uom['name'];?></td>
														<?php 
														if($this->session->userdata('utype')==1 || $templateDetails->pt_a_procore<3){?>
														<td>
															<form action="<?=base_url('admincontrol/procore/syncUom')?>" method="post">
																<input type="hidden" name="h-name" value="<?php echo $uom['name'];?>">
																<button type="submit" class="btn btn-sm btn-danger">Sync</button>
															</form>
														</td>
														<?php }?>
													</tr>
													<?php $count2++;}}}}?>
												</tbody>
											</table>
										</div>
										<div id="uom-synced" class="table-responsive" style="display:none">
											<table id="zero_config" class="table table-striped table-bordered">
												<thead>
													<tr style="font-weight: bold;">
														<th>Sl No.</th>
														<th>Name</th>
													</tr>
												</thead>
												<tbody>
													<?php
														$scount2=1;
														if(!empty($response3Data)){
															if(!isset($response3Data['error'])){
														foreach($response3Data as $uom){
															if(in_array($uom['name'],$uomArray)){
																
													?>
													<tr>
														<td><?php echo $scount2;?></td>
														<td><?php echo $uom['name'];?></td>
													</tr>
													<?php $scount2++;}else{?>
													<?php }}}}?>
												</tbody>
											</table>
										</div>
									</div>
									<div class="tab-pane fade" id="nav-suppliers" role="tabpanel"
										aria-labelledby="nav-suppliers-tab">
										<div id="suppliers-available" class="table-responsive">
											<table id="zero_config_2" class="table table-striped table-bordered">
												<thead>
													<tr style="font-weight: bold;">
														<th>Sl No.</th>
														<th>Name</th>
														<th>Contact Person</th>
														<th>Mobile</th>
														<th>Email</th>
														<th>Address</th>
														<?php 
														if($this->session->userdata('utype')==1 || $templateDetails->pt_a_procore<3){?>
															<th>Action</th>
														<?php }?>
													</tr>
												</thead>
												<tbody>
													<?php
														$count3=1;
														if(!empty($response4Data)){
															if(!isset($response4Data['error'])){
														foreach($response4Data as $suppliers){
															if(in_array($suppliers['name'],$suppliersArray) || $suppliers['is_active']!='true'){}else{
													?>
													<tr>
														<td><?php echo $count3;?></td>
														<td><?php echo $suppliers['name'];?></td>
														<td><?php if($suppliers['primary_contact']!=null){echo $suppliers['primary_contact']['first_name'].' '.$suppliers['primary_contact']['last_name'];}else{echo '-';}?></td>
														<td><?php echo $suppliers['business_phone'];?></td>
														<td><?php if($suppliers['primary_contact']!=null){echo $suppliers['primary_contact']['email_address'];}else{echo '-';}?></td>
														<td><?php echo $suppliers['address'].', '.$suppliers['city'].', '.$suppliers['zip'];?></td>
														<?php 
														if($this->session->userdata('utype')==1 || $templateDetails->pt_a_procore<3){?>
															<td>
																<form action="<?=base_url('admincontrol/procore/syncSuppliers')?>" method="post">
																	<input type="hidden" name="h-company-id" value="<?php echo $company_id;?>">
																	<input type="hidden" name="h-access-token" value="<?php echo $accessToken;?>">
																	<input type="hidden" name="h-id" value="<?php echo $suppliers['id'];?>">
																	<input type="hidden" name="h-name" value="<?php echo $suppliers['name'];?>">
																	<input type="hidden" name="h-primary-contact" value="<?php if($suppliers['primary_contact']!=null){echo $suppliers['primary_contact']['first_name'].' '.$suppliers['primary_contact']['last_name'];}else{echo '-';}?>">
																	<input type="hidden" name="h-mobile" value="<?php echo $suppliers['business_phone'];?>">
																	<input type="hidden" name="h-email" value="<?php if($suppliers['primary_contact']!=null){echo $suppliers['primary_contact']['email_address'];}else{echo '-';}?>">
																	<input type="hidden" name="h-address" value="<?php echo $suppliers['address'].', '.$suppliers['city'].', '.$suppliers['zip'];?>">
																	<button type="submit" class="btn btn-sm btn-danger">Sync</button>
																</form>
															</td>
														<?php }?>
													</tr>
													<?php $count3++;}}}}?>
												</tbody>
											</table>
										</div>
										<div id="suppliers-synced" class="table-responsive" style="display:none">
											<table id="zero_config_4" class="table table-striped table-bordered">
												<thead>
													<tr style="font-weight: bold;">
														<th>Sl No.</th>
														<th>Name</th>
														<th>Contact Person</th>
														<th>Mobile</th>
														<th>Email</th>
														<th>Address</th>
														<?php 
														if($this->session->userdata('utype')==1 || $templateDetails->pt_a_procore<3){?>
															<th>Action</th>
														<?php }?>
													</tr>
												</thead>
												<tbody>
													<?php
														$scount3=1;
														if(!empty($response4Data)){
															if(!isset($response4Data['error'])){
														foreach($response4Data as $suppliers){
															if(in_array($suppliers['name'],$suppliersArray) && $suppliers['is_active']=='true'){
													?>
													<tr>
														<td><?php echo $scount3;?></td>
														<td><?php echo $suppliers['name'];?></td>
														<td><?php if($suppliers['primary_contact']!=null){echo $suppliers['primary_contact']['first_name'].' '.$suppliers['primary_contact']['last_name'];}else{echo '-';}?></td>
														<td><?php echo $suppliers['business_phone'];?></td>
														<td><?php if($suppliers['primary_contact']!=null){echo $suppliers['primary_contact']['email_address'];}else{echo '-';}?></td>
														<td><?php echo $suppliers['address'].', '.$suppliers['city'].', '.$suppliers['zip'];?></td>
														<?php 
														if($this->session->userdata('utype')==1 || $templateDetails->pt_a_procore<3){?>
															<td>
																<form action="<?=base_url('admincontrol/procore/syncSuppliers')?>" method="post">
																	<input type="hidden" name="h-company-id" value="<?php echo $company_id;?>">
																	<input type="hidden" name="h-access-token" value="<?php echo $accessToken;?>">
																	<input type="hidden" name="h-id" value="<?php echo $suppliers['id'];?>">
																	<input type="hidden" name="h-name" value="<?php echo $suppliers['name'];?>">
																	<input type="hidden" name="h-primary-contact" value="<?php if($suppliers['primary_contact']!=null){echo $suppliers['primary_contact']['first_name'].' '.$suppliers['primary_contact']['last_name'];}else{echo '-';}?>">
																	<input type="hidden" name="h-mobile" value="<?php echo $suppliers['business_phone'];?>">
																	<input type="hidden" name="h-email" value="<?php if($suppliers['primary_contact']!=null){echo $suppliers['primary_contact']['email_address'];}else{echo '-';}?>">
																	<input type="hidden" name="h-address" value="<?php echo $suppliers['address'].', '.$suppliers['city'].', '.$suppliers['zip'];?>">
																	<button type="submit" class="btn btn-sm btn-danger">Re-Sync</button>
																</form>
															</td>
														<?php }?>
													</tr>
													<?php $scount3++;}else{?>
													<?php }}}}?>
												</tbody>
											</table>
										</div>
									</div>
									<div class="tab-pane fade" id="nav-taxgroup" role="tabpanel"
										aria-labelledby="nav-taxgroup-tab">
										<div id="taxgroup-available" class="table-responsive">
											<table id="zero_config_3" class="table table-striped table-bordered">
												<thead>
													<tr style="font-weight: bold;">
														<th>Sl No.</th>
														<th>Name</th>
														<th>Description</th>
														<th>Percentage</th>
														<?php 
														if($this->session->userdata('utype')==1 || $templateDetails->pt_a_procore<3){?>
															<th>Action</th>
														<?php }?>
													</tr>
												</thead>
												<tbody>
													<?php
														$count6=1;
														if(!empty($response6Data)){
															if(!isset($response6Data['error'])){
														foreach($response6Data as $taxgroup){
															if(in_array($taxgroup['code'],$taxGroupArray) || $taxgroup['archived']=='true'){}else{
													?>
													<tr>
														<td><?php echo $count6;?></td>
														<td><?php echo $taxgroup['code'];?></td>
														<td><?php echo $taxgroup['description'];?></td>
														<td><?php echo $taxgroup['rate1'];?></td>
														<?php 
														if($this->session->userdata('utype')==1 || $templateDetails->pt_a_procore<3){?>
														<td>
															<form action="<?=base_url('admincontrol/procore/syncTaxGroups')?>" method="post">
																<input type="hidden" name="h-id" value="<?php echo $taxgroup['id'];?>">
																<input type="hidden" name="h-name" value="<?php echo $taxgroup['code'];?>">
																<input type="hidden" name="h-description" value="<?php echo $taxgroup['description'];?>">
																<input type="hidden" name="h-rate" value="<?php echo $taxgroup['rate1'];?>">
																<button type="submit" class="btn btn-sm btn-danger">Sync</button>
															</form>
														</td>
														<?php }?>
													</tr>
													<?php $count6++;}}}}?>
												</tbody>
											</table>
										</div>
										<div id="taxgroup-synced" class="table-responsive" style="display:none">
											<table id="zero_config_3" class="table table-striped table-bordered">
												<thead>
													<tr style="font-weight: bold;">
														<th>Sl No.</th>
														<th>Name</th>
														<th>Description</th>
														<th>Percentage</th>
													</tr>
												</thead>
												<tbody>
													<?php
														$scount6=1;
														if(!empty($response6Data)){
															if(!isset($response6Data['error'])){
														foreach($response6Data as $taxgroup){
															if(in_array($taxgroup['code'],$taxGroupArray) && $taxgroup['archived']!='true'){
													?>
													<tr>
														<td><?php echo $scount6;?></td>
														<td><?php echo $taxgroup['code'];?></td>
														<td><?php echo $taxgroup['description'];?></td>
														<td><?php echo $taxgroup['rate1'];?></td>
													</tr>
													<?php $scount6++;}else{?>
													<?php }}}}?>
												</tbody>
											</table>
										</div>
									</div>
									<div class="tab-pane fade" id="nav-bli" role="tabpanel"
										aria-labelledby="nav-bli-tab">
										<div id="bli-available" class="table-responsive">
											<table id="zero_config" class="table table-striped table-bordered">
												<thead>
													<tr style="font-weight: bold;">
														<th>Sl No.</th>
														<th>Project</th>
														<th>Original Budget</th>
														<th>Revised Budget</th>
														<th>Committed Costs</th>
														<?php 
														if($this->session->userdata('utype')==1 || $templateDetails->pt_a_procore<3){?>
															<th>Action</th>
														<?php }?>
													</tr>
												</thead>
												<tbody>
													<?php
														$count4=1;
														$pcount=0;
														$formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
														if(!empty($budgetSummaryArray)){
															if(!isset($budgetSummaryArray['error'])){
														foreach($budgetSummaryArray as $bsa){

														foreach($budgetSummaryArray[$pcount] as $summary){
															if(isset($summary['id'])){
															if(in_array($summary['id'],$summaryArray)){}else{
															?>
															<tr>
																<td><?php echo $count4;?></td>
																<td><?php echo $summary['name'];?></td>
																<td>
																	<?php 
																		echo $formatter->formatCurrency($summary['original_budget_amount'], 'USD');
																	?>
																</td>
																<td><?php echo $formatter->formatCurrency($summary['Revised Budget'], 'USD');?></td>
																<td><?php echo $formatter->formatCurrency($summary['Committed Costs'], 'USD');?></td>
																<?php 
																if($this->session->userdata('utype')==1 || $templateDetails->pt_a_procore<3){?>
																	<td>
																		<form action="<?=base_url('admincontrol/procore/syncBli')?>" method="post">
																			<input type="hidden" name="h-access-token" value="<?php echo $accessToken?>">
																			<input type="hidden" name="h-company-id" value="<?php echo $company_id?>">
																			<input type="hidden" name="h-project-id" value="<?php echo $summary['id'];?>">
																			<input type="hidden" name="h-original-budget" value="<?php echo $summary['original_budget_amount'];?>">
																			<input type="hidden" name="h-revised-budget" value="<?php echo $summary['Revised Budget'];?>">
																			<input type="hidden" name="h-committed-cost" value="<?php echo $summary['Committed Costs'];?>">
																			<button type="submit" class="btn btn-sm btn-danger">Sync</button>
																		</form>
																</td>
																<?php }?>
															</tr>
															<?php $count4++;}}}
															$pcount++;
														}}}?>
												</tbody>
											</table>
										</div>
										<div id="bli-synced" class="table-responsive" style="display:none">
											<table id="zero_config" class="table table-striped table-bordered">
												<thead>
													<tr style="font-weight: bold;">
														<th>Sl No.</th>
														<th>Project</th>
														<!-- <th>Division</th>
														<th>Cost Code</th> -->
														<th>Original Budget</th>
														<th>Revised Budget</th>
														<th>Committed Costs</th>
														<?php 
														if($this->session->userdata('utype')==1 || $templateDetails->pt_a_procore<3){?>
															<th>Action</th>
														<?php }?>
													</tr>
												</thead>
												<tbody>
													<?php
														$scount4=1;
														$pcount1=0;
														if(!empty($budgetSummaryArray)){
															if(!isset($budgetSummaryArray['error'])){
														foreach($budgetSummaryArray as $bsa){?>
															<?php 
															foreach($budgetSummaryArray[$pcount1] as $bli){
															if(isset($bli['id'])){
																if(in_array($bli['id'],$summaryArray)){
														?>
														<tr>
															<td><?php echo $scount4;?></td>
															<td><?php echo $bli['name'];?></td>
															<td><?php echo $formatter->formatCurrency($bli['original_budget_amount'], 'USD');?></td>
															<td><?php echo $formatter->formatCurrency($bli['Revised Budget'], 'USD');?></td>
															<td><?php echo $formatter->formatCurrency($bli['Committed Costs'], 'USD');?></td> 
															<?php 
															if($this->session->userdata('utype')==1 || $templateDetails->pt_a_procore<3){?>
															<td>
																<form action="<?=base_url('admincontrol/procore/syncBli')?>" method="post">
																	<input type="hidden" name="h-access-token" value="<?php echo $accessToken?>">
																	<input type="hidden" name="h-company-id" value="<?php echo $company_id?>">
																	<input type="hidden" name="h-project-id" value="<?php echo $bli['id'];?>">
																	<input type="hidden" name="h-original-budget" value="<?php echo $bli['original_budget_amount'];?>">
																	<input type="hidden" name="h-revised-budget" value="<?php echo $bli['Revised Budget'];?>">
																	<input type="hidden" name="h-committed-cost" value="<?php echo $bli['Committed Costs'];?>">
																	<button type="submit" class="btn btn-sm btn-danger">Re-Sync</button>
																</form>
															</td>
															<?php }?>
														</tr>
														<?php $scount4++;}else{?>
														<?php }}}?>
															<?php 
															$pcount1++;
														}}}?>
													<?php ?>
												</tbody>
											</table>
										</div>
									</div>
									<div class="tab-pane fade" id="nav-commitments" role="tabpanel"
										aria-labelledby="nav-commitments-tab">
										<div id="commitments-available" class="table-responsive">
											<table id="zero_config" class="table table-striped table-bordered">
												<thead>
													<tr style="font-weight: bold;">
														<th>Sl No.</th>
														<th>Purchase Order Number</th>
														<th>Project</th>
														<th>Supplier</th>
														<th>Total Item</th>
														<?php 
														if($this->session->userdata('utype')==1 || $templateDetails->pt_a_procore<3){?>
															<th>Action</th>
														<?php }?>
													</tr>
												</thead>
												<tbody>
													<?php
														$count5=1;
														if(!empty($rtePoData)){
														foreach($rtePoData as $po){
													?>
													<tr>
														<td><?php echo $count5;?></td>
														<td><?php echo $po['porder_no'];?></td>
														<td><?php echo $po['project'];?></td>
														<td><?php echo $po['supplier'];?></td>
														<td><?php echo $po['porder_total_item'];?></td>
														<?php 
														if($this->session->userdata('utype')==1 || $templateDetails->pt_a_procore<3){?>
															<td>
																<form action="<?=base_url('admincontrol/procore/syncCommitments')?>" method="post">
																	<input type="hidden" name="h-company-id" value="<?php echo $company_id;?>">
																	<input type="hidden" name="h-supplier-id" value="<?php echo $po['supplier_id'];?>">
																	<input type="hidden" name="h-supplier-name" value="<?php echo $po['supplier'];?>">
																	<input type="hidden" name="h-delivery-date" value="<?php echo $po['porder_delivery_date'];?>">
																	<input type="hidden" name="h-project-id" value="<?php echo $po['project_id'];?>">
																	<input type="hidden" name="h-porder-id" value="<?php echo $po['porder_id'];?>">
																	<input type="hidden" name="h-porder-no" value="<?php echo $po['porder_no'];?>">
																	<input type="hidden" name="h-porder-address" value="<?php echo $po['porder_address'];?>">
																	<input type="hidden" name="h-porder-description" value="<?php echo $po['porder_description'];?>">
																	<input type="hidden" name="h-porder-total-items" value="<?php echo $po['porder_total_item'];?>">
																	<input type="hidden" name="h-access-token" value="<?php echo $accessToken;?>">
																	<button type="submit" class="btn btn-sm btn-danger">Sync</button>
																</form>
																<form action="<?=base_url('admincontrol/procore/unLinkCommitments')?>" style="margin-top:10px" method="post">
																	<!-- <input type="hidden" name="h-company-id" value="<?php echo $company_id;?>">
																	<input type="hidden" name="h-supplier-id" value="<?php echo $po['supplier_id'];?>">
																	<input type="hidden" name="h-supplier-name" value="<?php echo $po['supplier'];?>">
																	<input type="hidden" name="h-delivery-date" value="<?php echo $po['porder_delivery_date'];?>">
																	<input type="hidden" name="h-project-id" value="<?php echo $po['project_id'];?>"> -->
																	<input type="hidden" name="h-porder-id" value="<?php echo $po['porder_id'];?>">
																	<!-- <input type="hidden" name="h-porder-no" value="<?php echo $po['porder_no'];?>">
																	<input type="hidden" name="h-porder-address" value="<?php echo $po['porder_address'];?>">
																	<input type="hidden" name="h-porder-description" value="<?php echo $po['porder_description'];?>">
																	<input type="hidden" name="h-porder-total-items" value="<?php echo $po['porder_total_item'];?>">
																	<input type="hidden" name="h-access-token" value="<?php echo $accessToken;?>"> -->
																	<button type="submit" class="btn btn-sm btn-danger">Unlink</button>
																</form>
															</td>
														<?php }?>
													</tr>
													<?php $count5++;}}?>
												</tbody>
											</table>
										</div>
										<div id="commitments-failed" class="table-responsive" style="display:none">
											<table id="zero_config" class="table table-striped table-bordered">
												<thead>
													<tr style="font-weight: bold;">
														<th>Sl No.</th>
														<th>Purchase Order Number</th>
														<th>Project</th>
														<th>Supplier</th>
														<th>Total Item</th>
														<th>Reason</th>
														<th>Last Failed At</th>
													</tr>
												</thead>
												<tbody>
													<?php
														$fcount1=1;
														if(!empty($failedPoData)){
														foreach($failedPoData as $fpo){
													?>
													<tr>
														<td><?php echo $fcount1;?></td>
														<td><?php echo $fpo['fpo_porder_no'];?></td>
														<td><?php echo $fpo['fpo_project'];?></td>
														<td><?php echo $fpo['fpo_supplier'];?></td>
														<td><?php echo $fpo['fpo_total_items'];?></td>
														<td><?php echo $fpo['fpo_reason'];?></td>
														<td><?php echo $fpo['fpo_modifydate'];?></td>
													</tr>
													<?php $fcount1++;}}?>
												</tbody>
											</table>
										</div>
										<div id="commitments-synced" class="table-responsive" style="display:none">
											<table id="zero_config" class="table table-striped table-bordered">
												<thead>
													<tr style="font-weight: bold;">
														<th>Sl No.</th>
														<th>Purchase Order Number</th>
														<th>Project</th>
														<th>Supplier</th>
														<th>Total Item</th>
													</tr>
												</thead>
												<tbody>
													<?php
														$scount5=1;
														if(!empty($syncedPoData)){
														foreach($syncedPoData as $po1){
													?>
													<tr>
														<td><?php echo $scount5;?></td>
														<td><?php echo $po1['porder_no'];?></td>
														<td><?php echo $po1['project'];?></td>
														<td><?php echo $po1['supplier'];?></td>
														<td><?php echo $po1['porder_total_item'];?></td>
													</tr>
													<?php $scount5++;}}?>
												</tbody>
											</table>
										</div>
									</div>
								</div>			
							</div>
							<div class="col-md-2" style="background-color:#ededed">
								<?php 
								if($this->session->userdata('utype')==1 || $templateDetails->pt_a_procore<2){?>
								<?php if(!empty($procoreAuthDetails)){?>
									<div class="col-xs-12" style="text-align:center;padding:20px 20px 0px 20px">
										<button id="update-procore-cre-btn" class="btn btn-danger">Change Credentials</button>
									</div>
								<?php }}?>
								<div class="col-xs-12" style="text-align:center;padding:20px">
									<button id="data-refresh-btn" class="btn btn-danger">Refresh Data</button>
								</div>
								<div id="projects-filter" class="col-xs-12" style="padding:20px">
									<h5>Filter Projects By</h5>
									<ul style="list-style-type:none;padding:0px;margin:0px">
										<li class="filter-active"><button id="projects-available-btn" class="btn btn-link btn-sm filter-projects">Ready to import(<?php echo $count-1;?>)</button></li>
										<li><button id="projects-failed-btn" class="btn btn-link btn-sm filter-projects">Failed tp import(0)</button></li>
										<li><button id="projects-synced-btn" class="btn btn-link btn-sm filter-projects">Synced(<?php echo $scount-1;?>)</button></li>
									</ul>
								</div>
								<div id="cc-filter" class="col-xs-12" style="padding:20px;display:none">
									<h5>Filter Cost Code By</h5>
									<ul style="list-style-type:none;padding:0px;margin:0px">
										<li class="filter-active"><button id="cc-available-btn" class="btn btn-link btn-sm filter-cc">Ready to import(<?php echo $count1-1;?>)</button></li>
										<li><button id="cc-failed-btn" class="btn btn-link btn-sm filter-cc">Failed tp import(0)</button></li>
										<li><button id="cc-synced-btn" class="btn btn-link btn-sm filter-cc">Synced(<?php echo $scount1-1;?>)</li>
									</ul>
								</div>
								<div id="uom-filter" class="col-xs-12" style="padding:20px;display:none">
									<h5>Filter Units of Measure By</h5>
									<ul style="list-style-type:none;padding:0px;margin:0px">
										<li class="filter-active"><button id="uom-available-btn" class="btn btn-link btn-sm filter-uom">Ready to import(<?php echo $count2-1;?>)</button></li>
										<li><button id="uom-failed-btn" class="btn btn-link btn-sm filter-uom">Failed tp import(0)</button></li>
										<li><button id="uom-synced-btn" class="btn btn-link btn-sm filter-uom">Synced(<?php echo $scount2-1;?>)</li>
									</ul>
								</div>
								<div id="suppliers-filter" class="col-xs-12" style="padding:20px;display:none">
									<h5>Filter Suppliers By</h5>
									<ul style="list-style-type:none;padding:0px;margin:0px">
										<li class="filter-active"><button id="suppliers-available-btn" class="btn btn-link btn-sm filter-suppliers">Ready to import(<?php echo $count3-1;?>)</button></li>
										<li><button id="suppliers-failed-btn" class="btn btn-link btn-sm filter-suppliers">Failed tp import(0)</button></li>
										<li><button id="suppliers-synced-btn" class="btn btn-link btn-sm filter-suppliers">Synced(<?php echo $scount3-1;?>)</button></li>
									</ul>
								</div>
								<div id="taxgroup-filter" class="col-xs-12" style="padding:20px;display:none">
									<h5>Filter Tax Groups By</h5>
									<ul style="list-style-type:none;padding:0px;margin:0px">
										<li class="filter-active"><button id="taxgroup-available-btn" class="btn btn-link btn-sm filter-taxgroup">Ready to import(<?php echo $count6-1;?>)</button></li>
										<li><button id="taxgroup-failed-btn" class="btn btn-link btn-sm filter-taxgroup">Failed tp import(0)</button></li>
										<li><button id="taxgroup-synced-btn" class="btn btn-link btn-sm filter-taxgroup">Synced(<?php echo $scount6-1;?>)</button></li>
									</ul>
								</div>
								<div id="bli-filter" class="col-xs-12" style="padding:20px;display:none">
									<h5>Filter Budget Line Items By</h5>
									<ul style="list-style-type:none;padding:0px;margin:0px">
										<li class="filter-active"><button id="bli-available-btn" class="btn btn-link btn-sm filter-bli">Ready to import(<?php echo $count4-1;?>)</button></li>
										<li><button id="bli-failed-btn" class="btn btn-link btn-sm filter-bli">Failed tp import(0)</button></li>
										<li><button id="bli-synced-btn" class="btn btn-link btn-sm filter-bli">Synced(<?php echo $scount4-1;?>)</button></li>
									</ul>
								</div>
								<div id="commitments-filter" class="col-xs-12" style="padding:20px;display:none">
									<h5>Filter Commitments By</h5>
									<ul style="list-style-type:none;padding:0px;margin:0px">
										<li class="filter-active"><button id="commitments-available-btn" class="btn btn-link btn-sm filter-commitments">Ready to export(<?php echo $count5-1;?>)</button></li>
										<li><button id="commitments-failed-btn" class="btn btn-link btn-sm filter-commitments">Failed tp export(<?php echo $fcount1-1;?>)</button></li>
										<li><button id="commitments-synced-btn" class="btn btn-link btn-sm filter-commitments">Synced(<?php echo $scount5-1;?>)</button></li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>

	</div>
	<!-- ============================================================== -->
	<!-- End Container fluid  -->
	<!-- ============================================================== -->
	<!-- ============================================================== -->


	<?php $this->load->view('admin/component/footer') ?>

	<script type="text/javascript">
		$('#zero_config,#zero_config_1,#zero_config_3,#zero_config_4').DataTable();
		$('#zero_config_2').DataTable();
		
		$(".nav-link").click(function(){
			var id=$(this).attr('id');
			if(id == "nav-projects-tab"){
				$('#projects-filter').css('display','block');
				$('#cc-filter').css('display','none');
				$('#uom-filter').css('display','none');
				$('#suppliers-filter').css('display','none');
				$('#taxgroup-filter').css('display','none');
				$('#bli-filter').css('display','none');
				$('#commitments-filter').css('display','none');
			}else if(id == "nav-cost-code-tab"){
				$('#projects-filter').css('display','none');
				$('#cc-filter').css('display','block');
				$('#uom-filter').css('display','none');
				$('#suppliers-filter').css('display','none');
				$('#taxgroup-filter').css('display','none');
				$('#bli-filter').css('display','none');
				$('#commitments-filter').css('display','none');
			}else if(id == "nav-uom-tab"){
				$('#projects-filter').css('display','none');
				$('#cc-filter').css('display','none');
				$('#uom-filter').css('display','block');
				$('#suppliers-filter').css('display','none');
				$('#taxgroup-filter').css('display','none');
				$('#bli-filter').css('display','none');
				$('#commitments-filter').css('display','none');
			}else if(id == "nav-suppliers-tab"){
				$('#projects-filter').css('display','none');
				$('#cc-filter').css('display','none');
				$('#uom-filter').css('display','none');
				$('#suppliers-filter').css('display','block');
				$('#taxgroup-filter').css('display','none');
				$('#bli-filter').css('display','none');
				$('#commitments-filter').css('display','none');
			}else if(id == "nav-taxgroup-tab"){
				$('#projects-filter').css('display','none');
				$('#cc-filter').css('display','none');
				$('#uom-filter').css('display','none');
				$('#suppliers-filter').css('display','none');
				$('#taxgroup-filter').css('display','block');
				$('#bli-filter').css('display','none');
				$('#commitments-filter').css('display','none');
			}else if(id == "nav-bli-tab"){
				$('#projects-filter').css('display','none');
				$('#cc-filter').css('display','none');
				$('#uom-filter').css('display','none');
				$('#suppliers-filter').css('display','none');
				$('#taxgroup-filter').css('display','none');
				$('#bli-filter').css('display','block');
				$('#commitments-filter').css('display','none');
			}else if(id == "nav-commitments-tab"){
				$('#projects-filter').css('display','none');
				$('#cc-filter').css('display','none');
				$('#uom-filter').css('display','none');
				$('#suppliers-filter').css('display','none');
				$('#taxgroup-filter').css('display','none');
				$('#bli-filter').css('display','none');
				$('#commitments-filter').css('display','block');
			}
		});

		$('.filter-projects').click(function(){
			var id=$(this).attr('id');
			if(id == "projects-available-btn"){
				$(this).parent().addClass('filter-active');
				$('#projects-available').css('display','block');
				$('#projects-failed').css('display','none');
				$('#projects-synced').css('display','none');

			}else if(id == "projects-failed-btn"){
				$(this).parent().addClass('filter-active');
				$('#projects-available').css('display','none');
				$('#projects-failed').css('display','block');
				$('#projects-synced').css('display','none');
			}else if(id == "projects-synced-btn"){
				$('#projects-available').css('display','none');
				$('#projects-failed').css('display','none');
				$('#projects-synced').css('display','block');
			}
		});

		$('.filter-cc').click(function(){
			var id=$(this).attr('id');
			if(id == "cc-available-btn"){
				$(this).parent().addClass('filter-active');
				$('#cost-code-available').css('display','block');
				$('#cost-code-failed').css('display','none');
				$('#cost-code-synced').css('display','none');

			}else if(id == "cc-failed-btn"){
				$(this).parent().addClass('filter-active');
				$('#cost-code-available').css('display','none');
				$('#cost-code-failed').css('display','block');
				$('#cost-code-synced').css('display','none');
			}else if(id == "cc-synced-btn"){
				$('#cost-code-available').css('display','none');
				$('#cost-code-failed').css('display','none');
				$('#cost-code-synced').css('display','block');
			}
		});

		$('.filter-uom').click(function(){
			var id=$(this).attr('id');
			if(id == "uom-available-btn"){
				$(this).parent().addClass('filter-active');
				$('#uom-available').css('display','block');
				$('#uom-failed').css('display','none');
				$('#uom-synced').css('display','none');

			}else if(id == "uom-failed-btn"){
				$(this).parent().addClass('filter-active');
				$('#uom-available').css('display','none');
				$('#uom-failed').css('display','block');
				$('#uom-synced').css('display','none');
			}else if(id == "uom-synced-btn"){
				$('#uom-available').css('display','none');
				$('#uom-failed').css('display','none');
				$('#uom-synced').css('display','block');
			}
		});

		$('.filter-bli').click(function(){
			var id=$(this).attr('id');
			if(id == "bli-available-btn"){
				$(this).parent().addClass('filter-active');
				$('#bli-available').css('display','block');
				$('#bli-failed').css('display','none');
				$('#bli-synced').css('display','none');

			}else if(id == "bli-failed-btn"){
				$(this).parent().addClass('filter-active');
				$('#bli-available').css('display','none');
				$('#bli-failed').css('display','block');
				$('#bli-synced').css('display','none');
			}else if(id == "bli-synced-btn"){
				$('#bli-available').css('display','none');
				$('#bli-failed').css('display','none');
				$('#bli-synced').css('display','block');
			}
		});

		$('.filter-suppliers').click(function(){
			var id=$(this).attr('id');
			if(id == "suppliers-available-btn"){
				$(this).parent().addClass('filter-active');
				$('#suppliers-available').css('display','block');
				$('#suppliers-failed').css('display','none');
				$('#suppliers-synced').css('display','none');

			}else if(id == "suppliers-failed-btn"){
				$(this).parent().addClass('filter-active');
				$('#suppliers-available').css('display','none');
				$('#suppliers-failed').css('display','block');
				$('#suppliers-synced').css('display','none');
			}else if(id == "suppliers-synced-btn"){
				$('#suppliers-available').css('display','none');
				$('#suppliers-failed').css('display','none');
				$('#suppliers-synced').css('display','block');
			}
		});

		$('.filter-taxgroup').click(function(){
			var id=$(this).attr('id');
			if(id == "taxgroup-available-btn"){
				$(this).parent().addClass('filter-active');
				$('#taxgroup-available').css('display','block');
				$('#taxgroup-failed').css('display','none');
				$('#taxgroup-synced').css('display','none');
			}else if(id == "taxgroup-failed-btn"){
				$(this).parent().addClass('filter-active');
				$('#taxgroup-available').css('display','none');
				$('#taxgroup-failed').css('display','block');
				$('#taxgroup-synced').css('display','none');
			}else if(id == "taxgroup-synced-btn"){
				$('#taxgroup-available').css('display','none');
				$('#taxgroup-failed').css('display','none');
				$('#taxgroup-synced').css('display','block');
			}
		});

		$('.filter-commitments').click(function(){
			var id=$(this).attr('id');
			if(id == "commitments-available-btn"){
				$(this).parent().addClass('filter-active');
				$('#commitments-available').css('display','block');
				$('#commitments-failed').css('display','none');
				$('#commitments-synced').css('display','none');

			}else if(id == "commitments-failed-btn"){
				$(this).parent().addClass('filter-active');
				$('#commitments-available').css('display','none');
				$('#commitments-failed').css('display','block');
				$('#commitments-synced').css('display','none');
			}else if(id == "commitments-synced-btn"){
				$('#commitments-available').css('display','none');
				$('#commitments-failed').css('display','none');
				$('#commitments-synced').css('display','block');
			}
		});

		$('#insert_procore_auth_btn').click(function(){
			var clientId=$('#client_id').val();
			var clientSecret=$('#client_secret').val();
			var companyId=$('#company_id').val();
			var count=0;
			if(clientId==""){
				$('#client_id_err').html('required!');
				count++;
			}
			if(clientSecret==""){
				$('#client_secret_err').html('required!');;
				count++;
			}
			if(companyId==""){
				$('#company_id_err').html('required!');;
				count++;
			}

			if(count>0){
				return false;
			}else{
				return true;
			}
		});

		$('#update_procore_auth_btn').click(function(){
			var clientId=$('#u_client_id').val();
			var clientSecret=$('#u_client_secret').val();
			var companyId=$('#u_company_id').val();
			var count=0;
			if(clientId==""){
				$('#u_client_id_err').html('required!');
				count++;
			}
			if(clientSecret==""){
				$('#u_client_secret_err').html('required!');;
				count++;
			}
			if(companyId==""){
				$('#u_company_id_err').html('required!');;
				count++;
			}

			if(count>0){
				return false;
			}else{
				return true;
			}
		});

		$('#client_id,#client_secret,#company_id,#u_client_id,#u_client_secret,#u_company_id').click(function(){
			var id=$(this).attr('id');
			$('#'+id+'_err').html('');
		});

		$('#data-refresh-btn').click(function() {
			location.reload();
		});

		$('#update-procore-cre-btn').click(function() {
			$('#update_procore_auth_row').css('display','block')
		});

		$('#close_procore_update_auth_btn').click(function() {
			$('#update_procore_auth_row').css('display','none')
		});

		var commitmentStatus=$('#hidden-commitment-status').val();
		if(commitmentStatus == 'yes'){
			$('#commitment-error-div').css('display','block');
			setTimeout(function(){
				$('#commitment-error-div').css('display','none');
			}, 10000);
		}

	</script>
