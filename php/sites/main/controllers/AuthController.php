<?php
require_once __DIR__ . '/AbstractController.php';

use \Neolao\Site\Request;
use \Vo\User;
use \Bo\User as BoUser;
use \Mail;
use \Mail\Message\PasswordRecovery as PasswordRecoveryMessage;
use \Mail\Message\RegisterConfirmation as RegisterConfirmationMessage;

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

        // Handle the form
        if ($method == Request::METHOD_POST) {
            if (isset($parameters['signup'])) {
                $this->_submitRegisterForm();
            } else {
                $this->_submitLoginForm();
            }
        }

        // Render
        $this->view->formAction = $this->link('login');
        $this->render('auth/login');
    }

    /**
     * The user clicks on the confirmation link
     */
    public function registerConfirmationAction()
    {
        $request    = $this->request;
        $parameters = $request->parameters;
        $userId     = (int) $parameters['id'];
        $hash       = $parameters['hash'];

        // Get the user
        $boUser = BoUser::getInstance();
        $user   = $boUser->getById($userId);

        if ($user instanceof User === false) {
            // The user is not found
            $this->render('auth/registerConfirmationError');
        } else if ($hash !== $boUser->getRegisterConfirmationHash($user)) {
            // The hashes do not match
            $this->render('auth/registerConfirmationError');
        } else if ($user->confirmed) {
            // the link can be used once
            $this->render('auth/registerConfirmationError');
        }

        // Update the user
        $boUser->confirm($user);

        // Connect the user
        $auth = Auth::getInstance();
        $auth->currentUser = $user;

        // Render
        $this->view->isLogged = true;
        $this->render('auth/registerConfirmation');
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
            // The user must provide an email
            if (!isset($parameters['email'])) {
                $errors[] = $this->_('form.error.email.empty');
            }

            if (empty($errors)) {
                // Sanitize the email
                $email  = $parameters['email'];
                $email  = trim($email);
                $email  = strtolower($email);

                // Get the user
                $boUser = BoUser::getInstance();
                $user   = $boUser->getByEmail($email);

                if ($user instanceof User === false) {
                    // The user is not found
                    $errors[] = $this->_('form.error.email.notFound');
                } else {
                    // Send an email to the user to reset his password
                    $mailProvider   = Mail::providerFactory();
                    $message        = new PasswordRecoveryMessage($user, $this->language);
                    $mailProvider->send($message);

                    // Display a confirm message
                    $this->render('auth/recoverPasswordConfirm');
                }
            }
        }

        // Render
        $this->view->formAction = $this->link('recoverPassword');
        $this->view->errors     = $errors;
        $this->view->hasErrors  = !empty($errors);
        $this->render('auth/recoverPassword');
    }

    /**
     * Form to change the password
     */
    public function changePasswordAction()
    {
        $request    = $this->request;
        $parameters = $request->parameters;
        $method     = $request->method;
        $errors     = [];

        // Check if the hash is valid
        $boUser = BoUser::getInstance();
        $id     = (int) $parameters['id'];
        $hash   = $parameters['hash'];
        $user   = $boUser->getById($id);
        if ($user instanceof User === false) {
            $this->forward('error', 'http401');
        }
        $userHash = $boUser->getRecoveryHash($user);
        if ($userHash !== $hash) {
            $this->forward('error', 'http401');
        }

        // Handle the form
        if ($method == Request::METHOD_POST) {
            // Get parameters
            $password           = '';
            $passwordConfirm    = '';
            if (isset($parameters['password'])) {
                $password = $parameters['password'];
            }
            if (isset($parameters['passwordConfirm'])) {
                $passwordConfirm = $parameters['passwordConfirm'];
            }

            // Check the parameters
            if (empty($password)) {
                $errors[] = $this->_('form.error.password.empty');
            } else if (strlen($password) < 6) {
                $errors[] = $this->_('form.error.password.invalid');
            } else if ($password !== $passwordConfirm) {
                $errors[] = $this->_('form.error.passwordConfirm.notMatch');
            }

            // Change the password
            if (empty($errors)) {
                $auth = Auth::getInstance();
                $newPassword = $auth->getPasswordHash($password);
                $boUser->changePassword($user, $newPassword);

                // Login
                $auth->currentUser = $user;

                // Redirect to the profile
                $this->redirect('profile');
            }
        }

        // Render
        $this->view->formAction = $this->link('changePassword', ['id' => $id, 'hash' => $hash]);
        $this->view->errors     = $errors;
        $this->view->hasErrors  = !empty($errors);
        $this->render('auth/changePassword');
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

    /**
     * The user submit the login form
     */
    protected function _submitLoginForm()
    {
        $request    = $this->request;
        $parameters = $request->parameters;
        $boUser     = BoUser::getInstance();
        $auth       = Auth::getInstance();
        $email      = '';
        $password   = '';
        $error      = null;

        // Get the parameters
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
                $error = $this->_('form.error.password.incorrect');
            }
        } else {
            // The user does not exist
            $error = $this->_('form.error.email.notFound');
        }

        // Render
        $this->view->formAction = $this->link('login');
        $this->view->error      = $error;
        $this->view->hasError   = !empty($error);
        $this->render('auth/login');
    }

    /**
     * The user submit the register form
     */
    protected function _submitRegisterForm()
    {
        $request    = $this->request;
        $parameters = $request->parameters;
        $boUser     = BoUser::getInstance();
        $auth       = Auth::getInstance();
        $email      = '';
        $password   = '';
        $error      = null;

        // Get the parameters
        if (isset($parameters['email'])) {
            $email = $parameters['email'];
        }
        if (isset($parameters['password'])) {
            $password = $parameters['password'];
        }

        // Check the parameters
        if (empty($email)) {
            $error = $this->_('form.error.email.empty');
        } else {
            $user = $boUser->getByEmail($email);
            if ($user instanceof User) {
                $error = $this->_('form.error.email.exists');
            }
        }
        if (empty($password)) {
            $error = $this->_('form.error.password.empty');
        } else if (strlen($password) < 6) {
            $error = $this->_('form.error.password.invalid');
        }

        // Create the user
        if (is_null($error)) {
            $user               = new User();
            $user->email        = $email;
            $user->password     = $auth->getPasswordHash($password);
            $user->confirmed    = false;
            $boUser->add($user);

            // Send an email to the user for the confirmation
            $mailProvider   = Mail::providerFactory();
            $message        = new RegisterConfirmationMessage($user, $this->language);
            $mailProvider->send($message);

            // Render
            $this->render('auth/registerMessage');
        }

        // Render
        $this->view->formAction = $this->link('login');
        $this->view->error      = $error;
        $this->view->hasError   = !empty($error);
        $this->render('auth/login');

    }

}
