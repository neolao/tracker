<?php
namespace Bo;

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
     * DAO instance
     *
     * @var \Dao\Project\ProjectInterface
     */
    protected $_daoProject;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_daoProject = DaoProject::factory();
    }

    /**
     * Add a project
     *
     * @param   \Vo\Project $project        Project instance
     * @throws  \Dao\Project\Exception\CreateException
     */
    public function add(VoProject $project)
    {
        $this->_daoProject->add($project);
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
     * @param   string              $orderBy        The property name to sort
     * @param   int                 $count          The list length
     * @param   int                 $offset         The offset
     * @return  array                               Project list
     */
    public function getList(FilterProject $filter = null, $orderBy = null, $count = null, $offset = null)
    {
        $list = $this->_daoProject->getList($filter, $orderBy, $count, $offset);

        return $list;
    }

    /**
     * Update a project
     *
     * @param   \Vo\Project     $project    Project instance
     * @throws  \Dao\Project\Exception\UpdateException
     */
    public function update(VoProject $project)
    {
        $this->_daoProject->update($project);
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
