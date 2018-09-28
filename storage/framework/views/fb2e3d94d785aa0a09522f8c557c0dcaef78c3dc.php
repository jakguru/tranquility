<?php $__env->startSection('title'); ?>
	Settings
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main'); ?>
	<?php echo $__env->make('app.shared.breadcrumbs',['crumbs' => [
		[
			'name' => config('app.name'),
			'url' => route('dashboard'),
		],
		[
			'name' => __('Settings'),
			'url' => '#',
		],
	]], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-4 col-lg-3 col-xl-2 order-last order-md-first">
				<?php echo $__env->make('app.shared.navs.settings', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
			</div>
			<div class="col-md-8 col-lg-9 col-xl-10">
				<div class="jumbotron">
					<h1 class="display-4"><?php echo e(__('Application Settings')); ?></h1>
					<p class="lead"><?php echo e(__('Manage your CRM\'s settings.')); ?></p>
					<hr class="my-4">
					<p><?php echo e(__('Choose one of the Setting Sections to manage your application settings.')); ?></p>
				</div>
			</div>
		</div>
	</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app.blueprints.framed', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>