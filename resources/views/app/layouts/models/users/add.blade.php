@extends('app.blueprints.framed')

@section('title')
	{{ __('Add User') }}
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
			'name' => __('Users'),
			'url' => route('settings-users'),
		],
		[
			'name' => __('Add User'),
			'url' => '#',
		],
	]])
	<form class="container-fluid" action="{{ route('create-user') }}" method="POST">
		@csrf

		<div class="row">
			<div class="col-lg-9">
				<div class="card mb-3">
    				<div class="card-header bg-dark text-white">
    					<h4 class="mb-0">
    						{{ __('Personal Information') }}
    						<span class="pull-right">
    							<input type="submit" class="btn btn-sm btn-success" value="{{ __('Save User') }}" />
    						</span>
    					</h4>
    				</div>
    				<div class="card-body">
    					<div class="row">
    						<div class="col-lg-3 col-md-6">
    							<div class="form-group">
    								<label>{{ __('First Name') }}</label>
									<input type="text" name="fName" class="form-control form-control-sm{{ $errors->has('fName') ? ' is-invalid' : '' }}" value="{{ old('fName') }}" required />
									@if ($errors->has('fName'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('fName') }}</strong>
			                            </span>
			                        @endif
    							</div>
    						</div>
    						<div class="col-lg-3 col-md-6">
    							<div class="form-group">
    								<label>{{ __('Last Name') }}</label>
									<input type="text" name="lName" class="form-control form-control-sm{{ $errors->has('lName') ? ' is-invalid' : '' }}" value="{{ old('lName') }}" required />
									@if ($errors->has('lName'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('lName') }}</strong>
			                            </span>
			                        @endif
    							</div>
    						</div>
    						<div class="col-lg-3 col-md-6">
    							<div class="form-group">
    								<label>{{ __('Email') }}</label>
									<input type="email" name="email" class="form-control form-control-sm{{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" required />
									@if ($errors->has('email'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('email') }}</strong>
			                            </span>
			                        @endif
    							</div>
    						</div>
    						<div class="col-lg-3 col-md-6">
    							<div class="form-group">
    								<label>{{ __('Main Phone') }}</label>
									<input type="hidden" name="main_phone_country" value="{{ old('main_phone_country') }}" />
									<input type="tel" name="main_phone" class="form-control form-control-sm{{ $errors->has('main_phone') ? ' is-invalid' : '' }}" value="{{ old('main_phone') }}" />
									@if ($errors->has('main_phone'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('main_phone') }}</strong>
			                            </span>
			                        @endif
    							</div>
    						</div>
    						<div class="col-lg-3 col-md-6">
    							<div class="form-group">
    								<label>{{ __('Mobile Phone') }}</label>
									<input type="hidden" name="mobile_phone_country" value="{{ old('mobile_phone_country') }}" />
									<input type="tel" name="mobile_phone" class="form-control form-control-sm{{ $errors->has('mobile_phone') ? ' is-invalid' : '' }}" value="{{ old('mobile_phone') }}" />
									@if ($errors->has('mobile_phone'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('mobile_phone') }}</strong>
			                            </span>
			                        @endif
    							</div>
    						</div>
    						<div class="col-lg-3 col-md-6">
    							<div class="form-group">
    								<label>{{ __('Home Phone') }}</label>
									<input type="hidden" name="home_phone_country" value="{{ old('home_phone_country') }}" />
									<input type="tel" name="home_phone" class="form-control form-control-sm{{ $errors->has('home_phone') ? ' is-invalid' : '' }}" value="{{ old('home_phone') }}" />
									@if ($errors->has('home_phone'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('home_phone') }}</strong>
			                            </span>
			                        @endif
    							</div>
    						</div>
    						<div class="col-lg-3 col-md-6">
    							<div class="form-group">
    								<label>{{ __('Work Phone') }}</label>
									<input type="hidden" name="work_phone_country" value="{{ old('work_phone_country') }}" />
									<input type="tel" name="work_phone" class="form-control form-control-sm{{ $errors->has('work_phone') ? ' is-invalid' : '' }}" value="{{ old('work_phone') }}" />
									@if ($errors->has('work_phone'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('work_phone') }}</strong>
			                            </span>
			                        @endif
    							</div>
    						</div>
    						<div class="col-lg-3 col-md-6">
    							<div class="form-group">
    								<label>{{ __('Fax Phone') }}</label>
									<input type="hidden" name="fax_phone_country" value="{{ old('fax_phone_country') }}" />
									<input type="tel" name="fax_phone" class="form-control form-control-sm{{ $errors->has('fax_phone') ? ' is-invalid' : '' }}" value="{{ old('fax_phone') }}" />
									@if ($errors->has('fax_phone'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('fax_phone') }}</strong>
			                            </span>
			                        @endif
    							</div>
    						</div>
    						<div class="col-lg-3 col-md-6">
    							<div class="form-group">
    								<label>{{ __('Birthday') }}</label>
									<input type="date" name="birthday" class="form-control form-control-sm{{ $errors->has('birthday') ? ' is-invalid' : '' }}" value="{{ old('birthday') }}" />
									@if ($errors->has('birthday'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('birthday') }}</strong>
			                            </span>
			                        @endif
    							</div>
    						</div>
    						<div class="col-lg-9 col-md-6">
    							<div class="form-group">
    								<label>{{ __('Job Title') }}</label>
									<input type="text" name="title" class="form-control form-control-sm{{ $errors->has('title') ? ' is-invalid' : '' }}" value="{{ old('title') }}" />
									@if ($errors->has('title'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('title') }}</strong>
			                            </span>
			                        @endif
    							</div>
    						</div>
    					</div>
    					<div class="row">
    						<div class="col-12">
    							<div class="row">
    								<div class="col-md-6">
    									<div class="form-group">
		    								<label>{{ __('Address Line 1') }}</label>
											<input type="text" name="address_line_1" class="form-control form-control-sm{{ $errors->has('address_line_1') ? ' is-invalid' : '' }}" value="{{ old('address_line_1') }}" />
											@if ($errors->has('address_line_1'))
					                            <span class="invalid-feedback" role="alert">
					                                <strong>{{ $errors->first('address_line_1') }}</strong>
					                            </span>
					                        @endif
		    							</div>
		    						</div>
		    						<div class="col-md-6">
		    							<div class="form-group">
		    								<label>{{ __('Address Line 2') }}</label>
											<input type="text" name="address_line_2" class="form-control form-control-sm{{ $errors->has('address_line_2') ? ' is-invalid' : '' }}" value="{{ old('address_line_2') }}" />
											@if ($errors->has('address_line_2'))
					                            <span class="invalid-feedback" role="alert">
					                                <strong>{{ $errors->first('address_line_2') }}</strong>
					                            </span>
					                        @endif
		    							</div>
    								</div>
    								<div class="col-md-6 col-lg-3">
    									<div class="form-group">
		    								<label>{{ __('City') }}</label>
											<input type="text" name="city" class="form-control form-control-sm{{ $errors->has('city') ? ' is-invalid' : '' }}" value="{{ old('city') }}" />
											@if ($errors->has('city'))
					                            <span class="invalid-feedback" role="alert">
					                                <strong>{{ $errors->first('city') }}</strong>
					                            </span>
					                        @endif
		    							</div>
    								</div>
    								<div class="col-md-6 col-lg-3">
    									<div class="form-group">
		    								<label>{{ __('State') }}</label>
											<input type="text" name="state" class="form-control form-control-sm{{ $errors->has('state') ? ' is-invalid' : '' }}" value="{{ old('state') }}" />
											@if ($errors->has('state'))
					                            <span class="invalid-feedback" role="alert">
					                                <strong>{{ $errors->first('state') }}</strong>
					                            </span>
					                        @endif
		    							</div>
    								</div>
    								<div class="col-md-6 col-lg-3">
    									<div class="form-group">
		    								<label>{{ __('Postal') }}</label>
											<input type="text" name="postal" class="form-control form-control-sm{{ $errors->has('postal') ? ' is-invalid' : '' }}" value="{{ old('postal') }}" />
											@if ($errors->has('postal'))
					                            <span class="invalid-feedback" role="alert">
					                                <strong>{{ $errors->first('postal') }}</strong>
					                            </span>
					                        @endif
		    							</div>
    								</div>
    								<div class="col-md-6 col-lg-3">
    									<div class="form-group">
		    								<label>{{ __('Country') }}</label>
		    								<select name="country" class="form-control form-control-sm{{ $errors->has('country') ? ' is-invalid' : '' }}">
		    									@if('XX' == old('country', 'XX'))
		    									<option value="XX" selected disabled>{{__('Unknown Country')}}</option>
		    									@endif
		    									@foreach(\App\Helpers\CountryHelper::$countries as $iso => $info)
		    									@if('XX' !== $iso)
		    									<option value="{{$iso}}"{{$iso == old('country', 'XX') ? ' selected' : ''}}>{{__($info['name'])}}</option>
		    									@endif
		    									@endforeach
		    								</select>
											@if ($errors->has('postal'))
					                            <span class="invalid-feedback" role="alert">
					                                <strong>{{ $errors->first('postal') }}</strong>
					                            </span>
					                        @endif
		    							</div>
    								</div>
    							</div>
    						</div>
    					</div>
    					<div class="row">
    						<div class="col-lg-3 col-md-6">
    							<div class="form-group">
    								<label>{{ __('Whatsapp Phone') }}</label>
									<input type="hidden" name="whatsapp_phone_country" value="{{ old('whatsapp_phone_country') }}" />
									<input type="tel" name="whatsapp_phone" class="form-control form-control-sm{{ $errors->has('whatsapp_phone') ? ' is-invalid' : '' }}" value="{{ old('whatsapp_phone') }}" />
									@if ($errors->has('whatsapp_phone'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('whatsapp_phone') }}</strong>
			                            </span>
			                        @endif
    							</div>
    						</div>
    						<div class="col-lg-3 col-md-6">
    							<div class="form-group">
    								<label>{{ __('Telegram Phone') }}</label>
									<input type="hidden" name="telegram_phone_country" value="{{ old('telegram_phone_country') }}" />
									<input type="tel" name="telegram_phone" class="form-control form-control-sm{{ $errors->has('telegram_phone') ? ' is-invalid' : '' }}" value="{{ old('telegram_phone') }}" />
									@if ($errors->has('telegram_phone'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('telegram_phone') }}</strong>
			                            </span>
			                        @endif
    							</div>
    						</div>
    						<div class="col-lg-3 col-md-6">
    							<div class="form-group">
    								<label>{{ __('Viber Phone') }}</label>
									<input type="hidden" name="viber_phone_country" value="{{ old('viber_phone_country') }}" />
									<input type="tel" name="viber_phone" class="form-control form-control-sm{{ $errors->has('viber_phone') ? ' is-invalid' : '' }}" value="{{ old('viber_phone') }}" />
									@if ($errors->has('viber_phone'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('viber_phone') }}</strong>
			                            </span>
			                        @endif
    							</div>
    						</div>
    						<div class="col-lg-3 col-md-6">
    							<div class="form-group">
    								<label>{{ __('Skype Name') }}</label>
									<input type="text" name="skype" class="form-control form-control-sm{{ $errors->has('skype') ? ' is-invalid' : '' }}" value="{{ old('skype') }}" />
									@if ($errors->has('skype'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('skype') }}</strong>
			                            </span>
			                        @endif
    							</div>
    						</div>
    						<div class="col-lg-4 col-md-6">
    							<div class="form-group">
    								<label>{{ __('Facebook Profile') }}</label>
									<input type="url" name="facebook" class="form-control form-control-sm{{ $errors->has('facebook') ? ' is-invalid' : '' }}" value="{{ old('facebook') }}" />
									@if ($errors->has('facebook'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('facebook') }}</strong>
			                            </span>
			                        @endif
    							</div>
    						</div>
    						<div class="col-lg-4 col-md-6">
    							<div class="form-group">
    								<label>{{ __('Google+ Profile') }}</label>
									<input type="url" name="googleplus" class="form-control form-control-sm{{ $errors->has('googleplus') ? ' is-invalid' : '' }}" value="{{ old('googleplus') }}" />
									@if ($errors->has('googleplus'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('googleplus') }}</strong>
			                            </span>
			                        @endif
    							</div>
    						</div>
    						<div class="col-lg-4 col-md-6">
    							<div class="form-group">
    								<label>{{ __('LinkedIn Profile') }}</label>
									<input type="url" name="linkedin" class="form-control form-control-sm{{ $errors->has('linkedin') ? ' is-invalid' : '' }}" value="{{ old('linkedin') }}" />
									@if ($errors->has('linkedin'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('linkedin') }}</strong>
			                            </span>
			                        @endif
    							</div>
    						</div>
    					</div>
    				</div>
				</div>
				<div class="card mb-3">
					<div class="card-header bg-dark text-white">
    					<h4 class="mb-0">{{ __('Preferences') }}</h4>
    				</div>
    				<div class="card-body">
    					<div class="row">
    						<div class="col-lg-3 col-md-6">
    							<div class="form-group">
    								<label>{{ __('Time Zone') }}</label>
									<select name="timezone" class="form-control form-control-sm{{ $errors->has('timezone') ? ' is-invalid' : '' }}" required autocomplete="off">
									@foreach(\DateTimeZone::listIdentifiers(\DateTimeZone::ALL) as $tz)
									<option value="{{ $tz }}"{{ $tz == (old('timezone', config('app.timezone'))) ? ' selected' : '' }}>{{ ucwords(str_replace('_', ' ', $tz)) }}</option>
									@endforeach
								</select>
									@if ($errors->has('timezone'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('timezone') }}</strong>
			                            </span>
			                        @endif
    							</div>
    						</div>
    						<div class="col-lg-3 col-md-6">
    							<div class="form-group">
    								<label>{{ __('Locale') }}</label>
									<select name="locale" class="form-control form-control-sm{{ $errors->has('locale') ? ' is-invalid' : '' }}" required autocomplete="off">
									@foreach(\App\Http\Controllers\SettingsController::getListOfLanguages() as $value => $label)
									<option value="{{ $value }}"{{ $value == (old('locale', config('app.locale'))) ? ' selected' : '' }}>{{ $label }}</option>
									@endforeach
								</select>
									@if ($errors->has('locale'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('locale') }}</strong>
			                            </span>
			                        @endif
    							</div>
    						</div>
    						<div class="col-lg-3 col-md-6">
    							<div class="form-group">
    								<label>{{ __('Temperature Unit') }}</label>
									<select name="temperature_unit" class="form-control form-control-sm{{ $errors->has('temperature_unit') ? ' is-invalid' : '' }}" required autocomplete="off">
									@foreach(['celsius' => __('Metric'), 'fahrenheit' => __('Imperial')] as $tz => $label)
									<option value="{{ $tz }}"{{ $tz == (old('temperature_unit')) ? ' selected' : '' }}>{{ ucwords(str_replace('_', ' ', $label)) }}</option>
									@endforeach
								</select>
									@if ($errors->has('temperature_unit'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('temperature_unit') }}</strong>
			                            </span>
			                        @endif
    							</div>
    						</div>
    						<div class="col-lg-3 col-md-6">
    							<div class="form-group">
    								<label>{{ __('Date Format') }}</label>
									<input type="text" name="dateformat" class="form-control form-control-sm{{ $errors->has('dateformat') ? ' is-invalid' : '' }}" value="{{ old('dateformat', config('app.dateformat')) }}" />
									@if ($errors->has('dateformat'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('dateformat') }}</strong>
			                            </span>
			                        @endif
    							</div>
    						</div>
    						<div class="col-lg-3 col-md-6">
    							<div class="form-group">
    								<label>{{ __('Time Format') }}</label>
									<input type="text" name="timeformat" class="form-control form-control-sm{{ $errors->has('timeformat') ? ' is-invalid' : '' }}" value="{{ old('timeformat', config('app.timeformat')) }}" />
									@if ($errors->has('timeformat'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('timeformat') }}</strong>
			                            </span>
			                        @endif
    							</div>
    						</div>
    						<div class="col-lg-3 col-md-6">
    							<div class="form-group">
    								<label>{{ __('Date/Time Format') }}</label>
									<input type="text" name="datetimeformat" class="form-control form-control-sm{{ $errors->has('datetimeformat') ? ' is-invalid' : '' }}" value="{{ old('datetimeformat', config('app.datetimeformat')) }}" />
									@if ($errors->has('datetimeformat'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('datetimeformat') }}</strong>
			                            </span>
			                        @endif
    							</div>
    						</div>
    					</div>
    				</div>
				</div>
			</div>
			<div class="col-lg-3">
				<div class="card mb-3">
					<div class="card-header bg-dark text-white">
    					<h4 class="mb-0">{{ __('Security') }}</h4>
    				</div>
    				<div class="card-body">
    					<div class="form-check">
                            <input class="form-check-input" type="checkbox" name="active" id="active" value="1" {{ old('active') ? 'checked' : '' }}>

                            <label class="form-check-label" for="active">
                                {{ __('Active') }}
                            </label>

                            @if ($errors->has('active'))
	                            <span class="invalid-feedback" role="alert">
	                                <strong>{{ $errors->first('active') }}</strong>
	                            </span>
	                        @endif
                        </div>
						<div class="form-group">
							<label>{{ __('Role') }}</label>
							<select name="role_id" class="form-control form-control-sm{{ $errors->has('role') ? ' is-invalid' : '' }}">
								@foreach(\App\Role::getSelectChoices() as $value => $label)
								<option value="{{$value}}">{{$label}}</option>
								@endforeach
							</select>
							@if ($errors->has('role'))
	                            <span class="invalid-feedback" role="alert">
	                                <strong>{{ $errors->first('role') }}</strong>
	                            </span>
	                        @endif
						</div>
    				</div>
				</div>
				<div class="card mb-3">
					<div class="card-header bg-dark text-white">
    					<h4 class="mb-0">{{ __('Authentication') }}</h4>
    				</div>
    				<div class="card-body">
    					<div class="form-group">
							<label>{{ __('Password') }}</label>
							<div class="input-group input-group-sm{{ $errors->has('password') ? ' is-invalid' : '' }}">
								<input type="password" name="password" class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" value="" />
								<div class="input-group-append">
									<button class="btn btn-outline-secondary" type="button" reveal-password title="{{ __('Reveal Contents') }}"><i class="far fa-eye"></i></button>
									<button class="btn btn-outline-secondary" type="button" generate-password title="{{ __('Generate Password') }}"><i class="fas fa-retweet"></i></button>
								</div>
							</div>
							@if ($errors->has('password'))
	                            <span class="invalid-feedback" role="alert">
	                                <strong>{{ $errors->first('password') }}</strong>
	                            </span>
	                        @endif
						</div>
						<div class="form-group">
							<label>{{ __('Confirm Password') }}</label>
							<div class="input-group input-group-sm{{ $errors->has('password') ? ' is-invalid' : '' }}">
								<input type="password" name="password_confirmation" class="form-control {{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" value="" />
								<div class="input-group-append">
									<button class="btn btn-outline-secondary" type="button" reveal-password title="{{ __('Reveal Contents') }}"><i class="far fa-eye"></i></button>
								</div>
							</div>
							@if ($errors->has('password_confirmation'))
	                            <span class="invalid-feedback" role="alert">
	                                <strong>{{ $errors->first('password_confirmation') }}</strong>
	                            </span>
	                        @endif
						</div>
						<div class="form-group">
							<label>{{ __('Google Authenticator Secret') }}</label>
	    					<input type="text" class="form-control form-control-sm{{ $errors->has('google2fa_secret') ? ' is-invalid' : '' }}" name=
							"google2fa_secret" value="{{ old('google2fa_secret') }}" />
							@if ($errors->has('google2fa_secret'))
	                            <span class="invalid-feedback" role="alert">
	                                <strong>{{ $errors->first('google2fa_secret') }}</strong>
	                            </span>
	                        @endif
	                    </div>
    				</div>
				</div>
				<div class="card mb-3">
					<div class="card-header bg-dark text-white">
    					<h4 class="mb-0">{{ __('Groups') }}</h4>
    				</div>
    				@if ($errors->has('groups.*'))
                        <div class="card-body pt-0 pb-0">
                        	@foreach($errors->get('groups.*') as $message)
	                        <div class="alert alert-danger mb-0">{{ $message[0] }}</div>
	                        @endforeach
                        </div>
                    @endif
    				<div class="table-responsive max-height-200">
    					<table class="table table-sm table-striped table-hover mb-0">
    						<thead>
    							<tr>
    								<th class="text-center">&nbsp;</th>
    								<th>{{__('Group')}}</th>
    							</tr>
    						</thead>
    						<tbody>
    							@foreach(\App\Group::all() as $group)
    							<tr>
    								<td class="text-center">
    									<input type="checkbox" name="groups[{{$group->id}}]" />
    								</td>
    								<td>{{$group->name}}</td>
    							</tr>
    							@endforeach
    						</tbody>
    						<tfoot>
    							<tr>
    								<th class="text-center">&nbsp;</th>
    								<th>{{__('Group')}}</th>
    							</tr>
    						</tfoot>
    					</table>
    				</div>
				</div>
			</div>
		</div>
	</form>
@endsection