<?php

namespace App\Http\Controllers;

use App\Models\PriorityModel;
use App\Models\ProjectModel;
use App\Models\TaskModel;
use App\Models\CommentsModel;
use App\Models\StatusModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TasksController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->defaultView = 'task';
    }

    /**
     * Главная страница
     * @param Request $request
     * @return mixed
     */
    public function getTaskPage(Request $request, $id)
    {
        $this->params = [
            'task_id'
        ];
        $this->validatorRules = [
            'task_id' => 'required|integer',
        ];
        if (!$this->isValidRequest($request, ['task_id' => $id])) return ['status' => false, 'errors' => $this->latestValidationErrors];

        $this->model = new TaskModel(['task_id' => $id]);
        $task = $this->model->getTask();
        $taskEmps = $this->model->getTaskEmployees();
        $commentsModel = new CommentsModel(['task_id' => $id]);
        $comments = $commentsModel->getTaskComments();

        $user = Auth::user();
        $userId = $user->getUserId();
        $isManager = $user->hasRole('manager');
        $isTaskOperator = array_search($userId, array_column($taskEmps, 'emp_id')) !== false;

        $priorityModel = new PriorityModel();
        $statusModel = new StatusModel();
        $priority = $priorityModel->getPriority();
        $statuses = $statusModel->getStatuses();

        $projectModel = new ProjectModel();
        $projects = $projectModel->getProjects();

        $developers = User::getEmployeesByRole(2);
        $testers = User::getEmployeesByRole(1);

        $response = [
            'task' => $task,
            'taskEmps' => $taskEmps,
            'comments' => $comments,
            'priority' => $priority,
            'statuses' => $statuses,
            'projects' => $projects,
            'developers' => $developers,
            'testers' => $testers,
            'isManager' => $isManager,
            'isTaskOperator' => $isTaskOperator,
            'isNewTask' => false,
        ];

        return $this->prepareResponse($response);
    }

    /**
     * Страница создания новой задачи
     * @param Request $request
     * @return mixed
     */
    public function newTaskPage(Request $request)
    {
        $user = Auth::user();
        $isManager = $user->hasRole('manager');

        $priorityModel = new PriorityModel();
        $statusModel = new StatusModel();
        $priority = $priorityModel->getPriority();
        $statuses = $statusModel->getStatuses();

        $projectModel = new ProjectModel();
        $projects = $projectModel->getProjects();

        $developers = User::getEmployeesByRole(2);
        $testers = User::getEmployeesByRole(1);

        $response = [
            'priority' => $priority,
            'statuses' => $statuses,
            'projects' => $projects,
            'developers' => $developers,
            'testers' => $testers,
            'isManager' => $isManager,
            'isNewTask' => true,
        ];

        return $this->prepareResponse($response);
    }

    /**
     * Обновление задачи
     * @param Request $request
     * @return mixed
     */
    public function updateTask(Request $request)
    {
        $this->params = [
            'task_id',
            'task_title',
            'task_desc',
            'task_status',
            'task_priority',
            'task_created',
            'task_deadline',
            'task_project',
        ];
        $this->validatorRules = [
            'status_id' => 'required|integer',
            'task_id' => 'required|integer'
        ];


        if (!$this->isValidRequest($request)) return ['status' => false, 'errors' => $this->latestValidationErrors];

        $params = $this->getParam($request);

        $this->model = new TaskModel($params);
        $result = $this->model->updateTaskStatus();
        return [
            'status' => $result,
            'type' => $result === true ? 'success' : 'error',
            'message' => $result === true ? 'Статус задачи успешно изменен' : 'Произошла ошибка'
        ];
    }

    /**
     * Получение списка задач и статусов
     * @param Request $request
     * @return mixed
     */
    public function getTasks(Request $request)
    {
        $taskModel = new TaskModel();
        $tasks = $taskModel->getTasks();
        $statusModel = new StatusModel();
        $statuses = $statusModel->getStatuses();

        return $this->prepareResponse([
            'tasks' => $tasks,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Обновление статуса задачи
     * @param Request $request
     * @return mixed
     */
    public function updateTaskStatus(Request $request)
    {
        $this->params = ['status_id', 'task_id'];
        $this->validatorRules = [
            'status_id' => 'required|integer',
            'task_id' => 'required|integer',
        ];

        if (!$this->isValidRequest($request)) return ['status' => false, 'errors' => $this->latestValidationErrors];

        $params = $this->getParam($request);

        $this->model = new TaskModel($params);
        $result = $this->model->updateTaskStatus();
        return [
            'status' => $result,
            'type' => $result === true ? 'success' : 'error',
            'message' => $result === true ? 'Статус задачи успешно изменен' : 'Произошла ошибка'
        ];
    }

    /**
     * Сохранение задачи
     * @param Request $request
     * @return mixed
     */
    public function saveTask(Request $request)
    {
        $this->params = [
            'task_id',
            'task_priority',
            'task_status',
            'task_title',
            'task_desc',
            'task_project',
            'task_dev',
            'task_tester',
            'task_created',
            'task_deadline',
        ];
        $this->validatorRules = [
            'task_id' => 'nullable|integer',
            'task_priority' => 'required|integer',
            'task_status' => 'required|integer',
            'task_title' => 'required|string',
            'task_desc' => 'required|string',
            'task_project' => 'required|integer',
            'task_dev' => 'required|integer',
            'task_tester' => 'required|integer',
            'task_created' => 'required|date_format:d.m.Y',
            'task_deadline' => 'required|date_format:d.m.Y|after_or_equal:task_created'
        ];

        $this->validatorSpecialText =  [
            'task_deadline.after_or_equal' => 'Срок разработки не может быть раньше даты создания задачи',
        ];

        if (!$this->isValidRequest($request)) return ['status' => false, 'errors' => $this->latestValidationErrors];

        $params = $this->getParam($request);

        $this->model = new TaskModel($params);
        $this->model->fillTaskOperators($params);
        $result = $this->model->saveTask();

        $successText = isset($params['task_id']) ? 'Задача успешно обновлена' : 'Задача успешно создана';

        return [
            'status' => $result,
            'type' => $result === true ? 'success' : 'error',
            'message' => $result === true ? $successText : 'Произошла ошибка'
        ];
    }

    /**
     * Удаление задачи
     * @param Request $request
     * @return mixed
     */
    public function deleteTask(Request $request)
    {
        $this->params = [
            'task_id',
        ];
        $this->validatorRules = [
            'task_id' => 'nullable|integer',
        ];

        if (!$this->isValidRequest($request)) return ['status' => false, 'errors' => $this->latestValidationErrors];

        $params = $this->getParam($request);

        $this->model = new TaskModel($params);
        //$result = $this->model->deleteTask();
        $result = true;
        return [
            'status' => $result,
            'type' => $result === true ? 'success' : 'error',
            'message' => $result === true ? 'Задача успешно удалена' : 'Произошла ошибка'
        ];
    }
}
