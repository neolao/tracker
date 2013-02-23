<?php
/**
 * @package Neolao\Logger
 */
namespace Neolao\Logger;


use \Neolao\Logger\ListenerInterface;

/**
 * File listener for the logger
 */
class FileListener implements ListenerInterface
{
    /**
     * File path
     *
     * @var string
     */
    protected $_filePath;

    /**
     * Level
     *
     * @var string
     */
    protected $_level;

    /**
     * Resource of the file
     *
     * @var resource
     */
    protected $_resource;


    /**
     * Constructor
     */
    public function __construct($filePath, $level = null)
    {
        $this->_filePath = $filePath;
        $this->_level = $level;
    }

    /**
     * Log a message
     *
     * @param   string      $level      The level
     * @param   string      $message    The message
     */
    public function log($level, $message)
    {
        // Add time stamp and new line
        $message = date('[H:i:s] ').$message."\n";
        
        // Check if the directory is created
        $directory = dirname($this->_filePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }
        
        // Write ...
        if (is_null($this->_resource)) {
            $resource = @fopen($this->_filePath, 'a');
            if ($resource === false) {
                return;
            }
            $this->_resource = $resource;
        }
        @fwrite($this->_resource, $message);
    }

}
