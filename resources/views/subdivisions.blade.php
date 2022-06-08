<?php
$attrs = [
    'subdivs' => isset($subdivs) ? $subdivs : null,
];
?>

@extends('layouts.app')

@section('content')
    <subdivisions-page v-bind="{{json_encode($attrs)}}"/>
@endsection
