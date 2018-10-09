@extends('app.blueprints.framed')

@section('title')
	{{ __('Debug') }}
@endsection

@section('main')
	@include('app.shared.breadcrumbs',['crumbs' => [
		[
			'name' => config('app.name'),
			'url' => route('dashboard'),
		],
		[
			'name' => __('Debug'),
			'url' => '#',
		],
	]])
	<div class="container">
	</div>
@endsection