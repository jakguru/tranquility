@extends('app.blueprints.framed')

@section('title')
	{{ (is_null($model->id)) ? __('Add Role') : sprintf(__('Edit %s'), $model->name) }}
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
			'name' => __('Roles'),
			'url' => route('settings-roles'),
		],
		[
			'name' => (is_null($model->id)) ? __('Add Role') : sprintf(__('Edit %s'), $model->name),
			'url' => '#',
		],
	]])
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<form class="card mb-3" action="{{ (is_null($model->id)) ? route('create-role') : route('edit-role', ['id' => $model->id]) }}" method="POST">
					@if(!is_null($model->id))
					@method('PUT')
					@endif
    				@csrf
    				<div class="card-header bg-dark text-white">
    					<h4 class="mb-0">
                            @if(!is_null($model->id))
                            <span class="pull-right">
                                <a href="{{ route('audit-role', ['id' => $model->id]) }}" class="btn btn-sm btn-light">{{ __('Audit') }}</a>
                            </span>
                            @endif
                            {{ __('Settings') }}
                        </h4>
    				</div>
    				<div class="card-body">
    					<div class="row">
	    					<div class="col-md-6">
	    						<div class="form-group">
									<label>{{ __('Name') }}</label>
									<input type="text" name="name" class="form-control form-control-sm{{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ old('name', $model->name) }}" required />
                                    @if ($errors->has('name'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
								</div>
	    					</div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Parent Role') }}</label>
                                    <select name="role_id" class="form-control form-control-sm{{ $errors->has('role_id') ? ' is-invalid' : '' }}">
                                        <option value=""{{is_null($model->role_id) || empty($model->role_id) ? ' selected' : ''}}>{{ __('No Parent') }}</option>
                                        @foreach(\App\Role::getSelectChoices() as $value => $label)
                                        <option value="{{$value}}"{{$value == $model->role_id ? ' selected' : ''}}{{$value == $model->id ? ' disabled' : ''}}>{{$label}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('role_id'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('role_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
	    				</div>
    				</div>
    				<div class="card-footer">
    					<input type="submit" class="btn btn-dark" value="{{ __('Save Role') }}" />
    				</div>
    			</form>
    		</div>
    	</div>
    </div>
@endsection