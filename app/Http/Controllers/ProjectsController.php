<?php

namespace App\Http\Controllers;

use App\Models\PriorityModel;
use App\Models\ProjectModel;
use App\Models\SubdivisionModel;
use App\Models\TaskModel;
use App\Models\CommentModel;
use App\Models\StatusModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProjectsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->defaultView = 'projects';
    }

    /**
     * Страница проектов
     * @param Request $request
     * @return mixed
     */
    public function getProjectsPage(Request $request)
    {
        $this->model = new ProjectModel();
        $projects = $this->model->getProjects();

        return $this->prepareResponse([
            'projects' => $projects,
        ]);
    }

    /**
     * Страница проекта
     * @param Request $request
     * @return mixed
     */
    public function getProjectPage(Request $request, $id)
    {
        $this->params = [
            'project_id'
        ];
        $this->validatorRules = [
            'project_id' => 'required|integer',
        ];
        if (!$this->isValidRequest($request, ['project_id' => $id])) return ['status' => false, 'errors' => $this->latestValidationErrors];

        $this->defaultView = 'project';
        $this->model = new ProjectModel(['project_id' => $id]);
        $project = $this->model->getProject();

        return $this->prepareResponse([
            'project' => $project,
        ]);
    }

    /**
     * Обновление проекта
     * @param Request $request
     * @return mixed
     */
    public function updateProject(Request $request)
    {
        $this->params = [
            'project_id',
            'project_title',
            'project_desc',
            'project_customer',
            'project_dev_deadline',
            'project_dev_start',
            'project_subdiv',
        ];
        $this->validatorRules = [
            'subdiv_id' => 'required|integer',
            'subdiv_title' => 'required|string',
            'subdiv_desc' => 'required|string',
        ];

        if (!$this->isValidRequest($request)) return ['status' => false, 'errors' => $this->latestValidationErrors];

        $params = $this->getParam($request);
        $this->model = new SubdivisionModel($params);
        $result = $this->model->updateSubdiv();

        return [
            'status' => $result,
            'type' => $result === true ? 'success' : 'error',
            'message' => $result === true ? 'Подразделение успешно обновлено' : 'Произошла ошибка'
        ];
    }

    /**
     * Удаление подразделения
     * @param Request $request
     * @return mixed
     */
    public function deleteProject(Request $request)
    {
        $this->params = [
            'project_id',
        ];
        $this->validatorRules = [
            'project_id' => 'required|integer',
        ];

        if (!$this->isValidRequest($request)) return ['status' => false, 'errors' => $this->latestValidationErrors];

        $params = $this->getParam($request);
        $project = ProjectModel::find($params['project_id'])->toArray();
        if(count($project['project_tasks']) > 0) {
            return [
                'status' => false,
                'type' => 'error',
                'message' => 'У проекта остались задачи'
            ];
        } else {
            $result = $project->delete();
        }

        return [
            'status' => $result,
            'type' => $result === true ? 'success' : 'error',
            'message' => $result === true ? 'Проект успешно удален' : 'Произошла ошибка'
        ];
    }
}
