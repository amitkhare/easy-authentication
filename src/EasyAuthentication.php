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

        $mail = $this->mailer->to("amit@khare.co.in")
            ->subject("email subject")
            ->body("this is body");
        d($mail->send());
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