<?php
namespace Dao;

use \Dao\Project\FileSystem as DaoFileSystem;
use \Dao\Project\ProjectInterface;

/**
 * DAO of projects
 */
class Project
{
    /**
     * Get the concrete DAO
     *
     * @return  \Dao\Project\ProjectInterface       DAO instance
     */
    public static function factory()
    {
        // Get the instance of the concrete DAO
        $dao = DaoFileSystem::getInstance();

        // Check the interface of the concrete DAO
        if ($dao instanceof ProjectInterface === false) {
            throw new \Exception('The DAO must implements ProjectInterface');
        }

        return $dao;
    }
}
