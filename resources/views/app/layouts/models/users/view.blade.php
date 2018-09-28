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
			<div class="col-md-8 col-lg-9">
				<div class="row">
					<div class="col-12">
						@include('app.shared.business-card', ['model' => $model])
					</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				Right
			</div>
		</div>
	</div>
@endsection