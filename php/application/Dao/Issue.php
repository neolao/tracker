<?php
namespace Dao;

use \Filter\Issue as FilterIssue;
use \Bo\Issue as BoIssue;
use \Dao\Issue\Exception\UpdateException;
use \Neolao\Util\String as StringUtil;

/**
 * DAO of issues
 */
class Issue
{
    use \Neolao\Mixin\Singleton;

    /**
     * Get the directory path of the projects datas
     *
     * @return   string          Directory path
     */
    public function getDataDirectory()
    {
        return ROOT_PATH . '/data/issues';
    }

    /**
     * Add an issue
     *
     * @param   \Bo\Issue   $issue      Issue instance
     */
    public function add(BoIssue $issue)
    {
        $directory      = $this->getDataDirectory();
        $nextId         = $this->_getNextId();

        // Update and serialize the issue instance
        $issue->id      = $nextId;
        $serialized     = $issue->serializeJson();

        // Create the file
        if (!is_dir($directory)) {
            mkdir($directory, 0777 - umask(), true);
        }
        $filePath = $directory . '/' . $nextId . '.json';
        file_put_contents($filePath, $serialized);
    }

    /**
     * Get issue by id
     *
     * @param   string      $id             Issue id
     * @return  \Bo\Issue                   Issue instance
     */
    public function getById($id)
    {
        // Check the cache

        // Search in the directory
        $directory = $this->getDataDirectory();
        $filePath = $directory . '/' . $id . '.json';
        if (is_file($filePath)) {
            $project = $this->_buildIssueFromFile($filePath);
            return $project;
        }

        return null;
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
        $directory  = $this->getDataDirectory();
        $sortedIds  = [];
        $list       = [];

        // Initialize the parameter "filter"
        if (is_null($filter)) {
            $filter = new FilterIssue();
        }

        // Sanitize the parameter "orderBy"
        switch ($orderBy) {
            default:
            case 'id':
                $orderBy = 'id';
                $sortFlag = SORT_NUMERIC;
                break;
        }


        // Get the issues
        $filePaths  = glob($directory . '/*.json');
        foreach ($filePaths as $filePath) {
            // Build the issue instance
            $issue = $this->_buildIssueFromFile($filePath);
            $issueId = $issue->id;

            // Apply the filter
            if (!$issue->matchFilter($filter)) {
                continue;
            }

            // Update the sorted list
            $sortProperty = $issue->$orderBy;
            $sortProperty = strtolower($sortProperty);
            $sortProperty = StringUtil::removeAccents($sortProperty);
            $sortedIds[$issueId] = $sortProperty;

            // Add the issue to the list
            $list[$issueId] = $issue;
        }

        // Sort the filtered projects
        asort($sortedIds, $sortFlag);
        foreach ($list as $id => $issue) {
            $sortedIds[$id] = $issue;
        }
        $list = array_merge($sortedIds);

        // Extract
        if (!is_null($count) && $count > 0) {
            if (is_null($offset)) {
                $offset = 0;
            }
            $list = array_splice($list, $offset, $count);
        }

        // Return the list
        return $list;
    }

    /**
     * Update an issue
     *
     * @param   \Bo\Issue       $issue      Issue instance
     * @throws  \Dao\Issue\Exception\UpdateException
     */
    public function update(BoIssue $issue)
    {
        $directory      = $this->getDataDirectory();
        $issueId        = $issue->id;
        $filePath       = $directory . '/' . $issueId . '.json';

        // Check if the file exists
        if (!is_file($filePath)) {
            throw new UpdateException('Issue not found: ' . $issueId, UpdateException::ISSUE_NOT_FOUND);
        }

        // Serialize the issue instance
        $serialized = $issue->serializeJson();

        // Update the file
        if (!is_dir($directory)) {
            mkdir($directory, 0777 - umask(), true);
        }
        file_put_contents($filePath, $serialized);

    }

    /**
     * Delete an issue
     *
     * @param   int         $issueId        Issue id
     */
    public function delete($issueId)
    {
        $directory = $this->getDataDirectory();
        $filePath = $directory . '/' . $issueId . '.json';

        // Delete
        if (is_file($filePath)) {
            unlink($filePath);
        }
    }

    /**
     * Build an issue instance from a file
     *
     * @param   string      $filePath       File path
     * @return  \Bo\Issue                   Issue instance
     */
    protected function _buildIssueFromFile($filePath)
    {
        if (!is_readable($filePath)) {
            throw new \Exception("The file $filePath is not readable");
        }

        // Get the file content
        $fileContent = file_get_contents($filePath);

        // Create the issue instance
        $issue = new BoIssue();
        $issue->unserializeJson($fileContent);

        return $issue;
    }

    /**
     * Get the next unique id
     *
     * @return  int     The next unique id
     */
    protected function _getNextId()
    {
        $directory  = $this->getDataDirectory();
        $id         = 1;

        // Check the data directory for the next id
        $filePaths  = glob($directory . '/*.json');
        foreach ($filePaths as $filePath) {
            $fileName = pathinfo($filePath, PATHINFO_FILENAME);
            $currentId = (int) $fileName;

            if ($currentId >= $id) {
                $id = $currentId + 1;
            }
        }

        return $id;
    }
}
