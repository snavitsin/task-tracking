<?php
$attrs = [
    //'content' => isset($content) ? $content : null,
];
?>

@extends('layouts.app')

@section('content')
    <login-page v-bind="{{json_encode($attrs)}}"/>
@endsection
