@extends('app.blueprints.frameless')

@section('rbg')
	@rbg
@endsection

@section('main')
	<div class="col-sm-10 offset-sm-1 col-lg-4 offset-lg-4">
		<div class="card dynamic-bg dynamic-shadow dynamic-color">
			<div class="card-header">
				<h4>{{ __('Oops') }}</h4>
			</div>
			<div class="card-body">
				<p>{{ __('Sorry, you are not authorized to access this page.') }}</p>
			</div>
			<div class="card-footer">
				<a href="{{ url()->previous() }}" class="btn btn-dynamic">{{ __('Go Back') }}</a>
			</div>
		</div>
	</div>
@endsection