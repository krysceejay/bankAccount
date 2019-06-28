<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\User;
use App\Account;

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
    public function deposit(Request $request, $userid)
    {
      $validator = Validator::make($request->all(), [
        'amount' => 'required|integer'

      ]);

      // Throw error if validation fails
    if ($validator->fails()) {
      return response()->json(['error' => $validator->errors(), 'status code' => 400], 400);
    }

    if($request->input('amount') > 40000){
      return response()->json(['error' => 'deposit should not exceed 40000 per transaction', 'status code' => 400], 400);
    }

    // Add deposit
    $addDeposit = Account::create([
      'user_id' => $userid,
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

}
