<?php
namespace Vo;

use \Neolao\Behavior\SerializableJson;

/**
 * Value Object: User
 */
class User implements SerializableJson
{
    /**
     * User id
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
     * User nickname
     *
     * @var string
     */
    public $nickname;

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
        $json->email            = $this->email;
        $json->password         = $this->password;
        $json->nickname         = $this->nickname;

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

        // Email
        if (isset($json->email)) {
            $this->email = (string) $json->email;
        }

        // Password
        if (isset($json->password)) {
            $this->password = (string) $json->password;
        }

        // Nickname
        if (isset($json->nickname)) {
            $this->nickname = (string) $json->nickname;
        }

    }

    /**
     * Get the role of the ACL
     *
     * @return  string                  The role
     */
    public function getAclRole()
    {
        $role = 'admin';

        return $role;
    }
}
