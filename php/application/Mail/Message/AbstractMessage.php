<?php
namespace Mail\Message;

/**
 * Message of an email
 */
abstract class AbstractMessage extends \Zend\Mail\Message
{
    /**
     * Language of the message
     *
     * @var string
     */
    public $language;

    /**
     * Constructor
     */
    public function __construct()
    {
        // The default language is English
        $this->language = 'en';

        // @todo Custom email
        $this->addFrom('tracker@neolao.com');
    }
}
