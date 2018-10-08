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
		<div class="form-group">
			<div class="multi-model-search multi-model-search-sm">
				<div class="selected-results"></div>
				<input type="search" name="participants" class="form-control" />
			</div>
		</div>
		<div class="form-group">
			<div class="multi-model-search multi-model-search-sm">
				<div class="selected-results"></div>
				<input type="search" name="recipients" class="form-control" />
			</div>
		</div>
	</div>
@endsection