<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CustomField\UiUxMemberFieldHandler;
use App\Models\CustomField;

class CustomFieldServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Register the UI/UX Member field handler
        $this->app->singleton('custom_field.ui_ux_member', function ($app) {
            return new UiUxMemberFieldHandler();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Register a macro for the UI/UX Member field
        CustomField::macro('uiUxMember', function() {
            $handler = app('custom_field.ui_ux_member');
            return $handler->handle($this);
        });
    }
}
