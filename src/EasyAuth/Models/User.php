<?php

namespace AmitKhare\EasyAuth\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

use AmitKhare\EasyAuth\UserInterface;

use AmitKhare\EasyAuth\Models\UsersToken;
use AmitKhare\EasyAuth\Models\UsersRole;
use AmitKhare\EasyAuth\Models\UsersProfile;

class User extends  Eloquent implements UserInterface {

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
      
        return $this->hasMany(UsersToken::class,'user_id');
    }
    
    public function roles() {
        
        return $this->belongsToMany(UsersRole::class, 'user_role','user_id','role_id');
    }
    
    public function profile() {
        return $this->hasOne(UsersProfile::class,'user_id');
    }

    
}