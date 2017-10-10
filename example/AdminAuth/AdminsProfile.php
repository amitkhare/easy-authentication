<?php

namespace AdminAuth;

use Illuminate\Database\Eloquent\Model as Eloquent;

class AdminsProfile extends Eloquent  {
    
    protected $fillable = [
        'admin_id',
        'firstname',
        'middlename',
        'lastname',
        'gender'
    ];
    
    public function user() {
        return $this->admin();
    }
    
    public function admin() {
        return $this->belongsTo(Admin::class);
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