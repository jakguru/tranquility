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
							<input type="search" name="s" value="{{ request()->query('s') }}" class="form-control" />
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
							    <span class="input-group-text">{{ __('Jump to Page') }}</span>
							</div>
							<input type="number" min="1" max="{{ $total_pages }}" name="page" value="{{ $page }}" class="form-control" />
							<div class="input-group-append">
							    <span class="input-group-text">{{ sprintf( __('of %d'), $total_pages) }}</span>
							    <button class="btn" type="submit" role="submit">
							    	<span class="fas fa-check-circle"></span>
							    </button>
							</div>
						</div>
					</div>
					<div class="col-md-4 text-right">
						<span class="modellist-totals-summary">{{ sprintf(__('Showing %d of %d %s'), count($items), $total_items, ucwords($plural_label)) }}</span>
					</div>
				</div>
			</div>
			<div class="table-responsive">
				<table class="table table-compressed table-striped table-hover table-model-list">
					<thead>
						<tr>
							@foreach($columns as $column => $info)
							<th>
								<span class="column-label">{{ $info['label'] }}</span>
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
							<th>
								@switch($info['type'])
									@case('test')
										@break
									@default
										<input type="{{ $info['type'] }}" name="filter[{{ $column }}]" value="{{ request()->query(sprintf('filter.%s', $column)) }}" />
										@break
								@endswitch
							</th>
							@endforeach
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</form>
	</div>
@endsection