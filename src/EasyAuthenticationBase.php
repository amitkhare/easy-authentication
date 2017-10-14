<?php
namespace AmitKhare;

/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */
/**
 * Validbit is an easy to use PHP authentication library.
 *
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link https://github.com/amitkhare/easy-authentication
 * @author Amit Kumar Khare <me.amitkhare@gmail.com>
 * 
 * SAMPLE CONFIG -------------
 * 
 * $config = [
        "validation_rules" => [
            "identifier"  => "required|min:2|max:25",
            "password"  => "required|min:4|max:35"
        ],
        "mailer" => [
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
        ],
        "storage_name" => "AUTH",
        "locale"=> "en-IN",
        "locale_path" => __DIR__."/locales/",
    ];
 * 
 * 
 */

use Illuminate\Database\Eloquent\Model as Eloquent;

use AmitKhare\EasyValidation;

use AmitKhare\EasyAuth\Helpers;
use AmitKhare\EasyAuth\Response;
use AmitKhare\EasyAuth\Storage;
use AmitKhare\EasyAuth\Mailer;
use AmitKhare\EasyAuth\ValidationRules;
use AmitKhare\EasyAuth\UserInterface;


class EasyAuthenticationBase  {
    
    protected $config;
    protected $storage;
    protected $mailer;
    protected $validation;
    protected $translator;
    protected $user;
    protected $token;
    protected $role;
    protected $vRules;
    public $response;
    
    public function __construct(
                        array $config = null,
                        UserInterface $user = null
                        ) {

        // set config
        $this->_setConfig($config);
        
        if($user == null){
           $user = new \AmitKhare\EasyAuth\Models\User();
        }

        $this->user = $user;
        $this->token = $this->user->tokens()->make()->newInstance();
        $this->role = $this->user->roles()->make()->newInstance();
        
        $this->validation = new EasyValidation();
        $this->validation->setLocale($this->config['locale'],$this->config['locale_path']); 

        $this->response = new Response($this->config['locale'],$this->config['locale_path']);
        
        $this->vRules = new ValidationRules($config['validation_rules']);
        
        // set auth storage name
        $refl = new \ReflectionClass($user);
        $userClassName = $refl->getShortName();
        $this->storage = new Storage(strtoupper($this->config['storage_name']."_".$userClassName));
   
   
        $this->mailer = new Mailer($config["mailer"]);

    }
    
    
    public function isLoggedin() {
        if(!$data = $this->getStorage()){
            // not loggedin
            return false;
        }
        
        if(!$this->getToken($data->token)){
            // invalid token
            return false;
        }
        
        return $data->token;
    }
    
    public function check($userRole) {

        if(!$token = $this->isLoggedin()){
            // invalid token
            return false;
        }

        if(!$this->hasRole($token,$userRole)){
            // no role
            return false;
        }
        
        return true;

    }
    
    public function getCurrentUser(){
        $token = $this->getStorage()->token;
        if(!$token = $this->getToken($token) ){
           return false;
        }
        $user = $token->user;
        return $user;
    }
    
    public function getUser($token){
        if(!$token = $this->getToken($token) ){
           return false;
        }
        $user = $token->user;
        return $user;
    }
    
    public function getRoles($token){
        if(!$token = $this->getToken($token) ){
           return false;
        }
        $user = $token->user;
        
        return $user->roles;
    }
    
    public function hasRole($token,$role){
        
        $roles = $this->getRoles($token);
        
        foreach ($roles as $r) {
            if($r->role == $role){
                return true;
            }
        }
        
        return false;
        
    }
    
    public function getToken($token){
        
        if(!$token = $this->token->where(["token"=>$token,"is_active"=>1])->first() ){
           return false;
        }
        return $token;
    }
    
