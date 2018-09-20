@extends('app.blueprints.framed')

@section('main')
	@include('app.shared.breadcrumbs',['crumbs' => [
		[
			'name' => __('Dashboard'),
			'url' => '#',
		],
	]])
@endsection