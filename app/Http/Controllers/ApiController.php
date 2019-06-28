<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

      $acctBal = $totalDeposit * 1000;

      if($acctBal){
        return response()->json(['message' => 'success','status code' => 200,'data' => $acctBal], 200);
      }else{
        return response()->json(['message' => 'An error occurred'], 500);
      }

    }

}
