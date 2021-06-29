@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg">
            <div class="card">
                <div class="card-header">{{ __('Task list') }} -
                    @role('developer')Меню разработчика @endrole
                    @role('manager')Меню руководителя @endrole
                    @role('tester')Меню тестировщика @endrole</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        @role('manager')
                        <task-list
                                :props-data="{{ json_encode(['tasks' => $tasks,
                                 'comments' => $comments,
                                  'employees' => $employees,
                                  'projects' => $projects,
                                  'statistics' => $statistics,
                                  'isManager' => true]) }}"/>
                        @endrole

                        @role('developer')
{{--                        {{$user = App\Models\User::find(10)}}--}}
{{--                        {{dd($user->hasPermissionTo('operate-own-task'))}}--}}
                        <task-list
                                :props-data="{{ json_encode(['tasks' => $tasks,
                                 'comments' => $comments,
                                  'employees' => $employees,
                                  'projects' => $projects,
                                  'statistics' => $statistics,
                                  'isDeveloper' => true]) }}"/>
                        @endrole

                        @role('tester')
                        {{--                        {{$user = App\Models\User::find(10)}}--}}
                        {{--                        {{dd($user->hasPermissionTo('operate-own-task'))}}--}}
                        <task-list :props-data="{{ json_encode(['tasks' => $tasks,
                         'comments' => $comments,
                          'employees' => $employees,
                          'projects' => $projects,
                          'statistics' => $statistics,
                          'isTester' => true]) }}"/>
                        @endrole

                </div>
            </div>
        </div>
    </div>
</div>
@endsection