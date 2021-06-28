<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $manager = new Role();
        $manager->name = 'Manager';
        $manager->slug = 'manager';

        $manager->save();
        $developer = new Role();
        $developer->name = 'Developer';
        $developer->slug = 'developer';
        $developer->save();

        $tester = new Role();
        $tester->name = 'Tester';
        $tester->slug = 'tester';
        $tester->save();

        $employee = new Role();
        $employee->name = 'Employee';
        $employee->slug = 'employee';
        $employee->save();
    }
}
