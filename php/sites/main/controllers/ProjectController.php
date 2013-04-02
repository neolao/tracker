<?php
require_once __DIR__ . '/AbstractController.php';

use \Neolao\Site\Request;

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

    /**
     * Create page
     */
    public function createAction()
    {
        $request    = $this->request;
        $method     = $request->method;

        // The user submit the form
        if ($method === Request::METHOD_POST) {
            $this->_submitCreateForm();
        }

        // Render
        $this->render('projects/create');
    }

    /**
     * The create form is submitted
     */
    private function _submitCreateForm()
    {
        $request    = $this->request;
        $parameters = $request->parameters;
        $errors     = [];

        if (!isset($parameters['identifier'])) {
            $errors[] = $this->_('form.error.identifier.empty');
        }

        if (!isset($parameters['name'])) {
            $errors[] = $this->_('form.error.name.empty');
        }

        //$errors[] = $this->_('form.error.unknown');


        $this->view->errors = $errors;
    }
}
