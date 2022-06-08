<?php

namespace App\Models;

use Faker\Extension\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Extensions\Helpers;

class RefTaskEmpModel extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ref_task_emp';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'ref_task_emp_id';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ref_task_emp_id',
        'ref_task_emp_task',
        'ref_task_emp_emp',
        'ref_task_emp_role'
    ];

    public function __construct($values = [])
    {
        parent::__construct($values);
    }
}
