<?php
namespace Dao\Project;

use \Vo\Project;
use \Filter\Project as FilterProject;

/**
 * Interface of a DAO of projects
 */
interface ProjectInterface extends \Neolao\Behavior\Singleton
{
    /**
     * Add a project
     *
     * @param   \Vo\Project $project        Project instance
     */
    function add(Project $project);

    /**
     * Get project by id
     *
     * @param   string      $id             Project id
     * @return  \Vo\Project                 Project instance
     */
    function getById($id);

    /**
     * Get project by code name
     *
     * @param   string      $codeName       Project code name
     * @return  \Vo\Project                 Project instance
     */
    function getByCodeName($codeName);

    /**
     * Get a project list
     *
     * @param   \Filter\Project     $filter         The filter
     * @param   array               $orderBy        The properties to sort
     * @param   int                 $count          The list length
     * @param   int                 $offset         The offset
     * @return  array                               Project list
     */
    function getList(FilterProject $filter = null, array $orderBy = null, $count = null, $offset = null);

    /**
     * Update a project
     *
     * @param   \Vo\Project     $project    Project instance
     */
    function update(Project $project);

    /**
     * Delete a project
     *
     * @param   int         $projectId      Project id
     */
    function delete($projectId);

}
