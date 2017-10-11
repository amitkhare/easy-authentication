<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as Capsule;

use AmitKhare\EasyAuthentication;

// autoload via composer
require __DIR__.'/../vendor/autoload.php';

require __DIR__.'/AdminAuth/Admin.php';
require __DIR__.'/AdminAuth/AdminsRole.php';
require __DIR__.'/AdminAuth/AdminsProfile.php';
require __DIR__.'/AdminAuth/AdminsToken.php';


// load .env helper
try {
    ($dotenv = new Dotenv\Dotenv(__DIR__ . '/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

$capsule = new Capsule;

$capsule->addConnection([
    
    'driver'=>getenv('DB_DRIVER'),
    'port'=>getenv('DB_PORT'),
    'host'=>getenv('DB_HOST'),
    'database'=>getenv('DB_NAME'),
    'username'=>getenv('DB_USER'),
    'password'=>getenv('DB_PASS'),
    'charset'=>'utf8',
    'collation'=>'utf8_unicode_ci',
    'prefix'=>'',
    'strict' => false
    
]);

$capsule->setAsGlobal();

$capsule->bootEloquent();


$config = [
    "validation_rules" => [
        "identifier"  => "required|min:2|max:25",
        "password"  => "required|min:4|max:35"
    ],
    'mailer' => [
        "smtp"=>[
            'server' => getenv('MAIL_HOST'),
            'port' => getenv('MAIL_PORT'),
            'username' => getenv('MAIL_USERNAME'),
            'password' => getenv('MAIL_PASSWORD'),
            "sendmail" => getenv('MAIL_SENDMAIL_PATH'),
        ],
        'sender' => [
            'email' => getenv('MAIL_FROM_ADDRESS'),
            'name' => getenv('MAIL_FROM_NAME')
        ],
    ],
    "uri" => [
        "base"=>getenv('APP_BASEURL'),
        "verify_email" => "?verifyemail="  // use $app->router here
    ],
];

$auth = new EasyAuthentication( $config, new AdminAuth\Admin() );
//$auth = new EasyAuthentication($config);

if(isset($_GET['verify_email'])){
    $auth->verifyEmail(trim($_GET['verify_email']));
} else {
    $auth->register($_GET);
}

s($auth->response->getErrors());
s($auth->response->getMessages());
die;
// identifier = 'amit' password = 'pass'
$auth->login($_GET);

s($auth->getCurrentUser());
s($auth->getStorage()->token);

s($auth->isLoggedin());

$auth->logout(true);

s($auth->response->getErrors());
s($auth->response->getMessages());