    public function logout($tokenStr=null,$everywhere=false){
        
        // clear session
        
        if(!$tokenStr){
            if(!$tokenStr = $this->getStorage()->token){
                return false;
            }
        }
        
        if($everywhere){
            $this->removeTokens($tokenStr);
            $this->response->setMessage(200,"USER_LOGGED_OUT_EVERYWHERE","success");
        } else if($token = $this->getToken($tokenStr)){
            $token->delete();
            $this->response->setMessage(200,"USER_LOGGED_OUT","success");
        }
        
        
    	return $this->storage->clearData();
    }
    
    protected function removeToken($token){

        if(!$token = $this->getToken($token)){
            $this->response->setMessage(401,"TOKEN_INVALID","warning");
            return false;
        }
        
        $this->response->setMessage(200,"TOKEN_REMOVED","success");
        return $token->delete();
    }
    
    public function is_recover($hash) {
        $user = $this->user->where('password_recovery_hash','=',$hash)->first();
        return ($user->password_recovery_hash==null) ? false : true;
    }
    
    public function is_active($hash){
        $user = $this->user->where('activation_hash','=',$hash)->first();
        return ($user->activation_hash==null) ? false : true;
    }
    
    protected function removeTokens($token){

        if(!$user = $this->getUser($token)){
            $this->response->setMessage(401,"TOKEN_INVALID","warning");
            return false;
        }
    
        foreach ($user->tokens as $t) {
            $t->delete();
        }
        $this->response->setMessage(200,"TOKENS_REMOVED","success");
        return true;
    }
    
    public function getStorage(){
        $storage = $this->storage->getData();
        $storage['token'] = ($storage['token']) ? $storage['token'] : null;
        return (object) $storage;
    }
    
    protected function _createToken($user) {
        $user->allowed_tokens = ($user->allowed_tokens) ? $user->allowed_tokens : 3;
        
        // delete old and extra tokens, this will limit token creation
		//$tokens = $this->token->where(['user_id'=>$user->id]);
		$tokens = $user->tokens();
		
		$token_count = count($tokens->get());
		if($token_count >= $user->allowed_tokens){
			for ($i = $token_count; $i >= $user->allowed_tokens; $i--) {
				 if($tokens->first()){
				 	$tokens->first()->delete();
				 }
			}
		}
   
        $clientData = Helpers::getClientData();
        
        $token = $user->tokens()->create([
			'user_id'=>$user->id,
			'token'=>Helpers::randomKey(60,false),
            'session_data'=>$clientData['session_data'],
            'user_agent'=>$clientData['user_agent'],
            'referrer'=>$clientData['referrer'],
            'ip'=>$clientData['ip'],
            'is_active'=>1
        ]);
        
        return $token;
        
    }
    
    protected function _fetchUser(array $data) {
        
        $identifier = trim($data['identifier']);
        $password = trim($data['password']);
       
        $user = $this->user->where('email','=',$identifier)->orWhere('username', '=', $identifier)->first();
        
        if(!$user){
        	// user not found
        	$this->response->setMessage(403,"USER_NOT_FOUND","info");
            return false;
        }
        
        if(!Helpers::verify($password,$user->password)){
        	// password missmatch
        	$this->response->setMessage(403,"INVALID_PASSWORD","info");
        	return false;
        }
        
        // clear forgot password hash
        if($user->password_recovery_hash){
            $user->password_recovery_hash=null;
            $user->save();
        }
        
        return $user;
    }
    
    private function _setConfig(array $config = []){
        
        $config["storage_name"] = (isset($config["storage_name"])) ? $config["storage_name"] : "AUTH"; 
        $config["locale"] = (isset($config["locale"])) ? $config["locale"] : "en-IN"; 
        $config["locale_path"] = (isset($config["locale_path"])) ? $config["locale_path"] : __DIR__."/locales/"; 
        $config["validation_rules"] = (isset($config["validation_rules"])) ? $config["validation_rules"] : []; 
        $config["mailer"] = (isset($config["mailer"])) ? $config["mailer"] : null; 
        
        return $this->config = $config;
    }
}