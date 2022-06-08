<?php

namespace App\Http\Controllers;

use App\Models\TaskModel;
use App\Models\StatusModel;
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
     * Главная страница
     * @param Request $request
     * @return mixed
     */
    public function getTasksPage(Request $request)
    {
        $taskModel = new TaskModel();
        $tasks = $taskModel->getTasks();
        $statusModel = new StatusModel();
        $statuses = $statusModel->getStatuses();

        return $this->prepareResponse([
            'tasks' => $tasks,
            'statuses' => $statuses,
            'editable' => false,
        ]);
    }
}
