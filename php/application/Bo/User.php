<?php
namespace Bo;

use \Dao\User as DaoUser;
use \Vo\User as VoUser;

/**
 * Business Object to work with users
 */
class User
{
    use \Neolao\Mixin\Singleton;

    /**
     * Instance of a DAO user
     *
     * @var \Dao\User\UserInterface
     */
    protected $_daoUser;

    /**
     * Constructor
     */
    protected function __construct()
    {
        $this->_daoUser = DaoUser::factory();
    }

    /**
     * Add a user
     *
     * @param   \Vo\User    $user       User instance
     */
    public function add(VoUser $user)
    {
        // Update the user
        $user->creationDate     = time();
        $user->modificationDate = time();

        // Add the user into the database
        $this->_daoUser->add($user);
    }

    /**
     * Update a user
     *
     * @param   \Vo\User    $user       User instance
     */
    public function update(VoUser $user)
    {
        $user->modificationDate = time();
        $this->_daoUser->update($user);
    }

    /**
     * Get user by id
     *
     * @param   int         $id         User id
     * @return  \Vo\User                User instance
     */
    public function getById($id)
    {
        $user = $this->_daoUser->getById($id);

        return $user;
    }

    /**
     * Get user by email
     *
     * @param   string      $email      User email
     * @return  \Vo\User                User instance
     */
    public function getByEmail($email)
    {
        $user = $this->_daoUser->getByEmail($email);

        return $user;
    }

    /**
     * Confirm a user
     *
     * @param   \Vo\User    $user       User instance
     */
    public function confirm(VoUser $user)
    {
        // Update the user
        $user->confirmed = true;
        $user->modificationDate = time();

        // Update the user in the database
        $this->_daoUser->update($user);
    }

    /**
     * Get the register confirmation hash
     *
     * @param   \Vo\User    $user       User instance
     * @return  string                  Hash
     */
    public function getRegisterConfirmationHash(VoUser $user)
    {
        $hash = $user->email . $user->creationDate . $user->password;
        $hash = sha1($hash);

        return $hash;
    }

    /**
     * Get the recovery hash
     *
     * @param   \Vo\User    $user       User instance
     * @return  string                  Hash
     */
    public function getRecoveryHash(VoUser $user)
    {
        $hash = $user->email . $user->creationDate . $user->modificationDate . $user->password;
        $hash = sha1($hash);

        return $hash;
    }

    /**
     * Change the password
     *
     * @param   \Vo\User    $user           User instance
     * @param   string      $newPassword    New password
     */
    public function changePassword(VoUser $user, $newPassword)
    {
        $user->password = $newPassword;
        $user->modificationDate = time();

        $this->_daoUser->update($user);
    }
}
