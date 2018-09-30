<div class="card business-card mb-3">
	<div class="card-header bg-dark text-white">
		<h4 class="mb-0">
			@if(Auth::user()->can('edit', $model))
			<span class="pull-right">
				@if(\App\Helpers\BusinessCardHelper::hasLog($model))
				<a href="{{ route(\App\Helpers\BusinessCardHelper::getAuditRoute($model), ['id' => $model->id]) }}" class="btn btn-sm btn-light">{{ __('Audit') }}</a>
				@endif
				<a href="{{ route(\App\Helpers\BusinessCardHelper::getEditRoute($model), ['id' => $model->id]) }}" class="btn btn-sm btn-light">{{ __('Edit') }}</a>
			</span>
			@endif
			{{ sprintf(__('%s #%d'), ucwords(\App\Helpers\BusinessCardHelper::getSingleLabelForClass($model)), $model->id) }}
		</h4>
	</div>
	<div class="card-body pt-0 pb-0" style="background-image: url({{ \App\Helpers\BusinessCardHelper::getUrlForBackgroundImage($model, $model->id) }})">
		<div class="row">
			<div class="col-6 offset-3 col-md-2 offset-md-0 mb-5 mb-md-0">
				<img src="{{ \App\Helpers\BusinessCardHelper::getUrlForAvatarImage($model, $model->id) }}" class="avatar" />
			</div>
			<div class="col-12 col-md-5 mb-3 mb-md-0 d-md-flex flex-row justify-content-start align-items-end flex-fill">
				<h1 class="model-info-with-bg text-center text-md-left d-block mt-2"">
					@if(!is_null($model->salutation) && strlen($model->salutation) > 0)<small>{{ $model->salutation }}</small> @endif
					@if(!is_null($model->name) && strlen($model->name) > 0)
						{{ $model->name }}
					@elseif(!is_null($model->email) && strlen($model->email) > 0)
						{{ $model->email }}
					@else
						{{ sprintf(__('%s #%d'), ucwords(\App\Helpers\BusinessCardHelper::getSingleLabelForClass($model)), $model->id) }}
					@endif
				</h1>
			</div>
			<div class="col-12 col-md-5 mb-3 mb-md-0 d-md-flex flex-row justify-content-end align-items-end flex-fill">
				<h2 class="model-info-with-bg text-center text-md-left mt-2">
					@if(is_object($model->role))
						<span title="{{ __('Role') }}" class="mr-1 d-block d-lg-inline">
						@if(Auth::user()->can('view', $model->role))
						<a class="text-white" href="{{ route('view-role', ['id' => $model->role->id]) }}">
						@endif
							<i class="fab fa-black-tie mr-1"></i>
							{{ $model->role->name }}
						@if(Auth::user()->can('view', $model->role))
						</a>
						@endif
						</span>
					@endif
					@if(!is_null($model->active) && true == $model->active)
					<span class="text-success d-block d-lg-inline"><i class="far fa-check-circle mr-1"></i>{{__('Active')}}</span>
					@elseif(!is_null($model->active) && false == $model->active)
					<span class="text-info d-block d-lg-inline"><i class="far fa-times-circle mr-1"></i>{{__('Inactive')}}</span>
					@endif
				</h2>
			</div>
		</div>
	</div>
	<div class="card-footer bg-secondary text-white pt-0 pb-0">
		<div class="row">
			<div class="d-none d-md-block col-md-2 offset-md-0"></div>
			<div class="col-12 col-md-10">
				<ul class="model-info-menu d-block d-md-flex justify-content-between">
					<li class="d-block mb-3 d-md-inline-block mb-md-0">
						<div class="dropdown">
							<button class="btn btn-link dropdown-toggle" type="button" id="email-dropdown-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    							<i class="far fa-envelope mr-1"></i>
    							@if(!is_null($model->email) && strlen($model->email) > 0)
								{{$model->email}}
								@endif
  							</button>
  							<div class="dropdown-menu nowrap pt-0 pb-0" aria-labelledby="email-dropdown-menu">
  								@if(!is_null($model->email) && strlen($model->email) > 0)
  								<a class="dropdown-item" href="mailto:{{$model->email}}">{{__('Send Email')}}</a>
  								@endif
  							</div>
						</div>
					</li>
					<li class="d-block mb-3 d-md-inline-block mb-md-0">
						<div class="dropdown">
							<button class="btn btn-link dropdown-toggle" type="button" id="phone-dropdown-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    							<i class="fas fa-phone mr-1"></i>
    							<span class="flag-icon flag-icon-{{strlen($model->main_phone_country) > 0 ? strtolower($model->main_phone_country) : 'xx'}} flag-icon-squared mr-1"></span>
  							</button>
  							<div class="dropdown-menu nowrap pt-0 pb-0" aria-labelledby="phone-dropdown-menu">
  								@php
  								$phone_has_menu_item = false;
  								@endphp
  								@if(!is_null($model->mobile_phone) && strlen($model->mobile_phone) > 0)
  								<a href="#">
  									<i class="fas fa-mobile-alt mr-1"></i>
  									<span class="flag-icon flag-icon-{{strlen($model->mobile_phone_country) > 0 ? strtolower($model->mobile_phone_country) : 'xx'}} flag-icon-squared mr-1"></span>
  								</a>
  								@php
  								$phone_has_menu_item = true;
  								@endphp
  								@endif
								@if(!is_null($model->home_phone) && strlen($model->home_phone) > 0)
								<a href="#">
									<i class="fas fa-home mr-1"></i>
									<span class="flag-icon flag-icon-{{strlen($model->home_phone_country) > 0 ? strtolower($model->home_phone_country) : 'xx'}} flag-icon-squared mr-1"></span>
								</a>
								@php
  								$phone_has_menu_item = true;
  								@endphp
								@endif
								@if(!is_null($model->work_phone) && strlen($model->work_phone) > 0)
								<a href="#">
									<i class="far fa-building mr-1"></i>
									<span class="flag-icon flag-icon-{{strlen($model->work_phone_country) > 0 ? strtolower($model->work_phone_country) : 'xx'}} flag-icon-squared mr-1"></span>
								</a>
								@php
  								$phone_has_menu_item = true;
  								@endphp
								@endif
								@if(!is_null($model->fax_phone) && strlen($model->fax_phone) > 0)
								<a href="#">
									<i class="fas fa-fax mr-1"></i>
									<span class="flag-icon flag-icon-{{strlen($model->fax_phone_country) > 0 ? strtolower($model->fax_phone_country) : 'xx'}} flag-icon-squared mr-1"></span>
								</a>
								@php
  								$phone_has_menu_item = true;
  								@endphp
								@endif
								@if(false == $phone_has_menu_item)
								<div class="alert alert-danger mb-0">{{__('No Phone Numbers')}}</div>
								@endif
  							</div>
						</div>
					</li>
					<li class="d-block mb-3 d-md-inline-block mb-md-0">
						<div class="dropdown">
							<button class="btn btn-link dropdown-toggle" type="button" id="social-dropdown-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    							<i class="fas fa-comments mr-1"></i>
    							{{ __('Social Media') }}
  							</button>
  							<div class="dropdown-menu nowrap pt-0 pb-0" aria-labelledby="social-dropdown-menu">
  								@php
  								$social_has_menu_item = false;
  								@endphp
  								@if(!is_null($model->whatsapp_phone) && strlen($model->whatsapp_phone) > 0)
  								<a href="#">
  									<i class="fab fa-whatsapp mr-1"></i>
  									<span class="flag-icon flag-icon-{{strlen($model->whatsapp_phone_country) > 0 ? strtolower($model->whatsapp_phone_country) : 'xx'}} flag-icon-squared mr-1"></span>
  								</a>
								@php
  								$social_has_menu_item = true;
  								@endphp
  								@endif
								@if(!is_null($model->telegram_phone) && strlen($model->telegram_phone) > 0)
								<a href="#">
									<i class="fab fa-viber mr-1"></i>
									<span class="flag-icon flag-icon-{{strlen($model->telegram_phone_country) > 0 ? strtolower($model->telegram_phone_country) : 'xx'}} flag-icon-squared mr-1"></span>
								</a>
								@endif
								@if(!is_null($model->viber_phone) && strlen($model->viber_phone) > 0)
								<a href="#">
									<i class="fab fa-telegram mr-1"></i>
									<span class="flag-icon flag-icon-{{strlen($model->viber_phone_country) > 0 ? strtolower($model->viber_phone_country) : 'xx'}} flag-icon-squared mr-1"></span>
								</a>
								@php
  								$social_has_menu_item = true;
  								@endphp
								@endif
								@if(!is_null($model->skype) && strlen($model->skype) > 0)
								<a href="#">
									<i class="fab fa-skype mr-1"></i>
									{{ $model->skype }}
								</a>
								@php
  								$social_has_menu_item = true;
  								@endphp
								@endif
								@if(!is_null($model->facebook) && strlen($model->facebook) > 0)
								<a href="#">
									<i class="fab fa-facebook mr-1"></i>
									{{ $model->facebook }}
								</a>
								@php
  								$social_has_menu_item = true;
  								@endphp
								@endif
								@if(!is_null($model->googleplus) && strlen($model->googleplus) > 0)
								<a href="#">
									<i class="fab fa-google-plus-g mr-1"></i>
									{{ $model->googleplus }}
								</a>
								@php
  								$social_has_menu_item = true;
  								@endphp
								@endif
								@if(!is_null($model->linkedin) && strlen($model->linkedin) > 0)
								<a href="#">
									<i class="fab fa-linkedin mr-1"></i>
									{{ $model->linkedin }}
								</a>
								@php
  								$social_has_menu_item = true;
  								@endphp
								@endif
								@if(false == $social_has_menu_item)
								<div class="alert alert-danger mb-0">{{__('No Social Media Profiles')}}</div>
								@endif
  							</div>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</div>
	<div class="card-footer bg-secondary text-white pt-0 pb-0">
		<div class="row">
			<div class="col-md-12 col-lg-4 mb-3 mb-lg-0 pt-lg-1 pb-lg-1">
				@php
				$weather = \App\Helpers\BusinessCardHelper::getWeatherForModel($model);
				@endphp
				<span class="wi {{ $weather->condition }} mr-1"></span>{{$weather->temp}}°{{('fahrenheit' == Auth::user()->temperature_unit) ? 'F' : 'C'}}
			</div>
			@if(!is_null($model->status))
			<div class="col-md-12 col-lg-4 mb-3 mb-lg-0 pt-lg-1 pb-lg-1">
				{{ ucwords($model->status) }}
			</div>
			@endif
			@if(\App\Helpers\BusinessCardHelper::isOwned($model))
			<div class="col-md-12 col-lg-4 mb-3 mb-lg-0 pt-lg-1 pb-lg-1">
				@if(Auth::user()->can('view', $model->owner))
					<a href="{{ route('view-user', ['id' => $model->owner->id]) }}">
				@endif
				{{ $model->owner->name }}
				@if(Auth::user()->can('view', $model->owner))
					</a>
				@endif
			</div>
			@endif
		</div>
	</div>
	<div class="card-footer bg-secondary text-white pt-0 pb-0">
		<div class="row">
			<div class="col-md-12 col-lg-4 mb-3 mb-lg-0 pt-lg-1 pb-lg-1">
				<i class="fas fa-map-pin mr-1"></i>
				{{ \App\Helpers\BusinessCardHelper::formatModelAddress($model) }}
			</div>
			<div class="col-md-6 col-lg-4 mb-3 mb-md-0 pt-lg-1 pb-lg-1">
				@if(!is_null($model->google2fa_secret))
				<div class="d-block">
					<i class="fas fa-lock mr-1"></i> {{__('MFA Active')}}
				</div>
				@endif
				@if(!is_null($model->last_login_ip))
				<div class="d-block">
					<i class="fas fa-user-secret mr-1"></i>
					<strong>{{__('Last Login IP')}}:</strong>
					{{ $model->last_login_ip }}
				</div>
				@endif
				@if(!is_null($model->last_login_at))
				<div class="d-block">
					<i class="fas fa-user-secret mr-1"></i>
					<strong>{{__('Last Login Time')}}:</strong>
					{{ Auth::user()->formatDateTime($model->last_login_at) }}
				</div>
				@endif
			</div>
			<div class="col-md-6 col-lg-4 pt-lg-1 pb-lg-1">
				@if(!is_null($model->created_at))
				<div class="d-block">
					<i class="far fa-calendar-plus mr-1"></i>
					<strong>{{__('Created On')}}:</strong>
					{{ Auth::user()->formatDateTime($model->created_at) }}
				</div>
				@endif
				@if(!is_null($model->updated_at))
				<div class="d-block">
					<i class="far fa-calendar-check mr-1"></i>
					<strong>{{__('Last Updated On')}}:</strong>
					{{ Auth::user()->formatDateTime($model->updated_at) }}
				</div>
				@endif
				<div class="d-block">
					<i class="far fa-calendar mr-1"></i>
					<strong>{{__('Local Time')}}:</strong>
					<span class="system-clock" data-moment-format="{{ Auth::user()->getMomentDateTimeFormat('time') }}" data-moment-tz="{{ (is_null($model->timezone)) ? 'UTC' : $model->timezone }}" data-moment="now"></span>				
				</div>
			</div>
		</div>
	</div>
</div>