@extends('app.blueprints.framed')

@section('title')
	System Settings
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
			'name' => __('System Settings'),
			'url' => '#',
		],
	]])
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-4 col-lg-3 col-xl-2 order-last order-md-first">
				@include('app.shared.navs.settings')
			</div>
			<div class="col-md-8 col-lg-9 col-xl-10">
				<form action="{{ route('save-settings') }}" method="POST" autocomplete="off" class="mb-3">
					@csrf

					<h1>{{ __('System Settings') }}</h1>
					<input type="hidden" name="section" value="system" />
					<div class="card">
						<div class="alert alert-danger mb-0">
							<strong>{{ __('Danger Zone') }}</strong> {{ __('These settings affect the entire CRM. Changing these settings may have unintended consiquences.') }}
						</div>
						<div class="card-body">
							<div class="form-group">
								<label>Application Name</label>
								<input type="text" name="name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"  value="{{ old('name', config('app.name')) }}" required autocomplete="off" />
								@if ($errors->has('name'))
		                            <span class="invalid-feedback" role="alert">
		                                <strong>{{ $errors->first('name') }}</strong>
		                            </span>
		                        @endif
							</div>
							<div class="form-group">
								<label>System Time Zone</label>
								<select name="timezone" class="form-control{{ $errors->has('timezone') ? ' is-invalid' : '' }}" required autocomplete="off">
									@foreach(\DateTimeZone::listIdentifiers(\DateTimeZone::ALL) as $tz)
									<option value="{{ $tz }}"{{ $tz == old('timezone', config('app.timezone')) ? ' selected' : '' }}>{{ ucwords(str_replace('_', ' ', $tz)) }}</option>
									@endforeach
								</select>
								@if ($errors->has('timezone'))
		                            <span class="invalid-feedback" role="alert">
		                                <strong>{{ $errors->first('timezone') }}</strong>
		                            </span>
		                        @endif
							</div>
							<div class="form-group">
								<label>List Size</label>
								<input type="number" min="1" max="100" name="listsize" class="form-control{{ $errors->has('listsize') ? ' is-invalid' : '' }}"  value="{{ old('listsize', config('app.listsize')) }}" required autocomplete="off" />
								@if ($errors->has('listsize'))
		                            <span class="invalid-feedback" role="alert">
		                                <strong>{{ $errors->first('listsize') }}</strong>
		                            </span>
		                        @endif
							</div>
							<div class="form-group">
								<label>Date Format</label>
								<input type="text" name="dateformat" class="form-control{{ $errors->has('dateformat') ? ' is-invalid' : '' }}"  value="{{ old('dateformat', config('app.dateformat')) }}" required autocomplete="off" />
								@if ($errors->has('dateformat'))
		                            <span class="invalid-feedback" role="alert">
		                                <strong>{{ $errors->first('dateformat') }}</strong>
		                            </span>
		                        @endif
							</div>
							<div class="form-group">
								<label>Time Format</label>
								<input type="text" name="timeformat" class="form-control{{ $errors->has('timeformat') ? ' is-invalid' : '' }}"  value="{{ old('timeformat', config('app.timeformat')) }}" required autocomplete="off" />
								@if ($errors->has('timeformat'))
		                            <span class="invalid-feedback" role="alert">
		                                <strong>{{ $errors->first('timeformat') }}</strong>
		                            </span>
		                        @endif
							</div>
							<div class="form-group">
								<label>Date Time Format</label>
								<input type="text" name="datetimeformat" class="form-control{{ $errors->has('datetimeformat') ? ' is-invalid' : '' }}"  value="{{ old('datetimeformat', config('app.datetimeformat')) }}" required autocomplete="off" />
								@if ($errors->has('datetimeformat'))
		                            <span class="invalid-feedback" role="alert">
		                                <strong>{{ $errors->first('datetimeformat') }}</strong>
		                            </span>
		                        @endif
							</div>
						</div>
						<div class="card-footer">
							<input type="submit" class="btn btn-dark" value="{{ __('Save System Settings') }}" />
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection