<?php
namespace Dao;

use \Dao\User\FileSystem as DaoFileSystem;
use \Dao\User\UserInterface;

/**
 * DAO of users
 */
class User
{
    /**
     * Get the concrete DAO
     *
     * @return  \Dao\User\UserInterface     DAO instance
     */
    public static function factory()
    {
        // Get the instance of the concrete DAO
        $dao = DaoFileSystem::getInstance();

        // Check the interface of the concrete DAO
        if ($dao instanceof UserInterface === false) {
            throw new \Exception('The DAO must implements UserInterface');
        }

        return $dao;
    }
}
