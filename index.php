<?php
require 'defines.php';
require 'lib'.DIRECTORY_SEPARATOR.'DB.php';

// Setup the autoloader(s) for newer classes
require_once 'lib/Autoloader.php';

// PSR-0 compliant classes
$autoloaderPSR = new Autoloader('', dirname(__FILE__).DIRECTORY_SEPARATOR.'lib');
$autoloaderPSR->register();

define('DIR_VIEWS', dirname(__FILE__).DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR);

if (isset($_GET['c'])) {
    $controller = $_GET['c'];
}else{
    $controller = 'AddEmails';
}

if (isset($_GET['a'])) {
    $action = $_GET['a'];
}else{
    $action = 'index';
}

$controller = Common::safeString($controller);
$action = Common::safeString($action);

$controllerFile = 'controller'.DIRECTORY_SEPARATOR.$controller.'.php';

/** @noinspection PhpIncludeInspection */
require $controllerFile;

$c = new $controller();

$c->$action();




