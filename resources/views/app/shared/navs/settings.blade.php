<ul class="nav nav-pills flex-column">
  <li class="nav-item">
    <a class="nav-link @if(Request::route()->getName() == 'settings-users' || request()->is('settings/users/*')) active @endif" href="{{ route('settings-users') }}">{{ __('Manage Users') }}</a>
  </li>
  <li class="nav-item">
    <a class="nav-link @if(Request::route()->getName() == 'settings-groups' || request()->is('settings/groups/*')) active @endif" href="{{ route('settings-groups') }}">{{ __('Manage Groups') }}</a>
  </li>
  <li class="nav-item">
    <a class="nav-link @if(Request::route()->getName() == 'settings-roles' || request()->is('settings/roles/*')) active @endif" href="{{ route('settings-roles') }}">{{ __('Manage Roles') }}</a>
  </li>
  <li class="nav-item">
    <a class="nav-link @if(Request::route()->getName() == 'settings-system' || request()->is('settings/system/*')) active @endif" href="{{ route('settings-system') }}">{{ __('System Settings') }}</a>
  </li>
  <li class="nav-item">
    <a class="nav-link @if(Request::route()->getName() == 'settings-email' || request()->is('settings/email/*')) active @endif" href="{{ route('settings-email') }}">{{ __('Email Settings') }}</a>
  </li>
  <li class="nav-item">
    <a class="nav-link @if(Request::route()->getName() == 'settings-google' || request()->is('settings/google/*')) active @endif" href="{{ route('settings-google') }}">{{ __('Google Settings') }}</a>
  </li>
  <li class="nav-item">
    <a class="nav-link @if(Request::route()->getName() == 'settings-minfraud' || request()->is('settings/minfraud/*')) active @endif" href="{{ route('settings-minfraud') }}">{{ __('MinFraud Settings') }}</a>
  </li>
  <li class="nav-item">
    <a class="nav-link @if(Request::route()->getName() == 'settings-bin-check' || request()->is('settings/bin-check/*')) active @endif" href="{{ route('settings-bin-check') }}">{{ __('BIN Check Settings') }}</a>
  </li>
  <li class="nav-item">
    <a class="nav-link @if(Request::route()->getName() == 'settings-weather' || request()->is('settings/weather/*')) active @endif" href="{{ route('settings-weather') }}">{{ __('Weather Settings') }}</a>
  </li>
</ul>