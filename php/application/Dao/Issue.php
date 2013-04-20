<?php
namespace Dao;

use \Dao\Issue\FileSystem as DaoFileSystem;
use \Dao\Issue\IssueInterface;

/**
 * DAO of issues
 */
class Issue
{
    /**
     * Get the concrete DAO
     *
     * @return  \Dao\Issue\IssueInterface       DAO instance
     */
    public static function factory()
    {
        // Get the instance of the concrete DAO
        $dao = DaoFileSystem::getInstance();

        // Check the interface of the concrete DAO
        if ($dao instanceof IssueInterface === false) {
            throw new \Exception('The DAO must implements IssueInterface');
        }

        return $dao;
    }
}
