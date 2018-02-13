
<?php
//1. General settings
session_start();

//2. Including system files
define('ROOT', dirname(__FILE__));
require ROOT.'/vendor/autoload.php';

// Enabling errors handling
(new App\errors\ErrorHandler())->register();

//3. Calling the Router
(new App\components\Router())->run();
