<?php
use Illuminate\Database\Eloquent\Model;
use AmitKhare\EasyAuth\UserInterface;


class User extends Model implements UserInterface {
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
}

class Token extends Model  {
     protected $fillable = [
        'user_id',
        'token',
        'is_active',
        'ip',
        'user_agent',
        'referrer',
        'session_data',
    ];
    public function user() {
        return $this->belongsTo(User::class);
    }
}

class Role extends Model  {
    protected $fillable = [
        'role'
    ];
    public function users() {
        return $this->belongsToMany(User::class, 'users_roles');
    }
}
