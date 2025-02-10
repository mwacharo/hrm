@extends('layouts.app')
@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                Designation List
            </li>
        </ol>
    </nav>
    <hr />
    <!-- Designations Component -->
    <designations />
@endsection
