@extends('layouts.dashboard')

@section('css')

<!-- third party css -->
<link href="assets/css/vendor/dataTables.bootstrap5.css" rel="stylesheet" type="text/css">
<link href="assets/css/vendor/responsive.bootstrap5.css" rel="stylesheet" type="text/css">
<link href="assets/css/vendor/buttons.bootstrap5.css" rel="stylesheet" type="text/css">
<link href="assets/css/vendor/select.bootstrap5.css" rel="stylesheet" type="text/css">
<!-- third party css end -->
    
@endsection

@section('content')

    <!-- Start Content-->
    <div class="container-fluid">
                            
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Hyper</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Reports</a></li>
                            <li class="breadcrumb-item active">Cost Code Summary</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Reports</h4>
                </div>
            </div>
        </div>     
        <!-- end page title --> 

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title pb-2">Cost Code Summary</h4>

                        {{-- Search filter for report --}}

                        <form id="report-filter-form" action="{{ route('ccsummary') }}" method="post">
                            @csrf
                            <select id="report-filter-select" name="report_filter_select[]" class="select2 form-control select2-multiple" data-bs-toggle="select2" multiple="multiple" data-placeholder="Choose Jobs...">
                                @foreach ($jobs as $job)
                                    <option value="{{$job->Job}}"
                                        @if (!empty($reqJobs) && in_array($job->Job,$reqJobs))
                                            {{'selected'}}
                                        @endif
                                    >{{$job->Job.'-'.$job->Description}}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-info mt-2"><i class="mdi mdi-filter me-1"></i> <span>Generate Report</span> </button>
                        </form>

                        {{-- Search filter for report end --}}

                        @php
                            $count=1;
                            $headingCount=0;
                            $groupRowCount=0;
                            $formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
                            $category='';
                            $subCategory='';
                            $category1='';
                            $subCategory1='';
                            $originalEstimateTotalGroup=0;
                            $originalEstimateTotal=0;
                            $totalEstimateTotalGroup=0;
                            $totalEstimateTotal=0;
                            $totalCommitmentTotalGroup=0;
                            $totalCommitmentTotal=0;
                            $commitmentInvoicedTotalGroup=0;
                            $commitmentInvoicedTotal=0;
                            $jtdCostTotalGroup=0;
                            $jtdCostTotal=0;
                        @endphp

                        @if (!empty($reportData))
                            @foreach ($reportData as $i)
                                @php
                                    $cc = $i->Cost_Code;
                                    $ccArray = explode('-', $cc);
                                @endphp
                                @if ($ccArray[1]!='00' && $ccArray[2]=='00')
                                    @php
                                        $category=$ccArray[0];
                                        $subCategory=$ccArray[1];
                                        ${$ccArray[0].$ccArray[1].'_count'}=0;
                                    @endphp
                                @else
                                    @if ($category==$ccArray[0] && $subCategory==$ccArray[1])
                                        @php
                                            ${$ccArray[0].$ccArray[1].'_count'}++;
                                        @endphp
                                    @endif
                                @endif
                            @endforeach
                        @endif
                        {{-- Report table starts --}}

                        <div class="table-responsive mt-2">
                            <table class="table mb-0" style="font-size:12px;">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width:5%!important;vertical-align:middle">S NO</th>
                                        <th style="width:10%!important;vertical-align:middle">Cost Code</th>
                                        <th style="width:15%!important;vertical-align:middle">Description</th>
                                        <th style="width:10%!important;vertical-align:middle">Original Estimate</th>
                                        <th style="width:10%!important;vertical-align:middle">Total Estimate</th>
                                        <th style="width:10%!important;vertical-align:middle">Total Commitment</th>
                                        <th style="width:10%!important;vertical-align:middle">Commitment Invoiced</th>
                                        <th style="width:10%!important;vertical-align:middle">JTD Cost</th>
                                        <th style="width:10%!important;vertical-align:middle">Percentage Completed</th>
                                        <th style="width:10%!important;vertical-align:middle">Cost To Complete</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    {{-- if condition to check if any jobs is selected or not --}}

                                    @if (!empty($reportData))

                                        {{-- For each loop to render cost codes data start --}}

                                        @foreach ($reportData as $item)
                                            @php
                                                $myString = $item->Cost_Code;
                                                $myArray = explode('-', $myString);
                                            @endphp
                                            @if ($myArray[1]!='00')
                                                @if ($myArray[2]=='00' && ${$myArray[0].$myArray[1].'_count'}>0)
                                                    <tr class="table-dark" style="font-weight:bold">
                                                        <td colspan="10">{{$item->Description;}}</td>
                                                    </tr>
                                                    @php
                                                        $category1=$myArray[0];
                                                        $subCategory1=$myArray[1];
                                                        $heading=$item->Description;
                                                        $headingCount++;
                                                        $originalEstimateTotalGroup=0;
                                                        $totalEstimateTotalGroup=0;
                                                        $totalCommitmentTotalGroup=0;
                                                        $commitmentInvoicedTotalGroup=0;
                                                        $jtdCostTotalGroup=0;
                                                        $groupRowCount=0;
                                                    @endphp
                                                @else
                                                    @if ($category1==$myArray[0] && $subCategory1==$myArray[1] && $myArray[2]!='00')
                                                        @php
                                                            $originalEstimateTotalGroup=$originalEstimateTotalGroup+$item->Original_Estimate_Sum;
                                                            $totalEstimateTotalGroup=$totalEstimateTotalGroup+$item->Total_Estimate_Sum;
                                                            $totalCommitmentTotalGroup=$totalCommitmentTotalGroup+$item->Revised_Commitment_Sum;
                                                            $commitmentInvoicedTotalGroup=$commitmentInvoicedTotalGroup+$item->Commitment_Invoiced_Sum;
                                                            $jtdCostTotalGroup=$jtdCostTotalGroup+$item->JTD_Cost_Sum;
                                                            $originalEstimateTotal=$originalEstimateTotal+$item->Original_Estimate_Sum;
                                                            $totalEstimateTotal=$totalEstimateTotal+$item->Total_Estimate_Sum;
                                                            $totalCommitmentTotal=$totalCommitmentTotal+$item->Revised_Commitment_Sum;
                                                            $commitmentInvoicedTotal=$commitmentInvoicedTotal+$item->Commitment_Invoiced_Sum;
                                                            $jtdCostTotal=$jtdCostTotal+$item->JTD_Cost_Sum;
                                                            $groupRowCount++;
                                                        @endphp
                                                    @endif
                                                    @if ($myArray[2]!='00')
                                                        <tr>
                                                            <td>{{$count;}}</th>
                                                            <td>{{$item->Cost_Code;}}</td>
                                                            <td>{{$item->Description;}}</td>
                                                            <td>{{$formatter->formatCurrency($item->Original_Estimate_Sum, 'USD');}}</td>
                                                            <td>{{$formatter->formatCurrency($item->Total_Estimate_Sum, 'USD');}}</td>
                                                            <td>{{$formatter->formatCurrency($item->Revised_Commitment_Sum, 'USD');}}</td>
                                                            <td>{{$formatter->formatCurrency($item->Commitment_Invoiced_Sum, 'USD');}}</td>
                                                            <td>{{$formatter->formatCurrency($item->JTD_Cost_Sum, 'USD');}}</td>
                                                            <td>@if ($item->Total_Estimate_Sum!=0)
                                                                @if(round($item->JTD_Cost_Sum/$item->Total_Estimate_Sum*100,2) > 100)
                                                                    <span style="color:red">{{round($item->JTD_Cost_Sum/$item->Total_Estimate_Sum*100,2).' %';}}</span>
                                                                @else
                                                                    {{round($item->JTD_Cost_Sum/$item->Total_Estimate_Sum*100,2).' %';}}
                                                                @endif
                                                            @else
                                                                {{'0 %'}}
                                                            @endif</th>
                                                            <td>{{$formatter->formatCurrency(($item->Total_Estimate_Sum-$item->JTD_Cost_Sum)-($item->Revised_Commitment_Sum-$item->Commitment_Invoiced_Sum), 'USD');}}</td>
                                                        </tr>
                                                    @endif

                                                    @if ($groupRowCount==${$myArray[0].$myArray[1].'_count'})

                                                        <tr style="font-weight:bold">
                                                            <td colspan="3">{{'Total '.$heading;}}</th>
                                                            <td>{{$formatter->formatCurrency($originalEstimateTotalGroup, 'USD');}}</td>
                                                            <td>{{$formatter->formatCurrency($totalEstimateTotalGroup, 'USD');}}</td>
                                                            <td>{{$formatter->formatCurrency($totalCommitmentTotalGroup, 'USD');}}</td>
                                                            <td>{{$formatter->formatCurrency($commitmentInvoicedTotalGroup, 'USD');}}</td>
                                                            <td>{{$formatter->formatCurrency($jtdCostTotalGroup, 'USD');}}</td>
                                                            <td>@if ($totalEstimateTotalGroup!=0)
                                                                @if(round($jtdCostTotalGroup/$totalEstimateTotalGroup*100,2) > 100)
                                                                    <span style="color:red">{{round($jtdCostTotalGroup/$totalEstimateTotalGroup*100,2).' %';}}</span>
                                                                @else
                                                                    {{round($jtdCostTotalGroup/$totalEstimateTotalGroup*100,2).' %';}}
                                                                @endif
                                                            @else
                                                                {{'0 %'}}
                                                            @endif</th>
                                                            <td>{{$formatter->formatCurrency(($totalEstimateTotalGroup-$jtdCostTotalGroup)-($totalCommitmentTotalGroup-$commitmentInvoicedTotalGroup), 'USD');}}</td>
                                                        </tr>
                                                    @endif
                                                @endif
                                            @endif
                                            @php
                                                $count++;
                                            @endphp
                                        @endforeach

                                        <tr style="font-weight:bold">
                                            <td colspan="3">Report Total</th>
                                            <td>{{$formatter->formatCurrency($originalEstimateTotal, 'USD');}}</td>
                                            <td>{{$formatter->formatCurrency($totalEstimateTotal, 'USD');}}</td>
                                            <td>{{$formatter->formatCurrency($totalCommitmentTotal, 'USD');}}</td>
                                            <td>{{$formatter->formatCurrency($commitmentInvoicedTotal, 'USD');}}</td>
                                            <td>{{$formatter->formatCurrency($jtdCostTotal, 'USD');}}</td>
                                            <td>@if ($totalEstimateTotal!=0)
                                                @if(round($jtdCostTotal/$totalEstimateTotal*100,2) > 100)
                                                    <span style="color:red">{{round($jtdCostTotal/$totalEstimateTotal*100,2).' %';}}</span>
                                                @else
                                                    {{round($jtdCostTotal/$totalEstimateTotal*100,2).' %';}}
                                                @endif
                                            @else
                                                {{'0 %'}}
                                            @endif</th>
                                            <td>{{$formatter->formatCurrency(($totalEstimateTotal-$jtdCostTotal)-($totalCommitmentTotal-$commitmentInvoicedTotal), 'USD');}}</td>
                                        </tr>

                                    {{-- else condition if not job is selected for report --}}

                                    @else
                                        <tr>
                                            <th colspan="10" style="text-align:center;font-size:16px;"> Select Jobs from above jobs list to generate report.</td>
                                        </tr>
                                    @endif

                                    {{-- if condition to check if any jobs is selected or not --}}
                                </tbody>
                            </table>                                           
                        </div> <!-- end report table-->

                    </div> <!-- end card body-->
                </div> <!-- end card -->
            </div><!-- end col-->
        </div>
        <!-- end row-->
        
    </div> <!-- container -->
    
@endsection

@section('js')

    <!-- third party js -->
    <script src="assets/js/vendor/jquery.dataTables.min.js"></script>
    <script src="assets/js/vendor/dataTables.bootstrap5.js"></script>
    <script src="assets/js/vendor/dataTables.responsive.min.js"></script>
    <script src="assets/js/vendor/responsive.bootstrap5.min.js"></script>
    <script src="assets/js/vendor/dataTables.buttons.min.js"></script>
    <script src="assets/js/vendor/buttons.bootstrap5.min.js"></script>
    <script src="assets/js/vendor/buttons.html5.min.js"></script>
    <script src="assets/js/vendor/buttons.flash.min.js"></script>
    <script src="assets/js/vendor/buttons.print.min.js"></script>
    <script src="assets/js/vendor/dataTables.keyTable.min.js"></script>
    <script src="assets/js/vendor/dataTables.select.min.js"></script>
    <!-- third party js ends -->

    <!-- demo app -->
    <script src="assets/js/pages/demo.datatable-init.js"></script>
    <!-- end demo js-->
    {{-- <script>
        $('#report-filter-select').on('change',function(){
            $('#report-filter-form').submit();
        });
    </script> --}}
        
@endsection

