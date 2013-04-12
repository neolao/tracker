<?php
namespace Dao\Project;

use \Filter\Project as FilterProject;
use \Vo\Project;
use \Neolao\Util\String as StringUtil;
use \Dao\Database\Sqlite;

/**
 * DAO of projects
 */
class FileSystem implements ProjectInterface
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
     * Add a project
     *
     * @param   \Vo\Project $project        Project instance
     * @throws  \Dao\Project\Exception\CreateException
     */
    public function add(Project $project)
    {
        // Update the project instance
        $nextId         = $this->_getNextId();
        $project->id    = $nextId;

        // Create the file
        $directory  = $this->_getDataDirectory();
        $filePath   = $directory . '/' . $nextId . '.json';
        $serialized = $project->serializeJson();
        if (!is_dir($directory)) {
            mkdir($directory, 0777 - umask(), true);
        }
        file_put_contents($filePath, $serialized);

        // Add to the database
        $this->_databaseAdd($project);
    }

    /**
     * Get project by id
     *
     * @param   string      $id             Project id
     * @return  \Vo\Project                 Project instance
     */
    public function getById($id)
    {
        // Search in the directory
        $directory = $this->_getDataDirectory();
        $filePath = $directory . '/' . $id . '.json';
        if (is_file($filePath)) {
            $project = $this->_buildProjectFromFile($filePath);
            return $project;
        }

        return null;
    }

    /**
     * Get project by code name
     *
     * @param   string      $codeName       Project code name
     * @return  \Vo\Project                 Project instance
     */
    public function getByCodeName($codeName)
    {
        // @todo Check the cache

        // Search in the database
        try {
            $statement = $this->_database->prepare('SELECT id FROM projects WHERE codeName = :codeName');
            $statement->bindValue(':codeName', $codeName, SQLITE3_TEXT);
            $result = $statement->execute();
            $row = $result->fetchArray(SQLITE3_ASSOC);

            if ($row) {
                $projectId = (int) $row['id'];
                $project = $this->getById($projectId);
                return $project;
            } else {
                return null;
            }
        } catch (\Exception $exception) {
        }

        // The search in the database failed

        // Search in the directory
        $directory  = $this->_getDataDirectory();
        $filePaths  = glob($directory . '/*.json');
        foreach ($filePaths as $filePath) {
            $project = $this->_buildProjectFromFile($filePath);
            if ($project->codeName === $codeName) {
                return $project;
            }
        }

        return null;
    }

    /**
     * Get a project list
     *
     * @param   \Filter\Project     $filter         The filter
     * @param   array               $orderBy        The properties to sort
     * @param   int                 $count          The list length
     * @param   int                 $offset         The offset
     * @return  array                               Project list
     */
    public function getList(FilterProject $filter = null, array $orderBy = null, $count = null, $offset = null)
    {
        // Initialize the parameter "filter"
        if (is_null($filter)) {
            $filter = new FilterProject();
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

        // Get the projects from the database
        try {
            // Base of the query
            $query = 'SELECT id FROM projects';

            // @todo Where

            // Order by
            $queryOrderBy = [];
            foreach ($orderBy as $propertyName => $propertyOrder) {
                $queryOrderBy[] = 'LOWER(' . $propertyName . ') ' . $propertyOrder;
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
                $projectId = (int) $row['id'];
                $list[] = $this->getById($projectId);
            }
            return $list;
        } catch (\Exception $exception) {
        }

        // An error occurred with the database
        // Try to get the projects from the file system

        // Get the sort parameters for the file system
        foreach ($orderBy as $propertyName => $propertyOrder) {
            $sortOrder = $propertyOrder;
            switch ($propertyName) {
                default:
                case 'id':
                    $sortField = 'id';
                    $sortFlag = SORT_NUMERIC;
                    break;
                case 'name':
                    $sortField = 'name';
                    $sortFlag = SORT_STRING;
                    break;
            }
            break;
        }

        // Get the projects
        $directory  = $this->_getDataDirectory();
        $filePaths  = glob($directory . '/*.json');
        $sortedIds  = [];
        $list       = [];
        foreach ($filePaths as $filePath) {
            // Build the project instance
            $project = $this->_buildProjectFromFile($filePath);
            $projectId = $project->id;

            // Apply the filter
            if (!$project->matchFilter($filter)) {
                continue;
            }

            // Update the sorted list
            $sortProperty = $project->$sortField;
            $sortProperty = strtolower($sortProperty);
            $sortProperty = StringUtil::removeAccents($sortProperty);
            $sortedIds[$projectId] = $sortProperty;

            // Add the project to the list
            $list[$projectId] = $project;
        }

        // Sort the filtered projects
        if ($sortOrder === 'DESC') {
            arsort($sortedIds, $sortFlag);
        } else {
            asort($sortedIds, $sortFlag);
        }
        foreach ($list as $id => $project) {
            $sortedIds[$id] = $project;
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
     * Update a project
     *
     * @param   \Vo\Project     $project    Project instance
     */
    public function update(Project $project)
    {
        // Update the project
        $project->modificationDate = time();

        // Update the file
        $directory  = $this->_getDataDirectory();
        $filePath   = $directory . '/' . $project->id . '.json';
        $serialized = $project->serializeJson();
        if (!is_dir($directory)) {
            mkdir($directory, 0777 - umask(), true);
        }
        file_put_contents($filePath, $serialized);

        // Update the database
        $this->_databaseUpdate($project);
    }

    /**
     * Delete a project
     *
     * @param   int         $projectId      Project id
     */
    public function delete($projectId)
    {
        // Sanitize the parameter
        $projectId = (int) $projectId;

        // Delete from the file system
        $directory = $this->_getDataDirectory();
        $filePath = $directory . '/' . $projectId . '.json';
        if (is_file($filePath)) {
            unlink($filePath);
        }

        // Delete from the database
        try {
            $this->_database->execute('DELETE FROM projects WHERE id = ' . $projectId);
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
            // Get the project instance
            $project = $this->_buildProjectFromFile($filePath);

            // Add to the database
            $this->_databaseAdd($project);
        }
    }

    /**
     * Get the directory path of the projects datas
     *
     * @return   string          Directory path
     */
    protected function _getDataDirectory()
    {
        return ROOT_PATH . '/data/projects';
    }

    /**
     * Build a project instance from a file
     *
     * @param   string      $filePath       File path
     * @return  \Bo\Project                 Project instance
     */
    protected function _buildProjectFromFile($filePath)
    {
        if (!is_readable($filePath)) {
            throw new \Exception("The file $filePath is not readable");
        }

        // Get the file content
        $fileContent = file_get_contents($filePath);

        // Create the project instance
        $project = new Project();
        $project->unserializeJson($fileContent);

        return $project;
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
            $lastId = $this->_database->querySingle('SELECT id FROM projects ORDER BY id DESC LIMIT 1');
            if ($lastId) {
                $id = $lastId + 1;
            }
            return $id;
        } catch (\Exception $exception) {
        }

        // Check the data directory for the next id
        $directory  = $this->_getDataDirectory();
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
     * @param   \Vo\Project     $project        Project instance
     */
    public function _databaseAdd(Project $project)
    {
        // Prepare the query
        $statement = $this->_database->prepare(
            'INSERT INTO projects
                (id, creationDate, modificationDate, enabled, codeName, name, description)
             VALUES
                (:id, :creationDate, :modificationDate, :enabled, :codeName, :name, :description)'
        );
        $statement->bindValue(':id',                $project->id,               SQLITE3_INTEGER);
        $statement->bindValue(':creationDate',      $project->creationDate,     SQLITE3_INTEGER);
        $statement->bindValue(':modificationDate',  $project->modificationDate, SQLITE3_INTEGER);
        $statement->bindValue(':enabled',           $project->enabled,          SQLITE3_INTEGER);
        $statement->bindValue(':codeName',          $project->codeName,         SQLITE3_TEXT);
        $statement->bindValue(':name',              $project->name,             SQLITE3_TEXT);
        $statement->bindValue(':description',       $project->description,      SQLITE3_TEXT);

        // Execute the query
        $statement->execute();
    }

    /**
     * Update the database
     *
     * @param   \Vo\Project     $project        Project instance
     */
    public function _databaseUpdate(Project $project)
    {
        // Prepare the query
        $statement = $this->_database->prepare(
            'UPDATE projects
             SET
                creationDate        = :creationDate,
                modificationDate    = :modificationDate,
                enabled             = :enabled,
                codeName            = :codeName,
                name                = :name,
                description         = :description
             WHERE
                id                  = :id'
        );
        $statement->bindValue(':id',                $project->id,               SQLITE3_INTEGER);
        $statement->bindValue(':creationDate',      $project->creationDate,     SQLITE3_INTEGER);
        $statement->bindValue(':modificationDate',  $project->modificationDate, SQLITE3_INTEGER);
        $statement->bindValue(':enabled',           $project->enabled,          SQLITE3_INTEGER);
        $statement->bindValue(':codeName',          $project->codeName,         SQLITE3_TEXT);
        $statement->bindValue(':name',              $project->name,             SQLITE3_TEXT);
        $statement->bindValue(':description',       $project->description,      SQLITE3_TEXT);

        // Execute the query
        $statement->execute();
    }
}
