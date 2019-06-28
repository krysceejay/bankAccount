<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    //
    protected $fillable = [
      'user_id',
      'amount',
      'account_action'

    ];

    public function user()
    {
      return $this->belongsTo(User::class);
    }
}
