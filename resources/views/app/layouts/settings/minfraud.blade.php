@extends('app.blueprints.framed')

@section('title')
	MinFraud API Settings
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
			'name' => __('MinFraud API Settings'),
			'url' => '#',
		],
	]])
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-4 col-lg-3 col-xl-2 order-last order-md-first">
				@include('app.shared.navs.settings')
			</div>
			<div class="col-md-8 col-lg-9 col-xl-10">
				<h1>{{ __('MinFraud API Settings') }}</h1>
				<form action="{{ route('save-settings') }}" method="POST" autocomplete="off" class="mb-3">
					@csrf

					<input type="hidden" name="section" value="minfraud" />
					<div class="card bg-dark text-white">
						<div class="card-body">
							<div class="form-group">
								<label>License User</label>
								<input type="text" name="user" class="form-control{{ $errors->has('user') ? ' is-invalid' : '' }}"  value="{{ old('user', (is_object($settings) && property_exists($settings, 'user') ? $settings->user : null )) }}" autocomplete="off" />
								@if ($errors->has('user'))
		                            <span class="invalid-feedback" role="alert">
		                                <strong>{{ $errors->first('user') }}</strong>
		                            </span>
		                        @endif
							</div>
							<div class="form-group">
								<label>License Key</label>
								<input type="text" name="key" class="form-control{{ $errors->has('key') ? ' is-invalid' : '' }}"  value="{{ old('key', (is_object($settings) && property_exists($settings, 'key') ? $settings->key : null )) }}" autocomplete="off" />
								@if ($errors->has('key'))
		                            <span class="invalid-feedback" role="alert">
		                                <strong>{{ $errors->first('key') }}</strong>
		                            </span>
		                        @endif
							</div>
						</div>
						<div class="card-footer">
							<input type="submit" class="btn btn-light" value="{{ __('Save MinFraud Settings') }}" />
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection