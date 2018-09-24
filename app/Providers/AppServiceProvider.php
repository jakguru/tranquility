<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Model;
use \App\Helpers\AppServiceProviderEventHandlerHelper;
use \App\Helpers\BackgroundImageHelper;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \App\User::saved('\App\Helpers\PermissionsHelper@onUserSaved');
        \App\Role::saved('\App\Helpers\PermissionsHelper@onRoleSaved');
        AppServiceProviderEventHandlerHelper::hookToTraitedModelEvents('Loggable', ['created'], '\App\Helpers\LoggableEventHelper@logCreated');
        AppServiceProviderEventHandlerHelper::hookToTraitedModelEvents('Loggable', ['saved'], '\App\Helpers\LoggableEventHelper@logSaved');
        AppServiceProviderEventHandlerHelper::hookToTraitedModelEvents('Loggable', ['login'], '\App\Helpers\LoggableEventHelper@logLogin');
        AppServiceProviderEventHandlerHelper::hookToTraitedModelEvents('Loggable', ['deleted'], '\App\Helpers\LoggableEventHelper@logDeleted');
        AppServiceProviderEventHandlerHelper::hookToTraitedModelEvents('Loggable', ['restored'], '\App\Helpers\LoggableEventHelper@logRestored');
        AppServiceProviderEventHandlerHelper::hookToTraitedModelEvents('ElasticSearchable', ['created'], '\App\Helpers\ElasticSearchableModelHelper@save');
        AppServiceProviderEventHandlerHelper::hookToTraitedModelEvents('ElasticSearchable', ['saved'], '\App\Helpers\ElasticSearchableModelHelper@update');
        \App\User::saving(function (\App\User $user) {
            $nameParts = array_merge(explode(' ', $user->fName), explode(' ', $user->lName));
            $nameParts = array_map('strtolower', $nameParts);
            $nameParts = array_map('ucwords', $nameParts);
            $user->name = implode(' ', $nameParts);
        });
        Blade::directive('rbg', function ($input) {
            $css = 'body.%s{background-image:url(%s);background-repeat:no-repeat;background-size:cover}';
            return '<?php echo sprintf("<style>' . $css . '</style>\n<script type=\"text/javascript\">setTimeout(function(){document.body.className += \' with-bg \' + \'%s\';},100);</script>", Cache::get(\'random-bg-body-class\'), asset(Cache::get(\'random-bg-asset-path\')), Cache::get(\'random-bg-body-class\')); ?>';
        });
        $mail_options = \App\Options::get('mail');
        if (is_object($mail_options)) {
            Config::set('mail.host', $mail_options->hostname);
            Config::set('mail.port', $mail_options->port);
            Config::set('mail.encryption', $mail_options->encryption);
            Config::set('mail.username', $mail_options->username);
            Config::set('mail.password', $mail_options->password);
            Config::set('mail.from.address', $mail_options->sendermail);
            Config::set('mail.from.name', $mail_options->sendername);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // do something?
    }
}
