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
		<form class="card mb-3" action="{{ route('my-preferences') }}" method="POST">
			@method('PUT')
			@csrf

			<div class="card-header bg-dark text-white">
				<h4>{{ __('My Personal Information') }}</h4>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-6 offset-3 col-md-2 offset-md-0 mb-5 mb-md-0">
						<label>{{ __('Your Avatar') }}</label>
						<div class="avatar-upload" data-toggle="modal" data-target="#image-upload-modal">
							<input type="text" class="d-none" name="avatar" id="avatar-field" value="{{ old('avatar') }}" />
							<img src="{{ \App\Helpers\BusinessCardHelper::getUrlForAvatarImage($model, $model->id) }}" class="avatar" id="avatar" />
						</div>
					</div>
					<div class="col-12 col-md-10 mb-3 mb-md-1">
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
			</div>
		</form>
	</div>
@endsection

@section('modals')
	@include('app.shared.modals.image-upload', ['imageId' => 'avatar', 'fieldId' => 'avatar-field']);
@endsection