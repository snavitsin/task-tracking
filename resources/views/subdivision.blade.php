<?php
$attrs = [
    'subdiv' => isset($subdiv) ? $subdiv : null,
];
?>

@extends('layouts.app')

@section('content')
    <subdivision-page v-bind="{{json_encode($attrs)}}"/>
@endsection
