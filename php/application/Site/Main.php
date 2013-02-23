<?php
/**
 * @package Site
 */
namespace Site;


use \Neolao\Site\View;

/**
 * Main site
 */
class Main extends \Neolao\Site
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
    protected function _addControllerHelpers(\Neolao\Site\Controller $controller)
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
