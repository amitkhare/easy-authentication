<?php

namespace AmitKhare\EasyAuth;

use AmitKhare\EasyTranslator;

class Response {
    
    protected $errors=[];
    protected $messages=[];
    protected $translator;
    
    
     public function __construct($locale="hi-IN",$localePath=__DIR__."/locales/"){
        $this->translator = new EasyTranslator();
        $this->translator->setLocalePath($localePath);
        $this->translator->setLocale($locale); 
    }
    
    public function getErrors(){
        return $this->errors;
    }
    
    public function getMessages(){
        return $this->messages;
    }
    
    public function setErrors($errors = [],$type="danger"){
        
        $error["code"] = $errors['code'];
        $error["errs"] = $errors['msgs'];
    
        $error["type"] = strtolower($type);;
        
        $this->errors = $error;
    }
    
    
    public function setMessage($code=500,$msg,$type="info"){

        $message["code"] = $code;
        $message["msgs"] = $this->translator->translate($msg);
        $message["type"] = strtolower($type);
        $this->messages[] = $message;
    }
    
}