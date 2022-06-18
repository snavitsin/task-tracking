<?php
$attrs = [
    'project' => isset($project) ? $project : null,
    'subdivisions' => isset($subdivisions) ? $subdivisions : null,
    'customers' => isset($customers) ? $customers : null,
    'statuses' => isset($statuses) ? $statuses : null,
];
?>

@extends('layouts.app')

@section('content')
    <project-page v-bind="{{json_encode($attrs)}}"/>
@endsection
