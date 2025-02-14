@extends('layouts.app')
@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Resources</li>
            </ul>
        </div>

    </div>
@endsection

@section('content')


    <resources 
        :user="{{ json_encode($user) }}" 
        :roles="{{ json_encode($roles) }}" 
        :permissions="{{ json_encode($permissions) }}" 
    />
@endsection
