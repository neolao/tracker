<?php
/**
 * Package Dao
 */
namespace Dao;

/**
 * DAO of users
 */
class User
{
    use \Neolao\Mixin\Singleton;

    /**
     * Get user by email
     *
     * @param   string      $email      User email
     * @return  \Bo\User                User instance
     */
    public function getByEmail($email)
    {
        // @todo

        return null;
    }
}
