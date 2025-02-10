@extends('layouts.app')

@section('styles')
@endsection

@section('page-header')
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">User Account</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">My Account</li>
            </ul>
        </div>
    </div>
@endsection

@section('content')

<employee-account :user="{{ json_encode($user) }}"/>
@endsection


