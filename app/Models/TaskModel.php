<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskModel extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tasks';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'task_id';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'task_id',
        'task_title',
        'task_desc',
        'task_resolution',
        'task_status',
        'task_priority',
        'task_created',
        'task_deadline',
        'task_project',
        'status_id',
        'emp_id',
    ];

    protected $appends = [
        'task_priority_title', 'task_priority_color', 'task_project_title',
        'task_project_code', 'task_project_color', 'task_overdue',
    ];

    protected $operators = [
        'task_dev' => null,
        'task_tester' => null,
    ];

    public static function boot()
    {
        parent::boot();

        self::updating(function($model){
            $model->formatDateTime();
        });

        static::saving(function($model) {
            $model->formatDateTime();
        });
    }

    public function __construct($values = [])
    {
        parent::__construct($values);
    }

    public function getTasks()
    {
        $tasks = TaskModel::all()->toArray();
        $tasks = array_values($tasks);
        return $tasks;
    }

    public function getTask()
    {
        $taskId = $this->attributes['task_id'];
        $task = TaskModel::find($taskId)->toArray();
        $task = $this->convertTime([$task]);
        $task = $this->setTaskOperators($task);
        return array_shift($task);
    }

    public function getTaskEmployees()
    {
        $taskId = $this->attributes['task_id'];
        $emps = User::select('employees.*')
            ->join('ref_task_emp as RTE', function ($join) use ($taskId) {
                $join->on('employees.emp_id', '=', 'RTE.ref_task_emp_emp')->where('RTE.ref_task_emp_task', $taskId);
            })->get()->all();
        return array_values($emps);
    }

    public function getUserTasks()
    {
        $tasks = DB::table('tasks')->join('ref_task_emp as RTE', function($join) {
        $join->on('tasks.task_id', '=', 'RTE.ref_task_emp_task')
            ->where('RTE.ref_task_emp_emp', $this->attributes['emp_id']);
        })->select()->get()->all();
        return array_values($tasks);
    }

    public function updateTaskStatus() {
        $taskId = $this->attributes['task_id'];
        $statusId = $this->attributes['status_id'];
        $task = TaskModel::find($taskId);
        $result = null;
        if($task) {
            $task->attributes['task_status'] = $statusId;
            $result = $task->save();
        }
        return $result;
    }

    public function saveTask() {
        $isNewTask = !isset($this->attributes['task_id']);
        $result = null;
        if($isNewTask) {
            $task = new TaskModel($this->attributes);
            $result = $task->save();
            if($result) $this->attributes['task_id'] = $task->attributes['task_id'];
        }
        else {
            $taskId = $this->attributes['task_id'];
            $task = TaskModel::find($taskId);

            if($task) {
                $task->attributes = $this->attributes;
                $result = $task->save();
            }
        }

        if($result) $result &= $this->updateTaskOperators($isNewTask);

        return boolval($result);
    }

    public function deleteTask() {
        $taskId = $this->attributes['task_id'];
        $task = TaskModel::find($taskId);
        return $task->delete();
    }

    public function convertTime($tasks) {
        foreach ($tasks as &$task) {
            $task['task_created'] = date('d.m.Y', strtotime($task['task_created']));
            $task['task_deadline'] = date('d.m.Y', strtotime($task['task_deadline']));
        }
        return $tasks;
    }

    public function updateTaskOperators($isNewTask) {
        $taskId = $this->attributes['task_id'];
        $taskDev = $this->operators['task_dev'];
        $taskDevRef = RefTaskEmpModel::where([
            ['ref_task_emp_task', '=', $taskId],
            ['ref_task_emp_role', '=', 2],
        ])->first();

        if($taskDevRef) {
            if($taskDevRef->attributes['ref_task_emp_emp'] !== $taskDev) {
                $taskDevRef->attributes['ref_task_emp_emp'] = $taskDev;
                $result = $taskDevRef->save();
            }
            else {
                $result = true;
            }
        } else {
            $taskDevRef = new RefTaskEmpModel([
                'ref_task_emp_task' => $taskId,
                'ref_task_emp_emp' => $taskDev,
                'ref_task_emp_role' => 2,
            ]);
            $result = $taskDevRef->save();
        }

        $taskTester = $this->operators['task_tester'];
        $taskTesterRef = RefTaskEmpModel::where([
            ['ref_task_emp_task', '=', $taskId],
            ['ref_task_emp_role', '=', 3],
        ])->first();

        if($taskTesterRef) {
            if($taskTesterRef->attributes['ref_task_emp_emp'] !== $taskTester) {
                $taskTesterRef->attributes['ref_task_emp_emp'] = $taskTester;
                $result &= $taskTesterRef->save();
            }
            else {
                $result &= true;
            }
        } else {
            $taskTesterRef = new RefTaskEmpModel([
                'ref_task_emp_task' => $taskId,
                'ref_task_emp_emp' => $taskTester,
                'ref_task_emp_role' => 3,
            ]);
            $result &= $taskTesterRef->save();
        }

        if($isNewTask) {
            $taskAuthorRef = new RefTaskEmpModel([
                'ref_task_emp_task' => $taskId,
                'ref_task_emp_emp' => Auth::user()->getUserId(),
                'ref_task_emp_role' => 1,
            ]);
            $result &= $taskAuthorRef->save();
        }

        return $result;
    }

    public function setTaskOperators($tasks) {
        foreach ($tasks as &$task) {
            $this->attributes['task_id'] = $task['task_id'];
            $taskEmps = $this->getTaskEmployees();
            foreach ($taskEmps as $taskEmp) {
                switch ($taskEmp->emp_position) {
                    case 1:
                        $task['task_tester'] = $taskEmp->emp_id;
                        break;
                    case 2:
                        $task['task_dev'] = $taskEmp->emp_id;
                        break;
                    case 3:
                        $task['task_author'] = $taskEmp->emp_id;
                        break;
                    default: break;
                }
            }
        }
        return $tasks;
    }
    
    public function getRelatedValues($tasks) {
        $priorityModel = new PriorityModel();
        $statusModel = new StatusModel();
        $priority = $priorityModel->getPriority();
        $statuses = $statusModel->getStatuses();

        foreach ($tasks as &$task) {
            $task = (array) $task;
        }
        return $tasks;
    }

    public function formatDateTime() {
        $this->attributes['task_created'] = date('Y-m-d H:i:s', strtotime($this->attributes['task_created']));
        $this->attributes['task_deadline'] = date('Y-m-d H:i:s', strtotime($this->attributes['task_deadline']));
    }

    public function fillTaskOperators($params) {
        $this->operators['task_dev'] = $params['task_dev'];
        $this->operators['task_tester'] = $params['task_tester'];
    }

    public function getTaskPriorityTitleAttribute()
    {
        $priorityId = $this->attributes['task_priority'];
        $priority = PriorityModel::find($priorityId)->toArray();
        return $priority['priority_title'];
    }

    public function getTaskPriorityColorAttribute()
    {
        $priorityId = $this->attributes['task_priority'];
        $priority = PriorityModel::find($priorityId)->toArray();
        return $priority['priority_color'];
    }

    public function getTaskProjectTitleAttribute()
    {
        $projectId = $this->attributes['task_project'];
        $project = ProjectModel::find($projectId)->toArray();
        return $project['project_title'];
    }

    public function getTaskProjectCodeAttribute()
    {
        $projectId = $this->attributes['task_project'];
        $project = ProjectModel::find($projectId)->toArray();
        return $project['project_code'];
    }

    public function getTaskProjectColorAttribute()
    {
        $projectId = $this->attributes['task_project'];
        $project = ProjectModel::find($projectId)->toArray();
        return $project['project_color'];
    }

    public function getTaskOverdueAttribute()
    {
        $deadline = strtotime($this->attributes['task_deadline']);
        $nowTime = strtotime('now');
        $notCompleted = $this->attributes['task_status'] !== 3;
        return $nowTime > $deadline && $notCompleted;
    }
}
