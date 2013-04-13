<?php
namespace Mail\Message;

use \Vo\User;
use \Bo\User as BoUser;
use \Site\Helper\Controller\LinkMainHelper;

/**
 * This message contains the URL of the password recovery
 */
class PasswordRecovery extends AbstractMessage
{
    /**
     * Constructor
     *
     * @param   \Vo\User    $user       User instance
     */
    public function __construct(User $user)
    {
        parent::__construct();

        // Get the url
        $boUser = BoUser::getInstance();
        $helper = new LinkMainHelper();
        $hash   = $boUser->getRecoveryHash($user);
        $url    = $helper->reverse('changePassword', ['id' => $user->id, 'hash' => $hash]);

        // Set the email parameters
        // @todo complete
        $this->addTo($user->email);
        $this->setSubject('Test');
        $this->setBody('URL: ' . $url);
    }
}
