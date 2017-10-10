<?php

namespace CustomerAuth;

use Illuminate\Database\Eloquent\Model as Eloquent;

class CustomersToken extends Eloquent  {

     protected $fillable = [
        'customer_id',
        'token',
        'is_active',
        'ip',
        'user_agent',
        'referrer',
        'session_data',
    ];
    
    public function user() {
        return $this->customer();
    }
    
    public function customer() {
        return $this->belongsTo(Customer::class);
    }
}