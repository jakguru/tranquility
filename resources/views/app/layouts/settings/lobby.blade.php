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
			'url' => '#',
		],
	]])
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-4 col-lg-3 col-xl-2 order-last order-md-first">
				@include('app.shared.navs.settings')
			</div>
			<div class="col-md-8 col-lg-9 col-xl-10">
				<div class="jumbotron">
					<h1 class="display-4">{{ __('Application Settings') }}</h1>
					<p class="lead">{{ __('Manage your CRM\'s settings.') }}</p>
					<hr class="my-4">
					<p>{{ __('Choose one of the Setting Sections to manage your application settings.') }}</p>
				</div>
			</div>
		</div>
	</div>
@endsection