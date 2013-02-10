<?php
/**
 * @package Neolao\Site\Helper\View
 */
namespace Neolao\Site\Helper\View;

/**
 * Abstract class for a view helper
 */
abstract class AbstractHelper implements \Neolao\Site\Helper\ViewInterface
{
    /**
     * View instance
     *
     * @var \Neolao\Site\View
     */
    protected $_view;

    /**
     * The main function
     *
     * @param   mixed   $argument   The argument
     */
    public function main($argument)
    {
    }

    /**
     * Set the view
     *
     * @param   \Neolao\Site\View       $view       View instance
     */
    public function setView(\Neolao\Site\View $view)
    {
        $this->_view = $view;
    }

    /**
     * Get the view
     *
     * @return  \Neolao\Site\View                   View instance
     */
    public function getView()
    {
        return $this->_view;
    }

}
