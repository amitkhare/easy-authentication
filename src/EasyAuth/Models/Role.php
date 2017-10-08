<?php
namespace AmitKhare\EasyAuth\Models;

use Illuminate\Database\Eloquent\Model;

use AmitKhare\EasyAuth\Models\User;

class Role extends Model  {
    
    protected   $table = 'roles';
    
    protected $fillable = [
        'role'
    ];
    public function users() {
        return $this->belongsToMany(User::class, 'users_roles');
    }
}