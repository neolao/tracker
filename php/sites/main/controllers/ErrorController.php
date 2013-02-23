<?php
/**
 * Error actions
 */
class ErrorController extends \Neolao\Site\Controller
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
