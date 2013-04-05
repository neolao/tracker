<?php
namespace Dao;

use \Filter\Project as FilterProject;
use \Bo\Project as BoProject;
use \Dao\Project\Exception\CreateException;
use \Dao\Project\Exception\EditException;
use \Neolao\Util\String as StringUtil;

/**
 * DAO of projects
 */
class Project
{
    use \Neolao\Mixin\Singleton;

    /**
     * Get the directory path of the projects datas
     *
     * @return   string          Directory path
     */
    public function getDataDirectory()
    {
        return ROOT_PATH . '/data/projects';
    }

    /**
     * Add a project
     *
     * @param   \Bo\Project $project        Project instance
     * @throws  \Dao\Project\Exception\CreateException
     */
    public function add(BoProject $project)
    {
        $directory      = $this->getDataDirectory();
        $nextId         = $this->_getNextId();

        // Check if the code name already exists
        $projectFound = $this->getByCodeName($project->codeName);
        if ($projectFound instanceof BoProject) {
            throw new CreateException('The project already exists: ' . $project->codeName, CreateException::CODENAME_ALREADY_EXISTS);
        }

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
     * @return  \Bo\Project                 Project instance
     */
    public function getById($id)
    {
        // Check the cache

        // Search in the directory
        $directory = $this->getDataDirectory();
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
     * @return  \Bo\Project                 Project instance
     */
    public function getByCodeName($codeName)
    {
        // Check the cache

        // Search in the directory
        $directory  = $this->getDataDirectory();
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
        $directory  = $this->getDataDirectory();
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
     * @param   \Bo\Project     $project    Project instance
     */
    public function update(BoProject $project)
    {
        $directory      = $this->getDataDirectory();
        $projectId      = $project->id;
        $filePath       = $directory . '/' . $projectId . '.json';

        // Check if the file exists
        if (!is_file($filePath)) {
            throw new EditException('Project not found: ' . $projectId, EditException::PROJECT_NOT_FOUND);
        }

        // Check if the code name already exists
        $projectFound = $this->getByCodeName($project->codeName);
        if ($projectFound instanceof BoProject && $projectFound->id !== $projectId) {
            throw new EditException('The project already exists: ' . $project->codeName, EditException::CODENAME_ALREADY_EXISTS);
        }

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
        $directory = $this->getDataDirectory();
        $filePath = $directory . '/' . $projectId . '.json';

        // Delete
        if (is_file($filePath)) {
            unlink($filePath);
        }
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
        $project = new BoProject();
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
