<?php
namespace Mail\Message;

use \Vo\User;
use \Bo\User as BoUser;

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
        $hash   = $boUser->getRecoveryHash($user);
        $url    = $hash;

        // Set the email parameters
        // @todo complete
        $this->addTo($user->email);
        $this->setSubject('Test');
        $this->setBody('URL: ' . $url);
    }
}
