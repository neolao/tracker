<?php
require_once __DIR__ . '/AbstractController.php';

use \Neolao\Site\Request;
use \Vo\User;
use \Bo\User as BoUser;

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
        $request    = $this->request;
        $parameters = $request->parameters;
        $method     = $request->method;
        $errors     = [];

        // Handle the form
        if ($method == Request::METHOD_POST) {
            $email      = '';
            $password   = '';
            $boUser     = BoUser::getInstance();
            $auth       = Auth::getInstance();

            if (isset($parameters['email'])) {
                $email = $parameters['email'];
            }
            if (isset($parameters['password'])) {
                $password = $parameters['password'];
            }

            // Check the passwords
            $password   = $auth->getPasswordHash($password);
            $user       = $boUser->getByEmail($email);
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
        $this->view->errors     = $errors;
        $this->view->hasErrors  = !empty($errors);
        $this->render('auth/login');
    }

    /**
     * Form of the password recovery
     */
    public function recoverPasswordAction()
    {
        $request    = $this->request;
        $parameters = $request->parameters;
        $method     = $request->method;
        $errors     = [];

        // Handle the form
        if ($method == Request::METHOD_POST) {
            $email      = '';
            $boUser     = BoUser::getInstance();

            if (isset($parameters['email'])) {
                $email = $parameters['email'];
            }
        }

        // Render
        $this->view->formAction = $this->link('recoverPassword');
        $this->view->errors     = $errors;
        $this->view->hasErrors  = !empty($errors);
        $this->render('auth/recoverPassword');
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
