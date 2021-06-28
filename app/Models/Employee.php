<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Employee extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $emp_id = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'emp_id',
    ];

    public function __construct($values = [])
    {
        parent::__construct($values);
    }

    public function getEmployee()
    {
        return DB::table('Employees')->where('emp_id', $this->attributes['emp_id'])->get()->first();
    }
}
