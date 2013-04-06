<?php
namespace Dao\User;

/**
 * Interface of a DAO of users
 */
interface UserInterface extends \Neolao\Behavior\Singleton
{
    /**
     * Get user by email
     *
     * @param   string      $email      User email
     * @return  \Vo\User                User instance
     */
    function getByEmail($email);
}
