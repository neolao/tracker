<?php

use \Neolao\Site\Controller;
use \Neolao\Site\Request;
use \Neolao\Util\String as StringUtil;
use \Auth;
use \Vo\User;

/**
 * Abstract controller
 * It is used for common methods
 */
abstract class AbstractController extends Controller
{
    /**
     * The current user instance
     *
     * @var \Vo\User
     */
    public $currentUser;

    /**
     * Indicates that the user is logged
     *
     * @var bool
     */
    public $isLogged = false;

    /**
     * The locale string
     *
     * @var string
     */
    public $localeString;

    /**
     * The language
     *
     * @var string
     */
    public $language;

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

        // Get the locale
        $localeString               = $this->site->localeString;
        $language                   = StringUtil::getLanguage($localeString);
        $this->localeString         = $localeString;
        $this->language             = $language;
        $this->view->localeString   = $localeString;
        $this->view->language       = $language;

        // Some informations from the configuration
        $configGeneral                      = \ConfigGeneral::getInstance();
        $profile                            = $configGeneral->profile;
        $this->view->isProfileDevelopment   = ($profile === 'development');
        $this->view->isProfileProduction    = ($profile === 'production');

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
        $acl = $this->helpers->getAcl();

        // Get the role of the current user
        $auth           = Auth::getInstance();
        $currentUser    = $auth->currentUser;
        $role           = 'guest';
        if ($currentUser instanceof User) {
            $role = $currentUser->getAclRole();
        }

        // Check the ACL
        if ($acl->isAllowed($role, $resource, $privilege)) {
            return true;
        }
        return false;
    }
}
