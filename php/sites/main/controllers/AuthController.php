<?php
require_once __DIR__ . '/AbstractController.php';

use \Bo\User;
use \Dao\User as DaoUser;

/**
 * Authentication actions
 */
class AuthController extends AbstractController
{
    /**
     * Login
     */
    public function loginAction()
    {
        // Handle the form
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $parameters = $this->request->parameters;
            $email      = '';
            $password   = '';
            $daoUser    = DaoUser::getInstance();
            $auth       = Auth::getInstance();

            if (isset($parameters['email'])) {
                $email = $parameters['email'];
            }
            if (isset($parameters['password'])) {
                $password = $parameters['password'];
            }

            // Check the passwords
            $password   = $auth->getPasswordHash($password);
            $user       = $daoUser->getByEmail($email);
            if ($user instanceof User) {
                // The user exists
                if ($user->password === $password) {
                    // The passwords match

                    // The user is logged in and redirected to the home
                    $auth->currentUser = $user;
                    $this->redirect('home');
                } else {
                    // The passwords do not match
                }
            } else {
                // The user does not exist
            }
        }

        // Render
        $this->view->formAction = $this->link('login');
        $this->render('auth/login');
    }

    /**
     * Logout
     */
    public function logoutAction()
    {
        // Logout
        $auth               = Auth::getInstance();
        $auth->currentUser  = null;

        // Redirect to the home
        $this->redirect('home');
    }
}
