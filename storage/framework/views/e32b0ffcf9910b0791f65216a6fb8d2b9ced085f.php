<?php $__env->startSection('title'); ?>
	Dashboard
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main'); ?>
	<?php echo $__env->make('app.shared.breadcrumbs',['crumbs' => [
		[
			'name' => __('Dashboard'),
			'url' => '#',
		],
	]], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app.blueprints.framed', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>