<?php
/**
 * @package Site
 */
namespace Site;

/**
 * Main site
 */
class Main extends \Neolao\Site
{
    /**
     * Add view helpers
     * 
     * @param   \Neolao\Site\View           $view           View instance
     */
    protected function _addViewHelpers(\Neolao\Site\View $view)
    {
        parent::_addViewHelpers($view);
    }
}
