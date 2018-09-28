<div class="card business-card">
	<div class="card-header bg-dark text-white">
		<h4 class="mb-0">{{ sprintf(__('%s #%d'), ucwords(\App\Helpers\ModelListHelper::getSingleLabelForClass($model)), $model->id) }}</h4>
	</div>
	<div class="card-body" style="background-image: url({{ \App\Helpers\ModelImageHelper::getUrlForBackgroundImage($model, $model->id) }})">
		<div class="row">
			<div class="d-none d-sm-block col-sm-3 col-md-2">
				<img src="{{ \App\Helpers\ModelImageHelper::getUrlForAvatarImage($model, $model->id) }}" class="avatar" />
			</div>
			<div class="col-12 col-sm-9 col-md-10">
				<div class="text-panel">
					<h1>
						@if(!is_null($model->salutation) && strlen($model->salutation) > 0)<small>{{ $model->salutation }}</small> @endif
						@if(!is_null($model->name) && strlen($model->name) > 0)
							{{ $model->name }}
						@elseif(!is_null($model->email) && strlen($model->email) > 0)
							{{ $model->email }}
						@else
							{{ $model->id }}
						@endif
					</h1>
					@if(!is_null($model->title) && strlen($model->title) > 0)
					<h2>{{ $model->title }}</h2>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>