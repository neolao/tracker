<?php
require_once __DIR__ . '/AbstractController.php';

use \Neolao\Site\Request;
use \Vo\Project;
use \Bo\Project as BoProject;
use \Bo\Project\Exception\CreateException;
use \Bo\Project\Exception\UpdateException;
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
        if (!$this->isAllowed('main.projects', 'read')) {
            $this->forward('error', 'http401');
        }

        // Get all projects
        $boProject          = BoProject::getInstance();
        $filter             = new FilterProject();
        $filter->enabled    = true;
        $projects           = $boProject->getList($filter, ['name' => 'ASC']);

        // Render
        $this->view->projects = $projects;
        $this->render('projects/all');
    }

    /**
     * Display a project by his code name
     */
    public function sheetByCodeNameAction()
    {
        // Check ACL
        if (!$this->isAllowed('main.projects', 'read')) {
            $this->forward('error', 'http401');
        }


        $request    = $this->request;
        $parameters = $request->parameters;
        $codeName   = $parameters['codeName'];

        // Get the project instance
        $boProject  = BoProject::getInstance();
        $project    = $boProject->getByCodeName($codeName);
        if ($project instanceof Project === false) {
            $this->forward('error', 'http404');
        }

        // Render
        $this->view->project        = $project;
        $this->view->editEnabled    = $this->isAllowed('main.projects', 'update');
        $this->render('projects/sheet');
    }

    /**
     * Edit form
     */
    public function editAction()
    {
        // Check ACL
        if (!$this->isAllowed('main.projects', 'update')) {
            $this->forward('error', 'http401');
        }


        $request    = $this->request;
        $parameters = $request->parameters;
        $method     = $request->method;
        $id         = $parameters['id'];

        // Get the project instance
        $boProject  = BoProject::getInstance();
        $project    = $boProject->getById($id);
        if ($project instanceof Project === false) {
            $this->forward('error', 'http404');
        }

        // The user submit the form
        if ($method === Request::METHOD_POST) {
            if (isset($parameters['delete'])) {
                $this->_submitDeleteForm($project);
            } else {
                $this->_submitEditForm($project);
            }
        }


        // Render
        $this->view->project    = $project;
        $this->view->projectName= $project->name;
        $this->view->linkEdit   = $this->link('project.edit', ['id' => $project->id]);
        $this->view->displayDeleteForm = $this->isAllowed('main.projects', 'delete');
        $this->render('projects/edit');
    }

    /**
     * Create page
     */
    public function createAction()
    {
        // Check ACL
        if (!$this->isAllowed('main.projects', 'create')) {
            $this->forward('error', 'http401');
        }


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
        // Check ACL
        if (!$this->isAllowed('main.projects', 'create')) {
            $this->forward('error', 'http401');
        }


        $request        = $this->request;
        $parameters     = $request->parameters;
        $errors         = [];
        $codeName       = '';
        $name           = '';
        $description    = '';

        // Get the code name
        if (isset($parameters['codeName'])) {
            $codeName = trim($parameters['codeName']);
        }
        if (empty($codeName)) {
            $errors[] = $this->_('form.error.codeName.empty');
        } else if (!preg_match('/^[a-z0-9\-]{1,50}$/', $codeName)) {
            $errors[] = $this->_('form.error.codeName.invalid');
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
                $project->codeName      = $codeName;
                $project->name          = $name;
                $project->description   = $description;

                // Add the project
                $boProject = BoProject::getInstance();
                $boProject->add($project);

                // Redirect to the project page
                $this->redirect('project', ['codeName' => $project->codeName]);
            } catch (CreateException $exception) {
                $exceptionCode = $exception->getCode();
                switch ($exceptionCode) {
                    case CreateException::CODENAME_ALREADY_EXISTS:
                        $errors[] = $this->_('form.error.codeName.exists');
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
        $this->view->codeName       = $codeName;
        $this->view->name           = $name;
        $this->view->description    = $description;
        $this->view->hasErrors      = !empty($errors);
        $this->view->errors         = $errors;
    }

    /**
     * The edit form is submitted
     *
     * @param   \Vo\Project     $project        Project instance
     */
    private function _submitEditForm(Project $project)
    {
        // Check ACL
        if (!$this->isAllowed('main.projects', 'update')) {
            $this->forward('error', 'http401');
        }


        $request        = $this->request;
        $parameters     = $request->parameters;
        $errors         = [];
        $codeName       = '';
        $name           = '';
        $description    = '';

        // Get the code name
        if (isset($parameters['codeName'])) {
            $codeName = trim($parameters['codeName']);
        }
        if (empty($codeName)) {
            $errors[] = $this->_('form.error.codeName.empty');
        } else if (!preg_match('/^[a-z0-9\-]{1,50}$/', $codeName)) {
            $errors[] = $this->_('form.error.codeName.invalid');
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

        // Update the project
        if (empty($errors)) {
            try {
                // Update the project instance
                $project->codeName      = $codeName;
                $project->name          = $name;
                $project->description   = $description;

                // Update the database
                $boProject = BoProject::getInstance();
                $boProject->update($project);

                // Redirect to the project page
                $this->redirect('project', ['codeName' => $project->codeName]);

            } catch (UpdateException $exception) {
                $exceptionCode = $exception->getCode();
                switch ($exceptionCode) {
                    case UpdateException::PROJECT_NOT_FOUND:
                        $this->forward('error', 'http404');
                        break;
                    case UpdateException::CODENAME_ALREADY_EXISTS:
                        $errors[] = $this->_('form.error.codeName.exists');
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

    /**
     * The delete form is submitted
     *
     * @param   \Vo\Project     $project        Project instance
     */
    private function _submitDeleteForm($project)
    {
        // Check ACL
        if (!$this->isAllowed('main.projects', 'delete')) {
            $this->forward('error', 'http401');
        }


        $request        = $this->request;
        $parameters     = $request->parameters;
        $deleteErrors   = [];
        $codeName       = '';

        // Get the code name
        if (isset($parameters['codeName'])) {
            $codeName = trim($parameters['codeName']);
        }

        // Check if the code names are the same
        if ($codeName !== $project->codeName) {
            $deleteErrors[] = $this->_('form.error.codeName.notMatch');
        }

        // Delete the project and redirect to the list
        if (empty($deleteErrors)) {
            try {
                $boProject = BoProject::getInstance();
                $boProject->delete($project->id);

                $this->redirect('projects');
            } catch (\Exception $exception) {
                $deleteErrors[] = $this->_('form.error.unknown');
            }
        }

        // View parameters
        $this->view->hasDeleteErrors = !empty($deleteErrors);
        $this->view->deleteErrors = $deleteErrors;
    }
}
