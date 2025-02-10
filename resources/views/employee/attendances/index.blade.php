@extends('layouts.app')

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">My Attendances</li>
            </ul>
        </div>
    </div>
@endsection

@section('content')
    <employee-attendance :user-id="{{ $user_id }}" />
@endsection
