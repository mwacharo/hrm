@extends('layouts.app')


@section('content')
    <leave-balances :user-id="{{ json_encode($userId) }}" />
@endsection
