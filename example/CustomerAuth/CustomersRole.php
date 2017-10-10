<?php

namespace CustomerAuth;

use Illuminate\Database\Eloquent\Model as Eloquent;

class CustomersRole extends Eloquent  {
    
    protected $fillable = [
        'role'
    ];
    
    public function users() {
        return $this->customers();
    }
    
    public function customers() {
        return $this->belongsToMany(Customer::class, 'customer_role');
    }
    
}