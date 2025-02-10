@extends('layouts.app')

@section('content')
    <requisition :user-id="{{ json_encode($userId) }}" />

    
@endsection
