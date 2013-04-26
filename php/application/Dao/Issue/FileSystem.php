<?php
namespace Dao\Issue;

use \Filter\Issue as FilterIssue;
use \Vo\Issue as Issue;
use \Neolao\Util\String as StringUtil;
use \Dao\Database\Sqlite;

/**
 * DAO of issues
 */
class FileSystem implements IssueInterface
{
    use \Neolao\Mixin\Singleton;

    /**
     * Database instance
     *
     * @var \Dao\Database\Sqlite
     */
    protected $_database;

    /**
     * Constructor
     */
    protected function __construct()
    {
        // Get the database instance
        try {
            $this->_database = Sqlite::getInstance();
        } catch (\Exception $exception) {
        }
    }

    /**
     * Add an issue
     *
     * @param   \Vo\Issue   $issue      Issue instance
     */
    public function add(Issue $issue)
    {
        // Update the issue instance
        $nextId                     = $this->_getNextId();
        $issue->id                  = $nextId;

        // Create the file
        $directory  = $this->_getDataDirectory();
        $filePath   = $directory . '/' . $nextId . '.json';
        $serialized = $issue->serializeJson();
        if (!is_dir($directory)) {
            mkdir($directory, 0777 - umask(), true);
        }
        file_put_contents($filePath, $serialized, LOCK_EX);

        // Add to the database
        $this->_databaseAdd($issue);
    }

    /**
     * Get issue by id
     *
     * @param   string      $id             Issue id
     * @return  \Vo\Issue                   Issue instance
     */
    public function getById($id)
    {
        // Search in the directory
        $directory = $this->_getDataDirectory();
        $filePath = $directory . '/' . $id . '.json';
        if (is_file($filePath)) {
            $issue = $this->_buildIssueFromFile($filePath);
            return $issue;
        }

        return null;
    }

