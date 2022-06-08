<?php
$attrs = [
    'task' => isset($task) ? $task : null,
    'taskEmps' => isset($taskEmps) ? $taskEmps : null,
    'comments' => isset($comments) ? $comments : null,
    'priority' => isset($priority) ? $priority : null,
    'statuses' => isset($statuses) ? $statuses : null,
    'projects' => isset($projects) ? $projects : null,
    'developers' => isset($developers) ? $developers : null,
    'testers' => isset($testers) ? $testers : null,
    'isManager' => isset($isManager) ? $isManager : null,
    'isTaskOperator' => isset($isTaskOperator) ? $isTaskOperator : null,
    'isNewTask' => isset($isNewTask) ? $isNewTask : null,
];
?>

@extends('layouts.app')

@section('content')
    <projects-page v-bind="{{json_encode($attrs)}}"/>
@endsection
