<?php
/**
 * @package Site\View
 */
namespace Site\View;

/**
 * Mustache renderer
 */
class Mustache extends \Neolao\Site\View
{
    /**
     * Engine instance
     *
     * @var Mustache_Engine
     */
    private $_engine;

    /**
     * Constructor
     */
    public function __construct()
    {
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

        // Initialize the engine
        if (!$this->_engine) {
            $this->_engine = new \Mustache_Engine();
        }

        // Render
        $templateContent = file_get_contents($templatePath);
        echo $this->_engine->render($templateContent, $this->parameters);
        exit;
    }

}
