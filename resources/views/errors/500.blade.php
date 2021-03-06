@extends('app.blueprints.frameless')

@section('rbg')
	@rbg
@endsection

@section('main')
	<div class="col-sm-10 offset-sm-1 col-lg-4 offset-lg-4">
		<div class="card dynamic-bg dynamic-shadow dynamic-color">
			<div class="card-header">
				<h4>{{ __('Oops, An Error has Occured') }}</h4>
			</div>
			<div class="card-body">
				<p>{{ __('An error has occured while processing your request.') }}</p>
				<p>{{ __('Please check with a system administrator.') }}</p>
			</div>
			<div class="card-footer">
				<a href="{{ url()->previous() }}" class="btn btn-dynamic">{{ __('Go Back') }}</a>
			</div>
		</div>
	</div>
@endsection