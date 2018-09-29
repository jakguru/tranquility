@extends('app.blueprints.framed')

@section('title')
	{{ sprintf(__('View Audit of %s'), $model->name) }}
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
			'url' => route('view-user', ['id' => $model->id]),
		],
		[
			'name' => sprintf(__('View Audit of %s'), $model->name),
			'url' => '#',
		],
	]])

	<div class="container-fluid">
		<div class="card mb-3">
			<div class="card-header bg-dark text-white">
				<h4 class="mb-0">
					<form class="d-inline pull-right" method="GET">
						<div class="input-group input-group-sm mb-2 mb-md-0">
							<div class="input-group-prepend">
								@if($page !== 1)
								<a href="{{ \App\Helpers\ModelListHelper::getPageUrl(1) }}" class="btn btn-secondary"><i class="fas fa-fast-backward"></i></a>
								@endif
								@if(0 !== $previous_page)
								<a href="{{ \App\Helpers\ModelListHelper::getPageUrl($previous_page) }}" class="btn btn-secondary"><i class="fas fa-backward"></i></a>
								@endif
							    <span class="input-group-text">{{ __('Page') }}</span>
							</div>
							<input type="number" min="1" max="{{ $total_pages }}" name="page" value="{{ $page }}" class="form-control" />
							<div class="input-group-append">
							    <span class="input-group-text">{{ sprintf( __('of %d'), $total_pages) }}</span>
							    <button class="btn btn-secondary" type="submit" role="submit" title="{{ __('Jump Pages') }}">
							    	<span class="fas fa-check-circle"></span>
							    </button>
							    @if(0 !== $next_page)
								<a href="{{ \App\Helpers\ModelListHelper::getPageUrl($next_page) }}" class="btn btn-secondary"><i class="fas fa-forward"></i></a>
								@endif
								@if($page < $total_pages)
								<a href="{{ \App\Helpers\ModelListHelper::getPageUrl($total_pages) }}" class="btn btn-secondary"><i class="fas fa-fast-forward"></i></a>
								@endif
							</div>
						</div>
					</form>
					{{ __('Audit Log') }}
				</h4>
			</div>
			<div class="table-responsive">
				<table class="table table-sm table-striped table-hover mb-0 table-inline">
					<thead>
						<tr>
							<th>&nbsp;</th>
							<th>{{ __('User') }}</th>
							<th>{{ __('Action') }}</th>
							<th>{{ __('IP') }}</th>
							<th>{{ __('Changes') }}</th>
							<th>{{ __('Date / Time') }}</th>
						</tr>
					</thead>
					<tbody>
						@foreach($activities as $activity)
						<tr>
							<td>&nbsp;</td>
							<td>
								@if(is_object($activity->user) && Auth::user()->can('view', $activity->user))
								<a href="{{ route('view-user', ['id' => $activity->user->id ])}}">
								@endif
								{{ (is_object($activity->user)) ? $activity->user->name : '' }}
								@if(is_object($activity->user) && Auth::user()->can('view', $activity->user))
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
@endsection