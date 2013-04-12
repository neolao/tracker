<?php
namespace Bo;

use \Bo\Project\Exception\CreateException;
use \Bo\Project\Exception\UpdateException;
use \Dao\Project as DaoProject;
use \Vo\Project as VoProject;
use \Filter\Project as FilterProject;

/**
 * Business Object to work with projects
 */
class Project
{
    use \Neolao\Mixin\Singleton;

    /**
     * Instance of DAO project
     *
     * @var \Dao\Project\ProjectInterface
     */
    protected $_daoProject;

    /**
     * Constructor
     */
    protected function __construct()
    {
        $this->_daoProject = DaoProject::factory();
    }

    /**
     * Add a project
     *
     * @param   \Vo\Project $project        Project instance
     * @throws  \Bo\Project\Exception\CreateException
     */
    public function add(VoProject $project)
    {
        // Check if the code name already exists
        try {
            $projectFound = $this->_daoProject->getByCodeName($project->codeName);
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            throw new CreateException($message, CreateException::UNKNOWN, $exception);
        }
        if (!is_null($projectFound)) {
            throw new CreateException('The project already exists: ' . $project->codeName, CreateException::CODENAME_ALREADY_EXISTS);
        }

        // Add the project into the database
        try {
            // Update the project
            $project->creationDate      = time();
            $project->modificationDate  = time();

            $this->_daoProject->add($project);
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            throw new CreateException($message, CreateException::UNKNOWN, $exception);
        }
    }

    /**
     * Get project by id
     *
     * @param   string      $id             Project id
     * @return  \Vo\Project                 Project instance
     */
    public function getById($id)
    {
        $project = $this->_daoProject->getById($id);

        return $project;
    }

    /**
     * Get project by code name
     *
     * @param   string      $codeName       Project code name
     * @return  \Vo\Project                 Project instance
     */
    public function getByCodeName($codeName)
    {
        $project = $this->_daoProject->getByCodeName($codeName);

        return $project;
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
        $list = $this->_daoProject->getList($filter, $orderBy, $count, $offset);

        return $list;
    }

    /**
     * Update a project
     *
     * @param   \Vo\Project     $project    Project instance
     * @throws  \Bo\Project\Exception\UpdateException
     */
    public function update(VoProject $project)
    {
        // Check if the code name already exists
        try {
            $projectFound = $this->_daoProject->getByCodeName($project->codeName);
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            throw new UpdateException($message, UpdateException::UNKNOWN, $exception);
        }
        if (!is_object($projectFound)) {
            throw new UpdateException('Project not found: ' . $project->id, UpdateException::PROJECT_NOT_FOUND);
        } else if ($projectFound->id !== $project->id) {
            throw new UpdateException('The project already exists: ' . $project->codeName, UpdateException::CODENAME_ALREADY_EXISTS);
        }

        // Update the project in the database
        try {
            $project->modificationDate = time();
            $this->_daoProject->update($project);
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            throw new UpdateException($message, UpdateException::UNKNOWN, $exception);
        }
    }

    /**
     * Delete a project
     *
     * @param   int         $projectId      Project id
     */
    public function delete($projectId)
    {
        // Delete the issues
        // @todo

        // Delete the milestones
        // @todo

        // Delete the project
        $this->_daoProject->delete($projectId);
    }
}
