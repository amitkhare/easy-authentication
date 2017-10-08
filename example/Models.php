<?php
use Illuminate\Database\Eloquent\Model;

class User extends AmitKhare\EasyAuth\Models\User  {
    
    public function profile() {
        return $this->hasOne(Profile::class,'user_id');
    }
}


class Profile extends Model  {
    
    protected   $table = 'profiles';
    
    protected $fillable = [
        'user_id',
        'firstname',
        'middlename',
        'lastname',
        'gender'
    ];
    
    public function User() {
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