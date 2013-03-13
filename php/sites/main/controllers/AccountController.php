<?php
require_once __DIR__ . '/AbstractController.php';

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
        if (!$this->isAllowed('main.profile')) {
            $this->forward('error', 'http401');
        }

        // Get the user instance
        $auth           = Auth::getInstance();
        $currentUser    = $auth->currentUser;


        // Render
        $this->view->userEmail      = $currentUser->email;
        $this->view->userNickname   = $currentUser->nickname;
        $this->render('account/profile');
    }
}
