<?php $__env->startSection('rbg'); ?>
	<?php echo sprintf("<style>body.%s{background-image:url(%s);background-repeat:no-repeat;background-size:cover}</style>\n<script type=\"text/javascript\">setTimeout(function(){document.body.className += ' with-bg ' + '%s';},100);</script>", Cache::get('random-bg-body-class'), asset(Cache::get('random-bg-asset-path')), Cache::get('random-bg-body-class')); ?>
	<?php echo e(\App\Helpers\GoogleReCAPCHAHelper::injectJS()); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('title'); ?>
	Login
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main'); ?>
	<div class="col-sm-10 offset-sm-1 col-lg-4 offset-lg-4">
		<form action="<?php echo e(route('submit-login')); ?>" method="POST" class="card dynamic-bg dynamic-shadow dynamic-color" id="login-form">
			<?php echo csrf_field(); ?>

			<div class="card-header">
				<h4 class="text-center"><?php echo e(config('app.name')); ?></h4>
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
				<div class="row form-group">
					<label for="email" class="col-sm-5 col-form-label text-md-right"><?php echo e(__('Email')); ?></label>
					<div class="col-sm-7">
                        <input id="email" type="email" class="form-control input-sm<?php echo e($errors->has('email') ? ' is-invalid' : ''); ?>" name="email" value="<?php echo e(old('email')); ?>" required autofocus>

                        <?php if($errors->has('email')): ?>
                            <span class="invalid-feedback" role="alert">
                                <strong><?php echo e($errors->first('email')); ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>
				</div>
				<div class="row form-group">
					<label for="password" class="col-sm-5 col-form-label text-md-right"><?php echo e(__('Password')); ?></label>
					<div class="col-sm-7">
                        <input id="password" type="password" class="form-control input-sm<?php echo e($errors->has('password') ? ' is-invalid' : ''); ?>" name="password" required>

                        <?php if($errors->has('password')): ?>
                            <span class="invalid-feedback" role="alert">
                                <strong><?php echo e($errors->first('password')); ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>
				</div>
				<div class="row">
                    <div class="col-sm-7 offset-sm-5">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" <?php echo e(old('remember') ? 'checked' : ''); ?>>

                            <label class="form-check-label" for="remember">
                                <?php echo e(__('Remember Me')); ?>

                            </label>
                        </div>
                    </div>
                </div>
                <div class="row">
                	<div class="col-sm-7 offset-sm-5">
                		<?php if(\App\Helpers\GoogleReCAPCHAHelper::enabled()): ?>
                    	<?php echo e(\App\Helpers\GoogleReCAPCHAHelper::injectDiv()); ?>

                    	<?php if($errors->has('g-recaptcha-response')): ?>
                            <span class="text-danger" role="alert">
                                <strong><?php echo e($errors->first('g-recaptcha-response')); ?></strong>
                            </span>
                        <?php endif; ?>
						<?php endif; ?>
                	</div>
                </div>
			</div>
			<div class="card-footer">
				<div class="row">
                    <div class="col-sm-7 offset-sm-5">
                    	<input type="submit" class="btn btn-dynamic" value="<?php echo e(__('Log In')); ?>" />
					</div>
				</div>
			</div>
		</form>
	</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app.blueprints.frameless', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>