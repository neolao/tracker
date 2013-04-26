<?php
namespace Vo;

use \Neolao\Behavior\SerializableJson;
use \Filter\Issue as FilterIssue;

/**
 * Value Object: Issue
 */
class Issue implements SerializableJson
{
    /**
     * Issue id
     *
     * @var int
     */
    public $id;

    /**
     * Date of the creation (timestamp)
     *
     * @var int
     */
    public $creationDate;

    /**
     * Date of the modification (timestamp)
     *
     * @var int
     */
    public $modificationDate;

    /**
     * Issue name
     *
     * @var string
     */
    public $name;

    /**
     * Status
     *
     * @var int
     */
    public $status;

    /**
     * Project id
     *
     * @var int
     */
    public $projectId;

    /**
     * Milestone id
     *
     * @var int
     */
    public $milestoneId;

    /**
     * The user id of the responsible
     *
     * @var int
     */
    public $assignedUserId;

    /**
     * Priority
     *
     * @var int
     */
    public $priority;

    /**
     * Labels
     *
     * @var array
     */
    public $labels;

    /**
     * Start date of the issue (timestamp)
     *
     * @var int
     */
    public $startDate;

    /**
     * Due date of the issue (timestamp)
     *
     * @var int
     */
    public $dueDate;

    /**
     * Percentage of the progression
     *
     * @var int
     */
    public $progression;

    /**
     * Issue description
     *
     * @var string
     */
    public $description;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Serialize to a json format
     *
     * @return  string                  json string
     */
    public function serializeJson()
    {
        $json                   = new \stdClass();
        $json->id               = $this->id;
        $json->creationDate     = $this->creationDate;
        $json->modificationDate = $this->modificationDate;
        $json->name             = $this->name;
        $json->status           = $this->status;
        $json->projectId        = $this->projectId;
        $json->milestoneId      = $this->milestoneId;
        $json->assignedUserId   = $this->assignedUserId;
        $json->priority         = $this->priority;
        $json->labels           = $this->labels;
        $json->startDate        = $this->startDate;
        $json->dueDate          = $this->dueDate;
        $json->progression      = $this->progression;
        $json->description      = $this->description;

        return json_encode($json);
    }

    /**
     * Unserialize from a json
     *
     * @param   string      $json       json string
     */
    public function unserializeJson($json)
    {
        $json = json_decode($json);

        // Id
        if (isset($json->id)) {
            $this->id = (int) $json->id;
        }

        // Creation date
        if (isset($json->creationDate)) {
            $this->creationDate = (int) $json->creationDate;
        }

        // Modification date
        if (isset($json->modificationDate)) {
            $this->modificationDate = (int) $json->modificationDate;
        }

        // Name
        if (isset($json->name)) {
            $this->name = (string) $json->name;
        }

        // Status
        if (isset($json->status)) {
            $this->status = (int) $json->status;
        }

        // Project id
        if (isset($json->projectId)) {
            $this->projectId = (int) $json->projectId;
        }

        // Milestone id
        if (isset($json->milestoneId)) {
            $this->milestoneId = (int) $json->milestoneId;
        }

        // User id of the responsible
        if (isset($json->assignedUserId)) {
            $this->assignedUserId = (int) $json->assignedUserId;
        }

        // Priority
        if (isset($json->priority)) {
            $this->priority = (int) $json->priority;
        }

        // Labels
        if (isset($json->labels) && is_array($json->labels)) {
            $this->labels = $json->labels;
        }

        // Start date
        if (isset($json->startDate)) {
            $this->startDate = (int) $json->startDate;
        }

        // Due date
        if (isset($json->dueDate)) {
            $this->dueDate = (int) $json->dueDate;
        }

        // Progression
        if (isset($json->progression)) {
            $this->progression = (int) $json->progression;
        }

        // Description
        if (isset($json->description)) {
            $this->description = (string) $json->description;
        }
    }

    /**
     * Check if a filter matches the project
     *
     * @param   \Filter\Issue       $filter     Filter
     * @return  bool                            true if the filter matches, false otherwise
     */
    public function matchFilter(FilterIssue $filter)
    {
        return true;
    }
}
