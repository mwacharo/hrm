@extends('layouts.app')

@section('page-header')
    <div class="row align-items-center shadow border bg-light p-3">
        <div class="col">
            <h3 class="page-title"><i class="fa fa-bell text-info"></i> Notifications</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Notifications</li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    
@endsection
