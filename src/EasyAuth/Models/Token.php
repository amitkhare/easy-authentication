<?php

namespace AmitKhare\EasyAuth\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

use AmitKhare\EasyAuth\Models\User;

class Token extends Eloquent  {
     
     protected $fillable = [
        'user_id',
        'token',
        'is_active',
        'ip',
        'user_agent',
        'referrer',
        'session_data',
    ];
    public function user() {
        return $this->belongsTo(User::class);
    }
}