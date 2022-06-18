<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectModel extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'projects';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'project_id';


    protected $appends = ['project_tasks'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id',
        'project_title',
        'project_desc',
        'project_code',
        'project_customer',
        'project_dev_deadline',
        'project_dev_start',
        'project_subdiv',
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

    public function getProjects()
    {
        $projects = ProjectModel::all()->toArray();
        return array_values($projects);
    }

    public function getProject()
    {
        $projectId = $this->attributes['project_id'];
        $project = ProjectModel::find($projectId)->toArray();
        $project = $this->convertTime([$project]);
        return array_shift($project);
    }

    public function updateProject()
    {
        $isNewProject = !isset($this->attributes['project_id']);
        $result = null;
        if($isNewProject) {
            $project = new ProjectModel($this->attributes);
            $result = $project->save();
        }
        else {
            $project = ProjectModel::find($this->attributes['project_id']);
            $result = null;

            if ($project) {
                $project->attributes = $this->attributes;
                $result = $project->save();
            }
        }

        return boolval($result);
    }

    public function convertTime($projects) {
        foreach ($projects as &$project) {
            $project['project_dev_deadline'] = date('d.m.Y', strtotime($project['project_dev_deadline']));
            $project['project_dev_start'] = date('d.m.Y', strtotime($project['project_dev_start']));
        }
        return $projects;
    }

    public function formatDateTime() {
        $this->attributes['project_dev_start'] = date('Y-m-d H:i:s', strtotime($this->attributes['project_dev_start']));
        $this->attributes['project_dev_deadline'] = date('Y-m-d H:i:s', strtotime($this->attributes['project_dev_deadline']));
    }

    public function getProjectTasksAttribute()
    {
        return TaskModel::where('task_project', $this->attributes['project_id'])->get()->all();
    }
}
