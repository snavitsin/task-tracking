<?php
$attrs = [
    'projects' => isset($projects) ? $projects : null,
    'priority' => isset($priority) ? $priority : null,
    'statuses' => isset($statuses) ? $statuses : null,
    'customers' => isset($customers) ? $customers : null,
    'developers' => isset($developers) ? $developers : null,
    'testers' => isset($testers) ? $testers : null,
    'subdivisions' => isset($subdivisions) ? $subdivisions : null,
];
?>

@extends('layouts.app')

@section('content')
    <management-page v-bind="{{json_encode($attrs)}}"/>
@endsection
