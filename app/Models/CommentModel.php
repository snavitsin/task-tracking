<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommentModel extends Model
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
        $comments = CommentModel::all()->where('comment_task', $taskId)->toArray();
        return array_values($comments);
    }

    public function updateComment()
    {
        $isNew = !isset($this->attributes['comment_id']);
        $result = null;
        if($isNew) {
            $comment = new CommentModel($this->attributes);
            $comment->attributes['comment_emp'] = Auth::user()->getUserId();
            $result = $comment->save();
        }
        else {
            $comment = CommentModel::find($this->attributes['comment_id']);
            $result = null;

            if ($comment) {
                $comment->attributes = $this->attributes;
                $result = $comment->save();
            }
        }

        return boolval($result);
    }

    public function getCommentAuthorAttribute(){
        $userId = $this->attributes['comment_emp'];
        $user = User::find($userId)->toArray();
        return $user['emp_fio'];
    }

    public function getCommentTaskTitleAttribute(){
        $taskId = $this->attributes['comment_task'];
        $task = TaskModel::find($taskId);
        return $task->attributes['task_title'];
    }
}
