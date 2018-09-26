@extends('app.blueprints.framed')

@section('title')
	Google API Settings
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
			'name' => __('Google API Settings'),
			'url' => '#',
		],
	]])
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-4 col-lg-3 col-xl-2 order-last order-md-first">
				@include('app.shared.navs.settings')
			</div>
			<div class="col-md-8 col-lg-9 col-xl-10">
				<h1>{{ __('Google API Settings') }}</h1>
				<div class="row">
					<div class="col-md-6">
						<form action="{{ route('save-settings') }}" method="POST" class="card">
							@csrf

							<input type="hidden" name="section" value="google-recapcha" />
							<div class="card-header">
								<h4>{{ __('Google ReCAPCHA') }}</h4>
							</div>
							<div class="card-body">
								<div class="form-group">
									<label>API Key</label>
									<input type="text" name="recapcha[key]" class="form-control{{ $errors->has('recapcha.key') ? ' is-invalid' : '' }}"  value="{{ old('recapcha.key', (is_object($settings) && property_exists($settings, 'recapcha') ? $settings->recapcha['key'] : null )) }}" autocomplete="off" />
									@if ($errors->has('recapcha.key'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('recapcha.key') }}</strong>
			                            </span>
			                        @endif
								</div>
								<div class="form-group">
									<label>API Secret</label>
									<input type="text" name="recapcha[secret]" class="form-control{{ $errors->has('recapcha.secret') ? ' is-invalid' : '' }}"  value="{{ old('recapcha.secret', (is_object($settings) && property_exists($settings, 'recapcha') ? $settings->recapcha['secret'] : null )) }}" autocomplete="off" />
									@if ($errors->has('recapcha.secret'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('recapcha.secret') }}</strong>
			                            </span>
			                        @endif
								</div>
								<div class="form-check">
		                            <input class="form-check-input" type="checkbox" name="recapcha[enabled]" id="remember" {{ old('recapcha.enabled', (is_object($settings) && property_exists($settings, 'recapcha') ? $settings->recapcha['enabled'] : false )) ? 'checked' : '' }}>

		                            <label class="form-check-label" for="remember">
		                                {{ __('Enabled') }}
		                            </label>
		                        </div>
							</div>
							<div class="card-footer">
								<input type="submit" class="btn btn-dark" value="{{ __('Save') }}" />
							</div>
						</form>
					</div>
					<div class="col-md-6">
						<form action="{{ route('save-settings') }}" method="POST" class="card">
							@csrf

							<input type="hidden" name="section" value="google-maps" />
							<div class="card-header">
								<h4>{{ __('Google Maps') }}</h4>
							</div>
							<div class="card-body">
								<div class="form-group">
									<label>API Key</label>
									<input type="text" name="maps[key]" class="form-control{{ $errors->has('maps.key') ? ' is-invalid' : '' }}"  value="{{ old('maps.key', (is_object($settings) && property_exists($settings, 'maps') ? $settings->maps['key'] : null )) }}" autocomplete="off" />
									@if ($errors->has('maps.key'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('maps.key') }}</strong>
			                            </span>
			                        @endif
								</div>
								<div class="form-check">
		                            <input class="form-check-input" type="checkbox" name="maps[enabled]" id="remember" {{ old('maps.enabled', (is_object($settings) && property_exists($settings, 'maps') ? $settings->maps['enabled'] : false )) ? 'checked' : '' }}>

		                            <label class="form-check-label" for="remember">
		                                {{ __('Enabled') }}
		                            </label>
		                        </div>
							</div>
							<div class="card-footer">
								<input type="submit" class="btn btn-dark" value="{{ __('Save') }}" />
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection