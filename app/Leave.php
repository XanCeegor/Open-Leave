<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon;

class Leave extends Model
{
    protected $table = 'leaverequests';
    protected $guarded = [];


    public function user()
    {
        return $this->belongsTo('App\User', 'username', 'username');
    }

}
