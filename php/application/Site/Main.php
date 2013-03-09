<?php
/**
 * @package Site
 */
namespace Site;


use \Neolao\Site\View;
use \Neolao\Site\Controller;
use \Neolao\SiteAdvanced;

/**
 * Main site
 */
class Main extends SiteAdvanced
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add controller helpers
     *
     * @param   \Neolao\Site\Controller     $controller     Controller instance
     */
    protected function _addControllerHelpers(Controller $controller)
    {
        parent::_addControllerHelpers($controller);

        // Add custom helpers
    }

    /**
     * Add view helpers
     * 
     * @param   \Neolao\Site\View           $view           View instance
     */
    protected function _addViewHelpers(View $view)
    {
        parent::_addViewHelpers($view);

        // Add custom helpers
    }
}
