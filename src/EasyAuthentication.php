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
    
    public function resetPassword($data){

        // perform validation here
        
        $user = $this->user->where('email','=',$data['email'])->first();

        if(!$user){
            return false;
        }
        
        $user->recover_hash = Helper::randomKey();
        
        if(!$user->save()){
            return false;
        }

        $user->recover_link = $this->config['uri']['base'].$this->config['uri']['forgot_password'].$user->recover_hash;
        
        $this->mailer->to($user->email,$user->profile->firstname)
            ->subject($this->response->t("RESET_PASSWORD_SUBJECT",$user->profile->firstname))
            ->body($this->response->t("RESET_PASSWORD_BODY",[$user->profile->firstname,$user->recover_link ],false))
            ->send();

    	$this->response->setMessage(200,"USER_PASSWORD_RESET_LINK_SENT","success");
        return true;
        
    }
    
    public function recover($hash, $data){

        $user = $this->user->where('recover_hash','=',$hash)->first();

        if(!$user){
            return false;
        }
        
        // apply validation here
        
        $user->recover_hash = null;
        $user->password = $this->hasher->password($data['password']);
        
        if(!$user->save()){
            return false;
        }
        
        return true;
        
    }
    
    public function updatePassword($data){
        
        if(!$user = $this->getCurrentUser()){
            return false;
        }
        
        // apply validation here
        
        if(!Helpers::verify(trim($data['password']), $user->password )){
            // send respose password invalid
            return false;
        }
        
        $data['new_password'] = trim($data['new_password']);
        
        $user->recover_hash = null;
        $user->password = $this->hasher->password($data['new_password']);
        
        if(!$user->save()){
            // can not change password
            return false;
        }
        
        $user->raw_password = $data['new_password'];
        
        $this->mailer->to($user->email,$user->profile->firstname)
            ->subject($this->response->t("UPDATE_PASSWORD_SUBJECT",$user->profile->firstname))
            ->body($this->response->t("UPDATE_PASSWORD_BODY",[$user->profile->firstname,$user->raw_password ],false))
            ->send();
        
        return true;

    }
    
    
    // this dont work, set email update hash, set active to inactive
    public function updateEmail($data){
        
        if(!$user = $this->getCurrentUser()){
            return false;
        }
        
        // apply validation here
        
        if(!Helpers::verify(trim($data['password']), $user->password )){
            // send respose password invalid
            return false;
        }
        
        $data['new_email'] = trim($data['new_email']);
        
        
        $user->new_email = $data['new_email'];
        $user->is_active = 0;
    	$user->email_verification_hash = Helpers::randomKey(30);
      
        if(!$user->save()){
            // can not change password
            return false;
        }
        
        $user->update_email_link = $this->config['uri']['base'].$this->config['uri']['update_email'].$user->email_verification_hash;
        
        // email to new email address
        $this->mailer->to($user->email,$user->profile->firstname)
            ->subject($this->response->t("UPDATE_EMAIL_SUBJECT",$user->profile->firstname))
            ->body($this->response->t("UPDATE_EMAIL_BODY",[$user->profile->firstname, $user->update_email_link ],false))
            ->send();
        
        return true;

    }
    
    public function is_recover($hash) {
        $user = $this->user->where('recover_hash','=',$hash)->first();
        return ($user->recover_hash==null) ? false : true;
    }
    
    public function is_active($hash){
        $user = $this->user->where('activation_hash','=',$hash)->first();
        return ($user->activation_hash==null) ? false : true;
    }
    
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
        
        
        // assign roles, first role from roles table
        $user->roles()->attach(1);
        
        $user->email_verification_link = $this->config['uri']['base'].$this->config['uri']['verify_email'].$user->email_verification_hash;
        
        $this->response->setMessage(201,"USER_REGISTRATION_SUCCESS","success");
 
        $this->mailer->to($user->email,$user->profile->firstname)
            ->subject($this->response->t("VERIFY_EMAIL_SUBJECT",$user->profile->firstname))
            ->body($this->response->t("VERIFY_EMAIL_BODY",[$user->profile->firstname,$user->email_verification_link],false))
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
        
        $this->mailer->to($user->email,$user->profile->firstname)
            ->subject($this->response->t("VERIFY_EMAIL_SUBJECT_SUCCESS",$user->profile->firstname))
            ->body($this->response->t("VERIFY_EMAIL_BODY_SUCCESS",$user->profile->firstname))
            ->send();
            
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