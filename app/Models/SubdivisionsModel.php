<?php

namespace App\Models;

use Faker\Extension\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Extensions\Helpers;
use App\Models\TaskModel;

class SubdivisionsModel extends Model
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


    protected $appends = ['subdiv_emp_count', 'subdiv_emps'];

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
        $subdivs = SubdivisionsModel::all()->toArray();
        return array_values($subdivs);
    }

    public function getSubdivision()
    {
        $subdivId = $this->attributes['subdiv_id'];
        return SubdivisionsModel::find($subdivId)->toArray();
    }

    public function updateSubdiv()
    {
        $subdivId = $this->attributes['subdiv_id'];
        $subdiv = SubdivisionsModel::find($subdivId);
        $result = null;
        if ($subdiv) {
            $subdiv->attributes = $this->attributes;
            $result = $subdiv->save();
        }
        return $result;
    }

    public function getSubdivEmpCountAttribute()
    {
        return User::where('emp_subdiv', $this->attributes['subdiv_id'])->get()->count();
    }

    public function getSubdivEmpsAttribute()
    {
        return User::where('emp_subdiv', $this->attributes['subdiv_id'])->get()->all();
    }
}
