<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Руководитель
        $taskStatistics = new Permission();
        $taskStatistics->name = 'Task statistics';
        $taskStatistics->slug = 'task-statistics';
        $taskStatistics->save();

        $deleteComment = new Permission();
        $deleteComment->name = 'Delete Comment';
        $deleteComment->slug = 'delete-comment';
        $deleteComment->save();

        $assignEmployee = new Permission();
        $assignEmployee->name = 'Assign Employee';
        $assignEmployee->slug = 'assign-employee';
        $assignEmployee->save();

        $operateTask = new Permission();
        $operateTask->name = 'Operate Task';
        $operateTask->slug = 'operate-task';
        $operateTask->save();

        //Разработчик
        $executeTask = new Permission();
        $executeTask->name = 'Execute task';
        $executeTask->slug = 'execute-task';
        $executeTask->save();

        //Тестировщик
        $testTask = new Permission();
        $testTask->name = 'Test task';
        $testTask->slug = 'test-task';
        $testTask->save();

        //Разработчик, Тестировщик
        $operateOwnTask = new Permission();
        $operateOwnTask->name = 'Operate Own Task';
        $operateOwnTask->slug = 'operate-own-task';
        $operateOwnTask->save();

        //Любой сотрудник
        $operateOwnComment = new Permission();
        $operateOwnComment->name = 'Operate Own Comment';
        $operateOwnComment->slug = 'operate-own-comment';
        $operateOwnComment->save();

        $watchTask = new Permission();
        $watchTask->name = 'Watch Task';
        $watchTask->slug = 'watch-task';
        $watchTask->save();
    }

}
