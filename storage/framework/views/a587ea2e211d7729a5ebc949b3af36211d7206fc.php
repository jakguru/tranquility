<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
	<?php $__currentLoopData = $crumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $crumb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		<?php if($index == count($crumbs) - 1): ?>
			<li class="breadcrumb-item active" aria-current="page"><?php echo e($crumb['name']); ?></li>
		<?php else: ?>
			<li class="breadcrumb-item"><a href="<?php echo e($crumb['url']); ?>"><?php echo e($crumb['name']); ?></a></li>
		<?php endif; ?>
	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	</ol>
</nav>