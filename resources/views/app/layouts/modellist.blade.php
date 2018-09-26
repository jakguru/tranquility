@extends('app.blueprints.framed')

@section('title')
	{{ $title }}
@endsection

@section('main')
	@include('app.shared.breadcrumbs',['crumbs' => $breadcrumbs])
	<div class="container-fluid">
		<form class="card" action="{{ url()->current() }}" method="GET">
			<div class="card-header">
				<div class="row">
					<div class="col-md-4 col-lg-3">
						<div class="input-group input-group-sm">
							<input type="search" name="s" value="{{ request()->query('s') }}" class="form-control" />
							<div class="input-group-append">
							    <button class="btn" type="submit" role="submit">
							    	<span class="fas fa-search"></span>
							    </button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
@endsection