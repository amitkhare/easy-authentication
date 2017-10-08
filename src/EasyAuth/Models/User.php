<?php

namespace AmitKhare\EasyAuth\Models;

use Illuminate\Database\Eloquent\Model;

use AmitKhare\EasyAuth\UserInterface;

use AmitKhare\EasyAuth\Models\Token;
use AmitKhare\EasyAuth\Models\Role;
use AmitKhare\EasyAuth\Models\Profile;

class User extends  Model implements UserInterface {

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
      
        return $this->hasMany(Token::class,'user_id');
    }
    
    public function roles() {
        
        return $this->belongsToMany(Role::class, 'users_roles');
    }
    
    public function profile() {
        return $this->hasOne(Profile::class,'user_id');
    }
    
    
}