<?php $__env->startSection('title'); ?>
	<?php echo e($title); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('main'); ?>
	<?php echo $__env->make('app.shared.breadcrumbs',['crumbs' => $breadcrumbs], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
	<div class="container-fluid">
		<h1><?php echo e(ucwords($plural_label)); ?></h1>
		<form class="card mb-3" action="<?php echo e(url()->current()); ?>" method="GET">
			<div class="card-header">
				<div class="row">
					<div class="col-md-4">
						<div class="input-group input-group-sm mb-2 mb-md-0">
							<input type="search" name="s" value="<?php echo e(request()->input('s')); ?>" class="form-control" />
							<div class="input-group-append">
							    <button class="btn" type="submit" role="submit">
							    	<span class="fas fa-search"></span>
							    </button>
							    <a href="<?php echo e(route($create_route)); ?>" class="btn btn-success">
							    	<span class="fas fa-plus"></span> <?php echo e(sprintf(__('Add %s'), ucwords($single_label))); ?>

							    </a>
							    <a href="<?php echo e(URL::current()); ?>" class="btn btn-secondary">
							    	<span class="fas fa-undo"></span> <?php echo e(__('Reset Filters')); ?>

							    </a>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="input-group input-group-sm mb-2 mb-md-0">
							<div class="input-group-prepend">
								<?php if($page !== 1): ?>
								<a href="<?php echo e(\App\Helpers\ModelListHelper::getPageUrl(1)); ?>" class="btn btn-secondary"><i class="fas fa-fast-backward"></i></a>
								<?php endif; ?>
								<?php if(0 !== $previous_page): ?>
								<a href="<?php echo e(\App\Helpers\ModelListHelper::getPageUrl($previous_page)); ?>" class="btn btn-secondary"><i class="fas fa-backward"></i></a>
								<?php endif; ?>
							    <span class="input-group-text"><?php echo e(__('Page')); ?></span>
							</div>
							<input type="number" min="1" max="<?php echo e($total_pages); ?>" name="page" value="<?php echo e($page); ?>" class="form-control" />
							<div class="input-group-append">
							    <span class="input-group-text"><?php echo e(sprintf( __('of %d'), $total_pages)); ?></span>
							    <button class="btn btn-secondary" type="submit" role="submit" title="<?php echo e(__('Jump Pages')); ?>">
							    	<span class="fas fa-check-circle"></span>
							    </button>
							    <?php if(0 !== $next_page): ?>
								<a href="<?php echo e(\App\Helpers\ModelListHelper::getPageUrl($next_page)); ?>" class="btn btn-secondary"><i class="fas fa-forward"></i></a>
								<?php endif; ?>
								<?php if($page < $total_pages): ?>
								<a href="<?php echo e(\App\Helpers\ModelListHelper::getPageUrl($total_pages)); ?>" class="btn btn-secondary"><i class="fas fa-fast-forward"></i></a>
								<?php endif; ?>
							</div>
						</div>
					</div>
					<div class="col-md-4 text-right">
						<span class="modellist-totals-summary"><?php echo e(sprintf(__('Showing %d of %d %s'), count($items), $total_items, ucwords($plural_label))); ?></span>
					</div>
				</div>
			</div>
			<div class="table-responsive mb-0">
				<table class="table table-sm table-striped table-hover table-model-list mb-0">
					<thead>
						<tr>
							<?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<th <?php if($column == array_keys($columns)[0]): ?> colspan="2" <?php endif; ?> class="<?php echo e($info['type']); ?>-field">
								<span class="column-label"><?php echo e(__($info['label'])); ?></span>
								<span class="column-sorting">
									<a class="<?php echo e(\App\Helpers\ModelListHelper::pageIsSortedBy($column, 'asc') ? 'active' : ''); ?>" href="<?php echo e(\App\Helpers\ModelListHelper::getSortUrl($column, 'asc')); ?>"><span class="fas fa-caret-up"</a>
									<a class="<?php echo e(\App\Helpers\ModelListHelper::pageIsSortedBy($column, 'desc') ? 'active' : ''); ?>" href="<?php echo e(\App\Helpers\ModelListHelper::getSortUrl($column, 'desc')); ?>"><span class="fas fa-caret-down"</a>
									<a class="<?php echo e(\App\Helpers\ModelListHelper::pageIsSortedBy($column, 'none') ? 'active' : ''); ?>" href="<?php echo e(\App\Helpers\ModelListHelper::getSortUrl($column, 'none')); ?>"><span class="fas fa-eraser"</a>
								</span>
							</th>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tr>
						<tr>
							<?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<th <?php if($column == array_keys($columns)[0]): ?> colspan="2" <?php endif; ?> class="<?php echo e($info['type']); ?>-field">
								<div class="input-group input-group-sm">
								<?php switch($info['type']):
									case ('boolean'): ?>
										<select class="form-control form-control-sm" name="filter[<?php echo e($column); ?>]">
											<option value=""></option>
											<option value="1"<?php echo e((true == request()->input(sprintf('filter.%s', $column))) ? ' selected' : ''); ?>><?php echo e(__('Yes')); ?></option>
											<option value="0"<?php echo e(('0' === request()->input(sprintf('filter.%s', $column))) ? ' selected' : ''); ?>><?php echo e(__('No')); ?></option>
										</select>
										<?php break; ?>

									<?php case ('datetime'): ?>
										<input type="text" psuedo-type="datetime-local" class="form-control form-control-sm" name="filter[<?php echo e($column); ?>][min]" value="<?php echo e(request()->input(sprintf('filter.%s.min', $column))); ?>" />
										<div class="input-group-append">
											<span class="input-group-text"><?php echo e(__('to')); ?></span>
										</div>
										<input type="text" psuedo-type="datetime-local" class="form-control form-control-sm" name="filter[<?php echo e($column); ?>][max]" value="<?php echo e(request()->input(sprintf('filter.%s.max', $column))); ?>" />
										<?php break; ?>

									<?php case ('date'): ?>
										<input type="date" class="form-control form-control-sm" name="filter[<?php echo e($column); ?>][min]" value="<?php echo e(request()->input(sprintf('filter.%s.min', $column))); ?>" />
										<div class="input-group-append">
											<span class="input-group-text"><?php echo e(__('to')); ?></span>
										</div>
										<input type="date" class="form-control form-control-sm" name="filter[<?php echo e($column); ?>][max]" value="<?php echo e(request()->input(sprintf('filter.%s.max', $column))); ?>" />
										<?php break; ?>

									<?php case ('time'): ?>
										<input type="time" class="form-control form-control-sm" name="filter[<?php echo e($column); ?>][min]" value="<?php echo e(request()->input(sprintf('filter.%s.min', $column))); ?>" />
										<div class="input-group-append">
											<span class="input-group-text"><?php echo e(__('to')); ?></span>
										</div>
										<input type="time" class="form-control form-control-sm" name="filter[<?php echo e($column); ?>][max]" value="<?php echo e(request()->input(sprintf('filter.%s.max', $column))); ?>" />
										<?php break; ?>

									<?php default: ?>
										<input type="<?php echo e($info['type']); ?>" class="form-control form-control-sm" name="filter[<?php echo e($column); ?>]" value="<?php echo e(request()->input(sprintf('filter.%s', $column))); ?>" />
										<?php break; ?>
								<?php endswitch; ?>
									<div class="input-group-append">
										<button class="btn btn-secondary" type="submit" role="submit" title="<?php echo e(__('Filter Results')); ?>">
									    	<span class="fas fa-filter"></span>
									    </button>
									</div>
								</div>
							</th>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tr>
					</thead>
					<tbody>
						<?php if( 0 == $total_items ): ?>
						<tr>
							<td colspan="<?php echo e(count($columns) + 1); ?>">
								<div class="alert alert-info mb-0 text-center"><?php echo e(sprintf(__('No %s Found'), ucwords($plural_label))); ?></div>
							</td>
						</tr>
						<?php endif; ?>
						<?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $model): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr>
								<td><a href="<?php echo e(route($view_route,['id' => $model->id])); ?>" class="btn btn-block btn-sm btn-dark"><span class="far fa-eye"></span></a></td>
								<?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<td class="<?php echo e($info['type']); ?>-field">
										<?php switch($info['type']):
											case ('boolean'): ?>
												<input type="checkbox" disabled readonly <?php echo e(true == $model->{$column} ? 'checked' : ''); ?> />
												<?php break; ?>

											<?php case ('datetime'): ?>
												<?php echo e(Auth::user()->formatDateTime($model->{$column})); ?>

												<?php break; ?>

											<?php case ('date'): ?>
												<?php echo e(Auth::user()->formatDateTime($model->{$column}, 'date')); ?>

												<?php break; ?>

											<?php case ('time'): ?>
												<?php echo e(Auth::user()->formatDateTime($model->{$column}, 'time')); ?>

												<?php break; ?>

											<?php default: ?>
												<?php echo e($model->{$column}); ?>

												<?php break; ?>
										<?php endswitch; ?>
									</td>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tr>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</tbody>
				</table>
			</div>
		</form>
	</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app.blueprints.framed', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>