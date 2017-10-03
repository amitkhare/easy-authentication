<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as Capsule;

use AmitKhare\EasyAuthentication;

// autoload via composer
require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/Models.php';

$capsule = new Capsule;

$capsule->addConnection([
    
    'driver'=>'mysql',
    'host'=>'localhost',
    'database'=>'slim_modular',
    'username'=>'amit',
    'password'=>'amit',
    'charset'=>'utf8',
    'collation'=>'utf8_unicode_ci',
    'prefix'=>'',
    'strict' => false
    
]);

$capsule->setAsGlobal();

$capsule->bootEloquent();


$auth = new EasyAuthentication( new User() );



$auth->login($_GET);
//$auth->logout(true);
s($auth->response->getErrors());
s($auth->response->getMessages());
