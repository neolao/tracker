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
     * Render a view name
     * 
     * @param   string  $viewName       View name to render
     * @return  string                  The rendered view
     */
    public function render($viewName)
    {
        $templatePath = $this->_directoryPath . '/' . $viewName . '.mustache';
        if (!is_file($templatePath)) {
            throw new \Exception('Template file not found: ' . $templatePath);
        }

        // Initialize the engine
        if (!$this->_engine) {
            $options = array(
                'loader' => new \Mustache_Loader_FilesystemLoader($this->_directoryPath),
                'partials_loader' => new \Mustache_Loader_FilesystemLoader($this->_directoryPath)
            );
            $this->_engine = new \Mustache_Engine($options);
        }

        // Render
        $result = $this->_engine->render($viewName, $this);
        return $result;
    }

}
