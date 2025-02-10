@extends('layouts.app')
@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">My Payslips</li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <employee-payslips :user_id="{{ $user_id }}" />
@endsection
