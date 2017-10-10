<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as Capsule;

use AmitKhare\EasyAuthentication;

// autoload via composer
require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/CustomerModel.php';
require __DIR__.'/AdminModel.php';

$capsule = new Capsule;

$capsule->addConnection([
    
    'driver'=>'mysql',
    'host'=>'localhost',
    'database'=>'easy_auth',
    'username'=>'amit',
    'password'=>'amit',
    'charset'=>'utf8',
    'collation'=>'utf8_unicode_ci',
    'prefix'=>'',
    'strict' => false
    
]);

$capsule->setAsGlobal();

$capsule->bootEloquent();


$rules = [
    "identifier"  => "required|min:2|max:25",
    "password"  => "required|min:4|max:35"
];

//$auth = new EasyAuthentication( new Admin\User(), $rules );
$auth = new EasyAuthentication();

// identifier = 'amit' password = 'pass'
$auth->login($_GET);

s($auth->getCurrentUser());
s($auth->getStorage()->token);

s($auth->isLoggedin());

$auth->logout(true);

s($auth->response->getErrors());
s($auth->response->getMessages());