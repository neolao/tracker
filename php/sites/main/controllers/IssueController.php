<?php
require_once __DIR__ . '/AbstractController.php';

use \Neolao\Site\Request;
use \Bo\Issue;
use \Dao\Issue as DaoIssue;
use \Dao\Issue\Exception\UpdateException;

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

    /**
     * Display an issue
     */
    public function sheetAction()
    {
        $request    = $this->request;
        $parameters = $request->parameters;
        $id         = $parameters['id'];

        // Get the issue instance
        $daoIssue   = DaoIssue::getInstance();
        $issue      = $daoIssue->getById($id);
        if ($issue instanceof Issue === false) {
            $this->forward('error', 'http404');
        }

        // Render
        $this->view->issue      = $issue;
        $this->view->linkEdit   = $this->link('issue.edit', ['id' => $issue->id]);

        // Render
        $this->render('issues/sheet');
    }

    /**
     * Create form
     */
    public function createAction()
    {
        // Check ACL
        // @todo

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
        $request    = $this->request;
        $parameters = $request->parameters;
        $method     = $request->method;
        $id         = $parameters['id'];

        // Get the issue instance
        $daoIssue   = DaoIssue::getInstance();
        $issue      = $daoIssue->getById($id);
        if ($issue instanceof Issue === false) {
            $this->forward('error', 'http404');
        }

        // The user submit the form
        if ($method === Request::METHOD_POST) {
            $this->_submitEditForm($issue);
        }

        // Render
        $this->view->issue      = $issue;
        $this->view->issueName  = $issue->name;
        $this->view->linkEdit   = $this->link('issue.edit', ['id' => $issue->id]);
        $this->render('issues/edit');
    }

    /**
     * The create form is submitted
     */
    private function _submitCreateForm()
    {
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
                $daoIssue = DaoIssue::getInstance();
                $daoIssue->add($issue);

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
     * @param   \Bo\Issue       $issue      Issue instance
     */
    private function _submitEditForm(Issue $issue)
    {
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
                $daoIssue = DaoIssue::getInstance();
                $daoIssue->update($issue);

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
