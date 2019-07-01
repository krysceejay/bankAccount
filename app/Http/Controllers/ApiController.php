<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\User;
use App\Account;
use Carbon\Carbon;

class ApiController extends Controller
{
    //get account balance for a user
    public function accountBalance($userId)
    {
      $totalDeposit = 0;
      $deposits = Account::where('user_id', $userId)->where('account_action', 1)->get();

      foreach ($deposits as $dp) {
        $totalDeposit += $dp->amount;
      }

      if($totalDeposit){
        return response()->json(['status' => 'success', 'status code' => 200,'data' => 'USD'.$totalDeposit], 200);
      }else{
        return response()->json(['status' => 'error', 'message' => 'An error occurred', 'status code' => 500], 500);
      }

    }

    //deposit
    public function deposit(Request $request, $userId)
    {
      $validator = Validator::make($request->all(), [
        'amount' => 'required|integer'

      ]);

      // Throw error if validation fails
      if ($validator->fails()) {
        return response()->json(['error' => $validator->errors(), 'status code' => 400], 400);
      }

      $totalDepositToday = 0;
      $todayDeposits = Account::where('user_id', $userId)->where('account_action', 1)->whereDate('created_at', Carbon::today())->get();
      $depositFrequency = $todayDeposits->count();

      if($depositFrequency >= 4){
        return response()->json(['error' => 'You cannot exceed your maximum deposit frequency of 4 transactions per day', 'status code' => 400], 400);

      }

      foreach ($todayDeposits as $dp) {
        $totalDepositToday += $dp->amount;
      }

      $depositPlusAmount = $totalDepositToday + $request->input('amount');

      if($depositPlusAmount >= 150000){
        return response()->json(['error' => 'You cannot exceed your maximum deposit of USD150000 for today', 'status code' => 400], 400);
      }

    if($request->input('amount') > 40000){
      return response()->json(['error' => 'deposit should not exceed 40000 per transaction', 'status code' => 400], 400);
    }

    // Add deposit
    $addDeposit = Account::create([
      'user_id' => $userId,
      'amount' => $request->input('amount'),
      'account_action' => 1

    ]);
        if ($addDeposit) {
          return response()->json(['status' => 'success','status code' => 200,'message' => 'Amount deposited successfully.'], 200);
        } else {
          // Otherwise, send failure message
          return response()->json(['status' => 'error','message' => 'An error occurred', 'status code' => 500], 500);
        }

    }

    // public function withdrawal(Request $request, $userId)
    // {
    //
    // }

}
