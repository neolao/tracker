<?php
require_once __DIR__ . '/AbstractController.php';

use \Neolao\Site\Request;
use \Bo\Project;
use \Dao\Project as DaoProject;
use \Dao\Project\Exception\CreateException;
use \Filter\Project as FilterProject;

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

        // Get all projects
        // @todo Create a pagination
        $daoProject = DaoProject::getInstance();
        $filter     = new FilterProject();
        $projects   = $daoProject->getList($filter);

        // Render
        $this->view->projects = $projects;
        $this->render('projects/all');
    }

    /**
     * Display a project by his code name
     */
    public function sheetByCodeNameAction()
    {
        $request    = $this->request;
        $parameters = $request->parameters;
        $codeName   = $parameters['codeName'];

        // Get the project instance
        $daoProject = DaoProject::getInstance();
        $project    = $daoProject->getByCodeName($codeName);
        if ($project instanceof Project === false) {
            $this->forward('error', 'http404');
        }

        // Render
        $this->view->project        = $project;
        $this->view->projectName    = $project->name;
        $this->render('projects/sheet');
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
        $request        = $this->request;
        $parameters     = $request->parameters;
        $errors         = [];
        $identifier     = '';
        $name           = '';
        $description    = '';

        // Get the identifier
        if (isset($parameters['identifier'])) {
            $identifier = trim($parameters['identifier']);
        }
        if (empty($identifier)) {
            $errors[] = $this->_('form.error.identifier.empty');
        } else if (!preg_match('/^[a-z0-9\-]{1,50}$/', $identifier)) {
            $errors[] = $this->_('form.error.identifier.invalid');
        }

        // Get the name
        if (isset($parameters['name'])) {
            $name = trim($parameters['name']);
        }
        if (empty($name)) {
            $errors[] = $this->_('form.error.name.empty');
        } else if (!preg_match('/^.{1,50}$/', $name)) {
            $errors[] = $this->_('form.error.name.invalid');
        }

        // Get the description
        if (isset($parameters['description'])) {
            $description = $parameters['description'];
        }

        // Create the project
        if (empty($errors)) {
            try {
                // Build the project instance
                $project                = new Project();
                $project->codeName      = $identifier;
                $project->name          = $name;
                $project->description   = $description;

                // Add the project
                $daoProject = DaoProject::getInstance();
                $daoProject->add($project);

                // Redirect to the project page
                $this->redirect('project', ['codeName' => $project->codeName]);
            } catch (CreateException $exception) {
                $exceptionCode = $exception->getCode();
                switch ($exceptionCode) {
                    case CreateException::CODENAME_ALREADY_EXISTS:
                        $errors[] = $this->_('form.error.identifier.exists');
                        break;
                    default:
                        $errors[] = $this->_('form.error.unknown');
                }
            } catch (\Exception $exception) {
                $errors[] = $this->_('form.error.unknown');
            }
        }


        // View parameters
        $this->view->identifier     = $identifier;
        $this->view->name           = $name;
        $this->view->description    = $description;
        $this->view->hasErrors      = !empty($errors);
        $this->view->errors         = $errors;
    }
}
