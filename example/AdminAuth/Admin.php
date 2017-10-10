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
      
        return $this->hasMany(AdminsToken::class,'admin_id');
    }
    
    public function roles() {
        
        return $this->belongsToMany(AdminsRole::class, 'admin_role');
    }
    
    public function profile() {
        return $this->hasOne(AdminsProfile::class,'admin_id');
    }

    
}