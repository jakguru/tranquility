<?php $__env->startSection('title'); ?>
	System Settings
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main'); ?>
	<?php echo $__env->make('app.shared.breadcrumbs',['crumbs' => [
		[
			'name' => config('app.name'),
			'url' => route('dashboard'),
		],
		[
			'name' => __('Settings'),
			'url' => route('settings'),
		],
		[
			'name' => __('System Settings'),
			'url' => '#',
		],
	]], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-4 col-lg-3 col-xl-2 order-last order-md-first">
				<?php echo $__env->make('app.shared.navs.settings', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
			</div>
			<div class="col-md-8 col-lg-9 col-xl-10">
				<form action="<?php echo e(route('save-settings')); ?>" method="POST" autocomplete="off" class="mb-3">
					<?php echo csrf_field(); ?>

					<h1><?php echo e(__('System Settings')); ?></h1>
					<input type="hidden" name="section" value="system" />
					<div class="card">
						<div class="alert alert-danger mb-0">
							<strong><?php echo e(__('Danger Zone')); ?></strong> <?php echo e(__('These settings affect the entire CRM. Changing these settings may have unintended consiquences.')); ?>

						</div>
						<div class="card-body">
							<div class="form-group">
								<label>Application Name</label>
								<input type="text" name="name" class="form-control<?php echo e($errors->has('name') ? ' is-invalid' : ''); ?>"  value="<?php echo e(old('name', config('app.name'))); ?>" required autocomplete="off" />
								<?php if($errors->has('name')): ?>
		                            <span class="invalid-feedback" role="alert">
		                                <strong><?php echo e($errors->first('name')); ?></strong>
		                            </span>
		                        <?php endif; ?>
							</div>
							<div class="form-group">
								<label>System Time Zone</label>
								<select name="timezone" class="form-control<?php echo e($errors->has('timezone') ? ' is-invalid' : ''); ?>" required autocomplete="off">
									<?php $__currentLoopData = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<option value="<?php echo e($tz); ?>"<?php echo e($tz == old('timezone', config('app.timezone')) ? ' selected' : ''); ?>><?php echo e(ucwords(str_replace('_', ' ', $tz))); ?></option>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</select>
								<?php if($errors->has('timezone')): ?>
		                            <span class="invalid-feedback" role="alert">
		                                <strong><?php echo e($errors->first('timezone')); ?></strong>
		                            </span>
		                        <?php endif; ?>
							</div>
							<div class="form-group">
								<label>List Size</label>
								<input type="number" min="1" max="100" name="listsize" class="form-control<?php echo e($errors->has('listsize') ? ' is-invalid' : ''); ?>"  value="<?php echo e(old('listsize', config('app.listsize'))); ?>" required autocomplete="off" />
								<?php if($errors->has('listsize')): ?>
		                            <span class="invalid-feedback" role="alert">
		                                <strong><?php echo e($errors->first('listsize')); ?></strong>
		                            </span>
		                        <?php endif; ?>
							</div>
							<div class="form-group">
								<label>Date Format</label>
								<input type="text" name="dateformat" class="form-control<?php echo e($errors->has('dateformat') ? ' is-invalid' : ''); ?>"  value="<?php echo e(old('dateformat', config('app.dateformat'))); ?>" required autocomplete="off" />
								<?php if($errors->has('dateformat')): ?>
		                            <span class="invalid-feedback" role="alert">
		                                <strong><?php echo e($errors->first('dateformat')); ?></strong>
		                            </span>
		                        <?php endif; ?>
							</div>
							<div class="form-group">
								<label>Time Format</label>
								<input type="text" name="timeformat" class="form-control<?php echo e($errors->has('timeformat') ? ' is-invalid' : ''); ?>"  value="<?php echo e(old('timeformat', config('app.timeformat'))); ?>" required autocomplete="off" />
								<?php if($errors->has('timeformat')): ?>
		                            <span class="invalid-feedback" role="alert">
		                                <strong><?php echo e($errors->first('timeformat')); ?></strong>
		                            </span>
		                        <?php endif; ?>
							</div>
							<div class="form-group">
								<label>Date Time Format</label>
								<input type="text" name="datetimeformat" class="form-control<?php echo e($errors->has('datetimeformat') ? ' is-invalid' : ''); ?>"  value="<?php echo e(old('datetimeformat', config('app.datetimeformat'))); ?>" required autocomplete="off" />
								<?php if($errors->has('datetimeformat')): ?>
		                            <span class="invalid-feedback" role="alert">
		                                <strong><?php echo e($errors->first('datetimeformat')); ?></strong>
		                            </span>
		                        <?php endif; ?>
							</div>
						</div>
						<div class="card-footer">
							<input type="submit" class="btn btn-dark" value="<?php echo e(__('Save System Settings')); ?>" />
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app.blueprints.framed', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>