<?php

namespace AmitKhare\EasyAuth\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

use AmitKhare\EasyAuth\Models\User;

class UsersRole extends Eloquent  {
    
    protected $fillable = [
        'role'
    ];
    public function users() {
        return $this->belongsToMany(User::class, 'user_role');
    }
    
}