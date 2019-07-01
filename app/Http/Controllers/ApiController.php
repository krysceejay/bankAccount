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
      //total balance
      $totalBalance = $this->getDeposit($userId) - $this->getWithdrawal($userId);

      if($totalBalance == 0 || !empty($totalBalance)){
        return response()->json(['status' => 'success', 'status code' => 200,'data' => 'USD'.$totalBalance], 200);
      }else{
        return response()->json(['status' => 'error', 'message' => 'An error occurred', 'status code' => 500], 500);
      }

    }

    public function getDeposit($userId){
      //get user deposit
      $deposits = Account::where('user_id', $userId)->where('account_action', 1)->get();

      $totalDeposit = 0;

      foreach ($deposits as $dp) {
        $totalDeposit += $dp->amount;
      }

      return $totalDeposit;

    }

    public function getWithdrawal($userId){
      //get user withdrawal
      $withdrawal = Account::where('user_id', $userId)->where('account_action', 0)->get();

      $totalWithdrawal = 0;

      foreach ($withdrawal as $withd) {
        $totalWithdrawal += $withd->amount;
      }

      return $totalWithdrawal;

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

      $amount = $request->input('amount');

      $totalDepositToday = 0;
      $todayDeposits = Account::where('user_id', $userId)->where('account_action', 1)->whereDate('created_at', Carbon::today())->get();

      //deposit Frequecy
      $depositFrequency = $todayDeposits->count();

      if($depositFrequency >= 4){
        return response()->json(['error' => 'You cannot exceed your maximum deposit frequency of 4 transactions per day', 'status code' => 400], 400);

      }

      foreach ($todayDeposits as $dp) {
        $totalDepositToday += $dp->amount;
      }

      $depositPlusAmount = $totalDepositToday + $amount;

        //Maximum deposit for a day
      if($depositPlusAmount >= 150000){
        return response()->json(['error' => 'You cannot exceed your maximum deposit of USD150000 for today', 'status code' => 400], 400);
      }

      //Maximum deposit per transaction
    if($request->input('amount') > 40000){
      return response()->json(['error' => 'deposit should not exceed 40000 per transaction', 'status code' => 400], 400);
    }

    // Add deposit
    $addDeposit = Account::create([
      'user_id' => $userId,
      'amount' => $amount,
      'account_action' => 1

    ]);
        if ($addDeposit) {
          return response()->json(['status' => 'success','status code' => 200,'message' => 'Amount deposited successfully.'], 200);
        } else {
          // Otherwise, send failure message
          return response()->json(['status' => 'error','message' => 'An error occurred', 'status code' => 500], 500);
        }

    }

    public function withdrawal(Request $request, $userId)
    {
        $validator = Validator::make($request->all(), [
          'amount' => 'required|integer'

        ]);
        // Throw error if validation fails
        if ($validator->fails()) {
          return response()->json(['error' => $validator->errors(), 'status code' => 400], 400);
        }

        $amount = $request->input('amount');

        //total balance
        $totalBalance = $this->getDeposit($userId) - $this->getWithdrawal($userId);

        if($amount > $totalBalance){
          return response()->json(['error' => 'You have insufficient fund', 'status code' => 400], 400);

        }

        $totalWithdrawalToday = 0;
        $todayWithdrawal = Account::where('user_id', $userId)->where('account_action', 0)->whereDate('created_at', Carbon::today())->get();

        //Withdrawal Frequecy
        $withdrawalFrequency = $todayWithdrawal->count();

        if($withdrawalFrequency >= 3){
          return response()->json(['error' => 'You cannot exceed your maximum withdrawal frequency of 3 transactions per day', 'status code' => 400], 400);

        }


        foreach ($todayWithdrawal as $withd) {
          $totalWithdrawalToday += $withd->amount;
        }

        $withdrawalPlusAmount = $totalWithdrawalToday + $amount;

          //Maximum withdrawal for a day
        if($withdrawalPlusAmount >= 50000){
          return response()->json(['error' => 'You cannot exceed your maximum withdrawal of USD50000 for today', 'status code' => 400], 400);
        }

        //Maximum withdrawal per transaction
      if($request->input('amount') > 20000){
        return response()->json(['error' => 'Withdrawal should not exceed 20000 per transaction', 'status code' => 400], 400);
      }

        // Add withdrawal
        $addWithdrawal = Account::create([
          'user_id' => $userId,
          'amount' => $amount,
          'account_action' => 0

        ]);
            if ($addWithdrawal) {
              return response()->json(['status' => 'success','status code' => 200,'message' => 'Amount withdrawn successfully.'], 200);
            } else {
              // Otherwise, send failure message
              return response()->json(['status' => 'error','message' => 'An error occurred', 'status code' => 500], 500);
            }

    }

}
