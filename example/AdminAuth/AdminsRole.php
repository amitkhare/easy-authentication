<?php

namespace AdminAuth;

use Illuminate\Database\Eloquent\Model as Eloquent;

class AdminsRole extends Eloquent  {
    
    protected $fillable = [
        'role'
    ];
    
    public function users() {
        return $this->admins();
    }
    
    public function admins() {
        return $this->belongsToMany(Admin::class, 'admin_role','admin_id', 'role_id');
    }
    
}