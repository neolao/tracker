<?php
namespace Dao\Issue;

use \Vo\Issue;
use \Filter\Issue as FilterIssue;

/**
 * Interface of a DAO of issues
 */
interface IssueInterface extends \Neolao\Behavior\Singleton
{
    /**
     * Add an issue
     *
     * @param   \Vo\Issue   $issue      Issue instance
     */
    function add(Issue $issue);

    /**
     * Get issue by id
     *
     * @param   string      $id             Issue id
     * @return  \Vo\Issue                   Issue instance
     */
    function getById($id);

    /**
     * Get a issue list
     *
     * @param   \Filter\Issue       $filter         The filter
     * @param   array               $orderBy        The properties to sort
     * @param   int                 $count          The list length
     * @param   int                 $offset         The offset
     * @return  array                               Issue list
     */
    function getList(FilterIssue $filter = null, array $orderBy = null, $count = null, $offset = null);

    /**
     * Update an issue
     *
     * @param   \Vo\Issue       $issue      Issue instance
     * @throws  \Dao\Issue\Exception\UpdateException
     */
    function update(Issue $issue);

    /**
     * Delete an issue
     *
     * @param   int         $issueId        Issue id
     */
    function delete($issueId);
}
