<?php

use \Neolao\Site\Controller;
use \Neolao\Site\Request;
use \Auth;

/**
 * Abstract controller
 * It is used for common methods
 */
abstract class AbstractController extends Controller
{
    /**
     * Dispatch request
     *
     * @param   \Neolao\Site\Request    $request    HTTP Request
     */
    public function dispatch(Request $request)
    {
        // Set the current user in the view and the controller
        $auth                       = Auth::getInstance();
        $currentUser                = $auth->currentUser;
        $isLogged                   = ($currentUser);
        $this->currentUser          = $currentUser;
        $this->isLogged             = $isLogged;
        $this->view->currentUser    = $currentUser;
        $this->view->isLogged       = $isLogged;

        // Dispatch
        parent::dispatch($request);
    }

    /**
     * Indicates that the current user is allowed to access the specified resource
     *
     * @param   string      $resource       The resource name
     * @param   string      $privilege      The privilege name
     * @return  bool                        true if the current user is allowed, false otherwise
     */
    public function isAllowed($resource, $privilege = '*')
    {
        // Get the ACL instance from the helper
        $acl = $this->getAcl();

        // Get the role of the current user
        // @todo
        $role = 'guest';

        // Check the ACL
        if ($acl->isAllowed($role, $resource, $privilege)) {
            return true;
        }
        return false;
    }
}