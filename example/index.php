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

require __DIR__.'/CustomerAuth/Customer.php';
require __DIR__.'/CustomerAuth/CustomersRole.php';
require __DIR__.'/CustomerAuth/CustomersProfile.php';
require __DIR__.'/CustomerAuth/CustomersToken.php';


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
];

//$auth = new EasyAuthentication( new AdminAuth\Admin(), $rules );
$auth = new EasyAuthentication( new CustomerAuth\Customer(), $config );
//$auth = new EasyAuthentication();

$auth->register([]);
die;
// identifier = 'amit' password = 'pass'
$auth->login($_GET);

s($auth->getCurrentUser());
s($auth->getStorage()->token);

s($auth->isLoggedin());

$auth->logout(true);

s($auth->response->getErrors());
s($auth->response->getMessages());