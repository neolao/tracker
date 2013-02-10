<?php
/**
 * @package Neolao
 */
namespace Neolao;

/**
 * Site instance
 */
class Site
{
    use \Neolao\Mixin\GetterSetter;

    /**
     * The server name
     * 
     * @var string
     */
    protected $_serverName;
    
    /**
     * Site base URL
     *
     * @var string
     */
    protected $_baseUrl;
    
    /**
     * Controllers path
     * 
     * @var string
     */
    protected $_controllersPath;
    
    /**
     * Views path
     * 
     * @var string
     */
    protected $_viewsPath;

    /**
     * HTTP Request
     * 
     * @var \Neolao\Site\Request
     */
    protected $_request;
    
    /**
     * View instance
     * 
     * @var \Neolao\Site\View
     */
    protected $_view;
    
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        // Get the base url
        $this->_baseUrl = \Neolao\Util\Path::getBaseUrl();

        // Create a request
        $this->_request = new \Neolao\Site\Request();
        
        // Create a default view renderer
        $this->_view = new \Neolao\Site\View();
        $this->_view->site = $this;

        // Register default helpers for the view
        $this->_addViewHelpers($this->_view);
    }

    /**
     * Set the server name
     *
     * @param   string      $serverName     Server name
     */
    public function setServerName($serverName)
    {
        $this->_serverName = $serverName;
    }

    /**
     * Set the controllers path
     *
     * @param   string      $path           Controllers path
     */
    public function setControllersPath($path)
    {
        $this->_controllersPath = $path;
    }

    /**
     * Get the controllers path
     *
     * @return  string                      Controllers path
     */
    public function getControllersPath()
    {
        return $this->_controllersPath;
    }

    /**
     * Set the views path
     *
     * @param   string      $path           Views path
     */
    public function setViewsPath($path)
    {
        $this->_viewsPath = $path;
        $this->_view->setDirectory($path);
    }

    /**
     * Get the views path
     *
     * @return  string                      Views path
     */
    public function getViewsPath()
    {
        return $this->_viewsPath;
    }

    /**
     * Set the view renderer
     *
     * @param   \Neolao\Site\View   $renderer   View renderer
     */
    public function setViewRenderer($renderer)
    {
        $this->_view = $renderer;
        $this->_view->site = $this;

        // Set the views path
        $directory = $this->_viewsPath;
        $this->_view->setDirectory($directory);

        // Register default helpers for the view
        $this->_addViewHelpers($this->_view);
    }

    /**
     * Set the routes
     *
     * @param   stdClass    $routes         Routes object
     */
    public function setRoutes($routes)
    {
        $this->_request->setRoutes($routes);
    }

    /**
     * Server name
     * 
     * @var string
     */
    public function get_serverName()
    {
        return $this->_serverName;
    }
    
    /**
     * Site base URL
     * 
     * @var string
     */
    public function get_baseUrl()
    {
        return $this->_baseUrl;
    }
    
    /**
     * HTTP Request
     * 
     * @var \Neolao\Site\Request
     */
    public function get_request()
    {
        return $this->_request;
    }
    
    /**
     * View instance
     * 
     * @var \Neolao\Site\View
     */
    public function get_view()
    {
        return $this->_view;
    }
    
    /**
     * Run the site
     */
    public function run()
    {
        ob_start();

        try {
            // Handle the URL route
            $this->_request->handleRoute();
            
            // Display the site
            $this->display($this->_request->controllerName, $this->_request->actionName);
        } catch (\Exception $error) {
            // An error occured
            if (ob_get_length()) {
                ob_end_clean();
            }

            // Display the default action of the error controller
            try {
                $this->_view->message = $error->getMessage();
                $this->display('error', 'index');
            } catch (\Exception $errorForError) {
                if (ob_get_length()) {
                    ob_end_clean();
                }
                trigger_error($errorForError->getMessage());
            }
        }
    }
    
    /**
     * Display the site
     * 
     * @param   string  $controllerName     Controller name
     * @param   string  $actionName         Action name
     */
    public function display($controllerName, $actionName)
    {
        $controllerName                 = strtolower($controllerName);
        $this->_request->controllerName = $controllerName;
        $this->_request->actionName     = $actionName;
        
        // Create controller
        $controllerClassName    = ucfirst($controllerName).'Controller';
        $controllerPath         = $this->_controllersPath.'/'.$controllerClassName.'.php';
        if (!is_file($controllerPath)) {
            throw new \Exception('Controller not found: '.$controllerPath);
        }
        require_once($controllerPath);
        $controller = new $controllerClassName($this);
        if ($controller instanceof \Neolao\Site\Controller === false) {
            throw new \Exception($controllerClassName.' does not inherit \Neolao\Site\Controller');
        }

        // Add controller helpers
        $this->_addControllerHelpers($controller);
        
        // Dispatch the request
        $controller->dispatch($this->_request);
        
        // Render the view
        // By default, the view has the same name as the controller name
        $this->render($controllerName);
    }
    
    /**
     * Render the view
     * 
     * @param   string  $viewName       View name
     */
    public function render($viewName)
    {
        $renderedView = $this->_view->render($viewName);
        echo $renderedView;
        @ob_end_flush();
        exit;
    }
    
    /**
     * Redirect to a page
     *
     * @param   string  $routeName          Route name
     * @param   array   $parameters         Parameters
     */
    public function redirect($routeName, $parameters = array())
    {
        $url = $this->getLink($routeName, $parameters);
        header('Pragma: no-cache');
        header('Expires: 0');
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header('Cache-Control: max-age=0, no-cache, no-store, must-revalidate');
        header('Location: '.$url);
        exit;
    }

    /**
     * Add controller helpers
     *
     * @param   \Neolao\Site\Controller     $controller     Controller instance
     */
    protected function _addControllerHelpers(\Neolao\Site\Controller $controller)
    {

    }

    /**
     * Add view helpers
     * 
     * @param   \Neolao\Site\View           $view           View instance
     */
    protected function _addViewHelpers(\Neolao\Site\View $view)
    {
        //$view->registerHelperClass('example', '\\Neolao\\Site\\Helper\\View\\Example');
    }
}
