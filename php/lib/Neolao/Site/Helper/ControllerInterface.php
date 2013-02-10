<?php
/**
 * @package Neolao\Site\Helper
 */
namespace Neolao\Site\Helper;

/**
 * Interface for a helper controller
 *
 * The helper must contain a method named "main"
 */
interface ControllerInterface
{
    /**
     * The main function
     */
    //function main();

    /**
     * Set the controller
     *
     * @param   \Neolao\Site\Controller     $controller     Controller instance
     */
    function setController(\Neolao\Site\Controller $controller);

    /**
     * Get the controller
     *
     * @return  \Neolao\Site\Controller                     Controller instance
     */
    function getController();
}
