<?php

namespace CustomerAuth;

use Illuminate\Database\Eloquent\Model as Eloquent;

use AmitKhare\EasyAuth\UserInterface;

class Customer extends  Eloquent implements UserInterface {

    protected $fillable = [
        'username',
        'email',
        'password',
        'mobile',
        'password_recovery_hash',
        'email_verification_hash',
        'is_active',
    ];
    
    
    public function tokens() {
      
        return $this->hasMany(CustomersToken::class,'customer_id');
    }
    
    public function roles() {
        
        return $this->belongsToMany(CustomersRole::class, 'customer_role');
    }
    
    public function profile() {
        return $this->hasOne(CustomersProfile::class,'customer_id');
    }

    
}