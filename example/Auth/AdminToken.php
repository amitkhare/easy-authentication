<?php

namespace AdminAuth;

use Illuminate\Database\Eloquent\Model as Eloquent;

class AdminToken extends Eloquent  {

     protected $table = "admintokens";

     protected $fillable = [
        'admin_id',
        'token',
        'is_active',
        'ip',
        'user_agent',
        'referrer',
        'session_data',
    ];
    
    public function user() {
        return $this->admin();
    }
    
    public function admin() {
        return $this->belongsTo(Admin::class);
    }
}