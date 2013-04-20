<?php
namespace Mail\Message;

use \Vo\User;
use \Bo\User as BoUser;
use \Site\Helper\Controller\LinkMainHelper;
use \Zend\Mime\Message as MimeMessage;
use \Zend\Mime\Part as MimePart;

/**
 * This message contains the URL to confirm a registration
 */
class RegisterConfirmation extends AbstractMessage
{
    /**
     * Constructor
     *
     * @param   \Vo\User    $user       User instance
     * @param   string      $language   Language
     */
    public function __construct(User $user, $language = 'en')
    {
        parent::__construct($language);

        // Get the url
        $boUser = BoUser::getInstance();
        $helper = new LinkMainHelper();
        $hash   = $boUser->getRegisterConfirmationHash($user);
        $url    = $helper->reverse('registerConfirmation', ['id' => $user->id, 'hash' => $hash]);

        // Set the email parameters
        // @todo Internationalize it
        $this->addTo($user->email);
        if ($language === 'fr') {
            $this->setSubject('Tracker - Créer un compte');

            $text = new MimePart('Cliquez sur ce lien pour confirmer la création de votre compte : ' . $url);
            $text->type = "text/plain";

            $html = new MimePart('<p>Cliquez sur ce lien pour confirmer la création de votre compte : <a href="' . $url . '">' . $url . '</a>.</p>');
            $html->type = "text/html";
        } else {
            $this->setSubject('Tracker - Create an account');

            $text = new MimePart('Click on this link to confirm the creation of your account: ' . $url);
            $text->type = "text/plain";

            $html = new MimePart('<p>Click on this link to confirm the creation of your account: <a href="' . $url . '">' . $url . '</a>.</p>');
            $html->type = "text/html";
        }

        // Set the body
        $body = new MimeMessage();
        $body->setParts([$html, $text]);
        $this->setBody($body);

        // Correct a bug in Zend Framework
        // The content-type must be "multipart/alternative"
        $header = $this->getHeaderByName('content-type', 'Zend\Mail\Header\ContentType');
        $header->setType('multipart/alternative');
    }
}
