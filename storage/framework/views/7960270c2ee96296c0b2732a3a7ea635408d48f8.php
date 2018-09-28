<?php $__env->startSection('rbg'); ?>
	<?php echo sprintf("<style>body.%s{background-image:url(%s);background-repeat:no-repeat;background-size:cover}</style>\n<script type=\"text/javascript\">setTimeout(function(){document.body.className += ' with-bg ' + '%s';},100);</script>", Cache::get('random-bg-body-class'), asset(Cache::get('random-bg-asset-path')), Cache::get('random-bg-body-class')); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main'); ?>
	<div class="col-sm-10 offset-sm-1 col-lg-4 offset-lg-4">
		<div class="card dynamic-bg dynamic-shadow dynamic-color">
			<div class="card-header">
				<h4><?php echo e(__('Oops, Something went wrong.')); ?></h4>
			</div>
			<div class="card-body">
				<p><?php echo e(__('The page you requested could not be found.')); ?></p>
			</div>
			<div class="card-footer">
				<a href="<?php echo e(url()->previous()); ?>" class="btn btn-dynamic"><?php echo e(__('Go Back')); ?></a>
			</div>
		</div>
	</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app.blueprints.frameless', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>