<?php $__env->startSection('title'); ?>
	Google Multifactor Authentication
<?php $__env->stopSection(); ?>

<?php $__env->startSection('rbg'); ?>
	<?php echo sprintf("<style>body.%s{background-image:url(%s);background-repeat:no-repeat;background-size:cover}</style>\n<script type=\"text/javascript\">setTimeout(function(){document.body.className += ' with-bg ' + '%s';},100);</script>", Cache::get('random-bg-body-class'), asset(Cache::get('random-bg-asset-path')), Cache::get('random-bg-body-class')); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main'); ?>
	<div class="col-sm-10 offset-sm-1 col-lg-4 offset-lg-4">
		<form action="<?php echo e(route('validate-google2fa')); ?>" method="POST" class="card dynamic-bg dynamic-shadow dynamic-color">
			<?php echo csrf_field(); ?>

			<input type="hidden" name="origin" value="<?php echo e(url()->full()); ?>" />
			<div class="card-header">
				<h4 class="text-center"><?php echo e(__('Multifactor Login with Google Authenticator')); ?></h4>
			</div>
			<div class="card-body">
				<?php if(Session::has('errormessage')): ?>
				<div class="alert alert-danger">
					<?php echo e(Session::get('errormessage')); ?>

				</div>
				<?php elseif(Session::has('successmessage')): ?>
				<div class="alert alert-danger">
					<?php echo e(Session::get('successmessage')); ?>

				</div>
				<?php endif; ?>
				<div class="form-group">
					<label><?php echo e(__('Google Authenticator Code')); ?></label>
					<input type="text" class="form-control<?php echo e($errors->has('code') ? ' is-invalid' : ''); ?>" name=
						"code" value="<?php echo e(old('code')); ?>" required autofocus />
					<?php if($errors->has('code')): ?>
                        <span class="invalid-feedback" role="alert">
                            <strong><?php echo e($errors->first('code')); ?></strong>
                        </span>
                    <?php endif; ?>
				</div>
			</div>
			<div class="card-footer">
				<input type="submit" class="btn btn-dynamic" value="<?php echo e(__('Continue')); ?>" />
				<a href="<?php echo e(route('logout')); ?>" class="btn btn-secondary"><?php echo e(__('Cancel')); ?></a>
			</div>
		</form>
	</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app.blueprints.frameless', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>