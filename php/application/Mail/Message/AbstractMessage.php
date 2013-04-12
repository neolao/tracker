<?php
namespace Mail\Message;

/**
 * Message of an email
 */
abstract class AbstractMessage
{
    /**
     * Email of the sender
     *
     * @var string
     */
    public $senderEmail;

    /**
     * Label of the sender
     *
     * @var string
     */
    public $senderLabel;

    /**
     * Email of the recipient
     *
     * @var string
     */
    public $recipientEmail;

    /**
     * Label of the recipient
     *
     * @var string
     */
    public $recipientLabel;

    /**
     * Subject
     *
     * @var string
     */
    public $subject;

    /**
     * Content in plain text
     *
     * @var string
     */
    public $contentText;

    /**
     * Content in HTML
     *
     * @var string
     */
    public $contentHtml;

    /**
     * Language
     *
     * @var string
     */
    public $language;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->creationDate         = time();
        $this->language             = 'en';
        $this->senderEmail          = '';
        $this->recipientEmail       = '';
        $this->subject              = '';
        $this->contentText          = '';
        $this->contentHtml          = '';
    }
}
