<ul class="nav nav-pills flex-column">
  <li class="nav-item">
    <a class="nav-link <?php if(Request::route()->getName() == 'settings-users' || request()->is('settings/users/*')): ?> active <?php endif; ?>" href="<?php echo e(route('settings-users')); ?>"><?php echo e(__('Manage Users')); ?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php if(Request::route()->getName() == 'settings-groups' || request()->is('settings/groups/*')): ?> active <?php endif; ?>" href="<?php echo e(route('settings-groups')); ?>"><?php echo e(__('Manage Groups')); ?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php if(Request::route()->getName() == 'settings-roles' || request()->is('settings/roles/*')): ?> active <?php endif; ?>" href="<?php echo e(route('settings-roles')); ?>"><?php echo e(__('Manage Roles')); ?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php if(Request::route()->getName() == 'settings-system' || request()->is('settings/system/*')): ?> active <?php endif; ?>" href="<?php echo e(route('settings-system')); ?>"><?php echo e(__('System Settings')); ?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php if(Request::route()->getName() == 'settings-email' || request()->is('settings/email/*')): ?> active <?php endif; ?>" href="<?php echo e(route('settings-email')); ?>"><?php echo e(__('Email Settings')); ?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php if(Request::route()->getName() == 'settings-google' || request()->is('settings/google/*')): ?> active <?php endif; ?>" href="<?php echo e(route('settings-google')); ?>"><?php echo e(__('Google Settings')); ?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php if(Request::route()->getName() == 'settings-minfraud' || request()->is('settings/minfraud/*')): ?> active <?php endif; ?>" href="<?php echo e(route('settings-minfraud')); ?>"><?php echo e(__('MinFraud Settings')); ?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php if(Request::route()->getName() == 'settings-bin-check' || request()->is('settings/bin-check/*')): ?> active <?php endif; ?>" href="<?php echo e(route('settings-bin-check')); ?>"><?php echo e(__('BIN Check Settings')); ?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php if(Request::route()->getName() == 'settings-weather' || request()->is('settings/weather/*')): ?> active <?php endif; ?>" href="<?php echo e(route('settings-weather')); ?>"><?php echo e(__('Weather Settings')); ?></a>
  </li>
</ul>