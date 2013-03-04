<?php
require_once __DIR__ . '/AbstractController.php';

/**
 * Home actions
 */
class HomeController extends AbstractController
{
    /**
     * Default action
     */
    public function indexAction()
    {
        // Check ACL
        if (!$this->isAllowed('main.home')) {
            $this->forward('error', 'http401');
        }

        // Render
        $this->render('home');
    }
}
