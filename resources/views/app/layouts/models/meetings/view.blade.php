@extends('app.blueprints.framed')

@section('title')
	{{ sprintf(__('View %s'), $model->subject) }}
@endsection

@section('main')
	@include('app.shared.breadcrumbs',['crumbs' => [
		[
			'name' => config('app.name'),
			'url' => route('dashboard'),
		],
		[
			'name' => __('My Calendar'),
			'url' => route('my-calendar'),
		],
		[
			'name' => sprintf(__('View %s'), $model->subject),
			'url' => '#',
		],
	]])
	<div class="container-fluid">
		@if($ongoing)
		<div class="alert alert-success">
			{{ __('This appointment is currently ongoing.') }}
		</div>
		@elseif($past)
		<div class="alert alert-info">
			{{ __('This appointment has already finished.') }}
		</div>
		@endif
		<div class="row">
			<div class="col-lg-9">
				@if($mymeeting && !$past)
				<form class="card mb-3" action="{{ route('update-meeting', ['id' => $model->id]) }}" method="POST">
					@method('PUT')
    				@csrf

    				<div class="card-header bg-dark text-white">
    					<h4 class="mb-0">{{ __('Appointment Information') }}</h4>
    				</div>
    				<div class="card-body">
    					<div class="form-group">
    						<label>{{ __('Subject') }}</label>
    						<input type="text" name="subject" class="form-control form-control-lg{{ $errors->has('subject') ? ' is-invalid' : '' }}" value="{{ old('subject', $model->subject) }}" required />
    						@if ($errors->has('subject'))
	                            <span class="invalid-feedback" role="alert">
	                                <strong>{{ $errors->first('subject') }}</strong>
	                            </span>
	                        @endif
    					</div>
    					<div class="row">
    						<div class="col-md-6">
    							<div class="form-group">
    								<label>Start</label>
    								<input name="starts_at" type="text" class="form-control form-control{{ $errors->has('starts_at') ? ' is-invalid' : '' }}" value="{{ old('starts_at', $model->starts_at) }}" required autocomplete="off"/>
    								@if ($errors->has('starts_at'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('starts_at') }}</strong>
			                            </span>
			                        @endif
    							</div>
    						</div>
    						<div class="col-md-6">
    							<div class="form-group">
    								<label>Ends</label>
    								<input name="ends_at" type="text" class="form-control form-control{{ $errors->has('ends_at') ? ' is-invalid' : '' }}" value="{{ old('ends_at', $model->ends_at) }}" required autocomplete="off"/>
    								@if ($errors->has('ends_at'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('ends_at') }}</strong>
			                            </span>
			                        @endif
    							</div>
    						</div>
    					</div>
    					<div class="form-group">
    						<label>{{ __('Description') }}</label>
    						<textarea name="description" class="form-control twohundredtall{{ $errors->has('description') ? ' is-invalid' : '' }}" required>{{ old('description', $model->description) }}</textarea>
    						@if ($errors->has('description'))
	                            <span class="invalid-feedback" role="alert">
	                                <strong>{{ $errors->first('description') }}</strong>
	                            </span>
	                        @endif
    					</div>
    				</div>
    			</form>
				@else
				@endif
			</div>
			<div class="col-lg-3">
				@if($mymeeting && !$past)
				<form class="card mb-3" action="{{ route('update-meeting', ['id' => $model->id]) }}" method="POST">
					@method('PUT')
    				@csrf

    				<div class="card-header bg-dark text-white">
    					<h4 class="mb-0">{{ __('Participants') }}</h4>
    				</div>
    				<div class="card-body">
    					<div class="form-group">
							<div class="multi-model-search multi-model-search-sm">
								<div class="selected-results"></div>
								<input type="search" name="participants" class="form-control" />
							</div>
						</div>
    				</div>
    			</form>
				@else
				@endif
			</div>
		</div>
	</div>
@endsection