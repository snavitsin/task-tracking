<?php
$attrs = [
    'projects' => isset($projects) ? $projects : null,
];
?>

@extends('layouts.app')

@section('content')
    <projects-page v-bind="{{json_encode($attrs)}}"/>
@endsection
