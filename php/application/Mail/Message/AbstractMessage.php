<?php
namespace Mail\Message;

/**
 * Message of an email
 */
abstract class AbstractMessage extends \Zend\Mail\Message
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->addFrom('tracker@neolao.com');
    }
}
