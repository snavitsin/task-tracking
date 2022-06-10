<?php
$attrs = [
    'project' => isset($project) ? $project : null,
];
?>

@extends('layouts.app')

@section('content')
    <project-page v-bind="{{json_encode($attrs)}}"/>
@endsection
