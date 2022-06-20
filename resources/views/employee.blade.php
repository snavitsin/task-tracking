<?php
$attrs = [
    'emp' => isset($emp) ? $emp : null,
    'subdivisions' => isset($subdivisions) ? $subdivisions : null,
    'positions' => isset($positions) ? $positions : null,
    'statuses' => isset($statuses) ? $statuses : null,
    'priority' => isset($priority) ? $priority : null,
];
?>

@extends('layouts.app')

@section('content')
    <employee-page v-bind="{{json_encode($attrs)}}"/>
@endsection
