<?php
/**
 * @package Neolao\Site\Helper\Controller
 */
namespace Neolao\Site\Helper\Controller;

/**
 * ACL helper
 */
class AclHelper extends AbstractHelper
{
    /**
     * ACL instance
     */
    public $acl;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Initialize the ACL
        $this->acl = new \Neolao\Acl();
    }

    /**
     * Get the ACL instance
     *
     * @return  \Neolao\Acl
     */
    public function main()
    {
        return $this->acl;
    }
}
