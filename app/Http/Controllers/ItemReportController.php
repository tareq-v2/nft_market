<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ItemReport;
use App\Models\ReportUser;
use App\Models\ItemProblem;
use Illuminate\Http\Request;
use App\Models\ReportCategory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\Null_;

class ItemReportController extends Controller
{
    public function index()
    {
        return view('admin.ItemReport.index', ['reports' => ItemReport::all()]);
    }

    public function seeReport(){
        return view('admin.ItemReport.problem', ['problems' => ItemProblem::all()]);
    }

    public function ItemProblem(){
        return view('admin.ItemReport.problem', ['problems' => ItemProblem::all()]);
    }

    public function userReportIndex(){
        return view('admin.ItemReport.userReport', ['report_user' => ReportUser::all()]);
    }

    public function ItemProblemsDestroy($id){
        $deleteProblem = ItemProblem::find($id);
        $deleteProblem->delete();
        return back()->withErrors("Problem Deleted Success !");
    }

    public function store(Request $request)
    {
        $request->validate(['problem' => 'required']);
        ItemReport::create($request->except('_token') + ['created_at' => Carbon::now()]);
        return back()->withSuccess('Problem Create Successful');
    }

    public function update(Request $request,$id)
    {
        $request->validate(['problem' => 'required']);
        $itemReport = ItemReport::find($id);
        $itemReport->update($request->except('_token') + ['updated_at' => Carbon::now()]);
        return back()->withSuccess('Problem Update Successful');
    }
    public function destroy($id)
    {
        $itemReport = ItemReport::find($id);

        $probs = ItemProblem::where('problem',$id)->get();
        foreach($probs as $prob){
            $prob->update([
                'problem' => NULL,
            ]);
        }

        $users = ReportUser::where('report_id',$id)->get();
        foreach($users as $user){
            $user->update([
                'report_id' => NULL,
            ]);
        }
        $users = ReportCategory::where('report_id',$id)->get();
        foreach($users as $user){
            $user->update([
                'report_id' => NULL,
            ]);
        }

        $itemReport->delete();
        return back()->withDanger('Problem Delete Success !');
    }

    public function problemStore(Request $request)
    {
        // $request->validate([
        //     'problem' => 'required',
        // ],[
        //     'problem.required'=>'Please Select a Report !!!',
        //  ]);
        if($request->report_id){
            ItemProblem::create([
                'user_id'    => $request->user_id,
                'item_id'    => $request->item_id,
                'problem'    => $request->report_id,
                'created_at' => Carbon::now()
            ]);
            return response()->json([
                'status' => 200,
                'message' => "Report Place Success !",
            ]);
        }else{
            return response()->json(['message' => "Please Select a Report !"]);
        }
    }

    public function report_to_user(Request $request)
    {

        if($request->report_id){
            ReportUser::create([
                'report_to'    => $request->report_to,
                'report_by'    => $request->report_by,
                'report_id'    => $request->report_id,
                'created_at' => Carbon::now()
            ]);
            return response()->json([
                'status' => 200,
                'message' => "Report Place Success !",
            ]);
        }else{
            return response()->json(['message' => "Please Select a Report !"]);
        }
    }
    public function report_to_category(Request $request)
    {
        if($request->report_id){
            ReportCategory::create([
                'report_by'    => $request->report_by,
                'category_id'  => $request->category_id,
                'report_id'    => $request->report_id,
                'created_at' => Carbon::now()
            ]);
            return response()->json([
                'status' => 200,
                'message' => "Report Place Success !",
            ]);
        }else{
            return response()->json(['message' => "Please Select a Report !"]);
        }
    }

    public function ItemProblemDestroy($id)
    {
        $itemProblem = ItemProblem::find($id);
        $itemProblem->delete();
        return back()->withDanger('Report Deleted Success !');
    }
}
