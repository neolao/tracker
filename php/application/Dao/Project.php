<?php
namespace Dao;

use \Dao\Project\FileSystem as DaoFileSystem;

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
        $dao = DaoFileSystem::getInstance();

        return $dao;
    }
}
