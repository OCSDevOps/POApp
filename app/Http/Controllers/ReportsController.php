<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\JMC_MASTER_COST_CODE;
use App\Models\JMC_MASTER_JOB;

class ReportsController extends Controller
{
    //
    function getCcSummary(Request $req){

        if(Auth::check()){
            $jobs=$req->input('report_filter_select');
            $jobsArray=[];
            if(!empty($jobs)){
                foreach($jobs as $job){
                    array_push($jobsArray,$job);
                }
            }
            if(!empty($req->input('report_filter_select'))){
                $data=JMC_MASTER_COST_CODE::selectRaw('
                Cost_Code,
                min(Description) as Description,
                sum(Original_Estimate) as Original_Estimate_Sum,
                sum(Total_Estimate) as Total_Estimate_Sum,
                sum(Revised_Commitment) as Revised_Commitment_Sum,
                sum(Commitment_Invoiced) as Commitment_Invoiced_Sum,
                sum(JTD_Cost) as JTD_Cost_Sum')->whereIn('Job',$jobsArray)->groupBy('Cost_Code')->orderBy('Cost_Code')->get();
            }else{
                $data='';
            }
    
            $data1=JMC_MASTER_JOB::select('Job','Description')->get();
    
            return view('profile_pages/ccsummary',['reportData'=>$data,'jobs'=>$data1,'reqJobs'=>$req->input('report_filter_select')]);
        }

        return redirect('/')->with('error-login','CC Summary Report');
    }
}
