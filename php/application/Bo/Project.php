<?php
/**
 * Package Bo
 */
namespace Bo;

use \Neolao\Behavior\SerializableJson;

/**
 * Project
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
        $json->codeName     = $this->codeName;
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
}
