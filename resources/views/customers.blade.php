<?php
$attrs = [
    'customers' => isset($customers) ? $customers : null,
];
?>

@extends('layouts.app')

@section('content')
    <customers-page v-bind="{{json_encode($attrs)}}"/>
@endsection
