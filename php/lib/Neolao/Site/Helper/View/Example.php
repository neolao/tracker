<?php
/**
 * @package Neolao\Site\Helper\View
 */
namespace Neolao\Site\Helper\View;

/**
 * Abstract class for a view helper
 */
class Example extends \Neolao\Site\Helper\View\AbstractHelper
{
    /**
     * The main function
     *
     * @param   mixed   $argument   The only one argument
     */
    public function main($argument)
    {
        return 'example of a view helper: '.$argument;
    }
}
