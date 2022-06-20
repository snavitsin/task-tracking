<?php

namespace App\Http\Controllers;

use App\Models\CustomerModel;
use App\Models\PriorityModel;
use App\Models\ProjectModel;
use App\Models\SubdivisionModel;
use App\Models\TaskModel;
use App\Models\StatusModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatisticsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->defaultView = 'stat';
    }

    /**
     * Страница статистики
     * @param Request $request
     * @return mixed
     */
    public function getStatPage(Request $request)
    {
        $projectModel = new ProjectModel();
        $projects = $projectModel->getProjects();

        $empModel = new User();
        $emps = $empModel->getEmployees();

        return $this->prepareResponse([
            'projects' => $projects,
            'emps' => $emps,
        ]);
    }

    /**
     * Стастика проекта
     * @param Request $request
     * @return mixed
     */
    public function getProjectStat(Request $request, $id)
    {
        $this->params = [
            'project_id'
        ];
        $this->validatorRules = [
            'project_id' => 'required|integer',
        ];
        if (!$this->isValidRequest($request, ['project_id' => $id])) return ['status' => false, 'errors' => $this->latestValidationErrors];

        $this->model = new ProjectModel(['project_id' => $id]);
        $project = $this->model->getProject();
        $b = 2;

        return $this->prepareResponse([

        ]);
    }

    /**
     * Страница управления
     * @param Request $request
     * @return mixed
     */
    public function getManagementPage(Request $request)
    {
        $this->defaultView = 'management';
        $subdivisionsModel = new SubdivisionModel();
        $subdivisions = $subdivisionsModel->getSubdivisions();
        $customersModel = new CustomerModel();
        $customers = $customersModel->getCustomers();

        $projectModel = new ProjectModel();
        $projects = $projectModel->getProjects();

        $statusModel = new StatusModel();
        $statuses = $statusModel->getStatuses();

        $priorityModel = new PriorityModel();
        $priority = $priorityModel->getPriority();

        $developers = User::getEmployeesByRole(2);
        $testers = User::getEmployeesByRole(3);

        return $this->prepareResponse([
            'projects' => $projects,
            'subdivisions' => $subdivisions,
            'customers' => $customers,
            'statuses' => $statuses,
            'priority' => $priority,
            'developers' => $developers,
            'testers' => $testers,
        ]);
    }
}
