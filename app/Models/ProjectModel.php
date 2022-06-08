<?php

namespace App\Models;

use Faker\Extension\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Extensions\Helpers;

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


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id',
        'project_title',
        'project_desc',
        'project_customer',
        'project_dev_deadline',
        'project_dev_start',
        'project_subdiv',
    ];

    public function __construct($values = [])
    {
        parent::__construct($values);
    }

    public function getProjects()
    {
        $projects = ProjectModel::all()->toArray();
        return array_values($projects);
    }
}
