<?php

namespace App\Models;

use Faker\Extension\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Extensions\Helpers;
use App\Models\TaskModel;

class CommentsModel extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'comments';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'comment_id';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'comment_id',
        'comment_emp',
        'comment_task',
        'comment_comment',
        'task_id',
    ];

    protected $appends = ['comment_author', 'comment_task_title'];

    public function __construct($values = [])
    {
        parent::__construct($values);
    }

    public function getTaskComments()
    {
        $taskId = $this->attributes['task_id'];
        $comments = CommentsModel::all()->where('comment_task', $taskId)->toArray();
        return array_values($comments);
    }

    public function getTask()
    {
        $taskId = $this->attributes['task_id'];
        $task = TaskModel::find($taskId);
        return $task;
    }

    public function getUserTasks()
    {
        $tasks = DB::table('tasks')->join('ref_task_emp as RTE', function($join){
        $join->on('tasks.task_id', '=', 'RTE.ref_task_emp_task')
            ->where('RTE.ref_task_emp_emp', $this->attributes['emp_id']);
        })->select()->get()->all();
        $tasks = array_values($tasks);
        return $tasks;
        //return $this->getRelatedValues($tasks);
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

    public function getCommentAuthorAttribute(){
        $userId = $this->attributes['comment_emp'];
        $user = User::find($userId)->first();
        return $user->getEmpFioAttribute();
    }

    public function getCommentTaskTitleAttribute(){
        $taskId = $this->attributes['comment_task'];
        $task = TaskModel::find($taskId);
        return $task->attributes['task_title'];
    }
}
