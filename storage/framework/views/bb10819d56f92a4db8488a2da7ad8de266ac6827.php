<?php $__env->startSection('blueprint'); ?>
<div id="app" class="framed">
	<header>
		<div id="app-icon"><img src="<?php echo e(asset( 'img/favicon.png' )); ?>" /></div>
		<div id="app-name" class="d-none d-sm-inline-block"><?php echo e(config('app.name', 'Tranquility CRM')); ?></div>
		<div id="menu-toggle">
			<a href="#"><span class="fas fa-bars"></span></a>
		</div>
		<form action="<?php echo e(route('search')); ?>" method="GET" id="menu-search" class="d-none d-sm-inline-block">
			<?php echo csrf_field(); ?>

			<div class="input-group input-group-sm">
				<input type="search" name="s" class="form-control" placeholder="<?php echo e(__('Search')); ?>" aria-label="<?php echo e(__('Search')); ?>" value="<?php echo e(old('s', app('request')->query('s'))); ?>" required />
				<div class="input-group-append">
					<button type="button" class="btn btn-outline-dark dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<span class="sr-only">Toggle Dropdown</span>
					</button>
					<div class="dropdown-menu">
						<?php $__currentLoopData = \App\Helpers\ElasticSearchableModelHelper::getElasticSearchableModels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<div class="form-check">
							<input class="form-check-input" type="checkbox" name="model[]" value="<?php echo e($value); ?>" <?php echo e((is_array(old('model', Request::get('model'))) && in_array($value, old('model', Request::get('model')))) ? 'checked' : ''); ?>>
							<label class="form-check-label"><?php echo e($name); ?></label>
						</div>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</div>
					<button class="btn btn-outline-dark" type="submit" role="submit"><span class="fas fa-search"></span></button>
				</div>
			</div>
		</form>
		<?php if(auth()->guard()->check()): ?>
		<div id="user-bar" class="text-right">
			<a href="#" id="messages-indicator" class="indicator-with-label">
				<span class="fas fa-envelope"></span>
				<span class="indicator-label">0</span>
			</a>
			<a href="#" id="notifications-indicator" class="indicator-with-label">
				<span class="fas fa-bell"></span>
				<span class="indicator-label">0</span>
			</a>
			<!-- <span class="user-bar-seperator"></span> -->
			<div id="user-menu" class="dropdown">
				<button class="btn btn-link btn-user-menu-link dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<img src="<?php echo e(Auth::user()->getAvatarUrl(50)); ?>" class="user-avatar" />
					<?php echo e(Auth::user()->name); ?>

				</button>
				<div class="dropdown-menu">
					<a class="dropdown-item <?php if(Request::route()->getName() == 'my-inbox'): ?> active <?php endif; ?>" href="<?php echo e(route('my-inbox')); ?>"><?php echo e(__('My Inbox')); ?></a>
					<a class="dropdown-item <?php if(Request::route()->getName() == 'my-calendar'): ?> active <?php endif; ?>" href="<?php echo e(route('my-calendar')); ?>"><?php echo e(__('My Calendar')); ?></a>
					<a class="dropdown-item <?php if(Request::route()->getName() == 'my-preferences'): ?> active <?php endif; ?>" href="<?php echo e(route('my-preferences')); ?>"><?php echo e(__('My Preferences')); ?></a>
					<a class="dropdown-item" href="<?php echo e(route('logout')); ?>"><?php echo e(__('Log Out')); ?></a>
				</div>
			</div>
			<!-- <span class="user-bar-seperator"></span> -->
			<a href="<?php echo e(route('logout')); ?>"><span class="fas fa-user-slash"></span></a>
		</div>
		<?php endif; ?>
	</header>
	<aside>
		<form action="<?php echo e(route('search')); ?>" method="GET" id="left-menu-search" class="d-sm-none">
			<?php echo csrf_field(); ?>

			<div class="input-group input-group-sm">
				<input type="search" name="s" class="form-control" placeholder="<?php echo e(__('Search')); ?>" aria-label="<?php echo e(__('Search')); ?>" value="<?php echo e(old('s', app('request')->query('s'))); ?>" required />
				<div class="input-group-append">
					<button type="button" class="btn btn-outline-dark dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<span class="sr-only">Toggle Dropdown</span>
					</button>
					<div class="dropdown-menu">
						<?php $__currentLoopData = \App\Helpers\ElasticSearchableModelHelper::getElasticSearchableModels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<div class="form-check">
							<input class="form-check-input" type="checkbox" name="model[]" value="<?php echo e($value); ?>" <?php echo e((is_array(old('model', Request::get('model'))) && in_array($value, old('model', Request::get('model')))) ? 'checked' : ''); ?>>
							<label class="form-check-label"><?php echo e($name); ?></label>
						</div>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</div>
					<button class="btn btn-outline-dark" type="submit" role="submit"><span class="fas fa-search"></span></button>
				</div>
			</div>
		</form>
		<ul id="left-nav-items">
			<?php if(Auth::user()->isSudo()): ?>
			<li>
				<a href="<?php echo e(route('settings')); ?>" class="<?php if(Request::route()->getName() == 'settings' || request()->is('settings/*')): ?> active <?php endif; ?>" title="<?php echo e(__('Settings')); ?>">
					<span>
						<span class="fas fa-sliders-h"></span>
					</span>
					<span><?php echo e(__('Settings')); ?></span>
				</a>
			</li>
			<?php endif; ?>
		</ul>
	</aside>
	<main>
		<?php if(Session::has('globalerrormessage')): ?>
		<div class="alert alert-danger">
			<?php echo e(Session::get('globalerrormessage')); ?>

		</div>
		<?php elseif(Session::has('globalsuccessmessage')): ?>
		<div class="alert alert-success">
			<?php echo e(Session::get('globalsuccessmessage')); ?>

		</div>
		<?php endif; ?>
		<?php echo $__env->yieldContent('main'); ?>
	</main>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app.foundation', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>