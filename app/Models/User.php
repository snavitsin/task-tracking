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

    protected $appends = ['emp_fio', 'emp_position_title'];

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

    public function getEmpFioAttribute(){
        return $this->attributes['emp_surname'].' '.$this->attributes['emp_name'].' '.$this->attributes['emp_patroname'];
    }

    public function getEmpPositionTitleAttribute(){
        $positions = [
            1 => 'Тестировщик',
            2 => 'Разработчик',
            3 => 'Руководитель'];
        return $positions[$this->attributes['emp_position']];
    }
}
