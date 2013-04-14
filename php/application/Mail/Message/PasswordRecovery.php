<?php
namespace Mail\Message;

use \Vo\User;
use \Bo\User as BoUser;
use \Site\Helper\Controller\LinkMainHelper;
use \Zend\Mime\Message as MimeMessage;
use \Zend\Mime\Part as MimePart;

/**
 * This message contains the URL of the password recovery
 */
class PasswordRecovery extends AbstractMessage
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
        $hash   = $boUser->getRecoveryHash($user);
        $url    = $helper->reverse('changePassword', ['id' => $user->id, 'hash' => $hash]);

        // Set the email parameters
        $this->addTo($user->email);
        if ($language === 'fr') {
            $this->setSubject('Tracker - Réinitialiser votre mot de passe');

            $text = new MimePart('Cliquez sur ce lien pour réinitialiser votre mot de passe : ' . $url);
            $text->type = "text/plain";

            $html = new MimePart('<p>Cliquez sur ce lien pour réinitialiser votre mot de passe : <a href="' . $url . '">' . $url . '</a>.</p>');
            $html->type = "text/html";
        } else {
            $this->setSubject('Tracker - Reset your password');

            $text = new MimePart('Click on this link to reset your password: ' . $url);
            $text->type = "text/plain";

            $html = new MimePart('<p>Click on this link to reset your password: <a href="' . $url . '">' . $url . '</a>.</p>');
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
