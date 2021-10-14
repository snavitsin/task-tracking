<?php

namespace App\Http\Models;

use App\Extensions\Helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HomeModel extends Model
{
    use HasFactory;

    public $timestamps = false;

    //public $emp_id = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'emp_id',
        'task_id',
        'task_title',
        'task_desc',
        'task_status',
        'task_priority',
        'task_deadline',
        'task_project',
        'comment_id',
        'comment_text',
        'only_emp_stats',
        'task_dev',
        'task_tester'
    ];

    public function __construct($values = [])
    {
        parent::__construct($values);
    }

    public function deleteTask()
    {
        $taskId = $this->attributes['task_id'];
        return DB::table('Tasks')
            ->where('task_id', $taskId)->delete();
    }

    public function editTask()
    {
        $taskId = $this->attributes['task_id'];
        $taskDesc = $this->attributes['task_desc'];
        $taskStatus = $this->attributes['task_status'];
        $taskDev = $this->attributes['task_dev'];
        $taskTester = $this->attributes['task_tester'];

        DB::table('Tasks')
            ->where('task_id', $taskId)
            ->update(['task_desc' => $taskDesc, 'task_status' => $taskStatus]);


        $dev = DB::table('Ref_task_emp')
            ->where([
                ['ref_task_emp_task', '=', $taskId],
                ['ref_task_emp_role', '=', 'Разработчик'],
            ])->get();

        if(count($dev)){
            DB::table('Ref_task_emp')
                ->where([
                    ['ref_task_emp_task', '=', $taskId],
                    ['ref_task_emp_role', '=', 'Разработчик'],
                ])->update(['ref_task_emp_emp' => $taskDev]);
        } else {
            DB::table('Ref_task_emp')
                ->insert([
                    'ref_task_emp_task' => $taskId,
                    'ref_task_emp_role' => 'Разработчик',
                    'ref_task_emp_emp' => $taskDev,
                    ]);
        }


        $tester = DB::table('Ref_task_emp')
            ->where([
                ['ref_task_emp_task', '=', $taskId],
                ['ref_task_emp_role', '=', 'Тестировщик'],
            ])->get();

        if(count($tester)){
            DB::table('Ref_task_emp')
                ->where([
                    ['ref_task_emp_task', '=', $taskId],
                    ['ref_task_emp_role', '=', 'Тестировщик'],
                ])->update(['ref_task_emp_emp' => $taskTester]);
        } else {
            DB::table('Ref_task_emp')
                ->insert([
                    'ref_task_emp_task' => $taskId,
                    'ref_task_emp_role' => 'Тестировщик',
                    'ref_task_emp_emp' => $taskTester,
                ]);
        }
    }

    public function createTask()
    {
//        $params = [
//            //'task_emp' => $this->attributes['emp_id'],
//            'task_title' =>  $this->attributes['task_title'],
//            'task_desc' =>  $this->attributes['task_desc'],
//            'task_status' =>  $this->attributes['task_status'],
//            'task_priority' =>  $this->attributes['task_priority'],
//            'task_created' =>  date('Y-m-d H:i:s'),
//            'task_deadline' =>  $this->attributes['task_deadline']
//        ];

        $params = [
            $this->attributes['task_title'],
            $this->attributes['task_desc'],
            $this->attributes['task_status'],
            $this->attributes['task_priority'],
            date('Y-m-d H:i:s'),
            $this->attributes['task_deadline'],
            $this->attributes['task_project'],
        ];

        //$taskId = DB::select("exec createTask '$params[0]', '$params[1]', '$params[2]', '$params[3]', '$params[4]', '$params[5]', $params[6]");
        $taskId = DB::table('Tasks')
            ->insertGetId([
                'task_title' =>  $this->attributes['task_title'],
                'task_desc' =>  $this->attributes['task_desc'],
                'task_status' =>  $this->attributes['task_status'],
                'task_priority' =>  $this->attributes['task_priority'],
                'task_created' =>  date('Y-m-d H:i:s'),
                'task_deadline' =>  $this->attributes['task_deadline'],
                'task_project' =>  $this->attributes['task_project']
            ], 'task_id');

        DB::table('Ref_task_emp')
            ->insert([
                'ref_task_emp_task' => $taskId,
                'ref_task_emp_role' => 'Разработчик',
                'ref_task_emp_emp' => $this->attributes['task_dev'],
            ]);

        DB::table('Ref_task_emp')
            ->insert([
                'ref_task_emp_task' => $taskId,
                'ref_task_emp_role' => 'Тестировщик',
                'ref_task_emp_emp' => $this->attributes['task_tester'],
            ]);
    }

    public function deleteComment()
    {
        $commentId = $this->attributes['comment_id'];

        return DB::table('Comments')
            ->where('comment_id', $commentId)->delete();
    }

    public function createComment()
    {
        $taskId = $this->attributes['task_id'];
        $empId = $this->attributes['emp_id'];
        $commentText = $this->attributes['comment_text'];

        return DB::table('Comments')->insert(['comment_comment' => $commentText,
            'comment_task' => $taskId, 'comment_emp' => $empId]);
    }

    public function editComment()
    {
        $commentId = $this->attributes['comment_id'];
        $commentText = $this->attributes['comment_text'];

        return DB::table('Comments')
            ->where('comment_id', $commentId)
            ->update(['comment_comment' => $commentText]);
    }

    public function getComments($ownComments = false)
    {
        $taskId = $this->attributes['task_id'] ?? null;
        $empId = $this->attributes['emp_id'] ?? null;

        if($ownComments){
            $sql = "select * from get_emp_comments($empId)";
        } else {
            $sql = $taskId ? "select * from get_task_comments($taskId)" : "select * from get_emp_comments()";
        }

        return DB::select($sql);
    }

    public function getTasks($ownTasks = false)
    {
        $empId = $this->attributes['emp_id'];
        $sql = $ownTasks ? "select * from get_tasks_by_emp($empId)" : "select * from  get_tasks()";
        $tasks =  DB::select($sql);

        foreach ($tasks as $task){
            $task->task_dev = $this->getTaskOperators($task->task_id, 'Разработчик');
            $task->task_tester = $this->getTaskOperators($task->task_id, 'Тестировщик');
        }

        return $tasks;
    }

    public function getTaskOperators($taskId, $operator)
    {
        $employee = DB::select("select * from get_task_operator($taskId, '$operator')");
        if (!$employee) return null;

        $employee = array_shift($employee);
        return $employee->emp_id;
    }

    public function getTaskStatistics()
    {
        $empStats = $this->attributes['only_emp_stats'] ?? false;
        $empId = $this->attributes['emp_id'] ?? null;
        $sql = $empStats ? "select * from get_task_statistics($empId)" : "select * from get_task_statistics()";

        return DB::select($sql);
    }

    public function getEmployees(){
        $employees = DB::table('Employees')->select(['emp_id', 'emp_name', 'emp_surname', 'emp_patroname'])->get();
        foreach ($employees as &$employee){
            $employee->emp_fio = "$employee->emp_surname $employee->emp_name $employee->emp_patroname";
        }
        return $employees;
    }

    public function getProjectEmployees($empPosition){
        $projectId = $this->attributes['task_project'];
        $employees = DB::select("select * from get_emps_by_project($projectId, '$empPosition')");

        foreach ($employees as &$employee){
            $employee->emp_fio = "$employee->emp_surname $employee->emp_name $employee->emp_patroname";
        }
        return $employees;
    }

    public function getProjects(){
        return DB::table('Projects')->get();
    }
}
