<?php

namespace AmitKhare\EasyAuth;

class ValidationRules {
    protected $fieldRules=[];
    
    public function __construct($validationRules=null){
        
        if(empty($this->fieldRules)){
        
            $this->fieldRules = $this->defaultRules();
            
        }
        
        $this->setRules($validationRules);
        
    }
   
    private function setRules($validationRules = null){
        if(is_array($validationRules) && !empty($validationRules)){
            foreach ($validationRules as $field=>$rules) {
                $this->fieldRules[$field] = ($rules && $rules !="") ? $rules : "required";
            }
        }
    }
    
    public function getRules($field=null){
        if($field){
            return $this->fieldRules[$field];
        }
        return $this->fieldRules;
    }
    
    public function r($field){
        
        return (isset($this->fieldRules[$field])) ? $this->fieldRules[$field] : "required";
        
    }
    
    public function addRules($field,$rules){
        $this->fieldRules[$field] = $rules;
    }
    
    
    private function defaultRules(){
        
        $rules = [
            "identifier"    => "required|min:2|max:25",
            "email"         => "required|email",
            "username"      => "required|min:2|max:25",
            "gender"        => "required|alpha",
            "password"      => "required|min:3|max:35"
        ];
        
        return $rules;
    }

    
}