@extends('app.foundation')

@section('blueprint')
<div id="app" class="framed">
	<header>
		<div id="app-icon"><img src="{{ asset( 'img/favicon.png' ) }}" /></div>
		<div id="app-name" class="d-none d-sm-inline-block">{{ config('app.name', 'Tranquility CRM') }}</div>
		<div id="menu-toggle">
			<a href="#"><span class="fas fa-bars"></span></a>
		</div>
		<form action="{{ route('search') }}" method="GET" id="menu-search" class="d-none d-sm-inline-block">
			@csrf

			<div class="input-group input-group-sm">
				<input type="search" name="s" class="form-control" placeholder="{{ __('Search') }}" aria-label="{{ __('Search') }}" value="{{ old('s', app('request')->query('s')) }}" required />
				<div class="input-group-append">
					<button type="button" class="btn btn-outline-dark dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<span class="sr-only">Toggle Dropdown</span>
					</button>
					<div class="dropdown-menu">
						@foreach( \App\Helpers\ElasticSearchableModelHelper::getElasticSearchableModels() as $value => $name )
						<div class="form-check">
							<input class="form-check-input" type="checkbox" name="model[]" value="{{ $value }}" {{ (is_array(old('model', Request::get('model'))) && in_array($value, old('model', Request::get('model')))) ? 'checked' : '' }}>
							<label class="form-check-label">{{ $name }}</label>
						</div>
						@endforeach
					</div>
					<button class="btn btn-outline-dark" type="submit" role="submit"><span class="fas fa-search"></span></button>
				</div>
			</div>
		</form>
		@auth
		<div id="user-bar" class="text-right">
			<a href="#" id="chat-indicator" class="text-danger"><span class="fas fa-comment"></span></a>
			<a href="#" id="messages-indicator" class="indicator-with-label">
				<span class="fas fa-envelope"></span>
				<span class="indicator-label">0</span>
			</a>
			<a href="#" id="notifications-indicator" class="indicator-with-label">
				<span class="fas fa-bell"></span>
				<span class="indicator-label">0</span>
			</a>
			<!-- <span class="user-bar-seperator"></span> -->
			<div id="user-menu" class="dropdown">
				<button class="btn btn-link btn-user-menu-link dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<img src="{{ Auth::user()->getAvatarUrl(50) }}" class="user-avatar" />
					{{ Auth::user()->name }}
				</button>
				<div class="dropdown-menu">
					<a class="dropdown-item" href="#">{{ __('My Settings') }}</a>
					<a class="dropdown-item" href="{{ route('logout') }}">{{ __('Log Out') }}</a>
				</div>
			</div>
			<!-- <span class="user-bar-seperator"></span> -->
			<a href="{{ route('logout') }}"><span class="fas fa-user-slash"></span></a>
		</div>
		@endauth
	</header>
	<aside>
		<form action="{{ route('search') }}" method="GET" id="left-menu-search" class="d-sm-none">
			@csrf

			<div class="input-group input-group-sm">
				<input type="search" name="s" class="form-control" placeholder="{{ __('Search') }}" aria-label="{{ __('Search') }}" value="{{ old('s', app('request')->query('s')) }}" required />
				<div class="input-group-append">
					<button type="button" class="btn btn-outline-dark dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<span class="sr-only">Toggle Dropdown</span>
					</button>
					<div class="dropdown-menu">
						@foreach( \App\Helpers\ElasticSearchableModelHelper::getElasticSearchableModels() as $value => $name )
						<div class="form-check">
							<input class="form-check-input" type="checkbox" name="model[]" value="{{ $value }}" {{ (is_array(old('model', Request::get('model'))) && in_array($value, old('model', Request::get('model')))) ? 'checked' : '' }}>
							<label class="form-check-label">{{ $name }}</label>
						</div>
						@endforeach
					</div>
					<button class="btn btn-outline-dark" type="submit" role="submit"><span class="fas fa-search"></span></button>
				</div>
			</div>
		</form>
	</aside>
	<main>
		@yield('main')
	</main>
</div>
@endsection