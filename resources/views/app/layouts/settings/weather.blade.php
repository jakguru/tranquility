@extends('app.blueprints.framed')

@section('title')
	Weather API Settings
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
			'name' => __('Weather API Settings'),
			'url' => '#',
		],
	]])
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-4 col-lg-3 col-xl-2 order-last order-md-first">
				@include('app.shared.navs.settings')
			</div>
			<div class="col-md-8 col-lg-9 col-xl-10">
				<h1>{{ __('Weather API Settings') }}</h1>
				<div class="row">
					<div class="col-md-4">
						<form action="{{ route('save-settings') }}" method="POST" class="card">
							@csrf

							<input type="hidden" name="section" value="yahoo-weather" />
							<div class="card-header">
								<h4>{{ __('Yahoo Weather') }}</h4>
							</div>
							<div class="card-body">
								<div class="form-group">
									<label>Application ID</label>
									<input type="text" name="yahoo[id]" class="form-control{{ $errors->has('yahoo.id') ? ' is-invalid' : '' }}"  value="{{ old('yahoo.id', (is_object($settings) && property_exists($settings, 'yahoo') ? $settings->yahoo['id'] : null )) }}" autocomplete="off" />
									@if ($errors->has('yahoo.id'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('yahoo.id') }}</strong>
			                            </span>
			                        @endif
								</div>
								<div class="form-group">
									<label>Client ID</label>
									<input type="text" name="yahoo[key]" class="form-control{{ $errors->has('yahoo.key') ? ' is-invalid' : '' }}"  value="{{ old('yahoo.key', (is_object($settings) && property_exists($settings, 'yahoo') ? $settings->yahoo['key'] : null )) }}" autocomplete="off" />
									@if ($errors->has('yahoo.key'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('yahoo.key') }}</strong>
			                            </span>
			                        @endif
								</div>
								<div class="form-group">
									<label>Client Secret</label>
									<input type="text" name="yahoo[secret]" class="form-control{{ $errors->has('yahoo.secret') ? ' is-invalid' : '' }}"  value="{{ old('yahoo.secret', (is_object($settings) && property_exists($settings, 'yahoo') ? $settings->yahoo['secret'] : null )) }}" autocomplete="off" />
									@if ($errors->has('yahoo.secret'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('yahoo.secret') }}</strong>
			                            </span>
			                        @endif
								</div>
								<div class="form-check">
		                            <input class="form-check-input" type="checkbox" name="yahoo[enabled]" id="remember" {{ old('yahoo.enabled', (is_object($settings) && property_exists($settings, 'yahoo') ? $settings->yahoo['enabled'] : false )) ? 'checked' : '' }}>

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
					<div class="col-md-4">
						<form action="{{ route('save-settings') }}" method="POST" class="card">
							@csrf

							<input type="hidden" name="section" value="openweathermap-weather" />
							<div class="card-header">
								<h4>{{ __('OpenWeatherMap') }}</h4>
							</div>
							<div class="card-body">
								<div class="form-group">
									<label>API Key</label>
									<input type="text" name="openweathermap[key]" class="form-control{{ $errors->has('openweathermap.key') ? ' is-invalid' : '' }}"  value="{{ old('openweathermap.key', (is_object($settings) && property_exists($settings, 'openweathermap') ? $settings->openweathermap['key'] : null )) }}" autocomplete="off" />
									@if ($errors->has('openweathermap.key'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('openweathermap.key') }}</strong>
			                            </span>
			                        @endif
								</div>
								<div class="form-check">
		                            <input class="form-check-input" type="checkbox" name="openweathermap[enabled]" {{ old('openweathermap.enabled', (is_object($settings) && property_exists($settings, 'openweathermap') ? $settings->openweathermap['enabled'] : false )) ? 'checked' : '' }}>

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
					<div class="col-md-4">
						<form action="{{ route('save-settings') }}" method="POST" class="card">
							@csrf

							<input type="hidden" name="section" value="accuweather-weather" />
							<div class="card-header">
								<h4>{{ __('AccuWeather') }}</h4>
							</div>
							<div class="card-body">
								<div class="form-group">
									<label>API Key</label>
									<input type="text" name="accuweather[key]" class="form-control{{ $errors->has('accuweather.key') ? ' is-invalid' : '' }}"  value="{{ old('accuweather.key', (is_object($settings) && property_exists($settings, 'accuweather') ? $settings->accuweather['key'] : null )) }}" autocomplete="off" />
									@if ($errors->has('accuweather.key'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('accuweather.key') }}</strong>
			                            </span>
			                        @endif
								</div>
								<div class="form-check">
		                            <input class="form-check-input" type="checkbox" name="accuweather[enabled]" {{ old('accuweather.enabled', (is_object($settings) && property_exists($settings, 'accuweather') ? $settings->accuweather['enabled'] : false )) ? 'checked' : '' }}>

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