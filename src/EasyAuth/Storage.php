<?php

namespace AmitKhare\EasyAuth;

use Countable;
use AmitKhare\EasySession;

class Storage extends EasySession implements Countable
{

    private $authKey = "AUTHDATA";

    public function __construct($bucket='default') {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        parent::__construct($bucket);
    
    }
    
    public function getData() {
        return $this->get($this->authKey);
    }
    
    public function setData($value) {
        return $this->set($this->authKey,$value);
    }
    
    public function clearData() {
        return $this->remove($this->authKey);
    }
    
}