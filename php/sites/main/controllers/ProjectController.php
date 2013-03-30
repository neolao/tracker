<?php
require_once __DIR__ . '/AbstractController.php';

/**
 * Project pages
 */
class ProjectController extends AbstractController
{
    /**
     * All projects
     */
    public function allAction()
    {
        // Check ACL
        if (!$this->isAllowed('main.projects')) {
            $this->forward('error', 'http401');
        }

        // Render
        $this->render('projects/all');
    }
}
