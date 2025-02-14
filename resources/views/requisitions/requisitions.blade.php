@extends('layouts.app')

@section('content')





<requisition
    :user='@json($user)'
    :roles='@json($roles)'
    :permissions='@json($permissions)' />


@endsection