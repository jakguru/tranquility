@extends('app.blueprints.framed')

@section('title')
	Search
@endsection

@section('main')
	@include('app.shared.breadcrumbs',['crumbs' => [
		[
			'name' => config('app.name'),
			'url' => route('dashboard'),
		],
		[
			'name' => __('Search Results'),
			'url' => '#',
		],
	]])
	@if(!is_a($results, '\Illuminate\Support\Collection') || $results->isEmpty())
		@include('app.shared.alerts.warning', ['message' => __('Your search has not returned any results.')])
	@else
		<pre>{{ print_r($results, true) }}</pre>
	@endif
@endsection