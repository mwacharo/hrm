@extends('layouts.app')

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa fa-home text-info"></i>
                        Dashboard</a></li>
                <li class="breadcrumb-item active">Statistics</li>
            </ul>
        </div>

    </div>
@endsection

@section('content')
    <leave-statistics />
@endsection
