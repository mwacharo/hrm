@extends('layouts.app')
@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Appraisals</li>
            </ul>
        </div>
    </div>
@endsection

@section('content')
    <appraisals :user_id="{{ $user_id }}" />
@endsection
