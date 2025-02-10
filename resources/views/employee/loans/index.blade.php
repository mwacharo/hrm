@extends('layouts.app')

@section('page-header')
    <div class="row align-items-centerp-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Welfare Loans</li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <employee-loans :user_id="{{ $user_id }}" />
@endsection
