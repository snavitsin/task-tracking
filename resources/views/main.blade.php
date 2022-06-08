<?php
$attrs = [
    'tasks' => isset($tasks) ? $tasks : null,
    'statuses' => isset($statuses) ? $statuses : null,
    'editable' => isset($editable) ? $editable : null,
];
?>

@extends('layouts.app')

@section('content')
    <main-page v-bind="{{json_encode($attrs)}}"/>
@endsection
