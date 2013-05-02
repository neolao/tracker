<?php
require_once __DIR__ . '/AbstractController.php';

use \Neolao\Site\Request;
use \Vo\Issue;
use \Bo\Issue as BoIssue;
use \Bo\Issue\Exception\UpdateException;
use \Bo\Project as BoProject;

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
        if (!$this->isAllowed('main.issues', 'read')) {
            $this->forward('error', 'http401');
        }

        // Get the issues
        $boIssue = BoIssue::getInstance();
        $issues = $boIssue->getList();

        // Render
        $this->view->issues = $issues;
        $this->render('issues/all');
    }

    /**
     * Display an issue
     */
    public function sheetAction()
    {
        // Check ACL
        if (!$this->isAllowed('main.issues', 'read')) {
            $this->forward('error', 'http401');
        }


        $request    = $this->request;
        $parameters = $request->parameters;
        $id         = $parameters['id'];

        // Get the issue instance
        $boIssue    = BoIssue::getInstance();
        $issue      = $boIssue->getById($id);
        if ($issue instanceof Issue === false) {
            $this->forward('error', 'http404');
        }

        // Render
        $this->view->issue          = $issue;
        $this->view->editEnabled    = $this->isAllowed('main.issues', 'update');
        $this->render('issues/sheet');
    }

    /**
     * Create form
     */
    public function createAction()
    {
        // Check ACL
        if (!$this->isAllowed('main.issues', 'create')) {
            $this->forward('error', 'http401');
        }


        // Variables
        $request    = $this->request;
        $method     = $request->method;

        // The user submit the form
        if ($method === Request::METHOD_POST) {
            $this->_submitCreateForm();
        }

        // Render
        $this->render('issues/create');
    }

    /**
     * Edit form
     */
    public function editAction()
    {
        // Check ACL
        if (!$this->isAllowed('main.issues', 'update')) {
            $this->forward('error', 'http401');
        }


        $request    = $this->request;
        $parameters = $request->parameters;
        $method     = $request->method;
        $id         = $parameters['id'];

        // Get the issue instance
        $boIssue    = BoIssue::getInstance();
        $issue      = $boIssue->getById($id);
        if ($issue instanceof Issue === false) {
            $this->forward('error', 'http404');
        }

        // The user submit the form
        if ($method === Request::METHOD_POST) {
            $this->_submitEditForm($issue);
        }

        // Get the project list
        $boProject  = BoProject::getInstance();
        $projects   = $boProject->getList();

        // Render
        $this->view->issue      = $issue;
        $this->view->issueName  = $issue->name;
        $this->view->projects   = $projects;
        $this->view->linkEdit   = $this->helpers->link('issue.edit', ['id' => $issue->id]);
        $this->render('issues/edit');
    }

    /**
     * The create form is submitted
     */
    private function _submitCreateForm()
    {
        // Check ACL
        if (!$this->isAllowed('main.issues', 'create')) {
            $this->forward('error', 'http401');
        }


        $request        = $this->request;
        $parameters     = $request->parameters;
        $errors         = [];
        $name           = '';
        $description    = '';

        // Get the name
        if (isset($parameters['name'])) {
            $name = trim($parameters['name']);
        }
        if (empty($name)) {
            $errors[] = $this->_('form.error.name.empty');
        }

        // Get the description
        if (isset($parameters['description'])) {
            $description = $parameters['description'];
        }

        // Create the issue
        if (empty($errors)) {
            try {
                // Build the issue instance
                $issue              = new Issue();
                $issue->name        = $name;
                $issue->description = $description;

                // Add the issue
                $boIssue = BoIssue::getInstance();
                $boIssue->add($issue);

                // Redirect to the project page
                $this->redirect('issue', ['id' => $issue->id]);

            } catch (\Exception $exception) {
                $errors[] = $this->_('form.error.unknown');
                $this->getLogger()->error($exception->getMessage());
            }
        }


        // View parameters
        $this->view->name           = $name;
        $this->view->description    = $description;
        $this->view->hasErrors      = !empty($errors);
        $this->view->errors         = $errors;
    }

    /**
     * The edit form is submitted
     *
     * @param   \Vo\Issue       $issue      Issue instance
     */
    private function _submitEditForm(Issue $issue)
    {
        // Check ACL
        if (!$this->isAllowed('main.issues', 'update')) {
            $this->forward('error', 'http401');
        }


        $request        = $this->request;
        $parameters     = $request->parameters;
        $errors         = [];
        $name           = '';
        $description    = '';

        // Get the name
        if (isset($parameters['name'])) {
            $name = trim($parameters['name']);
        }
        if (empty($name)) {
            $errors[] = $this->_('form.error.name.empty');
        }

        // Get the description
        if (isset($parameters['description'])) {
            $description = $parameters['description'];
        }

        // Update the issue
        if (empty($errors)) {
            try {
                // Update the issue instance
                $issue->name            = $name;
                $issue->description     = $description;

                // Update the database
                $boIssue = BoIssue::getInstance();
                $boIssue->update($issue);

                // Redirect to the issue page
                $this->redirect('issue', ['id' => $issue->id]);

            } catch (UpdateException $exception) {
                $exceptionCode = $exception->getCode();
                switch ($exceptionCode) {
                    case UpdateException::ISSUE_NOT_FOUND:
                        $this->forward('error', 'http404');
                        break;
                    default:
                        $errors[] = $this->_('form.error.unknown');
                        $this->getLogger()->error($exception->getMessage());
                }
            } catch (\Exception $exception) {
                $errors[] = $this->_('form.error.unknown');
                $this->getLogger()->error($exception->getMessage());
            }
        }


        // View parameters
        $this->view->hasErrors      = !empty($errors);
        $this->view->errors         = $errors;
    }

}
