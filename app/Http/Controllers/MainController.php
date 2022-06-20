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

class MainController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->defaultView = 'main';
    }

    /**
     * Главная страница
     * @param Request $request
     * @return mixed
     */
    public function getMainPage(Request $request)
    {
        $user = Auth::user();
        $isManager = $user->hasRole('manager');
        $userAttrs = $user->getAttributes();
        $taskModel = new TaskModel(['emp_id' => $userAttrs['emp_id']]);

        $tasks = $isManager ? $taskModel->getTasks() : $taskModel->getUserTasks();
        $statusModel = new StatusModel();
        $statuses = $statusModel->getStatuses();

        return $this->prepareResponse([
            'tasks' => $tasks,
            'statuses' => $statuses,
            'editable' => true,
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
