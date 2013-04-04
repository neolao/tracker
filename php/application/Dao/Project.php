<?php
namespace Dao;

use \Filter\Project as FilterProject;

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
     */
    public function add(\Bo\Project $project)
    {
        $directory      = $this->getDataDirectory();
        $nextId         = $this->_getNextId();

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
        $list = [];

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
        $project = new \Bo\Project();
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
            if (preg_match('/^([0-9]+)\\.json$/', $filePath, $matches)) {
                $currentId = (int) $matches[1];

                if ($currentId >= $id) {
                    $id = $currentId + 1;
                }
            }
        }

        return $id;
    }
}
