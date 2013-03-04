<?php
require_once __DIR__ . '/AbstractController.php';

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
        // Render
        $this->render('auth/login');
    }

    /**
     * Logout
     */
    public function logoutAction()
    {
    }
}
