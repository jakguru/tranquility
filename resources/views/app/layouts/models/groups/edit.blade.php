@extends('app.blueprints.framed')

@section('title')
	{{ (is_null($model->id)) ? __('Add Group') : sprintf(__('Edit %s'), $model->name) }}
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
			'name' => __('Groups'),
			'url' => route('settings-groups'),
		],
		[
			'name' => (is_null($model->id)) ? __('Add Group') : sprintf(__('Edit %s'), $model->name),
			'url' => '#',
		],
	]])
	<div class="container-fluid">
		<div class="row">
			<div class="{{ (is_null($model->id)) ? 'col-12' : 'col-lg-9' }}">
				<form class="card mb-3" action="{{ (is_null($model->id)) ? route('create-group') : route('edit-group', ['id' => $model->id]) }}" method="POST">
					@if(!is_null($model->id))
					@method('PUT')
					@endif
    				@csrf
    				<div class="card-header bg-dark text-white">
    					<h4 class="mb-0">
                            @if(!is_null($model->id))
                            <span class="pull-right">
                                <a href="{{ route('audit-group', ['id' => $model->id]) }}" class="btn btn-sm btn-light">{{ __('Audit') }}</a>
                            </span>
                            @endif
                            {{ __('Settings') }}
                        </h4>
    				</div>
    				<div class="card-body">
    					<div class="row">
	    					<div class="col-md-4">
	    						<div class="form-group row">
									<label class="col-md-4 col-lg-3">{{ __('Name') }}</label>
									<div class="col-md-8 col-lg-9">
										<input type="text" name="name" class="form-control form-control-sm{{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ old('name', $model->name) }}" required />
										@if ($errors->has('name'))
				                            <span class="invalid-feedback" role="alert">
				                                <strong>{{ $errors->first('name') }}</strong>
				                            </span>
				                        @endif
									</div>
								</div>
	    					</div>
	    					<div class="col-md-4">
	    						<div class="form-check">
		                            <input class="form-check-input" type="checkbox" name="sudo" id="sudo" value="1" {{ old('sudo', $model->sudo) ? 'checked' : '' }}>

		                            <label class="form-check-label" for="sudo">
		                                {{ __('Super User Permissions') }}
		                            </label>

		                            @if ($errors->has('sudo'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('sudo') }}</strong>
			                            </span>
			                        @endif
		                        </div>
	    					</div>
	    					<div class="col-md-4">
	    						<div class="form-check">
		                            <input class="form-check-input" type="checkbox" name="infosec" id="infosec" value="1" {{ old('infosec', $model->infosec) ? 'checked' : '' }}>

		                            <label class="form-check-label" for="infosec">
		                                {{ __('InfoSec Permissions') }}
		                            </label>

		                            @if ($errors->has('infosec'))
			                            <span class="invalid-feedback" role="alert">
			                                <strong>{{ $errors->first('infosec') }}</strong>
			                            </span>
			                        @endif
		                        </div>
	    					</div>
	    				</div>
	    				<div class="form-group">
	    					<label>{{ __('Allowed Login IPs') }}<br /><small>{{ __('1 IP Address or CIDR per Line') }}</small><br /><small><code>any</code> & <code>all</code> {{ __('allows login from all locations') }}<br />Use <code>none</code> if you do not have an IP Address</small></label>
	    					<textarea name="ip_whitelist" class="form-control form-control-sm{{ $errors->has('ip_whitelist') ? ' is-invalid' : '' }}">{{ old('ip_whitelist', $model->ip_whitelist) }}</textarea>
	    					@if ($errors->has('ip_whitelist'))
	                            <span class="invalid-feedback" role="alert">
	                                <strong>{{ $errors->first('ip_whitelist') }}</strong>
	                            </span>
	                        @endif
	    				</div>
    				</div>
    				<div class="card-header bg-dark text-white">
    					<h4 class="mb-0">{{ __('Permissions') }}</h4>
    				</div>
    				<div class="table-responsive">
    					<table class="table table-sm table-striped table-hover table-model-list mb-0">
    						<thead>
    							<tr>
    								<th class="text-center">{{ __('Model') }}</th>
    								@foreach(\App\Helpers\PermissionsHelper::getPermissionFieldsForModel('Model') as $permission)
    								<th class="text-center">{{ ucwords(str_replace(['can_', '_model'], '', $permission)) }}</th>
    								@endforeach
    							</tr>
    						</thead>
    						<tbody>
    							@foreach(\App\Helpers\PermissionsHelper::getPermitableModels() as $class)
    							<tr>
    								<td class="text-center"><code>{{ str_after($class, '\\App\\') }}</code></td>
    								@foreach(\App\Helpers\PermissionsHelper::getPermissionFieldsForModel(str_after($class, '\\App\\')) as $permission)
    								<td>
    									<select name="{{ $permission }}" class="form-control form-control-sm" required>
    										@foreach(\App\Helpers\PermissionsHelper::$permissionOptions as $option)
    										<option value="{{ $option }}"{{ $option == $model->{$permission} ? ' selected' : '' }}>{{ ucwords($option) }}</option>
    										@endforeach
    									</select>
    								</td>
    								@endforeach
    							</tr>
    							@endforeach
    						</tbody>
    					</table>
    				</div>
    				<div class="card-footer">
    					<input type="hidden" name="section" value="settings" />
    					<input type="submit" class="btn btn-dark" value="{{ __('Save Group') }}" />
    				</div>
    			</form>
    		</div>
    		@if(!is_null($model->id))
			<div class="col-lg-3">
				<form class="card mb-3" action="{{ (is_null($model->id)) ? route('create-group') : route('edit-group', ['id' => $model->id]) }}" method="POST">
					@if(!is_null($model->id))
					@method('PUT')
					@endif
    				@csrf
    				<div class="card-header bg-dark text-white">
    					<h4 class="mb-0">{{ __('Associated Users') }}</h4>
    				</div>
                    @if ($errors->has('users.*'))
                        <div class="card-body pt-0 pb-0">
                            @foreach($errors->get('users.*') as $message)
                            <div class="alert alert-danger mb-0">{{ $message[0] }}</div>
                            @endforeach
                        </div>
                    @endif
    				<div class="table-responsive max-height-200">
    					<table class="table table-sm table-striped table-hover table-model-list mb-0">
    						<thead>
    							<tr>
    								<th style="max-width: 20px;" width="20">&nbsp;</th>
    								<th>{{ __('User') }}</th>
    							</tr>
    						</thead>
    						<tbody>
    							@php
    							$ownusers = $model->users->pluck('id')->toArray();
    							@endphp
    							@foreach(\App\User::all() as $user)
    							<tr>
    								<td class="text-center" style="max-width: 20px;" width="20">
    									<input type="checkbox" name="users[{{$user->id}}]" {{{in_array($user->id, $ownusers) ? 'checked' : ''}}} />
    								</td>
    								<td>
    									@if(Auth::user()->can('view', $user))
    									<a href="{{ route('view-user', ['id' => $user->id]) }}">
    									@endif
    									{{$user->name}}
    									@if(Auth::user()->can('view', $user))
    									</a>
    									@endif
    								</td>
    							</tr>
    							@endforeach
    						</tbody>
						</table>
					</div>
    				<div class="card-footer">
    					<input type="hidden" name="section" value="users" />
    					<input type="submit" class="btn btn-dark" value="{{ __('Update Membership') }}" />
    				</div>
    			</form>
			</div>
			@endif
    	</div>
    </div>
@endsection