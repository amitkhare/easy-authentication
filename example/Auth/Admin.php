<?php

namespace AdminAuth;

use Illuminate\Database\Eloquent\Model as Eloquent;

use AmitKhare\EasyAuth\UserInterface;

class Admin extends  Eloquent implements UserInterface {

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
      
        return $this->hasMany(AdminToken::class,'admin_id');
    }
    
    public function roles() {
        
        return $this->belongsToMany(AdminRole::class, 'role_admin');
    }
    
    public function profile() {
        return $this->hasOne(AdminProfile::class,'admin_id');
    }

    
}