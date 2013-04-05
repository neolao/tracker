<?php
namespace Dao;

use \Filter\Project as FilterProject;
use \Bo\Project as BoProject;
use \Dao\Project\Exception\CreateException;

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

        // Update and serialize the project isntance
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
     * Get project by identifier
     *
     * @param   string      $identifier     Project identifier
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
     * @param   \Filter\Project     $filter         Filter
     * @return  array                               Project list
     */
    public function getList(FilterProject $filter)
    {
        $directory  = $this->getDataDirectory();
        $list       = [];

        // Get the projects
        $filePaths  = glob($directory . '/*.json');
        foreach ($filePaths as $filePath) {
            // Build the project instance
            $project = $this->_buildProjectFromFile($filePath);

            // Apply the filter

            // Add the project to the list
            $list[] = $project;
        }

        return $list;
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
