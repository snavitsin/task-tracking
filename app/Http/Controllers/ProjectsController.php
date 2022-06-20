<?php

namespace App\Http\Controllers;

use App\Models\PriorityModel;
use App\Models\ProjectModel;
use App\Models\StatusModel;
use App\Models\SubdivisionModel;
use App\Models\CustomerModel;
use App\Models\User;
use Illuminate\Http\Request;

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

        $subdivisionsModel = new SubdivisionModel();
        $subdivisions = $subdivisionsModel->getSubdivisions();

        $customersModel = new CustomerModel();
        $customers = $customersModel->getCustomers();

        $statusesModel = new StatusModel();
        $statuses = $statusesModel->getStatuses();

        return $this->prepareResponse([
            'project' => $project,
            'customers' => $customers,
            'statuses' => $statuses,
            'subdivisions' => $subdivisions,
        ]);
    }

    /**
     * Обновление проекта
     * @param Request $request
     * @return mixed
     */
    public function saveProject(Request $request)
    {
        $this->params = [
            'project_id',
            'project_title',
            'project_desc',
            'project_code',
            'project_customer',
            'project_subdiv',
            'project_dev_start',
            'project_dev_deadline',
        ];
        $this->validatorRules = [
            'project_id' => 'nullable|integer',
            'project_title' => 'required|string',
            'project_desc' => 'required|string',
            'project_code' => 'required|string',
            'project_customer' => 'required|integer',
            'project_subdiv' => 'required|integer',
            'project_dev_start' => 'required|date_format:d.m.Y',
            'project_dev_deadline' => 'required|date_format:d.m.Y|after_or_equal:project_dev_start',
        ];

        $this->validatorSpecialText =  [
            'project_dev_deadline.after_or_equal' => 'Срок сдачи проекта не может быть раньше даты создания проекта',
        ];

        if (!$this->isValidRequest($request)) return ['status' => false, 'errors' => $this->latestValidationErrors];

        $params = $this->getParam($request);
        $this->model = new ProjectModel($params);
        $result = $this->model->updateProject();

        $successText = isset($params['task_id']) ? 'Проект успешно обновлен' : 'Проект успешно создан';

        return [
            'status' => $result,
            'type' => $result === true ? 'success' : 'error',
            'message' => $result === true ? $successText : 'Произошла ошибка'
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
        $project = ProjectModel::find($params['project_id']);
        $attrs = $project->toArray();
        if(count($attrs['project_tasks']) > 0) {
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
