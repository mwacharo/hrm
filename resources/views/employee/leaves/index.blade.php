@extends('layouts.app')
@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Leave</li>
            </ul>
        </div>
    </div>
@endsection

@section('content')
    <employee-leaves :user-id="{{ $userId }}" />
@endsection
