@extends('layouts.app')

@section('page-header')
    <div class="row">
        <div class="col-sm-12">
        </div>
    </div>
@endsection

@section('content')
    @can('view_admin_panel')
        <div class="admin-portal">
            <admin-dashboard :user="{{ json_encode($user) }}" />
        </div>
    @endcan

    @can('view_employee_panel')
        <div class="employee-portal">
            <employee-dashboard
                :user="{{ json_encode(auth()->user()->load('department', 'designation', 'unit', 'office')) }}" />
        </div>
    @endcan
@endsection
