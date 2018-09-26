@extends('app.blueprints.frameless')

@section('rbg')
	@rbg
	{{ \App\Helpers\GoogleReCAPCHAHelper::injectJS() }}
@endsection

@section('title')
	Login
@endsection

@section('main')
	<div class="col-sm-10 offset-sm-1 col-lg-4 offset-lg-4">
		<form action="{{ route('submit-login') }}" method="POST" class="card dynamic-bg dynamic-shadow dynamic-color">
			@csrf

			<div class="card-header">
				<h4 class="text-center">{{ config('app.name') }}</h4>
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
				<div class="row form-group">
					<label for="email" class="col-sm-5 col-form-label text-md-right">{{ __('Email') }}</label>
					<div class="col-sm-7">
                        <input id="email" type="email" class="form-control input-sm{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus>

                        @if ($errors->has('email'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
				</div>
				<div class="row form-group">
					<label for="password" class="col-sm-5 col-form-label text-md-right">{{ __('Password') }}</label>
					<div class="col-sm-7">
                        <input id="password" type="password" class="form-control input-sm{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                        @if ($errors->has('password'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
				</div>
				<div class="row">
                    <div class="col-sm-7 offset-sm-5">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                            <label class="form-check-label" for="remember">
                                {{ __('Remember Me') }}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row">
                	<div class="col-sm-7 offset-sm-5">
                		@if(\App\Helpers\GoogleReCAPCHAHelper::enabled())
                    	{{ \App\Helpers\GoogleReCAPCHAHelper::injectDiv() }}
                    	@if ($errors->has('g-recaptcha-response'))
                            <span class="text-danger" role="alert">
                                <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                            </span>
                        @endif
						@endif
                	</div>
                </div>
			</div>
			<div class="card-footer">
				<div class="row">
                    <div class="col-sm-7 offset-sm-5">
                    	<input type="submit" class="btn btn-dynamic" value="{{ __('Log In') }}" />
					</div>
				</div>
			</div>
		</form>
	</div>
@endsection