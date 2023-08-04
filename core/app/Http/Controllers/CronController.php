<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\Transaction;
use App\Models\Admin;
use Carbon\Carbon;

class CronController extends Controller
{


    public function investment(){


        try{

            $investments = Investment::where('status', 0)  // Status: 0=>Running, 1=>Completed
                                         ->whereDate('next_return_date', '<=', Carbon::now())
                                         ->get();

            foreach($investments as $index => $data){
                $user = $data->user;
                $user->balance += $data->interest_amount;
                $user->save();

                $data->next_return_date = Carbon::parse($data->next_return_date)->addDay(1);
                $data->total_paid += 1;

                if($data->total_paid >= $data->total_return){
                    $data->status = 1;
                }

                $data->save();

                $transaction = new Transaction();
                $transaction->user_id = $data->user_id;
                $transaction->amount = $data->interest_amount;
                $transaction->charge = 0;
                $transaction->post_balance = $user->balance;
                $transaction->trx_type = '+';
                $transaction->trx = getTrx();
                $transaction->details = 'Get Interest From '.$data->plan->name;
                $transaction->save();

            }

        }catch(\Exception $ex){
           // $admin = Admin::first();
          //  sendGeneralEmail($admin->email, $ex->getMessage(), $ex->getMessage(), '');
           // \Log::error('CronController -> investment() line '. __LINE__ .': '.$ex->getMessage() ."\n");
        }


    }


}
