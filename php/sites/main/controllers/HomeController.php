<?php
/**
 * Home actions
 */
class HomeController extends \Neolao\Site\Controller
{
    /**
     * Default action
     */
    public function indexAction()
    {
        $this->render('home');
    }
}
