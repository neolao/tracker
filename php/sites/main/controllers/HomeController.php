<?php

use \Neolao\Site\Controller;
use \Neolao\Logger;

/**
 * Home actions
 */
class HomeController extends Controller
{
    /**
     * Default action
     */
    public function indexAction()
    {
        // Check ACL
        $acl = $this->getAcl();
        if (!$acl->isAllowed('guest', 'main.home')) {
            $this->forward('error', 'http401');
        }

        $logger = Logger::getInstance();
        $logger->debug('test');

        // Render
        $this->render('home');
    }
}
