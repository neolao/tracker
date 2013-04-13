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
     *
     * @param   string      $language       Language
     */
    public function __construct($language = 'en')
    {
        // The default language is English
        $this->language = $language;

        // Set the encoding
        $this->setEncoding('UTF-8');

        // @todo Custom email
        $this->addFrom('tracker@neolao.com');
    }
}
