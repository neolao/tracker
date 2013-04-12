<?php
namespace Bo;

use \Dao\User as DaoUser;

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
}
