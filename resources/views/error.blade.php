<?php
$attrs = [
    'title' => isset($title) ? $title : null,
    'code' => isset($code) ? $code : null,
];
?>

@extends('layouts.app')

@section('content')
    <error-page v-bind="{{json_encode($attrs)}}"/>
@endsection