    /**
     * Get a issue list
     *
     * @param   \Filter\Issue       $filter         The filter
     * @param   array               $orderBy        The properties to sort
     * @param   int                 $count          The list length
     * @param   int                 $offset         The offset
     * @return  array                               Issue list
     */
    public function getList(FilterIssue $filter = null, array $orderBy = null, $count = null, $offset = null)
    {
        // Initialize the parameter "filter"
        if (is_null($filter)) {
            $filter = new FilterIssue();
        }

        // Sanitize the parameter "orderBy"
        if (is_null($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }
        foreach ($orderBy as $propertyName => $propertyOrder) {
            $propertyOrder = strtoupper($propertyOrder);
            if ($propertyOrder !== 'ASC' && $propertyOrder !== 'DESC') {
                $propertyOrder = 'ASC';
            }
            $orderBy[$propertyName] = $propertyOrder;
        }

        // Get the issues from the database
        try {
            // Base of the query
            $query = 'SELECT id FROM issues';

            // @todo Where

            // Order by
            $queryOrderBy = [];
            foreach ($orderBy as $propertyName => $propertyOrder) {
                $queryOrderBy[] = $propertyName . ' ' . $propertyOrder;
            }
            $query .= ' ORDER BY ' . implode(', ', $queryOrderBy);

            // Limit
            if (!is_null($count)) {
                $query .= ' LIMIT ' . $count;
            }

            // Offset
            if (!is_null($offset)) {
                $query .= ' OFFSET ' . $offset;
            }

            // Get the result
            $statement  = $this->_database->prepare($query);
            $result     = $statement->execute();
            $list       = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $issueId = (int) $row['id'];
                $list[] = $this->getById($issueId);
            }
            return $list;
        } catch (\Exception $exception) {
        }




        // An error occurred with the database
        // Try to get the issues from the file system

        // Get the sort parameters for the file system
        foreach ($orderBy as $propertyName => $propertyOrder) {
            $sortOrder = $propertyOrder;
            switch ($propertyName) {
                default:
                case 'id':
                    $sortField = 'id';
                    $sortFlag = SORT_NUMERIC;
                    break;
            }
            break;
        }

        // Get the issues
        $directory  = $this->_getDataDirectory();
        $filePaths  = glob($directory . '/*.json');
        $sortedIds  = [];
        $list       = [];
        foreach ($filePaths as $filePath) {
            // Build the issue instance
            $issue = $this->_buildIssueFromFile($filePath);
            $issueId = $issue->id;

            // Apply the filter
            if (!$issue->matchFilter($filter)) {
                continue;
            }

            // Update the sorted list
            $sortProperty = $issue->$sortField;
            $sortProperty = strtolower($sortProperty);
            $sortProperty = StringUtil::removeAccents($sortProperty);
            $sortedIds[$issueId] = $sortProperty;

            // Add the issue to the list
            $list[$issueId] = $issue;
        }

        // Sort the filtered projects
        if ($sortOrder === 'DESC') {
            arsort($sortedIds, $sortFlag);
        } else {
            asort($sortedIds, $sortFlag);
        }
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
     * @param   \Vo\Issue       $issue      Issue instance
     */
    public function update(Issue $issue)
    {
        // Update the file
        $directory  = $this->_getDataDirectory();
        $filePath   = $directory . '/' . $issue->id . '.json';
        $serialized = $issue->serializeJson();
        if (!is_dir($directory)) {
            mkdir($directory, 0777 - umask(), true);
        }
        file_put_contents($filePath, $serialized, LOCK_EX);

        // Update the database
        $this->_databaseUpdate($issue);
    }

    /**
     * Delete an issue
     *
     * @param   int         $issueId        Issue id
     */
    public function delete($issueId)
    {
        // Sanitize the parameter
        $issueId = (int) $issueId;

        // Delete from the file system
        $directory = $this->_getDataDirectory();
        $filePath = $directory . '/' . $issueId . '.json';
        if (is_file($filePath)) {
            unlink($filePath);
        }

        // Delete from the database
        try {
            $this->_database->execute('DELETE FROM issues WHERE id = ' . $issueId);
        } catch (\Exception $exception) {
        }

    }

    /**
     * Populate the database from the files
     */
    public function populateDatabase()
    {
        $directory  = $this->_getDataDirectory();
        $filePaths  = glob($directory . '/*.json');
        foreach ($filePaths as $filePath) {
            // Get the issue instance
            $issue = $this->_buildIssueFromFile($filePath);

            // Add to the database
            $this->_databaseAdd($issue);
        }
    }

    /**
     * Get the directory path of the projects datas
     *
     * @return   string          Directory path
     */
    public function _getDataDirectory()
    {
        return ROOT_PATH . '/data/issues';
    }

    /**
     * Build an issue instance from a file
     *
     * @param   string      $filePath       File path
     * @return  \Vo\Issue                   Issue instance
     */
    protected function _buildIssueFromFile($filePath)
    {
        if (!is_readable($filePath)) {
            throw new \Exception("The file $filePath is not readable");
        }

        // Get the file content
        $fileContent = file_get_contents($filePath);

        // Create the issue instance
        $issue = new Issue();
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
        // Default id
        $id = 1;

        // Check the database
        try {
            $lastId = $this->_database->querySingle('SELECT id FROM issues ORDER BY id DESC LIMIT 1');
            if ($lastId) {
                $id = $lastId + 1;
            }
            return $id;
        } catch (\Exception $exception) {
        }


        // Check the data directory for the next id
        $directory  = $this->getDataDirectory();
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

    /**
     * Add to the database
     *
     * @param   \Vo\Issue       $issue          Issue instance
     */
    public function _databaseAdd(Issue $issue)
    {
        // Prepare the query
        $statement = $this->_database->prepare(
            'INSERT INTO issues
                (id, creationDate, modificationDate, name, description) 
             VALUES 
                (:id, :creationDate, :modificationDate, :name, :description)'
        );
        $statement->bindValue(':id',                $issue->id,                 SQLITE3_INTEGER);
        $statement->bindValue(':creationDate',      $issue->creationDate,       SQLITE3_INTEGER);
        $statement->bindValue(':modificationDate',  $issue->modificationDate,   SQLITE3_INTEGER);
        $statement->bindValue(':name',              $issue->name,               SQLITE3_TEXT);
        $statement->bindValue(':description',       $issue->description,        SQLITE3_TEXT);

        // Execute the query
        $statement->execute();
    }

    /**
     * Update the database
     *
     * @param   \Vo\Issue       $issue          Issue instance
     */
    public function _databaseUpdate(Issue $issue)
    {
        // Prepare the query
        $statement = $this->_database->prepare(
            'UPDATE issues
             SET
                creationDate        = :creationDate,
                modificationDate    = :modificationDate,
                name                = :name,
                description         = :description
             WHERE
                id                  = :id'
        );
        $statement->bindValue(':id',                $issue->id,                 SQLITE3_INTEGER);
        $statement->bindValue(':creationDate',      $issue->creationDate,       SQLITE3_INTEGER);
        $statement->bindValue(':modificationDate',  $issue->modificationDate,   SQLITE3_INTEGER);
        $statement->bindValue(':name',              $issue->name,               SQLITE3_TEXT);
        $statement->bindValue(':description',       $issue->description,        SQLITE3_TEXT);

        // Execute the query
        $statement->execute();
    }


}
