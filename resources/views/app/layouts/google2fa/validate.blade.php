@extends('app.blueprints.frameless')

@section('title')
	Google Multifactor Authentication
@endsection

@section('rbg')
	@rbg
@endsection

@section('main')
	<div class="col-sm-10 offset-sm-1 col-lg-4 offset-lg-4 row-offset-md-2">
		<form action="{{ route('validate-google2fa') }}" method="POST" class="card dynamic-bg dynamic-shadow dynamic-color">
			@csrf

			<input type="hidden" name="origin" value="{{ url()->full() }}" />
			<div class="card-header">
				<h4 class="text-center">{{ __('Multifactor Login with Google Authenticator') }}</h4>
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
				<div class="form-group">
					<label>{{ __('Google Authenticator Code') }}</label>
					<input type="text" class="form-control{{ $errors->has('code') ? ' is-invalid' : '' }}" name=
						"code" value="{{ old('code') }}" required autofocus />
					@if ($errors->has('code'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('code') }}</strong>
                        </span>
                    @endif
				</div>
			</div>
			<div class="card-footer">
				<div class="row">
					
					<div class="col-sm-4">
						<input type="submit" class="btn btn-dynamic btn-block" value="{{ __('Continue') }}" />
					</div>
				</div>
			</div>
		</form>
	</div>
@endsection