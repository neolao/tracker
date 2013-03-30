<?php
require_once __DIR__ . '/AbstractController.php';

/**
 * Milestone pages
 */
class MilestoneController extends AbstractController
{
    /**
     * All milestones
     */
    public function allAction()
    {
        // Check ACL
        if (!$this->isAllowed('main.milestones')) {
            $this->forward('error', 'http401');
        }

        // Render
        $this->render('milestones/all');
    }
}
