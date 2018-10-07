@extends('app.blueprints.framed')

@section('title')
	{{ sprintf(__('View %s'), $model->name) }}
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
			'name' => sprintf(__('View %s'), $model->name),
			'url' => '#',
		],
	]])

	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-9">
				<div class="row">
					<div class="col-12">
						@include('app.shared.business-card', ['model' => $model])
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<div class="card mb-3">
							<div class="card-header bg-dark text-white">
								<h4 class="mb-0">{{ __('Latest Actions') }}</h4>
							</div>
							<div class="table-responsive">
								<table class="table table-sm table-striped table-hover mb-0 table-inline">
									<thead>
										<tr>
											<th>&nbsp;</th>
											<th>{{ __('Model') }}</th>
											<th>{{ __('Model ID') }}</th>
											<th>{{ __('Action') }}</th>
											<th>{{ __('IP') }}</th>
											<th>{{ __('Changes') }}</th>
											<th>{{ __('Date / Time') }}</th>
										</tr>
									</thead>
									<tbody>
										@foreach($model->ownActivities()->limit(config('app.listsize'))->latest()->get() as $activity)
										<tr>
											<td>&nbsp;</td>
											<td>{{ ucwords(\App\Helpers\ModelListHelper::getSingleLabelForClass($activity->model)) }}</td>
											<td>
												@if(Route::has(sprintf('view-%s', \App\Helpers\ModelListHelper::getSingleLabelForClass($activity->model))))
												<a href="{{ route(sprintf('view-%s', \App\Helpers\ModelListHelper::getSingleLabelForClass($activity->model)), ['id' => $activity->model_id]) }}">
												@endif
												#{{ $activity->model_id }}
												@if(Route::has(sprintf('view-%s', \App\Helpers\ModelListHelper::getSingleLabelForClass($activity->model))))
												</a>
												@endif
											</td>
											<td>{{ ucwords($activity->action) }}</td>
											<td>{{ $activity->ip }}</td>
											<td>
												@if(is_array($activity->changes) && count($activity->changes) > 0)
												<ul class="change-line">
												@foreach($activity->changes as $field => $changes)
												<li>
													<code>{{$field}}</code> 
													{{__('from')}} 
													<code>{{ \App\Helpers\ModelListHelper::getChangeLogDisplay($field, $changes['old']) }}</code> 
													{{__('to')}} 
													<code>{{ \App\Helpers\ModelListHelper::getChangeLogDisplay($field, $changes['new']) }}</code>
												</li>
												@endforeach
												</ul>
												@endif
											</td>
											<td>{{ Auth::user()->formatDateTime($activity->created_at) }}</td>
										</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-3">
				<div class="card mb-3">
					<div class="card-header bg-dark text-white">
						<h4 class="mb-0">{{ __('Groups') }}</h4>
					</div>
					<div class="table-responsive">
						<table class="table table-sm table-striped table-hover mb-0 table-inline">
							<thead>
								<tr>
									<th>&nbsp;</th>
									<th>{{ __('Group')}}</th>
								</tr>
							</thead>
							<tbody>
								@foreach($model->groups as $group)
								<tr>
									<td>&nbsp;</td>
									<td>
										@if(Auth::user()->can('view', $group))
										<a href="{{ route('view-group', ['id' => $group->id])}}">
										@endif
										{{ $group->name }}
										@if(Auth::user()->can('view', $group))
										</a>
										@endif
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection