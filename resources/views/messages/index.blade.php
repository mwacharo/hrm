@extends('layouts.app')

@section('page-header')
    <div class="row align-items-center bg-white shadow rounded p-3">
        <div class="col">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">my chats</li>
            </ul>
        </div>
        <div class="col-auto float-right ml-auto">
            <a href="/employee/messages/team" class="btn btn-warning mx-2">
                <i class="fa fa-list-alt"></i> Team Chat
            </a>
            <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_message">
                <i class="fa fa-plus"></i> New Message
            </a>
        </div>
    </div>
@endsection

@section('content')

@endsection

