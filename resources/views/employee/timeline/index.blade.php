@extends('layouts.app')
@section('page-header')
    <div class="row align-items-center bg-white py-2">
        <div class="col">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Timeline</li>
            </ul>
        </div>
    </div>
@endsection
@section('content')
    <employee-timeline :user_id="{{ $user_id }}" />
@endsection
