<?php
/**
 * @package Neolao\Site\Helper\Controller
 */
namespace Neolao\Site\Helper\Controller;

/**
 * Abstract class for a helper controller
 */
abstract class AbstractHelper implements \Neolao\Site\Helper\ControllerInterface
{
    /**
     * Controller instance
     *
     * @var \Neolao\Site\Controller
     */
    protected $_controller;



    /**
     * Set the controller
     *
     * @param   \Neolao\Site\Controller     $controller     Controller instance
     */
    public function setController(\Neolao\Site\Controller $controller)
    {
        $this->_controller = $controller;
    }

    /**
     * Get the controller
     *
     * @return  \Neolao\Site\Controller                     Controller instance
     */
    public function getController()
    {
        return $this->_controller;
    }
}
