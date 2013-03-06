<?php
/**
 * Package Bo
 */
namespace Bo;

use \Neolao\Behavior\SerializableJson;

/**
 * User
 */
class User implements SerializableJson
{
    /**
     * User email
     *
     * @var string
     */
    public $email;

    /**
     * User password (hash)
     *
     * @var string
     */
    public $password;

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
        $json           = new \stdClass();
        $json->email    = $this->email;


        return $json;
    }

    /**
     * Unserialize from a json
     *
     * @param   string      $json       json string
     */
    public function unserializeJson($json)
    {
        $json = json_decode($json);

        // Email
        if (isset($json->email)) {
            $this->email = (string) $json->email;
        }

        // Password
        if (isset($json->password)) {
            $this->password = (string) $json->password;
        }

    }
}
