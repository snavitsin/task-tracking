<?php
$attrs = [
    'customer' => isset($customer) ? $customer : null,
];
?>

@extends('layouts.app')

@section('content')
    <customer-page v-bind="{{json_encode($attrs)}}"/>
@endsection
