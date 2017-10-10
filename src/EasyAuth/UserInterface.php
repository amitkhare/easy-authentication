<?php

namespace AmitKhare\EasyAuth;


interface UserInterface {
    
    public function tokens();
    
    public function roles();
    
    public function profile();

}