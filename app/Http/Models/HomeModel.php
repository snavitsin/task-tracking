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
        'task_desc',
        'task_status',
        'comment_id',
        'comment_text'
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

        return DB::table('Tasks')
            ->where('task_id', $taskId)
            ->update(['task_desc' => $taskDesc, 'task_status' => $taskStatus]);
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
            $sql = "exec getEmpComments $empId";
        } else {
            $sql = $taskId ? "exec getTaskComments $taskId" : "exec getEmpComments";
        }

        return DB::select($sql);
    }

    public function getTasks($ownTasks = false)
    {
        $empId = $this->attributes['emp_id'];
        $sql = $ownTasks ? "exec getTasksByEmp $empId" : "exec getTasksByEmp";
        return DB::select($sql);
    }
}
