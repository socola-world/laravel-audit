<?php

namespace SocolaDaiCa\LaravelAudit\Providers;

use Illuminate\Support\ServiceProvider;

class LaravelAuditServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected $moduleName = 'LaravelAudit';

    /**
     * @var string
     */
    protected $moduleNameLower = 'laravelaudit';

    /**
     * Boot the application events.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/socoladaica__laravel_audit.php' => config_path('socoladaica__laravel_audit.php'),
        ], 'config');
        $this->mergeConfigFrom(__DIR__.'/'.'../../config/socoladaica__laravel_audit.php', 'socoladaica__laravel_audit');

//        $this->publishes([
//            module_path($this->moduleName, 'config/config.php') => config_path($this->moduleNameLower . '.php'),
//        ], 'config');
//        $this->mergeConfigFrom(
//            module_path($this->moduleName, 'config/config.php'), $this->moduleNameLower
//        );
//        $this->registerTranslations();
//        $this->registerConfig();
        $this->registerViews();
//        $this->loadMigrationsFrom(module_path($this->moduleName, 'database/migrations'));
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'config/config.php') => config_path($this->moduleNameLower.'.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'config/config.php'),
            $this->moduleNameLower
        );
    }

    /**
     * Register views.
     */
    public function registerViews()
    {
//        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);
//
//        $this->publishes([
//            __DIR__.'/../../resources/views' => resource_path('vendor/socoladaica/laravel-audit'),
//        ], ['views', $this->moduleNameLower . '-module-views']);
//
//        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [__DIR__.'/../../resources/views']), $this->moduleNameLower);
//        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);
//
//        $sourcePath = module_path($this->moduleName, 'resources/views');
//
//        $this->publishes([
//            $sourcePath => $viewPath
//        ], ['views', $this->moduleNameLower . '-module-views']);
//
//        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);

//        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);
//
//        $sourcePath = __DIR__.'/../../resources/views';
//
//        $this->publishes([
//            $sourcePath => $viewPath
//        ], ['views', $this->moduleNameLower . '-module-views']);

//        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);

        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'laravel-audit');
    }

    /**
     * Register translations.
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/'.$this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];

        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->moduleNameLower)) {
                $paths[] = $path.'/modules/'.$this->moduleNameLower;
            }
        }

        return $paths;
    }
}
