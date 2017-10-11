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
 
use AmitKhare\EasyAuth\Helpers;

class EasyAuthentication extends \AmitKhare\EasyAuthenticationBase {

    
    public function register(array $data){
        
        $v = $this->validation;
        $v->setSource($data);

        $v->check("firstname", $this->vRules->r("firstname") );
        $v->check("email", $this->vRules->r("email") );
        $v->check("password", $this->vRules->r("password") );
        $v->check("gender", $this->vRules->r("gender") );
        
        if(!$v->isValid()){
            $this->response->setErrors($v->getStatus(),"danger");
            return false;
        }

        if ($_usr = $this->user->where('email', '=', trim($data['email']) )->first()) {
			
        	if(!Helpers::verify(trim($data['password']),$_usr->password)){
        	    $this->response->setMessage(500,"USER_EMAIL_EXIESTS","warning");
	            return false;
        	}
            
            $this->response->setMessage(500,"USER_ALREADY_REGISTERED_LOGIN","danger");
            return false;

        }
        
        $user = $this->user->firstOrCreate([
            'email'=>trim($data['email']),
            'password'=>Helpers::password(trim($data['password'])),
            'email_verification_hash'=>Helpers::randomKey(30)
        ]);
        
        if(!$user){
            $this->response->setMessage(500,"USER_REGISTRATION_FAILED","danger");
            return false;
        }
        
        $user->profile()->create([
            'firstname'=>trim($data['firstname']),
            'gender'=>trim($data['gender'])
        ]);
        
        $user->email_verification_link = $this->config['uri']['base'].$this->config['uri']['verify_email'].$user->email_verification_hash;
        
        $this->response->setMessage(201,"USER_REGISTRATION_SUCCESS","success");
 
        $this->mailer->to($user->email,$user->profile->firstname)
            ->subject($this->response->t("VERIFY_EMAIL_SUBJECT",$user->profile->firstname))
            ->body($this->response->t("VERIFY_EMAIL_BODY",$user->email_verification_link,false))
            ->send();
            
        return $user->id;
    }
    
    public function verifyEmail($hash){
    	$hash = trim($hash);
    	if(!$user = $this->user->where(['email_verification_hash'=>$hash])->first()){
    		// invalid hash
    		$this->response->setMessage(404,"USER_ACTIVATION_HASH_INVALID","info");
    		return false;
    	}
    	
    	if($user->is_active){
    		// already activated
    		
    		$user->email_verification_hash = null;
    		$user->password_recovery_hash = null;
    		$user->save();
    	
    		$this->response->setMessage(404,"USER_ALREADY_ACTIVATED","info");
    		return true;
    	}
    	
    	$user->is_active = 1;
    	$user->email_verification_hash = null;
    	$user->password_recovery_hash = null;

    	if(!$user->save()){
	    	// unable to activate
	    	$this->response->setMessage(500,"USER_ACTIVATION_FAILED","danger");
	    	return false;
    	}
    	
    	// user ACTIVATION success
    	$this->response->setMessage(200,"USER_ACTIVATION_SUCCESS","success");

    	return $user->id;
    	
    }
    
    public function login(array $data){

        $v = $this->validation;
        $v->setSource($data);

        $v->check("identifier", $this->vRules->r("identifier") );
        $v->check("password", $this->vRules->r("password") );
    
        if(!$v->isValid()){
            $this->response->setErrors($v->getStatus(),"danger");
            return false;
        }
        
        if(!$user = $this->_fetchUser($data)){
            // no user found no need to set response, already set.
            return false;
        }
        
        // varified
        if(!$token= $this->_createToken($user)) {
        	// unable to create token
        	$this->response->setMessage(500,"TOKEN_CREATION_FAILED","danger");
            return false;	
        }
        
    	$this->storage->setData(["token"=>$token->token]);
        
        $this->response->setMessage(200,"USER_LOGGED_IN","success");
        
        return true;
    }
}