<?php
/**
 * @package Neolao\Site
 */
namespace Neolao\Site;

/**
 * A view
 */
class View
{
    /**
     * Parameters
     *
     * @var array
     */
    public $parameters;

    /**
     * Helper list
     *
     * @var array
     */
    protected $_helpers;
    
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->parameters = array();
        $this->_helpers = array();
    }
    
    /**
     * Render a template file
     * 
     * @param   string  $templatePath   Template path to render
     */
    public function render($templatePath)
    {
        if (!is_file($templatePath)) {
            throw new \Exception('Template file not found: '.$templatePath);
        }
        
        include $templatePath;
    }

    /**
     * Register a helper instance
     *
     * @param   string                          $key        Helper key in this view
     * @param   \Neolao\Helper\ViewInterface    $helper     Helper instance
     */
    public function registerHelper($key, \Neolao\Site\Helper\ViewInterface $helper)
    {
        $this->_helpers[$key] = $helper;
    }

    /**
     * Register a helper class
     *
     * @param   string                      $key            Helper key in this view
     * @param   string                      $helperClass    Helper class name
     * @param   array                       $parameters     Parameters for the instance
     */
    public function registerHelperClass($key, $helperClass, $parameters = array())
    {
        $this->_helpers[$key] = array(
            'className'     => $helperClass,
            'parameters'    => $parameters
        );
    }
    
    /**
     * Magic method for functions
     *
     * @param   string      $name           Function name
     * @param   array       $arguments      Arguments
     * @return  mixed                       The result
     */
    public function __call($name, $arguments)
    {
        // Call a helper
        if (isset($this->_helpers[$name])) {
            $helper = $this->_helpers[$name];

            // Check if the helper needs to create an instance
            if ($helper instanceof \Neolao\Site\Helper\ViewInterface === false) {
                $helperClassName    = $helper['className'];
                $helperParameters   = $helper['parameters'];
                $helper             = new $helperClassName();
                if ($helper instanceof \Neolao\Site\Helper\ViewInterface) {
                    foreach ($helperParameters as $helperParameterName => $helperParameterValue) {
                        $helper->$helperParameterName = $helperParameterValue;
                    }
                    $this->_helpers[$name] = $helper;
                }
            }

            // Call the main method
            return call_user_func_array(
                array($helper, 'main'),
                $arguments
            );
        }
    }
}
