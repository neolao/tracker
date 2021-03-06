<?php
namespace Vo;

use \Neolao\Behavior\SerializableJson;
use \Filter\Project as FilterProject;

/**
 * Value Object: Project
 */
class Project implements SerializableJson
{
    /**
     * Project id
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
     * Indicates that the project is enabled
     *
     * @var bool
     */
    public $enabled;

    /**
     * Project code name
     *
     * @var string
     */
    public $codeName;

    /**
     * Project name
     *
     * @var string
     */
    public $name;

    /**
     * Project description
     *
     * @var string
     */
    public $description;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->enabled = true;
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
        $json->enabled          = $this->enabled;
        $json->codeName         = $this->codeName;
        $json->name             = $this->name;
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

        // Enabled
        if (isset($json->enabled)) {
            $this->enabled = ($json->enabled == 'true')?true:false;
        }

        // Code name
        if (isset($json->codeName)) {
            $this->codeName = (string) $json->codeName;
        }

        // Name
        if (isset($json->name)) {
            $this->name = (string) $json->name;
        }

        // Description
        if (isset($json->description)) {
            $this->description = (string) $json->description;
        }
    }

    /**
     * Check if a filter matches the project
     *
     * @param   \Filter\Project     $filter     Filter
     * @return  bool                            true if the filter matches, false otherwise
     */
    public function matchFilter(FilterProject $filter)
    {
        // Check the property "enabled"
        if (!is_null($filter->enabled)) {
            if ($filter->enabled !== $this->enabled) {
                return false;
            }
        }

        return true;
    }
}
