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
     * Get user by email
     *
     * @param   string      $email      User email
     * @return  \Vo\User                User instance
     */
    public function getByEmail($email)
    {
        $daoUser = DaoUser::factory();
        $user = $daoUser->getByEmail($email);

        return $user;
    }
}
