@extends('app.blueprints.framed')

@section('title')
	{{ $title }}
@endsection

@section('main')
	@include('app.shared.breadcrumbs',['crumbs' => $breadcrumbs])
	<div class="container-fluid">
		<h1>{{ ucwords($plural_label) }}</h1>
		<form class="card" action="{{ url()->current() }}" method="GET">
			<div class="card-header">
				<div class="row">
					<div class="col-md-4">
						<div class="input-group input-group-sm mb-2 mb-md-0">
							<input type="search" name="s" value="{{ request()->input('s') }}" class="form-control" />
							<div class="input-group-append">
							    <button class="btn" type="submit" role="submit">
							    	<span class="fas fa-search"></span>
							    </button>
							    <a href="{{ route($create_route) }}" class="btn btn-success">
							    	<span class="fas fa-plus"></span> {{ sprintf(__('Add %s'), ucwords($single_label)) }}
							    </a>
							</div>
						</div>
					</div>
					<div class="col-md-4">
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
					</div>
					<div class="col-md-4 text-right">
						<span class="modellist-totals-summary">{{ sprintf(__('Showing %d of %d %s'), count($items), $total_items, ucwords($plural_label)) }}</span>
					</div>
				</div>
			</div>
			<div class="table-responsive mb-0">
				<table class="table table-sm table-striped table-hover table-model-list mb-0">
					<thead>
						<tr>
							@foreach($columns as $column => $info)
							<th @if($column == array_keys($columns)[0]) colspan="2" @endif>
								<span class="column-label">{{ __($info['label']) }}</span>
								<span class="column-sorting">
									<a href="{{ \App\Helpers\ModelListHelper::getSortUrl($column, 'asc') }}"><span class="fas fa-caret-up"</a>
									<a href="{{ \App\Helpers\ModelListHelper::getSortUrl($column, 'desc') }}"><span class="fas fa-caret-down"</a>
									<a href="{{ \App\Helpers\ModelListHelper::getSortUrl($column, 'none') }}"><span class="fas fa-eraser"</a>
								</span>
							</th>
							@endforeach
						</tr>
						<tr>
							@foreach($columns as $column => $info)
							<th @if($column == array_keys($columns)[0]) colspan="2" @endif>
								@switch($info['type'])
									@case('test')
										@break
									@default
										<input type="{{ $info['type'] }}" class="form-control form-control-sm" name="filter[{{ $column }}]" value="{{ request()->input(sprintf('filter.%s', $column)) }}" />
										@break
								@endswitch
							</th>
							@endforeach
						</tr>
					</thead>
					<tbody>
						@if( 0 == $total_items )
						<tr>
							<td colspan="{{ count($columns) + 1}}">
								<div class="alert alert-info mb-0 text-center">{{ sprintf(__('No %s Found'), ucwords($plural_label)) }}</div>
							</td>
						</tr>
						@endif
						@foreach($items as $model)
							<tr>
								<td><a href="{{ route($view_route,['id' => $model->id]) }}" class="btn btn-block btn-sm btn-dark"><span class="far fa-eye"></span></a></td>
								@foreach($columns as $column => $info)
									<td>
										@switch($info['type'])
											@case('test')
												@break
											@default
												{{ $model->{$column} }}
												@break
										@endswitch
									</td>
								@endforeach
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</form>
	</div>
@endsection