@extends('app.blueprints.framed')

@section('title')
	Settings
@endsection

@section('main')
	@include('app.shared.breadcrumbs',['crumbs' => [
		[
			'name' => config('app.name'),
			'url' => route('dashboard'),
		],
		[
			'name' => __('Settings'),
			'url' => route('settings'),
		],
		[
			'name' => __('Google Settings'),
			'url' => '#',
		],
	]])
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-4 col-lg-3 col-xl-2">
				@include('app.shared.navs.settings')
			</div>
			<div class="col-md-8 col-lg-9 col-xl-10">
				<form action="{{ route('save-settings') }}" method="POST">
					@csrf

					<h1>{{ __('Google Settings') }}</h1>
					<input type="hidden" name="section" value="google" />
				</form>
			</div>
		</div>
	</div>
@endsection