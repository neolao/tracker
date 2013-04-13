<?php
namespace Site\Helper\Controller;

use \Neolao\Site\Helper\Controller\AbstractHelper;
use \Neolao\Util\Json;
use \Neolao\Site\Router;

/**
 * Get a link to a page of the main website
 */
class LinkMainHelper extends AbstractHelper
{
    /**
     * Router
     *
     * @var \Neolao\Site\Router
     */
    protected $_router;

    /**
     * Server name
     *
     * @var string
     */
    protected $_serverName;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Configuration of the website
        $configPath     = CONFIG_PATH . '/siteMain.json';
        $configContent  = file_get_contents($configPath);
        $configContent  = Json::removeComments($configContent);
        $configSite     = json_decode($configContent);

        // Routes of the website
        $routesPath     = CONFIG_PATH . '/siteMainRoutes.json';
        $routesContent  = file_get_contents($routesPath);
        $routesContent  = Json::removeComments($routesContent);
        $routes         = json_decode($routesContent);

        // Get the server name
        $this->_serverName = $configSite->server->name;

        // Configure the router
        $this->_router  = new Router();
        $this->_router->configure($routes);
    }

    /**
     * Get the reverse URL of a route
     *
     * @param   string  $routeName      The route name
     * @param   array   $parameters     The route parameters
     * @return  string                  The reverse URL
     */
    public function main($routeName, $parameters = [])
    {
        return $this->reverse($routeName, $parameters);
    }

    /**
     * Get the reverse URL of a route
     *
     * @param   string  $routeName      The route name
     * @param   array   $parameters     The route parameters
     * @return  string                  The reverse URL
     */
    public function reverse($routeName, $parameters = [])
    {
        $url  = 'http://' . $this->_serverName;
        $url .= $this->_router->reverse($routeName, $parameters);
        return $url;
    }

}
