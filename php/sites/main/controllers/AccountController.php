<?php
require_once __DIR__ . '/AbstractController.php';

use \Neolao\Site\Request;
use \Bo\User as BoUser;

/**
 * Account pages
 */
class AccountController extends AbstractController
{
    /**
     * Profile page
     */
    public function profileAction()
    {
        // Check ACL
        if (!$this->isAllowed('main.profile', 'read')) {
            $this->forward('error', 'http401');
        }

        // Get the user instance
        $auth           = Auth::getInstance();
        $currentUser    = $auth->currentUser;

        // Render
        $this->view->user = $currentUser;
        $this->render('account/profile');
    }

    /**
     * Form of the profile edition
     */
    public function editProfileAction()
    {
        // Check ACL
        if (!$this->isAllowed('main.profile', 'update')) {
            $this->forward('error', 'http401');
        }

        $request    = $this->request;
        $parameters = $request->parameters;
        $method     = $request->method;
        $errors     = [];

        // Get the user instance
        $auth       = Auth::getInstance();
        $user       = $auth->currentUser;

        // Handle the form
        if ($method == Request::METHOD_POST) {
            // Update the nickname
            if (isset($parameters['nickname'])) {
                $user->nickname = $parameters['nickname'];
            }

            // Update the database
            $boUser = BoUser::getInstance();
            $boUser->update($user);

            // Redirect to the profile page
            $this->redirect('profile');
        }

        // Render
        $this->view->user       = $user;
        $this->view->errors     = $errors;
        $this->view->hasErrors  = !empty($errors);
        $this->render('account/profileEdit');

    }
}
