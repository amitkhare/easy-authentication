<?php

namespace AdminAuth;

use Illuminate\Database\Eloquent\Model as Eloquent;

class AdminRole extends Eloquent  {
    
    protected $fillable = [
        'role'
    ];
    
    public function users() {
        return $this->admins();
    }
    
    public function admins() {
        return $this->belongsToMany(Admin::class, 'role_admin');
    }
    
}