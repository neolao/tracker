<?php
// Check PHP version
if (version_compare(PHP_VERSION, '5.4.0') < 0) {
    die('You need PHP version 5.4.0 or above. Your version is '.PHP_VERSION.'.');
}

// PHP configuration
ini_set('html_errors', 'Off');

// Constants
define('PHP_PATH',          __DIR__);
define('ROOT_PATH',         realpath(__DIR__.'/..'));
define('APPLICATION_PATH',  PHP_PATH.'/application');
define('CONFIG_PATH',       ROOT_PATH.'/config');
define('LIB_PATH',          PHP_PATH.'/lib');


// Composer autoload
require PHP_PATH.'/vendor/autoload.php';


// Initialize the logger
use \Neolao\Logger;
use \Neolao\Logger\FileListener;
$logger = Logger::getInstance();
$fileListener = new FileListener(ROOT_PATH.'/logs/debug.log', Logger::DEBUG);
$logger->addListener($fileListener);
$fileListener = new FileListener(ROOT_PATH.'/logs/error.log', Logger::ERROR);
$logger->addListener($fileListener);


// Set the default error handler
function defaultErrorHandler($level, $message, $file, $line)
{
    $output = "$message ($file:$line)";
    throw new \Exception($output);
}
function defaultExceptionHandler($exception)
{
    if (ob_get_length()) {
        ob_end_clean();
    }
    
    // Report the error
    $logger = Logger::getInstance();
    $logger->error($exception->getMessage());
    $logger->error($exception->getTraceAsString());
    
    // If the header is already sent, do not display a message
    if (headers_sent()) {
        exit;
    }
    
    // Display a message
    header('HTTP/1.0 500 Internal Error');
    echo 'An error occured !';
    if (ob_get_length()) {
        ob_end_flush();
    }
    exit;
}
set_error_handler('defaultErrorHandler');
set_exception_handler('defaultExceptionHandler');


