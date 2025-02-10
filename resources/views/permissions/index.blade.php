@extends('layouts.app')

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Permissions</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">User Permissions</li>
            </ul>
        </div>
    </div>
@endsection

@section('content')
    <permissions />
@endsection
