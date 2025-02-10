@extends('layouts.app')

@section('content')
    <leave-requests :user-id="{{ json_encode($userId) }}" />
@endsection