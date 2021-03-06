<?php

use \Neolao\Auth\BasicCookie;
use \Vo\User;
use \Bo\User as BoUser;

/**
 * Authentication
 */
class Auth extends BasicCookie
{
    /**
     * Constructor
     */
    protected function __construct()
    {
        parent::__construct();
    }

    /**
     * Set the current user with the session id
     *
     * @param   string      $identity       The identity
     * @param   string      $sessionId      The session id
     */
    public function setIdentity($identity, $sessionId)
    {
        parent::setIdentity($identity, $sessionId);

        // ACL
    }

    /**
     * Get the identity of a user
     *
     * @param   \Bo\User    $user           User instance
     * @return  string                      The identity
     */
    public function getIdentity($user)
    {
        if ($user instanceof User === false) {
            return '';
        }

        return $user->email;
    }

    /**
     * Get the session id of a user
     *
     * @param   \Bo\User    $user           User instance
     * @return  string                      The session id
     */
    public function getSessionId($user)
    {
        if ($user instanceof User === false) {
            return '';
        }

        $id = $user->email;
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $id .= $_SERVER['HTTP_USER_AGENT'];
        }
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $id .= $_SERVER['REMOTE_ADDR'];
        }
        $id .= $user->password;
        return sha1($id);
    }

    /**
     * Get a user instance by his identity
     *
     * @param   string      $identity       User identity
     * @return  \Bo\User                    User instance
     */
    public function getUserByIdentity($identity)
    {
        $boUser = BoUser::getInstance();
        $user = $boUser->getByEmail($identity);
        return $user;
    }

    /**
     * Get the password hash
     *
     * @param   string      $clearPassword      The clear password
     * @return  string                          The hash
     */
    public function getPasswordHash($clearPassword)
    {
        // @todo Get the salt from the config
        $salt = 'aEky6D';

        return sha1($clearPassword.$salt);
    }
}
