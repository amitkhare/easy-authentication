<?php

namespace AmitKhare;

class EasyHasher {
    
    public static function password($password){
        $options = [
            'cost' => 10,
        ];
        return password_hash($password, PASSWORD_BCRYPT, $options);
    }
    
    public static function verify($password, $hash){
    
       return password_verify($password, $hash);
       
    }
    
    public static function randomKey($length = 30,$strong=false) {
		$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
		
		if($strong){
			$chars .= "@$*.!_-";
		}
		
		$key = "";
		
		for($i = 0; $i < $length; $i++)
		{
			$key .= $chars{rand(0, strlen($chars) - 1)};
		}
		
		return $key;
	}
	
	public static function randomDigit($length = 30) {
		$chars = "1234567890";

		$key = "";
		
		for($i = 0; $i < $length; $i++)
		{
			$key .= $chars{rand(0, strlen($chars) - 1)};
		}
		
		return $key;
	}
}