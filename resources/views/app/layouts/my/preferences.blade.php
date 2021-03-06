@extends('app.blueprints.framed')

@section('title')
	{{ __('My Preferences') }}
@endsection

@section('main')
	@include('app.shared.breadcrumbs',['crumbs' => [
		[
			'name' => config('app.name'),
			'url' => route('dashboard'),
		],
		[
			'name' => __('My Preferences'),
			'url' => '#',
		],
	]])

	<div class="container-fluid">
		<div class="row">
			<div class="col-md-9">
				<form class="card mb-3" action="{{ route('my-preferences') }}" method="POST">
					@method('PUT')
					@csrf

					<div class="card-header bg-dark text-white">
						<h4>
							<span class="pull-right">
								<input type="hidden" name="section" value="preferences" />
								<input type="submit" class="btn btn-light" value="{{ __('Save') }}" />
							</span>
							{{ __('My Personal Information') }}
						</h4>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-6 offset-3 col-md-2 offset-md-0 mb-5 mb-md-3">
								<label>{{ __('Your Avatar') }}</label>
								<div class="avatar-upload" data-toggle="modal" data-target="#image-upload-modal">
									<input type="text" class="d-none" name="avatar" id="avatar-field" value="{{ old('avatar') }}" />
									<img src="{{ \App\Helpers\BusinessCardHelper::getUrlForAvatarImage($model, $model->id) }}" class="avatar" id="avatar" />
								</div>
								@if ($errors->has('avatar'))
				                    <span class="invalid-feedback" role="alert">
				                        <strong>{{ $errors->first('avatar') }}</strong>
				                    </span>
				                @endif
							</div>
							<div class="col-12 col-md-10 mb-3 mb-md-1">
								<div class="row">
									<div class="col-12">
										<div class="form-group">
											<label>{{ __('Your Name') }}</label>
											<div class="input-group input-group-sm">
												<input type="text" name="salutation" class="form-control{{ $errors->has('salutation') ? ' is-invalid' : '' }}" placeholder="{{ __('Salutation') }}" value="{{ old('salutation', $model->salutation) }}">
												<input type="text" name="fName" class="form-control{{ $errors->has('fName') ? ' is-invalid' : '' }}" placeholder="{{ __('First Name') }}" value="{{ old('fName', $model->fName) }}" required>
												<input type="text" name="lName" class="form-control{{ $errors->has('lName') ? ' is-invalid' : '' }}" placeholder="{{ __('Last Name') }}" value="{{ old('lName', $model->lName) }}" required>
											</div>
											@if ($errors->has('salutation'))
							                    <span class="invalid-feedback" role="alert">
							                        <strong>{{ $errors->first('salutation') }}</strong>
							                    </span>
							                @endif
							                @if ($errors->has('fName'))
							                    <span class="invalid-feedback" role="alert">
							                        <strong>{{ $errors->first('fName') }}</strong>
							                    </span>
							                @endif
							                @if ($errors->has('lName'))
							                    <span class="invalid-feedback" role="alert">
							                        <strong>{{ $errors->first('lName') }}</strong>
							                    </span>
							                @endif
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-9">
										<div class="form-group">
											<label>{{ __('Job Title') }}</label>
											<input type="text" name="title" class="form-control form-control-sm{{ $errors->has('title') ? ' is-invalid' : '' }}" value="{{ old('title', $model->title) }}" />
											@if ($errors->has('title'))
					                            <span class="invalid-feedback" role="alert">
					                                <strong>{{ $errors->first('title') }}</strong>
					                            </span>
					                        @endif
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
		    								<label>{{ __('Birthday') }}</label>
											<input type="date" name="birthday" class="form-control form-control-sm{{ $errors->has('birthday') ? ' is-invalid' : '' }}" value="{{ old('birthday', $model->birthday) }}" />
											@if ($errors->has('birthday'))
					                            <span class="invalid-feedback" role="alert">
					                                <strong>{{ $errors->first('birthday') }}</strong>
					                            </span>
					                        @endif
		    							</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-2">
										<div class="form-group">
											<label>{{ __('Email Address') }}</label>
											<input type="email" name="email" class="form-control form-control-sm{{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email', $model->email) }}" required />
											@if ($errors->has('email'))
					                            <span class="invalid-feedback" role="alert">
					                                <strong>{{ $errors->first('email') }}</strong>
					                            </span>
					                        @endif
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group">
		    								<label>{{ __('Main Phone') }}</label>
											<input type="hidden" name="main_phone_country" value="{{ old('main_phone_country', $model->main_phone_country) }}" />
											<input type="tel" name="main_phone" class="form-control form-control-sm{{ $errors->has('main_phone') ? ' is-invalid' : '' }}" value="{{ old('main_phone', $model->main_phone) }}" />
											@if ($errors->has('main_phone'))
					                            <span class="invalid-feedback" role="alert">
					                                <strong>{{ $errors->first('main_phone') }}</strong>
					                            </span>
					                        @endif
		    							</div>
		    						</div>
		    						<div class="col-md-2">
		    							<div class="form-group">
		    								<label>{{ __('Mobile Phone') }}</label>
											<input type="hidden" name="mobile_phone_country" value="{{ old('mobile_phone_country', $model->mobile_phone_country) }}" />
											<input type="tel" name="mobile_phone" class="form-control form-control-sm{{ $errors->has('mobile_phone') ? ' is-invalid' : '' }}" value="{{ old('mobile_phone', $model->mobile_phone) }}" />
											@if ($errors->has('mobile_phone'))
					                            <span class="invalid-feedback" role="alert">
					                                <strong>{{ $errors->first('mobile_phone') }}</strong>
					                            </span>
					                        @endif
		    							</div>
		    						</div>
		    						<div class="col-md-2">
		    							<div class="form-group">
		    								<label>{{ __('Home Phone') }}</label>
											<input type="hidden" name="home_phone_country" value="{{ old('home_phone_country', $model->home_phone_country) }}" />
											<input type="tel" name="home_phone" class="form-control form-control-sm{{ $errors->has('home_phone') ? ' is-invalid' : '' }}" value="{{ old('home_phone', $model->home_phone) }}" />
											@if ($errors->has('home_phone'))
					                            <span class="invalid-feedback" role="alert">
					                                <strong>{{ $errors->first('home_phone') }}</strong>
					                            </span>
					                        @endif
		    							</div>
		    						</div>
		    						<div class="col-md-2">
		    							<div class="form-group">
		    								<label>{{ __('Work Phone') }}</label>
											<input type="hidden" name="work_phone_country" value="{{ old('work_phone_country', $model->work_phone_country) }}" />
											<input type="tel" name="work_phone" class="form-control form-control-sm{{ $errors->has('work_phone') ? ' is-invalid' : '' }}" value="{{ old('work_phone', $model->work_phone) }}" />
											@if ($errors->has('work_phone'))
					                            <span class="invalid-feedback" role="alert">
					                                <strong>{{ $errors->first('work_phone') }}</strong>
					                            </span>
					                        @endif
		    							</div>
		    						</div>
		    						<div class="col-md-2">
		    							<div class="form-group">
		    								<label>{{ __('Fax Phone') }}</label>
											<input type="hidden" name="fax_phone_country" value="{{ old('fax_phone_country', $model->fax_phone_country) }}" />
											<input type="tel" name="fax_phone" class="form-control form-control-sm{{ $errors->has('fax_phone') ? ' is-invalid' : '' }}" value="{{ old('fax_phone', $model->fax_phone) }}" />
											@if ($errors->has('fax_phone'))
					                            <span class="invalid-feedback" role="alert">
					                                <strong>{{ $errors->first('fax_phone') }}</strong>
					                            </span>
					                        @endif
		    							</div>
		    						</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6 col-lg-4">
								<div class="form-group">
									<label>{{ __('Address Line 1') }}</label>
									<input type="text" name="address_line_1" class="form-control form-control-sm{{ $errors->has('address_line_1') ? ' is-invalid' : '' }}" value="{{ old('address_line_1', $model->address_line_1) }}" />
									@if ($errors->has('address_line_1'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('address_line_1') }}</strong>
			                            </span>
			                        @endif
								</div>
								<div class="form-group">
									<label>{{ __('Address Line 2') }}</label>
									<input type="text" name="address_line_2" class="form-control form-control-sm{{ $errors->has('address_line_2') ? ' is-invalid' : '' }}" value="{{ old('address_line_2', $model->address_line_2) }}" />
									@if ($errors->has('address_line_2'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('address_line_2') }}</strong>
			                            </span>
			                        @endif
								</div>
								<div class="row">
									<div class="col-md-4">
										<div class="form-group">
		    								<label>{{ __('City') }}</label>
											<input type="text" name="city" class="form-control form-control-sm{{ $errors->has('city') ? ' is-invalid' : '' }}" value="{{ old('city', $model->city) }}" />
											@if ($errors->has('city'))
					                            <span class="invalid-feedback" role="alert">
					                                <strong>{{ $errors->first('city') }}</strong>
					                            </span>
					                        @endif
		    							</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
		    								<label>{{ __('State') }}</label>
											<input type="text" name="state" class="form-control form-control-sm{{ $errors->has('state') ? ' is-invalid' : '' }}" value="{{ old('state', $model->state) }}" />
											@if ($errors->has('state'))
					                            <span class="invalid-feedback" role="alert">
					                                <strong>{{ $errors->first('state') }}</strong>
					                            </span>
					                        @endif
		    							</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
		    								<label>{{ __('Postal') }}</label>
											<input type="text" name="postal" class="form-control form-control-sm{{ $errors->has('postal') ? ' is-invalid' : '' }}" value="{{ old('postal', $model->postal) }}" />
											@if ($errors->has('postal'))
					                            <span class="invalid-feedback" role="alert">
					                                <strong>{{ $errors->first('postal') }}</strong>
					                            </span>
					                        @endif
		    							</div>
									</div>
								</div>
								<div class="form-group">
									<label>{{ __('Country') }}</label>
									<select name="country" class="form-control form-control-sm{{ $errors->has('country') ? ' is-invalid' : '' }}">
										@if('XX' == old('country', $model->country))
										<option value="XX" selected disabled>{{__('Unknown Country')}}</option>
										@endif
										@foreach(\App\Helpers\CountryHelper::$countries as $iso => $info)
										@if('XX' !== $iso)
										<option value="{{$iso}}"{{$iso == old('country', $model->country) ? ' selected' : ''}}>{{__($info['name'])}}</option>
										@endif
										@endforeach
									</select>
									@if ($errors->has('postal'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('postal') }}</strong>
			                            </span>
			                        @endif
								</div>
								<div class="form-group">
									<label>{{ __('Locale') }}</label>
									<select name="locale" class="form-control form-control-sm{{ $errors->has('locale') ? ' is-invalid' : '' }}" required autocomplete="off">
										@foreach(\App\Http\Controllers\SettingsController::getListOfLanguages() as $value => $label)
										<option value="{{ $value }}"{{ $value == (old('locale', is_null($model->locale) ? config('app.locale') : $model->locale)) ? ' selected' : '' }}>{{ $label }}</option>
										@endforeach
									</select>
									@if ($errors->has('locale'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('locale') }}</strong>
			                            </span>
			                        @endif
								</div>
							</div>
							<div class="col-md-6 col-lg-4">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label>{{ __('Whatsapp Phone') }}</label>
											<input type="hidden" name="whatsapp_phone_country" value="{{ old('whatsapp_phone_country', $model->whatsapp_phone_country) }}" />
											<input type="tel" name="whatsapp_phone" class="form-control form-control-sm{{ $errors->has('whatsapp_phone') ? ' is-invalid' : '' }}" value="{{ old('whatsapp_phone', $model->whatsapp_phone) }}" />
											@if ($errors->has('whatsapp_phone'))
					                            <span class="invalid-feedback" role="alert">
					                                <strong>{{ $errors->first('whatsapp_phone') }}</strong>
					                            </span>
					                        @endif
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>{{ __('Telegram Phone') }}</label>
											<input type="hidden" name="telegram_phone_country" value="{{ old('telegram_phone_country', $model->telegram_phone_country) }}" />
											<input type="tel" name="telegram_phone" class="form-control form-control-sm{{ $errors->has('telegram_phone') ? ' is-invalid' : '' }}" value="{{ old('telegram_phone', $model->telegram_phone) }}" />
											@if ($errors->has('telegram_phone'))
					                            <span class="invalid-feedback" role="alert">
					                                <strong>{{ $errors->first('telegram_phone') }}</strong>
					                            </span>
					                        @endif
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label>{{ __('Viber Phone') }}</label>
											<input type="hidden" name="viber_phone_country" value="{{ old('viber_phone_country', $model->viber_phone_country) }}" />
											<input type="tel" name="viber_phone" class="form-control form-control-sm{{ $errors->has('viber_phone') ? ' is-invalid' : '' }}" value="{{ old('viber_phone', $model->viber_phone) }}" />
											@if ($errors->has('viber_phone'))
					                            <span class="invalid-feedback" role="alert">
					                                <strong>{{ $errors->first('viber_phone') }}</strong>
					                            </span>
					                        @endif
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>{{ __('Skype Name') }}</label>
											<input type="text" name="skype" class="form-control form-control-sm{{ $errors->has('skype') ? ' is-invalid' : '' }}" value="{{ old('skype', $model->skype) }}" />
											@if ($errors->has('skype'))
					                            <span class="invalid-feedback" role="alert">
					                                <strong>{{ $errors->first('skype') }}</strong>
					                            </span>
					                        @endif
										</div>
									</div>
								</div>
								<div class="form-group">
									<label>{{ __('Facebook Profile') }}</label>
									<input type="url" name="facebook" class="form-control form-control-sm{{ $errors->has('facebook') ? ' is-invalid' : '' }}" value="{{ old('facebook', $model->facebook) }}" />
									@if ($errors->has('facebook'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('facebook') }}</strong>
			                            </span>
			                        @endif
								</div>
								<div class="form-group">
									<label>{{ __('Google+ Profile') }}</label>
									<input type="url" name="googleplus" class="form-control form-control-sm{{ $errors->has('googleplus') ? ' is-invalid' : '' }}" value="{{ old('googleplus', $model->googleplus) }}" />
									@if ($errors->has('googleplus'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('googleplus') }}</strong>
			                            </span>
			                        @endif
								</div>
								<div class="form-group">
									<label>{{ __('LinkedIn Profile') }}</label>
									<input type="url" name="linkedin" class="form-control form-control-sm{{ $errors->has('linkedin') ? ' is-invalid' : '' }}" value="{{ old('linkedin', $model->linkedin) }}" />
									@if ($errors->has('linkedin'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('linkedin') }}</strong>
			                            </span>
			                        @endif
								</div>
							</div>
							<div class="col-md-6 col-lg-4">
								<div class="form-group">
									<label>{{ __('Time Zone') }}</label>
									<select name="timezone" class="form-control form-control-sm{{ $errors->has('timezone') ? ' is-invalid' : '' }}" required autocomplete="off">
										@foreach(\DateTimeZone::listIdentifiers(\DateTimeZone::ALL) as $tz)
										<option value="{{ $tz }}"{{ $tz == (old('timezone', $model->timezone)) ? ' selected' : '' }}>{{ ucwords(str_replace('_', ' ', $tz)) }}</option>
										@endforeach
									</select>
									@if ($errors->has('timezone'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('timezone') }}</strong>
			                            </span>
			                        @endif
								</div>
								<div class="form-group">
									<label>{{ __('Temperature Unit') }}</label>
									<select name="temperature_unit" class="form-control form-control-sm{{ $errors->has('temperature_unit') ? ' is-invalid' : '' }}" required autocomplete="off">
										@foreach(['celsius' => __('Metric'), 'fahrenheit' => __('Imperial')] as $tz => $label)
										<option value="{{ $tz }}"{{ $tz == (old('temperature_unit', $model->temperature_unit)) ? ' selected' : '' }}>{{ ucwords(str_replace('_', ' ', $label)) }}</option>
										@endforeach
									</select>
									@if ($errors->has('temperature_unit'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('temperature_unit') }}</strong>
			                            </span>
			                        @endif
								</div>
								<div class="form-group">
									<label>{{ __('Date Format') }}</label>
									<input type="text" name="dateformat" class="form-control form-control-sm{{ $errors->has('dateformat') ? ' is-invalid' : '' }}" value="{{ old('dateformat', (is_null($model->dateformat)) ? config('app.dateformat') : $model->dateformat) }}" />
									@if ($errors->has('dateformat'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('dateformat') }}</strong>
			                            </span>
			                        @endif
								</div>
								<div class="form-group">
									<label>{{ __('Time Format') }}</label>
									<input type="text" name="timeformat" class="form-control form-control-sm{{ $errors->has('timeformat') ? ' is-invalid' : '' }}" value="{{ old('timeformat', (is_null($model->timeformat)) ? config('app.timeformat') : $model->timeformat) }}" />
									@if ($errors->has('timeformat'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('timeformat') }}</strong>
			                            </span>
			                        @endif
								</div>
								<div class="form-group">
									<label>{{ __('Date/Time Format') }}</label>
									<input type="text" name="datetimeformat" class="form-control form-control-sm{{ $errors->has('datetimeformat') ? ' is-invalid' : '' }}" value="{{ old('datetimeformat', (is_null($model->datetimeformat)) ? config('app.datetimeformat') : $model->datetimeformat) }}" />
									@if ($errors->has('datetimeformat'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('datetimeformat') }}</strong>
			                            </span>
			                        @endif
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="col-md-3">
				<form class="card mb-3" action="{{ route('my-preferences') }}" method="POST">
					@method('PUT')
					@csrf

					@php
    				$google2fa = app('pragmarx.google2fa');
    				$qri = $google2fa->getQRCodeInline(
		                config('app.name'),
		                config('app.url'),
		                $model->google2fa_secret
		            );
    				@endphp

					<div class="card-header bg-dark text-white">
						<h4>
							<span class="pull-right">
								<input type="hidden" name="section" value="security" />
								<input type="submit" class="btn btn-light" value="{{ __('Save') }}" />
							</span>
							{{ __('My Security Settings') }}
						</h4>
					</div>
					<div class="card-body">
    					<div class="form-group">
							<label>{{ __('New Password') }}</label>
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
    					<div class="text-center">
    						<img src="{{ $qri }}">
    					</div>
    					<input type="text" class="text-center form-control form-control-sm{{ $errors->has('google2fa_secret') ? ' is-invalid' : '' }}" name=
						"google2fa_secret" value="{{ old('google2fa_secret', $model->google2fa_secret) }}" placeholder="{{ __('Secret Code') }}" />
						@if ($errors->has('google2fa_secret'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('google2fa_secret') }}</strong>
                            </span>
                        @endif
    				</div>
				</form>
			</div>
		</div>
	</div>
@endsection

@section('modals')
	@include('app.shared.modals.image-upload', ['imageId' => 'avatar', 'fieldId' => 'avatar-field']);
@endsection