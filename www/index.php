<?php
include_once __DIR__ . '/../php/bootstrap.php';

use \Neolao\Util\Json;

// General configuration
$configPath = CONFIG_PATH . '/general.json';
if (!is_readable($configPath)) {
    die('Please create the file: ' . $configPath);
}
$configContent  = file_get_contents($configPath);
$configContent  = Json::removeComments($configContent);
$configGeneral  = \ConfigGeneral::getInstance();
$configGeneral->parseJson($configContent);

// Site configuration
$configPath     = CONFIG_PATH . '/siteMain.json';
if (!is_readable($configPath)) {
    die('Please create the file: ' . $configPath);
}
$configContent  = file_get_contents($configPath);
$configContent  = Json::removeComments($configContent);
$configSite     = json_decode($configContent);

// Routes
$routesPath     = CONFIG_PATH . '/siteMainRoutes.json';
$routesContent  = file_get_contents($routesPath);
$routesContent  = Json::removeComments($routesContent);
$routes         = json_decode($routesContent);

// ACL: Create the controller helper
$aclHelper      = new \Neolao\Site\Helper\Controller\AclHelper();
$acl            = $aclHelper->acl;

// ACL: Add default resources, roles and rules
// @todo Easier configuration
$aclResourcesPath       = CONFIG_PATH . '/aclResources.json';
$aclResourcesContent    = file_get_contents($aclResourcesPath);
$aclResourcesContent    = Json::removeComments($aclResourcesContent);
$aclResources           = json_decode($aclResourcesContent);
$aclRolesPath           = CONFIG_PATH . '/aclRoles.json';
$aclRolesContent        = file_get_contents($aclRolesPath);
$aclRolesContent        = Json::removeComments($aclRolesContent);
$aclRoles               = json_decode($aclRolesContent);
$aclRulesPath           = CONFIG_PATH . '/aclRules.json';
$aclRulesContent        = file_get_contents($aclRulesPath);
$aclRulesContent        = Json::removeComments($aclRulesContent);
$aclRules               = json_decode($aclRulesContent);
foreach ($aclResources as $resourceName) {
    $acl->addResource($resourceName);
}
foreach ($aclRoles as $roleName => $parentName) {
    $acl->addRole($roleName, $parentName);
}
foreach ($aclRules as $rule) {
    $ruleType       = $rule->type;
    $ruleRole       = $rule->role;
    $ruleResource   = $rule->resource;
    $rulePrivilege  = $rule->privilege;
    if ($ruleType === 'deny') {
        $acl->deny($ruleRole, $ruleResource, $rulePrivilege);
    } else {
        $acl->allow($ruleRole, $ruleResource, $rulePrivilege);
    }
}

// ACL: Apply custom rules
// @todo


// Get the theme
$themeName = 'default';
if (isset($configSite->theme)) {
    $themeName = $configSite->theme;
}
$themePath = ROOT_PATH . '/www/themes/' . $themeName;


// Initialize and run the site
$site                       = new \Site\Main();
$site->serverName           = $configSite->server->name;
$site->controllersPath      = PHP_PATH . '/sites/main/controllers';
$site->viewsPath            = $themePath . '/views';
$site->viewRenderer         = new \Site\View\Mustache();
$site->localesPath          = ROOT_PATH . '/locales';
$site->localeString         = 'fr_FR';
$site->themePath            = $themePath;
$site->themeUrl             = '/themes/' . $themeName;
$site->themeGenerated       = $configGeneral->themeGenerated;
$site->configureRouter($routes);
$site->addControllerHelper('getAcl', $aclHelper);
$site->run();

