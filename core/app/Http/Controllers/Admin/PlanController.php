<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan;

class PlanController extends Controller
{


    public function index(){
        $pageTitle = 'Investment Plans';
        $plan = Plan::latest()->paginate(getPaginate());
        $emptyMessage = 'Data Not Found';
        return view('admin.plan.index', compact('pageTitle', 'plan', 'emptyMessage'));
    }

    public function create(Request $request){

        $request->validate([
            'name'=> 'required|string|max:191',
            'min_amount'=> 'required|numeric|gt:0',
            'max_amount'=> 'required|numeric|gt:min_amount',
            'total_return'=> 'required|integer|gt:0',
            'interest_type'=> 'required|in:0,1',
            'interest'=> 'required|numeric|gt:0',
            'status'=> 'required|in:0,1',
        ]);

        $plan = new Plan();
        $plan->name = $request->name;
        $plan->min_amount = $request->min_amount;
        $plan->max_amount = $request->max_amount;
        $plan->total_return = $request->total_return;
        $plan->interest_type = $request->interest_type;  //	1=>Percent, 0=>Fixed
        $plan->interest_amount = $request->interest;
        $plan->status = $request->status;
        $plan->save();

        $notify[] = ['success', 'Plan created successfully'];
        return redirect()->back()->withNotify($notify);

    }

    public function edit(Request $request){

        $request->validate([
            'id'=> 'required|exists:plans,id',
            'name'=> 'required|string|max:191',
            'min_amount'=> 'required|numeric|gt:0',
            'max_amount'=> 'required|numeric|gt:min_amount',
            'total_return'=> 'required|integer|gt:0',
            'interest_type'=> 'required|in:0,1',
            'interest'=> 'required|numeric|gt:0',
            'status'=> 'required|in:0,1',
        ]);

        $findPlan = Plan::find($request->id);
        $findPlan->name = $request->name;
        $findPlan->min_amount = $request->min_amount;
        $findPlan->max_amount = $request->max_amount;
        $findPlan->total_return = $request->total_return;
        $findPlan->interest_type = $request->interest_type;  //	1=>Percent, 0=>Fixed
        $findPlan->interest_amount = $request->interest;
        $findPlan->status = $request->status;
        $findPlan->save();

        $notify[] = ['success', 'Plan updated successfully'];
        return redirect()->back()->withNotify($notify);
    }







}
