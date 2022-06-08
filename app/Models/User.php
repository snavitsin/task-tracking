<?php

namespace App\Models;

use App\Traits\HasRolesAndPermissions;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    protected $appends = ['emp_fio'];

    protected $testerId = 1;
    protected $developerId = 2;
    protected $managerId = 3;

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

        $user = Auth::user();
        $attributes = $user->attributes;
        $hidden = $user->hidden;

        $attributes = array_filter($attributes, function($key) use ($hidden) {
            return array_search($key, $hidden) === false;
        },ARRAY_FILTER_USE_KEY);

        $roles = $user->roles()->get()->toArray();
        $attributes['user_roles'] = $roles;
        return array_change_key_case($attributes, CASE_LOWER);
    }

    public function getUserId() {
        return $this->attributes[$this->primaryKey];
    }

    public static function getEmployeesByRole($position) {
        $employees = User::where('emp_position', $position)->get()->all();
//        foreach ($employees as &$employee) {
//            $employee->attributes['emp_fio'] = User::getEmpFio($employee->attributes);
//        }
        return array_values($employees);
    }

    public static function getEmpFio($attributes) {
        return $attributes['emp_surname'].' '.$attributes['emp_name'].' '.$attributes['emp_surname'];
    }

    public function getEmpFioAttribute(){
        return $this->attributes['emp_surname'].' '.$this->attributes['emp_name'].' '.$this->attributes['emp_patroname'];
        //return 'abcd';
    }
}
