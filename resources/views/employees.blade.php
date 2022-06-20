<?php
$attrs = [
    'emps' => isset($emps) ? $emps : null,
];
?>

@extends('layouts.app')

@section('content')
    <employees-page v-bind="{{json_encode($attrs)}}"/>
@endsection
