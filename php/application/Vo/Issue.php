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
     * Issue name
     *
     * @var string
     */
    public $name;

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
        $json               = new \stdClass();
        $json->id           = $this->id;
        $json->name         = $this->name;
        $json->description  = $this->description;

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
     * @param   \Filter\Issue       $filter     Filter
     * @return  bool                            true if the filter matches, false otherwise
     */
    public function matchFilter(FilterIssue $filter)
    {
        return true;
    }
}
