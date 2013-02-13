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

// Get the theme
$theme = 'default';
if (isset($config->theme)) {
    $theme = $config->theme;
}

// Initialize and run the site
$site                       = new \Site\Main();
$site->serverName           = $config->server->name;
$site->controllersPath      = PHP_PATH.'/sites/main/controllers';
$site->viewsPath            = ROOT_PATH.'/www/themes/'.$theme.'/views';
$site->viewRenderer         = new \Site\View\Mustache();
$site->configureRoutes($routes);
$site->run();

