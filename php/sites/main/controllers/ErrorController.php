<?php
require_once __DIR__ . '/AbstractController.php';

/**
 * Error actions
 */
class ErrorController extends AbstractController
{
    /**
     * Default action
     */
    public function indexAction()
    {
        $this->render('errors/index');
    }

    /**
     * HTTP 401
     */
    public function http401Action()
    {
        $this->render('errors/401');
    }

    /**
     * HTTP 404
     */
    public function http404Action()
    {
        $this->render('errors/404');
    }
}
