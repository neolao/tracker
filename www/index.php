<?php
include_once __DIR__ . '/../php/bootstrap.php';

// Configuration
$configPath = CONFIG_PATH.'/siteMain.json';
if (!is_readable($configPath)) {
    die('Please create the file: ' . CONFIG_PATH . '/siteMain.json');
}
$configContent = file_get_contents($configPath);
$config = json_decode($configContent);

// Routes
$routesPath     = CONFIG_PATH . '/siteMainRoutes.json';
$routesContent  = file_get_contents($routesPath);
$routes         = json_decode($routesContent);

// ACL: Create the controller helper
$aclHelper      = new \Neolao\Site\Helper\Controller\AclHelper();
$acl            = $aclHelper->acl;

// ACL: Add default resources, roles and rules
$aclResourcesPath       = CONFIG_PATH . '/aclResources.json';
$aclResourcesContent    = file_get_contents($aclResourcesPath);
$aclResources           = json_decode($aclResourcesContent);
$aclRolesPath           = CONFIG_PATH . '/aclRoles.json';
$aclRolesContent        = file_get_contents($aclRolesPath);
$aclRoles               = json_decode($aclRolesContent);
$aclRulesPath           = CONFIG_PATH . '/aclRules.json';
$aclRulesContent        = file_get_contents($aclRulesPath);
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


// Get the theme
$themeName = 'default';
if (isset($config->theme)) {
    $themeName = $config->theme;
}
$themePath = ROOT_PATH.'/www/themes/' . $themeName;

// Theme helper
$stylesheetHelper = new \Neolao\Site\Helper\View\StylesheetHelper();
$stylesheetHelper->basePath = $themePath;
$stylesheetHelper->baseUrl = '/themes/' . $themeName;


// Initialize and run the site
$site                       = new \Site\Main();
$site->serverName           = $config->server->name;
$site->controllersPath      = PHP_PATH . '/sites/main/controllers';
$site->viewsPath            = $themePath . '/views';
$site->viewRenderer         = new \Site\View\Mustache();
$site->configureRoutes($routes);
$site->addControllerHelper('getAcl', $aclHelper);
$site->addViewHelper('stylesheetsPath', $stylesheetHelper);
$site->run();

