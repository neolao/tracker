<?php
require_once __DIR__ . '/AbstractController.php';

/**
 * Issue pages
 */
class IssueController extends AbstractController
{
    /**
     * All issues
     */
    public function allAction()
    {
        // Check ACL
        if (!$this->isAllowed('main.issues')) {
            $this->forward('error', 'http401');
        }

        // Render
        $this->render('issues/all');
    }
}
