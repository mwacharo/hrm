@extends('layouts.app')
@section('page-header')
    <div>
        <div class="col">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Tickets</li>
            </ul>
        </div>
    </div>
@endsection
@section('content')
    <employee-tickets :user_id="{{ $user_id }}" />
@endsection
