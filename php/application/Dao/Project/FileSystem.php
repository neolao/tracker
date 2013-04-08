<?php
namespace Dao\Project;

use \Filter\Project as FilterProject;
use \Vo\Project;
use \Neolao\Util\String as StringUtil;

/**
 * DAO of projects
 */
class FileSystem implements ProjectInterface
{
    use \Neolao\Mixin\Singleton;

    /**
     * Add a project
     *
     * @param   \Vo\Project $project        Project instance
     * @throws  \Dao\Project\Exception\CreateException
     */
    public function add(Project $project)
    {
        $directory      = $this->_getDataDirectory();
        $nextId         = $this->_getNextId();

        // Update and serialize the project instance
        $project->id    = $nextId;
        $serialized     = $project->serializeJson();

        // Create the file
        if (!is_dir($directory)) {
            mkdir($directory, 0777 - umask(), true);
        }
        $filePath = $directory . '/' . $nextId . '.json';
        file_put_contents($filePath, $serialized);
    }

    /**
     * Get project by id
     *
     * @param   string      $id             Project id
     * @return  \Vo\Project                 Project instance
     */
    public function getById($id)
    {
        // Check the cache

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
        // Check the cache

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
     * @param   string              $orderBy        The property name to sort
     * @param   int                 $count          The list length
     * @param   int                 $offset         The offset
     * @return  array                               Project list
     */
    public function getList(FilterProject $filter = null, $orderBy = null, $count = null, $offset = null)
    {
        $directory  = $this->_getDataDirectory();
        $sortedIds  = [];
        $list       = [];

        // Initialize the parameter "filter"
        if (is_null($filter)) {
            $filter = new FilterProject();
        }

        // Sanitize the parameter "orderBy"
        switch ($orderBy) {
            default:
            case 'id':
                $orderBy = 'id';
                $sortFlag = SORT_NUMERIC;
                break;
            case 'name':
                $orderBy = 'name';
                $sortFlag = SORT_STRING;
                break;
        }


        // Get the projects
        $filePaths  = glob($directory . '/*.json');
        foreach ($filePaths as $filePath) {
            // Build the project instance
            $project = $this->_buildProjectFromFile($filePath);
            $projectId = $project->id;

            // Apply the filter
            if (!$project->matchFilter($filter)) {
                continue;
            }

            // Update the sorted list
            $sortProperty = $project->$orderBy;
            $sortProperty = strtolower($sortProperty);
            $sortProperty = StringUtil::removeAccents($sortProperty);
            $sortedIds[$projectId] = $sortProperty;

            // Add the project to the list
            $list[$projectId] = $project;
        }

        // Sort the filtered projects
        asort($sortedIds, $sortFlag);
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
        $directory      = $this->_getDataDirectory();
        $projectId      = $project->id;
        $filePath       = $directory . '/' . $projectId . '.json';

        // Serialize the project instance
        $serialized = $project->serializeJson();

        // Update the file
        if (!is_dir($directory)) {
            mkdir($directory, 0777 - umask(), true);
        }
        file_put_contents($filePath, $serialized);

    }

    /**
     * Delete a project
     *
     * @param   int         $projectId      Project id
     */
    public function delete($projectId)
    {
        $directory = $this->_getDataDirectory();
        $filePath = $directory . '/' . $projectId . '.json';

        // Delete
        if (is_file($filePath)) {
            unlink($filePath);
        }
    }

    /**
     * Get the directory path of the projects datas
     *
     * @return   string          Directory path
     */
    public function _getDataDirectory()
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
        $directory  = $this->_getDataDirectory();
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
