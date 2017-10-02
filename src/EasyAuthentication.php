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
 */

use Illuminate\Database\Eloquent\Model;

use AmitKhare\EasyUserModelInterface;

use AmitKhare\EasySession;
use AmitKhare\EasyValidation;
use AmitKhare\EasyTranslator;
use AmitKhare\EasyHasher;

class EasyAuthentication  {
    
    protected $session;
    protected $validator;
    protected $translator;
    protected $user;
    
    public function __construct(EasyUserModelInterface $user){

        $this->session = new EasySession("AUTH");
        $this->validator = new EasyValidation();
        $this->translator = new EasyTranslator();
        
        $this->user = $user;
    }
    
    public function login(string $identifier, string $password){
        
        $identifier = trim($identifier);
        $password = trim($password);
       
        $user = $this->user->where('email','=',$identifier)->orWhere('username', '=', $identifier)->first();
        
        if(!$user){
        	// user not found
        	s("user not found");
            return false;
        }
        
        if(!EasyHasher::verify($password,$user->password)){
        	// password missmatch
        	s("password missmatch");
        	return false;
        }
        // varified
        d($this->_createToken($user));
    }

    public function register(array $data){
        
    }
    
    public function logout(){
        // clear session
    	$this->session->clear();
    }
    
    private function _createToken($user){
        
        // delete old and extra tokens, this will limit token creation
		$tokens = $user->tokens()->where(['user_id'=>$user->id]);
		$token_count = count($tokens->get());
		if($token_count >= $user->allowed_tokens){
			for ($i = $token_count; $i >= $user->allowed_tokens; $i--) {
				 if($tokens->first()){
				 	$tokens->first()->delete();
				 }
			}
		}
   

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
        
        
        $token = $user->tokens()->create([
			'user_id'=>$user->id,
			'token'=>EasyHasher::randomKey(60,true),
            'session_data'=>json_encode($session_data),
            'ip'=>$ip,
            'user_agent'=>$user_agent,
            'referrer'=>$referrer,
            'ip'=>$ip,
            'is_active'=>1
        ]);
        
        return $token;
        
    }
    
    private function _setAuthData($token,$user){
        $data['token'] = $token->token;
		$data['user'] = [
	        "id"=> $user->id,
	        "is_active"=> $user->is_active,
	        "email"=> $user->email,
	        "username"=> $user->username,
	        "fullname"=> $user->profile->fullname()
	    ];
    	$this->storage->setAuthData($data);
    	return $data;
    }
    
   
}