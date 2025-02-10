@extends('layouts.app')

@section('styles')

@endsection

@section('page-header')
<div class="row">
	<div class="col-sm-12">
		<h3 class="page-title">Activities</h3>
		<ul class="breadcrumb">
			<li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
			<li class="breadcrumb-item active">Activities</li>
		</ul>
	</div>
</div>
@endsection

@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="activity">
			<div class="activity-box">
				<ul class="activity-list">
					<li>
						<div class="activity-user">
							<a href="profile.html" title="Lesley Grauer" data-toggle="tooltip" class="avatar">
								<img src="assets/img/profiles/avatar-01.jpg" alt="">
							</a>
						</div>
						<div class="activity-content">
							<div class="timeline-content">
								<a href="profile.html" class="name">Douglas Rono</a> applied for a <a href="#">weekend leave</a>
								<span class="time">4 mins ago</span>
							</div>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>
@endsection


@section('scripts')

@endsection
