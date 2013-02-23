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
        // Check ACL
        $acl = $this->getAcl();
        if (!$acl->isAllowed('guest', 'main.home')) {
            $this->forward('error', 'http401');
        }

        // Render
        $this->render('home');
    }
}
