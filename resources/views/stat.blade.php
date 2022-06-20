<?php
$attrs = [
    'emps' => isset($emps) ? $emps : null,
    'projects' => isset($projects) ? $projects : null,
];
?>

@extends('layouts.app')

@section('content')
    <statistics-page v-bind="{{json_encode($attrs)}}"/>
@endsection
