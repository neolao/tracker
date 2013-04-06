<?php
namespace Dao;

use \Dao\User\FileSystem as DaoFileSystem;

/**
 * DAO of users
 */
class User
{
    use \Neolao\Mixin\Singleton;

    /**
     * Get the concrete DAO
     *
     * @return  \Dao\User\UserInterface     DAO instance
     */
    public static function factory()
    {
        $dao = DaoFileSystem::getInstance();

        return $dao;
    }
}
