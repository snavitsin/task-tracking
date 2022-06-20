<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubdivisionModel extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'subdivisions';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'subdiv_id';


    protected $appends = ['subdiv_emps', 'subdiv_projects'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subdiv_id',
        'subdiv_title',
        'subdiv_desc',
    ];

    public function __construct($values = [])
    {
        parent::__construct($values);
    }

    public function getSubdivisions()
    {
        $subdivs = SubdivisionModel::all()->toArray();
        return array_values($subdivs);
    }

    public function getSubdivision()
    {
        $subdivId = $this->attributes['subdiv_id'];
        return SubdivisionModel::find($subdivId)->toArray();
    }

    public function updateSubdiv()
    {
        $isNewSubdiv = !isset($this->attributes['subdiv_id']);
        $result = null;
        if($isNewSubdiv) {
            $subdiv = new SubdivisionModel($this->attributes);
            $result = $subdiv->save();
        }
        else {
            $subdiv = SubdivisionModel::find($this->attributes['subdiv_id']);
            $result = null;

            if ($subdiv) {
                $subdiv->attributes = $this->attributes;
                $result = $subdiv->save();
            }
        }

        return boolval($result);
    }

    public function deleteSubdiv() {
        $subdivId = $this->attributes['subdiv_id'];
        $subdiv = SubdivisionModel::find($subdivId);
        return $subdiv->delete();
    }

    public function getSubdivEmpsAttribute()
    {
        return User::where('emp_subdiv', $this->attributes['subdiv_id'])->get()->all();
    }

    public function getSubdivProjectsAttribute()
    {
        return ProjectModel::where('project_subdiv', $this->attributes['subdiv_id'])->get()->all();
    }
}
