<?php
namespace Bo;

use \Bo\Issue\Exception\UpdateException;
use \Dao\Issue as DaoIssue;
use \Vo\Issue as VoIssue;
use \Filter\Issue as FilterIssue;

/**
 * Business Object to work with issues
 */
class Issue
{
    use \Neolao\Mixin\Singleton;

    /**
     * Instance of DAO issue
     *
     * @var \Dao\Issue\IssueInterface
     */
    protected $_daoIssue;

    /**
     * Constructor
     */
    protected function __construct()
    {
        $this->_daoIssue = DaoIssue::factory();
    }

    /**
     * Add an issue
     *
     * @param   \Vo\Issue   $issue      Issue instance
     */
    public function add(VoIssue $issue)
    {
        $this->_daoIssue->add($issue);
    }

    /**
     * Get issue by id
     *
     * @param   string      $id             Issue id
     * @return  \Vo\Issue                   Issue instance
     */
    public function getById($id)
    {
        $issue = $this->_daoIssue->getById($id);

        return $issue;
    }

    /**
     * Get a issue list
     *
     * @param   \Filter\Issue       $filter         The filter
     * @param   string              $orderBy        The property name to sort
     * @param   int                 $count          The list length
     * @param   int                 $offset         The offset
     * @return  array                               Issue list
     */
    public function getList(FilterIssue $filter = null, $orderBy = null, $count = null, $offset = null)
    {
        $list = $this->_daoIssue->getList($filter, $orderBy, $count, $offset);

        return $list;
    }

    /**
     * Update an issue
     *
     * @param   \Vo\Issue       $issue      Issue instance
     * @throws  \Dao\Issue\Exception\UpdateException
     */
    public function update(VoIssue $issue)
    {
        // Check if the file exists
        try {
            $issueFound = $this->_daoIssue->getById($issue->id);
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            throw new UpdateException($message, UpdateException::UNKNOWN, $exception);
        }
        if ($issueFound instanceof VoIssue === false) {
            throw new UpdateException('Issue not found: ' . $issue->id, UpdateException::ISSUE_NOT_FOUND);
        }


        // Update the database
        try {
            $this->_daoIssue->update($issue);
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            throw new UpdateException($message, UpdateException::UNKNOWN, $exception);
        }
    }

    /**
     * Delete an issue
     *
     * @param   int         $issueId        Issue id
     */
    public function delete($issueId)
    {
        $this->_daoIssue->delete($issueId);
    }

}
