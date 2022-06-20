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

use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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
        $tasks = $project['project_tasks'];

        $tasks = array_map(function ($task) {
            return $task->toArray();
        }, $tasks);

        $overdue = [];
        $completed = [];
        $over = [];
        if(count($tasks)) {
            $overdue = array_filter($tasks, function ($task) {
                return $task['task_overdue'] == true;
            });
            $completed = array_filter($tasks, function ($task) {
                return $task['task_status'] == 3;
            });
            $over = array_filter($tasks, function ($task) {
                return $task['task_status'] != 3 && $task['task_overdue'] != true;
            });
        }

        $columns = ['Просроченные задачи', 'Выполненные задачи', 'Другие статусы'];
        $rows = [[count($overdue), count($completed), count($over)]];

        $data = ['columns' => $columns, 'data' => $rows];
        $fileName = 'Статистика проекта '.$project['project_title'];
        return $this->prepareExportCsv($data, $fileName);
    }

    /**
     * Стастика сотрудника
     * @param Request $request
     * @return mixed
     */
    public function getEmpStat(Request $request, $id)
    {
        $this->params = [
            'emp_id'
        ];
        $this->validatorRules = [
            'emp_id' => 'required|integer',
        ];
        if (!$this->isValidRequest($request, ['emp_id' => $id])) return ['status' => false, 'errors' => $this->latestValidationErrors];

        $emp = User::find($id);
        $tasks = $emp['emp_tasks'];

        $tasks = array_map(function ($task) {
            return $task->toArray();
        }, $tasks);

        $overdue = [];
        $completed = [];
        $over = [];
        if(count($tasks)) {
            $overdue = array_filter($tasks, function ($task) {
                return $task['task_overdue'] == true;
            });
            $completed = array_filter($tasks, function ($task) {
                return $task['task_status'] == 3;
            });
            $over = array_filter($tasks, function ($task) {
                return $task['task_status'] != 3 && $task['task_overdue'] != true;
            });
        }

        $columns = ['Просроченные задачи', 'Выполненные задачи', 'Другие статусы'];
        $rows = [[count($overdue), count($completed), count($over)]];

        $data = ['columns' => $columns, 'data' => $rows];
        $fileName = 'Статистика сотрудника '.$emp['emp_fio'];
        return $this->prepareExportCsv($data, $fileName);
    }

    public function prepareExportCsv($res = [], $fileName = 'file')
    {
        $fileName .= '.csv';

        $data = $res['data'];
        $columns = $res['columns'];
        $filePath = sys_get_temp_dir() . '/' . $fileName;
        $file = fopen($filePath, 'w');
        //BOM-mark
        fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($file, $columns, ';');

        foreach($data as $row){
            fputcsv($file, $row, ';');
        }
        fclose($file);
        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
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
