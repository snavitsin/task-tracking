<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $developer = Role::where('slug','web-developer')->first();
        $employee = Role::where('slug','employee')->first();
        $taskStatistics = Permission::where('slug','task-statistics')->first();
        $operateTask = Permission::where('slug','operate-task')->first();

        $user1 = new User();
        $user1->emp_id = 4;
        $user1->save();
        $user1->roles()->attach($developer);
        $user1->roles()->attach($employee);
        $user1->permissions()->attach($taskStatistics);
        $user1->permissions()->attach($operateTask);
    }
}
