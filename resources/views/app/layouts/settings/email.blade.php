@extends('app.blueprints.framed')

@section('title')
	Settings
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
			'name' => __('Email Settings'),
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

					<h1>{{ __('Email Settings') }}</h1>
					<input type="hidden" name="section" value="email" />
					<div class="card bg-dark text-white">
						<div class="card-body">
							<div class="form-group">
								<label>SMTP Server</label>
								<div class="input-group">
									<input type="text" name="hostname" class="form-control{{ $errors->has('hostname') ? ' is-invalid' : '' }}" placeholder="{{ __('SMTP Host') }}" value="{{ old('hostname', config('mail.host')) }}" required autocomplete="off" autofocus />
									<div class="input-group-append">
										<span class="input-group-text">:</span>
									</div>
									<input type="number" min="1" max="65535" name="port" class="form-control{{ $errors->has('port') ? ' is-invalid' : '' }}" placeholder="{{ __('SMTP Port') }}" value="{{ old('port', config('mail.port')) }}" required autocomplete="off" />
								</div>
								@if ($errors->has('hostname'))
		                            <span class="invalid-feedback" role="alert">
		                                <strong>{{ $errors->first('hostname') }}</strong>
		                            </span>
		                        @endif
		                        @if ($errors->has('port'))
		                            <span class="invalid-feedback" role="alert">
		                                <strong>{{ $errors->first('port') }}</strong>
		                            </span>
		                        @endif
							</div>
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label>SMTP Encryption</label>
										<select name="encryption" class="form-control{{ $errors->has('encryption') ? ' is-invalid' : '' }}">
											<option value="" @if(old('encryption', config('mail.encryption')) == '') selected @endif>{{ __('None') }}</option>
											<option value="ssl" @if(old('encryption', config('mail.encryption')) == 'ssl') selected @endif>{{ __('SSL') }}</option>
											<option value="tls" @if(old('encryption', config('mail.encryption')) == 'tls') selected @endif>{{ __('TLS') }}</option>
										</select>
										@if ($errors->has('encryption'))
				                            <span class="invalid-feedback" role="alert">
				                                <strong>{{ $errors->first('encryption') }}</strong>
				                            </span>
				                        @endif
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>SMTP Username</label>
										<input type="text" name="username" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}"  value="{{ old('username', config('mail.username')) }}" required autocomplete="off" />
										@if ($errors->has('username'))
				                            <span class="invalid-feedback" role="alert">
				                                <strong>{{ $errors->first('username') }}</strong>
				                            </span>
				                        @endif
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>SMTP Password</label>
										<input type="password" name="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"  value="{{ old('password', config('mail.password')) }}" required autocomplete="off" />
										@if ($errors->has('password'))
				                            <span class="invalid-feedback" role="alert">
				                                <strong>{{ $errors->first('password') }}</strong>
				                            </span>
				                        @endif
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label>Sender Email</label>
										<input type="email" name="sendermail" class="form-control{{ $errors->has('sendermail') ? ' is-invalid' : '' }}"  value="{{ old('sendermail', config('mail.from.address')) }}" required autocomplete="off" />
										@if ($errors->has('sendermail'))
				                            <span class="invalid-feedback" role="alert">
				                                <strong>{{ $errors->first('sendermail') }}</strong>
				                            </span>
				                        @endif
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Sender Name</label>
										<input type="text" name="sendername" class="form-control{{ $errors->has('sendername') ? ' is-invalid' : '' }}"  value="{{ old('sendername', config('mail.from.name')) }}" required autocomplete="off" />
										@if ($errors->has('sendername'))
				                            <span class="invalid-feedback" role="alert">
				                                <strong>{{ $errors->first('sendername') }}</strong>
				                            </span>
				                        @endif
									</div>
								</div>
							</div>
						</div>
						<div class="card-footer">
							<input type="submit" class="btn btn-light" value="{{ __('Save Email Settings') }}" />
						</div>
					</div>
				</form>
				<form action="{{ route('save-settings') }}" method="POST" autocomplete="off" class="mb-3">
					@csrf

					<h1>{{ __('Test Email Settings') }}</h1>
					<input type="hidden" name="section" value="test-email" />
					<div class="card bg-dark text-white">
						<div class="card-body">
							<div class="form-group">
								<label>Recipient</label>
								<input type="email" name="recipient" class="form-control{{ $errors->has('recipient') ? ' is-invalid' : '' }}" required autocomplete="off" />
								@if ($errors->has('recipient'))
		                            <span class="invalid-feedback" role="alert">
		                                <strong>{{ $errors->first('recipient') }}</strong>
		                            </span>
		                        @endif
							</div>
							<div class="form-group">
								<label>Subject</label>
								<input type="text" name="subject" class="form-control{{ $errors->has('subject') ? ' is-invalid' : '' }}" required autocomplete="off" />
								@if ($errors->has('subject'))
		                            <span class="invalid-feedback" role="alert">
		                                <strong>{{ $errors->first('subject') }}</strong>
		                            </span>
		                        @endif
							</div>
							<div class="form-group">
								<label>Message</label>
								<textarea name="message" class="form-control{{ $errors->has('message') ? ' is-invalid' : '' }}" required autocomplete="off"></textarea>
								@if ($errors->has('message'))
		                            <span class="invalid-feedback" role="alert">
		                                <strong>{{ $errors->first('message') }}</strong>
		                            </span>
		                        @endif
							</div>
						</div>
						<div class="card-footer">
							<input type="submit" class="btn btn-light" value="{{ __('Send Test Email') }}" />
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection