@extends('app.blueprints.framed')

@section('title')
	Dashboard
@endsection

@section('main')
	@include('app.shared.breadcrumbs',['crumbs' => [
		[
			'name' => __('Dashboard'),
			'url' => '#',
		],
	]])
@endsection