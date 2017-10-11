<?php
namespace AmitKhare\EasyAuth;
/*

########## SAMPLE CONFIG ARRAY....
$config = [
    "smtp" => [
        "server" => "smtp.gmail.com",
        "port" => 25,
        "username" => "amit@khare.co.in",
        "password" => "pword"
    ],

    "sender" => [
        "email" => "john@doe.com",
        "name" => "John Doe",
    ],
    
];

*/


class Mailer {
    
    protected $config;
    protected $sender;
    protected $mailer;
    protected $message;
    
    public function __construct(array $config){
        $config = $this->setConfig($config);
        // Create the Transport
        $this->sender = $config['sender'];
        
        // sendmail transport
        //$transport = new Swift_SendmailTransport('/usr/sbin/sendmail -bs');
        
        $transport = (new \Swift_SmtpTransport($config['smtp']['server'], $config['smtp']['port']))
          ->setUsername($config['smtp']['username'])
          ->setPassword($config['smtp']['password'])
        ;
        
        // Create the Mailer using your created Transport
        $this->mailer = new \Swift_Mailer($transport);
   
    }
    
    private function setConfig(array $config = []){
        
        $config["smtp"] = (isset($config["smtp"])) ? $config["smtp"] : ["server"=> "smtp.google.com", "port"=>25,"username"=>null,"password"=>null]; 
        $config["sender"] = (isset($config["sender"])) ? $config["sender"] : ["email"=>"no-reply@".$_SERVER['HTTP_HOST'],"name"=>"Web Master"]; 
        
        return $this->config = $config;
    }
    
    public function to($email,$name="User"){
        $this->to["email"] = $email;
        $this->to["name"] = $name;

        return $this;
    }
    public function subject($subject){
        $this->subject = $subject;
        return $this;
    }
    public function body($body){
        $this->body = $body;
        return $this;
    }

    public function send(){

        $message = (new \Swift_Message($this->subject))
          ->setFrom([ $this->sender['email'] => $this->sender['name'] ])
          ->setTo([ $this->to["email"] => $this->to["name"] ])
          ->setBody($this->body);
        
        return $this->mailer->send($message);

    }
    
}
