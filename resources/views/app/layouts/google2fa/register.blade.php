@extends('app.blueprints.framed')

@section('title')
	Activate Google Authenticator
@endsection

@if('app.blueprints.frameless' == $base_template)
	@section('rbg')
		@rbg
	@endsection
@endif

@section('main')
	@if('app.blueprints.framed' == $base_template)
		@include('app.shared.breadcrumbs',['crumbs' => [
			[
				'name' => __('Activate Google Authenticator'),
				'url' => '#',
			],
		]])
	@endif
	@if('app.blueprints.frameless' == $base_template)
		<div class="col-sm-10 offset-sm-1 col-lg-4 offset-lg-4 row-offset-md-2">
		<form action="{{ route('save-google2fa') }}" method="POST" class="card dynamic-bg dynamic-shadow dynamic-color">
			@csrf

			<input type="hidden" name="origin" value="{{ url()->full() }}" />
			<div class="card-header">
				<h4 class="text-center">{{ __('Activate Google Authenticator') }}</h4>
			</div>
			<div class="card-body">
				@if(Session::has('errormessage'))
				<div class="alert alert-danger">
					{{ Session::get('errormessage') }}
				</div>
				@elseif(Session::has('successmessage'))
				<div class="alert alert-danger">
					{{ Session::get('successmessage') }}
				</div>
				@endif
				<p>{{ __('You can scan the following barcode or use code:') }}</p>
				<div class="form-group">
					<input type="text" class="form-control input-sm text-primary" readonly value="{{ $secret }}">
				</div>
				<div class="text-center">
                    <img src="{{ $qri }}">
                </div>
			</div>
			<div class="card-footer">
				<div class="row">
					<div class="col-sm-8">
						<input type="text" class="form-control{{ $errors->has('code') ? ' is-invalid' : '' }}" name=
						"code" value="{{ old('code') }}" required placeholder="{{ __('Verification Code') }}" />
						@if ($errors->has('code'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('code') }}</strong>
                            </span>
                        @endif
					</div>
					<div class="col-sm-4">
						<input type="submit" class="btn btn-dynamic btn-block" value="{{ __('Continue') }}" />
					</div>
				</div>
			</div>
		</form>
	</div>
	@endif
@endsection