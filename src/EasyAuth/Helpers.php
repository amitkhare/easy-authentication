<?php

namespace AmitKhare\EasyAuth;

class Helpers {
    
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
	
	public static function getClientData() {

        // generate token data
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		    $ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
		    $ip = $_SERVER['REMOTE_ADDR'];
		}
		
		$user_agent = (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : "Unknown";
		
		$referrer = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER']  : "Accessed directly";

		$session_data["IP address"]  = $ip;
		$session_data["Browser (User Agent)"]   = $user_agent;
		$session_data["Referrer:"] =  $referrer;
        
        
        $clientData = [
            'session_data'=>json_encode($session_data),
            'ip'=>$ip,
            'user_agent'=>$user_agent,
            'referrer'=>$referrer
        ];
        
        return $clientData;
        
    }
}