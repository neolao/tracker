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


// Autoload classes
function defaultAutoload($className)
{
    $className = ltrim($className, '\\');
    $filePath  = '';
    $lastNamespacePosition = strripos($className, '\\');
    if ($lastNamespacePosition) {
        $namespace = substr($className, 0, $lastNamespacePosition);
        $className = substr($className, $lastNamespacePosition + 1);
        $filePath  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace).DIRECTORY_SEPARATOR;
    }
    $filePath .= str_replace('_', DIRECTORY_SEPARATOR, $className).'.php';

    require_once $filePath;
}
spl_autoload_register('defaultAutoload');
set_include_path(get_include_path().PATH_SEPARATOR.LIB_PATH.PATH_SEPARATOR.APPLICATION_PATH);


