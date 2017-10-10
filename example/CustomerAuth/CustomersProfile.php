<?php

namespace CustomerAuth;

use Illuminate\Database\Eloquent\Model as Eloquent;

class CustomersProfile extends Eloquent  {
    
    protected $fillable = [
        'customer_id',
        'firstname',
        'middlename',
        'lastname',
        'gender'
    ];
    
    public function user() {
        return $this->customer();
    }
    
    public function customer() {
        return $this->belongsTo(Customer::class);
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