<?php
namespace Dao;

use \Dao\Issue\FileSystem as DaoFileSystem;

/**
 * DAO of issues
 */
class Issue
{
    /**
     * Get the concrete DAO
     *
     * @return  \Dao\Issue\ProjectInterface       DAO instance
     */
    public static function factory()
    {
        $dao = DaoFileSystem::getInstance();

        return $dao;
    }
}
