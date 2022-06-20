<?php

namespace App\Models;

use App\Traits\HasRolesAndPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use HasFactory, HasRolesAndPermissions;

    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employees';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'emp_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'emp_id',
        'emp_name',
        'emp_patroname',
        'emp_surname',
        'emp_subdiv',
        'emp_position',
        'emp_mail',
        'emp_login',
        'emp_password',
        'emp_fio',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'emp_password',
        'emp_remember_token'
    ];

    protected $appends = ['emp_fio', 'emp_tasks', 'emp_position_title'];

    protected $testerId = 3;
    protected $developerId = 2;
    protected $managerId = 1;

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->emp_password;
    }

    /**
     * Возвращает Юзера
     * @access public
     * @return array
     */
    public static function getUser()
    {
        if (!Auth::check())
            return null;

        $id = Auth::user()->getUserId();
        $user = User::find($id);
        $roles = $user->roles()->get()->toArray();
        $user->attributes['user_roles'] = $roles;
        return $user;
    }

    public function getUserId() {
        return $this->attributes[$this->primaryKey];
    }

    public static function getEmployeesByRole($position) {
        $employees = User::where('emp_position', $position)->get()->all();
        return array_values($employees);
    }

    public function updateEmp()
    {
        $isNewEmp = !isset($this->attributes['emp_id']);
        $result = null;
        if($isNewEmp) {
            $emp = new User($this->attributes);
            $result = $emp->save();
        }
        else {
            $emp = User::find($this->attributes['emp_id']);
            $result = null;

            if ($emp) {
                $emp->attributes = $this->attributes;
                $result = $emp->save();
            }
        }

        return boolval($result);
    }

    public function deleteEmp() {
        $empId = $this->attributes['emp_id'];
        $emp = User::find($empId);
        return $emp->delete();
    }

    public function getEmployees()
    {
        $emps = User::all()->toArray();
        return array_values($emps);
    }

    public function getEmployee()
    {
        $empId = $this->attributes['emp_id'];
        return User::find($empId)->toArray();
    }

    public function getEmpFioAttribute(){
        return $this->attributes['emp_surname'].' '.$this->attributes['emp_name'].' '.$this->attributes['emp_patroname'];
    }

    public function getEmpTasksAttribute(){
        $empId = $this->attributes['emp_id'];
        $tasks = TaskModel::select('tasks.*')
            ->join('ref_task_emp as RTE', function ($join) use ($empId) {
                $join->on('tasks.task_id', '=', 'RTE.ref_task_emp_task')->where('RTE.ref_task_emp_emp', $empId);
            })->get()->all();
        return array_values($tasks);
    }

    public function getEmpPositionTitleAttribute(){

        $position = PositionModel::find($this->attributes['emp_position'])->toArray();
        return $position['emp_position'];
    }
}
