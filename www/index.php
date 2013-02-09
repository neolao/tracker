<?php
include_once __DIR__.'/../php/bootstrap.php';

// Configuration
$configPath = CONFIG_PATH.'/siteMain.json';
if (!is_readable($configPath)) {
    die('Please create the file: '.CONFIG_PATH.'/siteMain.json');
}
$configContent = file_get_contents($configPath);
$config = json_decode($configContent);

// Routes
$routesPath = CONFIG_PATH.'/siteMainRoutes.json';
if (!is_readable($routesPath)) {
    die('Please create the file: '.CONFIG_PATH.'/siteMainRoutes.json');
}
$routesContent = file_get_contents($routesPath);
$routes = json_decode($routesContent);


// Initialize and run the site
$viewRenderer = new \Site\View\Mustache();
$site = new \Site\Main();
$site->setServerName($config->server->name);
$site->setControllersPath(PHP_PATH.'/sites/main/controllers');
$site->setViewsPath(ROOT_PATH.'/www/themes/default/views');
$site->setViewRenderer($viewRenderer);
$site->setRoutes($routes);
$site->run();

