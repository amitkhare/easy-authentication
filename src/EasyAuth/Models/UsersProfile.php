<?php

namespace AmitKhare\EasyAuth\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

use AmitKhare\EasyAuth\Models\User;

class UsersProfile extends Eloquent  {
    
    protected $fillable = [
        'user_id',
        'firstname',
        'middlename',
        'lastname',
        'gender'
    ];
    
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function fullname() {
        $fullname = ($this->user->username) ? $this->user->username : $this->user->email;
        
        if($this->firstname){
            $fullname = $this->firstname;
        }
        if($this->middlename){
            $fullname .= " ".$this->middlename;
        }
        if($this->lastname){
            $fullname .= " ".$this->lastname;
        }
        
        return $fullname;
    }
}