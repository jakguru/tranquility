<?php $__env->startSection('blueprint'); ?>
<div id="app" class="frameless">
	<main class="container-fluid">
		<?php echo $__env->yieldContent('main'); ?>
	</main>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app.foundation', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>