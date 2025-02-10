@extends('layouts.app')

@section('page-header')
    <div class="row align-items-center shadow border bg-light p-3">
        <div class="col">
            <h3 class="page-title"><i class="fa fa-hand-o-up text-info"></i> Newsfeed</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Newsfeed</li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <div class="container mt-4">
        @forelse($newsfeeds as $newsfeed)
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fa fa-user-circle text-info"></i>
                        {{ $newsfeed->user->name }}
                        shared a {{ ucfirst($newsfeed->type) }}
                    </h5>
                    <p class="card-text">{{ $newsfeed->content }}</p>

                    @if($newsfeed->type === 'announcement')
                        <a href="{{ route('announcement.show', $newsfeed->announcement->id) }}" class="btn btn-info">
                            View Announcement
                        </a>
                    @elseif($newsfeed->type === 'birthday')
                        <p class="card-text">
                            <i class="fa fa-birthday-cake text-warning"></i>
                            Birthday: {{ $newsfeed->birthday->name }} on {{ $newsfeed->birthday->date }}
                        </p>
                    @elseif($newsfeed->type === 'policy')
                        <a href="{{ route('policy.show', $newsfeed->policy->id) }}" class="btn btn-info">
                            View Policy
                        </a>
                    @elseif($newsfeed->type === 'training')
                        <a href="{{ route('training.show', $newsfeed->training->id) }}" class="btn btn-info">
                            View Training
                        </a>
                    @endif

                    <!-- Like and Comment Section -->
                    <div class="mt-3">
                        <button class="btn btn-sm btn-outline-primary"><i class="fa fa-thumbs-up"></i> Like</button>
                        <button class="btn btn-sm btn-outline-secondary"><i class="fa fa-comment"></i> Comment</button>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center">No newsfeed found.</p>
        @endforelse
    </div>
@endsection
